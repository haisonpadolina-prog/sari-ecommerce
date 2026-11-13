@extends('layouts.admin')
@section('title','Complaints — SARI Admin')
@section('page-title','Complaints')
@section('content')
<style>
    /* ============================================================
       SARI ADMIN — COMPLAINTS & DISPUTES
       UI parity with Buyer "Shop Products" catalog.
       VISUAL ONLY: all IDs, data-* hooks, forms, routes and JS are
       unchanged. One clean stylesheet (no stacked override passes).
       ============================================================ */
    .complaints-page {
        --sh-gold: #c88912;
        --sh-gold-dark: #a96f0e;
        --sh-gold-text: #89580c;
        --sh-ink: #20252b;
        --sh-text: #2e343b;
        --sh-body: #29323b;
        --sh-muted: #707b86;
        --sh-soft: #818995;
        --sh-border: #e5e7eb;
        --sh-control-border: #dfe3e8;
        --sh-divider: #eceef1;
        --sh-row-divider: #eef0f2;
        --sh-icon: #596470;
        --sh-arrow: #a6adb5;
        /* Product card / insight card shadow */
        --sh-shadow:
            0 2px 5px rgba(15, 23, 42, .045),
            0 10px 24px rgba(15, 23, 42, .075),
            0 24px 54px rgba(15, 23, 42, .095),
            inset 0 1px 0 rgba(255, 255, 255, .92);
        width: 100%;
        max-width: 1440px !important;
        margin-inline: auto !important;
        padding-top: 20px;
        padding-bottom: 34px !important;
        color: var(--sh-text);
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .complaints-page *,
    .complaints-page *::before,
    .complaints-page *::after {
        box-sizing: border-box;
    }
    .complaints-page button,
    .complaints-page input,
    .complaints-page a {
        font-family: inherit;
        transition:
            color .16s ease,
            background-color .16s ease,
            border-color .16s ease,
            box-shadow .16s ease,
            opacity .16s ease;
    }
    /* ---------------- PAGE HEADER (open, no container) ---------------- */
    .complaints-page > section:first-of-type {
        display: block !important;
        margin: 0 !important;
        padding: 2px 2px 14px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }
    .complaints-header-main {
        display: block;
        min-width: 0;
    }
    .complaints-header-icon {
        display: none !important;
    }
    .complaints-eyebrow {
        margin: 0 !important;
        color: #a87316 !important;
        font-size: 10.5px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase;
    }
    .complaints-title {
        margin: 5px 0 0 !important;
        color: var(--sh-text) !important;
        font-size: clamp(29px, 2.35vw, 34px) !important;
        font-weight: 760 !important;
        line-height: 1.04 !important;
        letter-spacing: -.035em !important;
    }
    .complaints-title-base { color: var(--sh-text) !important; }
    .complaints-title-accent { color: var(--sh-gold) !important; }
    .complaints-subtitle {
        max-width: 760px !important;
        margin: 7px 0 0 !important;
        color: #7d8792 !important;
        font-size: 11.5px !important;
        font-weight: 450 !important;
        line-height: 1.55 !important;
    }
    /* ---------------- KPI CARDS (same surface as product/insight cards) ---------------- */
    .complaints-summary-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 18px !important;
        margin-top: 12px !important;
    }
    .complaint-stat {
        position: relative !important;
        min-height: 104px !important;
        overflow: hidden;
        border: 1px solid var(--sh-border) !important;
        border-radius: 17px !important;
        background: #fff !important;
        padding: 16px 62px 15px 18px !important;
        text-align: left;
        box-shadow: var(--sh-shadow) !important;
        transform: none !important;
        cursor: pointer;
    }
    .complaint-stat:hover,
    .complaint-stat.is-active,
    .complaint-stat.is-active:hover {
        border-color: var(--sh-border) !important;
        background: #fff !important;
        box-shadow: var(--sh-shadow) !important;
        transform: none !important;
    }
    .complaint-stat:focus-visible {
        outline: none;
        border-color: var(--sh-gold) !important;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .08), var(--sh-shadow) !important;
    }
    .complaint-stat > div {
        min-height: 72px !important;
        align-items: flex-start !important;
    }
    .complaint-stat-label {
        color: var(--sh-soft) !important;
        font-size: 11.5px !important;
        font-weight: 500 !important;
        line-height: 1.35 !important;
    }
    .complaint-stat-value {
        margin-top: 7px !important;
        color: var(--sh-ink) !important;
        font-size: 26px !important;
        font-weight: 750 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }
    .complaint-stat-helper {
        margin-top: 7px !important;
        color: var(--sh-muted) !important;
        font-size: 10.5px !important;
        line-height: 1.4 !important;
    }
    /* Bare heading-style icon (like Trending / Popular headers): no tile */
    .complaint-stat-icon,
    .complaint-stat-icon[class*="border"],
    .complaint-stat-icon[class*="bg-"] {
        position: absolute !important;
        top: 16px !important;
        right: 16px !important;
        display: grid !important;
        width: 28px !important;
        height: 28px !important;
        flex: 0 0 28px !important;
        place-items: center;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .complaint-stat-icon svg {
        width: 22px !important;
        height: 22px !important;
        stroke-width: 1.9 !important;
    }
    .complaint-stat[data-summary-status="all"] .complaint-stat-icon { color: var(--sh-gold) !important; }
    .complaint-stat[data-summary-status="open"] .complaint-stat-icon { color: #d97706 !important; }
    .complaint-stat[data-summary-status="resolved"] .complaint-stat-icon { color: #2f9e63 !important; }
    /* ---------------- FILTER DOCK (open controls, no card) ---------------- */
    .complaints-surface.complaints-filter {
        position: relative;
        z-index: 45;
        margin-top: 12px !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
    .complaints-filter-grid {
        display: grid;
        grid-template-columns:
            minmax(320px, 1.65fr)
            minmax(155px, .78fr)
            auto
            auto;
        align-items: center;
        gap: 10px;
    }
    .complaints-search {
        position: relative;
        z-index: 70;
        min-width: 0;
    }
    .complaints-search svg {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 13px;
        width: 16px;
        height: 16px;
        color: var(--sh-icon);
        pointer-events: none;
        transform: translateY(-50%);
        stroke-width: 1.9;
    }
    .complaints-control,
    .complaints-status-trigger {
        width: 100%;
        height: 42px !important;
        min-height: 42px !important;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #333d47 !important;
        font-size: 12px !important;
        font-weight: 650 !important;
        box-shadow: none !important;
    }
    .complaints-search input {
        padding: 0 40px 0 40px !important;
        color: var(--sh-body) !important;
        font-weight: 550 !important;
    }
    .complaints-search input::placeholder {
        color: #79838e !important;
        font-size: 11.5px;
        font-weight: 450;
        opacity: 1;
    }
    .complaints-control:hover,
    .complaints-status-trigger:hover {
        border-color: #cfd5dc !important;
        background: #fbfcfd !important;
    }
    .complaints-search input:focus {
        outline: none !important;
        border-color: #9faab5 !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(75, 85, 99, .10) !important;
    }
    /* Status dropdown */
    .complaints-status-dropdown {
        position: relative;
        min-width: 0;
    }
    .complaints-status-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 12px !important;
        cursor: pointer;
        text-align: left;
    }
    .complaints-status-trigger:focus-visible,
    .complaints-status-dropdown.is-open .complaints-status-trigger {
        outline: none !important;
        border-color: var(--sh-gold) !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .08) !important;
    }
    .complaints-status-trigger-main {
        display: inline-flex;
        min-width: 0;
        align-items: center;
        gap: 8px;
    }
    .complaints-status-trigger-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: #858078;
    }
    .complaints-status-trigger[data-current-status="open"] .complaints-status-trigger-dot { background: var(--sh-gold); }
    .complaints-status-trigger[data-current-status="resolved"] .complaints-status-trigger-dot { background: #3f9a61; }
    .complaints-status-trigger-label {
        overflow: hidden;
        color: #333d47;
        font-weight: 650;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .complaints-status-chevron {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        color: var(--sh-icon);
        stroke-width: 1.9;
        transition: transform .16s ease, color .16s ease;
    }
    .complaints-status-dropdown.is-open .complaints-status-chevron {
        color: #9b6710;
        transform: rotate(180deg);
    }
    .complaints-status-menu {
        position: absolute;
        z-index: 190;
        top: calc(100% + 6px);
        right: 0;
        left: 0;
        max-height: 270px;
        overflow-y: auto;
        padding: 5px;
        border: 1px solid #e1e5e9;
        border-radius: 12px;
        background: #fff;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-3px);
        box-shadow:
            0 18px 42px rgba(31, 41, 55, .13),
            0 4px 12px rgba(31, 41, 55, .04);
        scrollbar-width: thin;
        scrollbar-color: #d9dde2 transparent;
        transition: opacity .15s ease, transform .15s ease, visibility 0s linear .15s;
    }
    .complaints-status-dropdown.is-open .complaints-status-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
        transition: opacity .15s ease, transform .15s ease;
    }
    .complaints-status-option {
        display: flex;
        width: 100%;
        min-height: 39px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        padding: 0 10px;
        color: #3f4852;
        font-size: 11.5px;
        font-weight: 550;
        line-height: 1.2;
        text-align: left;
        cursor: pointer;
    }
    .complaints-status-option:hover,
    .complaints-status-option:focus-visible {
        outline: none;
        background: #f5f6f8;
        color: #1f252c;
    }
    .complaints-status-option.is-selected {
        background: #f7f3eb;
        color: var(--sh-gold-text);
        font-weight: 800;
    }
    .complaints-status-option-left {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .complaints-status-option-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: #8e857a;
    }
    .complaints-status-option[data-status-value="open"] .complaints-status-option-dot { background: var(--sh-gold); }
    .complaints-status-option[data-status-value="resolved"] .complaints-status-option-dot { background: #3f9a61; }
    .complaints-status-check {
        width: 13px;
        height: 13px;
        opacity: 0;
        color: var(--sh-gold-text);
    }
    .complaints-status-option.is-selected .complaints-status-check { opacity: 1; }
    /* Apply (gold) + Reset (text action, like Shop "Reset") */
    .complaints-apply,
    .complaints-reset {
        display: inline-flex !important;
        height: 42px !important;
        min-height: 42px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px;
        border-radius: 10px !important;
        padding: 0 14px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        white-space: nowrap;
        cursor: pointer;
    }
    .complaints-apply {
        border: 1px solid #d79a28 !important;
        background: var(--sh-gold) !important;
        color: #fff !important;
        box-shadow: 0 5px 12px rgba(200, 137, 18, .14) !important;
    }
    .complaints-apply:hover,
    .complaints-apply:focus-visible {
        outline: none;
        border-color: var(--sh-gold-dark) !important;
        background: var(--sh-gold-dark) !important;
    }
    .complaints-apply svg {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
    }
    .complaints-reset {
        border: 1px solid transparent !important;
        background: transparent !important;
        color: #865709 !important;
        box-shadow: none !important;
    }
    .complaints-reset:hover,
    .complaints-reset:focus-visible {
        outline: none;
        background: transparent !important;
        color: var(--sh-gold) !important;
    }
    /* ---------------- TABLE (same floating surface as cards) ---------------- */
    .complaints-surface.complaints-table-surface {
        position: relative;
        z-index: 1;
        margin-top: 16px !important;
        overflow: hidden !important;
        border: 1px solid var(--sh-border) !important;
        border-radius: 17px !important;
        background: #fff !important;
        box-shadow: var(--sh-shadow) !important;
    }
    .complaints-table-head,
    .complaint-row {
        display: grid;
        grid-template-columns:
            minmax(320px, 1.7fr)
            minmax(185px, .9fr)
            160px
            120px
            64px;
        align-items: center;
        gap: 14px;
    }
    .complaints-table-head {
        min-height: 52px;
        padding: 0 16px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--sh-divider) !important;
        background: #fafbfc !important;
        color: #4b5560 !important;
        font-size: 10.5px !important;
        font-weight: 800 !important;
        line-height: 1.3 !important;
        letter-spacing: .07em !important;
        text-transform: uppercase !important;
    }
    #complaintRows { background: #fff; }
    .complaint-row {
        min-height: 74px;
        padding: 10px 16px !important;
        border-bottom: 1px solid var(--sh-row-divider) !important;
        background: #fff !important;
        box-shadow: none !important;
        content-visibility: auto;
        contain-intrinsic-size: 74px;
    }
    .complaint-row[hidden] { display: none !important; }
    .complaint-row:last-child { border-bottom: 0 !important; }
    .complaint-row:hover { background: #fcfcfd !important; }
    .complaint-row:hover .complaint-subject { color: #9d670c !important; }
    .complaint-subject {
        color: var(--sh-body) !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        line-height: 1.45 !important;
        transition: color .16s ease;
    }
    .complaint-preview {
        max-width: 560px;
        margin-top: 3px !important;
        overflow: hidden;
        color: var(--sh-muted) !important;
        font-size: 10.5px !important;
        font-weight: 500 !important;
        line-height: 1.4 !important;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .complaint-meta-main {
        color: #475467 !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        line-height: 1.4 !important;
    }
    .complaint-meta-sub {
        margin-top: 2px !important;
        color: var(--sh-muted) !important;
        font-size: 10.5px !important;
        font-weight: 500 !important;
        line-height: 1.4 !important;
    }
    .complaint-status {
        display: inline-flex;
        width: fit-content;
        min-height: 26px !important;
        align-items: center;
        gap: 6px !important;
        border-radius: 999px;
        padding: 0 10px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
    }
    .complaint-status::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
    }
    .complaint-status-open {
        border: 1px solid #efd9b0 !important;
        background: #fff8e9 !important;
        color: #a87019 !important;
    }
    .complaint-status-resolved {
        border: 1px solid #d6e9dc !important;
        background: #eef8f1 !important;
        color: #36805a !important;
    }
    .complaint-view {
        display: inline-grid;
        width: 32px !important;
        height: 32px !important;
        place-items: center;
        border: 1px solid transparent !important;
        border-radius: 9px !important;
        background: transparent !important;
        color: var(--sh-arrow) !important;
        box-shadow: none !important;
        cursor: pointer;
    }
    .complaint-view svg {
        width: 15px !important;
        height: 15px !important;
        stroke: currentColor;
    }
    .complaint-row:hover .complaint-view { color: #596470 !important; }
    .complaint-view:hover,
    .complaint-view:focus-visible {
        outline: none;
        border-color: #eadfc9 !important;
        background: #fffaf1 !important;
        color: #9e6a12 !important;
    }
    .complaints-footer {
        display: flex;
        min-height: 54px !important;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 16px !important;
        border-top: 1px solid var(--sh-divider) !important;
        background: #fafbfc !important;
        color: #59636f !important;
        font-size: 11px !important;
        font-weight: 650;
    }
    .complaints-footer p:last-child {
        color: var(--sh-muted) !important;
        font-weight: 500;
    }
    .complaints-empty {
        padding: 44px 16px !important;
        color: var(--sh-muted) !important;
        font-size: 11px !important;
        font-weight: 600;
        text-align: center;
    }
    /* ============================================================
       MODAL — same system as product modal
       (border #e3e6ea, radius 15, shadow 0 30px 80px rgba(31,41,55,.20))
       ============================================================ */
    #complaintModalBackdrop {
        background: rgba(0, 0, 0, .35) !important;
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .18s ease, visibility 0s linear .18s;
    }
    #complaintModalBackdrop.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition: opacity .18s ease;
    }
    #complaintModal {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translate(-50%, -47%) scale(.985);
        transition:
            opacity .18s ease,
            transform .20s cubic-bezier(.22, 1, .36, 1),
            visibility 0s linear .20s;
    }
    #complaintModal.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translate(-50%, -50%) scale(1);
        transition:
            opacity .18s ease,
            transform .20s cubic-bezier(.22, 1, .36, 1);
    }
    .complaint-modal-shell {
        width: min(760px, calc(100vw - 36px)) !important;
        max-height: calc(100vh - 40px) !important;
        overflow: hidden !important;
        border: 1px solid #e3e6ea !important;
        border-radius: 15px !important;
        background: #fff !important;
        box-shadow: 0 30px 80px rgba(31, 41, 55, .20) !important;
    }
    .complaint-modal-header {
        display: flex;
        min-height: 68px !important;
        align-items: center !important;
        justify-content: space-between;
        gap: 12px !important;
        padding: 12px 18px !important;
        border-bottom: 1px solid var(--sh-divider) !important;
        background: #fff !important;
    }
    .complaint-modal-eyebrow {
        margin: 0 !important;
        color: #a87316 !important;
        font-size: 9.5px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase;
    }
    .complaint-modal-title {
        margin: 0 !important;
        color: var(--sh-text) !important;
        font-size: 18px !important;
        font-weight: 750 !important;
        line-height: 1.2 !important;
        letter-spacing: -.03em !important;
    }
    .complaint-modal-close {
        display: grid;
        width: 36px !important;
        height: 36px !important;
        flex: 0 0 36px !important;
        place-items: center;
        border: 1px solid #dfe3e8 !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #596470 !important;
        cursor: pointer;
    }
    .complaint-modal-close:hover,
    .complaint-modal-close:focus-visible {
        outline: none;
        border-color: #cfd5dc !important;
        background: #f5f6f8 !important;
        color: #1f252c !important;
    }
    .complaint-modal-close svg { width: 14px !important; height: 14px !important; }
    .complaint-modal-body {
        max-height: calc(100vh - 110px) !important;
        overflow-y: auto;
        padding: 16px 18px 18px !important;
        background: #fff !important;
        scrollbar-width: thin;
        scrollbar-color: #d9dde2 transparent;
    }
    .complaint-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px !important;
    }
    .complaint-field-label {
        margin: 0 0 5px !important;
        color: #4b5560 !important;
        font-size: 10.5px !important;
        font-weight: 750 !important;
        letter-spacing: .045em;
        text-transform: uppercase;
    }
    .complaint-field-value {
        display: flex;
        min-height: 42px !important;
        align-items: center;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: #fff !important;
        padding: 0 12px !important;
        color: #333d47 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }
    .complaint-detail-block { margin-top: 14px !important; }
    .complaint-detail-copy {
        min-height: 84px !important;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: #fafbfc !important;
        padding: 12px !important;
        color: #3f4852 !important;
        font-size: 12px !important;
        font-weight: 500;
        line-height: 1.6 !important;
        white-space: pre-wrap;
    }
    .complaint-admin-note {
        border-color: #eadfc9 !important;
        background: #fffaf1 !important;
    }
    .complaint-action-section {
        margin-top: 16px !important;
        border-top: 1px solid var(--sh-divider) !important;
        padding-top: 14px !important;
    }
    .complaint-action-title {
        color: var(--sh-ink) !important;
        font-size: 13px !important;
        font-weight: 750 !important;
    }
    .complaint-action-copy {
        margin-top: 3px !important;
        color: var(--sh-muted) !important;
        font-size: 11px !important;
        line-height: 1.5 !important;
    }
    .complaint-action-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px !important;
        margin-top: 11px !important;
    }
    .complaint-action-input {
        width: 100%;
        min-width: 0;
        height: 42px !important;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: #fff !important;
        padding: 0 12px !important;
        color: var(--sh-body) !important;
        font-size: 12px !important;
        font-weight: 550;
    }
    .complaint-action-input::placeholder {
        color: #79838e !important;
        font-weight: 450;
        opacity: 1;
    }
    .complaint-action-input:focus {
        outline: none !important;
        border-color: var(--sh-gold) !important;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .08) !important;
    }
    .complaint-resolve,
    .complaint-reopen {
        display: inline-flex;
        height: 42px !important;
        min-height: 42px !important;
        align-items: center;
        justify-content: center;
        border-radius: 10px !important;
        padding: 0 16px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        white-space: nowrap;
        box-shadow: none !important;
        cursor: pointer;
    }
    .complaint-resolve {
        border: 1px solid #cfe2d5 !important;
        background: #f3f9f5 !important;
        color: #3d7a55 !important;
    }
    .complaint-resolve:hover,
    .complaint-resolve:focus-visible {
        outline: none;
        border-color: #b8d6c2 !important;
        background: #eaf5ed !important;
        color: #2f7c48 !important;
    }
    .complaint-reopen {
        border: 1px solid #d79a28 !important;
        background: var(--sh-gold) !important;
        color: #fff !important;
        box-shadow: 0 5px 12px rgba(200, 137, 18, .14) !important;
    }
    .complaint-reopen:hover,
    .complaint-reopen:focus-visible {
        outline: none;
        border-color: var(--sh-gold-dark) !important;
        background: var(--sh-gold-dark) !important;
    }
    /* ---------------- RESPONSIVE ---------------- */
    @media (max-width: 1279px) {
        .complaints-filter-grid {
            grid-template-columns: minmax(0, 1fr) 190px;
        }
        .complaints-search { grid-column: 1 / -1; }
    }
    @media (max-width: 1023px) {
        .complaints-summary-grid { gap: 14px !important; }
        .complaints-table-head { display: none !important; }
        .complaint-row {
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            margin: 10px;
            padding: 14px !important;
            border: 1px solid var(--sh-border) !important;
            border-radius: 14px;
        }
        .complaint-row > div:nth-child(2),
        .complaint-row > div:nth-child(3),
        .complaint-row > div:nth-child(4) { grid-column: 1; }
        .complaint-row > div:last-child {
            grid-column: 2;
            grid-row: 1;
        }
    }
    @media (max-width: 767px) {
        .complaints-page { padding-top: 13px; }
        .complaints-eyebrow { font-size: 9.5px !important; }
        .complaints-title { font-size: 29px !important; }
        .complaints-subtitle { font-size: 10.5px !important; }
        .complaints-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 14px !important;
        }
        .complaint-stat:first-child { grid-column: 1 / -1; }
        .complaint-stat {
            min-height: 96px !important;
            padding: 14px 50px 13px 15px !important;
            border-radius: 14px !important;
        }
        .complaint-stat-value { font-size: 24px !important; }
        .complaint-stat-icon { top: 14px !important; right: 14px !important; }
        .complaints-filter-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .complaints-search { grid-column: auto; }
        .complaints-surface.complaints-table-surface { border-radius: 14px !important; }
        .complaints-footer {
            align-items: flex-start;
            flex-direction: column;
        }
        .complaint-modal-shell {
            width: calc(100vw - 14px) !important;
            max-height: calc(100vh - 14px) !important;
            border-radius: 14px !important;
        }
        .complaint-modal-header { padding: 11px 13px !important; }
        .complaint-modal-body { padding: 12px 13px 14px !important; }
        .complaint-detail-grid { grid-template-columns: 1fr; }
        .complaint-action-form { grid-template-columns: 1fr; }
        .complaint-resolve,
        .complaint-reopen { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) {
        .complaints-page *,
        #complaintModal,
        #complaintModalBackdrop {
            animation: none !important;
            transition-duration: .01ms !important;
            scroll-behavior: auto !important;
        }
        #complaintModal,
        #complaintModal.is-open {
            transform: translate(-50%, -50%) !important;
        }
    }
/* Seller Orders layout: neutral surfaces and consistent readable controls. */
.complaints-page.orders-reference{max-width:1520px!important;padding-top:0!important;--sh-shadow:0 3px 10px rgba(15,23,42,.035),0 12px 30px rgba(15,23,42,.055)}
.complaints-page.orders-reference .complaints-title{font-size:clamp(27px,2.3vw,31px)!important}
.complaints-page.orders-reference .complaints-summary-grid{gap:16px!important;margin-top:20px!important}
.complaints-page.orders-reference .complaint-stat{min-height:118px!important;padding:20px!important;border:1px solid #E5E7EB!important;border-radius:18px!important;background:#FFF!important;box-shadow:var(--sh-shadow)!important;transform:none!important;text-align:left!important}
.complaints-page.orders-reference .complaint-stat::before,.complaints-page.orders-reference .complaint-stat::after{display:none!important}
.complaints-page.orders-reference .complaint-stat-label{font-size:13px!important;font-weight:600!important;color:#637081!important}
.complaints-page.orders-reference .complaint-stat-value{font-size:30px!important;margin-top:8px!important;color:#1F2937!important}
.complaints-page.orders-reference .complaint-stat-helper{font-size:11px!important;color:#8793A1!important}
.complaints-page.orders-reference .complaint-stat-icon{top:20px!important;right:20px!important;background:transparent!important;border:0!important;box-shadow:none!important}
.complaints-page.orders-reference .complaint-stat-icon svg{width:25px!important;height:25px!important}
.complaints-page.orders-reference .complaints-filter{margin-top:20px!important}
.complaints-page.orders-reference .complaints-filter-grid{grid-template-columns:minmax(220px,1fr) 210px auto auto!important;gap:12px!important}
.complaints-page.orders-reference :is(.complaints-control,.complaints-status-trigger,.complaints-apply,.complaints-reset){height:48px!important;min-height:48px!important;border-radius:12px!important;font-size:13px!important;box-shadow:none!important}
.complaints-page.orders-reference .complaints-search input{font-weight:400!important;padding-left:44px!important}
.complaints-page.orders-reference .complaints-search input::placeholder{font-size:12px!important;color:#98A2B3!important}
.complaints-page.orders-reference .complaints-search svg{width:20px!important;height:20px!important;left:15px!important;color:#98A2B3!important}
.complaints-page.orders-reference .complaints-apply{padding:0 20px!important;background:#C88912!important;border:1px solid #C88912!important;color:white!important}
.complaints-page.orders-reference .complaints-reset{padding:0 20px!important;border:1px solid #DCE2E8!important;background:#FFF!important;color:#5F6D7C!important}
.complaints-page.orders-reference .complaints-status-option{min-height:40px!important;font-size:12px!important}
.complaints-page.orders-reference .complaints-status-option.is-selected,.complaints-page.orders-reference .complaints-status-option:hover{background:#F1F5F9!important;color:#344054!important}
.complaints-page.orders-reference .complaints-status-check{color:#64748B!important}
.complaints-page.orders-reference .complaints-table-surface{margin-top:20px!important;border-radius:18px!important}
.complaints-page.orders-reference .complaints-queue-heading{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:22px;border-bottom:1px solid #E9EDF1}
.complaints-page.orders-reference .complaints-queue-heading h3{margin:0;font-size:19px;font-weight:700;color:#293440}
.complaints-page.orders-reference .complaints-queue-heading p{margin:5px 0 0;font-size:12px;color:#8793A1}
.complaints-page.orders-reference .complaints-queue-heading > span{font-size:12px;white-space:nowrap;color:#8793A1}
.complaints-page.orders-reference .complaints-grid-scroll{overflow-x:auto}
.complaints-page.orders-reference .complaints-table-head,.complaints-page.orders-reference .complaint-row{display:grid!important;grid-template-columns:30px minmax(250px,1.7fr) minmax(160px,1fr) 145px 110px 65px!important;gap:14px!important;min-width:930px!important;padding:16px 22px!important}
.complaints-page.orders-reference .complaints-table-head{min-height:48px!important;font-size:11px!important;color:#8591A0!important;background:#F8FAFC!important}
.complaints-page.orders-reference .complaint-row{min-height:88px!important;content-visibility:visible!important}
.complaints-page.orders-reference .complaint-row[hidden]{display:none!important}
.complaints-page.orders-reference .complaint-row:hover{background:#F8FAFC!important}
.complaints-page.orders-reference .complaint-row.is-selected{background:#F1F5F9!important}
.complaints-page.orders-reference .complaint-subject{font-size:13px!important;color:#293440!important;font-weight:600!important}
.complaints-page.orders-reference .complaint-preview,.complaints-page.orders-reference .complaint-meta-sub{font-size:11.5px!important;line-height:1.6!important;color:#8793A1!important}
.complaints-page.orders-reference .complaint-meta-main{font-size:12px!important;color:#344054!important}
.complaints-page.orders-reference .complaint-status{font-size:11px!important;min-height:27px!important}
.complaints-page.orders-reference .complaint-status-open{background:#FFF!important;border-color:#E5C97C!important;color:#996918!important}
.complaints-page.orders-reference .complaint-view{width:36px!important;height:36px!important;border:1px solid #DCE2E8!important;background:white!important;color:#64748B!important}
.complaints-page.orders-reference .complaints-empty{padding:64px 20px!important;font-size:14px!important;text-align:center;color:#8591A0!important}
.complaints-page.orders-reference .complaints-footer{min-height:68px!important;padding:16px 22px!important;flex-wrap:wrap;font-size:12px!important;color:#7B8794!important}
.complaints-page.orders-reference .complaints-pagination{display:flex;align-items:center;gap:6px}
.complaints-page.orders-reference .complaints-pagination button{min-width:36px;height:36px;border:1px solid #E5E7EB;border-radius:9px;background:white;color:#64748B;font:600 12px inherit;cursor:pointer}
.complaints-page.orders-reference .complaints-pagination button[aria-current="page"]{background:#C88912;border-color:#C88912;color:white}
.complaints-page.orders-reference .complaints-pagination button:disabled{opacity:.4;cursor:default}
.complaints-page.orders-reference .complaint-select{width:18px;height:18px;accent-color:#C88912;cursor:pointer}
.complaints-page.orders-reference .complaint-selection{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;padding:12px 22px;background:#F8FAFC;border-bottom:1px solid #E5E7EB;font-size:12px}
.complaints-page.orders-reference .complaint-selection button{padding:7px 12px;border:1px solid #DCE2E8;border-radius:8px;background:white;color:#475467;font-size:12px}
.complaints-page.orders-reference [hidden]{display:none!important}
.complaints-page.orders-reference button:focus-visible,.complaints-page.orders-reference input:focus-visible{outline:2px solid #B18A42;outline-offset:3px}
@media(max-width:1000px){.complaints-page.orders-reference .complaints-filter-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}.complaints-page.orders-reference .complaints-search{grid-column:1 / -1!important}.complaints-page.orders-reference .complaints-reset{grid-column:2!important}.complaints-page.orders-reference .complaints-summary-grid{gap:12px!important}}
@media(max-width:600px){.complaints-page.orders-reference .complaints-summary-grid{grid-template-columns:minmax(0,1fr)!important}.complaints-page.orders-reference .complaint-stat{min-height:110px!important}.complaints-page.orders-reference .complaints-filter-grid{grid-template-columns:minmax(0,1fr)!important}.complaints-page.orders-reference .complaints-reset,.complaints-page.orders-reference .complaints-search{grid-column:auto!important}.complaints-page.orders-reference .complaints-queue-heading{padding:18px 16px}.complaints-page.orders-reference .complaints-footer{padding:14px 16px!important}.complaints-page.orders-reference .complaints-pagination{margin-left:auto}}

</style>
@php
    $complaintCount = $complaints->count();
    $statusTone = fn ($status) => strtolower((string) $status) === 'resolved' ? 'complaint-status-resolved' : 'complaint-status-open';
@endphp
<div class="complaints-page orders-reference mx-auto w-full max-w-[1880px] pb-8">
    @if(session('success'))
        <div class="mb-4 rounded-[14px] border border-[#d5e6da] bg-[#f4faf6] px-4 py-3 text-[9px] text-[#426b50]">{{ session('success') }}</div>
    @endif
    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="complaints-header-main">
            <span class="complaints-header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v11H8l-3 3V5Z"></path><path d="M9 9h6"></path><path d="M9 12h4"></path></svg></span>
            <div class="min-w-0"><p class="complaints-eyebrow">Platform Support</p><h2 class="complaints-title"><span class="complaints-title-base">Platform</span> <span class="complaints-title-accent">Complaints</span></h2><p class="complaints-subtitle"></p></div>
        </div>
    </section>
    <section class="complaints-summary-grid mt-4">
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="all"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#efdfbf] bg-[#fff7e8] text-[#b98112]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v11H8l-3 3V5Z"></path><path d="M9 9h6"></path><path d="M9 12h4"></path></svg></span><div><p class="complaint-stat-label">Total Complaints</p><p class="complaint-stat-value">{{ $stats['total'] ?? $complaintCount }}</p><p class="complaint-stat-helper">All submitted complaints</p></div></div></button>
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="open"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#f0dfd0] bg-[#fff5ed] text-[#c26f20]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5"></path><path d="M12 16h.01"></path></svg></span><div><p class="complaint-stat-label">Open Cases</p><p class="complaint-stat-value">{{ $stats['open'] ?? 0 }}</p><p class="complaint-stat-helper">Needs admin resolution</p></div></div></button>
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="resolved"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#d8ebde] bg-[#eef8f1] text-[#298b53]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg></span><div><p class="complaint-stat-label">Resolved</p><p class="complaint-stat-value">{{ $stats['resolved'] ?? 0 }}</p><p class="complaint-stat-helper">Completed complaint cases</p></div></div></button>
    </section>
    <section class="complaints-surface complaints-filter mt-4"><div class="complaints-filter-grid">
        <div class="complaints-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg><input id="complaintSearch" type="search" autocomplete="off" class="complaints-control" placeholder="Search subject, reporter, description, or note..."></div>
        <div class="complaints-status-dropdown" id="complaintStatusDropdown">
            <input id="complaintStatusFilter" type="hidden" value="all">
            <button id="complaintStatusTrigger" type="button" class="complaints-status-trigger" data-current-status="all" aria-haspopup="listbox" aria-expanded="false">
                <span class="complaints-status-trigger-main">
                    <span class="complaints-status-trigger-dot" aria-hidden="true"></span>
                    <span id="complaintStatusLabel" class="complaints-status-trigger-label">All Status</span>
                </span>
                <svg viewBox="0 0 24 24" class="complaints-status-chevron" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 10 5 5 5-5"></path></svg>
            </button>
            <div id="complaintStatusMenu" class="complaints-status-menu" role="listbox" aria-label="Filter complaints by status">
                <button type="button" class="complaints-status-option is-selected" data-status-value="all" role="option" aria-selected="true"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>All Status</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
                <button type="button" class="complaints-status-option" data-status-value="open" role="option" aria-selected="false"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>Open</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
                <button type="button" class="complaints-status-option" data-status-value="resolved" role="option" aria-selected="false"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>Resolved</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
            </div>
        </div>
        <button id="complaintApplyFilter" type="button" class="complaints-apply"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>Apply Filter</button>
        <button id="complaintResetFilter" type="button" class="complaints-reset">Reset</button>
    </div></section>
    <section class="complaints-surface complaints-table-surface mt-4 overflow-hidden">
        <div class="complaints-queue-heading"><div><h3>Complaint Records</h3><p>Review submitted complaints and their resolution status.</p></div><span id="complaintQueueCount">{{ $complaintCount }} records</span></div>
<div class="complaint-selection" id="complaintSelection" hidden><span id="complaintSelectedCount" role="status"></span><div><button type="button" id="complaintReviewSelected">Review first selected</button> <button type="button" id="complaintClearSelected">Clear selection</button></div></div>
<div class="complaints-grid-scroll" tabindex="0" role="region" aria-label="Complaint records">
<div class="complaints-table-head"><div><input class="complaint-select" type="checkbox" id="complaintSelectPage" aria-label="Select complaints on this page"></div><div>Complaint</div><div>Reporter</div><div>Submitted</div><div>Status</div><div class="text-right">Action</div></div>
        <div id="complaintRows">
        @forelse($complaints as $complaint)
            @php
                $complaintStatus = strtolower((string) $complaint->status);
                $reporterRole = strtoupper((string) $complaint->reporter_role);
                $reporter = $complaint->reporter_identifier ?: 'Account';
            @endphp
            <article class="complaint-row" data-complaint-row data-subject="{{ $complaint->subject }}" data-description="{{ $complaint->description }}" data-reporter-role="{{ $reporterRole }}" data-reporter="{{ $reporter }}" data-status="{{ $complaintStatus }}" data-submitted="{{ $complaint->created_at?->format('M d, Y h:i A') }}" data-admin-note="{{ $complaint->admin_note ?? '' }}" data-resolve-url="{{ $complaintStatus === 'open' ? route('admin.complaints.resolve',$complaint) : '' }}" data-reopen-url="{{ $complaintStatus !== 'open' ? route('admin.complaints.reopen',$complaint) : '' }}">
                <div><input class="complaint-select" type="checkbox" data-complaint-select aria-label="Select {{ $complaint->subject }}"></div><div class="min-w-0"><p class="complaint-subject truncate">{{ $complaint->subject }}</p><p class="complaint-preview">{{ $complaint->description }}</p></div>
                <div><p class="complaint-meta-main">{{ $reporter }}</p><p class="complaint-meta-sub">{{ $reporterRole ?: 'ACCOUNT' }}</p></div>
                <div><p class="complaint-meta-main">{{ $complaint->created_at?->format('M d, Y') ?: '—' }}</p><p class="complaint-meta-sub">{{ $complaint->created_at?->format('h:i A') }}</p></div>
                <div><span class="complaint-status {{ $statusTone($complaintStatus) }}">{{ ucfirst($complaintStatus) }}</span></div>
                <div class="flex justify-end"><button type="button" class="complaint-view" data-view-complaint title="View complaint" aria-label="View complaint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6S2.5 12 2.5 12Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg></button></div>
            </article>
        @empty
            <div class="complaints-empty">No complaints submitted.</div>
        @endforelse
        </div>
        </div><div class="complaints-footer"><p id="complaintResultCount">Showing {{ $complaintCount }} of {{ $complaintCount }} complaints</p><nav class="complaints-pagination" id="complaintPagination" aria-label="Complaint pages"></nav></div>
    </section>
</div>
<div id="complaintModalBackdrop" class="fixed inset-0 z-[90] bg-[#1f1d1a]/45" aria-hidden="true"></div>
<div id="complaintModal" class="fixed left-1/2 top-1/2 z-[100]" role="dialog" aria-modal="true" aria-labelledby="complaintModalTitle" aria-hidden="true">
    <div class="complaint-modal-shell">
        <div class="complaint-modal-header"><div class="min-w-0"><p class="complaint-modal-eyebrow">Complaint details</p><div class="mt-1 flex flex-wrap items-center gap-2"><h3 id="complaintModalTitle" class="complaint-modal-title">Complaint</h3><span id="complaintModalStatus" class="complaint-status complaint-status-open">Open</span></div></div><button id="complaintModalClose" type="button" class="complaint-modal-close" aria-label="Close"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg></button></div>
        <div class="complaint-modal-body">
            <div class="complaint-detail-grid"><div><p class="complaint-field-label">Reporter</p><div id="complaintModalReporter" class="complaint-field-value">—</div></div><div><p class="complaint-field-label">Submitted</p><div id="complaintModalSubmitted" class="complaint-field-value">—</div></div></div>
            <div class="complaint-detail-block"><p class="complaint-field-label">Description</p><div id="complaintModalDescription" class="complaint-detail-copy">—</div></div>
            <div id="complaintModalNoteWrap" class="complaint-detail-block hidden"><p class="complaint-field-label">Admin resolution note</p><div id="complaintModalNote" class="complaint-detail-copy complaint-admin-note">—</div></div>
            <section class="complaint-action-section"><p class="complaint-action-title">Case action</p><p id="complaintActionCopy" class="complaint-action-copy">Record a resolution note before closing this complaint.</p>
                <form id="complaintResolveForm" method="POST" class="complaint-action-form">@csrf<input id="complaintResolutionNote" name="admin_note" required placeholder="Resolution note..." class="complaint-action-input"><button type="submit" class="complaint-resolve">Resolve Complaint</button></form>
                <form id="complaintReopenForm" method="POST" class="mt-3 hidden">@csrf<button type="submit" class="complaint-reopen">Reopen Complaint</button></form>
            </section>
        </div>
    </div>
</div>
<script>
(function(){
    const rows=[...document.querySelectorAll('[data-complaint-row]')],search=document.getElementById('complaintSearch'),statusFilter=document.getElementById('complaintStatusFilter'),resultCount=document.getElementById('complaintResultCount'),summaryCards=[...document.querySelectorAll('[data-summary-status]')];
    const statusDropdown=document.getElementById('complaintStatusDropdown'),statusTrigger=document.getElementById('complaintStatusTrigger'),statusLabel=document.getElementById('complaintStatusLabel'),statusOptions=[...document.querySelectorAll('[data-status-value]')];
    const modal=document.getElementById('complaintModal'),backdrop=document.getElementById('complaintModalBackdrop'),resolveForm=document.getElementById('complaintResolveForm'),reopenForm=document.getElementById('complaintReopenForm');
    const normalize=v=>(v||'').toString().trim().toLowerCase();
    // Performance: normalize searchable complaint data once.
    const complaintIndex=rows.map(row=>({
        row,
        status:normalize(row.dataset.status),
        searchText:normalize([
            row.dataset.subject,
            row.dataset.description,
            row.dataset.reporterRole,
            row.dataset.reporter,
            row.dataset.adminNote
        ].join(' '))
    }));
    function debounce(callback,wait=130){
        let timer=0;
        return function(...args){
            window.clearTimeout(timer);
            timer=window.setTimeout(()=>callback.apply(this,args),wait);
        };
    }
    let filterFrame=0, currentPage=1;
    const PAGE_SIZE=10, selectedRows=new Set();
    let pageRows=[];
    const selectPage=document.getElementById('complaintSelectPage');
    function renderRows(){
        const q=normalize(search?.value), status=normalize(statusFilter?.value||'all');
        const matches=complaintIndex.filter(item=>(status==='all'||item.status===status)&&(!q||item.searchText.includes(q)));
        const pages=Math.max(1,Math.ceil(matches.length/PAGE_SIZE));
        currentPage=Math.min(Math.max(1,currentPage),pages);
        const offset=(currentPage-1)*PAGE_SIZE;
        pageRows=matches.slice(offset,offset+PAGE_SIZE).map(item=>item.row);
        rows.forEach(row=>{row.hidden=!pageRows.includes(row);row.classList.toggle('is-selected',selectedRows.has(row));row.querySelector('[data-complaint-select]').checked=selectedRows.has(row)});
        const checked=pageRows.filter(row=>selectedRows.has(row)).length;
        selectPage.checked=pageRows.length>0&&checked===pageRows.length;
        selectPage.indeterminate=checked>0&&checked<pageRows.length;
        selectPage.disabled=pageRows.length===0;
        document.getElementById('complaintSelection').hidden=selectedRows.size===0;
        document.getElementById('complaintSelectedCount').textContent=selectedRows.size+' selected';
        if(resultCount)resultCount.textContent=matches.length?`Showing ${offset+1}–${offset+pageRows.length} of ${matches.length} complaints`:'Showing 0 of 0 complaints';
        document.getElementById('complaintQueueCount').textContent=matches.length+' records';
        let empty=document.getElementById('complaintFilteredEmpty');
        if(!empty){empty=document.createElement('div');empty.id='complaintFilteredEmpty';empty.className='complaints-empty';empty.textContent='No complaints match your filters.';document.getElementById('complaintRows').appendChild(empty)}
        empty.hidden=matches.length>0||rows.length===0;
        const nav=document.getElementById('complaintPagination');nav.replaceChildren();nav.hidden=pages<=1;
        function pageButton(label,number,disabled,active){const b=document.createElement('button');b.type='button';b.textContent=label;b.disabled=disabled;b.setAttribute('aria-label',label==='Previous'||label==='Next'?label+' page':'Page '+number);if(active)b.setAttribute('aria-current','page');b.addEventListener('click',()=>{currentPage=number;renderRows()});nav.appendChild(b)}
        pageButton('Previous',currentPage-1,currentPage===1,false);
        const first=Math.max(1,Math.min(currentPage-2,pages-4));
        for(let n=first;n<=Math.min(pages,first+4);n++)pageButton(String(n),n,false,n===currentPage);
        pageButton('Next',currentPage+1,currentPage===pages,false);
        summaryCards.forEach(card=>card.classList.toggle('is-active',normalize(card.dataset.summaryStatus)===status));
    }
    function applyFilters(){currentPage=1;window.cancelAnimationFrame(filterFrame);filterFrame=window.requestAnimationFrame(renderRows)}
    rows.forEach(row=>row.querySelector('[data-complaint-select]').addEventListener('change',event=>{if(event.target.checked)selectedRows.add(row);else selectedRows.delete(row);renderRows()}));
    selectPage.addEventListener('change',()=>{pageRows.forEach(row=>{if(selectPage.checked)selectedRows.add(row);else selectedRows.delete(row)});renderRows()});
    document.getElementById('complaintClearSelected').addEventListener('click',()=>{selectedRows.clear();renderRows()});
    // Selection assists review only; resolve/reopen still use the original per-case forms.
    document.getElementById('complaintReviewSelected').addEventListener('click',()=>openModal(selectedRows.values().next().value));
    const debouncedApplyFilters=debounce(applyFilters,130);
    function syncStatusDropdown(value){const target=(value||'all').toString().toLowerCase(),active=statusOptions.find(option=>(option.dataset.statusValue||'all').toLowerCase()===target)||statusOptions[0];if(statusFilter)statusFilter.value=active?.dataset.statusValue||'all';if(statusLabel)statusLabel.textContent=active?.querySelector('.complaints-status-option-left span:last-child')?.textContent?.trim()||'All Status';if(statusTrigger)statusTrigger.dataset.currentStatus=active?.dataset.statusValue||'all';statusOptions.forEach(option=>{const selected=option===active;option.classList.toggle('is-selected',selected);option.setAttribute('aria-selected',selected?'true':'false')})}
    function closeStatusDropdown(){statusDropdown?.classList.remove('is-open');statusTrigger?.setAttribute('aria-expanded','false')}
    function resetFilters(){if(search)search.value='';syncStatusDropdown('all');applyFilters()}
    statusTrigger?.addEventListener('click',event=>{event.stopPropagation();const open=!statusDropdown?.classList.contains('is-open');statusDropdown?.classList.toggle('is-open',open);statusTrigger.setAttribute('aria-expanded',open?'true':'false')});
    statusOptions.forEach(option=>option.addEventListener('click',()=>{syncStatusDropdown(option.dataset.statusValue||'all');closeStatusDropdown();applyFilters()}));
    document.addEventListener('click',event=>{if(!event.target.closest('#complaintStatusDropdown'))closeStatusDropdown()});
    search?.addEventListener('input',debouncedApplyFilters,{passive:true});
    document.getElementById('complaintApplyFilter')?.addEventListener('click',applyFilters);
    document.getElementById('complaintResetFilter')?.addEventListener('click',resetFilters);
    summaryCards.forEach(card=>card.addEventListener('click',()=>{
        syncStatusDropdown(card.dataset.summaryStatus||'all');
        applyFilters();
    }));
    function openModal(row){if(!row)return;const status=normalize(row.dataset.status);document.getElementById('complaintModalTitle').textContent=row.dataset.subject||'Complaint';document.getElementById('complaintModalReporter').textContent=`${row.dataset.reporter||'Account'} · ${row.dataset.reporterRole||'ACCOUNT'}`;document.getElementById('complaintModalSubmitted').textContent=row.dataset.submitted||'—';document.getElementById('complaintModalDescription').textContent=row.dataset.description||'No description provided.';const noteWrap=document.getElementById('complaintModalNoteWrap'),note=document.getElementById('complaintModalNote');if(row.dataset.adminNote){note.textContent=row.dataset.adminNote;noteWrap.classList.remove('hidden')}else{noteWrap.classList.add('hidden')}const badge=document.getElementById('complaintModalStatus');badge.textContent=status==='resolved'?'Resolved':'Open';badge.className='complaint-status '+(status==='resolved'?'complaint-status-resolved':'complaint-status-open');if(status==='open'){resolveForm.action=row.dataset.resolveUrl||'';resolveForm.classList.remove('hidden');reopenForm.classList.add('hidden');document.getElementById('complaintActionCopy').textContent='Record a resolution note before closing this complaint.'}else{reopenForm.action=row.dataset.reopenUrl||'';reopenForm.classList.remove('hidden');resolveForm.classList.add('hidden');document.getElementById('complaintActionCopy').textContent='Reopen this complaint if the case requires additional review.'}modal.classList.add('is-open');backdrop.classList.add('is-open');modal.setAttribute('aria-hidden','false');backdrop.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';window.requestAnimationFrame(()=>document.getElementById('complaintModalClose')?.focus({preventScroll:true}))}
    function closeModal(){modal?.classList.remove('is-open');backdrop?.classList.remove('is-open');modal?.setAttribute('aria-hidden','true');backdrop?.setAttribute('aria-hidden','true');document.body.style.overflow=''}
    document.addEventListener('click',event=>{
        const viewButton=event.target.closest('[data-view-complaint]');
        if(viewButton){
            openModal(viewButton.closest('[data-complaint-row]'));
            return;
        }
        if(event.target.closest('#complaintModalClose')){
            closeModal();
            return;
        }
        if(event.target===backdrop){
            closeModal();
        }
    });
    document.addEventListener('keydown',e=>{
        if(e.key==='Escape')closeModal();
    });
    syncStatusDropdown(statusFilter?.value||'all');
    applyFilters();
})();
</script>
@endsection@extends('layouts.admin')
@section('title','Complaints — SARI Admin')
@section('page-title','Complaints')
@section('content')
<style>
    /* ============================================================
       SARI ADMIN — COMPLAINTS & DISPUTES
       UI parity with Buyer "Shop Products" catalog.
       VISUAL ONLY: all IDs, data-* hooks, forms, routes and JS are
       unchanged. One clean stylesheet (no stacked override passes).
       ============================================================ */
    .complaints-page {
        --sh-gold: #c88912;
        --sh-gold-dark: #a96f0e;
        --sh-gold-text: #89580c;
        --sh-ink: #20252b;
        --sh-text: #2e343b;
        --sh-body: #29323b;
        --sh-muted: #707b86;
        --sh-soft: #818995;
        --sh-border: #e5e7eb;
        --sh-control-border: #dfe3e8;
        --sh-divider: #eceef1;
        --sh-row-divider: #eef0f2;
        --sh-icon: #596470;
        --sh-arrow: #a6adb5;
        /* Product card / insight card shadow */
        --sh-shadow:
            0 2px 5px rgba(15, 23, 42, .045),
            0 10px 24px rgba(15, 23, 42, .075),
            0 24px 54px rgba(15, 23, 42, .095),
            inset 0 1px 0 rgba(255, 255, 255, .92);
        width: 100%;
        max-width: 1440px !important;
        margin-inline: auto !important;
        padding-top: 20px;
        padding-bottom: 34px !important;
        color: var(--sh-text);
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .complaints-page *,
    .complaints-page *::before,
    .complaints-page *::after {
        box-sizing: border-box;
    }
    .complaints-page button,
    .complaints-page input,
    .complaints-page a {
        font-family: inherit;
        transition:
            color .16s ease,
            background-color .16s ease,
            border-color .16s ease,
            box-shadow .16s ease,
            opacity .16s ease;
    }
    /* ---------------- PAGE HEADER (open, no container) ---------------- */
    .complaints-page > section:first-of-type {
        display: block !important;
        margin: 0 !important;
        padding: 2px 2px 14px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }
    .complaints-header-main {
        display: block;
        min-width: 0;
    }
    .complaints-header-icon {
        display: none !important;
    }
    .complaints-eyebrow {
        margin: 0 !important;
        color: #a87316 !important;
        font-size: 10.5px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase;
    }
    .complaints-title {
        margin: 5px 0 0 !important;
        color: var(--sh-text) !important;
        font-size: clamp(29px, 2.35vw, 34px) !important;
        font-weight: 760 !important;
        line-height: 1.04 !important;
        letter-spacing: -.035em !important;
    }
    .complaints-title-base { color: var(--sh-text) !important; }
    .complaints-title-accent { color: var(--sh-gold) !important; }
    .complaints-subtitle {
        max-width: 760px !important;
        margin: 7px 0 0 !important;
        color: #7d8792 !important;
        font-size: 11.5px !important;
        font-weight: 450 !important;
        line-height: 1.55 !important;
    }
    /* ---------------- KPI CARDS (same surface as product/insight cards) ---------------- */
    .complaints-summary-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 18px !important;
        margin-top: 12px !important;
    }
    .complaint-stat {
        position: relative !important;
        min-height: 104px !important;
        overflow: hidden;
        border: 1px solid var(--sh-border) !important;
        border-radius: 17px !important;
        background: #fff !important;
        padding: 16px 62px 15px 18px !important;
        text-align: left;
        box-shadow: var(--sh-shadow) !important;
        transform: none !important;
        cursor: pointer;
    }
    .complaint-stat:hover,
    .complaint-stat.is-active,
    .complaint-stat.is-active:hover {
        border-color: var(--sh-border) !important;
        background: #fff !important;
        box-shadow: var(--sh-shadow) !important;
        transform: none !important;
    }
    .complaint-stat:focus-visible {
        outline: none;
        border-color: var(--sh-gold) !important;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .08), var(--sh-shadow) !important;
    }
    .complaint-stat > div {
        min-height: 72px !important;
        align-items: flex-start !important;
    }
    .complaint-stat-label {
        color: var(--sh-soft) !important;
        font-size: 11.5px !important;
        font-weight: 500 !important;
        line-height: 1.35 !important;
    }
    .complaint-stat-value {
        margin-top: 7px !important;
        color: var(--sh-ink) !important;
        font-size: 26px !important;
        font-weight: 750 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }
    .complaint-stat-helper {
        margin-top: 7px !important;
        color: var(--sh-muted) !important;
        font-size: 10.5px !important;
        line-height: 1.4 !important;
    }
    /* Bare heading-style icon (like Trending / Popular headers): no tile */
    .complaint-stat-icon,
    .complaint-stat-icon[class*="border"],
    .complaint-stat-icon[class*="bg-"] {
        position: absolute !important;
        top: 16px !important;
        right: 16px !important;
        display: grid !important;
        width: 28px !important;
        height: 28px !important;
        flex: 0 0 28px !important;
        place-items: center;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .complaint-stat-icon svg {
        width: 22px !important;
        height: 22px !important;
        stroke-width: 1.9 !important;
    }
    .complaint-stat[data-summary-status="all"] .complaint-stat-icon { color: var(--sh-gold) !important; }
    .complaint-stat[data-summary-status="open"] .complaint-stat-icon { color: #d97706 !important; }
    .complaint-stat[data-summary-status="resolved"] .complaint-stat-icon { color: #2f9e63 !important; }
    /* ---------------- FILTER DOCK (open controls, no card) ---------------- */
    .complaints-surface.complaints-filter {
        position: relative;
        z-index: 45;
        margin-top: 12px !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
    .complaints-filter-grid {
        display: grid;
        grid-template-columns:
            minmax(320px, 1.65fr)
            minmax(155px, .78fr)
            auto
            auto;
        align-items: center;
        gap: 10px;
    }
    .complaints-search {
        position: relative;
        z-index: 70;
        min-width: 0;
    }
    .complaints-search svg {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 13px;
        width: 16px;
        height: 16px;
        color: var(--sh-icon);
        pointer-events: none;
        transform: translateY(-50%);
        stroke-width: 1.9;
    }
    .complaints-control,
    .complaints-status-trigger {
        width: 100%;
        height: 42px !important;
        min-height: 42px !important;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #333d47 !important;
        font-size: 12px !important;
        font-weight: 650 !important;
        box-shadow: none !important;
    }
    .complaints-search input {
        padding: 0 40px 0 40px !important;
        color: var(--sh-body) !important;
        font-weight: 550 !important;
    }
    .complaints-search input::placeholder {
        color: #79838e !important;
        font-size: 11.5px;
        font-weight: 450;
        opacity: 1;
    }
    .complaints-control:hover,
    .complaints-status-trigger:hover {
        border-color: #cfd5dc !important;
        background: #fbfcfd !important;
    }
    .complaints-search input:focus {
        outline: none !important;
        border-color: #9faab5 !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(75, 85, 99, .10) !important;
    }
    /* Status dropdown */
    .complaints-status-dropdown {
        position: relative;
        min-width: 0;
    }
    .complaints-status-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 12px !important;
        cursor: pointer;
        text-align: left;
    }
    .complaints-status-trigger:focus-visible,
    .complaints-status-dropdown.is-open .complaints-status-trigger {
        outline: none !important;
        border-color: var(--sh-gold) !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .08) !important;
    }
    .complaints-status-trigger-main {
        display: inline-flex;
        min-width: 0;
        align-items: center;
        gap: 8px;
    }
    .complaints-status-trigger-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: #858078;
    }
    .complaints-status-trigger[data-current-status="open"] .complaints-status-trigger-dot { background: var(--sh-gold); }
    .complaints-status-trigger[data-current-status="resolved"] .complaints-status-trigger-dot { background: #3f9a61; }
    .complaints-status-trigger-label {
        overflow: hidden;
        color: #333d47;
        font-weight: 650;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .complaints-status-chevron {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        color: var(--sh-icon);
        stroke-width: 1.9;
        transition: transform .16s ease, color .16s ease;
    }
    .complaints-status-dropdown.is-open .complaints-status-chevron {
        color: #9b6710;
        transform: rotate(180deg);
    }
    .complaints-status-menu {
        position: absolute;
        z-index: 190;
        top: calc(100% + 6px);
        right: 0;
        left: 0;
        max-height: 270px;
        overflow-y: auto;
        padding: 5px;
        border: 1px solid #e1e5e9;
        border-radius: 12px;
        background: #fff;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-3px);
        box-shadow:
            0 18px 42px rgba(31, 41, 55, .13),
            0 4px 12px rgba(31, 41, 55, .04);
        scrollbar-width: thin;
        scrollbar-color: #d9dde2 transparent;
        transition: opacity .15s ease, transform .15s ease, visibility 0s linear .15s;
    }
    .complaints-status-dropdown.is-open .complaints-status-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
        transition: opacity .15s ease, transform .15s ease;
    }
    .complaints-status-option {
        display: flex;
        width: 100%;
        min-height: 39px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        padding: 0 10px;
        color: #3f4852;
        font-size: 11.5px;
        font-weight: 550;
        line-height: 1.2;
        text-align: left;
        cursor: pointer;
    }
    .complaints-status-option:hover,
    .complaints-status-option:focus-visible {
        outline: none;
        background: #f5f6f8;
        color: #1f252c;
    }
    .complaints-status-option.is-selected {
        background: #f7f3eb;
        color: var(--sh-gold-text);
        font-weight: 800;
    }
    .complaints-status-option-left {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .complaints-status-option-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 999px;
        background: #8e857a;
    }
    .complaints-status-option[data-status-value="open"] .complaints-status-option-dot { background: var(--sh-gold); }
    .complaints-status-option[data-status-value="resolved"] .complaints-status-option-dot { background: #3f9a61; }
    .complaints-status-check {
        width: 13px;
        height: 13px;
        opacity: 0;
        color: var(--sh-gold-text);
    }
    .complaints-status-option.is-selected .complaints-status-check { opacity: 1; }
    /* Apply (gold) + Reset (text action, like Shop "Reset") */
    .complaints-apply,
    .complaints-reset {
        display: inline-flex !important;
        height: 42px !important;
        min-height: 42px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px;
        border-radius: 10px !important;
        padding: 0 14px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        white-space: nowrap;
        cursor: pointer;
    }
    .complaints-apply {
        border: 1px solid #d79a28 !important;
        background: var(--sh-gold) !important;
        color: #fff !important;
        box-shadow: 0 5px 12px rgba(200, 137, 18, .14) !important;
    }
    .complaints-apply:hover,
    .complaints-apply:focus-visible {
        outline: none;
        border-color: var(--sh-gold-dark) !important;
        background: var(--sh-gold-dark) !important;
    }
    .complaints-apply svg {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
    }
    .complaints-reset {
        border: 1px solid transparent !important;
        background: transparent !important;
        color: #865709 !important;
        box-shadow: none !important;
    }
    .complaints-reset:hover,
    .complaints-reset:focus-visible {
        outline: none;
        background: transparent !important;
        color: var(--sh-gold) !important;
    }
    /* ---------------- TABLE (same floating surface as cards) ---------------- */
    .complaints-surface.complaints-table-surface {
        position: relative;
        z-index: 1;
        margin-top: 16px !important;
        overflow: hidden !important;
        border: 1px solid var(--sh-border) !important;
        border-radius: 17px !important;
        background: #fff !important;
        box-shadow: var(--sh-shadow) !important;
    }
    .complaints-table-head,
    .complaint-row {
        display: grid;
        grid-template-columns:
            minmax(320px, 1.7fr)
            minmax(185px, .9fr)
            160px
            120px
            64px;
        align-items: center;
        gap: 14px;
    }
    .complaints-table-head {
        min-height: 52px;
        padding: 0 16px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--sh-divider) !important;
        background: #fafbfc !important;
        color: #4b5560 !important;
        font-size: 10.5px !important;
        font-weight: 800 !important;
        line-height: 1.3 !important;
        letter-spacing: .07em !important;
        text-transform: uppercase !important;
    }
    #complaintRows { background: #fff; }
    .complaint-row {
        min-height: 74px;
        padding: 10px 16px !important;
        border-bottom: 1px solid var(--sh-row-divider) !important;
        background: #fff !important;
        box-shadow: none !important;
        content-visibility: auto;
        contain-intrinsic-size: 74px;
    }
    .complaint-row[hidden] { display: none !important; }
    .complaint-row:last-child { border-bottom: 0 !important; }
    .complaint-row:hover { background: #fcfcfd !important; }
    .complaint-row:hover .complaint-subject { color: #9d670c !important; }
    .complaint-subject {
        color: var(--sh-body) !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        line-height: 1.45 !important;
        transition: color .16s ease;
    }
    .complaint-preview {
        max-width: 560px;
        margin-top: 3px !important;
        overflow: hidden;
        color: var(--sh-muted) !important;
        font-size: 10.5px !important;
        font-weight: 500 !important;
        line-height: 1.4 !important;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .complaint-meta-main {
        color: #475467 !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        line-height: 1.4 !important;
    }
    .complaint-meta-sub {
        margin-top: 2px !important;
        color: var(--sh-muted) !important;
        font-size: 10.5px !important;
        font-weight: 500 !important;
        line-height: 1.4 !important;
    }
    .complaint-status {
        display: inline-flex;
        width: fit-content;
        min-height: 26px !important;
        align-items: center;
        gap: 6px !important;
        border-radius: 999px;
        padding: 0 10px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
    }
    .complaint-status::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
    }
    .complaint-status-open {
        border: 1px solid #efd9b0 !important;
        background: #fff8e9 !important;
        color: #a87019 !important;
    }
    .complaint-status-resolved {
        border: 1px solid #d6e9dc !important;
        background: #eef8f1 !important;
        color: #36805a !important;
    }
    .complaint-view {
        display: inline-grid;
        width: 32px !important;
        height: 32px !important;
        place-items: center;
        border: 1px solid transparent !important;
        border-radius: 9px !important;
        background: transparent !important;
        color: var(--sh-arrow) !important;
        box-shadow: none !important;
        cursor: pointer;
    }
    .complaint-view svg {
        width: 15px !important;
        height: 15px !important;
        stroke: currentColor;
    }
    .complaint-row:hover .complaint-view { color: #596470 !important; }
    .complaint-view:hover,
    .complaint-view:focus-visible {
        outline: none;
        border-color: #eadfc9 !important;
        background: #fffaf1 !important;
        color: #9e6a12 !important;
    }
    .complaints-footer {
        display: flex;
        min-height: 54px !important;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 16px !important;
        border-top: 1px solid var(--sh-divider) !important;
        background: #fafbfc !important;
        color: #59636f !important;
        font-size: 11px !important;
        font-weight: 650;
    }
    .complaints-footer p:last-child {
        color: var(--sh-muted) !important;
        font-weight: 500;
    }
    .complaints-empty {
        padding: 44px 16px !important;
        color: var(--sh-muted) !important;
        font-size: 11px !important;
        font-weight: 600;
        text-align: center;
    }
    /* ============================================================
       MODAL — same system as product modal
       (border #e3e6ea, radius 15, shadow 0 30px 80px rgba(31,41,55,.20))
       ============================================================ */
    #complaintModalBackdrop {
        background: rgba(0, 0, 0, .35) !important;
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .18s ease, visibility 0s linear .18s;
    }
    #complaintModalBackdrop.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition: opacity .18s ease;
    }
    #complaintModal {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translate(-50%, -47%) scale(.985);
        transition:
            opacity .18s ease,
            transform .20s cubic-bezier(.22, 1, .36, 1),
            visibility 0s linear .20s;
    }
    #complaintModal.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translate(-50%, -50%) scale(1);
        transition:
            opacity .18s ease,
            transform .20s cubic-bezier(.22, 1, .36, 1);
    }
    .complaint-modal-shell {
        width: min(760px, calc(100vw - 36px)) !important;
        max-height: calc(100vh - 40px) !important;
        overflow: hidden !important;
        border: 1px solid #e3e6ea !important;
        border-radius: 15px !important;
        background: #fff !important;
        box-shadow: 0 30px 80px rgba(31, 41, 55, .20) !important;
    }
    .complaint-modal-header {
        display: flex;
        min-height: 68px !important;
        align-items: center !important;
        justify-content: space-between;
        gap: 12px !important;
        padding: 12px 18px !important;
        border-bottom: 1px solid var(--sh-divider) !important;
        background: #fff !important;
    }
    .complaint-modal-eyebrow {
        margin: 0 !important;
        color: #a87316 !important;
        font-size: 9.5px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase;
    }
    .complaint-modal-title {
        margin: 0 !important;
        color: var(--sh-text) !important;
        font-size: 18px !important;
        font-weight: 750 !important;
        line-height: 1.2 !important;
        letter-spacing: -.03em !important;
    }
    .complaint-modal-close {
        display: grid;
        width: 36px !important;
        height: 36px !important;
        flex: 0 0 36px !important;
        place-items: center;
        border: 1px solid #dfe3e8 !important;
        border-radius: 10px !important;
        background: #fff !important;
        color: #596470 !important;
        cursor: pointer;
    }
    .complaint-modal-close:hover,
    .complaint-modal-close:focus-visible {
        outline: none;
        border-color: #cfd5dc !important;
        background: #f5f6f8 !important;
        color: #1f252c !important;
    }
    .complaint-modal-close svg { width: 14px !important; height: 14px !important; }
    .complaint-modal-body {
        max-height: calc(100vh - 110px) !important;
        overflow-y: auto;
        padding: 16px 18px 18px !important;
        background: #fff !important;
        scrollbar-width: thin;
        scrollbar-color: #d9dde2 transparent;
    }
    .complaint-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px !important;
    }
    .complaint-field-label {
        margin: 0 0 5px !important;
        color: #4b5560 !important;
        font-size: 10.5px !important;
        font-weight: 750 !important;
        letter-spacing: .045em;
        text-transform: uppercase;
    }
    .complaint-field-value {
        display: flex;
        min-height: 42px !important;
        align-items: center;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: #fff !important;
        padding: 0 12px !important;
        color: #333d47 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }
    .complaint-detail-block { margin-top: 14px !important; }
    .complaint-detail-copy {
        min-height: 84px !important;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: #fafbfc !important;
        padding: 12px !important;
        color: #3f4852 !important;
        font-size: 12px !important;
        font-weight: 500;
        line-height: 1.6 !important;
        white-space: pre-wrap;
    }
    .complaint-admin-note {
        border-color: #eadfc9 !important;
        background: #fffaf1 !important;
    }
    .complaint-action-section {
        margin-top: 16px !important;
        border-top: 1px solid var(--sh-divider) !important;
        padding-top: 14px !important;
    }
    .complaint-action-title {
        color: var(--sh-ink) !important;
        font-size: 13px !important;
        font-weight: 750 !important;
    }
    .complaint-action-copy {
        margin-top: 3px !important;
        color: var(--sh-muted) !important;
        font-size: 11px !important;
        line-height: 1.5 !important;
    }
    .complaint-action-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px !important;
        margin-top: 11px !important;
    }
    .complaint-action-input {
        width: 100%;
        min-width: 0;
        height: 42px !important;
        border: 1px solid var(--sh-control-border) !important;
        border-radius: 10px !important;
        background: #fff !important;
        padding: 0 12px !important;
        color: var(--sh-body) !important;
        font-size: 12px !important;
        font-weight: 550;
    }
    .complaint-action-input::placeholder {
        color: #79838e !important;
        font-weight: 450;
        opacity: 1;
    }
    .complaint-action-input:focus {
        outline: none !important;
        border-color: var(--sh-gold) !important;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .08) !important;
    }
    .complaint-resolve,
    .complaint-reopen {
        display: inline-flex;
        height: 42px !important;
        min-height: 42px !important;
        align-items: center;
        justify-content: center;
        border-radius: 10px !important;
        padding: 0 16px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        white-space: nowrap;
        box-shadow: none !important;
        cursor: pointer;
    }
    .complaint-resolve {
        border: 1px solid #cfe2d5 !important;
        background: #f3f9f5 !important;
        color: #3d7a55 !important;
    }
    .complaint-resolve:hover,
    .complaint-resolve:focus-visible {
        outline: none;
        border-color: #b8d6c2 !important;
        background: #eaf5ed !important;
        color: #2f7c48 !important;
    }
    .complaint-reopen {
        border: 1px solid #d79a28 !important;
        background: var(--sh-gold) !important;
        color: #fff !important;
        box-shadow: 0 5px 12px rgba(200, 137, 18, .14) !important;
    }
    .complaint-reopen:hover,
    .complaint-reopen:focus-visible {
        outline: none;
        border-color: var(--sh-gold-dark) !important;
        background: var(--sh-gold-dark) !important;
    }
    /* ---------------- RESPONSIVE ---------------- */
    @media (max-width: 1279px) {
        .complaints-filter-grid {
            grid-template-columns: minmax(0, 1fr) 190px;
        }
        .complaints-search { grid-column: 1 / -1; }
    }
    @media (max-width: 1023px) {
        .complaints-summary-grid { gap: 14px !important; }
        .complaints-table-head { display: none !important; }
        .complaint-row {
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            margin: 10px;
            padding: 14px !important;
            border: 1px solid var(--sh-border) !important;
            border-radius: 14px;
        }
        .complaint-row > div:nth-child(2),
        .complaint-row > div:nth-child(3),
        .complaint-row > div:nth-child(4) { grid-column: 1; }
        .complaint-row > div:last-child {
            grid-column: 2;
            grid-row: 1;
        }
    }
    @media (max-width: 767px) {
        .complaints-page { padding-top: 13px; }
        .complaints-eyebrow { font-size: 9.5px !important; }
        .complaints-title { font-size: 29px !important; }
        .complaints-subtitle { font-size: 10.5px !important; }
        .complaints-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 14px !important;
        }
        .complaint-stat:first-child { grid-column: 1 / -1; }
        .complaint-stat {
            min-height: 96px !important;
            padding: 14px 50px 13px 15px !important;
            border-radius: 14px !important;
        }
        .complaint-stat-value { font-size: 24px !important; }
        .complaint-stat-icon { top: 14px !important; right: 14px !important; }
        .complaints-filter-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .complaints-search { grid-column: auto; }
        .complaints-surface.complaints-table-surface { border-radius: 14px !important; }
        .complaints-footer {
            align-items: flex-start;
            flex-direction: column;
        }
        .complaint-modal-shell {
            width: calc(100vw - 14px) !important;
            max-height: calc(100vh - 14px) !important;
            border-radius: 14px !important;
        }
        .complaint-modal-header { padding: 11px 13px !important; }
        .complaint-modal-body { padding: 12px 13px 14px !important; }
        .complaint-detail-grid { grid-template-columns: 1fr; }
        .complaint-action-form { grid-template-columns: 1fr; }
        .complaint-resolve,
        .complaint-reopen { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) {
        .complaints-page *,
        #complaintModal,
        #complaintModalBackdrop {
            animation: none !important;
            transition-duration: .01ms !important;
            scroll-behavior: auto !important;
        }
        #complaintModal,
        #complaintModal.is-open {
            transform: translate(-50%, -50%) !important;
        }
    }
/* Seller Orders layout: neutral surfaces and consistent readable controls. */
.complaints-page.orders-reference{max-width:1520px!important;padding-top:0!important;--sh-shadow:0 3px 10px rgba(15,23,42,.035),0 12px 30px rgba(15,23,42,.055)}
.complaints-page.orders-reference .complaints-title{font-size:clamp(27px,2.3vw,31px)!important}
.complaints-page.orders-reference .complaints-summary-grid{gap:16px!important;margin-top:20px!important}
.complaints-page.orders-reference .complaint-stat{min-height:118px!important;padding:20px!important;border:1px solid #E5E7EB!important;border-radius:18px!important;background:#FFF!important;box-shadow:var(--sh-shadow)!important;transform:none!important;text-align:left!important}
.complaints-page.orders-reference .complaint-stat::before,.complaints-page.orders-reference .complaint-stat::after{display:none!important}
.complaints-page.orders-reference .complaint-stat-label{font-size:13px!important;font-weight:600!important;color:#637081!important}
.complaints-page.orders-reference .complaint-stat-value{font-size:30px!important;margin-top:8px!important;color:#1F2937!important}
.complaints-page.orders-reference .complaint-stat-helper{font-size:11px!important;color:#8793A1!important}
.complaints-page.orders-reference .complaint-stat-icon{top:20px!important;right:20px!important;background:transparent!important;border:0!important;box-shadow:none!important}
.complaints-page.orders-reference .complaint-stat-icon svg{width:25px!important;height:25px!important}
.complaints-page.orders-reference .complaints-filter{margin-top:20px!important}
.complaints-page.orders-reference .complaints-filter-grid{grid-template-columns:minmax(220px,1fr) 210px auto auto!important;gap:12px!important}
.complaints-page.orders-reference :is(.complaints-control,.complaints-status-trigger,.complaints-apply,.complaints-reset){height:48px!important;min-height:48px!important;border-radius:12px!important;font-size:13px!important;box-shadow:none!important}
.complaints-page.orders-reference .complaints-search input{font-weight:400!important;padding-left:44px!important}
.complaints-page.orders-reference .complaints-search input::placeholder{font-size:12px!important;color:#98A2B3!important}
.complaints-page.orders-reference .complaints-search svg{width:20px!important;height:20px!important;left:15px!important;color:#98A2B3!important}
.complaints-page.orders-reference .complaints-apply{padding:0 20px!important;background:#C88912!important;border:1px solid #C88912!important;color:white!important}
.complaints-page.orders-reference .complaints-reset{padding:0 20px!important;border:1px solid #DCE2E8!important;background:#FFF!important;color:#5F6D7C!important}
.complaints-page.orders-reference .complaints-status-option{min-height:40px!important;font-size:12px!important}
.complaints-page.orders-reference .complaints-status-option.is-selected,.complaints-page.orders-reference .complaints-status-option:hover{background:#F1F5F9!important;color:#344054!important}
.complaints-page.orders-reference .complaints-status-check{color:#64748B!important}
.complaints-page.orders-reference .complaints-table-surface{margin-top:20px!important;border-radius:18px!important}
.complaints-page.orders-reference .complaints-queue-heading{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:22px;border-bottom:1px solid #E9EDF1}
.complaints-page.orders-reference .complaints-queue-heading h3{margin:0;font-size:19px;font-weight:700;color:#293440}
.complaints-page.orders-reference .complaints-queue-heading p{margin:5px 0 0;font-size:12px;color:#8793A1}
.complaints-page.orders-reference .complaints-queue-heading > span{font-size:12px;white-space:nowrap;color:#8793A1}
.complaints-page.orders-reference .complaints-grid-scroll{overflow-x:auto}
.complaints-page.orders-reference .complaints-table-head,.complaints-page.orders-reference .complaint-row{display:grid!important;grid-template-columns:30px minmax(250px,1.7fr) minmax(160px,1fr) 145px 110px 65px!important;gap:14px!important;min-width:930px!important;padding:16px 22px!important}
.complaints-page.orders-reference .complaints-table-head{min-height:48px!important;font-size:11px!important;color:#8591A0!important;background:#F8FAFC!important}
.complaints-page.orders-reference .complaint-row{min-height:88px!important;content-visibility:visible!important}
.complaints-page.orders-reference .complaint-row[hidden]{display:none!important}
.complaints-page.orders-reference .complaint-row:hover{background:#F8FAFC!important}
.complaints-page.orders-reference .complaint-row.is-selected{background:#F1F5F9!important}
.complaints-page.orders-reference .complaint-subject{font-size:13px!important;color:#293440!important;font-weight:600!important}
.complaints-page.orders-reference .complaint-preview,.complaints-page.orders-reference .complaint-meta-sub{font-size:11.5px!important;line-height:1.6!important;color:#8793A1!important}
.complaints-page.orders-reference .complaint-meta-main{font-size:12px!important;color:#344054!important}
.complaints-page.orders-reference .complaint-status{font-size:11px!important;min-height:27px!important}
.complaints-page.orders-reference .complaint-status-open{background:#FFF!important;border-color:#E5C97C!important;color:#996918!important}
.complaints-page.orders-reference .complaint-view{width:36px!important;height:36px!important;border:1px solid #DCE2E8!important;background:white!important;color:#64748B!important}
.complaints-page.orders-reference .complaints-empty{padding:64px 20px!important;font-size:14px!important;text-align:center;color:#8591A0!important}
.complaints-page.orders-reference .complaints-footer{min-height:68px!important;padding:16px 22px!important;flex-wrap:wrap;font-size:12px!important;color:#7B8794!important}
.complaints-page.orders-reference .complaints-pagination{display:flex;align-items:center;gap:6px}
.complaints-page.orders-reference .complaints-pagination button{min-width:36px;height:36px;border:1px solid #E5E7EB;border-radius:9px;background:white;color:#64748B;font:600 12px inherit;cursor:pointer}
.complaints-page.orders-reference .complaints-pagination button[aria-current="page"]{background:#C88912;border-color:#C88912;color:white}
.complaints-page.orders-reference .complaints-pagination button:disabled{opacity:.4;cursor:default}
.complaints-page.orders-reference .complaint-select{width:18px;height:18px;accent-color:#C88912;cursor:pointer}
.complaints-page.orders-reference .complaint-selection{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;padding:12px 22px;background:#F8FAFC;border-bottom:1px solid #E5E7EB;font-size:12px}
.complaints-page.orders-reference .complaint-selection button{padding:7px 12px;border:1px solid #DCE2E8;border-radius:8px;background:white;color:#475467;font-size:12px}
.complaints-page.orders-reference [hidden]{display:none!important}
.complaints-page.orders-reference button:focus-visible,.complaints-page.orders-reference input:focus-visible{outline:2px solid #B18A42;outline-offset:3px}
@media(max-width:1000px){.complaints-page.orders-reference .complaints-filter-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}.complaints-page.orders-reference .complaints-search{grid-column:1 / -1!important}.complaints-page.orders-reference .complaints-reset{grid-column:2!important}.complaints-page.orders-reference .complaints-summary-grid{gap:12px!important}}
@media(max-width:600px){.complaints-page.orders-reference .complaints-summary-grid{grid-template-columns:minmax(0,1fr)!important}.complaints-page.orders-reference .complaint-stat{min-height:110px!important}.complaints-page.orders-reference .complaints-filter-grid{grid-template-columns:minmax(0,1fr)!important}.complaints-page.orders-reference .complaints-reset,.complaints-page.orders-reference .complaints-search{grid-column:auto!important}.complaints-page.orders-reference .complaints-queue-heading{padding:18px 16px}.complaints-page.orders-reference .complaints-footer{padding:14px 16px!important}.complaints-page.orders-reference .complaints-pagination{margin-left:auto}}

</style>
@php
    $complaintCount = $complaints->count();
    $statusTone = fn ($status) => strtolower((string) $status) === 'resolved' ? 'complaint-status-resolved' : 'complaint-status-open';
@endphp
<div class="complaints-page orders-reference mx-auto w-full max-w-[1880px] pb-8">
    @if(session('success'))
        <div class="mb-4 rounded-[14px] border border-[#d5e6da] bg-[#f4faf6] px-4 py-3 text-[9px] text-[#426b50]">{{ session('success') }}</div>
    @endif
    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="complaints-header-main">
            <span class="complaints-header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v11H8l-3 3V5Z"></path><path d="M9 9h6"></path><path d="M9 12h4"></path></svg></span>
            <div class="min-w-0"><p class="complaints-eyebrow">Platform Support</p><h2 class="complaints-title"><span class="complaints-title-base">Platform</span> <span class="complaints-title-accent">Complaints</span></h2><p class="complaints-subtitle"></p></div>
        </div>
    </section>
    <section class="complaints-summary-grid mt-4">
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="all"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#efdfbf] bg-[#fff7e8] text-[#b98112]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v11H8l-3 3V5Z"></path><path d="M9 9h6"></path><path d="M9 12h4"></path></svg></span><div><p class="complaint-stat-label">Total Complaints</p><p class="complaint-stat-value">{{ $stats['total'] ?? $complaintCount }}</p><p class="complaint-stat-helper">All submitted complaints</p></div></div></button>
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="open"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#f0dfd0] bg-[#fff5ed] text-[#c26f20]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5"></path><path d="M12 16h.01"></path></svg></span><div><p class="complaint-stat-label">Open Cases</p><p class="complaint-stat-value">{{ $stats['open'] ?? 0 }}</p><p class="complaint-stat-helper">Needs admin resolution</p></div></div></button>
        <button type="button" class="complaint-stat relative pr-20" data-summary-status="resolved"><div class="flex h-full items-center gap-4"><span class="complaint-stat-icon absolute right-[18px] top-[18px] border border-[#d8ebde] bg-[#eef8f1] text-[#298b53]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg></span><div><p class="complaint-stat-label">Resolved</p><p class="complaint-stat-value">{{ $stats['resolved'] ?? 0 }}</p><p class="complaint-stat-helper">Completed complaint cases</p></div></div></button>
    </section>
    <section class="complaints-surface complaints-filter mt-4"><div class="complaints-filter-grid">
        <div class="complaints-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg><input id="complaintSearch" type="search" autocomplete="off" class="complaints-control" placeholder="Search subject, reporter, description, or note..."></div>
        <div class="complaints-status-dropdown" id="complaintStatusDropdown">
            <input id="complaintStatusFilter" type="hidden" value="all">
            <button id="complaintStatusTrigger" type="button" class="complaints-status-trigger" data-current-status="all" aria-haspopup="listbox" aria-expanded="false">
                <span class="complaints-status-trigger-main">
                    <span class="complaints-status-trigger-dot" aria-hidden="true"></span>
                    <span id="complaintStatusLabel" class="complaints-status-trigger-label">All Status</span>
                </span>
                <svg viewBox="0 0 24 24" class="complaints-status-chevron" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 10 5 5 5-5"></path></svg>
            </button>
            <div id="complaintStatusMenu" class="complaints-status-menu" role="listbox" aria-label="Filter complaints by status">
                <button type="button" class="complaints-status-option is-selected" data-status-value="all" role="option" aria-selected="true"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>All Status</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
                <button type="button" class="complaints-status-option" data-status-value="open" role="option" aria-selected="false"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>Open</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
                <button type="button" class="complaints-status-option" data-status-value="resolved" role="option" aria-selected="false"><span class="complaints-status-option-left"><span class="complaints-status-option-dot"></span><span>Resolved</span></span><svg viewBox="0 0 24 24" class="complaints-status-check" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 12 3 3 7-7"></path></svg></button>
            </div>
        </div>
        <button id="complaintApplyFilter" type="button" class="complaints-apply"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>Apply Filter</button>
        <button id="complaintResetFilter" type="button" class="complaints-reset">Reset</button>
    </div></section>
    <section class="complaints-surface complaints-table-surface mt-4 overflow-hidden">
        <div class="complaints-queue-heading"><div><h3>Complaint Records</h3><p>Review submitted complaints and their resolution status.</p></div><span id="complaintQueueCount">{{ $complaintCount }} records</span></div>
<div class="complaint-selection" id="complaintSelection" hidden><span id="complaintSelectedCount" role="status"></span><div><button type="button" id="complaintReviewSelected">Review first selected</button> <button type="button" id="complaintClearSelected">Clear selection</button></div></div>
<div class="complaints-grid-scroll" tabindex="0" role="region" aria-label="Complaint records">
<div class="complaints-table-head"><div><input class="complaint-select" type="checkbox" id="complaintSelectPage" aria-label="Select complaints on this page"></div><div>Complaint</div><div>Reporter</div><div>Submitted</div><div>Status</div><div class="text-right">Action</div></div>
        <div id="complaintRows">
        @forelse($complaints as $complaint)
            @php
                $complaintStatus = strtolower((string) $complaint->status);
                $reporterRole = strtoupper((string) $complaint->reporter_role);
                $reporter = $complaint->reporter_identifier ?: 'Account';
            @endphp
            <article class="complaint-row" data-complaint-row data-subject="{{ $complaint->subject }}" data-description="{{ $complaint->description }}" data-reporter-role="{{ $reporterRole }}" data-reporter="{{ $reporter }}" data-status="{{ $complaintStatus }}" data-submitted="{{ $complaint->created_at?->format('M d, Y h:i A') }}" data-admin-note="{{ $complaint->admin_note ?? '' }}" data-resolve-url="{{ $complaintStatus === 'open' ? route('admin.complaints.resolve',$complaint) : '' }}" data-reopen-url="{{ $complaintStatus !== 'open' ? route('admin.complaints.reopen',$complaint) : '' }}">
                <div><input class="complaint-select" type="checkbox" data-complaint-select aria-label="Select {{ $complaint->subject }}"></div><div class="min-w-0"><p class="complaint-subject truncate">{{ $complaint->subject }}</p><p class="complaint-preview">{{ $complaint->description }}</p></div>
                <div><p class="complaint-meta-main">{{ $reporter }}</p><p class="complaint-meta-sub">{{ $reporterRole ?: 'ACCOUNT' }}</p></div>
                <div><p class="complaint-meta-main">{{ $complaint->created_at?->format('M d, Y') ?: '—' }}</p><p class="complaint-meta-sub">{{ $complaint->created_at?->format('h:i A') }}</p></div>
                <div><span class="complaint-status {{ $statusTone($complaintStatus) }}">{{ ucfirst($complaintStatus) }}</span></div>
                <div class="flex justify-end"><button type="button" class="complaint-view" data-view-complaint title="View complaint" aria-label="View complaint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6S2.5 12 2.5 12Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg></button></div>
            </article>
        @empty
            <div class="complaints-empty">No complaints submitted.</div>
        @endforelse
        </div>
        </div><div class="complaints-footer"><p id="complaintResultCount">Showing {{ $complaintCount }} of {{ $complaintCount }} complaints</p><nav class="complaints-pagination" id="complaintPagination" aria-label="Complaint pages"></nav></div>
    </section>
</div>
<div id="complaintModalBackdrop" class="fixed inset-0 z-[90] bg-[#1f1d1a]/45" aria-hidden="true"></div>
<div id="complaintModal" class="fixed left-1/2 top-1/2 z-[100]" role="dialog" aria-modal="true" aria-labelledby="complaintModalTitle" aria-hidden="true">
    <div class="complaint-modal-shell">
        <div class="complaint-modal-header"><div class="min-w-0"><p class="complaint-modal-eyebrow">Complaint details</p><div class="mt-1 flex flex-wrap items-center gap-2"><h3 id="complaintModalTitle" class="complaint-modal-title">Complaint</h3><span id="complaintModalStatus" class="complaint-status complaint-status-open">Open</span></div></div><button id="complaintModalClose" type="button" class="complaint-modal-close" aria-label="Close"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg></button></div>
        <div class="complaint-modal-body">
            <div class="complaint-detail-grid"><div><p class="complaint-field-label">Reporter</p><div id="complaintModalReporter" class="complaint-field-value">—</div></div><div><p class="complaint-field-label">Submitted</p><div id="complaintModalSubmitted" class="complaint-field-value">—</div></div></div>
            <div class="complaint-detail-block"><p class="complaint-field-label">Description</p><div id="complaintModalDescription" class="complaint-detail-copy">—</div></div>
            <div id="complaintModalNoteWrap" class="complaint-detail-block hidden"><p class="complaint-field-label">Admin resolution note</p><div id="complaintModalNote" class="complaint-detail-copy complaint-admin-note">—</div></div>
            <section class="complaint-action-section"><p class="complaint-action-title">Case action</p><p id="complaintActionCopy" class="complaint-action-copy">Record a resolution note before closing this complaint.</p>
                <form id="complaintResolveForm" method="POST" class="complaint-action-form">@csrf<input id="complaintResolutionNote" name="admin_note" required placeholder="Resolution note..." class="complaint-action-input"><button type="submit" class="complaint-resolve">Resolve Complaint</button></form>
                <form id="complaintReopenForm" method="POST" class="mt-3 hidden">@csrf<button type="submit" class="complaint-reopen">Reopen Complaint</button></form>
            </section>
        </div>
    </div>
</div>
<script>
(function(){
    const rows=[...document.querySelectorAll('[data-complaint-row]')],search=document.getElementById('complaintSearch'),statusFilter=document.getElementById('complaintStatusFilter'),resultCount=document.getElementById('complaintResultCount'),summaryCards=[...document.querySelectorAll('[data-summary-status]')];
    const statusDropdown=document.getElementById('complaintStatusDropdown'),statusTrigger=document.getElementById('complaintStatusTrigger'),statusLabel=document.getElementById('complaintStatusLabel'),statusOptions=[...document.querySelectorAll('[data-status-value]')];
    const modal=document.getElementById('complaintModal'),backdrop=document.getElementById('complaintModalBackdrop'),resolveForm=document.getElementById('complaintResolveForm'),reopenForm=document.getElementById('complaintReopenForm');
    const normalize=v=>(v||'').toString().trim().toLowerCase();
    // Performance: normalize searchable complaint data once.
    const complaintIndex=rows.map(row=>({
        row,
        status:normalize(row.dataset.status),
        searchText:normalize([
            row.dataset.subject,
            row.dataset.description,
            row.dataset.reporterRole,
            row.dataset.reporter,
            row.dataset.adminNote
        ].join(' '))
    }));
    function debounce(callback,wait=130){
        let timer=0;
        return function(...args){
            window.clearTimeout(timer);
            timer=window.setTimeout(()=>callback.apply(this,args),wait);
        };
    }
    let filterFrame=0, currentPage=1;
    const PAGE_SIZE=10, selectedRows=new Set();
    let pageRows=[];
    const selectPage=document.getElementById('complaintSelectPage');
    function renderRows(){
        const q=normalize(search?.value), status=normalize(statusFilter?.value||'all');
        const matches=complaintIndex.filter(item=>(status==='all'||item.status===status)&&(!q||item.searchText.includes(q)));
        const pages=Math.max(1,Math.ceil(matches.length/PAGE_SIZE));
        currentPage=Math.min(Math.max(1,currentPage),pages);
        const offset=(currentPage-1)*PAGE_SIZE;
        pageRows=matches.slice(offset,offset+PAGE_SIZE).map(item=>item.row);
        rows.forEach(row=>{row.hidden=!pageRows.includes(row);row.classList.toggle('is-selected',selectedRows.has(row));row.querySelector('[data-complaint-select]').checked=selectedRows.has(row)});
        const checked=pageRows.filter(row=>selectedRows.has(row)).length;
        selectPage.checked=pageRows.length>0&&checked===pageRows.length;
        selectPage.indeterminate=checked>0&&checked<pageRows.length;
        selectPage.disabled=pageRows.length===0;
        document.getElementById('complaintSelection').hidden=selectedRows.size===0;
        document.getElementById('complaintSelectedCount').textContent=selectedRows.size+' selected';
        if(resultCount)resultCount.textContent=matches.length?`Showing ${offset+1}–${offset+pageRows.length} of ${matches.length} complaints`:'Showing 0 of 0 complaints';
        document.getElementById('complaintQueueCount').textContent=matches.length+' records';
        let empty=document.getElementById('complaintFilteredEmpty');
        if(!empty){empty=document.createElement('div');empty.id='complaintFilteredEmpty';empty.className='complaints-empty';empty.textContent='No complaints match your filters.';document.getElementById('complaintRows').appendChild(empty)}
        empty.hidden=matches.length>0||rows.length===0;
        const nav=document.getElementById('complaintPagination');nav.replaceChildren();nav.hidden=pages<=1;
        function pageButton(label,number,disabled,active){const b=document.createElement('button');b.type='button';b.textContent=label;b.disabled=disabled;b.setAttribute('aria-label',label==='Previous'||label==='Next'?label+' page':'Page '+number);if(active)b.setAttribute('aria-current','page');b.addEventListener('click',()=>{currentPage=number;renderRows()});nav.appendChild(b)}
        pageButton('Previous',currentPage-1,currentPage===1,false);
        const first=Math.max(1,Math.min(currentPage-2,pages-4));
        for(let n=first;n<=Math.min(pages,first+4);n++)pageButton(String(n),n,false,n===currentPage);
        pageButton('Next',currentPage+1,currentPage===pages,false);
        summaryCards.forEach(card=>card.classList.toggle('is-active',normalize(card.dataset.summaryStatus)===status));
    }
    function applyFilters(){currentPage=1;window.cancelAnimationFrame(filterFrame);filterFrame=window.requestAnimationFrame(renderRows)}
    rows.forEach(row=>row.querySelector('[data-complaint-select]').addEventListener('change',event=>{if(event.target.checked)selectedRows.add(row);else selectedRows.delete(row);renderRows()}));
    selectPage.addEventListener('change',()=>{pageRows.forEach(row=>{if(selectPage.checked)selectedRows.add(row);else selectedRows.delete(row)});renderRows()});
    document.getElementById('complaintClearSelected').addEventListener('click',()=>{selectedRows.clear();renderRows()});
    // Selection assists review only; resolve/reopen still use the original per-case forms.
    document.getElementById('complaintReviewSelected').addEventListener('click',()=>openModal(selectedRows.values().next().value));
    const debouncedApplyFilters=debounce(applyFilters,130);
    function syncStatusDropdown(value){const target=(value||'all').toString().toLowerCase(),active=statusOptions.find(option=>(option.dataset.statusValue||'all').toLowerCase()===target)||statusOptions[0];if(statusFilter)statusFilter.value=active?.dataset.statusValue||'all';if(statusLabel)statusLabel.textContent=active?.querySelector('.complaints-status-option-left span:last-child')?.textContent?.trim()||'All Status';if(statusTrigger)statusTrigger.dataset.currentStatus=active?.dataset.statusValue||'all';statusOptions.forEach(option=>{const selected=option===active;option.classList.toggle('is-selected',selected);option.setAttribute('aria-selected',selected?'true':'false')})}
    function closeStatusDropdown(){statusDropdown?.classList.remove('is-open');statusTrigger?.setAttribute('aria-expanded','false')}
    function resetFilters(){if(search)search.value='';syncStatusDropdown('all');applyFilters()}
    statusTrigger?.addEventListener('click',event=>{event.stopPropagation();const open=!statusDropdown?.classList.contains('is-open');statusDropdown?.classList.toggle('is-open',open);statusTrigger.setAttribute('aria-expanded',open?'true':'false')});
    statusOptions.forEach(option=>option.addEventListener('click',()=>{syncStatusDropdown(option.dataset.statusValue||'all');closeStatusDropdown();applyFilters()}));
    document.addEventListener('click',event=>{if(!event.target.closest('#complaintStatusDropdown'))closeStatusDropdown()});
    search?.addEventListener('input',debouncedApplyFilters,{passive:true});
    document.getElementById('complaintApplyFilter')?.addEventListener('click',applyFilters);
    document.getElementById('complaintResetFilter')?.addEventListener('click',resetFilters);
    summaryCards.forEach(card=>card.addEventListener('click',()=>{
        syncStatusDropdown(card.dataset.summaryStatus||'all');
        applyFilters();
    }));
    function openModal(row){if(!row)return;const status=normalize(row.dataset.status);document.getElementById('complaintModalTitle').textContent=row.dataset.subject||'Complaint';document.getElementById('complaintModalReporter').textContent=`${row.dataset.reporter||'Account'} · ${row.dataset.reporterRole||'ACCOUNT'}`;document.getElementById('complaintModalSubmitted').textContent=row.dataset.submitted||'—';document.getElementById('complaintModalDescription').textContent=row.dataset.description||'No description provided.';const noteWrap=document.getElementById('complaintModalNoteWrap'),note=document.getElementById('complaintModalNote');if(row.dataset.adminNote){note.textContent=row.dataset.adminNote;noteWrap.classList.remove('hidden')}else{noteWrap.classList.add('hidden')}const badge=document.getElementById('complaintModalStatus');badge.textContent=status==='resolved'?'Resolved':'Open';badge.className='complaint-status '+(status==='resolved'?'complaint-status-resolved':'complaint-status-open');if(status==='open'){resolveForm.action=row.dataset.resolveUrl||'';resolveForm.classList.remove('hidden');reopenForm.classList.add('hidden');document.getElementById('complaintActionCopy').textContent='Record a resolution note before closing this complaint.'}else{reopenForm.action=row.dataset.reopenUrl||'';reopenForm.classList.remove('hidden');resolveForm.classList.add('hidden');document.getElementById('complaintActionCopy').textContent='Reopen this complaint if the case requires additional review.'}modal.classList.add('is-open');backdrop.classList.add('is-open');modal.setAttribute('aria-hidden','false');backdrop.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';window.requestAnimationFrame(()=>document.getElementById('complaintModalClose')?.focus({preventScroll:true}))}
    function closeModal(){modal?.classList.remove('is-open');backdrop?.classList.remove('is-open');modal?.setAttribute('aria-hidden','true');backdrop?.setAttribute('aria-hidden','true');document.body.style.overflow=''}
    document.addEventListener('click',event=>{
        const viewButton=event.target.closest('[data-view-complaint]');
        if(viewButton){
            openModal(viewButton.closest('[data-complaint-row]'));
            return;
        }
        if(event.target.closest('#complaintModalClose')){
            closeModal();
            return;
        }
        if(event.target===backdrop){
            closeModal();
        }
    });
    document.addEventListener('keydown',e=>{
        if(e.key==='Escape')closeModal();
    });
    syncStatusDropdown(statusFilter?.value||'all');
    applyFilters();
})();
</script>
@endsection