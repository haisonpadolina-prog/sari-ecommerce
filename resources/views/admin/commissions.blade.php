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
        --fc-ink: #26211c;
        --fc-text: #514a42;
        --fc-muted: #8d8479;
        --fc-line: #e8e1d8;
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
        background: #fff;
        box-shadow: 0 6px 20px rgba(61,43,22,.045);
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
        border: 1px solid #eadfc9;
        border-radius: 10px;
        background: #fff8eb;
        color: #b77c18;
        box-shadow: 0 4px 12px rgba(75,54,25,.045);
    }

    .fc-title-icon svg {
        width: 15px;
        height: 15px;
    }

    .fc-eyebrow {
        color: #9a7b43;
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
        color: #17130f;
    }

    .fc-title-gold {
        color: var(--fc-brand);
    }

    .fc-subtitle {
        max-width: 760px;
        margin-top: 5px;
        color: #81786c;
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
        border: 1px solid #e5ddd2;
        border-radius: 9px;
        background: #fff;
        padding: 0 7px 0 9px;
        box-shadow: none;
    }

    .fc-date-control svg {
        width: 13px;
        height: 13px;
        color: #8d8479;
    }

    .fc-date-control input {
        width: 104px;
        border: 0;
        outline: 0;
        background: transparent;
        color: #514a42;
        font: inherit;
        font-size: 7.8px;
    }

    .fc-date-arrow {
        color: #aaa196;
        font-size: 8px;
    }

    .fc-date-submit {
        display: grid;
        width: 24px;
        height: 24px;
        place-items: center;
        border: 0;
        border-radius: 7px;
        background: #faf8f4;
        color: #756d63;
        cursor: pointer;
    }

    .fc-date-submit:hover {
        background: #fff4db;
        color: #a56d00;
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
        box-shadow: 0 4px 10px rgba(217,149,0,.11);
    }

    .fc-primary-button:hover {
        background: var(--fc-brand-strong);
        border-color: var(--fc-brand-strong);
        transform: translateY(-1px);
    }

    .fc-secondary-button,
    .fc-link-button {
        border: 1px solid #e5ddd2;
        background: #fff;
        color: #6f665b;
    }

    .fc-secondary-button:hover,
    .fc-link-button:hover {
        border-color: #d4c5b4;
        background: #faf8f4;
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
        background: #fff;
        box-shadow: 0 6px 18px rgba(61,43,22,.045);
        contain: paint;
    }

    .fc-stat-card:hover {
        border-color: #ddcfbb;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(61,43,22,.06);
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

    .fc-stat-icon.rate { background: #fff7e7; color: #c48612; }
    .fc-stat-icon.orders { background: #eef6fb; color: #4f7fa8; }
    .fc-stat-icon.sales { background: #f5f2fb; color: #7566a8; }
    .fc-stat-icon.earned { background: #eef7f1; color: #4f8060; }

    .fc-stat-label {
        color: #8e857a;
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
        color: #28221b;
        font-size: 19px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .fc-stat-help {
        margin-top: 6px;
        color: #9b9288;
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

    .fc-delta.positive { background: #eef8f1; color: #4f8060; }
    .fc-delta.negative { background: #fff2ef; color: #a65d5d; }
    .fc-delta.neutral { background: #f4f2ef; color: #81786d; }

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
        color: #302a24;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.35;
    }

    .fc-panel-copy {
        margin-top: 3px;
        color: #91887d;
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
        border: 1px solid #e7e1d9;
        border-radius: 7px;
        background: #fff;
        padding: 0 7px;
        color: #81786d;
        font-size: 7px;
        font-weight: 600;
        text-decoration: none;
    }

    .fc-range-tab:hover {
        background: #faf8f4;
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
        border: 1px solid #e1dad1;
        border-radius: 9px;
        background: rgba(255,255,255,.99);
        padding: 7px 9px;
        box-shadow: 0 8px 20px rgba(31,24,17,.11);
        opacity: 0;
    }

    .fc-chart-tooltip.show { opacity: 1; }
    .fc-chart-tooltip.below { transform: translate(-50%, 12px); }

    .fc-chart-tooltip small {
        display: block;
        color: #81786d;
        font-size: 7.5px;
        font-weight: 500;
    }

    .fc-chart-tooltip strong {
        display: block;
        margin-top: 3px;
        color: #302a24;
        font-size: 10px;
        font-weight: 700;
    }

    .fc-chart-empty {
        display: grid;
        height: 205px;
        place-items: center;
        color: #91887d;
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
        border: 1px solid #eadbbd;
        border-radius: 8px;
        background: #fff;
        padding: 0 8px;
        color: #9a6810;
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
        background: conic-gradient(var(--fc-brand) 0deg var(--rate-deg), #efede9 var(--rate-deg) 360deg);
    }

    .fc-rate-donut::before {
        content: '';
        position: absolute;
        width: 86px;
        height: 86px;
        border-radius: 50%;
        background: #fff;
        box-shadow: inset 0 0 0 1px #eee8df;
    }

    .fc-rate-center {
        position: relative;
        text-align: center;
    }

    .fc-rate-value {
        color: #302a24;
        font-size: 19px;
        font-weight: 700;
        line-height: 1;
    }

    .fc-rate-label {
        margin-top: 4px;
        color: #91887d;
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
        color: #8d8479;
    }

    .fc-policy-item-icon svg {
        width: 12px;
        height: 12px;
    }

    .fc-policy-label {
        color: #978e83;
        font-size: 6.3px;
    }

    .fc-policy-value {
        margin-top: 2px;
        color: #514a42;
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
        border: 1px solid #e5ddd2;
        border-radius: 8px;
        background: #fff;
        padding: 0 9px;
        color: #6f665b;
        font-size: 7px;
        cursor: pointer;
    }

    .fc-history-button:hover {
        background: #faf8f4;
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
        color: #9b9287;
        pointer-events: none;
        transform: translateY(-50%);
    }

    .fc-control,
    .fc-custom-select-trigger {
        width: 100%;
        height: 38px;
        border: 1px solid #e5ddd2;
        border-radius: 9px;
        background: #fff;
        color: #3d3730;
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
        box-shadow: 0 0 0 3px rgba(217,149,0,.075);
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
        border: 1px solid #e5ddd2;
        background: #fff;
        color: #6f665b;
    }

    .fc-filter-reset:hover {
        border-color: #d4c5b4;
        background: #faf8f4;
    }

    .fc-filter-apply {
        border: 1px solid var(--fc-brand);
        background: var(--fc-brand);
        color: #fff;
        box-shadow: 0 4px 10px rgba(217,149,0,.10);
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
        color: #9a6c18;
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
        color: #857b70;
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
        border: 1px solid #e4dcd1;
        border-radius: 10px;
        background: #fff;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(47,37,25,.12);
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
        color: #5b534a;
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
        background: #fff7e8;
        color: #9a6810;
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
        border-bottom: 1px solid #eee8df;
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
        background: #faf9f6;
    }

    .fc-table th {
        padding: 9px 11px;
        border-bottom: 1px solid #eee8df;
        color: #81786d;
        font-size: 7.5px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .fc-table td {
        padding: 9px 11px;
        border-bottom: 1px solid #f0ebe4;
        color: #5b534a;
        font-size: 8px;
        vertical-align: middle;
    }

    .fc-table tbody tr {
        content-visibility: auto;
        contain-intrinsic-size: 46px;
    }

    .fc-table tbody tr:hover {
        background: #fdfbf8;
    }

    .fc-order,
    .fc-money {
        color: #302a24;
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

    .fc-status.earned { background: #eef8f1; color: #4f8060; }
    .fc-status.adjusted { background: #fff7e8; color: #9a6810; }
    .fc-status.reversed { background: #fff2ef; color: #a65d5d; }

    .fc-view-button {
        display: inline-flex;
        height: 28px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e3dcd3;
        border-radius: 7px;
        background: #fff;
        padding: 0 9px;
        color: #514a42;
        font-size: 7.3px;
        font-weight: 600;
        cursor: pointer;
    }

    .fc-view-button:hover {
        border-color: #d6c7b4;
        background: #fffaf1;
        color: #9a6810;
    }

    .fc-table-footer {
        display: flex;
        min-height: 44px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 11px;
        border-top: 1px solid #eee8df;
        color: #81786d;
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
        border: 1px solid #e5ddd2;
        border-radius: 7px;
        background: #fff;
        color: #81786d;
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
        background: #fff7e8;
        color: #9a6810;
        font-size: 7.5px;
        font-weight: 700;
    }

    .fc-seller-name {
        overflow: hidden;
        color: #514a42;
        font-size: 8px;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fc-progress {
        height: 6px;
        overflow: hidden;
        border-radius: 999px;
        background: #eeeae4;
    }

    .fc-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--fc-brand);
    }

    .fc-seller-amount {
        color: #302a24;
        font-size: 8px;
        font-weight: 700;
        text-align: right;
        white-space: nowrap;
    }

    .fc-seller-share {
        color: #9b9288;
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
        background: #fff;
        box-shadow: inset 0 0 0 1px #eee8df;
    }

    .fc-status-center {
        position: relative;
        text-align: center;
    }

    .fc-status-total {
        color: #302a24;
        font-size: 17px;
        font-weight: 700;
    }

    .fc-status-caption {
        margin-top: 3px;
        color: #91887d;
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
        color: #81786d;
        font-size: 7.5px;
    }

    .fc-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 3px;
    }

    .fc-status-count {
        color: #514a42;
        font-weight: 600;
        text-align: right;
    }

    .fc-status-percent {
        color: #9b9288;
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
        border-bottom: 1px solid #f0ebe4;
        content-visibility: auto;
        contain-intrinsic-size: 34px;
    }

    .fc-recent-row:last-child {
        border-bottom: 0;
    }

    .fc-recent-icon {
        color: #81786d;
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
        color: #514a42;
        font-size: 7.5px;
        font-weight: 600;
    }

    .fc-recent-seller {
        color: #81786d;
        font-size: 7.3px;
    }

    .fc-recent-amount {
        color: #302a24;
        font-size: 7.8px;
        font-weight: 700;
        text-align: right;
    }

    .fc-recent-date {
        color: #91887d;
        font-size: 7px;
        text-align: right;
        white-space: nowrap;
    }

    .fc-empty {
        display: grid;
        min-height: 100px;
        place-items: center;
        color: #91887d;
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
        border: 1px solid #e5ddd2;
        border-radius: 999px;
        padding: 0 6px;
        color: #81786d;
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
        border-bottom: 1px solid #f0ebe4;
        content-visibility: auto;
        contain-intrinsic-size: 54px;
    }

    .fc-payout-row:last-child {
        border-bottom: 0;
    }

    .fc-payout-name {
        color: #514a42;
        font-size: 7.5px;
        font-weight: 600;
    }

    .fc-payout-meta {
        margin-top: 2px;
        color: #9b9288;
        font-size: 6.5px;
    }

    .fc-payout-amount {
        color: #302a24;
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
        border: 1px solid #e5ddd2;
        border-radius: 7px;
        padding: 0 8px;
        font: inherit;
        font-size: 6.8px;
    }

    .fc-action {
        height: 30px;
        border: 1px solid #e5ddd2;
        border-radius: 7px;
        background: #fff;
        padding: 0 8px;
        color: #6f665b;
        font: inherit;
        font-size: 6.8px;
        font-weight: 600;
        cursor: pointer;
    }

    .fc-action:hover {
        background: #faf8f4;
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
        background: rgba(28,24,20,.42);
        padding: 14px;
    }

    .fc-modal-backdrop.open {
        display: flex;
    }

    .fc-modal {
        width: min(620px, calc(100vw - 28px));
        max-height: calc(100vh - 28px);
        overflow: hidden;
        border: 1px solid #dfd8cf;
        border-radius: 16px;
        background: #f8f7f4;
        box-shadow:
            0 24px 64px rgba(31,24,17,.18),
            0 8px 22px rgba(31,24,17,.07);
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
        border-bottom: 1px solid #ebe5dd;
        background: #fff;
    }

    .fc-modal-title {
        color: #302a24;
        font-size: 10.5px;
        font-weight: 700;
    }

    .fc-modal-close {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 1px solid #e4ddd4;
        border-radius: 8px;
        background: #fff;
        color: #71685f;
        cursor: pointer;
    }

    .fc-modal-close:hover {
        background: #f7f5f2;
        color: #332d27;
    }

    .fc-modal-body {
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        padding: 12px 13px 14px;
        background: #f8f7f4;
        scrollbar-width: thin;
        scrollbar-color: #d0c8be transparent;
    }

    .fc-detail-grid {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 8px;
    }

    .fc-detail-box {
        border: 1px solid #e7e1d9;
        border-radius: 9px;
        background: #fff;
        padding: 9px;
    }

    .fc-detail-label {
        color: #9b9288;
        font-size: 6.5px;
    }

    .fc-detail-value {
        margin-top: 3px;
        color: #514a42;
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
        border: 1px solid #e7e1d9;
        border-radius: 9px;
        background: #fff;
        padding: 9px;
        content-visibility: auto;
        contain-intrinsic-size: 58px;
    }

    .fc-history-rate {
        color: #302a24;
        font-size: 9px;
        font-weight: 700;
    }

    .fc-history-copy,
    .fc-history-date {
        color: #81786d;
        font-size: 6.5px;
        line-height: 1.45;
    }

    .fc-history-date {
        color: #9b9288;
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
        box-shadow:0 4px 12px rgba(75,54,25,.045) !important;
    }

    .finance-commissions .fc-title-icon svg{
        width:17px !important;
        height:17px !important;
    }

    .finance-commissions .fc-eyebrow{
        color:#9a6f23 !important;
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
        color:#17130f !important;
    }

    .finance-commissions .fc-title-gold{
        color:#d99500 !important;
    }

    .finance-commissions .fc-subtitle{
        max-width:760px !important;
        margin-top:7px !important;
        color:#7f756a !important;
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
        <div class="fc-card" style="margin-bottom:12px;padding:10px 12px;border-color:#d7eadf;background:#f4fbf6;color:#356b50;font-size:8px;">
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
                    <div><strong style="color:#475467;">No commission activity in this period.</strong><br>Paid and delivered orders will appear here automatically.</div>
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
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                <input class="fc-control" type="search" name="search" value="{{ $filters['search'] }}" placeholder="Search order number or seller...">
            </div>
            <div class="fc-custom-select" data-fc-dropdown>
                <input type="hidden" name="status" value="{{ $filters['status'] }}" data-fc-dropdown-input>
                <button
                    type="button"
                    class="fc-custom-select-trigger"
                    data-fc-dropdown-trigger
                    aria-haspopup="listbox"
                    aria-expanded="false"
                >
                    <span class="fc-custom-select-main">
                        <span class="fc-custom-select-status-dot" aria-hidden="true"></span>
                        <span data-fc-dropdown-label>{{ $filters['status'] !== '' ? $formatStatus($filters['status']) : 'All Statuses' }}</span>
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
                <input type="hidden" name="seller" value="{{ $filters['seller'] ?: '' }}" data-fc-dropdown-input>
                <button
                    type="button"
                    class="fc-custom-select-trigger"
                    data-fc-dropdown-trigger
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
                        <span data-fc-dropdown-label>{{ $selectedSellerLabel }}</span>
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
                <input class="fc-control" type="date" name="from" value="{{ $period['from'] }}" aria-label="Filter from date">
                <input class="fc-control" type="date" name="to" value="{{ $period['to'] }}" aria-label="Filter to date">
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
            <a href="{{ $exportUrl }}" class="fc-link-button" style="height:36px;padding:0 12px;font-size:9.5px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 20h14"></path></svg>
                Export CSV
            </a>
        </div>

        <div class="fc-table-wrap">
            <table class="fc-table">
                <thead>
                    <tr>
                        <th>#</th><th>Order</th><th>Seller</th><th>Eligible Amount</th><th>Rate</th><th>Commission</th><th>Status</th><th>Delivered At</th><th>Actions</th>
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
                            <td>{{ ($rows->firstItem() ?? 1) + $loop->index }}</td>
                            <td><span class="fc-order">{{ $row['order']->order_number }}</span></td>
                            <td>{{ $row['seller'] }}</td>
                            <td><span class="fc-money">₱{{ number_format($row['subtotal'],2) }}</span></td>
                            <td>{{ $formatRate($row['rate']) }}%</td>
                            <td><span class="fc-money">₱{{ number_format($row['commission'],2) }}</span></td>
                            <td><span class="fc-status {{ $statusClass }}">{{ $formatStatus($row['status']) }}</span></td>
                            <td>{{ $row['order']->delivered_at?->format('M j, Y') ?: '—' }}</td>
                            <td><button type="button" class="fc-view-button" data-view-commission='@json($detailPayload)'>View</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="9"><div class="fc-empty">No commission records match the selected period and filters.</div></td></tr>
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
@endsection
