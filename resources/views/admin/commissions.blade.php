@extends('layouts.admin')

@section('title','Platform Commissions — SARI Admin')
@section('page-title','Commissions')

@section('content')
<style>
    /* ============================================================
       SARI PLATFORM COMMISSIONS — ENTERPRISE / PERFORMANCE EDITION
       Compact finance UI • Poppins • reduced paint/composite cost
       ============================================================ */

    .finance-commissions {
        --fc-brand: #d99500;
        --fc-brand-strong: #bd8205;
        --fc-brand-soft: #fff7e8;
        --fc-ink: var(--sari-dark-e23e64dd1e, #26211c);
        --fc-text: var(--sari-dark-e23e64dd1e, #514a42);
        --fc-muted: var(--sari-dark-a63aa16d7c, #8d8479);
        --fc-line: var(--sari-dark-d8c2716225, #e8e1d8);
        --fc-soft: #faf9f6;
        --fc-green: #4f8060;
        --fc-blue: #4f7fa8;
        --fc-red: #a65d5d;

        width: 100%;
        max-width: 1640px !important;
        margin: 0 auto;
        padding-bottom: 20px;
        color: var(--fc-ink);
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .finance-commissions *,
    .finance-commissions *::before,
    .finance-commissions *::after {
        box-sizing: border-box;
    }

    .finance-commissions button,
    .finance-commissions a,
    .finance-commissions input,
    .finance-commissions .fc-custom-select-menu {
        transition:
            color .15s ease,
            background-color .15s ease,
            border-color .15s ease,
            opacity .15s ease,
            transform .15s ease;
    }

    .fc-card {
        border: 1px solid var(--fc-line);
        border-radius: 14px;
        background: var(--sari-dark-950baec030, #fff);
        box-shadow: 0 6px 20px var(--sari-dark-1190f58155, rgba(61,43,22,.045));
    }

    /* ---------------- PAGE HEADER ---------------- */
    .fc-page-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .fc-title-side {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
    }

    .fc-title-icon {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        place-items: center;
        border: 1px solid var(--sari-dark-d8c2716225, #eadfc9);
        border-radius: 10px;
        background: var(--sari-dark-4d21eb669b, #fff8eb);
        color: var(--sari-dark-5acaf64852, #b77c18);
        box-shadow: 0 4px 12px var(--sari-dark-1190f58155, rgba(75,54,25,.045));
    }

    .fc-title-icon svg {
        width: 15px;
        height: 15px;
    }

    .fc-eyebrow {
        color: var(--sari-dark-a63aa16d7c, #9a7b43);
        font-size: 7px;
        font-weight: 700;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .fc-title {
        margin: 3px 0 0;
        font-size: clamp(22px, 1.55vw, 27px);
        font-weight: 700;
        line-height: 1.08;
        letter-spacing: -.035em;
    }

    .fc-title-platform {
        color: var(--sari-dark-e23e64dd1e, #17130f);
    }

    .fc-title-gold {
        color: var(--fc-brand);
    }

    .fc-subtitle {
        max-width: 760px;
        margin-top: 5px;
        color: var(--sari-dark-a63aa16d7c, #81786c);
        font-size: 9.5px;
        line-height: 1.55;
    }

    .fc-head-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .fc-date-control {
        display: flex;
        min-height: 38px;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 7px 0 9px;
        box-shadow: none;
    }

    .fc-date-control svg {
        width: 13px;
        height: 13px;
        color: var(--sari-dark-a63aa16d7c, #8d8479);
    }

    .fc-date-control input {
        width: 104px;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--sari-dark-e23e64dd1e, #514a42);
        font: inherit;
        font-size: 7.8px;
    }

    .fc-date-arrow {
        color: var(--sari-dark-a63aa16d7c, #aaa196);
        font-size: 8px;
    }

    .fc-date-submit {
        display: grid;
        width: 24px;
        height: 24px;
        place-items: center;
        border: 0;
        border-radius: 7px;
        background: var(--sari-dark-950baec030, #faf8f4);
        color: var(--sari-dark-a63aa16d7c, #756d63);
        cursor: pointer;
    }

    .fc-date-submit:hover {
        background: var(--sari-dark-4d21eb669b, #fff4db);
        color: var(--sari-dark-19b301f69a, #a56d00);
    }

    .fc-primary-button,
    .fc-secondary-button,
    .fc-link-button {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 9px;
        padding: 0 11px;
        font-size: 8px;
        font-weight: 600;
        text-decoration: none;
    }

    .fc-primary-button {
        border: 1px solid var(--fc-brand);
        background: var(--fc-brand);
        color: #fff;
        box-shadow: 0 4px 10px var(--sari-dark-71f44bdb16, rgba(217,149,0,.11));
    }

    .fc-primary-button:hover {
        background: var(--fc-brand-strong);
        border-color: var(--fc-brand-strong);
        transform: translateY(-1px);
    }

    .fc-secondary-button,
    .fc-link-button {
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-a63aa16d7c, #6f665b);
    }

    .fc-secondary-button:hover,
    .fc-link-button:hover {
        border-color: var(--sari-dark-d8c2716225, #d4c5b4);
        background: var(--sari-dark-950baec030, #faf8f4);
    }

    .fc-primary-button svg,
    .fc-secondary-button svg,
    .fc-link-button svg {
        width: 12px;
        height: 12px;
    }

    /* ---------------- KPI CARDS ---------------- */
    .fc-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 9px;
        margin-top: 11px;
    }

    .fc-stat-card {
        position: relative;
        min-height: 76px;
        padding: 11px 50px 11px 13px;
        border: 1px solid var(--fc-line);
        border-radius: 13px;
        background: var(--sari-dark-950baec030, #fff);
        box-shadow: 0 6px 18px var(--sari-dark-1190f58155, rgba(61,43,22,.045));
        contain: paint;
    }

    .fc-stat-card:hover {
        border-color: var(--sari-dark-d8c2716225, #ddcfbb);
        transform: translateY(-1px);
        box-shadow: 0 8px 22px var(--sari-dark-361b571631, rgba(61,43,22,.06));
    }

    .fc-stat-inner {
        display: block;
        min-height: 52px;
    }

    .fc-stat-icon {
        position: absolute;
        top: 12px;
        right: 12px;
        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;
        border-radius: 9px;
    }

    .fc-stat-icon svg {
        width: 14px;
        height: 14px;
    }

    .fc-stat-icon.rate { background: var(--sari-dark-4d21eb669b, #fff7e7); color: var(--sari-dark-c8bdcce6ab, #c48612); }
    .fc-stat-icon.orders { background: var(--sari-dark-4d21eb669b, #eef6fb); color: var(--sari-dark-55d2052ae1, #4f7fa8); }
    .fc-stat-icon.sales { background: var(--sari-dark-950baec030, #f5f2fb); color: var(--sari-dark-a63aa16d7c, #7566a8); }
    .fc-stat-icon.earned { background: var(--sari-dark-4d21eb669b, #eef7f1); color: var(--sari-dark-a63aa16d7c, #4f8060); }

    .fc-stat-label {
        color: var(--sari-dark-a63aa16d7c, #8e857a);
        font-size: 8px;
        font-weight: 500;
        line-height: 1.3;
    }

    .fc-stat-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
        margin-top: 4px;
    }

    .fc-stat-value {
        color: var(--sari-dark-e23e64dd1e, #28221b);
        font-size: 19px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .fc-stat-help {
        margin-top: 6px;
        color: var(--sari-dark-a63aa16d7c, #9b9288);
        font-size: 7.2px;
        line-height: 1.35;
    }

    .fc-delta {
        display: inline-flex;
        min-height: 19px;
        align-items: center;
        gap: 3px;
        border-radius: 999px;
        padding: 0 6px;
        font-size: 6.5px;
        font-weight: 700;
    }

    .fc-delta.positive { background: var(--sari-dark-4d21eb669b, #eef8f1); color: var(--sari-dark-a63aa16d7c, #4f8060); }
    .fc-delta.negative { background: var(--sari-dark-950baec030, #fff2ef); color: var(--sari-dark-887359ecf4, #a65d5d); }
    .fc-delta.neutral { background: var(--sari-dark-4d21eb669b, #f4f2ef); color: var(--sari-dark-a63aa16d7c, #81786d); }

    /* ---------------- ANALYTICS ---------------- */
    .fc-analytics-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(310px, .8fr);
        gap: 10px;
        margin-top: 10px;
    }

    .fc-chart-panel,
    .fc-policy-panel {
        min-height: 280px;
        padding: 14px;
    }

    .fc-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .fc-panel-title {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 11px;
        font-weight: 700;
        line-height: 1.35;
    }

    .fc-panel-copy {
        margin-top: 3px;
        color: var(--sari-dark-a63aa16d7c, #91887d);
        font-size: 7.5px;
        line-height: 1.5;
    }

    .fc-range-tabs {
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    .fc-range-tab {
        display: inline-flex;
        min-width: 34px;
        height: 25px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--sari-dark-d8c2716225, #e7e1d9);
        border-radius: 7px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 7px;
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 7px;
        font-weight: 600;
        text-decoration: none;
    }

    .fc-range-tab:hover {
        background: var(--sari-dark-950baec030, #faf8f4);
    }

    .fc-range-tab.active {
        border-color: var(--fc-brand);
        background: var(--fc-brand);
        color: #fff;
        box-shadow: none;
    }

    .fc-chart-wrap {
        position: relative;
        height: 220px;
        margin-top: 10px;
        overflow: visible;
        border-radius: 9px;
    }

    .fc-chart-svg {
        display: block;
        width: 100%;
        height: 220px;
    }

    .fc-chart-tooltip {
        position: absolute;
        z-index: 20;
        min-width: 112px;
        pointer-events: none;
        transform: translate(-50%, -118%);
        border: 1px solid var(--sari-dark-d8c2716225, #e1dad1);
        border-radius: 9px;
        background: var(--sari-dark-68ba16e186, rgba(255,255,255,.99));
        padding: 7px 9px;
        box-shadow: 0 8px 20px var(--sari-dark-71f44bdb16, rgba(31,24,17,.11));
        opacity: 0;
    }

    .fc-chart-tooltip.show { opacity: 1; }
    .fc-chart-tooltip.below { transform: translate(-50%, 12px); }

    .fc-chart-tooltip small {
        display: block;
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 7.5px;
        font-weight: 500;
    }

    .fc-chart-tooltip strong {
        display: block;
        margin-top: 3px;
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 10px;
        font-weight: 700;
    }

    .fc-chart-empty {
        display: grid;
        height: 205px;
        place-items: center;
        color: var(--sari-dark-a63aa16d7c, #91887d);
        font-size: 8px;
        text-align: center;
    }

    .fc-policy-head-actions {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .fc-mini-button {
        display: inline-flex;
        height: 28px;
        align-items: center;
        gap: 5px;
        border: 1px solid var(--sari-dark-d8c2716225, #eadbbd);
        border-radius: 8px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 8px;
        color: var(--sari-dark-af18afcaa4, #9a6810);
        font-size: 7px;
        font-weight: 600;
        text-decoration: none;
    }

    .fc-mini-button svg {
        width: 12px;
        height: 12px;
    }

    .fc-policy-body {
        display: grid;
        grid-template-columns: 124px 1fr;
        gap: 12px;
        align-items: center;
        margin-top: 14px;
    }

    .fc-rate-donut {
        position: relative;
        display: grid;
        width: 118px;
        height: 118px;
        place-items: center;
        border-radius: 50%;
        background: conic-gradient(var(--fc-brand) 0deg var(--rate-deg), var(--sari-dark-4d21eb669b, #efede9) var(--rate-deg) 360deg);
    }

    .fc-rate-donut::before {
        content: '';
        position: absolute;
        width: 86px;
        height: 86px;
        border-radius: 50%;
        background: var(--sari-dark-950baec030, #fff);
        box-shadow: inset 0 0 0 1px var(--sari-dark-f30a5f9061, #eee8df);
    }

    .fc-rate-center {
        position: relative;
        text-align: center;
    }

    .fc-rate-value {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 19px;
        font-weight: 700;
        line-height: 1;
    }

    .fc-rate-label {
        margin-top: 4px;
        color: var(--sari-dark-a63aa16d7c, #91887d);
        font-size: 7px;
    }

    .fc-policy-list {
        display: grid;
        gap: 8px;
    }

    .fc-policy-item {
        display: grid;
        grid-template-columns: 20px 1fr;
        gap: 6px;
        align-items: start;
    }

    .fc-policy-item-icon {
        display: grid;
        width: 20px;
        height: 20px;
        place-items: center;
        color: var(--sari-dark-a63aa16d7c, #8d8479);
    }

    .fc-policy-item-icon svg {
        width: 12px;
        height: 12px;
    }

    .fc-policy-label {
        color: var(--sari-dark-a63aa16d7c, #978e83);
        font-size: 6.3px;
    }

    .fc-policy-value {
        margin-top: 2px;
        color: var(--sari-dark-e23e64dd1e, #514a42);
        font-size: 7.2px;
        line-height: 1.45;
    }

    .fc-history-button {
        display: flex;
        width: 100%;
        height: 31px;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        border-radius: 8px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 9px;
        color: var(--sari-dark-a63aa16d7c, #6f665b);
        font-size: 7px;
        cursor: pointer;
    }

    .fc-history-button:hover {
        background: var(--sari-dark-950baec030, #faf8f4);
    }

    /* ---------------- FILTERS ---------------- */
    .fc-filter-card {
        position: relative;
        z-index: 25;
        margin-top: 10px;
        padding: 9px;
        overflow: visible;
    }

    .fc-filter-grid {
        display: grid;
        grid-template-columns: minmax(240px,1.25fr) 155px 175px minmax(210px,.9fr) 78px 108px;
        gap: 8px;
        align-items: center;
    }

    .fc-search {
        position: relative;
    }

    .fc-search svg {
        position: absolute;
        top: 50%;
        left: 12px;
        width: 14px;
        height: 14px;
        color: var(--sari-dark-a63aa16d7c, #9b9287);
        pointer-events: none;
        transform: translateY(-50%);
    }

    .fc-control,
    .fc-custom-select-trigger {
        width: 100%;
        height: 38px;
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-e23e64dd1e, #3d3730);
        font: inherit;
        font-size: 8.5px;
        font-weight: 500;
        box-shadow: none;
    }

    .fc-control {
        padding: 0 9px;
    }

    .fc-search .fc-control {
        padding-left: 36px;
    }

    .fc-control:focus,
    .fc-custom-select-trigger:focus-visible,
    .fc-custom-select.is-open .fc-custom-select-trigger {
        outline: none;
        border-color: #d49a2b;
        box-shadow: 0 0 0 3px var(--sari-dark-e07575a086, rgba(217,149,0,.075));
    }

    .fc-filter-dates {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5px;
    }

    .fc-filter-reset,
    .fc-filter-apply {
        display: inline-flex;
        width: 100%;
        height: 38px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 9px;
        padding: 0 9px;
        font-size: 8px;
        font-weight: 600;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
    }

    .fc-filter-reset {
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-a63aa16d7c, #6f665b);
    }

    .fc-filter-reset:hover {
        border-color: var(--sari-dark-d8c2716225, #d4c5b4);
        background: var(--sari-dark-950baec030, #faf8f4);
    }

    .fc-filter-apply {
        border: 1px solid var(--fc-brand);
        background: var(--fc-brand);
        color: #fff;
        box-shadow: 0 4px 10px var(--sari-dark-8eadf403a5, rgba(217,149,0,.10));
    }

    .fc-filter-apply:hover {
        background: var(--fc-brand-strong);
        border-color: var(--fc-brand-strong);
        transform: translateY(-1px);
    }

    /* Custom dropdowns */
    .fc-custom-select {
        position: relative;
        min-width: 0;
        z-index: 36;
    }

    .fc-custom-select.is-open {
        z-index: 70;
    }

    .fc-custom-select-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 10px;
        text-align: left;
        cursor: pointer;
    }

    .fc-custom-select-main,
    .fc-custom-select-option-main {
        display: inline-flex;
        min-width: 0;
        align-items: center;
        gap: 7px;
    }

    .fc-custom-select-main > span:last-child,
    .fc-custom-select-option-main > span:last-child {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fc-custom-select-status-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: var(--fc-brand);
    }

    .fc-custom-select-user-icon,
    .fc-option-user-icon {
        display: grid;
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
        place-items: center;
        color: var(--sari-dark-0d676d4438, #9a6c18);
    }

    .fc-custom-select-user-icon svg,
    .fc-option-user-icon svg {
        width: 13px;
        height: 13px;
    }

    .fc-custom-select-chevron {
        width: 12px;
        height: 12px;
        flex: 0 0 12px;
        color: var(--sari-dark-a63aa16d7c, #857b70);
    }

    .fc-custom-select.is-open .fc-custom-select-chevron {
        transform: rotate(180deg);
    }

    .fc-custom-select-menu {
        position: absolute;
        z-index: 80;
        top: calc(100% + 5px);
        right: 0;
        left: 0;
        max-height: 210px;
        overflow-y: auto;
        padding: 4px;
        border: 1px solid var(--sari-dark-d8c2716225, #e4dcd1);
        border-radius: 10px;
        background: var(--sari-dark-950baec030, #fff);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-3px);
        box-shadow: 0 14px 32px var(--sari-dark-3b2338cbf0, rgba(47,37,25,.12));
        scrollbar-width: thin;
        scrollbar-color: #ddd3c7 transparent;
    }

    .fc-custom-select.is-open .fc-custom-select-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    .fc-custom-select-option {
        display: flex;
        width: 100%;
        min-height: 31px;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        border: 0;
        border-radius: 7px;
        background: transparent;
        padding: 0 8px;
        color: var(--sari-dark-e23e64dd1e, #5b534a);
        font: inherit;
        font-size: 8px;
        font-weight: 500;
        text-align: left;
        cursor: pointer;
    }

    .fc-custom-select-option:hover,
    .fc-custom-select-option:focus-visible,
    .fc-custom-select-option.is-selected {
        outline: none;
        background: var(--sari-dark-4d21eb669b, #fff7e8);
        color: var(--sari-dark-af18afcaa4, #9a6810);
    }

    .fc-custom-select-check {
        width: 12px;
        height: 12px;
        flex: 0 0 12px;
        color: var(--fc-brand);
        opacity: 0;
    }

    .fc-custom-select-option.is-selected .fc-custom-select-check {
        opacity: 1;
    }

    .fc-option-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: var(--fc-brand);
    }

    .fc-option-dot.neutral {
        background: #8e857a;
    }

    /* ---------------- LEDGER ---------------- */
    .fc-ledger {
        margin-top: 10px;
        overflow: hidden;
    }

    .fc-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 14px;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #eee8df);
    }

    .fc-table-wrap {
        overflow-x: auto;
    }

    .fc-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
        text-align: left;
    }

    .fc-table thead {
        background: var(--sari-dark-950baec030, #faf9f6);
    }

    .fc-table th {
        padding: 9px 11px;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #eee8df);
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 7.5px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .fc-table td {
        padding: 9px 11px;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #f0ebe4);
        color: var(--sari-dark-e23e64dd1e, #5b534a);
        font-size: 8px;
        vertical-align: middle;
    }

    .fc-table tbody tr {
        content-visibility: auto;
        contain-intrinsic-size: 46px;
    }

    .fc-table tbody tr:hover {
        background: var(--sari-dark-950baec030, #fdfbf8);
    }

    .fc-order,
    .fc-money {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 8.3px;
        font-weight: 700;
        white-space: nowrap;
    }

    .fc-status {
        display: inline-flex;
        min-height: 22px;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 0 7px;
        font-size: 7px;
        font-weight: 600;
    }

    .fc-status::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }

    .fc-status.earned { background: var(--sari-dark-4d21eb669b, #eef8f1); color: var(--sari-dark-a63aa16d7c, #4f8060); }
    .fc-status.adjusted { background: var(--sari-dark-4d21eb669b, #fff7e8); color: var(--sari-dark-af18afcaa4, #9a6810); }
    .fc-status.reversed { background: var(--sari-dark-950baec030, #fff2ef); color: var(--sari-dark-887359ecf4, #a65d5d); }

    .fc-view-button {
        display: inline-flex;
        height: 28px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--sari-dark-d8c2716225, #e3dcd3);
        border-radius: 7px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 9px;
        color: var(--sari-dark-e23e64dd1e, #514a42);
        font-size: 7.3px;
        font-weight: 600;
        cursor: pointer;
    }

    .fc-view-button:hover {
        border-color: var(--sari-dark-d8c2716225, #d6c7b4);
        background: var(--sari-dark-950baec030, #fffaf1);
        color: var(--sari-dark-af18afcaa4, #9a6810);
    }

    .fc-table-footer {
        display: flex;
        min-height: 44px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 11px;
        border-top: 1px solid var(--sari-dark-d8c2716225, #eee8df);
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 7.5px;
    }

    .fc-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .fc-page-link {
        display: grid;
        min-width: 27px;
        height: 27px;
        place-items: center;
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        border-radius: 7px;
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 8px;
        text-decoration: none;
    }

    .fc-page-link.active {
        border-color: var(--fc-brand);
        background: var(--fc-brand);
        color: #fff;
    }

    .fc-page-link.disabled {
        opacity: .4;
        pointer-events: none;
    }

    /* ---------------- BOTTOM CARDS ---------------- */
    .fc-bottom-grid {
        display: grid;
        grid-template-columns: 1.05fr 1fr 1.12fr;
        gap: 10px;
        margin-top: 10px;
    }

    .fc-bottom-card {
        min-height: 220px;
        padding: 14px;
    }

    .fc-bottom-title-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 9px;
    }

    .fc-top-sellers {
        margin-top: 11px;
    }

    .fc-seller-row {
        display: grid;
        grid-template-columns: 24px minmax(100px,.75fr) minmax(100px,1fr) 88px;
        gap: 7px;
        align-items: center;
        min-height: 34px;
    }

    .fc-rank {
        display: grid;
        width: 23px;
        height: 23px;
        place-items: center;
        border-radius: 50%;
        background: var(--sari-dark-4d21eb669b, #fff7e8);
        color: var(--sari-dark-af18afcaa4, #9a6810);
        font-size: 7.5px;
        font-weight: 700;
    }

    .fc-seller-name {
        overflow: hidden;
        color: var(--sari-dark-e23e64dd1e, #514a42);
        font-size: 8px;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fc-progress {
        height: 6px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--sari-dark-4d21eb669b, #eeeae4);
    }

    .fc-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--fc-brand);
    }

    .fc-seller-amount {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 8px;
        font-weight: 700;
        text-align: right;
        white-space: nowrap;
    }

    .fc-seller-share {
        color: var(--sari-dark-a63aa16d7c, #9b9288);
        font-size: 6.8px;
        font-weight: 500;
    }

    .fc-status-layout {
        display: grid;
        grid-template-columns: 118px 1fr;
        gap: 12px;
        align-items: center;
        margin-top: 11px;
    }

    .fc-status-donut {
        position: relative;
        display: grid;
        width: 112px;
        height: 112px;
        place-items: center;
        border-radius: 50%;
        background: var(--status-gradient);
    }

    .fc-status-donut::before {
        content: '';
        position: absolute;
        width: 78px;
        height: 78px;
        border-radius: 50%;
        background: var(--sari-dark-950baec030, #fff);
        box-shadow: inset 0 0 0 1px var(--sari-dark-f30a5f9061, #eee8df);
    }

    .fc-status-center {
        position: relative;
        text-align: center;
    }

    .fc-status-total {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 17px;
        font-weight: 700;
    }

    .fc-status-caption {
        margin-top: 3px;
        color: var(--sari-dark-a63aa16d7c, #91887d);
        font-size: 7px;
    }

    .fc-status-legend {
        display: grid;
        gap: 7px;
    }

    .fc-status-legend-row {
        display: grid;
        grid-template-columns: 8px 1fr 36px 44px;
        gap: 6px;
        align-items: center;
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 7.5px;
    }

    .fc-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 3px;
    }

    .fc-status-count {
        color: var(--sari-dark-e23e64dd1e, #514a42);
        font-weight: 600;
        text-align: right;
    }

    .fc-status-percent {
        color: var(--sari-dark-a63aa16d7c, #9b9288);
        text-align: right;
    }

    .fc-recent-list {
        margin-top: 10px;
    }

    .fc-recent-row {
        display: grid;
        grid-template-columns: 18px minmax(100px,1fr) minmax(80px,.85fr) 72px 68px;
        gap: 6px;
        align-items: center;
        min-height: 34px;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #f0ebe4);
        content-visibility: auto;
        contain-intrinsic-size: 34px;
    }

    .fc-recent-row:last-child {
        border-bottom: 0;
    }

    .fc-recent-icon {
        color: var(--sari-dark-a63aa16d7c, #81786d);
    }

    .fc-recent-icon svg {
        width: 13px;
        height: 13px;
    }

    .fc-recent-order,
    .fc-recent-seller {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fc-recent-order {
        color: var(--sari-dark-e23e64dd1e, #514a42);
        font-size: 7.5px;
        font-weight: 600;
    }

    .fc-recent-seller {
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 7.3px;
    }

    .fc-recent-amount {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 7.8px;
        font-weight: 700;
        text-align: right;
    }

    .fc-recent-date {
        color: var(--sari-dark-a63aa16d7c, #91887d);
        font-size: 7px;
        text-align: right;
        white-space: nowrap;
    }

    .fc-empty {
        display: grid;
        min-height: 100px;
        place-items: center;
        color: var(--sari-dark-a63aa16d7c, #91887d);
        font-size: 8px;
        text-align: center;
    }

    /* ---------------- RIDER PAYOUTS ---------------- */
    .fc-payout-panel {
        margin-top: 10px;
        overflow: hidden;
    }

    .fc-payout-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .fc-chip {
        display: inline-flex;
        min-height: 20px;
        align-items: center;
        gap: 4px;
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        border-radius: 999px;
        padding: 0 6px;
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 6.5px;
        font-weight: 600;
    }

    .fc-chip::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #9b9288;
    }

    .fc-chip.pending::before { background: var(--fc-brand); }
    .fc-chip.approved::before { background: #4f7fa8; }
    .fc-chip.paid::before { background: #4f8060; }

    .fc-payout-row {
        display: grid;
        grid-template-columns: minmax(150px,1fr) 86px 96px minmax(240px,1.2fr);
        gap: 8px;
        align-items: center;
        min-height: 54px;
        padding: 8px 11px;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #f0ebe4);
        content-visibility: auto;
        contain-intrinsic-size: 54px;
    }

    .fc-payout-row:last-child {
        border-bottom: 0;
    }

    .fc-payout-name {
        color: var(--sari-dark-e23e64dd1e, #514a42);
        font-size: 7.5px;
        font-weight: 600;
    }

    .fc-payout-meta {
        margin-top: 2px;
        color: var(--sari-dark-a63aa16d7c, #9b9288);
        font-size: 6.5px;
    }

    .fc-payout-amount {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 7.5px;
        font-weight: 700;
    }

    .fc-payout-actions {
        display: flex;
        justify-content: flex-end;
        gap: 5px;
    }

    .fc-payout-actions form {
        display: flex;
        gap: 5px;
    }

    .fc-payout-input {
        height: 30px;
        min-width: 120px;
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        border-radius: 7px;
        padding: 0 8px;
        font: inherit;
        font-size: 6.8px;
    }

    .fc-action {
        height: 30px;
        border: 1px solid var(--sari-dark-d8c2716225, #e5ddd2);
        border-radius: 7px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 8px;
        color: var(--sari-dark-a63aa16d7c, #6f665b);
        font: inherit;
        font-size: 6.8px;
        font-weight: 600;
        cursor: pointer;
    }

    .fc-action:hover {
        background: var(--sari-dark-950baec030, #faf8f4);
    }

    /* ============================================================
       CENTERED FINANCE MODALS
       ============================================================ */
    .fc-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 80;
        display: none;
        align-items: center;
        justify-content: center;
        background: var(--sari-dark-416d70b0b2, rgba(28,24,20,.42));
        padding: 14px;
    }

    .fc-modal-backdrop.open {
        display: flex;
    }

    .fc-modal {
        width: min(620px, calc(100vw - 28px));
        max-height: calc(100vh - 28px);
        overflow: hidden;
        border: 1px solid var(--sari-dark-d8c2716225, #dfd8cf);
        border-radius: 16px;
        background: var(--sari-dark-4d21eb669b, #f8f7f4);
        box-shadow:
            0 24px 64px var(--sari-dark-3793f9e558, rgba(31,24,17,.18)),
            0 8px 22px var(--sari-dark-7c935c8886, rgba(31,24,17,.07));
        animation: fcModalIn .18s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes fcModalIn {
        from { opacity: 0; transform: translateY(8px) scale(.99); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .fc-modal-head {
        display: flex;
        min-height: 56px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 13px;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #ebe5dd);
        background: var(--sari-dark-950baec030, #fff);
    }

    .fc-modal-title {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 10.5px;
        font-weight: 700;
    }

    .fc-modal-close {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 1px solid var(--sari-dark-d8c2716225, #e4ddd4);
        border-radius: 8px;
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-a63aa16d7c, #71685f);
        cursor: pointer;
    }

    .fc-modal-close:hover {
        background: var(--sari-dark-4d21eb669b, #f7f5f2);
        color: var(--sari-dark-e23e64dd1e, #332d27);
    }

    .fc-modal-body {
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        padding: 12px 13px 14px;
        background: var(--sari-dark-4d21eb669b, #f8f7f4);
        scrollbar-width: thin;
        scrollbar-color: #d0c8be transparent;
    }

    .fc-detail-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 8px;
    }

    .fc-detail-box {
        border: 1px solid var(--sari-dark-d8c2716225, #e7e1d9);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 9px;
    }

    .fc-detail-label {
        color: var(--sari-dark-a63aa16d7c, #9b9288);
        font-size: 6.5px;
    }

    .fc-detail-value {
        margin-top: 3px;
        color: var(--sari-dark-e23e64dd1e, #514a42);
        font-size: 7.5px;
        font-weight: 600;
        word-break: break-word;
    }

    .fc-history-list {
        display: grid;
        gap: 7px;
    }

    .fc-history-row {
        display: grid;
        grid-template-columns: 72px 1fr 100px;
        gap: 8px;
        align-items: center;
        border: 1px solid var(--sari-dark-d8c2716225, #e7e1d9);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 9px;
        content-visibility: auto;
        contain-intrinsic-size: 58px;
    }

    .fc-history-rate {
        color: var(--sari-dark-e23e64dd1e, #302a24);
        font-size: 9px;
        font-weight: 700;
    }

    .fc-history-copy,
    .fc-history-date {
        color: var(--sari-dark-a63aa16d7c, #81786d);
        font-size: 6.5px;
        line-height: 1.45;
    }

    .fc-history-date {
        color: var(--sari-dark-a63aa16d7c, #9b9288);
        text-align: right;
    }

    /* ---------------- RESPONSIVE ---------------- */
    @media (max-height: 850px) and (min-width: 900px) {
        .fc-title {
            font-size: 22px;
        }

        .fc-stat-card {
            min-height: 70px;
            padding-top: 9px;
            padding-bottom: 9px;
        }

        .fc-stat-value {
            font-size: 18px;
        }

        .fc-chart-wrap,
        .fc-chart-svg {
            height: 200px;
        }

        .fc-chart-panel,
        .fc-policy-panel {
            min-height: 260px;
        }
    }

    @media (max-width: 1250px) {
        .fc-summary-grid {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }

        .fc-analytics-grid {
            grid-template-columns: 1fr;
        }

        .fc-filter-grid {
            grid-template-columns: minmax(230px,1fr) 150px 170px;
        }

        .fc-filter-dates {
            grid-column: span 2;
        }

        .fc-bottom-grid {
            grid-template-columns: 1fr 1fr;
        }

        .fc-bottom-card:last-child {
            grid-column: 1 / -1;
        }

        .fc-payout-row {
            grid-template-columns: 1fr 90px 90px;
        }

        .fc-payout-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 760px) {
        .fc-page-head {
            align-items: stretch;
            flex-direction: column;
        }

        .fc-head-actions {
            justify-content: flex-start;
        }

        .fc-date-control {
            width: 100%;
            min-height: 42px;
            flex-wrap: wrap;
            padding-block: 6px;
        }

        .fc-date-control input {
            flex: 1;
            min-width: 104px;
        }

        .fc-summary-grid,
        .fc-bottom-grid {
            grid-template-columns: 1fr;
        }

        .fc-bottom-card:last-child {
            grid-column: auto;
        }

        .fc-policy-body,
        .fc-status-layout {
            grid-template-columns: 1fr;
            justify-items: center;
        }

        .fc-policy-list,
        .fc-status-legend {
            width: 100%;
        }

        .fc-filter-grid {
            grid-template-columns: 1fr;
        }

        .fc-filter-dates {
            grid-column: auto;
        }

        .fc-control,
        .fc-custom-select-trigger,
        .fc-filter-reset,
        .fc-filter-apply {
            height: 42px;
            font-size: 9px;
        }

        .fc-panel-head {
            flex-direction: column;
        }

        .fc-range-tabs {
            flex-wrap: wrap;
        }

        .fc-recent-row {
            grid-template-columns: 18px 1fr 70px;
        }

        .fc-recent-seller,
        .fc-recent-date {
            display: none;
        }

        .fc-payout-row {
            grid-template-columns: 1fr;
        }

        .fc-payout-actions {
            grid-column: auto;
        }

        .fc-payout-actions,
        .fc-payout-actions form {
            width: 100%;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .fc-payout-input {
            flex: 1;
        }

        .fc-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .fc-detail-grid {
            grid-template-columns: 1fr;
        }

        .fc-history-row {
            grid-template-columns: 64px 1fr;
        }

        .fc-history-date {
            grid-column: 1 / -1;
            text-align: left;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .finance-commissions *,
        .fc-modal {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            scroll-behavior: auto !important;
        }
    }

    /* ============================================================
       PLATFORM COMMISSIONS — HEADER SCALE MATCH WITH SETTINGS
       Visual-only. Finance calculations and actions remain intact.
       ============================================================ */

    .finance-commissions .fc-page-head{
        display:flex !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:20px !important;
        margin-bottom:2px !important;
    }

    .finance-commissions .fc-title-side{
        display:flex !important;
        min-width:0 !important;
        align-items:center !important;
        gap:13px !important;
    }

    .finance-commissions .fc-title-icon{
        width:44px !important;
        height:44px !important;
        flex:0 0 44px !important;
        border-radius:12px !important;
        box-shadow:0 4px 12px var(--sari-dark-1190f58155, rgba(75,54,25,.045)) !important;
    }

    .finance-commissions .fc-title-icon svg{
        width:17px !important;
        height:17px !important;
    }

    .finance-commissions .fc-eyebrow{
        color:var(--sari-dark-a63aa16d7c, #9a6f23) !important;
        font-size:8px !important;
        font-weight:700 !important;
        line-height:1.15 !important;
        letter-spacing:.13em !important;
    }

    .finance-commissions .fc-title{
        margin:5px 0 0 !important;
        font-size:29px !important;
        font-weight:700 !important;
        line-height:1.02 !important;
        letter-spacing:-.045em !important;
    }

    .finance-commissions .fc-title-platform{
        color:var(--sari-dark-e23e64dd1e, #17130f) !important;
    }

    .finance-commissions .fc-title-gold{
        color:var(--sari-dark-7f7834ccc9, #d99500) !important;
    }

    .finance-commissions .fc-subtitle{
        max-width:760px !important;
        margin-top:7px !important;
        color:var(--sari-dark-a63aa16d7c, #7f756a) !important;
        font-size:11px !important;
        font-weight:400 !important;
        line-height:1.5 !important;
    }

    .finance-commissions .fc-head-actions{
        display:flex !important;
        align-items:center !important;
        justify-content:flex-end !important;
        gap:8px !important;
        flex:0 0 auto !important;
    }

    .finance-commissions .fc-date-control{
        min-height:42px !important;
        border-radius:10px !important;
        padding:0 8px 0 10px !important;
    }

    .finance-commissions .fc-date-control svg{
        width:14px !important;
        height:14px !important;
    }

    .finance-commissions .fc-date-control input{
        width:112px !important;
        font-size:8.5px !important;
    }

    .finance-commissions .fc-date-submit{
        width:27px !important;
        height:27px !important;
        border-radius:7px !important;
    }

    .finance-commissions .fc-primary-button{
        min-height:42px !important;
        border-radius:10px !important;
        padding:0 13px !important;
        font-size:8.5px !important;
    }

    .finance-commissions .fc-primary-button svg{
        width:13px !important;
        height:13px !important;
    }

    @media(max-height:850px) and (min-width:900px){
        .finance-commissions .fc-title-icon{
            width:42px !important;
            height:42px !important;
            flex-basis:42px !important;
        }

        .finance-commissions .fc-title{
            font-size:27px !important;
        }

        .finance-commissions .fc-subtitle{
            font-size:10.5px !important;
        }

        .finance-commissions .fc-date-control,
        .finance-commissions .fc-primary-button{
            min-height:40px !important;
        }
    }

    @media(max-width:760px){
        .finance-commissions .fc-page-head{
            align-items:flex-start !important;
            gap:12px !important;
        }

        .finance-commissions .fc-title-side{
            align-items:flex-start !important;
            gap:11px !important;
        }

        .finance-commissions .fc-title-icon{
            width:40px !important;
            height:40px !important;
            flex-basis:40px !important;
            border-radius:11px !important;
        }

        .finance-commissions .fc-title{
            font-size:24px !important;
        }

        .finance-commissions .fc-subtitle{
            font-size:10px !important;
        }

        .finance-commissions .fc-head-actions{
            width:100% !important;
            justify-content:flex-start !important;
        }
    }


    /* ============================================================
       FINAL SELLER-DERIVED ADMIN VISUAL SYSTEM — PLATFORM COMMISSIONS
       Visual-only authority layer. Finance calculations, routes,
       filters, payouts, exports, chart data, and JS remain untouched.
       ============================================================ */

    .finance-commissions {
        --fc-shell-line: var(--sari-dark-d8c2716225, #e5e7eb);
        --fc-shell-divider: var(--sari-dark-d8c2716225, #eceff2);
        --fc-shell-canvas: var(--sari-dark-4d21eb669b, #f4f5f7);
        --fc-shell-shadow:
            0 1px 2px var(--sari-dark-7593018246, rgba(31, 24, 17, .035)),
            0 8px 24px var(--sari-dark-fbd3b3b9f6, rgba(31, 24, 17, .055));
        --fc-shell-shadow-soft:
            0 1px 2px var(--sari-dark-e1384eb7fc, rgba(31, 24, 17, .025)),
            0 5px 16px var(--sari-dark-25b2da5e49, rgba(31, 24, 17, .04));
        --fc-shell-shadow-modal:
            0 26px 70px var(--sari-dark-ef4e8433d9, rgba(31, 24, 17, .19)),
            0 10px 28px var(--sari-dark-c33e3d1bb4, rgba(31, 24, 17, .08));

        max-width: 1600px !important;
        padding-bottom: 32px !important;
    }

    /* Open Seller-style document header */
    .finance-commissions .fc-page-head {
        align-items: flex-end !important;
        gap: 24px !important;
        margin-bottom: 0 !important;
        padding-bottom: 18px !important;
        border-bottom: 1px solid var(--fc-shell-divider) !important;
    }

    .finance-commissions .fc-title-side {
        align-items: flex-start !important;
        gap: 0 !important;
    }

    .finance-commissions .fc-title-icon {
        display: none !important;
    }

    .finance-commissions .fc-eyebrow {
        margin: 0 !important;
        color: var(--sari-dark-a63aa16d7c, #9a6f23) !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        line-height: 1.15 !important;
        letter-spacing: .14em !important;
    }

    .finance-commissions .fc-title {
        margin: 5px 0 0 !important;
        font-size: clamp(29px, 2vw, 36px) !important;
        font-weight: 700 !important;
        line-height: 1.02 !important;
        letter-spacing: -.045em !important;
    }

    .finance-commissions .fc-subtitle {
        max-width: 820px !important;
        margin-top: 8px !important;
        color: var(--sari-dark-a63aa16d7c, #7f756a) !important;
        font-size: 10px !important;
        font-weight: 400 !important;
        line-height: 1.55 !important;
    }

    .finance-commissions .fc-head-actions {
        gap: 9px !important;
        padding-bottom: 1px !important;
    }

    .finance-commissions .fc-date-control,
    .finance-commissions .fc-primary-button {
        min-height: 40px !important;
        height: 40px !important;
        border-radius: 10px !important;
    }

    .finance-commissions .fc-date-control {
        border-color: var(--sari-dark-d8c2716225, #e2e5e9) !important;
        padding-inline: 10px 8px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: none !important;
    }

    .finance-commissions .fc-primary-button {
        padding-inline: 13px !important;
        font-size: 8.5px !important;
        box-shadow: 0 5px 13px var(--sari-dark-3e32d50f5e, rgba(194, 132, 10, .13)) !important;
    }

    /* Seller elevation hierarchy: float major surfaces only */
    .finance-commissions .fc-card,
    .finance-commissions .fc-stat-card {
        border-color: var(--fc-shell-line) !important;
        border-radius: 14px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }

    .finance-commissions .fc-card {
        box-shadow: var(--fc-shell-shadow) !important;
    }

    .finance-commissions .fc-stat-card {
        min-height: 82px !important;
        padding: 13px 52px 13px 15px !important;
        box-shadow: var(--fc-shell-shadow-soft) !important;
        transform: none !important;
    }

    .finance-commissions .fc-stat-card:hover {
        border-color: var(--fc-shell-line) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: var(--fc-shell-shadow-soft) !important;
        transform: none !important;
    }

    .finance-commissions .fc-summary-grid {
        gap: 12px !important;
        margin-top: 16px !important;
    }

    .finance-commissions .fc-stat-inner {
        min-height: 54px !important;
    }

    .finance-commissions .fc-stat-icon {
        top: 14px !important;
        right: 14px !important;
        width: 32px !important;
        height: 32px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #eceff2) !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .finance-commissions .fc-stat-label {
        font-size: 8px !important;
        color: var(--sari-dark-a63aa16d7c, #85807a) !important;
    }

    .finance-commissions .fc-stat-value {
        font-size: 20px !important;
        letter-spacing: -.04em !important;
    }

    .finance-commissions .fc-stat-help {
        margin-top: 7px !important;
        font-size: 7.3px !important;
    }

    /* Analytics / policy workspace */
    .finance-commissions .fc-analytics-grid {
        gap: 14px !important;
        margin-top: 14px !important;
    }

    .finance-commissions .fc-chart-panel,
    .finance-commissions .fc-policy-panel {
        min-height: 292px !important;
        padding: 17px !important;
    }

    .finance-commissions .fc-panel-title {
        font-size: 11px !important;
        color: var(--sari-dark-e23e64dd1e, #2f2924) !important;
    }

    .finance-commissions .fc-panel-copy {
        margin-top: 4px !important;
        font-size: 7.5px !important;
    }

    .finance-commissions .fc-range-tab,
    .finance-commissions .fc-mini-button,
    .finance-commissions .fc-history-button,
    .finance-commissions .fc-detail-box,
    .finance-commissions .fc-history-row,
    .finance-commissions .fc-action,
    .finance-commissions .fc-payout-input {
        box-shadow: none !important;
    }

    .finance-commissions .fc-range-tab {
        min-width: 36px !important;
        height: 28px !important;
        border-color: var(--sari-dark-d8c2716225, #e3e6ea) !important;
        border-radius: 8px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }

    .finance-commissions .fc-range-tab.active {
        border-color: #d99500 !important;
        background: #d99500 !important;
        color: #fff !important;
    }

    .finance-commissions .fc-chart-wrap {
        margin-top: 13px !important;
    }

    .finance-commissions .fc-chart-tooltip {
        border-color: var(--sari-dark-d8c2716225, #e1e4e8) !important;
        border-radius: 10px !important;
        box-shadow:
            0 10px 26px var(--sari-dark-8eadf403a5, rgba(31, 24, 17, .10)),
            0 2px 7px var(--sari-dark-25b2da5e49, rgba(31, 24, 17, .04)) !important;
    }

    .finance-commissions .fc-policy-body {
        margin-top: 16px !important;
    }

    .finance-commissions .fc-history-button {
        border-color: var(--sari-dark-d8c2716225, #e3e6ea) !important;
        border-radius: 9px !important;
    }

    /* Finance filter bar: one elevated surface, flat internals */
    .finance-commissions .fc-filter-card {
        margin-top: 14px !important;
        padding: 11px !important;
        overflow: visible !important;
    }

    .finance-commissions .fc-filter-grid {
        gap: 9px !important;
    }

    .finance-commissions .fc-control,
    .finance-commissions .fc-custom-select-trigger,
    .finance-commissions .fc-filter-reset,
    .finance-commissions .fc-filter-apply {
        height: 40px !important;
        min-height: 40px !important;
        border-radius: 9px !important;
        font-size: 8.5px !important;
    }

    .finance-commissions .fc-control,
    .finance-commissions .fc-custom-select-trigger,
    .finance-commissions .fc-filter-reset {
        border-color: var(--sari-dark-d8c2716225, #e2e5e9) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: none !important;
    }

    .finance-commissions .fc-control:focus,
    .finance-commissions .fc-custom-select-trigger:focus-visible,
    .finance-commissions .fc-custom-select.is-open .fc-custom-select-trigger,
    .finance-commissions .fc-payout-input:focus {
        outline: none !important;
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px var(--sari-dark-e07575a086, rgba(217, 149, 0, .075)) !important;
    }

    .finance-commissions .fc-filter-apply {
        box-shadow: 0 5px 13px var(--sari-dark-3b2338cbf0, rgba(194, 132, 10, .12)) !important;
    }

    .finance-commissions .fc-filter-apply:hover {
        transform: none !important;
    }

    .finance-commissions .fc-custom-select-menu {
        border-color: var(--sari-dark-d8c2716225, #e1e4e8) !important;
        border-radius: 10px !important;
        box-shadow:
            0 15px 34px var(--sari-dark-71f44bdb16, rgba(31, 24, 17, .11)),
            0 3px 10px var(--sari-dark-25b2da5e49, rgba(31, 24, 17, .04)) !important;
    }

    /* Ledger */
    .finance-commissions .fc-ledger {
        margin-top: 14px !important;
    }

    .finance-commissions .fc-section-head {
        padding: 13px 15px !important;
        border-bottom-color: var(--fc-shell-divider) !important;
    }

    .finance-commissions .fc-table th {
        padding: 10px 12px !important;
        border-bottom-color: var(--fc-shell-divider) !important;
        background: var(--sari-dark-950baec030, #fafbfc) !important;
        color: var(--sari-dark-a63aa16d7c, #7d776f) !important;
        font-size: 7.5px !important;
        letter-spacing: .05em !important;
    }

    .finance-commissions .fc-table td {
        padding: 10px 12px !important;
        border-bottom-color: var(--sari-dark-d8c2716225, #f0f1f3) !important;
        font-size: 8px !important;
    }

    .finance-commissions .fc-table tbody tr:hover {
        background: var(--sari-dark-950baec030, #fcfbf9) !important;
    }

    .finance-commissions .fc-view-button {
        height: 30px !important;
        border-color: var(--sari-dark-d8c2716225, #e1e4e8) !important;
        border-radius: 8px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: none !important;
    }

    .finance-commissions .fc-view-button:hover,
    .finance-commissions .fc-view-button:focus-visible {
        outline: none !important;
        border-color: #e4c77e !important;
        background: var(--sari-dark-950baec030, #fff9ef) !important;
        color: var(--sari-dark-af18afcaa4, #94620a) !important;
    }

    .finance-commissions .fc-table-footer {
        min-height: 48px !important;
        padding: 9px 12px !important;
        border-top-color: var(--fc-shell-divider) !important;
    }

    /* Supporting finance cards */
    .finance-commissions .fc-bottom-grid {
        gap: 14px !important;
        margin-top: 14px !important;
    }

    .finance-commissions .fc-bottom-card {
        min-height: 224px !important;
        padding: 16px !important;
    }

    .finance-commissions .fc-seller-row,
    .finance-commissions .fc-recent-row {
        border-color: var(--sari-dark-d8c2716225, #f0f1f3) !important;
    }

    .finance-commissions .fc-payout-panel {
        margin-top: 14px !important;
    }

    .finance-commissions .fc-payout-row {
        border-bottom-color: var(--sari-dark-d8c2716225, #f0f1f3) !important;
    }

    .finance-commissions .fc-payout-input,
    .finance-commissions .fc-action {
        border-color: var(--sari-dark-d8c2716225, #e2e5e9) !important;
        border-radius: 8px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }

    /* Focal finance modals */
    .fc-modal-backdrop {
        background: var(--sari-dark-13762c5eae, rgba(28, 24, 20, .44)) !important;
    }

    .fc-modal {
        border-color: var(--sari-dark-d8c2716225, #dfe3e7) !important;
        border-radius: 16px !important;
        background: var(--sari-dark-950baec030, #f7f8f9) !important;
        box-shadow: var(--fc-shell-shadow-modal) !important;
    }

    .fc-modal-head {
        min-height: 58px !important;
        padding: 11px 14px !important;
        border-bottom-color: var(--fc-shell-divider) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }

    .fc-modal-title {
        font-size: 13px !important;
        letter-spacing: -.02em !important;
    }

    .fc-modal-close {
        width: 30px !important;
        height: 30px !important;
        border-color: var(--sari-dark-d8c2716225, #e1e4e8) !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }

    .fc-modal-body {
        padding: 14px !important;
        background: var(--sari-dark-950baec030, #f7f8f9) !important;
    }

    .fc-detail-box,
    .fc-history-row {
        border-color: var(--sari-dark-d8c2716225, #e1e4e8) !important;
        border-radius: 9px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }

    /* Keep smaller screens usable without changing finance behavior */
    @media (max-width: 1250px) {
        .finance-commissions {
            max-width: 100% !important;
        }
    }

    @media (max-width: 760px) {
        .finance-commissions {
            padding-bottom: 20px !important;
        }

        .finance-commissions .fc-page-head {
            align-items: stretch !important;
            padding-bottom: 14px !important;
        }

        .finance-commissions .fc-title {
            font-size: 26px !important;
        }

        .finance-commissions .fc-subtitle {
            font-size: 9.5px !important;
        }

        .finance-commissions .fc-head-actions {
            width: 100% !important;
            padding-bottom: 0 !important;
        }

        .finance-commissions .fc-date-control,
        .finance-commissions .fc-primary-button {
            min-height: 42px !important;
            height: auto !important;
        }

        .finance-commissions .fc-stat-card {
            min-height: 80px !important;
        }

        .finance-commissions .fc-chart-panel,
        .finance-commissions .fc-policy-panel,
        .finance-commissions .fc-bottom-card {
            padding: 14px !important;
        }

        .finance-commissions .fc-control,
        .finance-commissions .fc-custom-select-trigger,
        .finance-commissions .fc-filter-reset,
        .finance-commissions .fc-filter-apply {
            min-height: 42px !important;
            height: 42px !important;
            font-size: 9px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .finance-commissions .fc-stat-card,
        .finance-commissions .fc-filter-apply {
            transform: none !important;
        }
    }


    /* ============================================================
       PLATFORM COMMISSIONS — FINAL ADMIN USERS UI PARITY
       Visual-only override. Preserves finance calculations, routes,
       filters, chart data, exports, payouts, modals, and JavaScript.
       ============================================================ */

    .finance-commissions {
        --users-gold: #D99500;
        --users-ink: var(--sari-dark-e23e64dd1e, #1F2937);
        --users-muted: var(--sari-dark-a63aa16d7c, #7C8592);
        --users-border: var(--sari-dark-d8c2716225, #E5E7EB);
        --users-divider: var(--sari-dark-d8c2716225, #EEF0F3);
        max-width: 1880px !important;
        padding-bottom: 30px !important;
    }

    /* Open User Management-style page header. */
    .finance-commissions .fc-page-head {
        align-items: flex-end !important;
        gap: 24px !important;
        padding: 2px 0 18px !important;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #E7E9EC) !important;
    }

    .finance-commissions .fc-title-side {
        display: block !important;
        min-width: 0 !important;
    }

    .finance-commissions .fc-title-icon {
        display: none !important;
    }

    .finance-commissions .fc-eyebrow {
        margin: 0 0 5px !important;
        color: var(--sari-dark-a63aa16d7c, #9A6F23) !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        line-height: 1.15 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .finance-commissions .fc-title {
        margin: 0 !important;
        font-size: clamp(30px, 2.15vw, 37px) !important;
        font-weight: 700 !important;
        line-height: 1.02 !important;
        letter-spacing: -.045em !important;
    }

    .finance-commissions .fc-subtitle {
        max-width: 920px !important;
        margin-top: 7px !important;
        color: var(--sari-dark-a63aa16d7c, #7F8590) !important;
        font-size: 10.5px !important;
        line-height: 1.6 !important;
    }

    .finance-commissions .fc-head-actions {
        gap: 10px !important;
        padding-bottom: 0 !important;
    }

    .finance-commissions .fc-date-control,
    .finance-commissions .fc-primary-button {
        min-height: 42px !important;
        height: 42px !important;
        border-radius: 10px !important;
    }

    /* KPI cards: exact User Management visual scale. */
    .finance-commissions .fc-summary-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 12px !important;
        margin-top: 18px !important;
    }

    .finance-commissions .fc-stat-card {
        position: relative !important;
        min-height: 110px !important;
        overflow: hidden !important;
        border: 1px solid var(--users-border) !important;
        border-radius: 18px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        padding: 17px 64px 16px 18px !important;
        box-shadow:
            0 1px 2px var(--sari-dark-e1384eb7fc, rgba(17, 24, 39, .025)),
            0 8px 24px var(--sari-dark-fbd3b3b9f6, rgba(17, 24, 39, .055)),
            0 20px 44px var(--sari-dark-b13200ac05, rgba(17, 24, 39, .03)) !important;
        transform: none !important;
        contain: paint;
        transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease !important;
    }

    .finance-commissions .fc-stat-card:hover {
        border-color: var(--sari-dark-d8c2716225, #DADDE2) !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        box-shadow:
            0 1px 2px var(--sari-dark-e1384eb7fc, rgba(17, 24, 39, .025)),
            0 10px 27px var(--sari-dark-4fc908fafe, rgba(17, 24, 39, .065)),
            0 22px 48px var(--sari-dark-b13200ac05, rgba(17, 24, 39, .03)) !important;
        transform: translateY(-2px) !important;
    }

    .finance-commissions .fc-stat-inner {
        min-height: 76px !important;
    }

    .finance-commissions .fc-stat-label {
        color: var(--sari-dark-a63aa16d7c, #7D8590) !important;
        font-size: 11.5px !important;
        font-weight: 500 !important;
        line-height: 1.35 !important;
    }

    .finance-commissions .fc-stat-row {
        gap: 6px !important;
        margin-top: 7px !important;
    }

    .finance-commissions .fc-stat-value {
        color: var(--sari-dark-e23e64dd1e, #20242B) !important;
        font-size: 26px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .finance-commissions .fc-stat-help {
        margin-top: 8px !important;
        color: var(--sari-dark-a63aa16d7c, #98A0AA) !important;
        font-size: 9px !important;
        line-height: 1.4 !important;
    }

    .finance-commissions .fc-delta {
        min-height: 21px !important;
        padding: 0 7px !important;
        font-size: 7px !important;
    }

    /* Final Users-page icon treatment: no box, tile, circle, border, or background. */
    .finance-commissions .fc-stat-icon,
    .finance-commissions .fc-stat-icon.rate,
    .finance-commissions .fc-stat-icon.orders,
    .finance-commissions .fc-stat-icon.sales,
    .finance-commissions .fc-stat-icon.earned {
        position: absolute !important;
        top: 16px !important;
        right: 16px !important;
        display: grid !important;
        width: 28px !important;
        height: 28px !important;
        place-items: center !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .finance-commissions .fc-stat-icon svg {
        width: 24px !important;
        height: 24px !important;
        stroke-width: 1.8 !important;
    }

    .finance-commissions .fc-stat-icon.rate {
        color: var(--sari-dark-7f7834ccc9, #D99500) !important;
    }

    .finance-commissions .fc-stat-icon.orders {
        color: var(--sari-dark-8f882a075f, #2563EB) !important;
    }

    .finance-commissions .fc-stat-icon.sales {
        color: var(--sari-dark-904a7bfa5a, #7C3AED) !important;
    }

    .finance-commissions .fc-stat-icon.earned {
        color: var(--sari-dark-bc794282b4, #16A34A) !important;
    }

    /* Keep analytics useful but align surface depth/radii with Users. */
    .finance-commissions .fc-analytics-grid {
        gap: 14px !important;
        margin-top: 14px !important;
    }

    .finance-commissions .fc-chart-panel,
    .finance-commissions .fc-policy-panel,
    .finance-commissions .fc-bottom-card,
    .finance-commissions .fc-payout-panel {
        border: 1px solid var(--users-border) !important;
        border-radius: 18px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        box-shadow:
            0 1px 2px var(--sari-dark-e1384eb7fc, rgba(17,24,39,.025)),
            0 8px 24px var(--sari-dark-1df26971b5, rgba(17,24,39,.05)),
            0 20px 44px var(--sari-dark-e1384eb7fc, rgba(17,24,39,.025)) !important;
    }

    /* User Management-style bare filter row instead of an extra filter card. */
    .finance-commissions .fc-filter-card {
        margin-top: 14px !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    .finance-commissions .fc-filter-grid {
        grid-template-columns: minmax(260px, 1.2fr) 165px 185px minmax(220px, .9fr) 82px 118px !important;
        gap: 10px !important;
    }

    .finance-commissions .fc-control,
    .finance-commissions .fc-custom-select-trigger,
    .finance-commissions .fc-filter-reset,
    .finance-commissions .fc-filter-apply {
        min-height: 42px !important;
        height: 42px !important;
        border-radius: 10px !important;
        font-size: 9.5px !important;
    }

    .finance-commissions .fc-control,
    .finance-commissions .fc-custom-select-trigger,
    .finance-commissions .fc-filter-reset {
        border-color: var(--sari-dark-d8c2716225, #E2E5E9) !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        color: var(--sari-dark-e23e64dd1e, #344054) !important;
        box-shadow: 0 1px 2px var(--sari-dark-e1384eb7fc, rgba(17,24,39,.025)) !important;
    }

    .finance-commissions .fc-filter-apply {
        border-color: #D99500 !important;
        background: #D99500 !important;
        box-shadow: 0 6px 16px var(--sari-dark-59ed29fe9d, rgba(217,149,0,.14)) !important;
    }

    .finance-commissions .fc-filter-apply:hover {
        background: #C48700 !important;
        border-color: #C48700 !important;
        transform: none !important;
    }

    /* Commission ledger = Admin Users table scale and hierarchy. */
    .finance-commissions .fc-ledger {
        margin-top: 14px !important;
        overflow: hidden !important;
        border: 1px solid var(--users-border) !important;
        border-radius: 18px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        box-shadow:
            0 1px 2px var(--sari-dark-e1384eb7fc, rgba(17,24,39,.025)),
            0 9px 26px var(--sari-dark-fbd3b3b9f6, rgba(17,24,39,.055)),
            0 22px 48px var(--sari-dark-e1384eb7fc, rgba(17,24,39,.025)) !important;
    }

    .finance-commissions .fc-section-head {
        min-height: 66px !important;
        padding: 14px 16px !important;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #ECEFF2) !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
    }

    .finance-commissions .fc-ledger .fc-panel-title {
        color: var(--sari-dark-e23e64dd1e, #252A32) !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }

    .finance-commissions .fc-ledger .fc-panel-copy {
        margin-top: 4px !important;
        color: var(--sari-dark-a63aa16d7c, #98A2B3) !important;
        font-size: 9px !important;
    }

    .finance-commissions .fc-link-button {
        min-height: 38px !important;
        border-color: var(--sari-dark-d8c2716225, #E2E5E9) !important;
        border-radius: 9px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        box-shadow: none !important;
    }

    .finance-commissions .fc-table {
        min-width: 1080px !important;
    }

    .finance-commissions .fc-table thead {
        background: var(--sari-dark-950baec030, #F8FAFC) !important;
    }

    .finance-commissions .fc-table th {
        padding: 12px 16px !important;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #ECEFF2) !important;
        background: var(--sari-dark-950baec030, #F8FAFC) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.35 !important;
        letter-spacing: .065em !important;
        text-transform: uppercase !important;
        white-space: nowrap;
    }

    .finance-commissions .fc-table td {
        padding: 13px 16px !important;
        border-bottom: 1px solid var(--sari-dark-d8c2716225, #EEF0F3) !important;
        color: var(--sari-dark-e23e64dd1e, #475467) !important;
        font-size: 10.5px !important;
        line-height: 1.45 !important;
        vertical-align: middle !important;
    }

    .finance-commissions .fc-table tbody tr {
        min-height: 68px !important;
        contain-intrinsic-size: 68px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
    }

    .finance-commissions .fc-table tbody tr:hover {
        background: var(--sari-dark-950baec030, #FAFBFC) !important;
    }

    .finance-commissions .fc-order,
    .finance-commissions .fc-money {
        color: var(--sari-dark-e23e64dd1e, #252A32) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .finance-commissions .fc-status {
        min-height: 25px !important;
        gap: 6px !important;
        padding: 0 9px !important;
        border-radius: 999px !important;
        font-size: 9px !important;
        font-weight: 600 !important;
    }

    .finance-commissions .fc-status::before {
        width: 5px !important;
        height: 5px !important;
    }

    .finance-commissions .fc-view-button {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        border: 1px solid transparent !important;
        border-radius: 9px !important;
        background: transparent !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        font-size: 0 !important;
        box-shadow: none !important;
        position: relative;
    }

    .finance-commissions .fc-view-button::before {
        content: "";
        width: 15px;
        height: 15px;
        display: block;
        background:
            center / 15px 15px no-repeat
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23667085' stroke-width='1.8'%3E%3Cpath d='M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6S2.5 12 2.5 12Z'/%3E%3Ccircle cx='12' cy='12' r='2.5'/%3E%3C/svg%3E");
    }

    .finance-commissions .fc-view-button:hover,
    .finance-commissions .fc-view-button:focus-visible {
        outline: none !important;
        border-color: var(--sari-dark-d8c2716225, #F0E1BF) !important;
        background: var(--sari-dark-950baec030, #FFFAF0) !important;
        color: var(--sari-dark-7f7834ccc9, #D99500) !important;
    }

    .finance-commissions .fc-view-button:hover::before,
    .finance-commissions .fc-view-button:focus-visible::before {
        background-image:
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23D99500' stroke-width='1.8'%3E%3Cpath d='M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6S2.5 12 2.5 12Z'/%3E%3Ccircle cx='12' cy='12' r='2.5'/%3E%3C/svg%3E");
    }

    .finance-commissions .fc-table-footer {
        min-height: 54px !important;
        padding: 11px 16px !important;
        border-top: 1px solid var(--sari-dark-d8c2716225, #ECEFF2) !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        font-size: 9.5px !important;
    }

    .finance-commissions .fc-pagination {
        gap: 6px !important;
    }

    .finance-commissions .fc-page-link {
        min-width: 32px !important;
        height: 32px !important;
        border-color: var(--sari-dark-d8c2716225, #E2E5E9) !important;
        border-radius: 9px !important;
        background: var(--sari-dark-950baec030, #FFFFFF) !important;
        color: var(--sari-dark-a63aa16d7c, #667085) !important;
        font-size: 9px !important;
    }

    .finance-commissions .fc-page-link.active {
        border-color: #D99500 !important;
        background: #D99500 !important;
        color: #FFFFFF !important;
        box-shadow: 0 6px 16px var(--sari-dark-59ed29fe9d, rgba(217,149,0,.14)) !important;
    }

    @media (max-width: 1250px) {
        .finance-commissions .fc-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .finance-commissions .fc-filter-grid {
            grid-template-columns: minmax(240px, 1fr) 160px 180px !important;
        }
    }

    @media (max-width: 760px) {
        .finance-commissions {
            padding-bottom: 20px !important;
        }

        .finance-commissions .fc-page-head {
            align-items: stretch !important;
            padding-bottom: 14px !important;
        }

        .finance-commissions .fc-title {
            font-size: 26px !important;
        }

        .finance-commissions .fc-subtitle {
            font-size: 10px !important;
        }

        .finance-commissions .fc-summary-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .finance-commissions .fc-stat-card {
            min-height: 104px !important;
        }

        .finance-commissions .fc-stat-icon {
            top: 15px !important;
            right: 15px !important;
        }

        .finance-commissions .fc-filter-grid {
            grid-template-columns: 1fr !important;
        }

        .finance-commissions .fc-control,
        .finance-commissions .fc-custom-select-trigger,
        .finance-commissions .fc-filter-reset,
        .finance-commissions .fc-filter-apply {
            min-height: 44px !important;
            height: 44px !important;
            font-size: 10px !important;
        }

        .finance-commissions .fc-ledger,
        .finance-commissions .fc-chart-panel,
        .finance-commissions .fc-policy-panel,
        .finance-commissions .fc-bottom-card,
        .finance-commissions .fc-payout-panel {
            border-radius: 14px !important;
        }
    }


    /* ============================================================
       BUYER CATALOG VISUAL PARITY — COMMISSIONS
       Page-scoped presentation only.
       ============================================================ */
    .finance-commissions {
        --fc-brand: #c88912;
        --fc-brand-strong: #a96d08;
        --fc-ink: var(--sari-dark-e23e64dd1e, #29323b);
        --fc-text: var(--sari-dark-e23e64dd1e, #4c545e);
        --fc-muted: var(--sari-dark-a63aa16d7c, #707b86);
        --fc-line: var(--sari-dark-d8c2716225, #e5e7eb);
        --fc-surface-shadow: 0 2px 5px var(--sari-dark-1190f58155, rgba(15,23,42,.045)), 0 10px 24px var(--sari-dark-e07575a086, rgba(15,23,42,.075)), 0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15,23,42,.095)), inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.92));
        --fc-surface-shadow-mobile: 0 2px 4px var(--sari-dark-7593018246, rgba(15,23,42,.035)), 0 8px 18px var(--sari-dark-361b571631, rgba(15,23,42,.06)), 0 18px 38px var(--sari-dark-7c935c8886, rgba(15,23,42,.07));
        width: 100%;
        max-width: 1640px !important;
        min-width: 0;
        padding: 2px 2px 32px !important;
        font-size: 12px;
        line-height: 1.5;
        container-type: inline-size;
    }
    .finance-commissions :is(button,input,select,textarea) { font-family: inherit; }
    .finance-commissions :is(.fc-card,.fc-stat-card) {
        min-width: 0;
        border: 1px solid var(--fc-line) !important;
        border-radius: 17px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        box-shadow: var(--fc-surface-shadow) !important;
    }
    .finance-commissions .fc-stat-card:hover {
        border-color: var(--sari-dark-d8c2716225, #d5dbe0) !important;
        box-shadow: var(--fc-surface-shadow) !important;
        transform: none !important;
    }
    .finance-commissions .fc-page-head {
        align-items: flex-end !important;
        gap: 20px !important;
        padding: 2px 0 16px !important;
        border-bottom: 0 !important;
    }
    .finance-commissions .fc-eyebrow {
        margin: 0 !important;
        color: var(--sari-dark-685eb4fc40, #a87316) !important;
        font-size: 10.5px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
    }
    .finance-commissions .fc-title {
        margin-top: 5px !important;
        font-size: clamp(29px,2.35vw,34px) !important;
        font-weight: 750 !important;
        line-height: 1.04 !important;
        letter-spacing: -.035em !important;
    }
    .finance-commissions .fc-title-platform { color: var(--sari-dark-e23e64dd1e, #2e343b) !important; }
    .finance-commissions .fc-title-gold { color: var(--sari-dark-19b301f69a, #c88912) !important; }
    .finance-commissions .fc-subtitle {
        max-width: 760px !important;
        margin-top: 7px !important;
        color: var(--sari-dark-a63aa16d7c, #707b86) !important;
        font-size: 11.5px !important;
        line-height: 1.55 !important;
    }
    .finance-commissions .fc-head-actions { flex-wrap: wrap; gap: 10px !important; }
    .finance-commissions .fc-date-control {
        min-height: 42px !important;
        height: auto !important;
        border-color: var(--sari-dark-d8c2716225, #dfe3e8) !important;
        background: var(--sari-dark-950baec030, #fff) !important;
    }
    .finance-commissions .fc-date-control input { width: 122px !important; font-size: 11px !important; }
    .finance-commissions .fc-date-arrow { font-size: 11px; }
    .finance-commissions :is(.fc-primary-button,.fc-secondary-button,.fc-link-button,.fc-filter-reset,.fc-filter-apply) {
        min-height: 42px !important;
        height: 42px !important;
        padding-inline: 13px !important;
        border-radius: 9px !important;
        font-size: 11px !important;
        font-weight: 650 !important;
        white-space: nowrap;
    }
    .finance-commissions :is(.fc-primary-button,.fc-filter-apply) {
        border-color: var(--fc-brand) !important;
        background: var(--fc-brand) !important;
        box-shadow: 0 6px 16px var(--sari-dark-59ed29fe9d, rgba(201,132,8,.14)) !important;
    }
    .finance-commissions :is(.fc-primary-button,.fc-filter-apply):hover {
        border-color: var(--fc-brand-strong) !important;
        background: var(--fc-brand-strong) !important;
        transform: none !important;
    }
    .finance-commissions :is(.fc-primary-button,.fc-secondary-button,.fc-link-button,.fc-filter-reset,.fc-filter-apply) svg {
        width: 15px !important; height: 15px !important; flex-shrink: 0;
    }
    .finance-commissions :is(.fc-summary-grid,.fc-analytics-grid,.fc-bottom-grid) { gap: 18px !important; margin-top: 18px !important; }
    .finance-commissions .fc-stat-card { min-height: 110px !important; padding: 17px 52px 16px 18px !important; contain: none; }
    .finance-commissions .fc-stat-inner { min-height: 76px !important; }
    .finance-commissions .fc-stat-label { color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 11.5px !important; }
    .finance-commissions .fc-stat-row { gap: 7px !important; margin-top: 7px !important; }
    .finance-commissions .fc-stat-value { color: var(--sari-dark-e23e64dd1e, #29323b) !important; font-size: clamp(22px,1.6vw,26px) !important; line-height: 1.2 !important; overflow-wrap: anywhere; font-variant-numeric: tabular-nums; }
    .finance-commissions .fc-stat-help { color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 10.5px !important; margin-top: 7px !important; }
    .finance-commissions .fc-delta { min-height: 23px !important; font-size: 9px !important; }
    .finance-commissions .fc-stat-icon { top: 17px !important; right: 15px !important; width: 26px !important; height: 26px !important; }
    .finance-commissions .fc-stat-icon svg { width: 22px !important; height: 22px !important; }
    .finance-commissions :is(.fc-chart-panel,.fc-policy-panel,.fc-bottom-card) { padding: 18px !important; }
    .finance-commissions :is(.fc-chart-panel,.fc-policy-panel) { min-height: 310px !important; }
    .finance-commissions .fc-analytics-grid { grid-template-columns: minmax(0,1.5fr) minmax(350px,1fr); }
    .finance-commissions .fc-panel-title { color: var(--sari-dark-e23e64dd1e, #29323b) !important; font-size: 14px !important; font-weight: 750 !important; line-height: 1.35; }
    .finance-commissions .fc-panel-copy { color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 10.5px !important; line-height: 1.55; }
    .finance-commissions .fc-panel-head { flex-wrap: wrap; }
    .finance-commissions .fc-range-tabs { flex-wrap: wrap; gap: 5px; }
    .finance-commissions .fc-range-tab { min-width: 36px !important; height: 30px !important; font-size: 10px; }
    .finance-commissions .fc-range-tab.active { border-color: var(--fc-brand) !important; background: var(--fc-brand) !important; }
    .finance-commissions :is(.fc-mini-button,.fc-history-button) { min-height: 34px; height: auto; font-size: 10.5px; }
    .finance-commissions .fc-history-button { min-height: 38px; margin-top: 15px; }
    .finance-commissions .fc-policy-body { gap: 16px; margin-top: 18px !important; }
    .finance-commissions .fc-policy-list { gap: 10px; }
    .finance-commissions .fc-policy-label { color: var(--sari-dark-a63aa16d7c, #707b86); font-size: 10px; }
    .finance-commissions .fc-policy-value { color: var(--sari-dark-e23e64dd1e, #4c545e); font-size: 11px; }
    .finance-commissions .fc-policy-item-icon svg { width: 15px; height: 15px; }
    .finance-commissions :is(.fc-rate-label,.fc-status-caption) { font-size: 10px; }
    .finance-commissions .fc-chart-tooltip small { font-size: 10.5px; }
    .finance-commissions .fc-chart-tooltip strong { font-size: 12px; }
    .finance-commissions .fc-chart-empty { font-size: 11px; }
    /* Open filter workspace, with the reference's white search and dropdowns. */
    .finance-commissions .fc-filter-card {
        margin-top: 22px !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
    .finance-commissions .fc-filter-grid {
        grid-template-columns: minmax(240px,1.4fr) minmax(140px,.75fr) minmax(155px,.85fr) minmax(240px,1fr) 76px 122px !important;
        align-items: end;
        gap: 10px !important;
    }
    .finance-commissions .fc-filter-label { display: block; margin-bottom: 6px; color: var(--sari-dark-e23e64dd1e, #4b5560); font-size: 11px; font-weight: 750; letter-spacing: .045em; line-height: 1.3; }
    .finance-commissions .fc-search > svg { top: auto; bottom: 13px; width: 16px; height: 16px; transform: none; color: var(--sari-dark-a63aa16d7c, #596470); }
    .finance-commissions :is(.fc-control,.fc-custom-select-trigger) {
        min-width: 0;
        min-height: 42px !important;
        height: 42px !important;
        border: 1px solid var(--sari-dark-d8c2716225, #dfe3e8) !important;
        border-radius: 9px !important;
        background: var(--sari-dark-950baec030, #fff) !important;
        color: var(--sari-dark-e23e64dd1e, #333d47) !important;
        font-size: 12px !important;
        font-weight: 550 !important;
        box-shadow: none !important;
    }
    .finance-commissions .fc-control { padding-inline: 11px; }
    .finance-commissions .fc-search .fc-control { padding-left: 37px; padding-right: 12px; }
    .finance-commissions .fc-control::placeholder { color: var(--sari-dark-a63aa16d7c, #79838e); font-size: 11.5px; opacity: 1; }
    .finance-commissions .fc-search .fc-control:focus { border-color: var(--sari-dark-d8c2716225, #9faab5) !important; box-shadow: 0 0 0 3px var(--sari-dark-8eadf403a5, rgba(75,85,99,.10)) !important; }
    .finance-commissions .fc-custom-select-trigger { padding-inline: 12px; font-weight: 650 !important; }
    .finance-commissions .fc-custom-select-main { min-width: 0; }
    .finance-commissions .fc-custom-select-chevron { width: 14px; height: 14px; flex-basis: 14px; color: var(--sari-dark-a63aa16d7c, #596470); }
    .finance-commissions .fc-custom-select-menu {
        top: calc(100% + 6px);
        max-height: 270px;
        padding: 5px;
        border: 1px solid var(--sari-dark-d8c2716225, #e1e5e9) !important;
        border-radius: 12px !important;
        box-shadow: 0 18px 42px var(--sari-dark-3e32d50f5e, rgba(31,41,55,.13)), 0 4px 12px var(--sari-dark-25b2da5e49, rgba(31,41,55,.04)) !important;
    }
    .finance-commissions .fc-custom-select-option { min-height: 39px; padding: 7px 10px; border-radius: 8px; color: var(--sari-dark-e23e64dd1e, #4c545e); font-size: 11.5px; line-height: 1.4; }
    .finance-commissions .fc-custom-select-option:hover { background: var(--sari-dark-950baec030, #f5f6f8); color: var(--sari-dark-e23e64dd1e, #22282f); }
    .finance-commissions .fc-custom-select-option.is-selected { background: var(--sari-dark-4d21eb669b, #f7f3eb); color: var(--sari-dark-9b2ca2d764, #8e5d0e); font-weight: 700; }
    .finance-commissions .fc-custom-select-check { width: 14px; height: 14px; flex-basis: 14px; }
    .finance-commissions .fc-filter-dates { gap: 8px; min-width: 0; }
    .finance-commissions .fc-date-field { min-width: 0; }
    .finance-commissions .fc-filter-reset { color: var(--sari-dark-9b2ca2d764, #865709) !important; border-color: transparent !important; background: transparent !important; box-shadow: none !important; }
    .finance-commissions .fc-filter-reset:hover { color: var(--sari-dark-19b301f69a, #c88912) !important; }
    /* Ledger and selection controls. */
    .finance-commissions .fc-ledger { margin-top: 18px !important; }
    .finance-commissions .fc-section-head { min-height: 72px !important; padding: 16px 18px !important; flex-wrap: wrap; gap: 12px; }
    .finance-commissions .fc-ledger .fc-panel-title { font-size: 14px !important; }
    .finance-commissions .fc-ledger .fc-panel-copy { color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 10.5px !important; }
    .finance-commissions .fc-ledger-tools { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 12px; }
    .finance-commissions .fc-selection-summary { display: flex; align-items: center; gap: 9px; color: var(--sari-dark-a63aa16d7c, #707b86); font-size: 11px; }
    .finance-commissions .fc-selection-summary[hidden] { display: none; }
    .finance-commissions .fc-selection-clear { border: 0; background: transparent; color: var(--sari-dark-6e784b4931, #9b6710); padding: 5px; font-size: 11px; font-weight: 650; cursor: pointer; }
    .finance-commissions .fc-table-wrap { max-width: 100%; scrollbar-width: thin; scrollbar-color: #d9dde2 transparent; }
    .finance-commissions .fc-table { min-width: 1120px !important; }
    .finance-commissions .fc-table th { padding: 12px 14px !important; background: var(--sari-dark-950baec030, #f8fafc) !important; color: var(--sari-dark-a63aa16d7c, #667085) !important; font-size: 10.5px !important; font-weight: 650 !important; letter-spacing: .045em !important; }
    .finance-commissions .fc-table td { padding: 14px !important; color: var(--sari-dark-e23e64dd1e, #4c545e) !important; font-size: 12px !important; line-height: 1.5 !important; }
    .finance-commissions .fc-table tbody tr { content-visibility: visible; contain-intrinsic-size: none !important; background: var(--sari-dark-950baec030, #fff) !important; }
    .finance-commissions .fc-table tbody tr:hover { background: var(--sari-dark-950baec030, #fafbfc) !important; }
    .finance-commissions .fc-table tbody tr.fc-row-selected { background: var(--sari-dark-4d21eb669b, #fff9ed) !important; }
    .finance-commissions .fc-table .fc-check-cell { width: 42px; padding-right: 2px !important; }
    .finance-commissions .fc-checkbox { display: block; width: 16px; height: 16px; margin: 0; accent-color: var(--fc-brand); cursor: pointer; }
    .finance-commissions .fc-checkbox:disabled { cursor: default; opacity: .45; }
    .finance-commissions :is(.fc-order,.fc-money) { color: var(--sari-dark-e23e64dd1e, #29323b) !important; font-size: 12px !important; font-weight: 650 !important; font-variant-numeric: tabular-nums; }
    .finance-commissions .fc-status { min-height: 26px !important; font-size: 10.5px !important; padding: 3px 9px !important; white-space: nowrap; }
    .finance-commissions .fc-view-button { width: 34px !important; height: 34px !important; }
    .finance-commissions .fc-view-button::before { width: 16px; height: 16px; background-size: 16px; }
    .finance-commissions .fc-table-footer { min-height: 60px !important; padding: 13px 18px !important; color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 11px !important; flex-wrap: wrap; }
    .finance-commissions .fc-pagination { flex-wrap: wrap; gap: 6px !important; }
    .finance-commissions .fc-page-link { min-width: 34px !important; height: 34px !important; padding-inline: 6px; font-size: 11px !important; }
    .finance-commissions .fc-page-link.active { background: var(--fc-brand) !important; border-color: var(--fc-brand) !important; box-shadow: none !important; }
    .finance-commissions .fc-page-link:not(.active):not(.disabled):hover { background: var(--sari-dark-4d21eb669b, #f7f3eb) !important; border-color: #dbc18a !important; }
    /* Supporting panels use the same readable typography. */
    .finance-commissions .fc-bottom-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
    .finance-commissions .fc-bottom-card:last-child { grid-column: 1 / -1; }
    .finance-commissions .fc-bottom-card { min-height: 224px !important; }
    .finance-commissions .fc-bottom-title-row { flex-wrap: wrap; }
    .finance-commissions .fc-seller-row { grid-template-columns: 24px minmax(0,1fr) minmax(32px,.6fr) auto; min-height: 42px; gap: 9px; }
    .finance-commissions .fc-rank { font-size: 10.5px; }
    .finance-commissions :is(.fc-seller-name,.fc-seller-amount,.fc-recent-order,.fc-recent-seller,.fc-recent-amount) { font-size: 11.5px; }
    .finance-commissions :is(.fc-seller-share,.fc-recent-date) { font-size: 10px; }
    .finance-commissions .fc-status-legend-row { grid-template-columns: 8px minmax(0,1fr) 38px 44px; font-size: 11px; gap: 9px; }
    .finance-commissions .fc-status-legend { gap: 10px; }
    .finance-commissions .fc-recent-row { grid-template-columns: 20px minmax(0,1fr) minmax(0,1fr) 110px 90px; min-height: 44px; }
    .finance-commissions .fc-empty { min-height: 130px; padding: 20px; font-size: 11.5px; line-height: 1.6; }
    .finance-commissions .fc-payout-panel { margin-top: 18px !important; }
    .finance-commissions .fc-payout-row { min-height: 68px; padding: 13px 18px; grid-template-columns: minmax(140px,1fr) 110px 100px minmax(260px,1.2fr); }
    .finance-commissions :is(.fc-payout-name,.fc-payout-amount) { font-size: 12px; }
    .finance-commissions .fc-payout-meta { font-size: 10.5px; }
    .finance-commissions .fc-chip { min-height: 26px; font-size: 10px; padding-inline: 8px; }
    .finance-commissions :is(.fc-action,.fc-payout-input) { height: 36px; font-size: 11px; }
    .finance-commissions .fc-payout-actions { flex-wrap: wrap; }
    .finance-commissions .fc-payout-actions form { min-width: 0; flex-wrap: wrap; }
    /* Modals are siblings of the page wrapper, so use explicit values. */
    .fc-modal-backdrop :is(.fc-modal,.fc-modal-title,.fc-panel-copy,.fc-detail-box,.fc-history-row) { font-family: 'Poppins',ui-sans-serif,system-ui,sans-serif; }
    .fc-modal-backdrop .fc-modal { border-radius: 17px !important; box-shadow: 0 26px 70px var(--sari-dark-ef4e8433d9, rgba(31,24,17,.19)),0 10px 28px var(--sari-dark-c33e3d1bb4, rgba(31,24,17,.08)) !important; }
    .fc-modal-backdrop .fc-modal-head { padding: 16px 18px !important; border-bottom-color: var(--sari-dark-d8c2716225, #eceff2) !important; }
    .fc-modal-backdrop .fc-modal-title { font-size: 15px !important; }
    .fc-modal-backdrop .fc-panel-copy { font-size: 11px; color: var(--sari-dark-a63aa16d7c, #707b86); }
    .fc-modal-backdrop .fc-modal-body { padding: 18px !important; }
    .fc-modal-backdrop :is(.fc-detail-box,.fc-history-row) { padding: 12px; }
    .fc-modal-backdrop .fc-detail-label { font-size: 10.5px; color: var(--sari-dark-a63aa16d7c, #707b86); }
    .fc-modal-backdrop .fc-detail-value { font-size: 12px; }
    .fc-modal-backdrop .fc-history-rate { font-size: 13px; }
    .fc-modal-backdrop :is(.fc-history-copy,.fc-history-date) { font-size: 10.5px; }
    .finance-commissions :is(a,button,input):focus-visible,
    .fc-modal-backdrop button:focus-visible { outline: 2px solid #9b6710 !important; outline-offset: 3px; }
    @container (max-width: 1250px) {
        .finance-commissions .fc-page-head { flex-direction: column; align-items: stretch !important; }
        .finance-commissions .fc-head-actions { justify-content: flex-start !important; }
        .finance-commissions .fc-filter-grid { grid-template-columns: repeat(4,minmax(0,1fr)) !important; }
        .finance-commissions .fc-search { grid-column: span 2; }
        .finance-commissions .fc-filter-dates { grid-column: span 2; }
        .finance-commissions .fc-analytics-grid { grid-template-columns: 1fr; }
        .finance-commissions .fc-payout-row { grid-template-columns: 1fr 110px 100px; }
        .finance-commissions .fc-payout-actions { grid-column: 1 / -1; justify-content: flex-start; }
    }
    @container (max-width: 900px) {
        .finance-commissions .fc-summary-grid { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
        .finance-commissions .fc-bottom-grid { grid-template-columns: 1fr; }
        .finance-commissions .fc-bottom-card:last-child { grid-column: auto; }
    }
    @container (max-width: 600px) {
        .finance-commissions .fc-filter-grid { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
        .finance-commissions .fc-search { grid-column: 1 / -1; }
        .finance-commissions .fc-filter-dates { grid-column: 1 / -1; }
        .finance-commissions .fc-head-actions { flex-direction: column; align-items: stretch !important; }
        .finance-commissions .fc-date-control { flex-wrap: wrap; width: 100%; padding-block: 8px !important; }
        .finance-commissions .fc-date-control input { width: 110px !important; min-width: 0; flex: 1; }
        .finance-commissions .fc-policy-body { grid-template-columns: 1fr; justify-items: center; }
        .finance-commissions .fc-policy-list { width: 100%; }
        .finance-commissions .fc-seller-row { grid-template-columns: 24px minmax(0,1fr) auto; }
        .finance-commissions .fc-progress { display: none; }
        .finance-commissions .fc-recent-row { grid-template-columns: 20px minmax(0,1fr) 110px; }
        .finance-commissions :is(.fc-recent-seller,.fc-recent-date) { display: none; }
        .finance-commissions .fc-payout-row { grid-template-columns: 1fr; }
        .finance-commissions .fc-ledger-tools { width: 100%; justify-content: space-between; }
    }
    @container (max-width: 400px) {
        .finance-commissions .fc-summary-grid { grid-template-columns: 1fr !important; }
        .finance-commissions .fc-filter-grid { grid-template-columns: 1fr !important; }
        .finance-commissions .fc-search,.finance-commissions .fc-filter-dates { grid-column: auto; }
    }
    @media (max-width: 767px) {
        .finance-commissions .fc-title { font-size: 29px !important; }
        .finance-commissions .fc-eyebrow { font-size: 9.5px !important; }
        .finance-commissions :is(.fc-card,.fc-stat-card) { border-radius: 14px !important; box-shadow: var(--fc-surface-shadow-mobile) !important; }
        .finance-commissions .fc-filter-card { border-radius: 0 !important; box-shadow: none !important; }
        .finance-commissions :is(.fc-summary-grid,.fc-analytics-grid,.fc-bottom-grid) { gap: 14px !important; margin-top: 14px !important; }
        .finance-commissions :is(.fc-control,.fc-custom-select-trigger,.fc-filter-reset,.fc-filter-apply) { min-height: 44px !important; height: 44px !important; }
        .finance-commissions .fc-search > svg { bottom: 14px; }
        .finance-commissions .fc-table-footer { align-items: flex-start; }
        .fc-modal-backdrop .fc-modal { border-radius: 14px !important; }
    }
    @media (prefers-reduced-motion: reduce) {
        .finance-commissions * { transition: none !important; animation: none !important; scroll-behavior: auto !important; }
        .finance-commissions .fc-stat-card:hover { transform: none !important; }
    }

</style>

@php
    $commissionRate = max(0, min(100, (float) ($stats['rate'] ?? 0)));
    $rateDegrees = $commissionRate * 3.6;
    $exportUrl = route('admin.commissions', array_merge(request()->except(['page','export']), ['export' => 'csv']));
    $resetUrl = route('admin.commissions', $period['range'] === 'custom'
        ? ['range' => 'custom', 'from' => $period['from'], 'to' => $period['to']]
        : ['range' => $period['range']]);

    $rangeUrl = function (string $range) {
        return route('admin.commissions', array_merge(
            request()->except(['range','from','to','page','export']),
            ['range' => $range]
        ));
    };

    $formatRate = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
    $formatStatus = fn ($value) => ucfirst(str_replace('_', ' ', (string) $value));

    $statusTotal = (int) collect($statusBreakdown)->sum('count');
    $statusCursor = 0.0;
    $statusGradient = [];
    foreach ($statusBreakdown as $statusItem) {
        $share = $statusTotal > 0 ? ((int) $statusItem['count'] / $statusTotal) : 0;
        $startDeg = $statusCursor * 360;
        $statusCursor += $share;
        $endDeg = $statusCursor * 360;
        $statusGradient[] = $statusItem['color'].' '.$startDeg.'deg '.$endDeg.'deg';
    }
    if ($statusTotal === 0) {
        $statusGradient = ['#eef1f4 0deg 360deg'];
    }

    $payoutCollection = collect($payoutRequests ?? []);
    $pendingPayouts = $payoutCollection->where('status', 'pending')->count();
    $approvedPayouts = $payoutCollection->where('status', 'approved')->count();
    $paidPayouts = $payoutCollection->where('status', 'paid')->count();
@endphp

<div class="finance-commissions">
    @if(session('success'))
        <div class="fc-card" style="margin-bottom:12px;padding:10px 12px;border-color:var(--sari-dark-d8c2716225, #d7eadf);background:var(--sari-dark-950baec030, #f4fbf6);color:var(--sari-dark-e23e64dd1e, #356b50);font-size:8px;">
            {{ session('success') }}
        </div>
    @endif

    <section class="fc-page-head">
        <div class="fc-title-side">
            <span class="fc-title-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <ellipse cx="9" cy="6" rx="4.5" ry="2.3"></ellipse>
                    <path d="M4.5 6v4c0 1.3 2 2.3 4.5 2.3s4.5-1 4.5-2.3V6"></path>
                    <path d="M4.5 10v4c0 1.3 2 2.3 4.5 2.3.9 0 1.7-.1 2.4-.3"></path>
                    <circle cx="17" cy="15.5" r="4"></circle>
                    <path d="M17 13.5v4M15.7 14.5h2c.7 0 1.1.35 1.1.85s-.4.85-1.1.85h-1.5"></path>
                </svg>
            </span>
            <div>
                <div class="fc-eyebrow">Finance</div>
                <h1 class="fc-title"><span class="fc-title-platform">Platform</span> <span class="fc-title-gold">Commissions</span></h1>
                <p class="fc-subtitle">Monitor platform earnings, analyze commission performance, and manage commission settings.</p>
            </div>
        </div>

        <div class="fc-head-actions">
            <form method="GET" action="{{ route('admin.commissions') }}" class="fc-date-control">
                <input type="hidden" name="range" value="custom">
                @if($filters['search'] !== '')<input type="hidden" name="search" value="{{ $filters['search'] }}">@endif
                @if($filters['status'] !== '')<input type="hidden" name="status" value="{{ $filters['status'] }}">@endif
                @if($filters['seller'] > 0)<input type="hidden" name="seller" value="{{ $filters['seller'] }}">@endif
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg>
                <input type="date" name="from" value="{{ $period['from'] }}" aria-label="From date">
                <span class="fc-date-arrow">—</span>
                <input type="date" name="to" value="{{ $period['to'] }}" aria-label="To date">
                <button class="fc-date-submit" type="submit" aria-label="Apply date range">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                </button>
            </form>

            <a href="{{ $exportUrl }}" class="fc-primary-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 20h14"></path></svg>
                Export Report
            </a>
        </div>
    </section>

    <section class="fc-summary-grid">
        <article class="fc-stat-card">
            <div class="fc-stat-inner">
                <span class="fc-stat-icon rate">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 17 17 7"></path><circle cx="8" cy="8" r="2"></circle><circle cx="16" cy="16" r="2"></circle></svg>
                </span>
                <div>
                    <div class="fc-stat-label">Commission Rate</div>
                    <div class="fc-stat-row"><span class="fc-stat-value">{{ $formatRate($commissionRate) }}%</span></div>
                    <div class="fc-stat-help">Effective since {{ $currentRate->effective_from?->format('M j, Y') ?: '—' }}</div>
                </div>
            </div>
        </article>

        @foreach([
            ['key'=>'delivered_orders','label'=>'Delivered Orders','value'=>number_format((int) $stats['delivered_orders']),'icon'=>'orders'],
            ['key'=>'gross_sales','label'=>'Gross Sales','value'=>'₱'.number_format((float) $stats['gross_sales'],2),'icon'=>'sales'],
            ['key'=>'commission','label'=>'SARI Commission','value'=>'₱'.number_format((float) $stats['commission'],2),'icon'=>'earned'],
        ] as $card)
            @php $delta = $comparisons[$card['key']] ?? null; @endphp
            <article class="fc-stat-card">
                <div class="fc-stat-inner">
                    <span class="fc-stat-icon {{ $card['icon'] }}">
                        @if($card['icon'] === 'orders')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h2l1.4 8a2 2 0 0 0 2 1.7h7.8a2 2 0 0 0 1.9-1.4L21 9H7"></path><circle cx="10" cy="19" r="1.2"></circle><circle cx="18" cy="19" r="1.2"></circle></svg>
                        @elseif($card['icon'] === 'sales')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19h16"></path><path d="M6 16v-4h3v4M11 16V8h3v8M16 16V5h3v11"></path></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 7h10l2 3v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7l2-3Z"></path><path d="M9 7a3 3 0 0 1 6 0M12 10v5"></path></svg>
                        @endif
                    </span>
                    <div>
                        <div class="fc-stat-label">{{ $card['label'] }}</div>
                        <div class="fc-stat-row">
                            <span class="fc-stat-value">{{ $card['value'] }}</span>
                            @if($delta === null)
                                <span class="fc-delta neutral">No prior</span>
                            @elseif($delta > 0)
                                <span class="fc-delta positive">↗ +{{ number_format($delta,1) }}%</span>
                            @elseif($delta < 0)
                                <span class="fc-delta negative">↘ {{ number_format($delta,1) }}%</span>
                            @else
                                <span class="fc-delta neutral">0.0%</span>
                            @endif
                        </div>
                        <div class="fc-stat-help">{{ $period['comparison_label'] }}</div>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="fc-analytics-grid">
        <article class="fc-card fc-chart-panel">
            <div class="fc-panel-head">
                <div>
                    <h2 class="fc-panel-title">Commission Performance</h2>
                    <p class="fc-panel-copy">Daily platform commission earnings from paid, delivered orders.</p>
                </div>
                <nav class="fc-range-tabs" aria-label="Commission date ranges">
                    @foreach([7=>'7D',30=>'30D',90=>'90D',365=>'1Y'] as $rangeDays => $rangeLabel)
                        <a href="{{ $rangeUrl((string) $rangeDays) }}" class="fc-range-tab {{ $period['range'] === (string) $rangeDays ? 'active' : '' }}">{{ $rangeLabel }}</a>
                    @endforeach
                    <a href="#commissionFilterForm" class="fc-range-tab {{ $period['range'] === 'custom' ? 'active' : '' }}">Custom</a>
                </nav>
            </div>

            @if(collect($chartSeries)->isNotEmpty())
                <div class="fc-chart-wrap" id="fcChartWrap">
                    <svg id="fcChartSvg" class="fc-chart-svg" viewBox="0 0 1000 235" role="img" aria-label="Commission performance chart"></svg>
                    <div class="fc-chart-tooltip" id="fcChartTooltip"><small id="fcChartTooltipDate"></small><strong id="fcChartTooltipValue"></strong></div>
                </div>
            @else
                <div class="fc-chart-empty">
                    <div><strong style="color:var(--sari-dark-e23e64dd1e, #475467);">No commission activity in this period.</strong><br>Paid and delivered orders will appear here automatically.</div>
                </div>
            @endif
        </article>

        <article class="fc-card fc-policy-panel">
            <div class="fc-panel-head">
                <div>
                    <h2 class="fc-panel-title">Commission Rate &amp; Policy</h2>
                    <p class="fc-panel-copy">Current finance policy used for new eligible deliveries.</p>
                </div>
                <div class="fc-policy-head-actions">
                    <a href="{{ route('admin.platform-settings') }}" class="fc-mini-button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m14.7 6.3 3 3M4 20l3.8-.8L19 8a2.1 2.1 0 0 0-3-3L4.8 16.2 4 20Z"></path></svg>
                        Edit Rate
                    </a>
                </div>
            </div>

            <div class="fc-policy-body">
                <div class="fc-rate-donut" style="--rate-deg: {{ $rateDegrees }}deg">
                    <div class="fc-rate-center"><div class="fc-rate-value">{{ $formatRate($commissionRate) }}%</div><div class="fc-rate-label">Commission</div></div>
                </div>

                <div class="fc-policy-list">
                    <div class="fc-policy-item">
                        <span class="fc-policy-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg></span>
                        <div><div class="fc-policy-label">Effective Date</div><div class="fc-policy-value">{{ $currentRate->effective_from?->format('M j, Y') ?: '—' }}</div></div>
                    </div>
                    <div class="fc-policy-item">
                        <span class="fc-policy-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"></path><path d="m4 7.5 8 4.5 8-4.5M12 12v9"></path></svg></span>
                        <div><div class="fc-policy-label">Calculation Basis</div><div class="fc-policy-value">Delivered merchandise subtotal</div></div>
                    </div>
                    <div class="fc-policy-item">
                        <span class="fc-policy-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M8 12h8"></path></svg></span>
                        <div><div class="fc-policy-label">Excluded From Commission</div><div class="fc-policy-value">Delivery fee, cancelled orders, and non-delivered orders</div></div>
                    </div>
                    <div class="fc-policy-item">
                        <span class="fc-policy-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
                        <div><div class="fc-policy-label">Last Updated</div><div class="fc-policy-value">{{ $currentRate->updated_at?->format('M j, Y · h:i A') ?: '—' }}<br>{{ $currentRate->changedByAdmin?->name ? 'by '.$currentRate->changedByAdmin->name : 'by System' }}</div></div>
                    </div>
                </div>
            </div>

            <button type="button" class="fc-history-button" data-open-rate-history>
                <span>↺ &nbsp; View Rate History</span><span>›</span>
            </button>
        </article>
    </section>

    <form id="commissionFilterForm" method="GET" action="{{ route('admin.commissions') }}" class="fc-card fc-filter-card">
        <input type="hidden" name="range" value="custom">
        <div class="fc-filter-grid">
            <div class="fc-search">
                <label class="fc-filter-label" for="fcCommissionSearch">Search</label>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                <input id="fcCommissionSearch" class="fc-control" type="search" name="search" value="{{ $filters['search'] }}" placeholder="Search order number or seller...">
            </div>
            <div class="fc-custom-select" data-fc-dropdown>
                <span class="fc-filter-label" id="fcStatusLabel">Status</span>
                <input type="hidden" name="status" value="{{ $filters['status'] }}" data-fc-dropdown-input>
                <button
                    type="button"
                    class="fc-custom-select-trigger"
                    data-fc-dropdown-trigger
                    aria-labelledby="fcStatusLabel fcStatusValue"
                    aria-haspopup="listbox"
                    aria-expanded="false"
                >
                    <span class="fc-custom-select-main">
                        <span class="fc-custom-select-status-dot" aria-hidden="true"></span>
                        <span id="fcStatusValue" data-fc-dropdown-label>{{ $filters['status'] !== '' ? $formatStatus($filters['status']) : 'All Statuses' }}</span>
                    </span>
                    <svg class="fc-custom-select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m7 10 5 5 5-5"></path>
                    </svg>
                </button>

                <div class="fc-custom-select-menu" data-fc-dropdown-menu role="listbox" aria-label="Filter by commission status">
                    <button type="button" class="fc-custom-select-option {{ $filters['status'] === '' ? 'is-selected' : '' }}" data-fc-dropdown-option data-value="" data-label="All Statuses" role="option" aria-selected="{{ $filters['status'] === '' ? 'true' : 'false' }}">
                        <span class="fc-custom-select-option-main"><span class="fc-option-dot neutral"></span><span>All Statuses</span></span>
                        <svg viewBox="0 0 24 24" class="fc-custom-select-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg>
                    </button>
                    @foreach($statusOptions as $statusOption)
                        <button type="button" class="fc-custom-select-option {{ $filters['status'] === $statusOption ? 'is-selected' : '' }}" data-fc-dropdown-option data-value="{{ $statusOption }}" data-label="{{ $formatStatus($statusOption) }}" role="option" aria-selected="{{ $filters['status'] === $statusOption ? 'true' : 'false' }}">
                            <span class="fc-custom-select-option-main"><span class="fc-option-dot status"></span><span>{{ $formatStatus($statusOption) }}</span></span>
                            <svg viewBox="0 0 24 24" class="fc-custom-select-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg>
                        </button>
                    @endforeach
                </div>
            </div>
            @php
                $selectedSeller = collect($sellers)->first(fn ($seller) => (int) $seller->id === (int) $filters['seller']);
                $selectedSellerLabel = $selectedSeller
                    ? ($selectedSeller->store_name ?: 'SARI Seller #'.$selectedSeller->id)
                    : 'All Sellers';
            @endphp
            <div class="fc-custom-select" data-fc-dropdown>
                <span class="fc-filter-label" id="fcSellerLabel">Seller</span>
                <input type="hidden" name="seller" value="{{ $filters['seller'] ?: '' }}" data-fc-dropdown-input>
                <button
                    type="button"
                    class="fc-custom-select-trigger"
                    data-fc-dropdown-trigger
                    aria-labelledby="fcSellerLabel fcSellerValue"
                    aria-haspopup="listbox"
                    aria-expanded="false"
                >
                    <span class="fc-custom-select-main">
                        <span class="fc-custom-select-user-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="8" r="3"></circle>
                                <path d="M5 20a7 7 0 0 1 14 0"></path>
                            </svg>
                        </span>
                        <span id="fcSellerValue" data-fc-dropdown-label>{{ $selectedSellerLabel }}</span>
                    </span>
                    <svg class="fc-custom-select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m7 10 5 5 5-5"></path>
                    </svg>
                </button>

                <div class="fc-custom-select-menu" data-fc-dropdown-menu role="listbox" aria-label="Filter by seller">
                    <button type="button" class="fc-custom-select-option {{ (int) $filters['seller'] === 0 ? 'is-selected' : '' }}" data-fc-dropdown-option data-value="" data-label="All Sellers" role="option" aria-selected="{{ (int) $filters['seller'] === 0 ? 'true' : 'false' }}">
                        <span class="fc-custom-select-option-main">
                            <span class="fc-option-user-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3"></circle><path d="M5 20a7 7 0 0 1 14 0"></path></svg>
                            </span>
                            <span>All Sellers</span>
                        </span>
                        <svg viewBox="0 0 24 24" class="fc-custom-select-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg>
                    </button>

                    @foreach($sellers as $seller)
                        @php $sellerLabel = $seller->store_name ?: 'SARI Seller #'.$seller->id; @endphp
                        <button type="button" class="fc-custom-select-option {{ (int) $filters['seller'] === (int) $seller->id ? 'is-selected' : '' }}" data-fc-dropdown-option data-value="{{ $seller->id }}" data-label="{{ $sellerLabel }}" role="option" aria-selected="{{ (int) $filters['seller'] === (int) $seller->id ? 'true' : 'false' }}">
                            <span class="fc-custom-select-option-main">
                                <span class="fc-option-user-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3"></circle><path d="M5 20a7 7 0 0 1 14 0"></path></svg>
                                </span>
                                <span>{{ $sellerLabel }}</span>
                            </span>
                            <svg viewBox="0 0 24 24" class="fc-custom-select-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="fc-filter-dates">
                <label class="fc-date-field"><span class="fc-filter-label">From</span><input class="fc-control" type="date" name="from" value="{{ $period['from'] }}" aria-label="Filter from date"></label>
                <label class="fc-date-field"><span class="fc-filter-label">To</span><input class="fc-control" type="date" name="to" value="{{ $period['to'] }}" aria-label="Filter to date"></label>
            </div>

            <a href="{{ $resetUrl }}" class="fc-filter-reset">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M4 4v6h6"></path><path d="M5.5 15a7 7 0 1 0 1.2-7.7L4 10"></path></svg>
                Reset
            </a>
            <button type="submit" class="fc-filter-apply">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>
                Apply Filter
            </button>
        </div>
    </form>

    <section class="fc-card fc-ledger">
        <div class="fc-section-head">
            <div>
                <h2 class="fc-panel-title">Commission Ledger</h2>
                <p class="fc-panel-copy">Detailed list of paid, delivered orders and their snapshotted commission calculations.</p>
            </div>
            <div class="fc-ledger-tools">
                <div class="fc-selection-summary" id="fcSelectionSummary" hidden>
                    <span id="fcSelectionCount" role="status" aria-live="polite"></span>
                    <button type="button" class="fc-selection-clear" id="fcClearSelection">Clear selection</button>
                </div>
            <a href="{{ $exportUrl }}" class="fc-link-button" style="height:36px;padding:0 12px;font-size:9.5px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 20h14"></path></svg>
                Export CSV
            </a>
            </div>
        </div>

        <div class="fc-table-wrap">
            <table class="fc-table">
                <thead>
                    <tr>
                        <th class="fc-check-cell"><input type="checkbox" class="fc-checkbox" id="fcSelectAll" aria-label="Select all transactions on this page"></th><th>#</th><th>Order</th><th>Seller</th><th>Eligible Amount</th><th>Rate</th><th>Commission</th><th>Status</th><th>Delivered At</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $statusClass = in_array($row['status'], ['earned','adjusted','reversed'], true) ? $row['status'] : 'earned';
                            $detailPayload = [
                                'order' => $row['order']->order_number,
                                'seller' => $row['seller'],
                                'eligible' => '₱'.number_format($row['subtotal'],2),
                                'rate' => $formatRate($row['rate']).'%',
                                'gross' => '₱'.number_format($row['gross_commission'],2),
                                'adjustment' => '₱'.number_format($row['adjustment_total'],2),
                                'net' => '₱'.number_format($row['commission'],2),
                                'status' => $formatStatus($row['status']),
                                'payment' => $formatStatus($row['payment_status']),
                                'delivery_fee' => '₱'.number_format($row['delivery_fee'],2),
                                'seller_net' => $row['seller_net'] !== null ? '₱'.number_format($row['seller_net'],2) : 'Not available',
                                'delivered' => $row['order']->delivered_at?->format('M j, Y · h:i A') ?: '—',
                            ];
                        @endphp
                        <tr>
                            <td class="fc-check-cell"><input type="checkbox" class="fc-checkbox" data-fc-row-select aria-label="Select order {{ $row['order']->order_number }}"></td>
                            <td>{{ ($rows->firstItem() ?? 1) + $loop->index }}</td>
                            <td><span class="fc-order">{{ $row['order']->order_number }}</span></td>
                            <td>{{ $row['seller'] }}</td>
                            <td><span class="fc-money">₱{{ number_format($row['subtotal'],2) }}</span></td>
                            <td>{{ $formatRate($row['rate']) }}%</td>
                            <td><span class="fc-money">₱{{ number_format($row['commission'],2) }}</span></td>
                            <td><span class="fc-status {{ $statusClass }}">{{ $formatStatus($row['status']) }}</span></td>
                            <td>{{ $row['order']->delivered_at?->format('M j, Y') ?: '—' }}</td>
                            <td><button type="button" class="fc-view-button" aria-label="View commission for order {{ $row['order']->order_number }}" title="View commission" data-view-commission='@json($detailPayload)'>View</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="10"><div class="fc-empty">No commission records match the selected period and filters.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="fc-table-footer">
            <div>Showing {{ $rows->firstItem() ?? 0 }} to {{ $rows->lastItem() ?? 0 }} of {{ number_format($rows->total()) }} transactions</div>
            @if($rows->lastPage() > 1)
                <nav class="fc-pagination" aria-label="Commission ledger pages">
                    <a class="fc-page-link {{ $rows->onFirstPage() ? 'disabled' : '' }}" href="{{ $rows->previousPageUrl() ?: '#' }}">‹</a>
                    @php
                        $startPage = max(1, $rows->currentPage() - 2);
                        $endPage = min($rows->lastPage(), $rows->currentPage() + 2);
                    @endphp
                    @for($page = $startPage; $page <= $endPage; $page++)
                        <a class="fc-page-link {{ $page === $rows->currentPage() ? 'active' : '' }}" href="{{ $rows->url($page) }}">{{ $page }}</a>
                    @endfor
                    <a class="fc-page-link {{ $rows->hasMorePages() ? '' : 'disabled' }}" href="{{ $rows->nextPageUrl() ?: '#' }}">›</a>
                </nav>
            @endif
        </div>
    </section>

    <section class="fc-bottom-grid">
        <article class="fc-card fc-bottom-card">
            <div class="fc-bottom-title-row">
                <div><h2 class="fc-panel-title">Top Sellers by Commission</h2><p class="fc-panel-copy">Sellers with the highest platform commission in this period.</p></div>
                <a href="#commissionFilterForm" class="fc-mini-button">View All</a>
            </div>
            @if(collect($topSellers)->isNotEmpty())
                <div class="fc-top-sellers">
                    @foreach($topSellers as $seller)
                        <div class="fc-seller-row">
                            <span class="fc-rank">{{ $loop->iteration }}</span>
                            <span class="fc-seller-name" title="{{ $seller['name'] }}">{{ $seller['name'] }}</span>
                            <span class="fc-progress"><span style="width:{{ max(0,min(100,$seller['bar'])) }}%"></span></span>
                            <span class="fc-seller-amount">₱{{ number_format($seller['commission'],2) }} <span class="fc-seller-share">({{ number_format($seller['share'],1) }}%)</span></span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="fc-empty">No seller commission data for this period.</div>
            @endif
        </article>

        <article class="fc-card fc-bottom-card">
            <div><h2 class="fc-panel-title">Order Status Breakdown</h2><p class="fc-panel-copy">Distribution of marketplace orders created in the selected period.</p></div>
            <div class="fc-status-layout">
                <div class="fc-status-donut" style="--status-gradient: conic-gradient({{ implode(', ', $statusGradient) }})">
                    <div class="fc-status-center"><div class="fc-status-total">{{ number_format($statusTotal) }}</div><div class="fc-status-caption">Total Orders</div></div>
                </div>
                <div class="fc-status-legend">
                    @foreach($statusBreakdown as $statusItem)
                        @php $statusPercent = $statusTotal > 0 ? (($statusItem['count'] / $statusTotal) * 100) : 0; @endphp
                        <div class="fc-status-legend-row">
                            <span class="fc-status-dot" style="background:{{ $statusItem['color'] }}"></span>
                            <span>{{ $statusItem['label'] }}</span>
                            <span class="fc-status-count">{{ number_format($statusItem['count']) }}</span>
                            <span class="fc-status-percent">{{ number_format($statusPercent,1) }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="fc-card fc-bottom-card">
            <div class="fc-bottom-title-row">
                <div><h2 class="fc-panel-title">Recent Commission Transactions</h2><p class="fc-panel-copy">Latest paid and delivered commission entries.</p></div>
                <a href="#commissionFilterForm" class="fc-mini-button">View All</a>
            </div>
            @if(collect($recentTransactions)->isNotEmpty())
                <div class="fc-recent-list">
                    @foreach($recentTransactions as $transaction)
                        <div class="fc-recent-row">
                            <span class="fc-recent-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h2l1.4 8a2 2 0 0 0 2 1.7h7.8a2 2 0 0 0 1.9-1.4L21 9H7"></path></svg></span>
                            <span class="fc-recent-order" title="{{ $transaction['order_number'] }}">{{ $transaction['order_number'] }}</span>
                            <span class="fc-recent-seller" title="{{ $transaction['seller'] }}">{{ $transaction['seller'] }}</span>
                            <span class="fc-recent-amount">₱{{ number_format($transaction['commission'],2) }}</span>
                            <span class="fc-recent-date">{{ $transaction['earned_at']?->format('M j, Y') ?: '—' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="fc-empty">No recent commission transactions in this period.</div>
            @endif
        </article>
    </section>

    {{-- Existing rider payout operations are preserved below the commission dashboard so this redesign does not remove working finance actions. --}}
    <section class="fc-card fc-payout-panel">
        <div class="fc-section-head">
            <div>
                <h2 class="fc-panel-title">Rider Payout Operations</h2>
                <p class="fc-panel-copy">Existing payout workflow retained while the dedicated Rider Payouts page is being separated.</p>
            </div>
            <div class="fc-payout-summary">
                <span class="fc-chip pending">Pending {{ $pendingPayouts }}</span>
                <span class="fc-chip approved">Approved {{ $approvedPayouts }}</span>
                <span class="fc-chip paid">Paid {{ $paidPayouts }}</span>
            </div>
        </div>

        @forelse($payoutRequests as $payout)
            @php
                $riderName = trim(($payout->courier?->first_name ?? '').' '.($payout->courier?->last_name ?? '')) ?: 'Rider';
                $payoutStatus = strtolower((string) $payout->status);
            @endphp
            <div class="fc-payout-row">
                <div><div class="fc-payout-name">{{ $riderName }}</div><div class="fc-payout-meta">{{ $payout->created_at?->format('M j, Y · h:i A') ?: '—' }}</div></div>
                <div class="fc-payout-amount">₱{{ number_format((float) $payout->amount,2) }}</div>
                <div><span class="fc-status {{ $payoutStatus === 'paid' ? 'earned' : ($payoutStatus === 'rejected' ? 'reversed' : 'adjusted') }}">{{ ucfirst($payoutStatus) }}</span></div>
                <div class="fc-payout-actions">
                    @if($payoutStatus === 'pending')
                        <form method="POST" action="{{ route('admin.commissions.payouts.approve', $payout) }}">@csrf<button class="fc-action">Approve</button></form>
                        <form method="POST" action="{{ route('admin.commissions.payouts.reject', $payout) }}">@csrf<input class="fc-payout-input" name="admin_note" required placeholder="Reason for rejection"><button class="fc-action">Reject</button></form>
                    @elseif($payoutStatus === 'approved')
                        <form method="POST" action="{{ route('admin.commissions.payouts.paid', $payout) }}">@csrf<button class="fc-action">Mark Paid</button></form>
                    @else
                        <span class="fc-payout-meta">No action required</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="fc-empty">No rider payout requests yet.</div>
        @endforelse
    </section>
</div>

<div class="fc-modal-backdrop" id="commissionDetailModal" aria-hidden="true">
    <div class="fc-modal" role="dialog" aria-modal="true" aria-labelledby="commissionDetailTitle">
        <div class="fc-modal-head"><div><div class="fc-modal-title" id="commissionDetailTitle">Commission Transaction</div><div class="fc-panel-copy" id="commissionDetailSubtitle"></div></div><button type="button" class="fc-modal-close" data-close-modal>×</button></div>
        <div class="fc-modal-body"><div class="fc-detail-grid" id="commissionDetailGrid"></div></div>
    </div>
</div>

<div class="fc-modal-backdrop" id="rateHistoryModal" aria-hidden="true">
    <div class="fc-modal" role="dialog" aria-modal="true" aria-labelledby="rateHistoryTitle">
        <div class="fc-modal-head"><div><div class="fc-modal-title" id="rateHistoryTitle">Commission Rate History</div><div class="fc-panel-copy">Historical rates remain immutable for already-earned commissions.</div></div><button type="button" class="fc-modal-close" data-close-modal>×</button></div>
        <div class="fc-modal-body">
            <div class="fc-history-list">
                @forelse($rateHistory as $history)
                    <div class="fc-history-row">
                        <div class="fc-history-rate">{{ $formatRate($history->rate_percent) }}%</div>
                        <div class="fc-history-copy">{{ $history->reason ?: 'Commission rate policy entry.' }}<br>{{ $history->changedByAdmin?->name ? 'Changed by '.$history->changedByAdmin->name : 'System record' }}</div>
                        <div class="fc-history-date">From {{ $history->effective_from?->format('M j, Y') ?: '—' }}<br>{{ $history->effective_until ? 'Until '.$history->effective_until->format('M j, Y') : 'Current' }}</div>
                    </div>
                @empty
                    <div class="fc-empty">No rate history records are available yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const dropdowns = Array.from(document.querySelectorAll('[data-fc-dropdown]'));

    function closeFinanceDropdowns(except = null) {
        dropdowns.forEach(dropdown => {
            if (dropdown === except) return;
            dropdown.classList.remove('is-open');
            dropdown.querySelector('[data-fc-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
        });
    }

    document.addEventListener('click', event => {
        const trigger = event.target.closest('[data-fc-dropdown-trigger]');
        if (trigger) {
            event.stopPropagation();

            const dropdown = trigger.closest('[data-fc-dropdown]');
            const willOpen = !dropdown?.classList.contains('is-open');

            closeFinanceDropdowns(dropdown);
            dropdown?.classList.toggle('is-open', willOpen);
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            return;
        }

        const option = event.target.closest('[data-fc-dropdown-option]');
        if (option) {
            const dropdown = option.closest('[data-fc-dropdown]');
            const input = dropdown?.querySelector('[data-fc-dropdown-input]');
            const label = dropdown?.querySelector('[data-fc-dropdown-label]');
            const dropdownTrigger = dropdown?.querySelector('[data-fc-dropdown-trigger]');
            const options = Array.from(dropdown?.querySelectorAll('[data-fc-dropdown-option]') || []);

            const value = option.dataset.value ?? '';
            const optionLabel = option.dataset.label ?? option.textContent.trim();

            if (input) input.value = value;
            if (label) label.textContent = optionLabel;

            options.forEach(item => {
                const selected = item === option;
                item.classList.toggle('is-selected', selected);
                item.setAttribute('aria-selected', selected ? 'true' : 'false');
            });

            dropdown?.classList.remove('is-open');
            dropdownTrigger?.setAttribute('aria-expanded', 'false');
            dropdownTrigger?.focus();
            return;
        }

        if (!event.target.closest('[data-fc-dropdown]')) {
            closeFinanceDropdowns();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeFinanceDropdowns();
    });

    const money = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
    const rawSeries = @json($chartSeries);
    const periodFrom = @json($period['from']);
    const periodTo = @json($period['to']);

    const svg = document.getElementById('fcChartSvg');
    const wrap = document.getElementById('fcChartWrap');
    const tooltip = document.getElementById('fcChartTooltip');
    const tooltipDate = document.getElementById('fcChartTooltipDate');
    const tooltipValue = document.getElementById('fcChartTooltipValue');

    function parseLocalDate(text) {
        const [y,m,d] = String(text || '').split('-').map(Number);
        return new Date(y, (m || 1) - 1, d || 1, 12, 0, 0);
    }

    function keyFor(date) {
        return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    }

    function addDays(date, count) {
        const next = new Date(date);
        next.setDate(next.getDate() + count);
        return next;
    }

    function svgNode(name, attrs = {}) {
        const node = document.createElementNS('http://www.w3.org/2000/svg', name);
        Object.entries(attrs).forEach(([key,value]) => node.setAttribute(key, value));
        return node;
    }

    function niceMax(value) {
        const v = Math.max(1, Number(value || 0));
        const magnitude = 10 ** Math.floor(Math.log10(v));
        const normalized = v / magnitude;
        const nice = normalized <= 1 ? 1 : normalized <= 2 ? 2 : normalized <= 5 ? 5 : 10;
        return nice * magnitude;
    }

    function smoothPath(points) {
        if (!points.length) return '';
        if (points.length === 1) return `M ${points[0].x} ${points[0].y}`;
        let path = `M ${points[0].x} ${points[0].y}`;
        for (let i = 0; i < points.length - 1; i++) {
            const p0 = points[i - 1] || points[i];
            const p1 = points[i];
            const p2 = points[i + 1];
            const p3 = points[i + 2] || p2;
            path += ` C ${p1.x + (p2.x-p0.x)/6} ${p1.y + (p2.y-p0.y)/6}, ${p2.x - (p3.x-p1.x)/6} ${p2.y - (p3.y-p1.y)/6}, ${p2.x} ${p2.y}`;
        }
        return path;
    }

    function renderChart() {
        if (!svg || !wrap || !Array.isArray(rawSeries) || rawSeries.length === 0) return;

        const grouped = new Map(rawSeries.map(item => [String(item.date), Number(item.commission || 0)]));
        const start = parseLocalDate(periodFrom);
        const end = parseLocalDate(periodTo);
        const series = [];
        for (let cursor = new Date(start); cursor <= end; cursor = addDays(cursor, 1)) {
            const key = keyFor(cursor);
            series.push({ date: new Date(cursor), value: grouped.get(key) || 0 });
        }

        const width = 1000, height = 260;
        const margin = { top: 24, right: 20, bottom: 38, left: 72 };
        const iw = width - margin.left - margin.right;
        const ih = height - margin.top - margin.bottom;
        const maxValue = niceMax(Math.max(...series.map(item => item.value), 1));
        const x = index => margin.left + (series.length <= 1 ? 0 : (index / (series.length - 1)) * iw);
        const y = value => margin.top + ih - (Number(value || 0) / maxValue) * ih;
        const points = series.map((item,index) => ({...item, x:x(index), y:y(item.value)}));
        const line = smoothPath(points);
        const area = `${line} L ${points[points.length-1].x} ${margin.top+ih} L ${points[0].x} ${margin.top+ih} Z`;

        svg.replaceChildren();

        for (let i=0; i<=4; i++) {
            const ratio = i/4;
            const yy = margin.top + ih - ratio*ih;
            const value = maxValue*ratio;
            svg.appendChild(svgNode('line',{x1:margin.left,y1:yy,x2:width-margin.right,y2:yy,stroke:'#edf0f3','stroke-width':'1'}));
            const label = svgNode('text',{x:margin.left-12,y:yy+4,fill:'#667085','font-size':'11','font-weight':'600','font-family':'Poppins, sans-serif','text-anchor':'end'});
            label.textContent = `₱${Number(value).toLocaleString('en-PH',{maximumFractionDigits:0})}`;
            svg.appendChild(label);
        }

        const labelCount = Math.min(6, series.length);
        const used = new Set();
        for (let i=0; i<labelCount; i++) {
            const index = labelCount === 1 ? 0 : Math.round((i/(labelCount-1))*(series.length-1));
            if (used.has(index)) continue;
            used.add(index);
            const point = points[index];
            const text = svgNode('text',{x:point.x,y:height-10,fill:'#667085','font-size':'10.5','font-weight':'500','font-family':'Poppins, sans-serif','text-anchor':index===0?'start':index===series.length-1?'end':'middle'});
            text.textContent = point.date.toLocaleDateString('en-PH',{month:'short',day:'numeric'});
            svg.appendChild(text);
        }

        svg.appendChild(svgNode('path',{d:area,fill:'#d99a00','fill-opacity':'.10',stroke:'none'}));
        svg.appendChild(svgNode('path',{d:line,fill:'none',stroke:'#d99a00','stroke-width':'2.7','stroke-linecap':'round','stroke-linejoin':'round'}));

        const hoverLine = svgNode('line',{x1:margin.left,y1:margin.top,x2:margin.left,y2:margin.top+ih,stroke:'#d99a00','stroke-width':'1','stroke-dasharray':'3 4',opacity:'0'});
        const hoverDot = svgNode('circle',{cx:margin.left,cy:margin.top+ih,r:'5.5',fill:'#fff',stroke:'#d99a00','stroke-width':'2.5',opacity:'0'});
        const target = svgNode('rect',{x:margin.left,y:margin.top,width:iw,height:ih,fill:'transparent',style:'cursor:crosshair'});
        svg.appendChild(hoverLine); svg.appendChild(hoverDot); svg.appendChild(target);

        const show = point => {
            hoverLine.setAttribute('x1',point.x); hoverLine.setAttribute('x2',point.x); hoverLine.setAttribute('opacity','1');
            hoverDot.setAttribute('cx',point.x); hoverDot.setAttribute('cy',point.y); hoverDot.setAttribute('opacity','1');
            if (tooltip && tooltipDate && tooltipValue) {
                tooltipDate.textContent = point.date.toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});
                tooltipValue.textContent = money.format(point.value);
                tooltip.style.left = `${(point.x/width)*100}%`;
                tooltip.style.top = `${(point.y/height)*100}%`;
                tooltip.classList.toggle('below', point.y < 58);
                tooltip.classList.add('show');
            }
        };

        let hoverFrame = 0;
        let chartRect = null;
        let latestPointerX = 0;

        target.addEventListener('mouseenter', () => {
            chartRect = svg.getBoundingClientRect();
        }, { passive: true });

        target.addEventListener('mousemove', event => {
            latestPointerX = event.clientX;

            if (hoverFrame) return;

            hoverFrame = window.requestAnimationFrame(() => {
                hoverFrame = 0;

                const rect = chartRect || svg.getBoundingClientRect();
                const relativeX = ((latestPointerX - rect.left) / rect.width) * width;
                const normalized = Math.max(0, Math.min(1, (relativeX - margin.left) / iw));
                const index = Math.round(normalized * (points.length - 1));

                show(points[index]);
            });
        }, { passive: true });

        target.addEventListener('mouseleave',()=>{
            chartRect = null;

            if (hoverFrame) {
                window.cancelAnimationFrame(hoverFrame);
                hoverFrame = 0;
            }

            hoverLine.setAttribute('opacity','0');
            hoverDot.setAttribute('opacity','0');
            tooltip?.classList.remove('show','below');
        }, { passive: true });

        const latest = [...points].reverse().find(point => point.value > 0);
        if (latest) show(latest);
    }

    // Performance: let the first paint complete before building the SVG chart.
    const scheduleChartRender = window.requestIdleCallback
        ? callback => window.requestIdleCallback(callback, { timeout: 350 })
        : callback => window.setTimeout(callback, 0);

    scheduleChartRender(renderChart);

    const detailModal = document.getElementById('commissionDetailModal');
    const detailGrid = document.getElementById('commissionDetailGrid');
    const detailSubtitle = document.getElementById('commissionDetailSubtitle');
    const rateHistoryModal = document.getElementById('rateHistoryModal');

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden','false');
        document.body.style.overflow = 'hidden';

        window.requestAnimationFrame(() => {
            modal.querySelector('[data-close-modal]')?.focus({ preventScroll: true });
        });
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden','true');
        if (!document.querySelector('.fc-modal-backdrop.open')) document.body.style.overflow = '';
    }

    document.addEventListener('click', event => {
        const commissionButton = event.target.closest('[data-view-commission]');
        if (commissionButton) {
            try {
                const data = JSON.parse(commissionButton.getAttribute('data-view-commission') || '{}');

                if (detailSubtitle) {
                    detailSubtitle.textContent = `${data.order || ''} · ${data.seller || ''}`;
                }

                const fields = [
                    ['Eligible amount',data.eligible],
                    ['Applied rate',data.rate],
                    ['Gross commission',data.gross],
                    ['Adjustment total',data.adjustment],
                    ['Net commission',data.net],
                    ['Commission status',data.status],
                    ['Payment status',data.payment],
                    ['Delivery fee',data.delivery_fee],
                    ['Seller net payable',data.seller_net],
                    ['Delivered at',data.delivered]
                ];

                if (detailGrid) {
                    detailGrid.innerHTML = fields.map(([label,value]) =>
                        `<div class="fc-detail-box"><div class="fc-detail-label">${label}</div><div class="fc-detail-value">${String(value ?? '—').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]))}</div></div>`
                    ).join('');
                }

                openModal(detailModal);
            } catch (error) {
                console.error(error);
            }

            return;
        }

        if (event.target.closest('[data-open-rate-history]')) {
            openModal(rateHistoryModal);
            return;
        }

        const closeButton = event.target.closest('[data-close-modal]');
        if (closeButton) {
            closeModal(closeButton.closest('.fc-modal-backdrop'));
            return;
        }

        const backdrop = event.target.closest('.fc-modal-backdrop');
        if (backdrop && event.target === backdrop) {
            closeModal(backdrop);
        }
    });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') document.querySelectorAll('.fc-modal-backdrop.open').forEach(closeModal); });
})();
</script>

<script>
(function () {
    // Local selection covers only the transactions rendered on this page.
    const selectAll = document.getElementById('fcSelectAll');
    const checkboxes = Array.from(document.querySelectorAll('[data-fc-row-select]'));
    const summary = document.getElementById('fcSelectionSummary');
    const count = document.getElementById('fcSelectionCount');
    const clear = document.getElementById('fcClearSelection');

    function syncCommissionSelection() {
        const selected = checkboxes.filter(checkbox => checkbox.checked).length;
        checkboxes.forEach(checkbox => {
            checkbox.closest('tr')?.classList.toggle('fc-row-selected', checkbox.checked);
        });
        if (selectAll) {
            selectAll.disabled = checkboxes.length === 0;
            selectAll.checked = selected > 0 && selected === checkboxes.length;
            selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
        }
        if (summary) summary.hidden = selected === 0;
        if (count) count.textContent = `${selected} selected on this page`;
    }

    selectAll?.addEventListener('change', () => {
        checkboxes.forEach(checkbox => { checkbox.checked = selectAll.checked; });
        syncCommissionSelection();
    });
    checkboxes.forEach(checkbox => checkbox.addEventListener('change', syncCommissionSelection));
    clear?.addEventListener('click', () => {
        checkboxes.forEach(checkbox => { checkbox.checked = false; });
        syncCommissionSelection();
        selectAll?.focus();
    });
    syncCommissionSelection();
})();
</script>
@endsection
