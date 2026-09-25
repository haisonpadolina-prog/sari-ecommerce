@extends('layouts.admin')

@section('title','Complaints — SARI Admin')
@section('page-title','Complaints')

@section('content')
<style>
    /* ============================================================
       SARI ADMIN — COMPLAINTS & DISPUTES
       Enterprise compact UI + low-paint performance layer
       ============================================================ */

    .complaints-page {
        --cp-gold: #d99500;
        --cp-gold-dark: #bd8205;
        --cp-ink: #26211c;
        --cp-text: #514a42;
        --cp-muted: #8d8479;
        --cp-line: #e8e1d8;
        --cp-soft: #faf9f6;
        --cp-success: #4f8060;
        --cp-danger: #a65d5d;

        width: 100%;
        max-width: 1640px !important;
        margin-inline: auto;
        padding-bottom: 20px;
        color: var(--cp-ink);
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .complaints-page *,
    .complaints-page *::before,
    .complaints-page *::after {
        box-sizing: border-box;
    }

    .complaints-page button,
    .complaints-page input,
    .complaints-page a,
    .complaints-status-menu,
    #complaintModal,
    #complaintModalBackdrop {
        transition:
            color .15s ease,
            background-color .15s ease,
            border-color .15s ease,
            opacity .15s ease,
            transform .15s ease;
    }

    /* ---------------- PAGE HEADER ---------------- */
    .complaints-header-main {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px !important;
    }

    .complaints-header-icon {
        display: grid;
        width: 36px !important;
        height: 36px !important;
        flex: 0 0 36px !important;
        place-items: center;
        border: 1px solid #eadfc9 !important;
        border-radius: 10px !important;
        background: #fff8eb !important;
        color: #b77c18 !important;
        box-shadow: 0 4px 12px rgba(75,54,25,.045) !important;
    }

    .complaints-header-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .complaints-eyebrow {
        margin: 0 !important;
        color: #9a7b43 !important;
        font-size: 7px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase;
    }

    .complaints-title {
        margin: 3px 0 0 !important;
        font-size: clamp(22px, 1.55vw, 27px) !important;
        font-weight: 700 !important;
        line-height: 1.08 !important;
        letter-spacing: -.035em !important;
    }

    .complaints-title-base {
        color: #17130f !important;
    }

    .complaints-title-accent {
        color: var(--cp-gold) !important;
    }

    .complaints-subtitle {
        max-width: 780px !important;
        margin-top: 5px !important;
        color: #81786c !important;
        font-size: 9.5px !important;
        line-height: 1.55 !important;
    }

    /* ---------------- SHARED SURFACES ---------------- */
    .complaints-surface {
        border: 1px solid var(--cp-line) !important;
        border-radius: 14px !important;
        background: #fff !important;
        box-shadow: 0 6px 20px rgba(61,43,22,.045) !important;
    }

    /* ---------------- KPI CARDS ---------------- */
    .complaints-summary-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 9px !important;
        margin-top: 11px !important;
    }

    .complaint-stat {
        position: relative !important;
        min-height: 76px !important;
        border: 1px solid var(--cp-line) !important;
        border-radius: 13px !important;
        background: #fff !important;
        padding: 11px 50px 11px 13px !important;
        text-align: left;
        box-shadow: 0 6px 18px rgba(61,43,22,.045) !important;
        contain: paint;
    }

    .complaint-stat:hover {
        border-color: #ddcfbb !important;
        background: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(61,43,22,.06) !important;
    }

    .complaint-stat.is-active {
        border-color: #dfbd78 !important;
        background: #fffdf8 !important;
    }

    .complaint-stat > div {
        min-height: 52px !important;
        align-items: center !important;
    }

    .complaint-stat-icon {
        position: absolute !important;
        top: 12px !important;
        right: 12px !important;
        display: grid;
        width: 32px !important;
        height: 32px !important;
        flex: 0 0 32px !important;
        place-items: center;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    .complaint-stat-icon svg {
        width: 14px !important;
        height: 14px !important;
    }

    .complaint-stat-label {
        color: #8e857a !important;
        font-size: 8px !important;
        font-weight: 500 !important;
        line-height: 1.3 !important;
    }

    .complaint-stat-value {
        margin-top: 4px !important;
        color: #28221b !important;
        font-size: 19px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .complaint-stat-helper {
        margin-top: 6px !important;
        color: #9b9288 !important;
        font-size: 7.3px !important;
        line-height: 1.35 !important;
    }

    /* ---------------- FILTER TOOLBAR ---------------- */
    .complaints-filter {
        position: relative;
        z-index: 30;
        margin-top: 11px !important;
        padding: 9px !important;
        overflow: visible !important;
    }

    .complaints-filter-grid {
        display: grid;
        grid-template-columns: minmax(320px, 1fr) 170px 110px 72px;
        align-items: center;
        gap: 8px !important;
    }

    .complaints-search {
        position: relative;
        min-width: 0;
    }

    .complaints-search svg {
        position: absolute;
        z-index: 2;
        top: 50%;
        left: 12px !important;
        width: 14px !important;
        height: 14px !important;
        pointer-events: none;
        color: #9b9287 !important;
        transform: translateY(-50%);
    }

    .complaints-control,
    .complaints-status-trigger {
        width: 100%;
        min-height: 38px !important;
        height: 38px !important;
        border: 1px solid #e5ddd2 !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #3d3730 !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 9px !important;
        font-weight: 400 !important;
        box-shadow: none !important;
    }

    .complaints-search input {
        padding: 0 10px 0 36px !important;
    }

    .complaints-search input::placeholder {
        color: #a59c91 !important;
        opacity: 1;
    }

    .complaints-control:hover,
    .complaints-status-trigger:hover {
        border-color: #d4c5b4 !important;
    }

    .complaints-control:focus,
    .complaints-status-trigger:focus-visible,
    .complaints-status-dropdown.is-open .complaints-status-trigger {
        outline: none !important;
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217,149,0,.075) !important;
    }

    .complaints-status-dropdown {
        position: relative;
        min-width: 0;
    }

    .complaints-status-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 10px !important;
        cursor: pointer;
        font-weight: 500 !important;
    }

    .complaints-status-trigger-main {
        display: inline-flex;
        min-width: 0;
        align-items: center;
        gap: 7px !important;
    }

    .complaints-status-trigger-dot {
        width: 6px !important;
        height: 6px !important;
        flex: 0 0 6px !important;
        border-radius: 999px;
        background: #858078;
    }

    .complaints-status-trigger[data-current-status="open"] .complaints-status-trigger-dot {
        background: var(--cp-gold);
    }

    .complaints-status-trigger[data-current-status="resolved"] .complaints-status-trigger-dot {
        background: #3f9a61;
    }

    .complaints-status-trigger-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .complaints-status-chevron {
        width: 12px !important;
        height: 12px !important;
        flex: 0 0 12px !important;
        color: #8b8175 !important;
    }

    .complaints-status-dropdown.is-open .complaints-status-chevron {
        transform: rotate(180deg);
    }

    .complaints-status-menu {
        position: absolute;
        z-index: 80;
        top: calc(100% + 5px) !important;
        right: 0;
        left: 0;
        padding: 4px !important;
        border: 1px solid #e4dcd1 !important;
        border-radius: 10px !important;
        background: #fff !important;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(47,37,25,.12) !important;
    }

    .complaints-status-dropdown.is-open .complaints-status-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    .complaints-status-option {
        display: flex;
        width: 100%;
        min-height: 31px !important;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        border: 0;
        border-radius: 7px !important;
        background: transparent;
        padding: 0 8px !important;
        color: #5b534a !important;
        font-size: 8.3px !important;
        font-weight: 500 !important;
        text-align: left;
    }

    .complaints-status-option:hover,
    .complaints-status-option:focus-visible,
    .complaints-status-option.is-selected {
        outline: none;
        background: #fff7e8 !important;
        color: #9a6810 !important;
    }

    .complaints-status-option-left {
        display: inline-flex;
        align-items: center;
        gap: 7px !important;
    }

    .complaints-status-option-dot {
        width: 6px !important;
        height: 6px !important;
        flex: 0 0 6px !important;
        border-radius: 999px;
        background: #8e857a;
    }

    .complaints-status-option[data-status-value="open"] .complaints-status-option-dot {
        background: var(--cp-gold);
    }

    .complaints-status-option[data-status-value="resolved"] .complaints-status-option-dot {
        background: #3f9a61;
    }

    .complaints-status-check {
        width: 12px !important;
        height: 12px !important;
        opacity: 0;
        color: var(--cp-gold) !important;
    }

    .complaints-status-option.is-selected .complaints-status-check {
        opacity: 1;
    }

    .complaints-apply,
    .complaints-reset {
        display: inline-flex !important;
        width: 100%;
        min-height: 38px !important;
        height: 38px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        border-radius: 9px !important;
        padding: 0 10px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 8.2px !important;
        font-weight: 600 !important;
        line-height: 1 !important;
        white-space: nowrap;
    }

    .complaints-apply {
        border: 1px solid var(--cp-gold) !important;
        background: var(--cp-gold) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(217,149,0,.11) !important;
    }

    .complaints-apply:hover,
    .complaints-apply:focus-visible {
        outline: none;
        border-color: var(--cp-gold-dark) !important;
        background: var(--cp-gold-dark) !important;
        transform: translateY(-1px);
    }

    .complaints-apply svg {
        width: 12px !important;
        height: 12px !important;
        flex: 0 0 12px;
    }

    .complaints-reset {
        border: 1px solid #e5ddd2 !important;
        background: #fff !important;
        color: #6f665b !important;
        box-shadow: none !important;
    }

    .complaints-reset:hover,
    .complaints-reset:focus-visible {
        outline: none;
        border-color: #d4c5b4 !important;
        background: #faf8f4 !important;
        color: #514940 !important;
    }

    /* ---------------- COMPLAINT TABLE ---------------- */
    .complaints-table-surface {
        margin-top: 10px !important;
        overflow: hidden !important;
        border-radius: 14px !important;
    }

    .complaints-table-head,
    .complaint-row {
        display: grid;
        grid-template-columns:
            minmax(285px, 1.65fr)
            minmax(170px, .9fr)
            150px
            112px
            56px;
        align-items: center;
        gap: 12px !important;
    }

    .complaints-table-head {
        min-height: 40px !important;
        padding: 9px 14px !important;
        border: 0 !important;
        border-bottom: 1px solid #eee8df !important;
        background: #faf9f6 !important;
        color: #81786d !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
        letter-spacing: .055em !important;
        text-transform: uppercase !important;
    }

    #complaintRows {
        background: #fff !important;
    }

    .complaint-row {
        min-height: 60px !important;
        padding: 9px 14px !important;
        border-bottom: 1px solid #f0ebe4 !important;
        background: #fff !important;
        box-shadow: none !important;

        /* Native browser rendering optimization for long lists. */
        content-visibility: auto;
        contain-intrinsic-size: 60px;
    }

    .complaint-row:last-child {
        border-bottom: 0 !important;
    }

    .complaint-row:hover {
        background: #fdfbf8 !important;
    }

    .complaint-subject {
        color: #2e2924 !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
    }

    .complaint-preview {
        max-width: 540px;
        margin-top: 3px !important;
        overflow: hidden;
        color: #978e83 !important;
        font-size: 7.2px !important;
        line-height: 1.4 !important;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .complaint-meta-main {
        color: #514a42 !important;
        font-size: 8px !important;
        font-weight: 500 !important;
        line-height: 1.4 !important;
    }

    .complaint-meta-sub {
        margin-top: 2px !important;
        color: #978e83 !important;
        font-size: 7px !important;
        line-height: 1.4 !important;
    }

    .complaint-status {
        display: inline-flex;
        width: fit-content;
        min-height: 22px !important;
        align-items: center;
        gap: 5px !important;
        border-radius: 999px;
        padding: 0 7px !important;
        font-size: 7px !important;
        font-weight: 600 !important;
        line-height: 1 !important;
    }

    .complaint-status::before {
        content: "";
        width: 5px !important;
        height: 5px !important;
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
        width: 28px !important;
        height: 28px !important;
        place-items: center;
        border: 0;
        border-radius: 7px !important;
        background: transparent;
        color: #4d4842 !important;
    }

    .complaint-view svg {
        width: 14px !important;
        height: 14px !important;
        stroke: currentColor;
    }

    .complaint-view:hover,
    .complaint-view:focus-visible {
        outline: none;
        background: #fff7e8 !important;
        color: var(--cp-gold) !important;
        transform: translateY(-1px);
    }

    .complaints-footer {
        display: flex;
        min-height: 48px !important;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 9px 14px !important;
        border-top: 1px solid #eee8df !important;
        background: #fff !important;
        color: #756d63 !important;
        font-size: 7.5px !important;
    }

    .complaints-empty {
        padding: 34px 16px !important;
        color: #918677 !important;
        font-size: 8px !important;
        text-align: center;
    }

    /* ============================================================
       CENTERED ENTERPRISE COMPLAINT MODAL
       ============================================================ */
    #complaintModalBackdrop {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        background: rgba(28,24,20,.42) !important;
        transition: opacity .18s ease, visibility 0s linear .18s;
    }

    #complaintModalBackdrop.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition: opacity .18s ease;
    }

    #complaintModal {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translate(-50%, -47%) scale(.985);
        transition:
            opacity .18s ease,
            transform .20s cubic-bezier(.22,1,.36,1),
            visibility 0s linear .20s;
    }

    #complaintModal.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translate(-50%, -50%) scale(1);
        transition:
            opacity .18s ease,
            transform .20s cubic-bezier(.22,1,.36,1);
    }

    .complaint-modal-shell {
        width: min(720px, calc(100vw - 36px)) !important;
        max-height: calc(100vh - 40px) !important;
        overflow: hidden !important;
        border: 1px solid #dfd8cf !important;
        border-radius: 16px !important;
        background: #f8f7f4 !important;
        box-shadow:
            0 24px 64px rgba(31,24,17,.18),
            0 8px 22px rgba(31,24,17,.07) !important;
    }

    .complaint-modal-header {
        display: flex;
        min-height: 58px !important;
        align-items: center !important;
        justify-content: space-between;
        gap: 12px !important;
        padding: 10px 14px !important;
        border-bottom: 1px solid #ebe5dd !important;
        background: #fff !important;
    }

    .complaint-modal-eyebrow {
        margin: 0 !important;
        color: #9a7b43 !important;
        font-size: 6.5px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        letter-spacing: .11em !important;
        text-transform: uppercase;
    }

    .complaint-modal-title {
        margin-top: 3px !important;
        color: #25221e !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        letter-spacing: -.025em !important;
    }

    .complaint-modal-close {
        display: grid;
        width: 30px !important;
        height: 30px !important;
        flex: 0 0 30px !important;
        place-items: center;
        border: 1px solid #e4ddd4 !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #71685f !important;
    }

    .complaint-modal-close:hover,
    .complaint-modal-close:focus-visible {
        outline: none;
        background: #f7f5f2 !important;
        color: #332d27 !important;
    }

    .complaint-modal-close svg {
        width: 13px !important;
        height: 13px !important;
    }

    .complaint-modal-body {
        max-height: calc(100vh - 100px) !important;
        overflow-y: auto;
        padding: 12px 14px 14px !important;
        background: #f8f7f4 !important;
        scrollbar-width: thin;
        scrollbar-color: #d0c8be transparent;
    }

    .complaint-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px !important;
    }

    .complaint-field-label {
        margin-bottom: 4px !important;
        color: #746b62 !important;
        font-size: 7px !important;
        font-weight: 600 !important;
    }

    .complaint-field-value {
        display: flex;
        min-height: 36px !important;
        align-items: center;
        border: 1px solid #e3ddd5 !important;
        border-radius: 8px !important;
        background: #fff !important;
        padding: 0 10px !important;
        color: #403a34 !important;
        font-size: 8px !important;
        font-weight: 500 !important;
    }

    .complaint-detail-block {
        margin-top: 10px !important;
    }

    .complaint-detail-copy {
        min-height: 72px !important;
        border: 1px solid #e3ddd5 !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 9px 10px !important;
        color: #5d554d !important;
        font-size: 8px !important;
        line-height: 1.55 !important;
        white-space: pre-wrap;
    }

    .complaint-admin-note {
        background: #fffdf8 !important;
    }

    .complaint-action-section {
        margin-top: 11px !important;
        border-top: 1px solid #e7e1d9 !important;
        padding-top: 10px !important;
    }

    .complaint-action-title {
        color: #39332d !important;
        font-size: 9px !important;
        font-weight: 700 !important;
    }

    .complaint-action-copy {
        margin-top: 3px !important;
        color: #91887d !important;
        font-size: 7px !important;
        line-height: 1.45 !important;
    }

    .complaint-action-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 7px !important;
        margin-top: 9px !important;
    }

    .complaint-action-input {
        width: 100%;
        min-width: 0;
        height: 36px !important;
        border: 1px solid #e3ddd5 !important;
        border-radius: 8px !important;
        background: #fff !important;
        padding: 0 9px !important;
        color: #403a34 !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 8px !important;
    }

    .complaint-action-input::placeholder {
        color: #aaa196 !important;
    }

    .complaint-action-input:focus {
        outline: none !important;
        border-color: #d49a2b !important;
        box-shadow: 0 0 0 3px rgba(217,149,0,.075) !important;
    }

    .complaint-resolve,
    .complaint-reopen {
        display: inline-flex;
        min-height: 36px !important;
        height: 36px !important;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
        padding: 0 12px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 7.8px !important;
        font-weight: 600 !important;
        white-space: nowrap;
        box-shadow: none !important;
    }

    .complaint-resolve {
        border: 1px solid #cfe2d5 !important;
        background: #f3f9f5 !important;
        color: #4d7b5c !important;
    }

    .complaint-resolve:hover,
    .complaint-resolve:focus-visible {
        outline: none;
        border-color: #b8d6c2 !important;
        background: #eaf5ed !important;
        color: #2f7c48 !important;
    }

    .complaint-reopen {
        border: 1px solid #e7d6b7 !important;
        background: #fffaf1 !important;
        color: #94671f !important;
    }

    .complaint-reopen:hover,
    .complaint-reopen:focus-visible {
        outline: none;
        border-color: #d8bd86 !important;
        background: #fff4df !important;
        color: #b77400 !important;
    }

    /* ---------------- LAPTOP ---------------- */
    @media (max-height: 850px) and (min-width: 900px) {
        .complaints-title {
            font-size: 22px !important;
        }

        .complaint-stat {
            min-height: 70px !important;
            padding-top: 9px !important;
            padding-bottom: 9px !important;
        }

        .complaint-stat-value {
            font-size: 18px !important;
        }

        .complaint-modal-shell {
            max-height: calc(100vh - 28px) !important;
        }
    }

    /* ---------------- TABLET ---------------- */
    @media (max-width: 1023px) {
        .complaints-filter-grid {
            grid-template-columns: minmax(0, 1fr) 170px;
        }

        .complaints-search {
            grid-column: 1 / -1;
        }

        .complaints-table-head {
            display: none !important;
        }

        .complaint-row {
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 9px !important;
            margin: 9px !important;
            border: 1px solid var(--cp-line) !important;
            border-radius: 11px !important;
        }

        .complaint-row > div:nth-child(2),
        .complaint-row > div:nth-child(3),
        .complaint-row > div:nth-child(4) {
            grid-column: 1;
        }

        .complaint-row > div:last-child {
            grid-column: 2;
            grid-row: 1;
        }
    }

    /* ---------------- MOBILE ---------------- */
    @media (max-width: 639px) {
        .complaints-page {
            padding-bottom: 14px;
        }

        .complaints-header-main {
            align-items: flex-start;
        }

        .complaints-header-icon {
            width: 34px !important;
            height: 34px !important;
            flex-basis: 34px !important;
        }

        .complaints-title {
            font-size: 22px !important;
        }

        .complaints-subtitle {
            font-size: 9px !important;
        }

        .complaints-summary-grid {
            grid-template-columns: 1fr 1fr !important;
        }

        .complaint-stat:first-child {
            grid-column: 1 / -1;
        }

        .complaints-filter-grid {
            grid-template-columns: 1fr;
            gap: 7px !important;
        }

        .complaints-search {
            grid-column: auto;
        }

        .complaints-control,
        .complaints-status-trigger,
        .complaints-apply,
        .complaints-reset {
            min-height: 42px !important;
            height: 42px !important;
            font-size: 9.5px !important;
        }

        .complaints-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .complaint-modal-shell {
            width: calc(100vw - 14px) !important;
            max-height: calc(100vh - 14px) !important;
            border-radius: 13px !important;
        }

        .complaint-modal-header {
            padding: 10px 11px !important;
        }

        .complaint-modal-body {
            padding: 9px !important;
        }

        .complaint-detail-grid {
            grid-template-columns: 1fr;
        }

        .complaint-action-form {
            grid-template-columns: 1fr;
        }

        .complaint-resolve,
        .complaint-reopen {
            width: 100%;
            min-height: 40px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .complaints-page *,
        #complaintModal,
        #complaintModalBackdrop {
            animation: none !important;
            transition: none !important;
            transform: none;
            scroll-behavior: auto !important;
        }

        #complaintModal,
        #complaintModal.is-open {
            transform: translate(-50%, -50%) !important;
        }
    }

    /* ============================================================
       COMPLAINTS & DISPUTES — HEADER SCALE MATCH
       Matches Platform Settings / Platform Commissions header sizing.
       Visual-only; complaint behavior and backend remain untouched.
       ============================================================ */

    .complaints-page > section:first-of-type{
        align-items:center !important;
        gap:20px !important;
        margin-bottom:2px !important;
    }

    .complaints-page .complaints-header-main{
        display:flex !important;
        min-width:0 !important;
        align-items:center !important;
        gap:13px !important;
    }

    .complaints-page .complaints-header-icon{
        width:44px !important;
        height:44px !important;
        flex:0 0 44px !important;
        border-radius:12px !important;
        box-shadow:0 4px 12px rgba(75,54,25,.045) !important;
    }

    .complaints-page .complaints-header-icon svg{
        width:17px !important;
        height:17px !important;
    }

    .complaints-page .complaints-eyebrow{
        color:#9a6f23 !important;
        font-size:8px !important;
        font-weight:700 !important;
        line-height:1.15 !important;
        letter-spacing:.13em !important;
    }

    .complaints-page .complaints-title{
        margin:5px 0 0 !important;
        font-size:29px !important;
        font-weight:700 !important;
        line-height:1.02 !important;
        letter-spacing:-.045em !important;
    }

    .complaints-page .complaints-title-base{
        color:#17130f !important;
    }

    .complaints-page .complaints-title-accent{
        color:#d99500 !important;
    }

    .complaints-page .complaints-subtitle{
        max-width:820px !important;
        margin-top:7px !important;
        color:#7f756a !important;
        font-size:11px !important;
        font-weight:400 !important;
        line-height:1.5 !important;
    }

    @media(max-height:850px) and (min-width:900px){
        .complaints-page .complaints-header-icon{
            width:42px !important;
            height:42px !important;
            flex-basis:42px !important;
        }

        .complaints-page .complaints-title{
            font-size:27px !important;
        }

        .complaints-page .complaints-subtitle{
            font-size:10.5px !important;
        }
    }

    @media(max-width:639px){
        .complaints-page .complaints-header-main{
            align-items:flex-start !important;
            gap:11px !important;
        }

        .complaints-page .complaints-header-icon{
            width:40px !important;
            height:40px !important;
            flex-basis:40px !important;
            border-radius:11px !important;
        }

        .complaints-page .complaints-title{
            font-size:24px !important;
        }

        .complaints-page .complaints-subtitle{
            font-size:10px !important;
        }
    }


    /* ============================================================
       COMPLAINT SUMMARY — COMPACT / NEUTRAL STYLE
       Removes yellow active-border effect and shortens card width.
       ============================================================ */

    .complaints-page .complaints-summary-grid{
        width:100% !important;
        max-width:900px !important;
        grid-template-columns:repeat(3,minmax(0,1fr)) !important;
        gap:10px !important;
        margin-top:14px !important;
        margin-right:auto !important;
    }

    .complaints-page .complaint-stat,
    .complaints-page .complaint-stat.is-active{
        min-height:74px !important;
        border:1px solid #e8e1d8 !important;
        background:#fff !important;
        box-shadow:0 5px 14px rgba(54,42,28,.045) !important;
    }

    .complaints-page .complaint-stat:hover{
        border-color:#ddd5cb !important;
        background:#fff !important;
        box-shadow:0 7px 16px rgba(54,42,28,.055) !important;
        transform:translateY(-1px) !important;
    }

    .complaints-page .complaint-stat.is-active{
        border-color:#e8e1d8 !important;
        background:#fff !important;
    }

    .complaints-page .complaint-stat-icon{
        border-color:#ece5dc !important;
    }

    @media(max-width:1100px){
        .complaints-page .complaints-summary-grid{
            max-width:none !important;
        }
    }

    @media(max-width:639px){
        .complaints-page .complaints-summary-grid{
            grid-template-columns:1fr 1fr !important;
            gap:8px !important;
        }

        .complaints-page .complaint-stat:first-child{
            grid-column:1 / -1 !important;
        }
    }

</style>

@php
    $complaintCount = $complaints->count();
    $statusTone = fn ($status) => strtolower((string) $status) === 'resolved' ? 'complaint-status-resolved' : 'complaint-status-open';
@endphp

<div class="complaints-page mx-auto w-full max-w-[1880px] pb-8">
    @if(session('success'))
        <div class="mb-4 rounded-[14px] border border-[#d5e6da] bg-[#f4faf6] px-4 py-3 text-[9px] text-[#426b50]">{{ session('success') }}</div>
    @endif

    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="complaints-header-main">
            <span class="complaints-header-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v11H8l-3 3V5Z"></path><path d="M9 9h6"></path><path d="M9 12h4"></path></svg></span>
            <div class="min-w-0"><p class="complaints-eyebrow">Platform Support</p><h2 class="complaints-title"><span class="complaints-title-base">Platform</span> <span class="complaints-title-accent">Complaints</span></h2><p class="complaints-subtitle">Review submitted complaints, inspect reporter details, record resolution notes, and reopen cases when additional action is required.</p></div>
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
        <div class="complaints-table-head"><div>Complaint</div><div>Reporter</div><div>Submitted</div><div>Status</div><div class="text-right">Action</div></div>
        <div id="complaintRows">
        @forelse($complaints as $complaint)
            @php
                $complaintStatus = strtolower((string) $complaint->status);
                $reporterRole = strtoupper((string) $complaint->reporter_role);
                $reporter = $complaint->reporter_identifier ?: 'Account';
            @endphp
            <article class="complaint-row" data-complaint-row data-subject="{{ $complaint->subject }}" data-description="{{ $complaint->description }}" data-reporter-role="{{ $reporterRole }}" data-reporter="{{ $reporter }}" data-status="{{ $complaintStatus }}" data-submitted="{{ $complaint->created_at?->format('M d, Y h:i A') }}" data-admin-note="{{ $complaint->admin_note ?? '' }}" data-resolve-url="{{ $complaintStatus === 'open' ? route('admin.complaints.resolve',$complaint) : '' }}" data-reopen-url="{{ $complaintStatus !== 'open' ? route('admin.complaints.reopen',$complaint) : '' }}">
                <div class="min-w-0"><p class="complaint-subject truncate">{{ $complaint->subject }}</p><p class="complaint-preview">{{ $complaint->description }}</p></div>
                <div><p class="complaint-meta-main">{{ $reporter }}</p><p class="complaint-meta-sub">{{ $reporterRole ?: 'ACCOUNT' }}</p></div>
                <div><p class="complaint-meta-main">{{ $complaint->created_at?->format('M d, Y') ?: '—' }}</p><p class="complaint-meta-sub">{{ $complaint->created_at?->format('h:i A') }}</p></div>
                <div><span class="complaint-status {{ $statusTone($complaintStatus) }}">{{ ucfirst($complaintStatus) }}</span></div>
                <div class="flex justify-end"><button type="button" class="complaint-view" data-view-complaint title="View complaint" aria-label="View complaint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6S2.5 12 2.5 12Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg></button></div>
            </article>
        @empty
            <div class="complaints-empty">No complaints submitted.</div>
        @endforelse
        </div>
        <div class="complaints-footer"><p id="complaintResultCount">Showing {{ $complaintCount }} of {{ $complaintCount }} complaints</p><p class="text-[#9a9187]">Select a complaint to review complete details and take action.</p></div>
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

    let filterFrame=0;

    function applyFilters(){
        const q=normalize(search?.value);
        const status=normalize(statusFilter?.value||'all');

        window.cancelAnimationFrame(filterFrame);

        filterFrame=window.requestAnimationFrame(()=>{
            let visible=0;

            complaintIndex.forEach(item=>{
                const show=
                    (status==='all'||item.status===status)
                    &&(!q||item.searchText.includes(q));

                if(item.row.hidden===show){
                    item.row.hidden=!show;
                }

                if(show)visible++;
            });

            if(resultCount){
                resultCount.textContent=`Showing ${visible} of ${complaintIndex.length} complaints`;
            }

            summaryCards.forEach(card=>{
                card.classList.toggle(
                    'is-active',
                    normalize(card.dataset.summaryStatus)===status
                );
            });
        });
    }

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
