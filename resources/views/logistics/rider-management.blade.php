@extends('layouts.logistics')

@section('title', 'Rider Management | SARI Logistics')
@section('page-title', 'Rider Management')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .rm-page,
    .rm-page * {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .rm-page {
        --rm-gold: #D59617;
        --rm-gold-strong: #B97805;
        --rm-text: var(--sari-dark-e23e64dd1e, #111827);
        --rm-text-2: var(--sari-dark-e23e64dd1e, #344054);
        --rm-muted: var(--sari-dark-a63aa16d7c, #7C8592);
        --rm-line: var(--sari-dark-d8c2716225, #E5E7EB);
        --rm-line-soft: var(--sari-dark-d8c2716225, #ECEFF2);
        --rm-soft: #F9FAFB;
        --rm-green: #16856A;
        --rm-red: #C45454;
        width: 100%;
        max-width: 1800px;
        margin-inline: auto;
        padding-bottom: 24px;
        color: var(--rm-text);
    }

    .rm-page button,
    .rm-page input,
    .rm-page select {
        font: inherit;
    }

    .rm-header {
        min-height: 66px;
        margin-bottom: 12px;
        padding: 2px 2px 13px;
        border-bottom: 1px solid var(--rm-line-soft);
    }

    .rm-eyebrow {
        margin: 0 0 5px;
        color: var(--rm-gold-strong);
        font-size: 8px;
        font-weight: 700;
        line-height: 1.15;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .rm-title {
        margin: 0;
        font-size: clamp(29px, 2.2vw, 36px);
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.035em;
        color: var(--rm-text);
    }

    .rm-title-accent { color: var(--rm-gold); }

    .rm-subtitle {
        max-width: 780px;
        margin: 7px 0 0;
        color: var(--rm-muted);
        font-size: 10px;
        font-weight: 400;
        line-height: 1.55;
    }

    .rm-summary-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 12px;
    }

    @media (min-width: 640px) {
        .rm-summary-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    .rm-summary-card {
        position: relative;
        min-height: 104px;
        overflow: hidden;
        border: 1px solid var(--rm-line);
        border-radius: 12px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 18px;
        box-shadow: 0 2px 6px var(--sari-dark-25b2da5e49, rgba(16, 24, 40, .04));
    }

    .rm-summary-label {
        color: var(--sari-dark-a63aa16d7c, #667085);
        font-size: 12px;
        font-weight: 500;
        line-height: 20px;
    }

    .rm-summary-value {
        margin-top: 10px;
        color: var(--sari-dark-e23e64dd1e, #101828);
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .rm-summary-icon {
        position: absolute;
        top: 16px;
        right: 18px;
        display: inline-flex;
        width: 24px;
        height: 24px;
        align-items: center;
        justify-content: center;
        background: transparent;
    }

    .rm-summary-icon svg {
        width: 24px;
        height: 24px;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rm-toolbar {
        display: grid;
        grid-template-columns: minmax(360px, 1fr) 155px 155px 108px;
        gap: 8px;
        align-items: center;
        margin-top: 12px;
    }

    .rm-search {
        position: relative;
        min-width: 0;
    }

    .rm-search svg {
        position: absolute;
        top: 50%;
        left: 12px;
        width: 13px;
        height: 13px;
        color: var(--sari-dark-a63aa16d7c, #98A2B3);
        transform: translateY(-50%);
        pointer-events: none;
    }

    .rm-control-wrap {
        position: relative;
        min-width: 0;
    }

    .rm-control-wrap > svg {
        position: absolute;
        z-index: 2;
        top: 50%;
        right: 11px;
        width: 12px;
        height: 12px;
        color: var(--sari-dark-a63aa16d7c, #667085);
        transform: translateY(-50%);
        pointer-events: none;
    }

    .rm-control {
        width: 100%;
        height: 38px;
        border: 1px solid var(--rm-line);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #fff);
        color: var(--rm-text-2);
        padding: 0 10px;
        font-size: 9.5px;
        font-weight: 400;
        line-height: 1;
        outline: none;
        box-shadow: none;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .rm-search .rm-control {
        padding-left: 35px;
    }

    .rm-control-wrap .rm-control {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 32px;
        cursor: pointer;
        font-weight: 500;
    }

    .rm-control::placeholder {
        color: var(--sari-dark-a63aa16d7c, #98A2B3);
        font-size: 9.5px;
        font-weight: 400;
    }

    .rm-control:hover {
        border-color: var(--sari-dark-d8c2716225, #D1D5DB);
        background: var(--sari-dark-950baec030, #fff);
    }

    .rm-control:focus {
        border-color: var(--rm-gold);
        box-shadow: 0 0 0 3px var(--sari-dark-b12a5e9552, rgba(213, 150, 23, .09));
    }

    .rm-add-button {
        display: inline-flex;
        width: 108px;
        height: 38px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid var(--rm-gold);
        border-radius: 9px;
        background: var(--rm-gold);
        padding: 0 11px;
        color: #fff;
        font-size: 9px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        box-shadow: 0 4px 10px var(--sari-dark-59ed29fe9d, rgba(213, 150, 23, .14));
        transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
    }

    .rm-add-button svg {
        width: 12px;
        height: 12px;
        flex: 0 0 12px;
    }

    .rm-add-button:hover {
        border-color: var(--rm-gold-strong);
        background: var(--rm-gold-strong);
        box-shadow: 0 5px 12px var(--sari-dark-b5c2a2270e, rgba(185, 120, 5, .16));
    }

    .rm-alert {
        margin-top: 12px;
        border-radius: 12px;
        padding: 11px 13px;
        font-size: 9.5px;
        line-height: 1.55;
    }

    .rm-table-card {
        margin-top: 14px;
        overflow: hidden;
        border: 1px solid var(--rm-line);
        border-radius: 14px;
        background: var(--sari-dark-950baec030, #fff);
        box-shadow: 0 2px 5px var(--sari-dark-25b2da5e49, rgba(15, 23, 42, .04)), 0 10px 24px var(--sari-dark-361b571631, rgba(15, 23, 42, .06));
    }

    .rm-table-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 18px;
        border-bottom: 1px solid var(--rm-line-soft);
    }

    .rm-table-title-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .rm-table-title {
        margin: 0;
        color: var(--sari-dark-e23e64dd1e, #101828);
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -.02em;
    }

    .rm-count {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        border: 1px solid var(--rm-line);
        border-radius: 999px;
        background: var(--rm-soft);
        padding: 0 8px;
        color: var(--sari-dark-a63aa16d7c, #667085);
        font-size: 8px;
        font-weight: 600;
    }

    .rm-table-copy {
        margin-top: 3px;
        color: var(--rm-muted);
        font-size: 9.5px;
        line-height: 1.5;
    }

    .rm-table-wrap { overflow-x: auto; }
    .rm-table { width: 100%; min-width: 940px; border-collapse: collapse; }
    .rm-table thead tr { background: var(--sari-dark-950baec030, #F9FAFB); }
    .rm-table th {
        padding: 10px 14px;
        color: var(--sari-dark-a63aa16d7c, #667085);
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .075em;
        text-transform: uppercase;
        text-align: left;
    }
    .rm-table th:first-child,
    .rm-table td:first-child { padding-left: 18px; }
    .rm-table th:last-child,
    .rm-table td:last-child { padding-right: 18px; }

    .rm-table tbody tr {
        border-top: 1px solid var(--rm-line-soft);
        background: var(--sari-dark-950baec030, #fff);
        transition: background-color .14s ease, box-shadow .14s ease;
    }
    .rm-table tbody tr:hover {
        background: var(--sari-dark-950baec030, #FFFCF5);
        box-shadow: inset 2px 0 0 var(--rm-gold);
    }
    .rm-table td {
        padding: 11px 14px;
        color: var(--sari-dark-e23e64dd1e, #475467);
        font-size: 9px;
        vertical-align: middle;
    }

    .rm-rider-name { color: var(--sari-dark-e23e64dd1e, #101828); font-size: 9.5px; font-weight: 600; }
    .rm-rider-email { margin-top: 2px; color: var(--sari-dark-a63aa16d7c, #98A2B3); font-size: 8px; }

    .rm-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid var(--rm-line);
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 8px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
    }

    .rm-badge.is-active { border-color: var(--sari-dark-d8c2716225, #CFE1D5); background: var(--sari-dark-4d21eb669b, #F2F8F4); color: var(--sari-dark-a63aa16d7c, #4F7C60); }
    .rm-badge.is-pending { border-color: var(--sari-dark-d8c2716225, #EADFC9); background: var(--sari-dark-950baec030, #FFFAF2); color: var(--sari-dark-5d5ad776eb, #A8731F); }
    .rm-badge.is-inactive { border-color: var(--sari-dark-d8c2716225, #E8CCCC); background: var(--sari-dark-950baec030, #FFF3F3); color: var(--sari-dark-573e4b124d, #A55A5A); }
    .rm-badge.is-online { border-color: var(--sari-dark-d8c2716225, #CFE1D5); background: var(--sari-dark-4d21eb669b, #F2F8F4); color: var(--sari-dark-a63aa16d7c, #4F7C60); }
    .rm-badge.is-offline { border-color: var(--sari-dark-d8c2716225, #E5E7EB); background: var(--sari-dark-950baec030, #F9FAFB); color: var(--sari-dark-a63aa16d7c, #667085); }

    .rm-resend {
        margin-top: 6px;
        border: 0;
        background: transparent;
        padding: 0;
        color: var(--rm-gold-strong);
        font-size: 7.5px;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    /* Buyer Cart-sized, light-gray empty states (no icon tile or background). */
    .rm-empty {
        padding: 72px 20px;
        text-align: center;
    }

    /* Win over .rm-table td padding on the placeholder cell. */
    .rm-page .rm-table td.rm-empty {
        padding: 72px 24px;
    }

    .rm-page .rm-empty-icon {
        display: grid;
        width: 92px;
        height: 92px;
        place-items: center;
        margin: 0 auto;
        border: 0;
        background: transparent;
        color: var(--sari-dark-a63aa16d7c, #B4BBC3);
    }

    .rm-page .rm-empty-icon svg {
        width: 58px;
        height: 58px;
        stroke: currentColor;
        stroke-width: 1.45;
        stroke-linecap: round;
        stroke-linejoin: round;
        fill: none;
    }

    .rm-page .rm-empty-title {
        margin: 18px 0 0;
        color: var(--sari-dark-a63aa16d7c, #9CA4AD);
        font-size: 22px;
        font-weight: 750;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    .rm-page .rm-empty-copy {
        max-width: 520px;
        margin: 7px auto 0;
        color: var(--sari-dark-a63aa16d7c, #B0B7BF);
        font-size: 12px;
        font-weight: 500;
        line-height: 1.55;
    }

    /* Without Riders, don't force the empty message into a 940px-wide table. */
    .rm-page .rm-table.is-empty {
        min-width: 0;
    }

    .rm-page .rm-table.is-empty thead {
        display: none;
    }

    @media (max-width: 640px) {
        .rm-empty { padding: 56px 18px; }
        .rm-page .rm-table td.rm-empty { padding: 56px 18px; }
        .rm-page .rm-empty-icon { width: 64px; height: 64px; }
        .rm-page .rm-empty-icon svg { width: 40px; height: 40px; }
        .rm-page .rm-empty-title { font-size: 20px; }
        .rm-page .rm-empty-copy { font-size: 11px; }
    }

    .rm-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: var(--sari-dark-afab254fcf, rgba(17, 24, 39, .46));
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
    }
    .rm-modal-backdrop.is-open { display: flex; }

    .rm-modal {
        width: min(860px, 100%);
        max-height: min(88vh, 760px);
        overflow: hidden;
        border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB);
        border-radius: 18px;
        background: var(--sari-dark-950baec030, #fff);
        box-shadow: 0 30px 80px var(--sari-dark-ef22e61893, rgba(15, 23, 42, .20));
    }

    .rm-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--rm-line-soft);
    }
    .rm-modal-kicker {
        margin: 0 0 4px;
        color: var(--rm-gold-strong);
        font-size: 7.5px;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
    }
    .rm-modal-title {
        margin: 0;
        color: var(--sari-dark-e23e64dd1e, #101828);
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.025em;
    }
    .rm-modal-copy {
        margin-top: 5px;
        color: var(--rm-muted);
        font-size: 9px;
        line-height: 1.5;
    }
    .rm-modal-close {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border: 1px solid var(--rm-line);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-a63aa16d7c, #667085);
    }
    .rm-modal-close:hover { background: var(--sari-dark-950baec030, #F9FAFB); }

    .rm-modal-body {
        max-height: calc(min(88vh, 760px) - 78px);
        overflow-y: auto;
        padding: 18px 20px 20px;
    }

    .rm-form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .rm-field { display: block; min-width: 0; }
    .rm-field-label {
        display: block;
        margin-bottom: 6px;
        color: var(--sari-dark-e23e64dd1e, #475467);
        font-size: 8.5px;
        font-weight: 600;
    }
    .rm-form-control {
        width: 100%;
        height: 40px;
        border: 1px solid var(--rm-line);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #fff);
        padding: 0 11px;
        color: var(--sari-dark-e23e64dd1e, #344054);
        font-size: 9.5px;
        outline: none;
    }
    .rm-form-control:focus {
        border-color: var(--rm-gold);
        box-shadow: 0 0 0 3px var(--sari-dark-8eadf403a5, rgba(213, 150, 23, .10));
    }

    .rm-file-input {
        width: 100%;
        min-height: 40px;
        border: 1px dashed var(--sari-dark-d8c2716225, #D1D5DB);
        border-radius: 9px;
        background: var(--sari-dark-950baec030, #F9FAFB);
        padding: 8px 10px;
        color: var(--sari-dark-a63aa16d7c, #667085);
        font-size: 8.5px;
    }

    .rm-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--rm-line-soft);
    }
    .rm-button-secondary,
    .rm-button-primary {
        display: inline-flex;
        height: 40px;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        padding: 0 14px;
        font-size: 9.5px;
        font-weight: 600;
    }
    .rm-button-secondary {
        border: 1px solid var(--rm-line);
        background: var(--sari-dark-950baec030, #fff);
        color: var(--sari-dark-a63aa16d7c, #667085);
    }
    .rm-button-primary {
        border: 1px solid var(--rm-gold);
        background: var(--rm-gold);
        color: #fff;
    }
    .rm-button-primary:hover { border-color: var(--rm-gold-strong); background: var(--rm-gold-strong); }

    @media (max-width: 1023px) {
        .rm-toolbar { grid-template-columns: minmax(0, 1fr) 145px 145px; }
        .rm-add-button { grid-column: 1 / -1; width: 100%; }
        .rm-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 639px) {
        .rm-header { min-height: auto; }
        .rm-title { font-size: 28px; }
        .rm-subtitle { font-size: 10px; }
        .rm-toolbar { grid-template-columns: 1fr; }
        .rm-add-button { grid-column: auto; }
        .rm-form-grid { grid-template-columns: 1fr; }
        .rm-modal-backdrop { padding: 10px; }
        .rm-modal-head,
        .rm-modal-body { padding-left: 14px; padding-right: 14px; }
    }

    /* Final compact typography override for Rider toolbar */
    .rm-page .rm-control-wrap select.rm-control {
        font-size: 8.5px !important;
        font-weight: 500 !important;
        letter-spacing: 0 !important;
        line-height: 1 !important;
    }

    .rm-page .rm-control-wrap select.rm-control option {
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important;
        font-size: 8.5px !important;
        font-weight: 400 !important;
    }

    .rm-page .rm-add-button {
        font-size: 8.5px !important;
        font-weight: 500 !important;
        letter-spacing: 0 !important;
    }

    .rm-page .rm-search .rm-control,
    .rm-page .rm-search .rm-control::placeholder {
        font-size: 9px !important;
        font-weight: 400 !important;
    }

    /* Seller Orders reference: workspace, typography, controls and table density. */
    .rm-page {
        --rm-gold: #C88912; --rm-gold-strong: #B47B0E;
        --rm-text: var(--sari-dark-e23e64dd1e, #2F3742);
        --rm-muted: var(--sari-dark-a63aa16d7c, #8793A1);
        --rm-soft: var(--sari-dark-4d21eb669b, #F8FAFC);
        max-width: 1520px; padding-bottom: 28px;
    }
    .rm-page [hidden] { display: none !important; }
    .rm-header { min-height: 0; margin-bottom: 20px; padding: 0 0 14px; }
    .rm-eyebrow { margin: 0; font-size: 10.5px; font-weight: 800; line-height: 1.4; letter-spacing: .14em; color: var(--sari-dark-685eb4fc40, #A87316); }
    .rm-title { margin-top: 6px; font-size: clamp(27px,2.3vw,31px); font-weight: 760; line-height: 1.08; letter-spacing: -.04em; }
    .rm-summary-grid { grid-template-columns: repeat(3,minmax(0,1fr)); gap: 14px; }
    .rm-summary-card, .rm-table-card { border-radius: 18px; box-shadow: 0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)), 0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055)); }
    .rm-summary-card { min-height: 110px; padding: 14px 16px; }
    .rm-summary-label { margin: 0; padding-right: 32px; font-size: 13px; font-weight: 650; line-height: 1.4; }
    .rm-summary-value { margin-top: 7px; font-size: clamp(25px,1.9vw,30px); font-weight: 650; line-height: 1.12; font-variant-numeric: tabular-nums; }
    .rm-summary-icon { top: 14px; right: 16px; width: 27px; height: 27px; }
    .rm-summary-icon svg { width: 27px; height: 27px; }
    .rm-toolbar { position: relative; z-index: 15; margin-top: 20px; gap: 12px; grid-template-columns: minmax(230px,1fr) minmax(160px,185px) minmax(160px,185px) auto auto auto; }
    .rm-page .rm-search .rm-control, .rm-page .rm-search .rm-control::placeholder,
    .rm-page .rm-control-wrap select.rm-control { font-size: 11.5px !important; }
    .rm-page .rm-control-wrap select.rm-control option { font-size: 11.5px !important; }
    .rm-page .rm-control, .rm-page .rm-add-button, .rm-filter-apply, .rm-filter-reset, .rm-status-trigger {
        height: 48px; min-height: 48px; border-radius: 14px; transform: none !important; translate: none !important;
    }
    .rm-page .rm-control { font-weight: 600; background: var(--sari-dark-950baec030, #FFF); }
    .rm-page .rm-search .rm-control { padding-left: 46px; padding-right: 14px; }
    .rm-search svg { left: 16px; width: 21px; height: 21px; }
    .rm-page .rm-add-button, .rm-filter-apply, .rm-filter-reset { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: auto; grid-column: auto; padding: 0 18px; font-size: 12px !important; font-weight: 700 !important; white-space: nowrap; cursor: pointer; box-shadow: none; border: 1px solid var(--rm-gold); }
    .rm-page .rm-add-button svg, .rm-filter-apply svg { width: 15px; height: 15px; flex: 0 0 15px; }
    .rm-filter-apply { color: #FFF; background: var(--rm-gold); }
    .rm-filter-apply:hover, .rm-page .rm-add-button:hover { background: var(--rm-gold-strong); border-color: var(--rm-gold-strong); box-shadow: none; }
    .rm-filter-reset { min-width: 86px; border-color: var(--rm-line); background: var(--sari-dark-950baec030, #FFF); color: var(--rm-muted); }
    .rm-filter-reset:hover, .rm-control:hover { background: var(--rm-soft); }
    .rm-page button:focus-visible, .rm-control:focus-visible, .rm-status-trigger[aria-expanded="true"] { outline: 2px solid var(--rm-gold); outline-offset: 2px; }
    .rm-control-wrap { z-index: 50; }
    .rm-control-wrap[data-enhanced] > select { position: absolute; width: 1px; height: 1px; min-height: 0; padding: 0; margin: 0; border: 0; clip-path: inset(50%); opacity: 0; pointer-events: none; overflow: hidden; }
    .rm-control-wrap[data-enhanced] > svg { display: none; }
    .rm-status-trigger { display: flex; width: 100%; align-items: center; justify-content: space-between; gap: 8px; padding: 0 16px; border: 1px solid var(--rm-line); background: var(--sari-dark-950baec030, #FFF); color: var(--rm-text-2); text-align: left; cursor: pointer; }
    .rm-page .rm-status-trigger { font-size: 11.5px; font-weight: 600; }
    .rm-status-trigger span { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rm-status-trigger svg { width: 17px; height: 17px; flex: 0 0 17px; transition: transform .15s ease; }
    .rm-status-trigger[aria-expanded="true"] svg { transform: rotate(180deg); }
    .rm-status-trigger:hover { background: var(--rm-soft); }
    .rm-status-menu { position: absolute; top: calc(100% + 8px); left: 0; right: 0; z-index: 100; padding: 8px; border: 1px solid var(--rm-line); border-radius: 14px; background: var(--sari-dark-950baec030, #FFF); box-shadow: 0 16px 32px rgba(15,23,42,.12), 0 2px 6px rgba(15,23,42,.05); }
    .rm-status-option { display: flex; align-items: center; width: 100%; min-height: 38px; padding: 0 12px; border: 0; border-radius: 10px; background: transparent; color: var(--rm-text-2); text-align: left; cursor: pointer; }
    .rm-page .rm-status-option { font-size: 11.5px; font-weight: 600; }
    .rm-status-option:hover, .rm-status-option:focus-visible, .rm-status-option[aria-selected="true"] { background: var(--rm-soft); color: var(--sari-dark-af18afcaa4, #A56D0A); }
    .rm-status-option[aria-selected="true"] { font-weight: 700; }
    .rm-table-card { margin-top: 20px; }
    .rm-table-header { padding: 21px 23px; gap: 20px; }
    .rm-table-title { font-size: 18px; font-weight: 780; }
    .rm-table-copy { margin-top: 4px; font-size: 11.5px; }
    .rm-count { min-height: 0; padding: 0; border: 0; border-radius: 0; background: transparent; font-size: 12px; font-weight: 600; color: var(--rm-muted); }
    .rm-table-wrap { scrollbar-width: thin; }
    .rm-table-wrap:focus-visible { outline: 2px solid var(--rm-gold); outline-offset: -2px; }
    .rm-table { min-width: 1060px; }
    .rm-table thead tr { background: var(--rm-soft); }
    .rm-table th { height: 48px; padding: 13px 16px; font-size: 11px; font-weight: 750; line-height: 1.4; }
    .rm-table td { padding: 17px 16px; font-size: 11.5px; line-height: 1.5; }
    .rm-table th:first-child, .rm-table td:first-child { padding-left: 16px; }
    .rm-table th:last-child, .rm-table td:last-child { padding-right: 16px; }
    .rm-table tbody tr:hover { background: var(--rm-soft); box-shadow: none; }
    .rm-table tbody tr[hidden] { display: none !important; }
    .rm-rider-name { margin: 0; font-size: 12.5px; font-weight: 750; line-height: 1.5; }
    .rm-rider-email { margin-top: 3px; font-size: 11px; line-height: 1.5; }
    .rm-table td > p { margin: 0; font-size: 11.5px; }
    .rm-table td > p + p { margin-top: 3px; font-size: 11px; color: var(--rm-muted); }
    .rm-table td:first-child img { border: 1px solid var(--rm-line); border-radius: 9px !important; }
    .rm-badge { min-height: 27px; padding: 5px 10px; font-size: 10.5px; font-weight: 700; line-height: 1.3; }
    .rm-resend { font-size: 10.5px; }
    .rm-table-footer { display: flex; min-height: 64px; align-items: center; justify-content: space-between; gap: 16px; padding: 13px 20px; border-top: 1px solid var(--rm-line-soft); }
    .rm-result-count { margin: 0; color: var(--rm-muted); font-size: 11px; font-weight: 550; }
    .rm-pagination, .rm-page-numbers { display: flex; align-items: center; gap: 6px; }
    .rm-pagination button { display: grid; place-items: center; width: 34px; min-width: 34px; height: 34px; padding: 0; border: 1px solid var(--rm-line); border-radius: 10px; background: var(--sari-dark-950baec030, #FFF); color: var(--rm-muted); cursor: pointer; }
    .rm-page .rm-pagination button { font-size: 11.5px; font-weight: 700; }
    .rm-pagination button[aria-current="page"] { color: #FFF; background: var(--rm-gold); border-color: var(--rm-gold); }
    .rm-pagination button:disabled { opacity: .42; cursor: not-allowed; }
    .rm-pagination button:hover:not(:disabled) { border-color: var(--rm-gold); }
    .rm-pagination svg { width: 15px; height: 15px; }
    @media (max-width: 1180px) {
        .rm-toolbar { grid-template-columns: repeat(6,minmax(0,1fr)); }
        .rm-search { grid-column: 1 / -1; }
        .rm-control-wrap { grid-column: span 3; }
        .rm-page .rm-toolbar > button { grid-column: span 2; width: 100%; }
    }
    @media (max-width: 767px) {
        .rm-title { font-size: 25px; }
        .rm-eyebrow { font-size: 9.5px; }
        .rm-summary-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
    }
    @media (max-width: 640px) {
        .rm-toolbar { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .rm-control-wrap { grid-column: 1 / -1; }
        .rm-page .rm-toolbar > button { grid-column: auto; }
        .rm-page .rm-toolbar > .rm-add-button { grid-column: 1 / -1; }
    }
    @media (max-width: 540px) {
        .rm-summary-grid { grid-template-columns: 1fr; gap: 11px; }
        .rm-summary-card { min-height: 100px; }
        .rm-table-header { flex-direction: column; align-items: flex-start; padding: 17px 16px; gap: 6px; }
        .rm-table-footer { flex-direction: column; align-items: flex-start; gap: 12px; }
        .rm-pagination { align-self: flex-end; }
    }
    @media (prefers-reduced-motion: reduce) { .rm-page * { transition: none !important; animation: none !important; } }
    /* Fit the toolbar to its workspace when the Logistics sidebar is open. */
    .rm-page { container-type: inline-size; }
    @container (max-width: 1100px) {
        .rm-toolbar { grid-template-columns: repeat(6,minmax(0,1fr)); }
        .rm-search { grid-column: 1 / -1; }
        .rm-control-wrap { grid-column: span 3; }
        .rm-page .rm-toolbar > button { grid-column: span 2; width: 100%; }
    }
    @container (max-width: 640px) {
        .rm-toolbar { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .rm-control-wrap { grid-column: 1 / -1; }
        .rm-page .rm-toolbar > button { grid-column: auto; }
        .rm-page .rm-toolbar > .rm-add-button { grid-column: 1 / -1; }
    }

</style>
@endpush

@section('content')
<div class="rm-page sari-logistics-ui">
    <section class="rm-header">
        <p class="rm-eyebrow">Rider Operations</p>
        <h1 class="rm-title">Manage <span class="rm-title-accent">Riders</span></h1>
    </section>

    <section class="rm-summary-grid" aria-label="Rider account summary">
        <article class="rm-summary-card">
            <p class="rm-summary-label">Approved Riders</p>
            <p class="rm-summary-value">{{ $stats['total'] }}</p>
            <span class="rm-summary-icon" style="color:var(--sari-dark-9db03b407c, #AC7A16)" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="9" cy="7" r="3"></circle><path d="M3 20a6 6 0 0 1 12 0"></path><path d="M17 6a3 3 0 0 1 0 6"></path><path d="M18 15a5 5 0 0 1 3 5"></path></svg>
            </span>
        </article>
        <article class="rm-summary-card">
            <p class="rm-summary-label">Active Accounts</p>
            <p class="rm-summary-value">{{ $stats['active'] }}</p>
            <span class="rm-summary-icon" style="color:var(--sari-dark-63ef464c7f, #16856A)" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
            </span>
        </article>
        <article class="rm-summary-card">
            <p class="rm-summary-label">Busy Now</p>
            <p class="rm-summary-value">{{ $stats['busy'] }}</p>
            <span class="rm-summary-icon" style="color:var(--sari-dark-5eab7c8294, #4F82B6)" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 15h2l2-5h7l2 5h3"></path><circle cx="8" cy="17" r="2"></circle><circle cx="18" cy="17" r="2"></circle><path d="M9 10V7h5"></path></svg>
            </span>
        </article>
    </section>

    @if(session('success'))
        <div class="rm-alert border border-[#CFE1D5] bg-[#F2F8F4] text-[#4F7C60]" role="status">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rm-alert border border-[#E8CCCC] bg-[#FFF3F3] text-[#A55A5A]" role="alert">{{ $errors->first() }}</div>
    @endif

    <section class="rm-toolbar" aria-label="Rider filters">
        <div class="rm-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg>
            <input id="riderSearch" aria-label="Search riders" type="search" autocomplete="off" class="rm-control" placeholder="Search Rider, email, contact, vehicle, or plate...">
        </div>

        <div class="rm-control-wrap" data-rm-dropdown>
            <select id="riderAccountFilter" class="rm-control" aria-label="Filter by account status">
                <option value="all">All Accounts</option>
                <option value="active">Active</option>
                <option value="pending_activation">Pending Activation</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
            <button type="button" class="rm-status-trigger" data-rm-status-trigger aria-label="Filter by account status" aria-haspopup="listbox" aria-expanded="false" aria-controls="riderAccountFilterMenu" hidden>
                <span data-rm-status-label>All Accounts</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
            </button>
            <div id="riderAccountFilterMenu" class="rm-status-menu" role="listbox" aria-label="Filter by account status" data-rm-status-menu hidden>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="all" aria-selected="true">All Accounts</button>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="active" aria-selected="false">Active</button>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="pending_activation" aria-selected="false">Pending Activation</button>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="inactive" aria-selected="false">Inactive</button>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="suspended" aria-selected="false">Suspended</button>
            </div>
        </div>

        <div class="rm-control-wrap" data-rm-dropdown>
            <select id="riderAvailabilityFilter" class="rm-control" aria-label="Filter by availability">
                <option value="all">All Availability</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="busy">Busy</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
            <button type="button" class="rm-status-trigger" data-rm-status-trigger aria-label="Filter by availability" aria-haspopup="listbox" aria-expanded="false" aria-controls="riderAvailabilityFilterMenu" hidden>
                <span data-rm-status-label>All Availability</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
            </button>
            <div id="riderAvailabilityFilterMenu" class="rm-status-menu" role="listbox" aria-label="Filter by availability" data-rm-status-menu hidden>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="all" aria-selected="true">All Availability</button>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="online" aria-selected="false">Online</button>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="offline" aria-selected="false">Offline</button>
                <button type="button" class="rm-status-option" role="option" tabindex="-1" data-rm-status-option data-value="busy" aria-selected="false">Busy</button>
            </div>
        </div>

        <button id="applyRiderFilters" type="button" class="rm-filter-apply">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h16M7 10h10M10 15h4"></path></svg>
            Apply Filter
        </button>
        <button id="resetRiderFilters" type="button" class="rm-filter-reset">Reset</button>
        <button id="openAddRiderModal" type="button" class="rm-add-button">
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
            Add Rider
        </button>
    </section>

    <section class="rm-table-card">
        <div class="rm-table-header">
            <div>
                <div class="rm-table-title-row">
                    <h2 class="rm-table-title">Rider directory</h2>
                </div>
                <p class="rm-table-copy">Review Rider account status, availability, assigned vehicle, and active delivery work.</p>
            </div>
            <span id="riderVisibleCount" class="rm-count" aria-live="polite">Showing {{ $riders->count() }} of {{ $riders->count() }}</span>
        </div>

        <div class="rm-table-wrap" tabindex="0" aria-label="Rider directory, scroll horizontally to see all columns">
            <table aria-label="Rider directory records" class="rm-table {{ $riders->isEmpty() ? 'is-empty' : '' }}">
                <thead>
                    <tr>
                        <th scope="col">Rider</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Account</th>
                        <th scope="col">Availability</th>
                        <th scope="col">Current Work</th>
                    </tr>
                </thead>
                <tbody id="riderTableBody">
                    @forelse($riders as $rider)
                        @php
                            $activeOrder = $activeOrders->get(strtolower((string) $rider->email));
                            $accountStatus = strtolower((string) ($rider->account_status ?: 'inactive'));
                            $availability = strtolower((string) ($rider->availability_status ?: 'online'));
                            $searchText = strtolower(trim($rider->first_name.' '.$rider->last_name).' '.$rider->email.' '.$rider->contact_no.' '.$rider->vehicle_type.' '.$rider->plate_number);
                            $accountBadge = $accountStatus === 'active' ? 'is-active' : ($accountStatus === 'pending_activation' ? 'is-pending' : 'is-inactive');
                            $availabilityBadge = $availability === 'online' ? 'is-online' : 'is-offline';
                        @endphp
                        <tr
                            data-rider-row
                            data-search="{{ $searchText }}"
                            data-account="{{ $accountStatus }}"
                            data-availability="{{ $availability }}"
                        >
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;min-width:0">
                                    @if($rider->profile_image_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($rider->profile_image_path) }}" alt="{{ trim($rider->first_name.' '.$rider->last_name) }}" width="44" height="44" loading="lazy" style="width:44px;height:44px;flex-shrink:0;object-fit:cover;border-radius:12px">
                                    @endif
                                    <div style="min-width:0"><p class="rm-rider-name">{{ trim($rider->first_name.' '.$rider->last_name) }}</p>
                                    <p class="rm-rider-email">{{ $rider->email }}</p></div>
                                </div>
                            </td>
                            <td>
                                <p class="font-medium text-[#344054]">{{ $rider->vehicle_type ?: '—' }}</p>
                                <p class="mt-0.5 text-[8px] text-[#98A2B3]">{{ $rider->plate_number ?: 'No plate number' }}</p>
                            </td>
                            <td>{{ $rider->contact_no ?: '—' }}</td>
                            <td>
                                <span class="rm-badge {{ $accountBadge }}">{{ str($accountStatus)->replace('_', ' ')->title() }}</span>
                                @if($accountStatus === 'pending_activation')
                                    <form method="POST" action="{{ route('logistics.rider-management.resend-activation', $rider) }}">
                                        @csrf
                                        <button type="submit" class="rm-resend">Resend activation</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <span class="rm-badge {{ $availabilityBadge }}">{{ ucfirst($availability) }}</span>
                            </td>
                            <td>
                                @if($activeOrder)
                                    <p class="font-medium text-[#344054]">{{ $activeOrder->order_number }}</p>
                                    <p class="mt-0.5 text-[8px] text-[#98A2B3]">{{ $activeOrder->statusLabel() }}</p>
                                @else
                                    <span class="text-[#667085]">{{ $availability === 'online' ? 'Available' : 'Offline' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr data-empty-row>
                            <td colspan="6" class="rm-empty">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="rm-empty-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24">
                                            <circle cx="9" cy="8" r="3"></circle>
                                            <path d="M3.5 20c.5-4.1 2.3-6.3 5.5-6.3s5 2.2 5.5 6.3"></path>
                                            <path d="M16 8.5a2.6 2.6 0 1 0 0-5.2M16.5 14c2.6.3 4.2 2.2 4.5 5"></path>
                                        </svg>
                                    </span>
                                    <p class="rm-empty-title">No Riders yet</p>
                                    <p class="rm-empty-copy">Approved Rider accounts will appear here once available.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="riderSearchEmpty" hidden class="rm-empty border-t border-[#ECEFF2]">
            <div class="flex flex-col items-center justify-center">
                <span class="rm-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <circle cx="10.5" cy="10.5" r="6.5"></circle>
                        <path d="m16 16 4.5 4.5M8 10.5h5"></path>
                    </svg>
                </span>
                <p class="rm-empty-title">No matching Riders</p>
                <p class="rm-empty-copy">Try another search or adjust your account and availability filters.</p>
            </div>
        </div>
        <div class="rm-table-footer">
            <p id="riderResultCount" class="rm-result-count" aria-live="polite">Showing {{ $riders->count() ? 1 : 0 }}–{{ $riders->count() }} of {{ $riders->count() }} riders</p>
            <nav class="rm-pagination" aria-label="Rider directory pagination" data-rm-pagination hidden>
                <button type="button" data-rm-page-prev aria-label="Previous page" disabled><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button>
                <div class="rm-page-numbers" data-rm-page-numbers></div>
                <button type="button" data-rm-page-next aria-label="Next page" disabled><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg></button>
            </nav>
        </div>
    </section>
</div>

<div id="addRiderModal" class="rm-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="addRiderModalTitle" aria-hidden="true">
    <div class="rm-modal" role="document">
        <div class="rm-modal-head">
            <div>
                <p class="rm-modal-kicker">Rider Account</p>
                <h2 id="addRiderModalTitle" class="rm-modal-title">Add Rider</h2>
                <p class="rm-modal-copy">Creates a Pending Activation account and emails a secure 24-hour activation link.</p>
            </div>
            <button id="closeAddRiderModal" type="button" class="rm-modal-close" aria-label="Close Add Rider dialog">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 7 10 10M17 7 7 17"></path></svg>
            </button>
        </div>

        <div class="rm-modal-body">
            <form method="POST" action="{{ route('logistics.rider-management.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="rm-form-grid">
                    @foreach([
                        ['first_name','First Name','text'],
                        ['last_name','Last Name','text'],
                        ['birthday','Date of Birth','date'],
                        ['email','Email','email'],
                        ['contact_no','Mobile Number','text'],
                        ['license_number',"Driver's License No.",'text'],
                        ['license_expiry_date','License Expiry Date','date'],
                        ['plate_number','Plate Number','text'],
                    ] as [$name,$label,$type])
                        <label class="rm-field">
                            <span class="rm-field-label">{{ $label }} *</span>
                            <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name) }}" required class="rm-form-control">
                        </label>
                    @endforeach

                    <label class="rm-field">
                        <span class="rm-field-label">Vehicle Type *</span>
                        <select name="vehicle_type" required class="rm-form-control">
                            <option value="">Select vehicle</option>
                            @foreach(['Motorcycle','Car','Van'] as $vehicle)
                                <option value="{{ $vehicle }}" @selected(old('vehicle_type') === $vehicle)>{{ $vehicle }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="rm-field">
                        <span class="rm-field-label">Driver's License *</span>
                        <input type="file" name="driver_license_document" accept=".jpg,.jpeg,.png,.pdf" required class="rm-file-input">
                    </label>

                    <label class="rm-field">
                        <span class="rm-field-label">Vehicle OR/CR *</span>
                        <input type="file" name="orcr_document" accept=".jpg,.jpeg,.png,.pdf" required class="rm-file-input">
                    </label>
                </div>

                <div class="rm-modal-actions">
                    <button id="cancelAddRiderModal" type="button" class="rm-button-secondary">Cancel</button>
                    <button type="submit" class="rm-button-primary">Save Rider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    function initRiderManagementPage() {
        if (window.__SARI_RIDER_MANAGEMENT_ABORT__) {
            window.__SARI_RIDER_MANAGEMENT_ABORT__.abort();
        }

        const controller = new AbortController();
        const signal = controller.signal;
        window.__SARI_RIDER_MANAGEMENT_ABORT__ = controller;

        const search = document.getElementById('riderSearch');
        const accountFilter = document.getElementById('riderAccountFilter');
        const availabilityFilter = document.getElementById('riderAvailabilityFilter');
        const rows = Array.from(document.querySelectorAll('[data-rider-row]'));
        const count = document.getElementById('riderVisibleCount');
        const empty = document.getElementById('riderSearchEmpty');

        const modal = document.getElementById('addRiderModal');
        const openModal = document.getElementById('openAddRiderModal');
        const closeModal = document.getElementById('closeAddRiderModal');
        const cancelModal = document.getElementById('cancelAddRiderModal');
        let lastFocused = null;

        function listen(node, eventName, handler, options) {
            if (!node) return;
            node.addEventListener(eventName, handler, Object.assign({}, options || {}, { signal }));
        }

        const pageSize = 10;
        let currentPage = 1;
        let pageCount = 1;
        const pagination = document.querySelector('[data-rm-pagination]');
        const numbers = document.querySelector('[data-rm-page-numbers]');
        const previous = document.querySelector('[data-rm-page-prev]');
        const next = document.querySelector('[data-rm-page-next]');
        const resultCount = document.getElementById('riderResultCount');

        function filterRows({ resetPage = true } = {}) {
            const query = (search?.value || '').trim().toLowerCase();
            const account = accountFilter?.value || 'all';
            const availability = availabilityFilter?.value || 'all';
            const matches = rows.filter(row =>
                (!query || (row.dataset.search || '').includes(query)) &&
                (account === 'all' || row.dataset.account === account) &&
                (availability === 'all' || row.dataset.availability === availability)
            );
            pageCount = Math.max(1, Math.ceil(matches.length / pageSize));
            currentPage = resetPage ? 1 : Math.min(Math.max(1, currentPage), pageCount);
            const offset = (currentPage - 1) * pageSize;
            const visible = new Set(matches.slice(offset, offset + pageSize));
            rows.forEach(row => { row.hidden = !visible.has(row); });
            if (count) count.textContent = `Showing ${matches.length} of ${rows.length}`;
            if (empty) empty.hidden = matches.length > 0 || rows.length === 0;
            if (resultCount) resultCount.textContent = `Showing ${matches.length ? offset + 1 : 0}–${Math.min(offset + pageSize, matches.length)} of ${matches.length} riders`;
            if (pagination) pagination.hidden = pageCount <= 1;
            if (previous) previous.disabled = currentPage <= 1;
            if (next) next.disabled = currentPage >= pageCount;
            if (!numbers) return;
            numbers.replaceChildren();
            const start = Math.max(1, Math.min(currentPage - 2, pageCount - 4));
            const end = Math.min(pageCount, start + 4);
            for (let page = start; page <= end; page += 1) {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.rmPage = String(page);
                button.textContent = String(page);
                button.setAttribute('aria-label', `Page ${page}`);
                if (page === currentPage) button.setAttribute('aria-current', 'page');
                numbers.append(button);
            }
        }

        listen(pagination, 'click', event => {
            const button = event.target.closest('button');
            if (!button || button.disabled) return;
            if (button.hasAttribute('data-rm-page-prev')) currentPage -= 1;
            else if (button.hasAttribute('data-rm-page-next')) currentPage += 1;
            else if (button.dataset.rmPage) currentPage = Number(button.dataset.rmPage);
            else return;
            filterRows({ resetPage: false });
            if (button.dataset.rmPage || button.disabled) numbers?.querySelector('[aria-current="page"]')?.focus();
        });
        listen(document.getElementById('applyRiderFilters'), 'click', () => filterRows());
        listen(document.getElementById('resetRiderFilters'), 'click', () => {
            if (search) search.value = '';
            if (accountFilter) accountFilter.value = 'all';
            if (availabilityFilter) availabilityFilter.value = 'all';
            accountFilter?.dispatchEvent(new Event('change', { bubbles: true }));
            availabilityFilter?.dispatchEvent(new Event('change', { bubbles: true }));
            filterRows();
        });

        document.querySelectorAll('[data-rm-dropdown]').forEach(dropdown => {
            const select = dropdown.querySelector('select');
            const trigger = dropdown.querySelector('[data-rm-status-trigger]');
            const label = dropdown.querySelector('[data-rm-status-label]');
            const menu = dropdown.querySelector('[data-rm-status-menu]');
            const options = Array.from(dropdown.querySelectorAll('[data-rm-status-option]'));
            if (!select || !trigger || !menu || !options.length) return;

            function syncStatus() {
                if (label) label.textContent = select.options[select.selectedIndex]?.text || '';
                options.forEach(option => option.setAttribute('aria-selected', String(option.dataset.value === select.value)));
            }
            function closeStatus({ restoreFocus = false } = {}) {
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                if (restoreFocus) trigger.focus({ preventScroll: true });
            }
            function openStatus() {
                menu.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
                (options.find(option => option.getAttribute('aria-selected') === 'true') || options[0])?.focus({ preventScroll: true });
            }
            dropdown.dataset.enhanced = 'true';
            select.tabIndex = -1;
            select.setAttribute('aria-hidden', 'true');
            trigger.hidden = false;
            syncStatus();
            closeStatus();
            listen(select, 'change', syncStatus);
            listen(trigger, 'click', () => menu.hidden ? openStatus() : closeStatus());
            listen(trigger, 'keydown', event => {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') { event.preventDefault(); openStatus(); }
            });
            listen(menu, 'click', event => {
                const option = event.target.closest('[data-rm-status-option]');
                if (!option) return;
                select.value = option.dataset.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                closeStatus({ restoreFocus: true });
            });
            listen(dropdown, 'keydown', event => {
                if (menu.hidden) return;
                if (event.key === 'Escape') { event.preventDefault(); closeStatus({ restoreFocus: true }); }
                else if (event.key === 'Tab') closeStatus({ restoreFocus: true });
                else if (['ArrowDown','ArrowUp','Home','End'].includes(event.key) && menu.contains(event.target)) {
                    event.preventDefault();
                    const index = options.indexOf(document.activeElement);
                    const nextIndex = event.key === 'Home' ? 0 : event.key === 'End' ? options.length - 1
                        : (index + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length;
                    options[nextIndex]?.focus();
                }
            });
            listen(document, 'click', event => { if (!dropdown.contains(event.target)) closeStatus(); });
            listen(dropdown, 'focusout', event => { if (!dropdown.contains(event.relatedTarget)) closeStatus(); });
        });

        function openAddModal() {
            if (!modal) return;
            lastFocused = document.activeElement;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.documentElement.style.overflow = 'hidden';
            window.setTimeout(function () {
                modal.querySelector('input, select, button')?.focus({ preventScroll: true });
            }, 0);
        }

        function closeAddModal() {
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.documentElement.style.overflow = '';
            lastFocused?.focus?.({ preventScroll: true });
        }

        listen(search, 'input', filterRows, { passive: true });
        listen(accountFilter, 'change', filterRows);
        listen(availabilityFilter, 'change', filterRows);
        listen(openModal, 'click', openAddModal);
        listen(closeModal, 'click', closeAddModal);
        listen(cancelModal, 'click', closeAddModal);
        listen(modal, 'click', function (event) {
            if (event.target === modal) closeAddModal();
        });
        listen(document, 'keydown', function (event) {
            if (event.key === 'Escape' && modal?.classList.contains('is-open')) closeAddModal();
        });

        filterRows();

        @if($errors->any())
            openAddModal();
        @endif
    }

    window.__SARI_INIT_RIDER_MANAGEMENT__ = initRiderManagementPage;

    if (!window.__SARI_RIDER_MANAGEMENT_LIVEWIRE_LISTENER__) {
        window.__SARI_RIDER_MANAGEMENT_LIVEWIRE_LISTENER__ = true;
        document.addEventListener('livewire:navigated', function () {
            window.__SARI_INIT_RIDER_MANAGEMENT__?.();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRiderManagementPage, { once: true });
    } else {
        initRiderManagementPage();
    }
})();
</script>
@endsection
