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

    @media (max-width: 767px) {
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


    /* ============================================================
       ACCOUNT REGISTRATIONS — FINAL COMPACT SIZING LAYER
       Visual sizing only. No PHP data, routes, forms, filtering,
       review actions, protected-document routes, or JS are changed.
       ============================================================ */

    .reg-page.registration-page-shell {
        max-width: 1640px !important;
        padding-bottom: 18px !important;
    }

    /* ---------------- PAGE HEADER ---------------- */
    .registration-page-header {
        margin-bottom: 12px !important;
    }

    .registration-page-header .registration-page-header-inner {
        gap: 10px !important;
    }

    .registration-page-header .registration-page-header-icon {
        width: 36px !important;
        height: 36px !important;
        flex-basis: 36px !important;
        border-radius: 10px !important;
    }

    .registration-page-header .registration-page-header-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .registration-page-header .registration-page-eyebrow {
        font-size: 7px !important;
        letter-spacing: .13em !important;
    }

    .registration-page-header h2 {
        margin-top: 3px !important;
        font-size: clamp(22px, 1.55vw, 27px) !important;
        line-height: 1.08 !important;
        letter-spacing: -.035em !important;
    }

    .registration-page-header .registration-page-subtitle {
        max-width: 760px !important;
        margin-top: 5px !important;
        font-size: 9.5px !important;
        line-height: 1.55 !important;
    }

    /* ---------------- SUMMARY CARDS ---------------- */
    .summary-grid {
        gap: 9px !important;
    }

    .summary-card {
        min-height: 78px !important;
        border-radius: 13px !important;
        padding: 11px 13px !important;
    }

    .summary-card p:first-child {
        font-size: 8.5px !important;
        line-height: 1.3 !important;
    }

    .summary-card p:nth-child(2) {
        margin-top: 3px !important;
        font-size: 20px !important;
        line-height: 1 !important;
    }

    .summary-card > div > div:last-child {
        margin-top: 6px !important;
        gap: 5px !important;
    }

    .summary-card > div > div:last-child span:last-child {
        font-size: 7.5px !important;
    }

    .summary-card > span.absolute {
        right: 12px !important;
        top: 12px !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 9px !important;
    }

    .summary-card > span.absolute svg {
        width: 14px !important;
        height: 14px !important;
    }

    /* ---------------- WORKSPACE / RIGHT MIX ---------------- */
    .registration-workspace-layout {
        gap: 12px !important;
    }

    @media (min-width: 1180px) {
        .registration-workspace-layout {
            grid-template-columns: minmax(0, 1fr) 248px !important;
            gap: 12px !important;
        }

        .registration-side-column {
            top: 12px !important;
        }
    }

    .registration-mix-card {
        border-radius: 14px !important;
        padding: 14px !important;
    }

    .registration-mix-card > div:first-child {
        gap: 8px !important;
    }

    .registration-mix-card > div:first-child p:first-child {
        font-size: 10.5px !important;
    }

    .registration-mix-card > div:first-child p:nth-child(2) {
        margin-top: 3px !important;
        font-size: 7.8px !important;
        line-height: 1.45 !important;
    }

    .registration-mix-card > div:first-child > span {
        width: 28px !important;
        height: 28px !important;
        border-radius: 8px !important;
    }

    .registration-mix-card > div:first-child > span svg {
        width: 13px !important;
        height: 13px !important;
    }

    .registration-mix-card > .mx-auto.mt-5 {
        width: 118px !important;
        height: 118px !important;
        margin-top: 12px !important;
    }

    .registration-mix-card > .mx-auto.mt-5 p:first-child {
        font-size: 20px !important;
    }

    .registration-mix-card > .mx-auto.mt-5 p:last-child {
        margin-top: 4px !important;
        font-size: 6.5px !important;
    }

    .registration-mix-card > .mt-4.rounded-\[13px\] {
        margin-top: 10px !important;
        border-radius: 10px !important;
        padding: 8px 10px !important;
    }

    .registration-mix-card > .mt-4.rounded-\[13px\] p:first-child {
        font-size: 6.5px !important;
    }

    .registration-mix-card > .mt-4.rounded-\[13px\] p:nth-child(2) {
        margin-top: 3px !important;
        font-size: 9.5px !important;
    }

    .registration-mix-card > .mt-4.rounded-\[13px\] span {
        font-size: 15px !important;
    }

    .registration-mix-card > .mt-4.space-y-2\.5 {
        margin-top: 10px !important;
    }

    .mix-role-row {
        gap: 7px !important;
        border-radius: 8px !important;
        padding: 5px 6px !important;
    }

    .mix-role-row > span:first-child {
        width: 7px !important;
        height: 7px !important;
    }

    .mix-role-row span {
        font-size: 8px !important;
    }

    .registration-mix-card > a:last-child {
        height: 32px !important;
        margin-top: 12px !important;
        border-radius: 9px !important;
        font-size: 8px !important;
    }

    /* ---------------- FILTER TOOLBAR ---------------- */
    .reg-page .registration-filter-surface {
        margin-top: 11px !important;
        padding: 9px !important;
        border-radius: 14px !important;
    }

    .reg-page .registration-filter-bar {
        grid-template-columns: minmax(300px, 1fr) 150px 145px auto auto !important;
        gap: 8px !important;
    }

    .reg-page .registration-filter-search > svg {
        left: 12px !important;
        width: 14px !important;
        height: 14px !important;
    }

    .reg-page .registration-filter-search .reg-control {
        height: 38px !important;
        border-radius: 9px !important;
        padding: 0 11px 0 36px !important;
        font-size: 9.5px !important;
    }

    .reg-page .registration-filter-search .reg-control::placeholder {
        font-size: 9.5px !important;
    }

    .reg-page .registration-filter-select .premium-select-trigger {
        height: 38px !important;
        border-radius: 9px !important;
        padding: 0 10px !important;
        font-size: 9.5px !important;
    }

    .reg-page .registration-filter-select .premium-select-chevron {
        width: 12px !important;
        height: 12px !important;
    }

    .reg-page .registration-filter-status-dot {
        width: 6px !important;
        height: 6px !important;
        flex-basis: 6px !important;
    }

    .reg-page .registration-filter-select .premium-select-menu {
        top: calc(100% + 6px) !important;
        padding: 4px !important;
        border-radius: 10px !important;
    }

    .reg-page .registration-filter-select .premium-select-option {
        min-height: 30px !important;
        padding: 0 8px !important;
        border-radius: 7px !important;
        font-size: 8.5px !important;
    }

    .reg-page .registration-filter-select .premium-select-check {
        width: 12px !important;
        height: 12px !important;
    }

    .reg-page .registration-filter-apply,
    .reg-page .registration-filter-reset {
        height: 38px !important;
        border-radius: 9px !important;
        padding-inline: 12px !important;
        font-size: 9px !important;
    }

    .reg-page .registration-filter-apply {
        min-width: 104px !important;
    }

    .reg-page .registration-filter-reset {
        min-width: 64px !important;
    }

    .reg-page .registration-filter-apply svg {
        width: 12px !important;
        height: 12px !important;
    }

    /* ---------------- APPLICATION QUEUE ---------------- */
    .reg-page .registration-queue-card {
        margin-top: 11px !important;
        border-radius: 14px !important;
    }

    .reg-page .registration-queue-card .queue-header {
        padding: 11px 14px !important;
    }

    .reg-page .registration-queue-card .queue-header h3 {
        font-size: 12px !important;
    }

    .reg-page .registration-queue-card .queue-header p {
        margin-top: 3px !important;
        font-size: 8.5px !important;
        line-height: 1.45 !important;
    }

    .reg-page .registration-queue-card #visibleCount {
        padding: 3px 7px !important;
        font-size: 7.5px !important;
    }

    .role-stat-chips {
        gap: 5px !important;
    }

    .role-stat-chips > span {
        padding: 4px 7px !important;
        font-size: 7px !important;
    }

    .application-table {
        min-width: 760px !important;
    }

    .application-table th {
        padding-top: 8px !important;
        padding-bottom: 8px !important;
        font-size: 7.5px !important;
        letter-spacing: .065em !important;
    }

    .application-table th:first-child,
    .application-table td:first-child {
        padding-left: 14px !important;
    }

    .application-table th:last-child,
    .application-table td:last-child {
        padding-right: 14px !important;
    }

    .application-table tbody td {
        padding-top: 9px !important;
        padding-bottom: 9px !important;
    }

    .application-table tbody td > div.flex.items-center.gap-3 {
        gap: 8px !important;
    }

    .application-table tbody td > div.flex.items-center.gap-3 > span:first-child {
        width: 30px !important;
        height: 30px !important;
        font-size: 8px !important;
    }

    .application-table td p,
    .application-table td span {
        font-size: 8px !important;
    }

    .application-table td .text-\[8px\] {
        font-size: 7px !important;
    }

    .application-table td .text-\[9px\] {
        font-size: 8px !important;
    }

    .application-table td .text-\[10px\] {
        font-size: 8.5px !important;
    }

    .application-table td .inline-flex.rounded-full {
        padding: 3px 7px !important;
    }

    .application-table td .grid.h-7.w-7 {
        width: 24px !important;
        height: 24px !important;
        border-radius: 7px !important;
    }

    .application-table td .grid.h-7.w-7 svg {
        width: 12px !important;
        height: 12px !important;
    }

    .reg-review-btn {
        height: 29px !important;
        border-radius: 8px !important;
        padding-inline: 9px !important;
        font-size: 8px !important;
    }

    .reg-review-btn svg {
        width: 10px !important;
        height: 10px !important;
    }

    /* ---------------- LAPTOP 100% ZOOM ---------------- */
    @media (max-height: 850px) and (min-width: 900px) {
        .registration-page-header {
            margin-bottom: 10px !important;
        }

        .registration-page-header h2 {
            font-size: 22px !important;
        }

        .registration-page-header .registration-page-subtitle {
            font-size: 9px !important;
        }

        .summary-card {
            min-height: 72px !important;
            padding-top: 9px !important;
            padding-bottom: 9px !important;
        }

        .summary-card p:nth-child(2) {
            font-size: 18px !important;
        }

        .reg-page .registration-filter-surface {
            margin-top: 9px !important;
        }

        .reg-page .registration-queue-card {
            margin-top: 9px !important;
        }

        .registration-mix-card > .mx-auto.mt-5 {
            width: 106px !important;
            height: 106px !important;
        }
    }

    /* ---------------- TABLET / MOBILE ---------------- */
    @media (max-width: 1023px) {
        .reg-page .registration-filter-bar {
            grid-template-columns: minmax(0, 1fr) minmax(140px, .42fr) !important;
            gap: 8px !important;
        }

        .reg-page .registration-filter-search {
            grid-column: 1 / -1 !important;
        }

        .reg-page .registration-filter-apply,
        .reg-page .registration-filter-reset {
            width: 100% !important;
            min-width: 0 !important;
        }
    }

    @media (max-width: 767px) {
        .registration-page-header .registration-page-header-icon {
            width: 34px !important;
            height: 34px !important;
            flex-basis: 34px !important;
        }

        .registration-page-header h2 {
            font-size: 22px !important;
        }

        .registration-page-header .registration-page-subtitle {
            font-size: 9.5px !important;
        }

        .summary-card {
            min-height: 76px !important;
        }

        .reg-page .registration-filter-bar {
            grid-template-columns: 1fr !important;
        }

        .reg-page .registration-filter-search {
            grid-column: auto !important;
        }

        .reg-page .registration-filter-search .reg-control,
        .reg-page .registration-filter-select .premium-select-trigger,
        .reg-page .registration-filter-apply,
        .reg-page .registration-filter-reset {
            height: 42px !important;
            font-size: 10px !important;
        }
    }


    /* ============================================================
       ACCOUNT REGISTRATIONS — BALANCED ENTERPRISE DESIGN
       Subtle outer elevation + flat inner content.
       ============================================================ */

    .reg-page{
        --reg-surface-shadow:0 6px 18px rgba(54,42,28,.050);
        --reg-kpi-shadow:0 5px 14px rgba(54,42,28,.055);
        --reg-kpi-hover:0 8px 18px rgba(54,42,28,.070);
        --reg-line:#e9e2d9;
        --reg-line-soft:#f0ebe4;
    }

    /* Main containers: one restrained depth level. */
    .reg-page .reg-surface,
    .reg-page .registration-filter-surface,
    .reg-page .registration-mix-card{
        border-color:var(--reg-line) !important;
        background:#fff !important;
        box-shadow:var(--reg-surface-shadow) !important;
        transform:none !important;
    }

    /* KPI cards: slightly more depth because they are primary numeric data. */
    .reg-page .summary-card,
    .reg-page .summary-card.is-current{
        border-color:var(--reg-line) !important;
        background:#fff !important;
        box-shadow:var(--reg-kpi-shadow) !important;
        transform:none !important;
        transition:border-color .14s ease,box-shadow .14s ease,transform .14s ease !important;
    }

    .reg-page .summary-card:hover{
        border-color:#ddd1c1 !important;
        box-shadow:var(--reg-kpi-hover) !important;
        transform:translateY(-1px) !important;
    }

    /* Controls: mostly flat. */
    .reg-page .registration-filter-search .reg-control,
    .reg-page .registration-filter-select .premium-select-trigger,
    .reg-page .registration-filter-reset,
    .reg-page .reg-review-btn,
    .reg-page .reg-secondary-btn{
        box-shadow:none !important;
    }

    .reg-page .registration-filter-apply{
        box-shadow:0 4px 10px rgba(217,149,0,.11) !important;
    }

    .reg-page .registration-filter-apply:hover{
        box-shadow:0 5px 12px rgba(217,149,0,.14) !important;
    }

    .reg-page .premium-select-menu{
        box-shadow:0 14px 32px rgba(47,37,25,.12) !important;
    }

    /* ---------------- REGISTRATION MIX ---------------- */
    @media (min-width:1180px){
        .registration-workspace-layout{
            grid-template-columns:minmax(0,1fr) 278px !important;
            gap:14px !important;
        }
    }

    .registration-mix-enterprise{
        position:relative;
        overflow:hidden;
        border-radius:14px !important;
        padding:14px !important;
    }

    .registration-mix-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:10px;
    }

    .registration-mix-title{
        color:#2d2721;
        font-size:10.5px;
        font-weight:700;
        line-height:1.3;
        letter-spacing:-.02em;
    }

    .registration-mix-copy{
        max-width:190px;
        margin-top:3px;
        color:#91877c;
        font-size:7.5px;
        line-height:1.45;
    }

    .registration-mix-head-icon{
        display:grid;
        width:28px;
        height:28px;
        flex:0 0 28px;
        place-items:center;
        border:1px solid #eadfc9;
        border-radius:8px;
        background:#fff8eb;
        color:#b77c18;
    }

    .registration-mix-head-icon svg{
        width:13px;
        height:13px;
    }

    .registration-mix-overview{
        display:grid;
        grid-template-columns:108px minmax(0,1fr);
        gap:13px;
        align-items:center;
        margin-top:13px;
    }

    .registration-mix-chart{
        position:relative;
        width:106px;
        height:106px;
    }

    .registration-mix-ring{
        display:block;
        width:100%;
        height:100%;
        filter:none !important;
    }

    .registration-mix-chart-center{
        position:absolute;
        inset:0;
        display:grid;
        place-content:center;
        text-align:center;
        pointer-events:none;
    }

    .registration-mix-chart-center strong{
        color:#241e18;
        font-size:20px;
        font-weight:700;
        line-height:1;
        letter-spacing:-.045em;
    }

    .registration-mix-chart-center span{
        margin-top:4px;
        color:#9a9187;
        font-size:5.8px;
        font-weight:700;
        line-height:1.2;
        letter-spacing:.10em;
        text-transform:uppercase;
    }

    .registration-mix-overview-copy{
        min-width:0;
    }

    .registration-mix-overview-label{
        display:block;
        color:#9e8a65;
        font-size:6px;
        font-weight:700;
        line-height:1.3;
        letter-spacing:.09em;
        text-transform:uppercase;
    }

    .registration-mix-dominant-role{
        display:block;
        overflow:hidden;
        margin-top:4px;
        color:#3d352d;
        font-size:12px;
        font-weight:700;
        line-height:1.2;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .registration-mix-dominant-value{
        display:flex;
        align-items:baseline;
        gap:5px;
        margin-top:6px;
    }

    .registration-mix-dominant-value strong{
        color:#a76f08;
        font-size:17px;
        font-weight:700;
        line-height:1;
        letter-spacing:-.035em;
    }

    .registration-mix-dominant-value span{
        color:#9b9288;
        font-size:6px;
        line-height:1.25;
    }

    .registration-mix-divider{
        height:1px;
        margin:12px 0 9px;
        background:var(--reg-line-soft);
    }

    .registration-mix-list{
        display:grid;
        gap:2px;
    }

    .registration-mix-role{
        display:block !important;
        border:0 !important;
        border-radius:8px !important;
        background:transparent !important;
        padding:6px 5px !important;
        box-shadow:none !important;
        transform:none !important;
        text-decoration:none;
    }

    .registration-mix-role:hover{
        border-color:transparent !important;
        background:#faf8f4 !important;
        transform:none !important;
    }

    .registration-mix-role-main{
        display:grid;
        grid-template-columns:7px minmax(0,1fr) 22px 30px;
        gap:6px;
        align-items:center;
    }

    .registration-mix-role-dot{
        width:6px;
        height:6px;
        border-radius:50%;
    }

    .registration-mix-role-name{
        overflow:hidden;
        color:#62594f;
        font-size:7.5px;
        font-weight:600;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .registration-mix-role-count{
        color:#2f2923;
        font-size:7.5px;
        font-weight:700;
        text-align:right;
    }

    .registration-mix-role-percent{
        color:#9a9187;
        font-size:6.8px;
        font-weight:500;
        text-align:right;
    }

    .registration-mix-role-track{
        display:block;
        height:3px;
        margin:5px 0 0 13px;
        overflow:hidden;
        border-radius:999px;
        background:#f1ede7;
    }

    .registration-mix-role-fill{
        display:block;
        height:100%;
        min-width:0;
        border-radius:inherit;
    }

    .registration-mix-all-link{
        display:flex;
        width:100%;
        min-height:31px;
        align-items:center;
        justify-content:space-between;
        gap:8px;
        margin-top:10px;
        border-top:1px solid var(--reg-line-soft);
        padding:10px 3px 0;
        color:#756b60;
        font-size:7.5px;
        font-weight:600;
        text-decoration:none;
    }

    .registration-mix-all-link:hover{
        color:#a56d00;
    }

    .registration-mix-all-link svg{
        width:12px;
        height:12px;
    }

    /* Queue row paint stays simple. */
    .application-table tbody tr{
        box-shadow:none !important;
    }

    @media(max-height:850px) and (min-width:900px){
        .registration-mix-enterprise{
            padding:12px !important;
        }

        .registration-mix-overview{
            grid-template-columns:96px minmax(0,1fr);
            gap:10px;
            margin-top:10px;
        }

        .registration-mix-chart{
            width:94px;
            height:94px;
        }

        .registration-mix-chart-center strong{
            font-size:18px;
        }

        .registration-mix-divider{
            margin:10px 0 7px;
        }

        .registration-mix-role{
            padding:5px 4px !important;
        }
    }

    @media(max-width:1179px){
        .registration-mix-overview{
            grid-template-columns:108px minmax(0,1fr);
        }
    }

    @media(max-width:639px){
        .registration-mix-enterprise{
            padding:13px !important;
        }

        .registration-mix-overview{
            grid-template-columns:96px minmax(0,1fr);
        }

        .registration-mix-chart{
            width:92px;
            height:92px;
        }
    }


    /* ============================================================
       REGISTRATION MIX — TEXT HIERARCHY REFINEMENT
       Separates total volume from the dominant account type.
       ============================================================ */

    .registration-mix-chart-center{
        display:flex !important;
        flex-direction:column !important;
        align-items:center !important;
        justify-content:center !important;
        gap:5px !important;
    }

    .registration-mix-chart-center strong{
        display:block !important;
        margin:0 !important;
        font-size:21px !important;
        line-height:.95 !important;
    }

    .registration-mix-chart-center span{
        display:block !important;
        margin:0 !important;
        font-size:5.7px !important;
        line-height:1.15 !important;
        letter-spacing:.09em !important;
    }

    .registration-mix-overview-copy{
        display:flex !important;
        min-width:0 !important;
        flex-direction:column !important;
        justify-content:center !important;
    }

    .registration-mix-overview-label{
        margin:0 !important;
        color:#9a8e80 !important;
        font-size:6px !important;
        font-weight:700 !important;
        line-height:1.2 !important;
        letter-spacing:.09em !important;
        text-transform:uppercase !important;
    }

    .registration-mix-dominant-row{
        display:flex;
        min-width:0;
        align-items:center;
        justify-content:space-between;
        gap:8px;
        margin-top:6px;
    }

    .registration-mix-dominant-role{
        display:block !important;
        min-width:0 !important;
        overflow:hidden !important;
        margin:0 !important;
        color:#332c25 !important;
        font-size:13px !important;
        font-weight:700 !important;
        line-height:1.2 !important;
        text-overflow:ellipsis !important;
        white-space:nowrap !important;
    }

    .registration-mix-dominant-percent{
        display:inline-flex;
        min-height:24px;
        flex:0 0 auto;
        align-items:center;
        justify-content:center;
        border:1px solid #eadfc9;
        border-radius:999px;
        background:#fff8ea;
        padding:0 8px;
        color:#a66f0d;
        font-size:8px;
        font-weight:700;
        line-height:1;
    }

    .registration-mix-dominant-meta{
        margin:5px 0 0 !important;
        color:#9b9288 !important;
        font-size:6.5px !important;
        font-weight:500 !important;
        line-height:1.35 !important;
    }

    /* Remove obsolete stacked percentage styling from v1. */
    .registration-mix-dominant-value{
        display:none !important;
    }

    @media(max-height:850px) and (min-width:900px){
        .registration-mix-chart-center{
            gap:4px !important;
        }

        .registration-mix-chart-center strong{
            font-size:19px !important;
        }

        .registration-mix-dominant-role{
            font-size:12px !important;
        }

        .registration-mix-dominant-percent{
            min-height:22px;
            padding-inline:7px;
        }
    }


    /* ============================================================
       ACCOUNT REGISTRATIONS — HEADER SCALE MATCH
       Matches Seller Compliance / Seller Account Control /
       Platform Settings / Commissions / Complaints.
       Visual-only; registration logic remains untouched.
       ============================================================ */

    .reg-page .registration-page-header{
        margin-bottom:16px !important;
        padding:0 !important;
    }

    .reg-page .registration-page-header-inner{
        display:flex !important;
        min-width:0 !important;
        align-items:center !important;
        gap:13px !important;
    }

    .reg-page .registration-page-header-icon{
        display:grid !important;
        width:44px !important;
        height:44px !important;
        flex:0 0 44px !important;
        place-items:center !important;
        border:1px solid #eadfc9 !important;
        border-radius:12px !important;
        background:#fff8eb !important;
        color:#b77c18 !important;
        box-shadow:0 4px 12px rgba(75,54,25,.045) !important;
    }

    .reg-page .registration-page-header-icon svg{
        width:17px !important;
        height:17px !important;
    }

    .reg-page .registration-page-eyebrow{
        margin:0 !important;
        color:#9a6f23 !important;
        -webkit-text-fill-color:#9a6f23 !important;
        font-size:8px !important;
        font-weight:700 !important;
        line-height:1.15 !important;
        letter-spacing:.13em !important;
        text-transform:uppercase !important;
    }

    .reg-page .registration-page-header h2{
        margin:5px 0 0 !important;
        font-size:29px !important;
        font-weight:700 !important;
        line-height:1.02 !important;
        letter-spacing:-.045em !important;
    }

    .reg-page .registration-title-base{
        color:#17130f !important;
        -webkit-text-fill-color:#17130f !important;
    }

    .reg-page .registration-title-accent{
        color:#d99500 !important;
        -webkit-text-fill-color:#d99500 !important;
    }

    .reg-page .registration-page-subtitle{
        max-width:850px !important;
        margin:7px 0 0 !important;
        color:#7f756a !important;
        -webkit-text-fill-color:#7f756a !important;
        font-size:11px !important;
        font-weight:400 !important;
        line-height:1.5 !important;
        letter-spacing:0 !important;
    }

    @media(max-height:850px) and (min-width:900px){
        .reg-page .registration-page-header{
            margin-bottom:14px !important;
        }

        .reg-page .registration-page-header-icon{
            width:42px !important;
            height:42px !important;
            flex-basis:42px !important;
        }

        .reg-page .registration-page-header h2{
            font-size:27px !important;
        }

        .reg-page .registration-page-subtitle{
            font-size:10.5px !important;
        }
    }

    @media(max-width:767px){
        .reg-page .registration-page-header-inner{
            align-items:flex-start !important;
            gap:11px !important;
        }

        .reg-page .registration-page-header-icon{
            width:40px !important;
            height:40px !important;
            flex-basis:40px !important;
            border-radius:11px !important;
        }

        .reg-page .registration-page-header h2{
            font-size:24px !important;
        }

        .reg-page .registration-page-subtitle{
            font-size:10px !important;
        }
    }


    /* ============================================================
       SELLER VISUAL SYSTEM — FINAL ACCOUNT REGISTRATIONS OVERRIDE
       Compiled from the active Seller Center visual language.
       Visual-only: preserves routes, PHP data, filters, review actions,
       protected document access, and every existing JavaScript hook.
       ============================================================ */

    .reg-page.registration-page-shell {
        --seller-canvas: #F4F5F7;
        --seller-surface: #FFFFFF;
        --seller-text: #111827;
        --seller-text-secondary: #344054;
        --seller-muted: #7C8592;
        --seller-gold: #D59617;
        --seller-gold-strong: #B97805;
        --seller-border: #E5E7EB;
        --seller-border-strong: #D1D5DB;
        --seller-divider: #ECEFF2;
        --seller-radius: 14px;
        --seller-surface-shadow:
            0 2px 5px rgba(15, 23, 42, .045),
            0 10px 24px rgba(15, 23, 42, .075),
            0 24px 54px rgba(15, 23, 42, .095),
            inset 0 1px 0 rgba(255, 255, 255, .92);
        --seller-summary-shadow:
            0 1px 3px rgba(15, 23, 42, .035),
            0 7px 18px rgba(15, 23, 42, .06),
            0 15px 32px rgba(15, 23, 42, .065),
            inset 0 1px 0 rgba(255, 255, 255, .90);

        width: 100% !important;
        max-width: 1800px !important;
        padding-inline: 0 !important;
        padding-bottom: 24px !important;
        color: var(--seller-text) !important;
    }

    /* ---------------- OPEN SELLER-STYLE PAGE HEADER ---------------- */
    .reg-page .registration-page-header {
        min-height: 66px !important;
        margin: 0 0 12px !important;
        padding: 2px 2px 13px !important;
        border-bottom: 1px solid var(--seller-divider) !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .reg-page .registration-page-header-inner {
        display: block !important;
        min-width: 0 !important;
    }

    .reg-page .registration-page-header-icon {
        display: none !important;
    }

    .reg-page .registration-page-eyebrow {
        margin: 0 0 5px !important;
        color: var(--seller-gold-strong) !important;
        -webkit-text-fill-color: var(--seller-gold-strong) !important;
        font-size: 8px !important;
        font-weight: 800 !important;
        line-height: 1.15 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
    }

    .reg-page .registration-page-header h2 {
        margin: 0 !important;
        color: var(--seller-text) !important;
        font-size: clamp(29px, 2.2vw, 36px) !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .reg-page .registration-page-header h2 .registration-title-base {
        color: var(--seller-text) !important;
        -webkit-text-fill-color: var(--seller-text) !important;
    }

    .reg-page .registration-page-header h2 .registration-title-accent {
        color: var(--seller-gold) !important;
        -webkit-text-fill-color: var(--seller-gold) !important;
    }

    .reg-page .registration-page-subtitle {
        max-width: 760px !important;
        margin: 7px 0 0 !important;
        color: var(--seller-muted) !important;
        -webkit-text-fill-color: var(--seller-muted) !important;
        font-size: 10px !important;
        font-weight: 400 !important;
        line-height: 1.55 !important;
        letter-spacing: 0 !important;
    }

    /* ---------------- TOP-LEVEL SURFACE HIERARCHY ---------------- */
    .reg-page .registration-filter-surface,
    .reg-page .registration-queue-card,
    .reg-page .registration-mix-card {
        border: 1px solid var(--seller-border) !important;
        border-radius: var(--seller-radius) !important;
        background: var(--seller-surface) !important;
        box-shadow: var(--seller-surface-shadow) !important;
        transform: none !important;
    }

    .reg-page .summary-card,
    .reg-page .summary-card.is-current {
        min-height: 92px !important;
        overflow: hidden !important;
        border: 1px solid var(--seller-border) !important;
        border-radius: var(--seller-radius) !important;
        background: var(--seller-surface) !important;
        box-shadow: var(--seller-summary-shadow) !important;
        transform: none !important;
        transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease !important;
    }

    .reg-page .summary-card:hover,
    .reg-page .summary-card.is-current:hover {
        border-color: #D8DDE5 !important;
        background: #FFFFFF !important;
        box-shadow: var(--seller-summary-shadow) !important;
        transform: none !important;
    }

    .reg-page .summary-card.is-current::after {
        display: none !important;
        content: none !important;
    }

    .reg-page .summary-grid {
        gap: 12px !important;
    }

    .reg-page .summary-card {
        padding: 15px 16px !important;
    }

    .reg-page .summary-card p:first-child {
        color: #667085 !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.35 !important;
    }

    .reg-page .summary-card p:nth-child(2) {
        margin-top: 5px !important;
        color: #101828 !important;
        font-size: 24px !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .reg-page .summary-card > div > div:last-child {
        margin-top: 8px !important;
    }

    .reg-page .summary-card > div > div:last-child span:last-child {
        color: #98A2B3 !important;
        font-size: 8.5px !important;
    }

    .reg-page .summary-card > span.absolute {
        right: 14px !important;
        top: 14px !important;
        width: 36px !important;
        height: 36px !important;
        border-radius: 10px !important;
        box-shadow: none !important;
    }

    .reg-page .summary-card > span.absolute svg {
        width: 15px !important;
        height: 15px !important;
    }

    /* ---------------- WORKSPACE SCALE ---------------- */
    .reg-page .registration-workspace-layout {
        gap: 14px !important;
    }

    @media (min-width: 1180px) {
        .reg-page .registration-workspace-layout {
            grid-template-columns: minmax(0, 1fr) 280px !important;
            gap: 14px !important;
        }

        .reg-page .registration-side-column {
            top: 14px !important;
        }
    }

    /* ---------------- FILTER TOOLBAR ---------------- */
    .reg-page .registration-filter-surface {
        margin-top: 12px !important;
        padding: 12px !important;
        overflow: visible !important;
    }

    .reg-page .registration-filter-bar {
        grid-template-columns: minmax(320px, 1fr) 160px 155px auto auto !important;
        gap: 9px !important;
    }

    .reg-page .registration-filter-search .reg-control,
    .reg-page .registration-filter-select .premium-select-trigger,
    .reg-page .registration-filter-reset,
    .reg-page .reg-review-btn,
    .reg-page .reg-secondary-btn {
        box-shadow: none !important;
    }

    .reg-page .registration-filter-search .reg-control,
    .reg-page .registration-filter-select .premium-select-trigger {
        height: 40px !important;
        border: 1px solid var(--seller-border) !important;
        border-radius: 10px !important;
        background: #FFFFFF !important;
        color: #344054 !important;
        font-size: 10px !important;
    }

    .reg-page .registration-filter-search .reg-control {
        padding: 0 12px 0 38px !important;
    }

    .reg-page .registration-filter-search > svg {
        left: 13px !important;
        width: 14px !important;
        height: 14px !important;
        color: #98A2B3 !important;
    }

    .reg-page .registration-filter-select .premium-select-trigger {
        padding: 0 11px !important;
        font-weight: 600 !important;
    }

    .reg-page .registration-filter-search .reg-control:hover,
    .reg-page .registration-filter-select .premium-select-trigger:hover,
    .reg-page .registration-filter-reset:hover {
        border-color: var(--seller-border-strong) !important;
        box-shadow: none !important;
    }

    .reg-page .registration-filter-search .reg-control:focus,
    .reg-page .registration-filter-select.is-open .premium-select-trigger,
    .reg-page .registration-filter-select .premium-select-trigger:focus-visible {
        outline: none !important;
        border-color: var(--seller-gold) !important;
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .10) !important;
    }

    .reg-page .registration-filter-select .premium-select-menu {
        top: calc(100% + 7px) !important;
        padding: 5px !important;
        border: 1px solid var(--seller-border) !important;
        border-radius: 11px !important;
        background: #FFFFFF !important;
        box-shadow:
            0 8px 18px rgba(15, 23, 42, .08),
            0 18px 40px rgba(15, 23, 42, .12) !important;
    }

    .reg-page .registration-filter-select .premium-select-option {
        min-height: 32px !important;
        border-radius: 8px !important;
        color: #475467 !important;
        font-size: 9px !important;
    }

    .reg-page .registration-filter-select .premium-select-option:hover,
    .reg-page .registration-filter-select .premium-select-option:focus-visible,
    .reg-page .registration-filter-select .premium-select-option.is-selected {
        background: #FFFAF0 !important;
        color: var(--seller-gold-strong) !important;
    }

    .reg-page .registration-filter-apply,
    .reg-page .registration-filter-reset {
        height: 40px !important;
        border-radius: 10px !important;
        padding-inline: 13px !important;
        font-size: 9.5px !important;
        font-weight: 650 !important;
    }

    .reg-page .registration-filter-apply {
        min-width: 108px !important;
        border: 1px solid var(--seller-gold) !important;
        background: var(--seller-gold) !important;
        color: #FFFFFF !important;
        box-shadow: 0 5px 12px rgba(213, 150, 23, .16) !important;
        transform: none !important;
    }

    .reg-page .registration-filter-apply:hover {
        border-color: var(--seller-gold-strong) !important;
        background: var(--seller-gold-strong) !important;
        box-shadow: 0 6px 14px rgba(185, 120, 5, .18) !important;
        transform: none !important;
    }

    .reg-page .registration-filter-reset {
        min-width: 68px !important;
        border: 1px solid var(--seller-border) !important;
        background: #FFFFFF !important;
        color: #667085 !important;
    }

    /* ---------------- APPLICATION QUEUE ---------------- */
    .reg-page .registration-queue-card {
        margin-top: 14px !important;
    }

    .reg-page .registration-queue-card .queue-header {
        padding: 15px 18px !important;
        border-bottom: 1px solid var(--seller-divider) !important;
        background: #FFFFFF !important;
        box-shadow: none !important;
    }

    .reg-page .registration-queue-card .queue-header h3 {
        color: #101828 !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        letter-spacing: -.02em !important;
    }

    .reg-page .registration-queue-card .queue-header p {
        margin-top: 3px !important;
        color: #7C8592 !important;
        font-size: 9.5px !important;
        line-height: 1.5 !important;
    }

    .reg-page .registration-queue-card #visibleCount {
        border-color: var(--seller-border) !important;
        background: #F9FAFB !important;
        color: #667085 !important;
        font-size: 8px !important;
    }

    .reg-page .role-stat-chips {
        gap: 5px !important;
    }

    .reg-page .role-stat-chips > span {
        padding: 4px 8px !important;
        border-color: var(--seller-border) !important;
        background: #FFFFFF !important;
        font-size: 7.5px !important;
        box-shadow: none !important;
    }

    .reg-page .application-table-wrap {
        border-top: 0 !important;
    }

    .reg-page .application-table {
        min-width: 820px !important;
    }

    .reg-page .application-table thead tr {
        background: #F9FAFB !important;
    }

    .reg-page .application-table th {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        color: #667085 !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        letter-spacing: .075em !important;
    }

    .reg-page .application-table th:first-child,
    .reg-page .application-table td:first-child {
        padding-left: 18px !important;
    }

    .reg-page .application-table th:last-child,
    .reg-page .application-table td:last-child {
        padding-right: 18px !important;
    }

    .reg-page .application-table tbody tr {
        background: #FFFFFF !important;
        box-shadow: none !important;
    }

    .reg-page .application-table tbody td {
        padding-top: 11px !important;
        padding-bottom: 11px !important;
        color: #475467 !important;
    }

    .reg-page [data-application-row] {
        transition: background-color .14s ease, box-shadow .14s ease !important;
    }

    .reg-page [data-application-row]:hover {
        background: #FFFCF5 !important;
        box-shadow: inset 2px 0 0 var(--seller-gold) !important;
        transform: none !important;
    }

    .reg-page [data-application-row].is-selected {
        background: #FFF9EC !important;
        box-shadow: inset 2px 0 0 var(--seller-gold) !important;
    }

    .reg-page .reg-review-btn {
        height: 31px !important;
        border: 1px solid var(--seller-border) !important;
        border-radius: 8px !important;
        background: #FFFFFF !important;
        padding-inline: 10px !important;
        color: #475467 !important;
        font-size: 8.5px !important;
        transform: none !important;
    }

    .reg-page .reg-review-btn:hover {
        border-color: #D5B66D !important;
        background: #FFFAF0 !important;
        color: var(--seller-gold-strong) !important;
        box-shadow: none !important;
        transform: none !important;
    }

    /* ---------------- REGISTRATION MIX ---------------- */
    .reg-page .registration-mix-card {
        padding: 16px !important;
    }

    .reg-page .registration-mix-title {
        color: #101828 !important;
        font-size: 11px !important;
    }

    .reg-page .registration-mix-copy {
        color: #98A2B3 !important;
        font-size: 8px !important;
    }

    .reg-page .registration-mix-head-icon {
        border-color: #F0E2BF !important;
        background: #FFFAF0 !important;
        color: var(--seller-gold-strong) !important;
        box-shadow: none !important;
    }

    .reg-page .registration-mix-divider {
        background: var(--seller-divider) !important;
    }

    .reg-page .registration-mix-role:hover {
        background: #F9FAFB !important;
    }

    .reg-page .registration-mix-role-track {
        background: #F2F4F7 !important;
    }

    .reg-page .registration-mix-all-link {
        border-top-color: var(--seller-divider) !important;
        color: #667085 !important;
    }

    .reg-page .registration-mix-all-link:hover {
        color: var(--seller-gold-strong) !important;
    }

    /* ---------------- ALERTS ---------------- */
    .reg-page > .mb-4.flex.items-start {
        border-radius: var(--seller-radius) !important;
        box-shadow: var(--seller-summary-shadow) !important;
    }

    /* ---------------- RESPONSIVE ---------------- */
    @media (max-width: 1279px) {
        .reg-page .registration-filter-bar {
            grid-template-columns: minmax(260px, 1fr) 150px 145px auto auto !important;
        }
    }

    @media (max-width: 1023px) {
        .reg-page .registration-filter-bar {
            grid-template-columns: minmax(0, 1fr) minmax(150px, .42fr) !important;
        }

        .reg-page .registration-filter-search {
            grid-column: 1 / -1 !important;
        }

        .reg-page .registration-filter-apply,
        .reg-page .registration-filter-reset {
            width: 100% !important;
            min-width: 0 !important;
        }
    }

    @media (max-width: 767px) {
        .reg-page.registration-page-shell {
            padding-bottom: 18px !important;
        }

        .reg-page .registration-page-header {
            min-height: auto !important;
            padding: 1px 0 12px !important;
        }

        .reg-page .registration-page-header h2 {
            font-size: clamp(28px, 8vw, 34px) !important;
        }

        .reg-page .registration-page-subtitle {
            max-width: 100% !important;
            font-size: 10px !important;
        }

        .reg-page .summary-grid {
            gap: 9px !important;
        }

        .reg-page .summary-card {
            min-height: 86px !important;
            padding: 13px !important;
        }

        .reg-page .summary-card p:nth-child(2) {
            font-size: 22px !important;
        }

        .reg-page .registration-filter-surface,
        .reg-page .registration-queue-card,
        .reg-page .registration-mix-card {
            border-radius: 13px !important;
            box-shadow:
                0 2px 5px rgba(15, 23, 42, .04),
                0 9px 22px rgba(15, 23, 42, .07) !important;
        }

        .reg-page .registration-filter-bar {
            grid-template-columns: 1fr !important;
        }

        .reg-page .registration-filter-search {
            grid-column: auto !important;
        }

        .reg-page .registration-filter-search .reg-control,
        .reg-page .registration-filter-select .premium-select-trigger,
        .reg-page .registration-filter-apply,
        .reg-page .registration-filter-reset {
            height: 42px !important;
            font-size: 10px !important;
        }
    }



    /* ============================================================
       ACCOUNT REGISTRATIONS — CLEAN HEADER / KPI LABEL REMOVAL
       Removes redundant role chips and summary-card helper labels.
       ============================================================ */

    .reg-page .registration-queue-card .queue-header > div {
        justify-content: flex-start !important;
    }

    .reg-page .summary-card {
        align-items: center !important;
    }


/* Bare registration filter row, matching User Management. */
.reg-page section.registration-filter-surface {
    padding: 0 !important;
    background: transparent !important;
    background-color: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    overflow: visible !important;
}
.reg-page .registration-filter-surface > form.registration-filter-bar {
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

/* Registration queue occupies the space released by the removed mix panel. */
.reg-page .registration-workspace-layout { grid-template-columns: minmax(0, 1fr) !important; }
.reg-page .registration-main-column { width: 100% !important; min-width: 0 !important; }

/* Compact registration metrics with freestanding, upper-right icons. */
.reg-page .summary-grid .summary-card {
    position: relative;
    display: block;
    min-height: 104px !important;
    padding: 18px !important;
    border-color: #E4E7EC !important;
    border-radius: 12px !important;
    background: #FFFFFF !important;
    box-shadow: 0 2px 6px rgba(16, 24, 40, .04) !important;
}
.reg-page .summary-grid .summary-card-content { padding-right: 34px; }
.reg-page .summary-grid .summary-card p:first-child {
    overflow: visible;
    white-space: normal;
    font-size: 12px !important;
    font-weight: 500 !important;
    line-height: 20px !important;
    color: #667085 !important;
}
.reg-page .summary-grid .summary-card p:nth-child(2) {
    margin-top: 10px !important;
    font-size: 28px !important;
    line-height: 1 !important;
    color: #101828 !important;
}
.reg-page .summary-grid .summary-card > .summary-card-icon {
    position: absolute;
    top: 16px !important;
    right: 18px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px !important;
    height: 24px !important;
    padding: 0;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    pointer-events: none;
}
.reg-page .summary-grid .summary-card > .summary-card-icon svg {
    display: block;
    width: 24px !important;
    height: 24px !important;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.reg-page .summary-grid .summary-card:focus-visible {
    outline: 2px solid #AC7A16;
    outline-offset: 3px;
}
@media (max-width: 479px) {
    .reg-page .summary-grid { gap: 10px !important; }
    .reg-page .summary-grid .summary-card { padding: 14px !important; }
    .reg-page .summary-grid .summary-card > .summary-card-icon {
        top: 12px !important;
        right: 14px !important;
    }
}
</style>
@include('admin.partials.registration.review-styles')

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
            ['Total Applications', $stats['total'], '#AC7A16', 'users', 'all'],
            ['Pending Review', $stats['pending'], '#B7791F', 'clock', 'pending'],
            ['Approved', $stats['approved'], '#16856A', 'check', 'approved'],
            ['Rejected', $stats['rejected'], '#C45454', 'x', 'rejected'],
        ];

    @endphp


    <section class="registration-workspace-layout">
        <div class="registration-main-column">
            {{-- COMPACT SUMMARY METRICS --}}
            <div class="summary-grid grid grid-cols-2 gap-3 xl:grid-cols-4">
                @foreach($summaryCards as [$label, $value, $iconColor, $icon, $targetStatus])
                    @php
                        $summaryParams = ['status' => $targetStatus];
                        if ($role) $summaryParams['role'] = $role;
                        $isCurrentSummary = $status === $targetStatus;
                    @endphp
            
                    <a
                        href="{{ route('admin.registrations', $summaryParams) }}"
                        class="summary-card {{ $isCurrentSummary ? 'is-current' : '' }} flex items-center justify-between gap-3 px-4 py-4 sm:px-5"
                    >
                        <div class="summary-card-content min-w-0">
                            <p class="truncate text-[10px] font-medium leading-4 text-[#655c52] sm:text-[11px]">{{ $label }}</p>
                            <p class="mt-1 text-[23px] font-bold leading-none tracking-[-.04em] text-[#201b16] sm:text-[25px]">{{ $value }}</p>
                        </div>
            
                        <span
                            class="summary-card-icon absolute"
                            style="color: {{ $iconColor }};"
                            aria-hidden="true"
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
                                    'seller' => array_values(array_filter([
                                        ['label' => 'Government-issued ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                                        $application->seller_type === 'sole_proprietorship' ? ['label' => 'DTI Certificate', 'type' => 'permit', 'available' => (bool) $application->business_permit_path] : null,
                                        $application->seller_type !== 'individual' ? ['label' => 'BIR Form 2303', 'type' => 'bir', 'available' => (bool) $application->bir_path] : null,
                                        $application->seller_type === 'corporation' ? ['label' => 'SEC Certificate', 'type' => 'sec', 'available' => (bool) $application->sec_path] : null,
                                    ])),
                                    'logistics' => [
                                        ['label' => 'Authorized Representative ID', 'type' => 'id', 'available' => (bool) $application->id_path],
                                        ['label' => 'Mayor / Business Permit', 'type' => 'permit', 'available' => (bool) $application->business_permit_path],
                                        ['label' => 'BIR Form 2303', 'type' => 'bir', 'available' => (bool) $application->bir_path],
                                        ['label' => 'SEC Certificate', 'type' => 'sec', 'available' => (bool) $application->sec_path],
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
    </section>
</div>

@include('admin.partials.registration.review-modal')
@include('admin.partials.registration.review-script')

<script>
(function () {
    function initRegistrationPage() {
        if (window.__SARI_REGISTRATION_PAGE_ABORT__) {
            window.__SARI_REGISTRATION_PAGE_ABORT__.abort();
            window.__SARI_REGISTRATION_PAGE_ABORT__ = null;
        }

        const page = document.querySelector('.reg-page');
        if (!page) return;

        const abortController = new AbortController();
        const signal = abortController.signal;
        window.__SARI_REGISTRATION_PAGE_ABORT__ = abortController;

        const search = document.getElementById('applicationSearch');
        const rows = Array.from(document.querySelectorAll('[data-application-row]'));
        const empty = document.getElementById('searchEmpty');
        const count = document.getElementById('visibleCount');
        const premiumSelects = Array.from(document.querySelectorAll('[data-premium-select]'));

        function listen(element, eventName, handler, options) {
            if (!element) return;
            element.addEventListener(eventName, handler, Object.assign({}, options || {}, { signal }));
        }

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

            listen(trigger, 'click', function (event) {
                event.stopPropagation();
                const willOpen = !select.classList.contains('is-open');
                closePremiumSelects(select);
                select.classList.toggle('is-open', willOpen);
                trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });

            options.forEach(function (option) {
                listen(option, 'click', function (event) {
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

        listen(document, 'click', function () {
            closePremiumSelects();
        });

        const rowIndex = rows.map(function (row) {
            return {
                row,
                searchText: (row.dataset.search || '').toLowerCase(),
            };
        });

        function debounce(callback, wait) {
            let timer = 0;

            return function () {
                const args = arguments;
                const context = this;
                window.clearTimeout(timer);
                timer = window.setTimeout(function () {
                    callback.apply(context, args);
                }, wait);
            };
        }

        let filterFrame = 0;

        function filterRows() {
            const query = (search?.value || '').trim().toLowerCase();
            window.cancelAnimationFrame(filterFrame);

            filterFrame = window.requestAnimationFrame(function () {
                let visible = 0;

                rowIndex.forEach(function (item) {
                    const match = !query || item.searchText.includes(query);
                    item.row.hidden = !match;
                    if (match) visible++;
                });

                if (empty) empty.hidden = visible !== 0 || rowIndex.length === 0;
                if (count) count.textContent = 'Showing ' + visible;
            });
        }

        const debouncedFilterRows = debounce(filterRows, 120);

        listen(document, 'keydown', function (event) {
            if (event.key === 'Escape') closePremiumSelects();
        });

        listen(search, 'input', debouncedFilterRows, { passive: true });
        filterRows();
    }

    window.__SARI_INIT_REGISTRATION_PAGE__ = initRegistrationPage;

    if (!window.__SARI_REGISTRATION_LIVEWIRE_LISTENER__) {
        window.__SARI_REGISTRATION_LIVEWIRE_LISTENER__ = true;
        document.addEventListener('livewire:navigated', function () {
            window.__SARI_INIT_REGISTRATION_PAGE__?.();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRegistrationPage, { once: true });
    } else {
        initRegistrationPage();
    }
})();
</script>

@endsection
