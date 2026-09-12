@extends('layouts.admin')

@section('title', 'Account Registrations — SARI Admin')
@section('page-title', 'Account Registrations')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    :root {
        --sari-gold: #d99500;
        --sari-gold-dark: #b77c00;
        --sari-gold-soft: #fff8e9;
        --sari-ink: #1c1915;
        --sari-text: #474038;
        --sari-muted: #8b8278;
        --sari-line: #e8dfd4;
        --sari-soft: #fbfaf7;
        --sari-warm: #fff9ef;
        --sari-shadow: 0 18px 44px rgba(68, 49, 25, .075), 0 4px 14px rgba(68, 49, 25, .035);
        --sari-shadow-soft: 0 10px 26px rgba(68, 49, 25, .06), 0 2px 8px rgba(68, 49, 25, .025);
    }

    .reg-page {
        color: var(--sari-ink);
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
    }

    .reg-page button,
    .reg-page input,
    .reg-page select,
    .reg-page textarea {
        font: inherit;
    }

    .reg-surface {
        background: #fff;
        border: 1px solid var(--sari-line);
        border-radius: 18px;
        box-shadow: var(--sari-shadow);
    }

    .reg-mini-surface {
        background: rgba(255, 255, 255, .96);
        border: 1px solid #e7ddd0;
        border-radius: 15px;
        box-shadow: var(--sari-shadow-soft), inset 0 1px 0 rgba(255,255,255,.95);
    }

    .summary-card {
        min-height: 102px;
        background: rgba(255,255,255,.98);
        border: 1px solid #e8dfd4;
        border-radius: 16px;
        box-shadow: 0 10px 26px rgba(62, 44, 23, .065), 0 2px 7px rgba(62, 44, 23, .025), inset 0 1px 0 rgba(255,255,255,.98);
        transform: translate3d(0, 0, 0);
        transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    }

    .summary-card:hover {
        transform: translate3d(0, -2px, 0);
        border-color: #dccdaf;
        box-shadow: 0 14px 30px rgba(68, 49, 25, .085), 0 3px 9px rgba(68, 49, 25, .03), inset 0 1px 0 rgba(255,255,255,.98);
    }

    .summary-card.is-current {
        border-color: #dfc287;
        box-shadow: 0 12px 28px rgba(176, 117, 0, .09), 0 2px 7px rgba(68,49,25,.025), inset 0 0 0 1px rgba(217,149,0,.055);
    }

    .registration-workspace-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 14px;
        align-items: start;
    }

    .registration-main-column {
        min-width: 0;
    }

    .registration-side-column {
        min-width: 0;
    }

    .registration-mix-card {
        background: rgba(255,255,255,.99);
        border: 1px solid #e8dfd4;
        border-radius: 17px;
        box-shadow: 0 14px 34px rgba(62, 44, 23, .075), 0 3px 9px rgba(62, 44, 23, .025), inset 0 1px 0 rgba(255,255,255,.98);
    }

    .registration-mix-ring {
        filter: drop-shadow(0 7px 11px rgba(74, 51, 22, .075));
    }

    .mix-role-row {
        transition: background-color .14s ease, transform .14s ease;
    }

    .mix-role-row:hover {
        background: #faf7f2;
        transform: translateX(2px);
    }

    @media (min-width: 1180px) {
        .registration-workspace-layout {
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 16px;
        }

        .registration-side-column {
            position: sticky;
            top: 14px;
        }
    }

    .reg-control {
        color: #2c261f !important;
        -webkit-text-fill-color: #2c261f !important;
        background-color: #fff !important;
        border-color: #e4d9cb !important;
        box-shadow: 0 5px 14px rgba(61, 43, 22, .035), inset 0 1px 0 rgba(255,255,255,.96);
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease, transform .15s ease;
    }

    .reg-control:hover {
        border-color: #d8c8b1 !important;
        box-shadow: 0 7px 18px rgba(61, 43, 22, .05), inset 0 1px 0 rgba(255,255,255,.96);
    }

    .reg-control::placeholder {
        color: #aaa198 !important;
        -webkit-text-fill-color: #aaa198 !important;
    }

    .reg-control:focus {
        outline: none;
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217,149,0,.085), 0 8px 20px rgba(77,55,24,.055);
    }

    .premium-select {
        position: relative;
        min-width: 150px;
    }

    .premium-select-trigger {
        width: 100%;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 0 12px;
        border: 1px solid #e3d8ca;
        border-radius: 11px;
        background: #fff;
        color: #3c342c;
        box-shadow: 0 4px 12px rgba(61, 43, 22, .03), inset 0 1px 0 rgba(255,255,255,.98);
        font-size: 8px;
        font-weight: 600;
        line-height: 1;
        cursor: pointer;
        transition: border-color .14s ease, box-shadow .14s ease, background-color .14s ease;
    }

    .premium-select-trigger:hover {
        border-color: #d7c7af;
        background: #fff;
        box-shadow: 0 6px 16px rgba(61, 43, 22, .045), inset 0 1px 0 rgba(255,255,255,.98);
    }

    .premium-select-trigger:focus-visible,
    .premium-select.is-open .premium-select-trigger {
        outline: none;
        border-color: #d49a2b;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(217,149,0,.075), 0 6px 16px rgba(61,43,22,.04);
    }

    .premium-select-dot {
        width: 5px;
        height: 5px;
        flex: 0 0 auto;
        border-radius: 999px;
        background: #d99500;
        box-shadow: 0 0 0 3px rgba(217,149,0,.07);
    }

    .premium-select-chevron {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
        color: #75695d;
        transition: transform .14s ease;
    }

    .premium-select.is-open .premium-select-chevron {
        transform: rotate(180deg);
    }

    .premium-select-menu {
        position: absolute;
        z-index: 60;
        top: calc(100% + 7px);
        left: 0;
        width: 100%;
        min-width: 165px;
        padding: 5px;
        border: 1px solid #e5dbce;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 16px 34px rgba(56, 39, 19, .11), 0 4px 12px rgba(56, 39, 19, .045);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translate3d(0, -4px, 0) scale(.985);
        transform-origin: top;
        transition: opacity .13s ease, transform .13s ease, visibility 0s linear .13s;
    }

    .premium-select.is-open .premium-select-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translate3d(0, 0, 0) scale(1);
        transition: opacity .13s ease, transform .13s ease;
    }

    .premium-select-option {
        width: 100%;
        min-height: 34px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 9px;
        border: 0;
        border-radius: 8px;
        background: #fff;
        color: #51483f;
        font-size: 8px;
        font-weight: 500;
        text-align: left;
        cursor: pointer;
        transition: background-color .12s ease, color .12s ease;
    }

    .premium-select-option:hover,
    .premium-select-option:focus-visible {
        outline: none;
        background: #fbf8f3;
        color: #312a23;
    }

    .premium-select-option.is-selected {
        background: #fff8eb;
        color: #9a6706;
        font-weight: 600;
    }

    .premium-select-check {
        width: 14px;
        height: 14px;
        opacity: 0;
        color: #c2830b;
    }

    .premium-select-option.is-selected .premium-select-check {
        opacity: 1;
    }

    .reg-primary-btn,
    .reg-secondary-btn,
    .reg-review-btn {
        transform: translate3d(0, 0, 0);
        transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease, background-color .16s ease, color .16s ease;
    }

    .reg-primary-btn {
        background: var(--sari-gold);
        box-shadow: 0 8px 18px rgba(217, 149, 0, .20);
    }

    .reg-primary-btn:hover {
        background: var(--sari-gold-dark);
        transform: translate3d(0, -1px, 0);
        box-shadow: 0 10px 22px rgba(183, 124, 0, .23);
    }

    .reg-primary-btn:active,
    .reg-secondary-btn:active,
    .reg-review-btn:active {
        transform: translate3d(0, 0, 0) scale(.985);
    }

    .reg-secondary-btn {
        background: #fff;
        box-shadow: 0 5px 13px rgba(60, 43, 23, .035);
    }

    .reg-secondary-btn:hover {
        background: #fffaf2;
        border-color: #ddc89f;
        color: #9c6a0d;
    }

    [data-application-row] {
        cursor: pointer;
        transition: background-color .14s ease, box-shadow .14s ease;
    }

    [data-application-row]:hover {
        background: #fffdf9;
        box-shadow: inset 3px 0 0 rgba(217,149,0,.24);
    }

    [data-application-row].is-selected {
        background: #fff8eb;
        box-shadow: inset 3px 0 0 var(--sari-gold);
    }

    .reg-review-btn {
        box-shadow: 0 5px 13px rgba(60, 43, 23, .035);
    }

    .reg-review-btn:hover {
        border-color: #dcc28f;
        background: #fff9ee;
        color: #9e6b0a;
        box-shadow: 0 8px 18px rgba(100, 72, 28, .07);
        transform: translate3d(0, -1px, 0);
    }

    [data-application-row][hidden],
    [data-application-detail][hidden],
    [data-detail-tab-panel][hidden] {
        display: none !important;
    }

    .reg-backdrop {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        background: rgba(29, 23, 16, .18);
        transition: opacity .20s ease, visibility 0s linear .20s;
    }

    .reg-backdrop.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition: opacity .20s ease;
    }

    .reg-drawer {
        width: min(820px, calc(100vw - 300px));
        max-width: 820px;
        opacity: .985;
        visibility: hidden;
        pointer-events: none;
        transform: translate3d(102%, 0, 0);
        box-shadow: -24px 0 58px rgba(31, 24, 17, .13);
        will-change: transform, opacity;
        transition:
            transform .28s cubic-bezier(.22, .82, .22, 1),
            opacity .18s ease,
            visibility 0s linear .28s;
    }

    .reg-drawer.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translate3d(0, 0, 0);
        transition:
            transform .28s cubic-bezier(.22, .82, .22, 1),
            opacity .18s ease;
    }

    .review-card,
    #reviewDrawer section {
        box-shadow: 0 9px 22px rgba(67, 48, 24, .045), inset 0 1px 0 rgba(255,255,255,.9);
    }

    .reg-scroll {
        scrollbar-width: thin;
        scrollbar-color: #d7cfc5 transparent;
        overscroll-behavior: contain;
    }

    .reg-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .reg-scroll::-webkit-scrollbar-thumb { background: #d7cfc5; border-radius: 999px; }

    .detail-tab {
        transition: color .16s ease, border-color .16s ease, background-color .16s ease;
    }

    .detail-tab.is-active {
        color: var(--sari-gold-dark);
        border-color: var(--sari-gold);
    }

    body.reg-review-open { overflow: hidden; }

    @media (prefers-reduced-motion: reduce) {
        .summary-card,
        .reg-control,
        .premium-select-trigger,
        .premium-select-menu,
        .premium-select-option,
        .premium-select-chevron,
        .reg-primary-btn,
        .reg-secondary-btn,
        .reg-review-btn,
        [data-application-row],
        .reg-backdrop,
        .reg-drawer,
        .detail-tab {
            transition-duration: .01ms !important;
        }
    }

    @media (max-width: 1279px) {
        .reg-drawer { width: min(760px, 92vw); }
    }

    @media (max-width: 767px) {
        .reg-drawer { width: 100vw; max-width: none; border-left: 0 !important; }
        .summary-card { min-height: 90px; }
    }

    /* =========================================================
       SARI REGISTRATIONS — REFINED ADMIN UI
       Visual-only layer: no routes, form names, or JS hooks changed.
       ========================================================= */

    .registration-page-shell {
        padding-inline: clamp(0px, .65vw, 10px);
    }

    .registration-page-header {
        margin-bottom: 16px;
    }

    .registration-page-header > div {
        align-items: center;
    }

    .registration-page-header h2 {
        font-size: clamp(24px, 1.75vw, 28px) !important;
        line-height: 1.16;
        letter-spacing: -.035em;
    }

    .registration-page-header p {
        max-width: 760px;
        color: #81786c;
    }

    .reg-surface,
    .registration-mix-card {
        border-color: #e9e1d7;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .025),
            0 10px 28px rgba(61, 43, 22, .055);
    }

    .summary-grid {
        gap: 12px;
    }

    .summary-card {
        position: relative;
        min-height: 96px;
        overflow: hidden;
        border-radius: 15px;
        border-color: #e9e1d7;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .025),
            0 7px 20px rgba(61, 43, 22, .045);
    }

    .summary-card:hover {
        transform: translate3d(0, -1px, 0);
        border-color: #ddcfb8;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .03),
            0 10px 24px rgba(61, 43, 22, .065);
    }

    .summary-card.is-current {
        border-color: #dfc896;
        background: #fffdfa;
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .025),
            0 8px 22px rgba(126, 87, 20, .065);
    }

    .summary-card.is-current::after {
        content: "";
        position: absolute;
        left: 16px;
        right: 16px;
        bottom: 0;
        height: 2px;
        border-radius: 999px 999px 0 0;
        background: #d99500;
        opacity: .78;
    }

    .registration-workspace-layout {
        gap: 18px;
    }

    .queue-header {
        background: #fff;
        padding-top: 17px !important;
        padding-bottom: 15px !important;
    }

    .queue-top {
        gap: 16px;
    }

    .queue-top h3 {
        font-size: 18px !important;
        line-height: 1.2;
    }

    .queue-top #visibleCount {
        padding: 4px 9px;
        font-size: 9px;
        background: #faf8f4;
        border-color: #e9e1d7;
    }

    .filter-controls {
        width: auto;
        flex-wrap: wrap;
        gap: 6px !important;
        padding: 4px;
        border: 1px solid #eee7de;
        border-radius: 13px;
        background: #faf9f6;
    }

    .filter-controls .premium-select {
        min-width: 136px;
    }

    .filter-controls .premium-select:first-child {
        min-width: 154px;
    }

    .filter-controls .premium-select-trigger {
        height: 36px !important;
        padding: 0 11px;
        border-radius: 9px;
        border-color: #e5ddd2;
        background: #fff;
        box-shadow: none;
        font-size: 10px !important;
        font-weight: 600;
    }

    .filter-controls .premium-select-trigger:hover {
        border-color: #d7c7af;
        box-shadow: 0 3px 10px rgba(61, 43, 22, .035);
    }

    .filter-controls .premium-select-dot {
        width: 5px;
        height: 5px;
        box-shadow: 0 0 0 3px rgba(217,149,0,.06);
    }

    .filter-controls .premium-select-chevron {
        width: 13px;
        height: 13px;
    }

    .filter-controls .premium-select-menu {
        top: calc(100% + 6px);
        min-width: 100%;
        padding: 5px;
        border-radius: 11px;
        border-color: #e7ded3;
        box-shadow: 0 14px 30px rgba(56, 39, 19, .10);
    }

    .filter-controls .premium-select-option {
        min-height: 32px;
        padding: 0 9px;
        border-radius: 8px;
        font-size: 9px !important;
        font-weight: 500;
    }

    .filter-controls .reg-primary-btn,
    .filter-controls .reg-secondary-btn {
        height: 36px !important;
        border-radius: 9px;
        padding-inline: 13px;
        font-size: 10px !important;
        font-weight: 600;
        white-space: nowrap;
    }

    .filter-controls .reg-primary-btn {
        box-shadow: 0 5px 12px rgba(183, 124, 0, .14);
    }

    .filter-controls .reg-primary-btn:hover {
        transform: none;
        box-shadow: 0 6px 15px rgba(183, 124, 0, .18);
    }

    .filter-controls .reg-secondary-btn {
        background: transparent;
        box-shadow: none;
    }

    .queue-bottom {
        gap: 12px;
        padding-top: 1px;
    }

    .queue-search {
        flex: 1 1 420px;
    }

    .queue-search .reg-control {
        height: 38px !important;
        border-radius: 10px;
        font-size: 10px !important;
        box-shadow: none;
    }

    .queue-search .reg-control:hover {
        box-shadow: none;
    }

    .queue-search .reg-control:focus {
        box-shadow: 0 0 0 3px rgba(217,149,0,.075);
    }

    .role-stat-chips {
        justify-content: flex-end;
        gap: 6px !important;
    }

    .role-stat-chips > span {
        padding: 5px 9px !important;
        border-radius: 999px;
        font-size: 8.5px !important;
        line-height: 1;
        white-space: nowrap;
    }

    .application-table-wrap {
        border-top: 1px solid #f3eee7;
    }

    .application-table thead {
        position: sticky;
        top: 0;
        z-index: 5;
    }

    .application-table thead tr {
        background: #faf9f6 !important;
    }

    .application-table th {
        padding-top: 11px !important;
        padding-bottom: 11px !important;
        font-size: 9px !important;
        letter-spacing: .07em !important;
        color: #766d63 !important;
    }

    .application-table tbody tr {
        background: #fff;
    }

    .application-table tbody td {
        vertical-align: middle;
    }

    [data-application-row]:hover {
        background: #fffaf3;
        box-shadow: inset 2px 0 0 rgba(217,149,0,.72);
    }

    [data-application-row].is-selected {
        background: #fff8eb;
        box-shadow: inset 2px 0 0 var(--sari-gold);
    }

    .reg-review-btn {
        border-radius: 9px !important;
        box-shadow: none;
    }

    .reg-review-btn:hover {
        transform: none;
        box-shadow: none;
    }

    .registration-mix-card {
        border-radius: 16px;
    }

    .mix-role-row {
        border: 1px solid transparent;
    }

    .mix-role-row:hover {
        transform: none;
        border-color: #eee6db;
        background: #fbf9f5;
    }

    .reg-drawer {
        width: min(780px, calc(100vw - 290px));
        max-width: 780px;
        box-shadow: -20px 0 50px rgba(31, 24, 17, .12);
    }

    @media (min-width: 1180px) {
        .registration-workspace-layout {
            grid-template-columns: minmax(0, 1fr) 292px;
            gap: 18px;
        }

        .registration-side-column {
            top: 18px;
        }
    }

    @media (max-width: 1279px) {
        .filter-controls {
            width: 100%;
        }

        .filter-controls .premium-select {
            flex: 1 1 150px;
        }

        .role-stat-chips {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767px) {
        .registration-page-shell {
            padding-inline: 0;
        }

        .registration-page-header h2 {
            font-size: 23px !important;
        }

        .summary-card {
            min-height: 90px;
        }

        .filter-controls {
            padding: 5px;
        }

        .filter-controls .premium-select,
        .filter-controls .premium-select:first-child {
            width: 100%;
            min-width: 0;
        }

        .filter-controls .reg-primary-btn,
        .filter-controls .reg-secondary-btn {
            width: 100%;
        }

        .queue-search {
            flex-basis: auto;
        }

        .role-stat-chips {
            overflow-x: auto;
            flex-wrap: nowrap !important;
            padding-bottom: 2px;
            scrollbar-width: none;
        }

        .role-stat-chips::-webkit-scrollbar {
            display: none;
        }
    }


    /* =========================================================
       SOFT FLOATING DEPTH — subtle, enterprise-style shadows
       ========================================================= */

    .reg-surface {
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .035),
            0 12px 28px rgba(61, 43, 22, .07),
            0 24px 50px rgba(61, 43, 22, .035) !important;
    }

    .summary-card {
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .035),
            0 10px 24px rgba(61, 43, 22, .065),
            0 20px 38px rgba(61, 43, 22, .025) !important;
    }

    .summary-card:hover {
        box-shadow:
            0 3px 6px rgba(61, 43, 22, .04),
            0 14px 30px rgba(61, 43, 22, .08),
            0 24px 44px rgba(61, 43, 22, .035) !important;
    }

    .summary-card.is-current {
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .035),
            0 12px 28px rgba(145, 99, 16, .085),
            0 22px 42px rgba(145, 99, 16, .035) !important;
    }

    .registration-mix-card {
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .035),
            0 13px 30px rgba(61, 43, 22, .075),
            0 24px 48px rgba(61, 43, 22, .035) !important;
    }

    .filter-controls {
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .025),
            0 8px 18px rgba(61, 43, 22, .045) !important;
    }

    .premium-select-menu {
        box-shadow:
            0 8px 18px rgba(56, 39, 19, .08),
            0 18px 40px rgba(56, 39, 19, .11) !important;
    }

    .review-card,
    #reviewDrawer section {
        box-shadow:
            0 2px 5px rgba(67, 48, 24, .025),
            0 10px 24px rgba(67, 48, 24, .06) !important;
    }

    #reviewDrawer {
        box-shadow:
            -10px 0 24px rgba(31, 24, 17, .055),
            -28px 0 60px rgba(31, 24, 17, .105) !important;
    }

    /* Keep the floating effect restrained on smaller screens */
    @media (max-width: 767px) {
        .reg-surface,
        .registration-mix-card,
        .summary-card {
            box-shadow:
                0 2px 5px rgba(61, 43, 22, .03),
                0 10px 24px rgba(61, 43, 22, .06) !important;
        }
    }


    /* =========================================================
       TYPOGRAPHY SCALE — slightly larger, still compact
       ========================================================= */

    .registration-page-header h2 {
        font-size: clamp(29px, 2.15vw, 36px) !important;
        line-height: 1.12 !important;
        letter-spacing: -.04em !important;
    }

    .registration-page-header p {
        font-size: 12px !important;
        line-height: 1.7 !important;
    }

    .summary-card p:first-child {
        font-size: 11px !important;
    }

    .summary-card p:nth-child(2) {
        font-size: 26px !important;
    }

    .summary-card span.text-\[8px\],
    .summary-card span.text-\[9px\] {
        font-size: 9.5px !important;
    }

    .queue-top h3 {
        font-size: 19px !important;
    }

    .queue-top p {
        font-size: 11.5px !important;
    }

    .queue-top #visibleCount {
        font-size: 9.5px !important;
    }

    .filter-controls .premium-select-trigger {
        font-size: 10.5px !important;
    }

    .filter-controls .premium-select-option {
        font-size: 9.5px !important;
    }

    .filter-controls .reg-primary-btn,
    .filter-controls .reg-secondary-btn {
        font-size: 10.5px !important;
    }

    .queue-search .reg-control {
        font-size: 10.5px !important;
    }

    .role-stat-chips > span {
        font-size: 9px !important;
    }

    .application-table th {
        font-size: 9.5px !important;
    }

    .application-table td p,
    .application-table td span {
        font-size: 9.5px;
    }

    .application-table td .text-\[8px\] {
        font-size: 8.5px !important;
    }

    .application-table td .text-\[9px\] {
        font-size: 9.5px !important;
    }

    .application-table td .text-\[10px\] {
        font-size: 10.5px !important;
    }

    .reg-review-btn {
        font-size: 9.5px !important;
    }

    .registration-mix-card > div:first-child p:first-child {
        font-size: 13px !important;
    }

    .registration-mix-card > div:first-child p:nth-child(2) {
        font-size: 9.5px !important;
    }

    .mix-role-row span {
        font-size: 9.5px !important;
    }

    .registration-mix-card > a:last-child {
        font-size: 9.5px !important;
    }

    @media (max-width: 767px) {
        .registration-page-header h2 {
            font-size: 28px !important;
        }
    }


    /* =========================================================
       CLEAN SUMMARY CARD STATE
       Keep filtering/click behavior, remove selected-card effects.
       ========================================================= */

    .summary-card.is-current {
        background: rgba(255,255,255,.98) !important;
        border-color: #e9e1d7 !important;
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .035),
            0 10px 24px rgba(61, 43, 22, .065),
            0 20px 38px rgba(61, 43, 22, .025) !important;
    }

    .summary-card.is-current::after {
        display: none !important;
        content: none !important;
    }



    /* =========================================================
       EXTRA FLOATING DEPTH — cleaner layered elevation
       Visual-only override. No layout, routes, or JS hooks changed.
       ========================================================= */

    .reg-surface {
        border-color: #e8dfd4 !important;
        box-shadow:
            0 2px 6px rgba(61, 43, 22, .035),
            0 14px 34px rgba(61, 43, 22, .075),
            0 30px 62px rgba(61, 43, 22, .032),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .summary-card {
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .032),
            0 12px 28px rgba(61, 43, 22, .07),
            0 24px 48px rgba(61, 43, 22, .028),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .summary-card:hover {
        transform: translate3d(0, -2px, 0);
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .04),
            0 17px 36px rgba(61, 43, 22, .09),
            0 30px 58px rgba(61, 43, 22, .035),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .summary-card.is-current {
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .032),
            0 12px 28px rgba(61, 43, 22, .07),
            0 24px 48px rgba(61, 43, 22, .028),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .registration-mix-card {
        box-shadow:
            0 2px 6px rgba(61, 43, 22, .035),
            0 15px 36px rgba(61, 43, 22, .08),
            0 30px 58px rgba(61, 43, 22, .032),
            inset 0 1px 0 rgba(255,255,255,.97) !important;
    }

    .filter-controls {
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .028),
            0 10px 24px rgba(61, 43, 22, .055),
            inset 0 1px 0 rgba(255,255,255,.92) !important;
    }

    .premium-select-menu {
        box-shadow:
            0 7px 16px rgba(56, 39, 19, .075),
            0 20px 44px rgba(56, 39, 19, .13) !important;
    }

    .review-card,
    #reviewDrawer section {
        box-shadow:
            0 2px 5px rgba(67, 48, 24, .03),
            0 12px 28px rgba(67, 48, 24, .065),
            0 22px 44px rgba(67, 48, 24, .025) !important;
    }

    #reviewDrawer {
        box-shadow:
            -12px 0 28px rgba(31, 24, 17, .06),
            -32px 0 68px rgba(31, 24, 17, .12) !important;
    }

    @media (max-width: 767px) {
        .reg-surface,
        .registration-mix-card,
        .summary-card {
            box-shadow:
                0 2px 5px rgba(61, 43, 22, .03),
                0 11px 26px rgba(61, 43, 22, .06) !important;
        }

        .filter-controls {
            box-shadow: 0 7px 18px rgba(61, 43, 22, .045) !important;
        }
    }


    /* =========================================================
       STRONGER FLOATING DEPTH — more visible, still clean
       Final visual override only.
       ========================================================= */

    .reg-surface {
        border-color: #e7ddd1 !important;
        box-shadow:
            0 3px 8px rgba(61, 43, 22, .045),
            0 18px 42px rgba(61, 43, 22, .095),
            0 38px 78px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .summary-card {
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .04),
            0 15px 34px rgba(61, 43, 22, .085),
            0 30px 58px rgba(61, 43, 22, .038),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .summary-card:hover {
        transform: translate3d(0, -3px, 0);
        box-shadow:
            0 4px 9px rgba(61, 43, 22, .05),
            0 21px 46px rgba(61, 43, 22, .115),
            0 40px 76px rgba(61, 43, 22, .048),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .summary-card.is-current {
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .04),
            0 15px 34px rgba(61, 43, 22, .085),
            0 30px 58px rgba(61, 43, 22, .038),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .registration-mix-card {
        border-color: #e7ddd1 !important;
        box-shadow:
            0 3px 8px rgba(61, 43, 22, .045),
            0 19px 44px rgba(61, 43, 22, .10),
            0 38px 72px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .filter-controls {
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .035),
            0 13px 30px rgba(61, 43, 22, .07),
            0 24px 46px rgba(61, 43, 22, .025),
            inset 0 1px 0 rgba(255,255,255,.95) !important;
    }

    .premium-select-trigger,
    .reg-control,
    .reg-secondary-btn,
    .reg-review-btn {
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .025),
            0 7px 16px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .premium-select-menu {
        box-shadow:
            0 8px 18px rgba(56, 39, 19, .09),
            0 24px 52px rgba(56, 39, 19, .15) !important;
    }

    .review-card,
    #reviewDrawer section {
        box-shadow:
            0 3px 7px rgba(67, 48, 24, .035),
            0 15px 34px rgba(67, 48, 24, .08),
            0 28px 54px rgba(67, 48, 24, .032) !important;
    }

    #reviewDrawer {
        box-shadow:
            -14px 0 32px rgba(31, 24, 17, .075),
            -38px 0 82px rgba(31, 24, 17, .145) !important;
    }

    @media (max-width: 767px) {
        .reg-surface,
        .registration-mix-card,
        .summary-card {
            box-shadow:
                0 3px 7px rgba(61, 43, 22, .035),
                0 14px 32px rgba(61, 43, 22, .075),
                0 24px 46px rgba(61, 43, 22, .025) !important;
        }

        .filter-controls {
            box-shadow:
                0 2px 5px rgba(61, 43, 22, .03),
                0 10px 24px rgba(61, 43, 22, .06) !important;
        }
    }


    /* =========================================================
       SARI STANDARD ADMIN HEADER
       Seller Compliance-inspired hierarchy for Account Registrations.
       Header only — dashboard, routes, forms, and JS remain unchanged.
       ========================================================= */

    .registration-page-header {
        margin-bottom: 18px !important;
        padding: 0 !important;
    }

    .registration-page-header-inner {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .registration-page-header-icon {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        border: 1px solid #eadfc9;
        border-radius: 14px;
        background: #fff8eb;
        color: #b77c18;
        box-shadow:
            0 2px 5px rgba(75, 54, 25, .035),
            0 10px 22px rgba(75, 54, 25, .075),
            inset 0 1px 0 rgba(255,255,255,.98);
    }

    .registration-page-header-icon svg {
        width: 18px;
        height: 18px;
    }

    .registration-page-eyebrow {
        margin: 0 0 5px;
        color: #9a7b43;
        font-size: 10px !important;
        font-weight: 600;
        line-height: 1;
        letter-spacing: .17em;
        text-transform: uppercase;
    }

    .registration-page-header h2 {
        margin: 0 !important;
        font-size: clamp(30px, 2vw, 34px) !important;
        font-weight: 700 !important;
        line-height: 1.08 !important;
        letter-spacing: -.04em !important;
    }

    .registration-page-header .registration-title-base {
        color: #17130f;
    }

    .registration-page-header .registration-title-accent {
        color: #d99500;
    }

    .registration-page-subtitle {
        max-width: 830px;
        margin-top: 7px !important;
        color: #81786c;
        font-size: 11.5px !important;
        line-height: 1.65 !important;
    }

    @media (max-width: 767px) {
        .registration-page-header-inner {
            align-items: flex-start;
            gap: 12px;
        }

        .registration-page-header-icon {
            width: 42px;
            height: 42px;
            flex-basis: 42px;
            border-radius: 13px;
        }

        .registration-page-eyebrow {
            font-size: 9px !important;
            letter-spacing: .14em;
        }

        .registration-page-header h2 {
            font-size: 27px !important;
        }

        .registration-page-subtitle {
            margin-top: 6px !important;
            font-size: 10.5px !important;
            line-height: 1.6 !important;
        }
    }


    /* =========================================================
       EXACT SELLER COMPLIANCE HEADER PARITY — FINAL OVERRIDE
       Keeps the Account Registrations wording but copies the exact
       color hierarchy, spacing, and proportions of Seller Compliance.
       ========================================================= */

    .registration-page-header {
        margin-bottom: 16px !important;
        padding: 0 !important;
    }

    .registration-page-header .registration-page-header-inner {
        display: flex;
        align-items: center !important;
        gap: 14px;
    }

    .registration-page-header .registration-page-header-icon {
        display: grid;
        width: 44px !important;
        height: 44px !important;
        flex: 0 0 44px !important;
        place-items: center;
        border: 1px solid #eadfc9 !important;
        border-radius: 14px !important;
        background: #fff8eb !important;
        color: #b77c18 !important;
        box-shadow:
            0 2px 5px rgba(75, 54, 25, .03),
            0 9px 20px rgba(75, 54, 25, .06) !important;
    }

    .registration-page-header .registration-page-header-icon svg {
        width: 18px !important;
        height: 18px !important;
    }

    .registration-page-header .registration-page-eyebrow {
        margin: 0 !important;
        color: #9a7b43 !important;
        -webkit-text-fill-color: #9a7b43 !important;
        font-size: 9px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .registration-page-header h2 {
        margin: 4px 0 0 !important;
        font-size: clamp(1.75rem, 1.55rem + .5vw, 2.15rem) !important;
        font-weight: 700 !important;
        line-height: 1.08 !important;
        letter-spacing: -.04em !important;
    }

    .registration-page-header h2 .registration-title-base {
        color: #17130f !important;
        -webkit-text-fill-color: #17130f !important;
    }

    .registration-page-header h2 .registration-title-accent {
        color: #d99500 !important;
        -webkit-text-fill-color: #d99500 !important;
    }

    .registration-page-header .registration-page-subtitle {
        max-width: 820px !important;
        margin: 6px 0 0 !important;
        color: #81786c !important;
        -webkit-text-fill-color: #81786c !important;
        font-size: clamp(.73rem, .70rem + .08vw, .81rem) !important;
        font-weight: 400 !important;
        line-height: 1.65 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
    }

    @media (max-width: 767px) {
        .registration-page-header .registration-page-header-inner {
            align-items: flex-start !important;
            gap: 12px;
        }

        .registration-page-header .registration-page-header-icon {
            width: 42px !important;
            height: 42px !important;
            flex-basis: 42px !important;
            border-radius: 13px !important;
        }

        .registration-page-header .registration-page-eyebrow {
            font-size: 8.5px !important;
        }

        .registration-page-header h2 {
            font-size: 1.65rem !important;
        }

        .registration-page-header .registration-page-subtitle {
            font-size: .72rem !important;
        }
    }


    /* =========================================================
       ACCOUNT REGISTRATIONS — USER MANAGEMENT STYLE FILTER BAR
       Search + Role + Status + Apply + Reset in one floating toolbar.
       Existing GET filters, search JS, routes, and review logic stay intact.
       ========================================================= */

    .reg-page .registration-filter-surface {
        position: relative;
        z-index: 30;
        overflow: visible;
        padding: 12px;
        border: 1px solid #e7ddd1;
        border-radius: 18px;
        background: #fff;
        box-shadow:
            0 3px 8px rgba(61, 43, 22, .045),
            0 18px 42px rgba(61, 43, 22, .095),
            0 38px 78px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.98);
    }

    .reg-page .registration-filter-bar {
        display: grid;
        grid-template-columns: minmax(360px, 1fr) 185px 175px auto auto;
        gap: 12px;
        align-items: center;
    }

    .reg-page .registration-filter-search {
        position: relative;
        min-width: 0;
    }

    .reg-page .registration-filter-search > svg {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 16px;
        width: 16px;
        height: 16px;
        pointer-events: none;
        color: #9d8f7e;
        transform: translateY(-50%);
    }

    .reg-page .registration-filter-search .reg-control {
        width: 100%;
        height: 44px !important;
        border: 1px solid #e8e0d5 !important;
        border-radius: 12px !important;
        background: #fff !important;
        padding: 0 16px 0 44px !important;
        color: #332c25 !important;
        font-size: 11px !important;
        font-weight: 400 !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .reg-page .registration-filter-search .reg-control::placeholder {
        color: #a69c91 !important;
    }

    .reg-page .registration-filter-search .reg-control:hover {
        border-color: #d8c8b1 !important;
    }

    .reg-page .registration-filter-search .reg-control:focus {
        border-color: #d9a33a !important;
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 10px 24px rgba(61,43,22,.07),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .reg-page .registration-filter-select {
        width: 100%;
        min-width: 0 !important;
    }

    .reg-page .registration-filter-select .premium-select-trigger {
        width: 100%;
        height: 44px !important;
        padding: 0 14px !important;
        border: 1px solid #e8e0d5 !important;
        border-radius: 12px !important;
        background: #fff !important;
        color: #332c25 !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .reg-page .registration-filter-select .premium-select-trigger:hover {
        border-color: #d8c8b1 !important;
        background: #fff !important;
        box-shadow:
            0 2px 5px rgba(61,43,22,.03),
            0 9px 20px rgba(61,43,22,.055),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .reg-page .registration-filter-select.is-open .premium-select-trigger,
    .reg-page .registration-filter-select .premium-select-trigger:focus-visible {
        border-color: #d9a33a !important;
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 10px 24px rgba(61,43,22,.07) !important;
    }

    .reg-page .registration-filter-select .premium-select-chevron {
        width: 14px !important;
        height: 14px !important;
        color: #8b8175 !important;
    }

    .reg-page .registration-filter-select .premium-select-menu {
        top: calc(100% + 8px) !important;
        min-width: 100% !important;
        padding: 6px !important;
        border: 1px solid #e7dfd4 !important;
        border-radius: 14px !important;
        background: #fff !important;
        box-shadow:
            0 8px 18px rgba(47,37,25,.09),
            0 24px 52px rgba(47,37,25,.15) !important;
    }

    .reg-page .registration-filter-select .premium-select-option {
        min-height: 36px !important;
        padding: 0 10px !important;
        border-radius: 10px !important;
        color: #5c534a !important;
        font-size: 10px !important;
        font-weight: 500 !important;
    }

    .reg-page .registration-filter-select .premium-select-option:hover,
    .reg-page .registration-filter-select .premium-select-option:focus-visible {
        background: #fff7e8 !important;
        color: #a8731f !important;
    }

    .reg-page .registration-filter-select .premium-select-option.is-selected {
        background: #fff7e8 !important;
        color: #a8731f !important;
        font-weight: 600 !important;
    }

    .reg-page .registration-filter-select .premium-select-check {
        color: #d99500 !important;
    }

    .reg-page .registration-filter-status-dot {
        width: 8px;
        height: 8px;
        flex: 0 0 8px;
        border-radius: 999px;
        background: #3f9a61;
    }

    .reg-page .registration-filter-apply {
        height: 44px !important;
        min-width: 124px;
        border: 0 !important;
        border-radius: 12px !important;
        padding: 0 18px !important;
        background: #d99500 !important;
        color: #fff !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        white-space: nowrap;
        box-shadow:
            0 3px 7px rgba(183,124,0,.10),
            0 13px 28px rgba(217,149,0,.23) !important;
    }

    .reg-page .registration-filter-apply:hover {
        background: #bd8205 !important;
        transform: translateY(-1px);
        box-shadow:
            0 4px 8px rgba(183,124,0,.12),
            0 16px 34px rgba(217,149,0,.26) !important;
    }

    .reg-page .registration-filter-reset {
        height: 44px !important;
        min-width: 78px;
        border: 1px solid #e6ddd2 !important;
        border-radius: 12px !important;
        padding: 0 16px !important;
        background: #fff !important;
        color: #6f665b !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        white-space: nowrap;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045) !important;
    }

    .reg-page .registration-filter-reset:hover {
        border-color: #d8c8b1 !important;
        background: #faf8f4 !important;
        color: #51483f !important;
    }

    /* Keep the queue card visually separate from the toolbar. */
    .reg-page .registration-queue-card {
        margin-top: 16px !important;
    }

    .reg-page .registration-queue-card .queue-header {
        padding: 16px 20px !important;
        border-bottom: 1px solid #eee8df !important;
        background: #fff !important;
    }

    .reg-page .registration-queue-card .queue-header h3 {
        font-size: 15px !important;
        font-weight: 700 !important;
        letter-spacing: -.02em !important;
    }

    .reg-page .registration-queue-card .queue-header p {
        font-size: 10px !important;
        line-height: 1.55 !important;
    }

    .reg-page .registration-queue-card #visibleCount {
        font-size: 9px !important;
    }

    @media (max-width: 1279px) {
        .reg-page .registration-filter-bar {
            grid-template-columns: minmax(280px, 1fr) 170px 165px auto auto;
            gap: 10px;
        }
    }

    @media (max-width: 1023px) {
        .reg-page .registration-filter-bar {
            grid-template-columns: minmax(0, 1fr) minmax(160px, .42fr);
        }

        .reg-page .registration-filter-search {
            grid-column: 1 / -1;
        }

        .reg-page .registration-filter-apply,
        .reg-page .registration-filter-reset {
            min-width: 0;
            width: 100%;
        }
    }

    @media (max-width: 639px) {
        .reg-page .registration-filter-surface {
            padding: 10px;
            border-radius: 16px;
        }

        .reg-page .registration-filter-bar {
            grid-template-columns: 1fr;
            gap: 9px;
        }

        .reg-page .registration-filter-search {
            grid-column: auto;
        }

        .reg-page .registration-filter-apply,
        .reg-page .registration-filter-reset {
            width: 100%;
        }
    }


    /* =========================================================
       ENTERPRISE REGISTRATION REVIEW MODAL — FINAL OVERRIDE
       Centered, neutral, formal. Existing routes and JS hooks remain.
       ========================================================= */

    .reg-backdrop {
        background: rgba(15, 23, 42, .52) !important;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    #reviewDrawer {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        right: auto !important;
        bottom: auto !important;

        width: min(960px, calc(100vw - 48px)) !important;
        max-width: 960px !important;
        height: min(820px, calc(100vh - 48px)) !important;
        max-height: calc(100vh - 48px) !important;

        border: 1px solid #e2e8f0 !important;
        border-radius: 20px !important;
        background: #ffffff !important;
        box-shadow:
            0 30px 90px rgba(15, 23, 42, .24),
            0 10px 30px rgba(15, 23, 42, .10) !important;

        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        transform: translate(-50%, -47%) scale(.975) !important;
        transform-origin: center !important;

        transition:
            opacity .20s ease,
            transform .24s cubic-bezier(.22, 1, .36, 1),
            visibility 0s linear .24s !important;
    }

    #reviewDrawer.is-open {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        transform: translate(-50%, -50%) scale(1) !important;

        transition:
            opacity .20s ease,
            transform .24s cubic-bezier(.22, 1, .36, 1) !important;
    }

    #reviewDrawer [data-application-detail] {
        background: #ffffff;
    }

    #reviewDrawer .reg-scroll {
        background: #f8fafc;
    }

    #reviewDrawer .enterprise-review-header {
        min-height: 76px;
        border-bottom: 1px solid #e5e7eb !important;
        background: #ffffff !important;
        padding: 16px 20px !important;
    }

    .enterprise-review-header-icon {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;
        border: 1px solid #dbe2ea;
        border-radius: 10px;
        background: #f8fafc;
        color: #334155;
    }

    .enterprise-review-header-icon svg {
        width: 18px;
        height: 18px;
    }

    .enterprise-review-eyebrow {
        margin: 0;
        color: #64748b;
        font-size: 9px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .enterprise-review-title {
        margin: 4px 0 0;
        color: #0f172a;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -.02em;
    }

    .enterprise-close-btn {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #dbe2ea;
        border-radius: 9px;
        background: #fff;
        padding: 0 11px;
        color: #475569;
        font-size: 9px;
        font-weight: 650;
        transition:
            background-color .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    .enterprise-close-btn:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #0f172a;
    }

    .enterprise-close-btn svg {
        width: 14px;
        height: 14px;
    }

    #reviewDrawer .review-card {
        border-color: #e2e8f0 !important;
        background: #fff !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
    }

    #reviewDrawer .detail-tab {
        color: #64748b !important;
        border-color: transparent !important;
    }

    #reviewDrawer .detail-tab.is-active {
        color: #0f172a !important;
        border-color: #2563eb !important;
    }

    #reviewDrawer [data-detail-tab-panel] > section {
        border-color: #e2e8f0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .enterprise-role-badge {
        display: inline-flex;
        align-items: center;
        border: 1px solid #dbe2ea;
        border-radius: 999px;
        background: #f8fafc;
        padding: 5px 9px;
        color: #475569;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .enterprise-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid;
        border-radius: 999px;
        background: #fff;
        padding: 5px 9px;
        font-size: 8px;
        font-weight: 700;
        line-height: 1;
    }

    .enterprise-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
        opacity: .8;
    }

    .enterprise-status--pending {
        border-color: #dbe2ea;
        color: #475569;
    }

    .enterprise-status--approved {
        border-color: #bbd7c5;
        color: #166534;
    }

    .enterprise-status--rejected {
        border-color: #e4bcbc;
        color: #991b1b;
    }

    .enterprise-decision-shell {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        padding: 18px;
    }

    .enterprise-decision-heading {
        margin: 0;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -.015em;
    }

    .enterprise-decision-copy {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 9px;
        line-height: 1.6;
    }

    .enterprise-decision-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 16px;
    }

    .enterprise-decision-card {
        display: flex;
        min-width: 0;
        flex-direction: column;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        padding: 16px;
        box-shadow: 0 1px 2px rgba(15,23,42,.025);
    }

    .enterprise-decision-card:hover {
        border-color: #cbd5e1;
    }

    .enterprise-decision-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid;
        border-radius: 10px;
        background: #fff;
    }

    .enterprise-decision-icon svg {
        width: 17px;
        height: 17px;
    }

    .enterprise-decision-icon--approve {
        border-color: #bbd7c5;
        color: #166534;
    }

    .enterprise-decision-icon--reject {
        border-color: #e4bcbc;
        color: #991b1b;
    }

    .enterprise-decision-card-title {
        margin: 0;
        color: #0f172a;
        font-size: 11px;
        font-weight: 700;
    }

    .enterprise-decision-card-copy {
        margin: 3px 0 0;
        color: #64748b;
        font-size: 8.5px;
        line-height: 1.5;
    }

    .enterprise-field-label {
        display: block;
        margin-top: 16px;
        color: #334155;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .enterprise-field-hint {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 7.5px;
        line-height: 1.45;
    }

    .enterprise-decision-textarea {
        width: 100%;
        min-height: 96px;
        margin-top: 8px;
        resize: vertical;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        background: #fff !important;
        padding: 10px 11px !important;
        color: #0f172a !important;
        font-size: 9px !important;
        line-height: 1.55;
        box-shadow: none !important;
    }

    .enterprise-decision-textarea::placeholder {
        color: #94a3b8 !important;
    }

    .enterprise-decision-textarea:focus {
        outline: none !important;
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .10) !important;
    }

    .enterprise-decision-meta {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 12px;
        color: #64748b;
        font-size: 7.5px;
        line-height: 1.5;
    }

    .enterprise-decision-meta svg {
        width: 13px;
        height: 13px;
        flex: 0 0 13px;
        margin-top: 1px;
    }

    .enterprise-action-btn {
        display: inline-flex;
        width: 100%;
        min-height: 40px;
        margin-top: 14px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 0 14px;
        font-size: 9px;
        font-weight: 700;
        transition:
            transform .15s ease,
            background-color .15s ease,
            border-color .15s ease,
            box-shadow .15s ease;
    }

    .enterprise-action-btn:hover {
        transform: translateY(-1px);
    }

    .enterprise-action-btn svg {
        width: 14px;
        height: 14px;
    }

    .enterprise-action-btn--approve {
        border-color: #166534;
        background: #166534;
        color: #fff;
        box-shadow: 0 5px 14px rgba(22, 101, 52, .14);
    }

    .enterprise-action-btn--approve:hover {
        background: #14532d;
        border-color: #14532d;
    }

    .enterprise-action-btn--reject {
        border-color: #b91c1c;
        background: #fff;
        color: #b91c1c;
        box-shadow: none;
    }

    .enterprise-action-btn--reject:hover {
        background: #fef2f2;
        border-color: #991b1b;
        color: #991b1b;
    }

    .enterprise-warning {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-bottom: 14px;
        border: 1px solid #fecaca;
        border-radius: 12px;
        background: #fff;
        padding: 11px 12px;
        color: #7f1d1d;
        font-size: 8.5px;
        line-height: 1.55;
    }

    .enterprise-warning svg {
        width: 15px;
        height: 15px;
        flex: 0 0 15px;
        margin-top: 1px;
    }

    .enterprise-reviewed-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        padding: 18px;
    }

    .enterprise-reviewed-icon {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;
        border: 1px solid;
        border-radius: 10px;
        background: #fff;
    }

    .enterprise-reviewed-icon svg {
        width: 18px;
        height: 18px;
    }

    .enterprise-reviewed-icon--approved {
        border-color: #bbd7c5;
        color: #166534;
    }

    .enterprise-reviewed-icon--rejected {
        border-color: #e4bcbc;
        color: #991b1b;
    }

    @media (max-width: 767px) {
        #reviewDrawer {
            width: calc(100vw - 20px) !important;
            height: calc(100vh - 20px) !important;
            max-height: calc(100vh - 20px) !important;
            border-radius: 16px !important;
        }

        #reviewDrawer .enterprise-review-header {
            min-height: 68px;
            padding: 13px 14px !important;
        }

        .enterprise-review-header-icon {
            width: 36px;
            height: 36px;
            flex-basis: 36px;
        }

        .enterprise-review-title {
            font-size: 14px;
        }

        .enterprise-decision-grid {
            grid-template-columns: 1fr;
        }

        .enterprise-close-btn span {
            display: none;
        }

        .enterprise-close-btn {
            width: 36px;
            padding: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #reviewDrawer,
        .enterprise-action-btn {
            transition-duration: .01ms !important;
        }
    }

</style>

<div class="reg-page registration-page-shell mx-auto w-full max-w-[1880px] pb-6">

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-[15px] border border-[#cfe2d5] bg-[#f5faf6] px-4 py-3.5 shadow-[0_10px_24px_rgba(62,91,72,.06)]">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#dce9e0] bg-white text-[#56816a]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path>
                </svg>
            </span>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.11em] text-[#56816a]">Success</p>
                <p class="mt-1 text-[11px] leading-5 text-[#55705f]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 flex items-start gap-3 rounded-[15px] border border-[#ead0d0] bg-[#fff7f7] px-4 py-3.5 shadow-[0_10px_24px_rgba(126,70,70,.06)]">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#eedddd] bg-white text-[#a65f5f]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16.5h.01"></path>
                </svg>
            </span>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.11em] text-[#a65f5f]">Action Required</p>
                <p class="mt-1 text-[11px] leading-5 text-[#8d5f5f]">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    {{-- PAGE HEADER — SELLER COMPLIANCE STYLE --}}
    <section class="registration-page-header">
        <div class="registration-page-header-inner">
            <span class="registration-page-header-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="9" cy="8" r="3"></circle>
                    <path d="M3 20a6 6 0 0 1 12 0"></path>
                    <path d="m16 12 2 2 4-4"></path>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="registration-page-eyebrow">Account Registration</p>

                <h2>
                    <span class="registration-title-base">Review account</span>
                    <span class="registration-title-accent">applications</span>
                </h2>

                <p class="registration-page-subtitle">
                    Verify applicant information, inspect documents, then approve or reject registrations from a focused review panel.
                </p>
            </div>
        </div>
    </section>

    {{-- DASHBOARD-STYLE REGISTRATION WORKSPACE --}}
    @php
        $summaryCards = [
            ['Total Applications', $stats['total'], '#9a6b10', '#fff7e6', '#ead6ac', 'users', 'all'],
            ['Pending Review', $stats['pending'], '#a8731f', '#fff7e9', '#ead9b8', 'clock', 'pending'],
            ['Approved', $stats['approved'], '#4f7c60', '#eef7f1', '#d7e6dc', 'check', 'approved'],
            ['Rejected', $stats['rejected'], '#9d5a5a', '#fff2f2', '#ead6d6', 'x', 'rejected'],
        ];

        // Controller V4 supplies all-time counts. The fallback keeps the view
        // safe if the Blade is copied before the controller is replaced.
        $mixCounts = $registrationMix ?? [
            'buyer' => (int) ($stats['buyers'] ?? 0),
            'seller' => (int) ($stats['sellers'] ?? 0),
            'courier' => (int) ($stats['couriers'] ?? 0),
            'logistics' => (int) ($stats['logistics'] ?? 0),
            'rider' => (int) ($stats['riders'] ?? 0),
        ];

        $mixMeta = [
            'buyer' => ['Buyer', '#d99500'],
            'seller' => ['Seller', '#e7b44d'],
            'courier' => ['Courier', '#b98224'],
            'logistics' => ['Logistics', '#8f7655'],
            'rider' => ['Rider', '#c9b38a'],
        ];

        $mixTotal = array_sum($mixCounts);
        $dominantRole = null;
        $dominantCount = 0;
        foreach ($mixCounts as $mixRole => $mixCount) {
            if ((int) $mixCount > $dominantCount) {
                $dominantRole = $mixRole;
                $dominantCount = (int) $mixCount;
            }
        }
        $dominantPercent = $mixTotal > 0 ? round(($dominantCount / $mixTotal) * 100) : 0;
        $mixOffset = 0.0;
    @endphp


    <section class="registration-workspace-layout">
        <div class="registration-main-column">
            {{-- COMPACT SUMMARY METRICS --}}
            <div class="summary-grid grid grid-cols-2 gap-3 xl:grid-cols-4">
                @foreach($summaryCards as [$label, $value, $iconColor, $iconBg, $iconBorder, $icon, $targetStatus])
                    @php
                        $summaryParams = ['status' => $targetStatus];
                        if ($role) $summaryParams['role'] = $role;
                        $isCurrentSummary = $status === $targetStatus;
                    @endphp
            
                    <a
                        href="{{ route('admin.registrations', $summaryParams) }}"
                        class="summary-card {{ $isCurrentSummary ? 'is-current' : '' }} flex items-center justify-between gap-3 px-4 py-4 sm:px-5"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-[10px] font-medium leading-4 text-[#655c52] sm:text-[11px]">{{ $label }}</p>
                            <p class="mt-1 text-[23px] font-bold leading-none tracking-[-.04em] text-[#201b16] sm:text-[25px]">{{ $value }}</p>
                            <div class="mt-2 flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#d6cec4]"></span>
                                <span class="text-[8px] font-medium text-[#91887d] sm:text-[9px]">
                                    View records
                                </span>
                            </div>
                        </div>
            
                        <span
                            class="absolute right-4 top-4 grid h-10 w-10 shrink-0 place-items-center rounded-[12px] border shadow-[0_6px_14px_rgba(69,49,25,.04)] sm:right-5 sm:top-4"
                            style="color: {{ $iconColor }}; background: {{ $iconBg }}; border-color: {{ $iconBorder }};"
                        >
                            @if($icon === 'users')
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="9" cy="7" r="3"></circle>
                                    <path d="M3 20a6 6 0 0 1 12 0"></path>
                                    <path d="M17 6a3 3 0 0 1 0 6"></path>
                                    <path d="M18 15a5 5 0 0 1 3 5"></path>
                                </svg>
                            @elseif($icon === 'clock')
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg>
                            @elseif($icon === 'check')
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="m8 12 2.5 2.5L16 9"></path>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="m9 9 6 6"></path>
                                    <path d="m15 9-6 6"></path>
                                </svg>
                            @endif
                        </span>
                    </a>
                @endforeach
            </div>
            

        {{-- USER MANAGEMENT-STYLE REGISTRATION FILTER BAR --}}
        @php
            $statusOptions = [
                'all' => 'All Status',
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ];

            $roleOptions = [
                '' => 'All Roles',
                'buyer' => 'Buyer',
                'seller' => 'Seller',
                'courier' => 'Courier',
                'logistics' => 'Logistics',
                'rider' => 'Rider',
            ];

            $currentStatusLabel = $statusOptions[$status] ?? 'All Status';
            $currentRoleLabel = $roleOptions[$role ?? ''] ?? 'All Roles';
        @endphp

        <section class="registration-filter-surface mt-4">
            <form method="GET" class="registration-filter-bar">
                {{-- Wide search --}}
                <div class="registration-filter-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.4-3.4"></path>
                    </svg>

                    <input
                        id="applicationSearch"
                        type="search"
                        autocomplete="off"
                        placeholder="Search name, email, contact, or role..."
                        class="reg-control"
                    >
                </div>

                {{-- Role --}}
                <div class="premium-select registration-filter-select" data-premium-select>
                    <input type="hidden" name="role" value="{{ $role ?? '' }}" data-select-input>

                    <button
                        type="button"
                        class="premium-select-trigger"
                        data-select-trigger
                        aria-haspopup="listbox"
                        aria-expanded="false"
                    >
                        <span class="truncate" data-select-label>{{ $currentRoleLabel }}</span>

                        <svg viewBox="0 0 24 24" class="premium-select-chevron" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div class="premium-select-menu" data-select-menu role="listbox" aria-label="Filter by role">
                        @foreach($roleOptions as $value => $label)
                            <button
                                type="button"
                                role="option"
                                aria-selected="{{ ($role ?? '') === $value ? 'true' : 'false' }}"
                                data-select-option
                                data-value="{{ $value }}"
                                class="premium-select-option {{ ($role ?? '') === $value ? 'is-selected' : '' }}"
                            >
                                <span>{{ $label }}</span>

                                <svg viewBox="0 0 24 24" class="premium-select-check" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m7 12 3 3 7-7"></path>
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Status --}}
                <div class="premium-select registration-filter-select" data-premium-select>
                    <input type="hidden" name="status" value="{{ $status }}" data-select-input>

                    <button
                        type="button"
                        class="premium-select-trigger"
                        data-select-trigger
                        aria-haspopup="listbox"
                        aria-expanded="false"
                    >
                        <span class="flex min-w-0 items-center gap-2">
                            <span class="registration-filter-status-dot" aria-hidden="true"></span>
                            <span class="truncate" data-select-label>{{ $currentStatusLabel }}</span>
                        </span>

                        <svg viewBox="0 0 24 24" class="premium-select-chevron" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div class="premium-select-menu" data-select-menu role="listbox" aria-label="Filter by status">
                        @foreach($statusOptions as $value => $label)
                            <button
                                type="button"
                                role="option"
                                aria-selected="{{ $status === $value ? 'true' : 'false' }}"
                                data-select-option
                                data-value="{{ $value }}"
                                class="premium-select-option {{ $status === $value ? 'is-selected' : '' }}"
                            >
                                <span>{{ $label }}</span>

                                <svg viewBox="0 0 24 24" class="premium-select-check" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m7 12 3 3 7-7"></path>
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>

                <button
                    type="submit"
                    class="registration-filter-apply inline-flex items-center justify-center gap-2"
                >
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 6h16"></path>
                        <path d="M7 12h10"></path>
                        <path d="M10 18h4"></path>
                    </svg>
                    Apply Filter
                </button>

                <a
                    href="{{ route('admin.registrations') }}"
                    class="registration-filter-reset inline-flex items-center justify-center"
                >
                    Reset
                </a>
            </form>
        </section>

        {{-- FULL-WIDTH APPLICATION QUEUE --}}
        <section class="reg-surface registration-queue-card overflow-hidden">
            <div class="queue-header">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-bold text-[#2d2721]">Application queue</h3>

                            <span
                                id="visibleCount"
                                class="rounded-full border border-[#e9e1d7] bg-[#f8f6f2] px-2.5 py-1 font-semibold text-[#6f665d]"
                            >
                                Showing {{ $applications->count() }}
                            </span>
                        </div>

                        <p class="mt-1 text-[#81786c]">
                            Open a registration to review the complete applicant profile and protected files.
                        </p>
                    </div>

                    <div class="role-stat-chips flex flex-wrap gap-1.5">
                        <span class="rounded-full border border-[#e7ddc7] bg-[#fff9ec] px-2.5 py-1.5 text-[7px] font-semibold text-[#9a6b10]">Buyer {{ $roleStats['buyers'] ?? $stats['buyers'] }}</span>
                        <span class="rounded-full border border-[#ead9b8] bg-[#fff8eb] px-2.5 py-1.5 text-[7px] font-semibold text-[#a8731f]">Seller {{ $roleStats['sellers'] ?? $stats['sellers'] }}</span>
                        <span class="rounded-full border border-[#d9e7e3] bg-[#f4f9f7] px-2.5 py-1.5 text-[7px] font-semibold text-[#56796f]">Courier {{ $roleStats['couriers'] ?? $stats['couriers'] }}</span>
                        <span class="rounded-full border border-[#e4dcf2] bg-[#faf7fd] px-2.5 py-1.5 text-[7px] font-semibold text-[#725b91]">Logistics {{ $roleStats['logistics'] ?? $stats['logistics'] }}</span>
                        <span class="rounded-full border border-[#d9e7e3] bg-[#f4f9f7] px-2.5 py-1.5 text-[7px] font-semibold text-[#527b6f]">Rider {{ $roleStats['riders'] ?? $stats['riders'] }}</span>
                    </div>
                </div>
            </div>

            <div class="application-table-wrap overflow-x-auto">
                <table class="application-table w-full min-w-[820px] border-collapse">
                    <thead>
                        <tr class="border-b border-[#eee8df] bg-[#fcfbf8] text-left">
                            <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-[.09em] text-[#6f655a]">Applicant</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[.09em] text-[#6f655a]">Contact</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[.09em] text-[#6f655a]">Role</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[.09em] text-[#6f655a]">Status</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[.09em] text-[#6f655a]">Documents</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[.09em] text-[#6f655a]">Submitted</th>
                            <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-[.09em] text-[#6f655a]">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            @php
                                $rowKey = (string) $application->getKey();
                                $initials = mb_strtoupper(mb_substr($application->first_name ?? '', 0, 1))
                                    . mb_strtoupper(mb_substr($application->last_name ?? '', 0, 1));
        
                                $roleTone = match($application->role) {
                                    'buyer' => 'border-[#dae7f2] bg-[#f4f8fc] text-[#537a9f]',
                                    'seller' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]',
                                    'courier' => 'border-[#d9e7e3] bg-[#f2f8f6] text-[#527b6f]',
                                    'logistics' => 'border-[#e4dcf2] bg-[#f8f5fc] text-[#725b91]',
                                    'rider' => 'border-[#d9e7e3] bg-[#f2f8f6] text-[#527b6f]',
                                    default => 'border-[#e3ded7] bg-[#f7f5f2] text-[#746d64]',
                                };
        
                                $statusTone = match($application->status) {
                                    'approved' => 'border-[#cfe1d5] bg-[#f2f8f4] text-[#4f7c60]',
                                    'rejected' => 'border-[#e8cccc] bg-[#fff3f3] text-[#a55a5a]',
                                    default => 'border-[#eadfc9] bg-[#fffaf2] text-[#a8731f]',
                                };
        
                                $requiredDocuments = match($application->role) {
                                    'seller' => [
                                        ['label' => 'Valid ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                                        ['label' => 'Business Permit', 'type' => 'permit', 'available' => (bool) $application->business_permit_path],
                                    ],
                                    'logistics' => [
                                        ['label' => 'Valid ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                                        ['label' => 'Business / DTI Permit', 'type' => 'permit', 'available' => (bool) $application->business_permit_path],
                                    ],
                                    'courier', 'rider' => [
                                        ['label' => 'ID / Driver’s License', 'type' => 'id', 'available' => (bool) $application->id_path],
                                        ['label' => 'OR / CR', 'type' => 'orcr', 'available' => (bool) $application->orcr_path],
                                    ],
                                    default => [
                                        ['label' => 'Valid ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                                    ],
                                };
        
                                $availableDocumentCount = collect($requiredDocuments)->where('available', true)->count();
                                $allDocumentsAvailable = collect($requiredDocuments)->every(fn ($doc) => $doc['available']);
                            @endphp
        
                            <tr
                                data-application-row="{{ $rowKey }}"
                                data-search="{{ strtolower($application->fullName() . ' ' . $application->email . ' ' . $application->contact_no . ' ' . $application->role) }}"
                                tabindex="0"
                                role="button"
                                aria-label="Review {{ $application->fullName() }}"
                                class="border-b border-[#f0ebe4] last:border-b-0"
                            >
                                <td class="px-5 py-3.5 sm:py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-[#e6dfd6] bg-[#faf8f4] text-[9px] font-bold text-[#6e655a]">{{ $initials ?: 'SA' }}</span>
                                        <div class="min-w-0">
                                            <p class="truncate text-[10px] font-bold text-[#2b261f]">{{ $application->fullName() }}</p>
                                            <p class="mt-0.5 truncate text-[9px] text-[#91887d]">ID #{{ $application->getKey() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 sm:py-4">
                                    <p class="max-w-[220px] truncate text-[9px] font-semibold text-[#50483f]">{{ $application->email }}</p>
                                    <p class="mt-0.5 text-[9px] text-[#968d82]">{{ $application->contact_no ?: 'No contact number' }}</p>
                                </td>
                                <td class="px-4 py-3.5 sm:py-4">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-[8px] font-semibold {{ $roleTone }}">{{ strtoupper($application->role) }}</span>
                                </td>
                                <td class="px-4 py-3.5 sm:py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[8px] font-semibold {{ $statusTone }}"><span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>{{ ucfirst($application->status) }}</span>
                                </td>
                                <td class="px-4 py-3.5 sm:py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-7 w-7 place-items-center rounded-lg border {{ $allDocumentsAvailable ? 'border-[#d9e8dd] bg-[#f2f8f4] text-[#56816a]' : 'border-[#eadfc9] bg-[#fffaf2] text-[#a8731f]' }}">
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h7l4 4v14H7z"></path><path d="M14 3v5h5"></path></svg>
                                        </span>
                                        <div>
                                            <p class="text-[9px] font-medium text-[#5f574e]">{{ $availableDocumentCount }}/{{ count($requiredDocuments) }} files</p>
                                            <p class="mt-0.5 text-[8px] {{ $allDocumentsAvailable ? 'text-[#56816a]' : 'text-[#a8731f]' }}">{{ $allDocumentsAvailable ? 'Complete' : 'Needs review' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 sm:py-4">
                                    <p class="text-[9px] font-medium text-[#5f574e]">{{ $application->created_at?->format('M d, Y') }}</p>
                                    <p class="mt-0.5 text-[8px] text-[#a0978c]">{{ $application->created_at?->format('h:i A') }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <button type="button" data-open-review="{{ $rowKey }}" class="reg-review-btn inline-flex h-8 items-center gap-1.5 rounded-lg border border-[#e3d9cc] bg-white px-3 text-[9px] font-medium text-[#5f574e]">
                                        Review
                                        <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m9 18 6-6-6-6"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="mx-auto grid h-11 w-11 place-items-center rounded-full border border-[#e6dfd5] bg-[#fcfbf8] text-[#958a7c]">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg>
                                    </div>
                                    <p class="mt-4 text-[13px] font-bold text-[#3f3831]">No matching registration applications</p>
                                    <p class="mx-auto mt-1.5 max-w-[420px] text-[10px] leading-5 text-[#81786c]">Change the status or role filter to review another group.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        
            <div id="searchEmpty" hidden class="border-t border-[#eee8df] px-6 py-12 text-center">
                <div class="mx-auto grid h-11 w-11 place-items-center rounded-full border border-[#e6dfd5] bg-[#fcfbf8] text-[#958a7c]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold text-[#51483f]">No applicant matches your search.</p>
            </div>
        </section>
        </div>

        {{-- RIGHT SIDEBAR — POSITIONED LIKE THE REFERENCE DASHBOARD --}}
        <aside class="registration-side-column">
            <section class="registration-mix-card px-5 py-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[12px] font-bold tracking-[-.02em] text-[#2d2721]">Registration mix</p>
                        <p class="mt-1 text-[9px] leading-4 text-[#91877c]">All-time applicant distribution by account type.</p>
                    </div>
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] border border-[#eadfc9] bg-[#fff8eb] text-[#b77c18]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 19V9"></path><path d="M10 19V5"></path><path d="M16 19v-7"></path><path d="M22 19V3"></path>
                        </svg>
                    </span>
                </div>

                <div class="mx-auto mt-5 h-[148px] w-[148px]">
                    <div class="relative h-full w-full">
                        <svg viewBox="0 0 100 100" class="registration-mix-ring h-full w-full" aria-label="Registration role distribution">
                            <circle cx="50" cy="50" r="36" fill="none" stroke="#f1ece5" stroke-width="12"></circle>
                            @php $sidebarMixOffset = 0.0; @endphp
                            @if($mixTotal > 0)
                                @foreach($mixCounts as $mixRole => $mixCount)
                                    @php
                                        $mixPercent = $mixTotal > 0 ? (((int) $mixCount / $mixTotal) * 100) : 0;
                                        $dash = number_format($mixPercent, 4, '.', '');
                                        $gap = number_format(max(0, 100 - $mixPercent), 4, '.', '');
                                        $dashOffset = number_format(-$sidebarMixOffset, 4, '.', '');
                                        $sidebarMixOffset += $mixPercent;
                                    @endphp
                                    @if($mixPercent > 0)
                                        <circle
                                            cx="50" cy="50" r="36" fill="none"
                                            stroke="{{ $mixMeta[$mixRole][1] }}" stroke-width="12" stroke-linecap="butt"
                                            pathLength="100" stroke-dasharray="{{ $dash }} {{ $gap }}"
                                            stroke-dashoffset="{{ $dashOffset }}" transform="rotate(-90 50 50)"
                                        ></circle>
                                    @endif
                                @endforeach
                            @endif
                        </svg>
                        <div class="absolute inset-0 grid place-items-center text-center">
                            <div>
                                <p class="text-[24px] font-bold leading-none tracking-[-.05em] text-[#241e18]">{{ $mixTotal }}</p>
                                <p class="mt-1.5 text-[7px] font-semibold uppercase tracking-[.12em] text-[#9a9187]">Registrations</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($mixTotal > 0 && $dominantRole)
                    <div class="mt-4 rounded-[13px] border border-[#eadfc9] bg-[#fffaf0] px-3.5 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[7px] font-semibold uppercase tracking-[.11em] text-[#9e8a65]">Most registrations</p>
                                <p class="mt-1 truncate text-[11px] font-bold text-[#40372e]">{{ $mixMeta[$dominantRole][0] }}</p>
                            </div>
                            <span class="text-[18px] font-bold tracking-[-.04em] text-[#a76f08]">{{ $dominantPercent }}%</span>
                        </div>
                    </div>
                @endif

                <div class="mt-4 space-y-2.5">
                    @foreach($mixCounts as $mixRole => $mixCount)
                        @php
                            $rolePercent = $mixTotal > 0 ? round((((int) $mixCount / $mixTotal) * 100)) : 0;
                        @endphp
                        <a href="{{ route('admin.registrations', ['status' => 'all', 'role' => $mixRole]) }}" class="mix-role-row group flex items-center gap-2.5 rounded-[10px] px-2 py-1.5">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $mixMeta[$mixRole][1] }};"></span>
                            <span class="min-w-0 flex-1 truncate text-[9px] font-medium text-[#62594f]">{{ $mixMeta[$mixRole][0] }}</span>
                            <span class="text-[9px] font-semibold text-[#2f2923]">{{ (int) $mixCount }}</span>
                            <span class="w-8 text-right text-[8px] text-[#9a9187]">{{ $rolePercent }}%</span>
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('admin.registrations', ['status' => 'all']) }}" class="mt-5 inline-flex h-9 w-full items-center justify-center gap-2 rounded-[11px] border border-[#e2d7c8] bg-white text-[9px] font-semibold text-[#665d53] shadow-[0_5px_14px_rgba(68,49,25,.035)] transition hover:border-[#d8c29a] hover:bg-[#fffaf1] hover:text-[#9d6908]">
                    View all registrations
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6"></path></svg>
                </a>
            </section>
        </aside>
    </section>
</div>

{{-- REVIEW DRAWER BACKDROP --}}
<div id="reviewBackdrop" class="reg-backdrop fixed inset-0 z-[80]" aria-hidden="true"></div>

{{-- REVIEW DRAWER --}}
<aside
    id="reviewDrawer"
    class="reg-drawer z-[90] overflow-hidden"
    role="dialog"
    aria-modal="true"
    aria-label="Registration application review"
    aria-hidden="true"
>
    @foreach($applications as $application)
        @php
            $detailKey = (string) $application->getKey();
            $initials = mb_strtoupper(mb_substr($application->first_name ?? '', 0, 1))
                . mb_strtoupper(mb_substr($application->last_name ?? '', 0, 1));

            $roleTone = match($application->role) {
                'buyer' => 'border-[#dae7f2] bg-[#f4f8fc] text-[#537a9f]',
                'seller' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]',
                'courier' => 'border-[#d9e7e3] bg-[#f2f8f6] text-[#527b6f]',
                'logistics' => 'border-[#e4dcf2] bg-[#f8f5fc] text-[#725b91]',
                'rider' => 'border-[#d9e7e3] bg-[#f2f8f6] text-[#527b6f]',
                default => 'border-[#e3ded7] bg-[#f7f5f2] text-[#746d64]',
            };

            $statusTone = match($application->status) {
                'approved' => 'border-[#cfe1d5] bg-[#f2f8f4] text-[#4f7c60]',
                'rejected' => 'border-[#e8cccc] bg-[#fff3f3] text-[#a55a5a]',
                default => 'border-[#eadfc9] bg-[#fffaf2] text-[#a8731f]',
            };

            $requiredDocuments = match($application->role) {
                'seller' => [
                    ['label' => 'Valid ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                    ['label' => 'Business Permit', 'type' => 'permit', 'available' => (bool) $application->business_permit_path],
                ],
                'logistics' => [
                    ['label' => 'Valid ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                    ['label' => 'Business / DTI Permit', 'type' => 'permit', 'available' => (bool) $application->business_permit_path],
                ],
                'courier', 'rider' => [
                    ['label' => 'ID / Driver’s License', 'type' => 'id', 'available' => (bool) $application->id_path],
                    ['label' => 'OR / CR', 'type' => 'orcr', 'available' => (bool) $application->orcr_path],
                ],
                default => [
                    ['label' => 'Valid ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                ],
            };
            $allDocumentsAvailable = collect($requiredDocuments)->every(fn ($doc) => $doc['available']);
        @endphp

        <div data-application-detail="{{ $detailKey }}" hidden class="flex h-full flex-col">
            {{-- CENTERED ENTERPRISE MODAL HEADER --}}
            <div class="enterprise-review-header">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="enterprise-review-header-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="9" cy="8" r="3"></circle>
                                <path d="M3.5 20a5.5 5.5 0 0 1 11 0"></path>
                                <path d="M16 7h5"></path>
                                <path d="M16 11h5"></path>
                                <path d="M16 15h3"></path>
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <p class="enterprise-review-eyebrow">Registration Review</p>
                            <h2 class="enterprise-review-title">
                                Application #{{ $application->getKey() }} · {{ $application->fullName() }}
                            </h2>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <span class="enterprise-status {{ $application->status === 'approved' ? 'enterprise-status--approved' : ($application->status === 'rejected' ? 'enterprise-status--rejected' : 'enterprise-status--pending') }}">
                            {{ ucfirst($application->status) }}
                        </span>

                        <button
                            type="button"
                            data-close-review
                            class="enterprise-close-btn"
                            aria-label="Close application review"
                        >
                            <span>Close</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="m7 7 10 10"></path>
                                <path d="m17 7-10 10"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="reg-scroll flex-1 overflow-y-auto">
                {{-- PROFILE HERO --}}
                <div class="bg-white px-5 py-5 sm:px-6">
                    <div class="review-card rounded-[16px] border border-[#e2e8f0] bg-white p-4 sm:p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex items-start gap-4">
                                <span class="grid h-[72px] w-[72px] shrink-0 place-items-center rounded-full border border-[#e7dfd5] bg-white text-[18px] font-bold tracking-[-.04em] text-[#6b6258] shadow-sm">{{ $initials ?: 'SA' }}</span>
                                <div class="min-w-0 pt-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-[19px] font-bold tracking-[-.03em] text-[#211c17]">{{ $application->fullName() }}</h3>
                                        <span class="enterprise-role-badge">{{ strtoupper($application->role) }}</span>
                                    </div>
                                    <p class="mt-1 text-[9px] text-[#91887d]">Registration #{{ $application->getKey() }}</p>

                                    <div class="mt-3 grid grid-cols-1 gap-1.5 text-[9px] text-[#6f665b] sm:grid-cols-2 sm:gap-x-6">
                                        <span class="inline-flex min-w-0 items-center gap-2"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0 text-[#9a9085]" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg><span class="truncate">{{ $application->email }}</span></span>
                                        <span class="inline-flex items-center gap-2"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0 text-[#9a9085]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 4h4l2 5-2.5 1.5a15 15 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2C9.7 21 3 14.3 3 6a2 2 0 0 1 2-2Z"></path></svg>{{ $application->contact_no ?: 'No contact number' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2 sm:flex-col sm:items-end">
                                <span class="enterprise-status {{ $application->status === 'approved' ? 'enterprise-status--approved' : ($application->status === 'rejected' ? 'enterprise-status--rejected' : 'enterprise-status--pending') }}">{{ strtoupper($application->status) }}</span>
                                <span class="text-[7px] text-[#9b9389]">Submitted {{ $application->created_at?->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 border-t border-[#eee8df] pt-4 sm:grid-cols-4">
                            <div>
                                <p class="text-[7px] uppercase tracking-[.08em] text-[#9b9288]">Sex</p>
                                <p class="mt-1 text-[9px] font-semibold text-[#4a433c]">{{ $application->sex ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[7px] uppercase tracking-[.08em] text-[#9b9288]">Age</p>
                                <p class="mt-1 text-[9px] font-semibold text-[#4a433c]">{{ $application->age ? $application->age . ' yrs' : '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[7px] uppercase tracking-[.08em] text-[#9b9288]">Birthday</p>
                                <p class="mt-1 text-[9px] font-semibold text-[#4a433c]">{{ $application->birthday?->format('M d, Y') ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[7px] uppercase tracking-[.08em] text-[#9b9288]">Documents</p>
                                <p class="mt-1 text-[9px] font-semibold {{ $allDocumentsAvailable ? 'text-[#56816a]' : 'text-[#a8731f]' }}">{{ $allDocumentsAvailable ? 'Complete' : 'Needs review' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABS --}}
                <div class="sticky top-0 z-10 border-y border-[#eee8df] bg-white/95 px-5 sm:px-6">
                    <div class="flex gap-6 overflow-x-auto">
                        <button type="button" class="detail-tab is-active whitespace-nowrap border-b-2 border-transparent px-1 py-3 text-[9px] font-semibold text-[#6f665b]" data-detail-tab="overview" data-detail-owner="{{ $detailKey }}">Applicant Information</button>
                        <button type="button" class="detail-tab whitespace-nowrap border-b-2 border-transparent px-1 py-3 text-[9px] font-semibold text-[#6f665b]" data-detail-tab="documents" data-detail-owner="{{ $detailKey }}">Verification Files</button>
                        <button type="button" class="detail-tab whitespace-nowrap border-b-2 border-transparent px-1 py-3 text-[9px] font-semibold text-[#6f665b]" data-detail-tab="decision" data-detail-owner="{{ $detailKey }}">Decision</button>
                    </div>
                </div>

                {{-- OVERVIEW --}}
                <div data-detail-tab-panel="overview" data-detail-owner="{{ $detailKey }}" class="space-y-3 px-5 py-5 sm:px-6">
                    <section class="rounded-[16px] border border-[#eee8df] bg-white p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="text-[11px] font-bold text-[#312b25]">Applicant information</h4>
                            <span class="text-[7px] uppercase tracking-[.10em] text-[#a0978c]">Personal details</span>
                        </div>

                        @php
                            $baseInfo = [
                                ['Full Name', $application->fullName()],
                                ['Role', ucfirst($application->role)],
                                ['Email Address', $application->email],
                                ['Contact Number', $application->contact_no],
                                ['Sex', $application->sex],
                                ['Age', $application->age ? $application->age . ' years old' : null],
                                ['Birthday', $application->birthday?->format('M d, Y')],
                                ['Submitted', $application->created_at?->format('M d, Y h:i A')],
                            ];
                        @endphp

                        <div class="mt-4 grid grid-cols-1 gap-x-7 gap-y-3 sm:grid-cols-2">
                            @foreach($baseInfo as [$label, $value])
                                <div class="grid grid-cols-[105px_1fr] gap-3 text-[9px]">
                                    <span class="text-[#958c80]">{{ $label }}</span>
                                    <span class="break-words font-medium text-[#494139]">{{ $value ?: '—' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-[16px] border border-[#eee8df] bg-white p-4 sm:p-5">
                        <div class="flex items-center gap-2">
                            <span class="grid h-8 w-8 place-items-center rounded-lg border border-[#eee6dc] bg-[#fcfbf8] text-[#8f8375]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
                            </span>
                            <h4 class="text-[11px] font-bold text-[#312b25]">Registered address</h4>
                        </div>
                        <p class="mt-3 text-[9px] leading-5 text-[#5f574e]">{{ $application->street_address }}, {{ $application->barangay_name }}, {{ $application->municipality_name }}, {{ $application->province_name }}</p>
                    </section>

                    @if(in_array($application->role, ['seller', 'logistics'], true))
                        <section class="rounded-[16px] border border-[#eee8df] bg-white p-4 sm:p-5">
                            <h4 class="text-[11px] font-bold text-[#312b25]">Business information</h4>
                            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="rounded-xl bg-[#faf9f6] p-3.5">
                                    <p class="text-[8px] text-[#958c80]">{{ $application->role === 'logistics' ? 'Logistics / Sorting Center Name' : 'Business Name' }}</p>
                                    <p class="mt-1.5 text-[9px] font-semibold text-[#403a33]">{{ $application->business_name ?: '—' }}</p>
                                </div>
                                @if($application->role === 'seller')
                                    <div class="rounded-xl bg-[#faf9f6] p-3.5">
                                        <p class="text-[8px] text-[#958c80]">Line of Business</p>
                                        <p class="mt-1.5 text-[9px] font-semibold text-[#403a33]">{{ $application->line_of_business ?: '—' }}</p>
                                    </div>
                                @endif
                            </div>
                        </section>
                    @elseif(in_array($application->role, ['courier', 'rider'], true))
                        <section class="rounded-[16px] border border-[#eee8df] bg-white p-4 sm:p-5">
                            <h4 class="text-[11px] font-bold text-[#312b25]">Vehicle information</h4>
                            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="rounded-xl bg-[#faf9f6] p-3.5"><p class="text-[8px] text-[#958c80]">Vehicle</p><p class="mt-1.5 text-[9px] font-semibold text-[#403a33]">{{ $application->vehicle_type ?: '—' }}</p></div>
                                <div class="rounded-xl bg-[#faf9f6] p-3.5"><p class="text-[8px] text-[#958c80]">Plate Number</p><p class="mt-1.5 text-[9px] font-semibold tracking-[.04em] text-[#403a33]">{{ $application->plate_number ?: '—' }}</p></div>
                            </div>
                        </section>
                    @endif

                    @if($application->admin_note)
                        <section class="rounded-[16px] border border-[#eee8df] bg-[#fcfaf7] p-4 sm:p-5">
                            <p class="text-[8px] font-bold uppercase tracking-[.09em] text-[#958c80]">Admin Note</p>
                            <p class="mt-2 text-[9px] leading-5 text-[#756d63]">{{ $application->admin_note }}</p>
                        </section>
                    @endif
                </div>

                {{-- DOCUMENTS --}}
                <div data-detail-tab-panel="documents" data-detail-owner="{{ $detailKey }}" hidden class="px-5 py-5 sm:px-6">
                    <section class="rounded-[16px] border border-[#eee8df] bg-white p-4 sm:p-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-[11px] font-bold text-[#312b25]">Verification documents</h4>
                                <p class="mt-1 text-[8px] leading-4 text-[#91887d]">Open every protected file before making a registration decision.</p>
                            </div>
                            <span class="inline-flex w-fit rounded-full border px-2.5 py-1 text-[7px] font-semibold {{ $allDocumentsAvailable ? 'border-[#d5e5da] bg-[#f3f9f5] text-[#56816a]' : 'border-[#ead2d2] bg-[#fff5f5] text-[#a65d5d]' }}">{{ $allDocumentsAvailable ? 'Complete' : 'Missing required file' }}</span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach($requiredDocuments as $document)
                                <article class="rounded-[14px] border {{ $document['available'] ? 'border-[#e7e1d8]' : 'border-[#ead4d4]' }} bg-[#fcfbf8] p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border {{ $document['available'] ? 'border-[#e7e1d8] bg-white text-[#83786b]' : 'border-[#ead4d4] bg-[#fff7f7] text-[#a65d5d]' }}">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h7l4 4v14H7z"></path><path d="M14 3v5h5"></path></svg>
                                        </span>
                                        <span class="rounded-full px-2 py-1 text-[7px] font-semibold {{ $document['available'] ? 'bg-[#eef7f1] text-[#56816a]' : 'bg-[#fff0f0] text-[#a65d5d]' }}">{{ $document['available'] ? 'Available' : 'Missing' }}</span>
                                    </div>
                                    <p class="mt-3 text-[9px] font-bold text-[#514a42]">{{ $document['label'] }}</p>
                                    <p class="mt-1 text-[7px] leading-4 text-[#958c80]">Protected verification document</p>

                                    @if($document['available'])
                                        <a href="{{ route('admin.registrations.document', [$application, $document['type']]) }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex h-9 w-full items-center justify-center gap-2 rounded-xl border border-[#ddd7ce] bg-white text-[8px] font-semibold text-[#62594e] transition hover:border-[#dec89d] hover:bg-[#fffaf2] hover:text-[#a8731f]">
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                                            Open document
                                        </a>
                                    @else
                                        <div class="mt-3 flex h-9 items-center justify-center rounded-xl border border-dashed border-[#e2caca] bg-[#fff8f8] text-[8px] font-semibold text-[#9e6969]">File not submitted</div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                </div>

                {{-- DECISION --}}
                <div data-detail-tab-panel="decision" data-detail-owner="{{ $detailKey }}" hidden class="px-5 py-5 sm:px-6">
                    @if($application->status === 'pending')
                        @if(!$allDocumentsAvailable)
                            <div class="enterprise-warning">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 7.5v6"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                                <span>
                                    Some required verification files are missing. Confirm the applicant's documents before making a final decision.
                                </span>
                            </div>
                        @endif

                        @if($application->role === 'rider')
                            <div class="enterprise-decision-shell">
                                <div class="flex items-start gap-3">
                                    <span class="enterprise-review-header-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="8" cy="8" r="3"></circle>
                                            <circle cx="17" cy="9" r="2"></circle>
                                            <path d="M3 20c.7-4 2.5-6 5-6s4.3 2 5 6"></path>
                                            <path d="M14 14c2.8.2 4.6 1.8 5.4 5"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <h4 class="enterprise-decision-heading">Decision managed by Logistics</h4>
                                        <p class="enterprise-decision-copy">
                                            Admin access is read-only for rider approval. Review the profile and protected documents here; the final approve or reject action is completed from Logistics → Rider Applications.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="enterprise-decision-shell">
                                <div>
                                    <h4 class="enterprise-decision-heading">Final registration decision</h4>
                                    <p class="enterprise-decision-copy">
                                        Confirm the applicant information and verification files before completing this review. The selected decision is recorded against the registration.
                                    </p>
                                </div>

                                <div class="enterprise-decision-grid">
                                    {{-- APPROVE --}}
                                    <form
                                        method="POST"
                                        action="{{ route('admin.registrations.approve', $application) }}"
                                        class="enterprise-decision-card"
                                    >
                                        @csrf
                                        <input type="hidden" name="return_role" value="{{ $role ?? '' }}">

                                        <div class="flex items-start gap-3">
                                            <span class="enterprise-decision-icon enterprise-decision-icon--approve" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <path d="m8 12 2.5 2.5L16 9"></path>
                                                </svg>
                                            </span>

                                            <div>
                                                <p class="enterprise-decision-card-title">Approve registration</p>
                                                <p class="enterprise-decision-card-copy">
                                                    Approve the submitted information and activate the applicant's SARI account.
                                                </p>
                                            </div>
                                        </div>

                                        <label class="enterprise-field-label" for="approve-note-{{ $detailKey }}">
                                            Reviewer note <span class="normal-case tracking-normal text-[#94a3b8]">(optional)</span>
                                        </label>
                                        <p class="enterprise-field-hint">
                                            Add internal context or a short approval message when needed.
                                        </p>

                                        <textarea
                                            id="approve-note-{{ $detailKey }}"
                                            name="admin_note"
                                            maxlength="1500"
                                            rows="4"
                                            placeholder="Add an optional approval note..."
                                            class="enterprise-decision-textarea"
                                        ></textarea>

                                        <div class="enterprise-decision-meta">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="M12 11v5"></path>
                                                <path d="M12 8h.01"></path>
                                            </svg>
                                            <span>Approval will create or activate the eligible account and complete this review.</span>
                                        </div>

                                        <button type="submit" class="enterprise-action-btn enterprise-action-btn--approve">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path d="m7 12 3 3 7-7"></path>
                                            </svg>
                                            Approve registration
                                        </button>
                                    </form>

                                    {{-- REJECT --}}
                                    <form
                                        method="POST"
                                        action="{{ route('admin.registrations.reject', $application) }}"
                                        class="enterprise-decision-card"
                                    >
                                        @csrf
                                        <input type="hidden" name="return_role" value="{{ $role ?? '' }}">

                                        <div class="flex items-start gap-3">
                                            <span class="enterprise-decision-icon enterprise-decision-icon--reject" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <path d="m8.5 8.5 7 7"></path>
                                                    <path d="m15.5 8.5-7 7"></path>
                                                </svg>
                                            </span>

                                            <div>
                                                <p class="enterprise-decision-card-title">Reject registration</p>
                                                <p class="enterprise-decision-card-copy">
                                                    Decline the application and provide a clear reason the applicant can act on.
                                                </p>
                                            </div>
                                        </div>

                                        <label class="enterprise-field-label" for="reject-note-{{ $detailKey }}">
                                            Rejection reason <span class="normal-case tracking-normal text-[#991b1b]">(required)</span>
                                        </label>
                                        <p class="enterprise-field-hint">
                                            Keep the reason specific, professional, and related to the submitted registration.
                                        </p>

                                        <textarea
                                            id="reject-note-{{ $detailKey }}"
                                            name="admin_note"
                                            required
                                            minlength="5"
                                            maxlength="1500"
                                            rows="4"
                                            placeholder="Explain why this registration cannot be approved..."
                                            class="enterprise-decision-textarea"
                                        ></textarea>

                                        <div class="enterprise-decision-meta">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="M12 7.5v6"></path>
                                                <path d="M12 17h.01"></path>
                                            </svg>
                                            <span>The rejection reason is retained with the application review record.</span>
                                        </div>

                                        <button type="submit" class="enterprise-action-btn enterprise-action-btn--reject">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path d="m8 8 8 8"></path>
                                                <path d="m16 8-8 8"></path>
                                            </svg>
                                            Reject registration
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="enterprise-reviewed-card">
                            <div class="flex items-start gap-3">
                                <span class="enterprise-reviewed-icon {{ $application->status === 'approved' ? 'enterprise-reviewed-icon--approved' : 'enterprise-reviewed-icon--rejected' }}" aria-hidden="true">
                                    @if($application->status === 'approved')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <path d="m8 12 2.5 2.5L16 9"></path>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <path d="m9 9 6 6"></path>
                                            <path d="m15 9-6 6"></path>
                                        </svg>
                                    @endif
                                </span>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="enterprise-decision-heading">
                                            Review completed
                                        </h4>
                                        <span class="enterprise-status {{ $application->status === 'approved' ? 'enterprise-status--approved' : 'enterprise-status--rejected' }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </div>

                                    <p class="enterprise-decision-copy">
                                        This registration has already been reviewed. Applicant information, verification files, and the recorded decision remain available for reference.
                                    </p>

                                    @if($application->status === 'approved')
                                        <div class="enterprise-decision-meta">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M5 12.5 9.2 17 19 7"></path>
                                            </svg>
                                            <span>The approved account has been created and is active.</span>
                                        </div>
                                    @elseif($application->admin_note)
                                        <div class="mt-4 border-t border-[#e2e8f0] pt-4">
                                            <p class="enterprise-field-label !mt-0">Recorded rejection reason</p>
                                            <p class="mt-2 text-[9px] leading-5 text-[#475569]">{{ $application->admin_note }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                </div>
            </div>
        </div>
    @endforeach
</aside>

<script>
(function () {
    const search = document.getElementById('applicationSearch');
    const rows = Array.from(document.querySelectorAll('[data-application-row]'));
    const details = Array.from(document.querySelectorAll('[data-application-detail]'));
    const openButtons = Array.from(document.querySelectorAll('[data-open-review]'));
    const closeButtons = Array.from(document.querySelectorAll('[data-close-review]'));
    const drawer = document.getElementById('reviewDrawer');
    const backdrop = document.getElementById('reviewBackdrop');
    const empty = document.getElementById('searchEmpty');
    const count = document.getElementById('visibleCount');
    const premiumSelects = Array.from(document.querySelectorAll('[data-premium-select]'));
    let selectedKey = null;

    function closePremiumSelects(except) {
        premiumSelects.forEach(function (select) {
            if (select === except) return;
            select.classList.remove('is-open');
            select.querySelector('[data-select-trigger]')?.setAttribute('aria-expanded', 'false');
        });
    }

    premiumSelects.forEach(function (select) {
        const trigger = select.querySelector('[data-select-trigger]');
        const input = select.querySelector('[data-select-input]');
        const label = select.querySelector('[data-select-label]');
        const options = Array.from(select.querySelectorAll('[data-select-option]'));

        trigger?.addEventListener('click', function (event) {
            event.stopPropagation();
            const willOpen = !select.classList.contains('is-open');
            closePremiumSelects(select);
            select.classList.toggle('is-open', willOpen);
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });

        options.forEach(function (option) {
            option.addEventListener('click', function (event) {
                event.stopPropagation();
                const value = option.dataset.value ?? '';
                if (input) input.value = value;
                if (label) label.textContent = option.querySelector('span')?.textContent?.trim() || value;

                options.forEach(function (candidate) {
                    const selected = candidate === option;
                    candidate.classList.toggle('is-selected', selected);
                    candidate.setAttribute('aria-selected', selected ? 'true' : 'false');
                });

                select.classList.remove('is-open');
                trigger?.setAttribute('aria-expanded', 'false');
                trigger?.focus({ preventScroll: true });
            });
        });
    });

    document.addEventListener('click', function () {
        closePremiumSelects();
    });

    function selectDetail(key) {
        selectedKey = key ? String(key) : null;

        rows.forEach(function (row) {
            row.classList.toggle('is-selected', row.dataset.applicationRow === selectedKey);
        });

        details.forEach(function (detail) {
            detail.hidden = detail.dataset.applicationDetail !== selectedKey;
        });
    }

    function openReview(key) {
        selectDetail(key);
        if (!selectedKey || !drawer || !backdrop) return;

        drawer.classList.add('is-open');
        backdrop.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        backdrop.setAttribute('aria-hidden', 'false');
        document.body.classList.add('reg-review-open');

        requestAnimationFrame(function () {
            const closeButton = drawer.querySelector('[data-close-review]');
            closeButton?.focus({ preventScroll: true });
        });
    }

    function closeReview() {
        if (!drawer || !backdrop) return;

        drawer.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        backdrop.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('reg-review-open');
        rows.forEach(function (row) { row.classList.remove('is-selected'); });
        selectedKey = null;
    }

    function filterRows() {
        const query = (search?.value || '').trim().toLowerCase();
        let visible = 0;

        rows.forEach(function (row) {
            const haystack = (row.dataset.search || '').toLowerCase();
            const match = !query || haystack.includes(query);
            row.hidden = !match;
            if (match) visible++;
        });

        if (empty) empty.hidden = visible !== 0 || rows.length === 0;
        if (count) count.textContent = 'Showing ' + visible;
    }

    rows.forEach(function (row) {
        row.addEventListener('click', function (event) {
            if (event.target.closest('a, button, input, textarea, select, form')) return;
            openReview(row.dataset.applicationRow);
        });

        row.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openReview(row.dataset.applicationRow);
            }
        });
    });

    openButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            openReview(button.dataset.openReview);
        });
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeReview);
    });

    backdrop?.addEventListener('click', closeReview);

    document.addEventListener('keydown', function (event) {
        if (
            event.key === 'Tab'
            && drawer?.classList.contains('is-open')
        ) {
            const focusable = Array.from(
                drawer.querySelectorAll(
                    'button:not([disabled]), a[href], input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
                )
            ).filter(function (element) {
                return !element.closest('[hidden]')
                    && element.offsetParent !== null;
            });

            if (focusable.length > 0) {
                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        }

        if (event.key !== 'Escape') return;
        closePremiumSelects();
        if (drawer?.classList.contains('is-open')) closeReview();
    });

    document.querySelectorAll('[data-detail-tab]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            const owner = tab.dataset.detailOwner;
            const tabName = tab.dataset.detailTab;

            document.querySelectorAll('[data-detail-tab][data-detail-owner="' + owner + '"]').forEach(function (candidate) {
                candidate.classList.toggle('is-active', candidate === tab);
            });

            document.querySelectorAll('[data-detail-tab-panel][data-detail-owner="' + owner + '"]').forEach(function (panel) {
                panel.hidden = panel.dataset.detailTabPanel !== tabName;
            });
        });
    });

    search?.addEventListener('input', filterRows);
    filterRows();

    // The review panel intentionally opens only after an explicit Admin click.
})();
</script>

@endsection
