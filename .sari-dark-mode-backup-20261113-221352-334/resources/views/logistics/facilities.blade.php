
@extends('layouts.logistics')

@section('title', 'Municipal Hubs | SARI Logistics')
@section('page-title', 'Municipal Hubs')

@php
    $fieldClass = 'w-full h-[48px] rounded-[14px] border border-[#dce3ea] bg-white px-4 text-[13px] text-[#2d3640] outline-none transition placeholder:text-[#a0a8b2] focus:border-[#d3ad63] focus:ring-4 focus:ring-[#d3ad63]/10';

    $labelClass = 'mb-2 block text-[12px] font-semibold text-[#49515a]';
@endphp

@push('styles')

<link
    rel="stylesheet"
    href="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.css"
>

<style>
    [hidden] {
        display: none !important;
    }

    /*
    |--------------------------------------------------------------------------
    | SELLER-STYLE MUNICIPAL HUB WORKSPACE
    |--------------------------------------------------------------------------
    */

    #logisticsFacilitiesWorkspace {
        --fh-gold: #d9950b;
        --fh-gold-dark: #c88708;
        --fh-border: #e8e2d9;
        --fh-muted: #887f75;
        --fh-shadow: 0 6px 18px rgba(33,24,14,.035);

        max-width: 1620px;
        font-family: Poppins, ui-sans-serif, sans-serif;
        color: #24211d;
    }

    /* Page heading */

    #logisticsFacilitiesWorkspace .fh-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;

        padding: 5px 2px 17px;
        border-bottom: 1px solid #e4e1dc;
    }

    #logisticsFacilitiesWorkspace .fh-eyebrow {
        color: #a76f11;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    #logisticsFacilitiesWorkspace .fh-title {
        margin-top: 5px;
        color: #24201b;
        font-size: clamp(23px,2.15vw,30px);
        font-weight: 750;
        letter-spacing: -.045em;
        line-height: 1.2;
    }

    #logisticsFacilitiesWorkspace .fh-title span {
        color: var(--fh-gold);
    }

    #logisticsFacilitiesWorkspace .fh-intro {
        margin-top: 6px;
        color: #897f75;
        font-size: 10px;
        line-height: 1.7;
    }

    #logisticsFacilitiesWorkspace .fh-heading-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    #logisticsFacilitiesWorkspace .fh-network-state {
        padding: 9px 13px;
        border: 1px solid #d5e8da;
        border-radius: 11px;
        background: #f5fbf7;
        color: #387b54;
    }

    #logisticsFacilitiesWorkspace .fh-network-state.is-warning {
        border-color: #ead9b4;
        background: #fffaf0;
        color: #996a15;
    }

    #logisticsFacilitiesWorkspace .fh-network-state small {
        display: block;
        color: #857e75;
        font-size: 8px;
        font-weight: 650;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    #logisticsFacilitiesWorkspace .fh-network-state strong {
        display: block;
        margin-top: 3px;
        font-size: 10px;
        font-weight: 650;
    }

    /* Buttons */

    #logisticsFacilitiesWorkspace .fh-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 39px;
        padding: 0 17px;
        border: 1px solid var(--fh-gold);
        border-radius: 10px;
        background: var(--fh-gold);
        color: white;
        font-size: 10px;
        font-weight: 650;
        box-shadow: 0 5px 12px rgba(194,132,10,.12);

        transition:
            background .15s ease,
            border-color .15s ease,
            box-shadow .15s ease;
    }

    #logisticsFacilitiesWorkspace .fh-primary:hover {
        background: var(--fh-gold-dark);
        border-color: var(--fh-gold-dark);
        box-shadow: 0 7px 16px rgba(194,132,10,.16);
    }

    /* KPI cards */

    #logisticsFacilitiesWorkspace .fh-kpis {
        display: grid;
        grid-template-columns: repeat(4,minmax(0,1fr));
        gap: 12px;
    }

    #logisticsFacilitiesWorkspace .fh-kpi {
        display: flex;
        justify-content: space-between;
        gap: 9px;
        min-width: 0;
        min-height: 94px;
        padding: 13px;
        border: 1px solid var(--fh-border);
        border-radius: 16px;
        background: #fff;
        box-shadow: var(--fh-shadow);

        transition:
            transform .18s cubic-bezier(.22,1,.36,1),
            border-color .18s ease,
            box-shadow .18s ease;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-label {
        color: #736a60;
        font-size: 10px;
        font-weight: 600;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-value {
        margin-top: 7px;
        color: #211d18;
        font-size: 24px;
        font-weight: 750;
        letter-spacing: -.04em;
        line-height: 1.2;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-sub {
        margin-top: 5px;
        color: #8a8177;
        font-size: 9px;
        line-height: 1.4;
    }

    /* Icon-only KPI design: no colored icon tiles or extra container footprint. */
    #logisticsFacilitiesWorkspace .fh-kpi-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 27px;
        width: 27px;
        height: 27px;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        color: #bd831c;
    }

    #logisticsFacilitiesWorkspace .fh-kpi:nth-child(2) .fh-kpi-icon {
        color: #45845f;
    }

    #logisticsFacilitiesWorkspace .fh-kpi:nth-child(3) .fh-kpi-icon {
        color: #597fac;
    }

    #logisticsFacilitiesWorkspace .fh-kpi:nth-child(4) .fh-kpi-icon {
        color: #6a7481;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-icon svg {
        width: 27px;
        height: 27px;
    }

    /* Management panels */

    #logisticsFacilitiesWorkspace .fh-panel,
    #logisticsFacilitiesWorkspace #hubEditor {
        overflow: hidden;
        border: 1px solid var(--fh-border) !important;
        border-radius: 17px !important;
        background: #fff !important;
        box-shadow: var(--fh-shadow) !important;
    }

    #logisticsFacilitiesWorkspace .fh-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid #eee8df;
    }

    #logisticsFacilitiesWorkspace .fh-panel-title {
        color: #28231d;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.035em;
    }

    #logisticsFacilitiesWorkspace .fh-panel-desc {
        margin-top: 4px;
        color: #948a80;
        font-size: 9px;
        line-height: 1.5;
    }

    #logisticsFacilitiesWorkspace .fh-panel-tools {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    #logisticsFacilitiesWorkspace .fh-search {
        height: 38px;
        width: 225px;
        max-width: 100%;
        padding: 0 12px;
        border: 1px solid #dae0e7;
        border-radius: 10px;
        background: white;
        font-size: 10px;
        outline: none;

        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    #logisticsFacilitiesWorkspace .fh-search:focus {
        border-color: var(--fh-gold);
        box-shadow: 0 0 0 3px rgba(217,149,11,.09);
    }

    #logisticsFacilitiesWorkspace .fh-counter {
        display: inline-flex;
        align-items: center;
        min-height: 35px;
        padding: 0 11px;
        border: 1px solid #ecdec1;
        border-radius: 9px;
        background: #fffaf0;
        color: #9f6c13;
        font-size: 9px;
        font-weight: 700;
    }

    /* Municipal hub cards */

    #logisticsFacilitiesWorkspace .fh-hubs {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        align-items: stretch;
        gap: 12px;
        padding: 14px;
    }

    #logisticsFacilitiesWorkspace .fh-hub {
        display: flex;
        flex-direction: column;
        min-width: 0;
        border: 1px solid #e8e2da;
        border-radius: 13px;
        background: white;
        box-shadow: 0 4px 13px rgba(35,27,18,.02);

        transition:
            transform .18s cubic-bezier(.22,1,.36,1),
            border-color .18s ease,
            box-shadow .18s ease;
    }

    #logisticsFacilitiesWorkspace .fh-hub-body {
        display: flex;
        gap: 12px;
        min-width: 0;
        padding: 15px 15px 12px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-symbol {
        display: grid;
        place-items: center;
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: #eff8f2;
        color: #44815d;
    }

    #logisticsFacilitiesWorkspace .fh-hub-symbol.is-inactive {
        background: #f4f2ef;
        color: #958d82;
    }

    #logisticsFacilitiesWorkspace .fh-hub-symbol svg {
        width: 18px;
        height: 18px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-name {
        color: #322b23;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.45;
    }

    #logisticsFacilitiesWorkspace .fh-status {
        display: inline-flex;
        padding: 4px 8px;
        border-radius: 999px;
        background: #edf8f1;
        color: #367b52;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .02em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    #logisticsFacilitiesWorkspace .fh-status.is-inactive {
        background: #f2f0ed;
        color: #847d74;
    }

    #logisticsFacilitiesWorkspace .fh-hub-address {
        margin-top: 7px;
        color: #655c52;
        font-size: 10px;
        line-height: 1.5;
    }

    #logisticsFacilitiesWorkspace .fh-hub-meta {
        margin-top: 5px;
        color: #93887c;
        font-size: 9px;
        line-height: 1.55;
    }

    /* Operator card */

    #logisticsFacilitiesWorkspace .fh-operator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin: 0 15px 13px;
        padding: 11px 12px;
        border: 1px solid #eee8e0;
        border-radius: 10px;
        background: #fbfbfc;
    }

    #logisticsFacilitiesWorkspace .fh-operator small {
        display: block;
        color: #9a8e7f;
        font-size: 8px;
        font-weight: 650;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    #logisticsFacilitiesWorkspace .fh-operator strong {
        display: block;
        margin-top: 5px;
        color: #4b4339;
        font-size: 10px;
        font-weight: 650;
    }

    #logisticsFacilitiesWorkspace .fh-operator p {
        margin-top: 3px;
        color: #8e8478;
        font-size: 9px;
        overflow-wrap: anywhere;
    }

    /* Consistent action placement */

    #logisticsFacilitiesWorkspace .fh-hub-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: auto;
        padding: 11px 15px;
        border-top: 1px solid #f0ebe5;
    }

    #logisticsFacilitiesWorkspace .fh-code {
        color: #8e8274;
        font-family: ui-monospace, monospace;
        font-size: 9px;
        font-weight: 650;
    }

    #logisticsFacilitiesWorkspace .fh-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }

    #logisticsFacilitiesWorkspace .fh-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 31px;
        padding: 0 11px;
        border: 1px solid #e2d8c9;
        border-radius: 8px;
        background: white;
        color: #65594c;
        font-size: 9px;
        font-weight: 600;

        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    #logisticsFacilitiesWorkspace .fh-action:hover {
        background: #fffaf1;
        border-color: #d9bf93;
        color: #94630b;
    }

    #logisticsFacilitiesWorkspace .fh-action.is-warning {
        border-color: #e9d8b5;
        background: #fff9ee;
        color: #986a19;
    }

    #logisticsFacilitiesWorkspace .fh-action.is-danger {
        border-color: #e8cccc;
        background: #fff7f7;
        color: #ac5555;
    }

    #logisticsFacilitiesWorkspace .fh-action.is-danger:hover {
        background: #ffefef;
        border-color: #deaaaa;
    }

    #logisticsFacilitiesWorkspace .fh-empty {
        grid-column: 1 / -1;
        padding: 38px 15px;
        border: 1px dashed #e5dbcd;
        border-radius: 12px;
        color: #8f8374;
        font-size: 10px;
        text-align: center;
    }



    /* =========================================================
       MUNICIPAL HUB DIRECTORY — CLEAN ROW + EYE DETAIL PATTERN
       ========================================================= */
    #logisticsFacilitiesWorkspace .fh-hub-table-wrap {
        overflow: visible;
        background: #fff;
    }

    #logisticsFacilitiesWorkspace .fh-hub-table {
        width: 100%;
        min-width: 0;
        border-collapse: collapse;
        table-layout: fixed;
    }

    #logisticsFacilitiesWorkspace .fh-hub-table thead tr {
        background: #f8fafb;
        border-bottom: 1px solid #e9edf1;
    }

    #logisticsFacilitiesWorkspace .fh-hub-table th {
        padding: 10px 16px;
        color: #6f7782;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .075em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    #logisticsFacilitiesWorkspace .fh-hub-row {
        border-bottom: 1px solid #eef1f4;
        background: #fff;
        transition: background-color .14s ease, box-shadow .14s ease;
    }

    #logisticsFacilitiesWorkspace .fh-hub-row:hover {
        background: #fcfcfd;
        box-shadow: inset 2px 0 0 var(--fh-gold);
    }

    #logisticsFacilitiesWorkspace .fh-hub-row td {
        padding: 12px 16px;
        vertical-align: middle;
        color: #4b5563;
        font-size: 9px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-identity {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-row-icon {
        display: grid;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        place-items: center;
        border: 1px solid #dce7df;
        border-radius: 9px;
        background: #f4faf6;
        color: #42805b;
    }

    #logisticsFacilitiesWorkspace .fh-hub-row-icon.is-inactive {
        border-color: #e5e7eb;
        background: #f7f7f8;
        color: #8b929b;
    }

    #logisticsFacilitiesWorkspace .fh-hub-row-icon svg {
        width: 15px;
        height: 15px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-row-name {
        overflow: hidden;
        color: #20242b;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #logisticsFacilitiesWorkspace .fh-hub-row-sub {
        overflow: hidden;
        margin-top: 3px;
        color: #8a929d;
        font-size: 9px;
        line-height: 1.45;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #logisticsFacilitiesWorkspace .fh-role-badge,
    #logisticsFacilitiesWorkspace .fh-route-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 24px;
        border: 1px solid #e3e7eb;
        border-radius: 999px;
        background: #fff;
        padding: 0 9px;
        color: #69727d;
        font-size: 8px;
        font-weight: 650;
        white-space: nowrap;
    }

    #logisticsFacilitiesWorkspace .fh-role-badge.is-main {
        border-color: #e5c980;
        background: #fffbf1;
        color: #94640a;
    }

    #logisticsFacilitiesWorkspace .fh-route-badge.is-active {
        border-color: #cfe6d7;
        background: #f3faf5;
        color: #367b52;
    }

    #logisticsFacilitiesWorkspace .fh-view-hub {
        display: inline-flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        border: 1px solid #dfe4e8;
        border-radius: 9px;
        background: #fff;
        color: #69727d;
        transition: border-color .14s ease, background-color .14s ease, color .14s ease, box-shadow .14s ease;
    }

    #logisticsFacilitiesWorkspace .fh-view-hub:hover,
    #logisticsFacilitiesWorkspace .fh-view-hub[aria-expanded="true"] {
        border-color: #d8bd79;
        background: #fffcf5;
        color: #a66f0d;
        box-shadow: 0 0 0 3px rgba(217,149,11,.07);
    }

    #logisticsFacilitiesWorkspace .fh-view-hub:focus-visible {
        outline: 2px solid #b57b0a;
        outline-offset: 2px;
    }

    #logisticsFacilitiesWorkspace .fh-view-hub svg {
        width: 16px;
        height: 16px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-detail-row td {
        padding: 0;
        border-bottom: 1px solid #e9edf1;
        background: #fafbfc;
    }

    #logisticsFacilitiesWorkspace .fh-hub-detail-panel {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        padding: 14px 16px 15px 58px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-detail-item {
        min-width: 0;
    }

    #logisticsFacilitiesWorkspace .fh-hub-detail-item small {
        display: block;
        color: #98a0aa;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    #logisticsFacilitiesWorkspace .fh-hub-detail-item strong,
    #logisticsFacilitiesWorkspace .fh-hub-detail-item p {
        display: block;
        margin-top: 4px;
        color: #4b5563;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.55;
        overflow-wrap: anywhere;
    }

    #logisticsFacilitiesWorkspace .fh-hub-detail-item p {
        font-weight: 400;
        color: #737d88;
    }

    #logisticsFacilitiesWorkspace .fh-hub-detail-actions {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
        padding-top: 10px;
        border-top: 1px solid #e8ecef;
    }

    /* Buyer Cart-inspired light-gray, readable directory empty states. */
    #logisticsFacilitiesWorkspace .fh-hub-table-empty {
        padding: 64px 20px;
        text-align: center;
    }

    #logisticsFacilitiesWorkspace .fh-hub-table:is(.is-empty, .is-filter-empty) {
        min-width: 0;
    }

    #logisticsFacilitiesWorkspace .fh-hub-table:is(.is-empty, .is-filter-empty) thead {
        display: none;
    }

    #logisticsFacilitiesWorkspace .fh-empty-icon {
        display: grid;
        width: 92px;
        height: 92px;
        place-items: center;
        margin: 0 auto;
        background: transparent;
        color: #b4bbc3;
    }

    #logisticsFacilitiesWorkspace .fh-empty-icon svg {
        width: 58px;
        height: 58px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.45;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    #logisticsFacilitiesWorkspace .fh-empty-title {
        margin: 18px 0 0;
        color: #9ca4ad;
        font-size: 22px;
        font-weight: 750;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    #logisticsFacilitiesWorkspace .fh-empty-copy {
        max-width: 520px;
        margin: 7px auto 0;
        color: #b0b7bf;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.55;
    }

    @media (max-width: 640px) {
        #logisticsFacilitiesWorkspace .fh-hub-table-empty { padding: 56px 18px; }
        #logisticsFacilitiesWorkspace .fh-empty-icon { width: 64px; height: 64px; }
        #logisticsFacilitiesWorkspace .fh-empty-icon svg { width: 40px; height: 40px; }
        #logisticsFacilitiesWorkspace .fh-empty-title { font-size: 20px; }
        #logisticsFacilitiesWorkspace .fh-empty-copy { font-size: 11px; }
    }

    @media (max-width: 900px) {
        #logisticsFacilitiesWorkspace .fh-hub-detail-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding-left: 16px;
        }
    }

    /* Hub editor */

    #logisticsFacilitiesWorkspace #hubEditor {
        border: 1px solid #e5eaf0 !important;
        border-radius: 24px !important;
        background: #ffffff !important;
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08), 0 4px 14px rgba(15, 23, 42, 0.04) !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor > div:first-child {
        padding: 22px 24px !important;
        border-color: #edf1f4 !important;
        background: #ffffff !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor > div:first-child h3 {
        font-size: 29px !important;
        font-weight: 760 !important;
        letter-spacing: -.04em;
        line-height: 1.12;
        color: #1f2933;
    }

    #logisticsFacilitiesWorkspace #sariFacilityForm {
        padding: 24px !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm > div.grid {
        gap: 24px !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm > div.grid > div:last-child > section {
        background: #ffffff !important;
        border: 1px solid #e8edf2 !important;
        border-radius: 18px !important;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05) !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm input:not([type="hidden"]):not([type="checkbox"]),

    #logisticsFacilitiesWorkspace #sariFacilityForm select,

    #logisticsFacilitiesWorkspace #sariFacilityForm textarea {
        font-family: inherit;
        font-size: 13px !important;
        background: #fff;
        color: #2d3640;
        border-color: #dce3ea;
        border-radius: 14px !important;

        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm input:not([type="hidden"]):not([type="checkbox"]):focus,

    #logisticsFacilitiesWorkspace #sariFacilityForm select:focus,

    #logisticsFacilitiesWorkspace #sariFacilityForm textarea:focus {
        outline: none;
        border-color: #d9950b !important;
        box-shadow: 0 0 0 3px rgba(217,149,11,.09) !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm .hub-input-valid {
        border-color: #68aa80 !important;
        background: #f8fdf9 !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm .hub-input-invalid {
        border-color: #dc7777 !important;
        background: #fff9f9 !important;
    }

    #logisticsFacilitiesWorkspace #hubSubmitButton {
        background: #d9950b;
        min-height: 50px;
        font-size: 13px !important;
        border-radius: 14px !important;
        box-shadow: 0 12px 28px rgba(194,132,10,.18);

        transition:
            background .15s ease,
            box-shadow .15s ease;
    }

    #logisticsFacilitiesWorkspace #hubSubmitButton:hover {
        background: #c88708;
        box-shadow: 0 7px 16px rgba(194,132,10,.16);
    }


    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].font-bold.uppercase.tracking-wider.text-[#b97805],
    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-bold.uppercase.tracking-wider.text-[#9e6b10],
    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-bold.uppercase.tracking-wider.text-[#69707a] {
        font-size: 11px !important;
        letter-spacing: .14em !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-bold.uppercase.tracking-wider.text-[#69707a],
    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-semibold.text-[#5f574e],
    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-semibold.text-[#3e4650],
    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-semibold.text-[#51493f] {
        color: #43505c !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-semibold.text-[#5f574e],
    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-semibold.text-[#3e4650],
    #logisticsFacilitiesWorkspace #hubEditor .text-[10px].font-semibold.text-[#51493f] {
        font-size: 13px !important;
        line-height: 1.45 !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].text-[#998f82],
    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].leading-5.text-[#8a919c],
    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].leading-5.text-[#7b8490],
    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].leading-5.text-[#6f7782],
    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].text-[#9a9084],
    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].text-[#998f82],
    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].leading-5.text-[#93897e],
    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].text-[#918779],
    #logisticsFacilitiesWorkspace #hubEditor .text-[9px].text-[#aaa198] {
        font-size: 12px !important;
        line-height: 1.7 !important;
        color: #7b8794 !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .rounded-\[15px\].border.border-\[\#e1e5e9\].bg-\[\#f9fafb\].p-4.sm\:p-5,
    #logisticsFacilitiesWorkspace #hubEditor .rounded-\[15px\].border.border-\[\#e1e5e9\].bg-white.p-4.sm\:p-5 {
        border-color: #e8edf2 !important;
        background: #ffffff !important;
        border-radius: 18px !important;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05) !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .border-b.border-\[\#eee5d8\],
    #logisticsFacilitiesWorkspace #hubEditor .border-b.border-\[\#e5e7eb\],
    #logisticsFacilitiesWorkspace #hubEditor .border-t.border-\[\#eee5d8\] {
        border-color: #edf1f4 !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .rounded-\[10px\].border.border-\[\#eadfc9\].bg-\[\#fffaf2\].px-3.py-3.text-\[10px\].text-\[\#98753b\] {
        border-color: #dce5ef !important;
        background: #f7fafc !important;
        color: #51606d !important;
        font-size: 12px !important;
        line-height: 1.65 !important;
        border-radius: 14px !important;
        padding: 14px 16px !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .rounded-\[11px\].border.border-\[\#eee4d3\].bg-\[\#fffdf8\].p-4 {
        border-color: #e5ebf1 !important;
        background: #ffffff !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04) !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .rounded-full.px-3.py-1.text-\[8px\].font-bold {
        font-size: 10px !important;
        padding: 7px 12px !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor .rounded-\[9px\].border.border-\[\#e4dacd\].px-4.py-2.text-\[10px\].font-semibold.text-\[\#81786c\] {
        border-color: #dce3ea !important;
        color: #51606d !important;
        font-size: 12px !important;
        border-radius: 12px !important;
        background: #fff !important;
    }


    /* Address directory */

    .hub-address-wrap {
        position: relative;
    }

    .hub-address-results {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        z-index: 50;
        max-height: 215px;
        overflow-y: auto;
        padding: 5px;
        border: 1px solid #e6dccc;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(42,31,18,.12);
    }

    .hub-address-results button {
        display: block;
        width: 100%;
        padding: 10px 11px;
        border-radius: 7px;
        color: #51483e;
        font-size: 11px;
        text-align: left;
    }

    .hub-address-results button:hover,
    .hub-address-results button:focus {
        outline: none;
        background: #fff5df;
        color: #99650b;
    }

    /* Map */

    #sariFacilityMap {
        width: 100%;
        height: 340px;
        overflow: hidden;
        border: 1px solid #e5e2dd;
        border-radius: 12px;
        background: #eef1ed;
    }

    #sariFacilityMap .maplibregl-canvas {
        outline: none;
    }

    #sariFacilityMap .maplibregl-ctrl-group {
        overflow: hidden;
        border: 1px solid #e7ddcf;
        border-radius: 9px;
        box-shadow: 0 7px 20px rgba(64,50,34,.08);
    }

    /* Warehouse pin: same icon in the editor and saved-location detail modal.
       Anchor at the pointed tip, not the visual center. No letters/initials. */
    .sari-facility-pin {
        position: relative;
        display: block;
        box-sizing: border-box;
        width: 56px;
        height: 58px;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        color: #fff;
        box-shadow: none;
        font-size: 0;
        cursor: grab;
        user-select: none;
        -webkit-user-select: none;
    }
    .sari-facility-pin:active { cursor: grabbing; }
    .sari-facility-pin::before {
        content: '';
        position: absolute;
        top: 0;
        left: 3px;
        width: 50px;
        height: 50px;
        box-sizing: border-box;
        border: 3px solid #fff;
        border-radius: 15px;
        background: #293541;
        box-shadow: 0 9px 24px rgba(24,37,51,.26), 0 2px 5px rgba(24,37,51,.12);
    }
    .sari-facility-pin::after {
        content: '';
        position: absolute;
        top: 45px;
        left: 19px;
        border-left: 9px solid transparent;
        border-right: 9px solid transparent;
        border-top: 13px solid #293541;
        filter: drop-shadow(0 2px 2px rgba(24,37,51,.18));
    }
    .sari-facility-pin svg {
        position: absolute;
        z-index: 1;
        top: 12px;
        left: 16px;
        width: 24px;
        height: 24px;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
        pointer-events: none;
    }

    /* Detail maps are read-only; only the editor pin can be dragged. */
    #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .sari-facility-pin,
    #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .sari-facility-pin:active {
        cursor: default;
    }

    /* Password fields */

    .hub-password-wrap {
        position: relative;
        margin-top: 6px;
    }

    .hub-password-wrap input {
        padding-right: 46px !important;
    }

    .hub-password-eye {
        position: absolute;
        top: 50%;
        right: 6px;
        transform: translateY(-50%);
        display: grid;
        place-items: center;
        width: 35px;
        height: 35px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #82776a;
        cursor: pointer;
    }

    .hub-password-eye:hover {
        background: #fff3dc;
        color: #b97805;
    }

    .hub-password-eye svg {
        width: 17px;
        height: 17px;
        pointer-events: none;
    }

    .hub-password-rules {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 9px;
        margin-top: 12px;
        padding: 13px;
        border: 1px solid #eadfcf;
        border-radius: 11px;
        background: white;
    }

    .hub-password-rule {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #93897d;
        font-size: 10px;
    }

    .hub-password-rule::before {
        content: "○";
        font-size: 13px;
    }

    .hub-password-rule.valid {
        color: #328354;
    }

    .hub-password-rule.valid::before {
        content: "✓";
        font-weight: 800;
    }

    .hub-password-rule.invalid {
        color: #c04f4f;
    }

    .hub-password-rule.invalid::before {
        content: "✕";
        font-weight: 800;
    }

    .hub-feedback {
        display: block;
        margin-top: 7px;
        color: #93897d;
        font-size: 10px;
        line-height: 16px;
    }

    .hub-feedback.valid {
        color: #328354;
        font-weight: 600;
    }

    .hub-feedback.invalid {
        color: #c04f4f;
        font-weight: 600;
    }

    .hub-input-valid {
        border-color: #68aa80 !important;
        background: #f8fdf9 !important;
    }

    .hub-input-invalid {
        border-color: #dc7777 !important;
        background: #fff9f9 !important;
    }

    /* Hover */

    @media (hover:hover) and (pointer:fine) {
        #logisticsFacilitiesWorkspace .fh-kpi:hover,
        #logisticsFacilitiesWorkspace .fh-hub:hover {
            transform: translateY(-2px);
            border-color: #d8c9b2;
            box-shadow: 0 13px 28px rgba(43,33,21,.055);
        }
    }

    /* Responsive */

    @media (max-width:1199px) {
        #logisticsFacilitiesWorkspace .fh-kpis {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
    }


    @media (max-width: 1024px) {
        #logisticsFacilitiesWorkspace .fh-hub-table-wrap {
            overflow: visible;
        }

        #logisticsFacilitiesWorkspace .fh-hub-table {
            table-layout: auto;
        }

        #logisticsFacilitiesWorkspace .fh-hub-table th,
        #logisticsFacilitiesWorkspace .fh-hub-row td {
            padding-left: 12px;
            padding-right: 12px;
        }

        #logisticsFacilitiesWorkspace .fh-hub-row-sub,
        #logisticsFacilitiesWorkspace .fh-hub-row td .font-mono,
        #logisticsFacilitiesWorkspace .fh-hub-row td p {
            overflow-wrap: anywhere;
            white-space: normal;
        }
    }

    @media (max-width:767px) {
        #logisticsFacilitiesWorkspace .fh-hubs {
            grid-template-columns: 1fr;
        }

        #logisticsFacilitiesWorkspace .fh-heading {
            align-items: flex-start;
        }

        #logisticsFacilitiesWorkspace .fh-panel-tools {
            width: 100%;
        }

        #logisticsFacilitiesWorkspace .fh-panel-tools input {
            flex: 1;
            min-width: 0;
        }
    }

    @media (max-width:640px) {
        #sariFacilityMap {
            height: 270px;
        }

        .hub-password-rules {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width:480px) {
        #logisticsFacilitiesWorkspace .fh-heading-actions {
            width: 100%;
        }

        #logisticsFacilitiesWorkspace .fh-kpis {
            gap: 9px;
        }

        #logisticsFacilitiesWorkspace .fh-kpi {
            min-height: 91px;
            padding: 11px;
        }

        #logisticsFacilitiesWorkspace .fh-kpi-icon,
        #logisticsFacilitiesWorkspace .fh-kpi-icon svg {
            width: 23px;
            height: 23px;
        }

        #logisticsFacilitiesWorkspace .fh-kpi-icon {
            display: inline-flex;
            flex-basis: 23px;
        }

        #logisticsFacilitiesWorkspace .fh-title {
            font-size: 23px;
        }
    }

    @media (prefers-reduced-motion:reduce) {
        #logisticsFacilitiesWorkspace *,
        #logisticsFacilitiesWorkspace *::before,
        #logisticsFacilitiesWorkspace *::after {
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
        }

        #logisticsFacilitiesWorkspace .fh-kpi:hover,
        #logisticsFacilitiesWorkspace .fh-hub:hover {
            transform: none !important;
        }
    }

    /* Directory: precise column allocation, no forced overflow, and plain eye actions. */
    #logisticsFacilitiesWorkspace .fh-hub-table-wrap { overflow: visible; }
    #logisticsFacilitiesWorkspace .fh-hub-table { width: 100%; min-width: 0; table-layout: fixed; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:nth-child(1) { width: 27%; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:nth-child(2) { width: 19%; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:nth-child(3) { width: 10%; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:nth-child(4) { width: 12%; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:nth-child(5) { width: 10%; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:nth-child(6) { width: 15%; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:nth-child(7) { width: 7%; }
    #logisticsFacilitiesWorkspace .fh-hub-table th,
    #logisticsFacilitiesWorkspace .fh-hub-row td { padding-inline: 10px; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:first-child,
    #logisticsFacilitiesWorkspace .fh-hub-row td:first-child { padding-left: 18px; }
    #logisticsFacilitiesWorkspace .fh-hub-table th:last-child,
    #logisticsFacilitiesWorkspace .fh-hub-row td:last-child { padding-right: 16px; text-align: right; }
    #logisticsFacilitiesWorkspace .fh-hub-table th { font-size: 9px; }
    #logisticsFacilitiesWorkspace .fh-hub-row td { font-size: 10px; }
    #logisticsFacilitiesWorkspace .fh-hub-row td:nth-child(6) { overflow-wrap: anywhere; }
    #logisticsFacilitiesWorkspace .fh-hub-row td:nth-child(6) .font-mono {
        display: block; white-space: normal; overflow-wrap: anywhere; line-height: 1.45;
    }
    #logisticsFacilitiesWorkspace .fh-view-hub,
    #logisticsFacilitiesWorkspace .fh-view-hub:hover,
    #logisticsFacilitiesWorkspace .fh-view-hub:active {
        width: 36px; height: 36px; padding: 0; border: 0; border-radius: 0;
        background: transparent; box-shadow: none; color: #697580;
    }
    #logisticsFacilitiesWorkspace .fh-view-hub:hover { color: #aa720d; }
    #logisticsFacilitiesWorkspace .fh-view-hub svg { width: 21px; height: 21px; }
    #logisticsFacilitiesWorkspace .fh-view-hub:focus-visible {
        outline: 2px solid #b57b0a; outline-offset: 2px; border-radius: 6px;
    }

    /* Match the actual shared Buyer parity stylesheet cascade.
       It applies !important rounded-card styling to .fh-hub-detail-panel.
       Inside a native dialog that creates a second floating box/radius.
       Keep the one outer dialog radius; make the inner surface seamless. */
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog {
        overflow: hidden;
        background: #fff !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-dialog-surface {
        border: 0 !important;
        border-radius: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
        overflow-y: auto;
        max-height: min(90dvh, 820px);
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-detail-panel {
        margin: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: #fff !important;
        background-image: none !important;
        box-shadow: none !important;
        transform: none !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-dialog-header {
        border-radius: 0 !important;
        border-bottom: 1px solid #e9edf2 !important;
        box-shadow: none !important;
        background: #fff !important;
    }
    /* Gold is only for Deactivate; the existing Activate action remains neutral. */
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-action.fh-deactivate-action {
        color: #fff !important;
        background: #d59617 !important;
        border-color: #d59617 !important;
        box-shadow: none !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-action.fh-deactivate-action:hover,
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-action.fh-deactivate-action:focus-visible {
        color: #fff !important;
        background: #b97805 !important;
        border-color: #b97805 !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-action.fh-deactivate-action:focus-visible {
        outline: 2px solid #d59617;
        outline-offset: 3px;
    }

    /* Native floating detail dialog: no expansion underneath table rows. */
    #logisticsFacilitiesWorkspace .fh-hub-dialog {
        position: fixed; inset: 0; width: min(640px, calc(100vw - 32px));
        max-width: 640px; max-height: min(90dvh, 820px); margin: auto;
        padding: 0; overflow-y: auto; border: 1px solid #e1e6ed;
        border-radius: 20px; background: #fff; color: #25303b;
        box-shadow: 0 32px 92px rgba(15,23,42,.24), 0 8px 24px rgba(15,23,42,.08);
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog:not([open]) { display: none; }
    #logisticsFacilitiesWorkspace .fh-hub-dialog::backdrop { background: rgba(18,25,34,.52); }
    #logisticsFacilitiesWorkspace .fh-hub-dialog-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        gap: 20px; padding: 22px 24px; border-bottom: 1px solid #e9edf2; background: #fff;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog-kicker {
        margin: 0; font-size: 10px; line-height: 1.3; font-weight: 750;
        letter-spacing: .12em; color: #aa720d; text-transform: uppercase;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog-title {
        margin: 6px 0 0; font-size: 22px; font-weight: 750;
        line-height: 1.25; letter-spacing: -.03em; overflow-wrap: anywhere;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog-subtitle {
        margin: 6px 0 0; font-size: 11px; color: #8a95a1; overflow-wrap: anywhere;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog-close {
        display: inline-grid; flex: 0 0 34px; width: 34px; height: 34px;
        place-items: center; border: 1px solid #e1e6ed; border-radius: 9px;
        background: white; color: #667382;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog-close:hover { background: #f7f9fb; }
    #logisticsFacilitiesWorkspace .fh-hub-dialog-close svg { width: 18px; height: 18px; }
    #logisticsFacilitiesWorkspace .fh-hub-dialog-close:focus-visible { outline: 2px solid #b57b0a; outline-offset: 2px; }
    /* Hub details: a vertical stack of four clearly separated information cards.
       The modal itself stays seamless: no second frame around the entire body. */
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-detail-panel {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 12px !important;
        padding: 20px 22px 22px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-detail-item {
        display: block !important;
        width: 100%;
        min-width: 0;
        grid-column: auto !important;
        box-sizing: border-box;
        padding: 15px 17px !important;
        border: 1px solid #e7edf3 !important;
        border-radius: 13px !important;
        background: #f8fafc !important;
        background-image: none !important;
        box-shadow: none !important;
        transform: none !important;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-hub-detail-item small {
        display: block;
        color: #84909e;
        font-size: 10px;
        font-weight: 750;
        line-height: 1.3;
        letter-spacing: .07em;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-hub-detail-item strong,
    #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-hub-detail-item p {
        margin-top: 6px;
        font-size: 12px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-hub-detail-item p {
        color: #566273;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-detail-actions {
        width: 100%;
        grid-column: auto !important;
        gap: 9px;
        padding-top: 16px;
        margin-top: 4px;
        border-top: 1px solid #e9edf2;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-action {
        min-height: 37px; padding-inline: 14px; font-size: 11px;
        border-color: #dce3ea; color: #4b5563;
    }
    #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-action:hover { border-color: #d4a851; color: #98660b; background: #fff; }
    @media (max-width: 1100px) {
        /* The seven-column directory becomes two-column cards instead of scrolling. */
        #logisticsFacilitiesWorkspace .fh-hub-table { display: block; width: 100%; }
        #logisticsFacilitiesWorkspace .fh-hub-table thead { display: none; }
        #logisticsFacilitiesWorkspace .fh-hub-table tbody { display: block; }
        #logisticsFacilitiesWorkspace .fh-hub-table tbody + tbody { border-top: 1px solid #edf0f3; }
        #logisticsFacilitiesWorkspace .fh-hub-row {
            position: relative; display: grid; grid-template-columns: repeat(2,minmax(0,1fr));
            gap: 14px 18px; padding: 17px 19px;
        }
        #logisticsFacilitiesWorkspace .fh-hub-row td {
            display: block; min-width: 0; padding: 0 !important; text-align: left !important;
        }
        #logisticsFacilitiesWorkspace .fh-hub-row td:first-child { grid-column: 1 / -1; padding-right: 44px !important; }
        #logisticsFacilitiesWorkspace .fh-hub-row td:nth-child(6) { grid-column: 1 / -1; }
        #logisticsFacilitiesWorkspace .fh-hub-row td:last-child {
            position: absolute; top: 13px; right: 15px; padding: 0 !important;
        }
        #logisticsFacilitiesWorkspace .fh-hub-row td:not(:first-child):not(:last-child)::before {
            content: attr(data-label); display: block; margin-bottom: 6px;
            font-size: 9px; font-weight: 750; letter-spacing: .07em;
            color: #95a0ad; text-transform: uppercase;
        }
        #logisticsFacilitiesWorkspace .fh-hub-row-name,
        #logisticsFacilitiesWorkspace .fh-hub-row-sub { white-space: normal; overflow-wrap: anywhere; }
        #logisticsFacilitiesWorkspace .fh-hub-row-name { font-size: 12px; }
        #logisticsFacilitiesWorkspace .fh-hub-row-sub { font-size: 10px; }
        #logisticsFacilitiesWorkspace .fh-hub-table-empty { display: block; }
    }
    @media (max-width: 540px) {
        #logisticsFacilitiesWorkspace .fh-hub-dialog { width: calc(100vw - 20px); border-radius: 16px; }
        #logisticsFacilitiesWorkspace .fh-hub-dialog-header { padding: 18px; }
        #logisticsFacilitiesWorkspace .fh-hub-dialog-title { font-size: 19px; }
        #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-detail-panel {
            gap: 10px !important; padding: 14px !important;
        }
        #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-detail-item {
            padding: 13px 14px !important;
            border-radius: 11px !important;
        }
        #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-hub-detail-actions { justify-content: stretch; }
        #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-hub-detail-actions > form { flex: 1 1 auto; }
        #logisticsFacilitiesWorkspace .fh-hub-dialog .fh-hub-detail-actions .fh-action { width: 100%; }
    }

    /* Admin User Management visual parity: independent filter strip + clean data table. */
    #logisticsFacilitiesWorkspace .fh-admin-filters {
        display: grid;
        grid-template-columns: minmax(240px,1fr) 158px 178px 122px 82px;
        align-items: center;
        gap: 12px;
        margin: 0;
    }
    #logisticsFacilitiesWorkspace .fh-admin-search,
    #logisticsFacilitiesWorkspace .fh-admin-select-wrap { position: relative; min-width: 0; display: block; }
    #logisticsFacilitiesWorkspace .fh-admin-filters .sr-only {
        position: absolute !important; width: 1px !important; height: 1px !important;
        padding: 0 !important; margin: -1px !important; overflow: hidden !important;
        clip: rect(0,0,0,0) !important; white-space: nowrap !important; border: 0 !important;
    }
    #logisticsFacilitiesWorkspace #facilitySearchEmpty.hidden { display: none !important; }
    #logisticsFacilitiesWorkspace .fh-admin-search > svg {
        position: absolute; top: 50%; left: 15px; transform: translateY(-50%);
        width: 17px; height: 17px; color: #98a2b3; pointer-events: none;
    }
    #logisticsFacilitiesWorkspace .fh-admin-select-wrap > svg {
        position: absolute; top: 50%; right: 13px; transform: translateY(-50%);
        width: 14px; height: 14px; color: #8c96a2; pointer-events: none;
    }
    #logisticsFacilitiesWorkspace .fh-admin-search input,
    #logisticsFacilitiesWorkspace .fh-admin-select-wrap select {
        box-sizing: border-box; width: 100%; height: 44px;
        border: 1px solid #dfe5eb; border-radius: 12px;
        background: #fff; color: #344054; outline: none;
        font: 500 11px/1.3 Poppins,ui-sans-serif,sans-serif;
        box-shadow: none; transition: border-color .15s ease, box-shadow .15s ease;
    }
    #logisticsFacilitiesWorkspace .fh-admin-search input { padding: 0 14px 0 43px; }
    #logisticsFacilitiesWorkspace .fh-admin-search input::placeholder { color: #98a2b3; font-weight: 400; }
    #logisticsFacilitiesWorkspace .fh-admin-select-wrap select { padding: 0 32px 0 13px; appearance: none; cursor: pointer; }
    #logisticsFacilitiesWorkspace .fh-admin-filters :is(input,select,button):focus-visible,
    #logisticsFacilitiesWorkspace .fh-admin-pagination :is(button,select):focus-visible {
        outline: 2px solid #c48c1b; outline-offset: 2px;
    }
    #logisticsFacilitiesWorkspace .fh-admin-filters :is(input,select):focus {
        border-color: #d7a448; box-shadow: 0 0 0 3px rgba(217,149,0,.08);
    }
    #logisticsFacilitiesWorkspace .fh-admin-apply,
    #logisticsFacilitiesWorkspace .fh-admin-reset {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 44px; border-radius: 12px; padding: 0 12px;
        white-space: nowrap; font-size: 11px; font-weight: 650;
        transition: background-color .15s ease,border-color .15s ease;
    }
    #logisticsFacilitiesWorkspace .fh-admin-apply {
        background: #d99500; border: 1px solid #d99500; color: #fff;
        box-shadow: 0 6px 14px rgba(217,149,0,.12);
    }
    #logisticsFacilitiesWorkspace .fh-admin-apply:hover { background: #b77c00; border-color: #b77c00; }
    #logisticsFacilitiesWorkspace .fh-admin-apply svg { width: 15px; height: 15px; }
    #logisticsFacilitiesWorkspace .fh-admin-reset {
        background: white; border: 1px solid #dfe5eb; color: #596575;
    }
    #logisticsFacilitiesWorkspace .fh-admin-reset:hover { background: #f9fafb; }

    /* Table retains the seven backend-driven fields and eye-modal action. */
    #logisticsFacilitiesWorkspace .fh-admin-directory {
        overflow: hidden; border-color: #e5e9ef !important; border-radius: 18px !important;
        box-shadow: 0 2px 5px rgba(15,23,42,.035),0 12px 28px rgba(15,23,42,.075) !important;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-panel-head {
        padding: 18px 20px; border-bottom: 1px solid #eef0f3;
        background: #fff;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-panel-title {
        color: #222b36; font-size: 16px; font-weight: 750;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-panel-desc {
        color: #8a95a2; font-size: 11px; line-height: 1.5;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-counter {
        background: #f9fafb; border-color: #e5e9ef; color: #667085;
        font-size: 10px; min-height: 30px;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table-wrap { overflow: visible; }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table {
        width: 100%; min-width: 0; table-layout: fixed;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table thead tr {
        background: #f9fafb; border-bottom: 1px solid #eceff3;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table thead th {
        padding-top: 13px; padding-bottom: 13px; color: #748092;
        font-size: 10px; font-weight: 700; letter-spacing: .065em;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row {
        border-bottom-color: #edf0f3; background: #fff;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row:hover {
        background: #fdfbf7; box-shadow: inset 2px 0 0 #d99500;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td {
        padding-top: 15px; padding-bottom: 15px; color: #475467; font-size: 11px;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row-name {
        font-size: 12px; line-height: 1.4; color: #1e293b;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row-sub {
        font-size: 9.5px; line-height: 1.5; color: #98a2b3;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td:nth-child(6) .font-mono {
        font-size: 9.5px; color: #667085;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-role-badge,
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-route-badge {
        min-height: 27px; padding: 3px 10px; font-size: 10px;
    }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-view-hub { color: #667085; }
    #logisticsFacilitiesWorkspace .fh-admin-directory .fh-view-hub:hover { color: #aa720d; }

    #logisticsFacilitiesWorkspace .fh-admin-footer {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
        gap: 12px; padding: 13px 20px; border-top: 1px solid #edf0f3;
        background: #fff;
    }
    #logisticsFacilitiesWorkspace #facilityResultCount {
        margin: 0; font-size: 11px; color: #667085; line-height: 1.5;
    }
    #logisticsFacilitiesWorkspace .fh-admin-pagination {
        display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap;
        gap: 8px; font-size: 12px; color: #667085;
    }
    #logisticsFacilitiesWorkspace .fh-admin-pagination button,
    #logisticsFacilitiesWorkspace .fh-admin-pagination select {
        height: 36px; border: 1px solid #e4e7ec; border-radius: 8px; background: #fff;
        font-size: 11px; font-weight: 500; color: #475467; padding: 0 11px;
    }
    #logisticsFacilitiesWorkspace .fh-admin-pagination select { width: 64px; padding: 0 7px; }
    #logisticsFacilitiesWorkspace .fh-admin-pagination #facilityNextPage:not(:disabled) {
        background: #d99500; border-color: #d99500; color: white;
    }
    #logisticsFacilitiesWorkspace .fh-admin-pagination button:disabled {
        background: #f9fafb; border-color: #eef0f3; color: #98a2b3; cursor: not-allowed;
    }
    #logisticsFacilitiesWorkspace #facilityPageNumber { min-width: 39px; text-align: center; font-variant-numeric: tabular-nums; }

    @media(max-width: 1140px) {
        #logisticsFacilitiesWorkspace .fh-admin-filters {
            grid-template-columns: minmax(0,1fr) 150px 170px;
        }
        #logisticsFacilitiesWorkspace .fh-admin-apply,
        #logisticsFacilitiesWorkspace .fh-admin-reset { grid-row: 2; }
    }
    @media(max-width: 700px) {
        #logisticsFacilitiesWorkspace .fh-admin-filters {
            grid-template-columns: repeat(2,minmax(0,1fr)); gap: 9px;
        }
        #logisticsFacilitiesWorkspace .fh-admin-search { grid-column: 1 / -1; }
        #logisticsFacilitiesWorkspace .fh-admin-apply,
        #logisticsFacilitiesWorkspace .fh-admin-reset { grid-row: auto; }
        #logisticsFacilitiesWorkspace .fh-admin-footer { align-items: flex-start; }
        #logisticsFacilitiesWorkspace .fh-admin-pagination { justify-content: flex-start; }
    }
    @media(max-width: 380px) {
        #logisticsFacilitiesWorkspace .fh-admin-filters { grid-template-columns: 1fr; }
        #logisticsFacilitiesWorkspace .fh-admin-search { grid-column: auto; }
    }

    /* ============================================================
       MUNICIPAL HUB DIRECTORY / ADMIN-INSPIRED CLEANUP
       One surface, one toolbar, one table. Shared shell CSS must not
       inject additional padding, shadows, or rounded inner panels.
       ============================================================ */
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #e5eaf0 !important;
        border-radius: 18px !important;
        background: #fff !important;
        box-shadow: 0 12px 28px rgba(15,23,42,.07), 0 2px 6px rgba(15,23,42,.025) !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 17px 21px !important;
        background: #fff !important;
        border-bottom: 1px solid #eef0f3 !important;
        min-height: 0;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-panel-title {
        margin: 0 !important;
        color: #212b36;
        font-size: 17px !important;
        font-weight: 750;
        line-height: 1.35;
        letter-spacing: -.02em;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-counter {
        flex: 0 0 auto;
        min-height: 29px;
        border-color: #e5eaf0;
        background: #f8fafc;
        padding-inline: 11px;
        color: #667085;
        white-space: nowrap;
        font-size: 10px;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip {
        padding: 13px 18px;
        border-bottom: 1px solid #eef1f4;
        background: #fff;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-filters {
        display: grid;
        width: 100%;
        min-width: 0;
        grid-template-columns: minmax(220px, 1fr) 150px 165px 120px 74px;
        gap: 9px;
        align-items: stretch;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters .fh-admin-search,
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters .fh-admin-select-wrap {
        display: block;
        position: relative;
        min-width: 0;
        margin: 0;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters :is(input,select) {
        box-sizing: border-box;
        display: block;
        min-width: 0;
        width: 100%;
        height: 40px;
        border: 1px solid #dce4eb;
        border-radius: 10px;
        background: #fff;
        color: #344054;
        font-family: Poppins, sans-serif;
        font-size: 11px !important;
        font-weight: 500;
        line-height: 1.4;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters .fh-admin-search input { padding: 0 12px 0 40px; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters .fh-admin-search > svg { left: 13px; width: 16px; height: 16px; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters .fh-admin-select-wrap select { padding: 0 29px 0 12px; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters .fh-admin-select-wrap > svg { right: 12px; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters :is(.fh-admin-apply,.fh-admin-reset) {
        box-sizing: border-box;
        min-height: 40px;
        height: 40px;
        border-radius: 10px;
        margin: 0;
        font-size: 10px;
        white-space: nowrap;
        box-shadow: none !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table-wrap,
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table {
        max-width: 100%;
        min-width: 0;
        overflow-x: visible;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table thead tr {
        background: #f8fafc;
        border-bottom: 1px solid #e9edf2;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table th {
        padding: 12px 9px;
        font-size: 9px !important;
        line-height: 1.4;
        color: #718096;
        font-weight: 750;
        white-space: nowrap;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td {
        padding: 13px 9px;
        min-width: 0;
        font-size: 10px !important;
        line-height: 1.6;
        vertical-align: middle;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table :is(th,td):first-child { padding-left: 19px; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table :is(th,td):last-child { padding-right: 19px; text-align: right; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table th:nth-child(1) { width: 28%; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table th:nth-child(2) { width: 16%; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table th:nth-child(3) { width: 10%; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table th:nth-child(4) { width: 13%; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table th:nth-child(5) { width: 9%; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table th:nth-child(6) { width: 17%; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table th:nth-child(7) { width: 7%; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row-name { font-size: 11px; font-weight: 700; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td:nth-child(6) .font-mono {
        font-size: 9px;
        white-space: normal;
        overflow-wrap: anywhere;
        line-height: 1.5;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-identity { gap: 9px; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row-icon { flex: 0 0 30px; width: 30px; height: 30px; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-view-hub {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        width: 34px;
        height: 34px;
        color: #687686;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-view-hub:hover { color: #b47a0e; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-view-hub svg { width: 20px; height: 20px; }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-admin-footer {
        padding: 12px 19px;
        border-top: 1px solid #edf0f3;
        background: #fff;
    }
    @media (max-width: 1190px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-filters {
            grid-template-columns: minmax(0,1fr) 165px 165px;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters :is(.fh-admin-apply,.fh-admin-reset) {
            grid-row: 2;
        }
    }
    @media (max-width: 1100px) {
        /* Existing mobile cards use grid layout, not an overflowing seven-column table. */
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table,
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table tbody {
            display: block;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table thead { display: none; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row {
            display: grid;
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td {
            display: block;
            padding: 0 !important;
            text-align: left !important;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td:last-child { text-align: center !important; }
    }
    @media (max-width: 700px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip { padding: 12px; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-filters {
            grid-template-columns: repeat(2,minmax(0,1fr));
            gap: 8px;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-search { grid-column: 1 / -1; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-filters :is(.fh-admin-apply,.fh-admin-reset) { grid-row: auto; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-panel-head { padding: 14px 15px !important; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-panel-title { font-size: 15px !important; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-admin-footer { padding: 12px 15px; }
    }
    @media (max-width: 380px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-filters { grid-template-columns: 1fr; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-search { grid-column: auto; }
    }


    /* ============================================================
       MUNICIPAL HUB DIRECTORY — ADMIN TABLE + SEPARATE FILTER ROW
       Keeps the existing data table, detail dialogs and actions intact.
       ============================================================ */
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip {
        display: block;
        width: 100%;
        padding: 0 !important;
        margin: 0 0 14px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        overflow: visible !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip > .fh-admin-filters {
        display: grid;
        grid-template-columns: minmax(230px,1fr) 175px 187px 140px 84px;
        gap: 12px;
        align-items: center;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        border: 0 !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-search input,
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-select-wrap select {
        height: 49px !important;
        border-color: #e0e5ed;
        border-radius: 13px;
        background: #fff;
        font-size: 11px !important;
        box-shadow: 0 1px 2px rgba(16,24,40,.025);
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip :is(.fh-admin-apply,.fh-admin-reset) {
        height: 49px !important;
        min-height: 49px !important;
        border-radius: 13px !important;
        font-size: 10px;
        font-weight: 650;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-apply {
        border-color: #d99a12;
        background: #d99a12;
        box-shadow: 0 6px 15px rgba(205,139,21,.15) !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-apply:hover {
        border-color: #bc8010;
        background: #bc8010;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-reset {
        border-color: #e0e5ed;
        background: #fff;
        color: #566477;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory {
        margin: 0 !important;
        border: 1px solid #e6eaf0 !important;
        border-radius: 17px !important;
        background: #fff !important;
        box-shadow: 0 13px 30px rgba(16,24,40,.065),0 2px 6px rgba(16,24,40,.025) !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-panel-head {
        padding: 16px 21px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-panel-title {
        font-size: 16px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-admin-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 20px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-page-size-label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #7c8795;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-page-size-label select {
        height: 33px;
        padding: 0 9px;
        border: 1px solid #e1e6ed;
        border-radius: 9px;
        background: #fff;
        color: #4b5563;
        font-size: 11px;
        outline-offset: 2px;
    }
    /* Pagination is a distinct row below the table, like Admin User Management. */
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        width: 100%;
        margin: 14px 0 4px !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-page-buttons {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav :is(.fh-page-arrow,.fh-page-number) {
        display: inline-flex;
        flex: 0 0 46px;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        padding: 0;
        border: 1px solid transparent !important;
        border-radius: 14px !important;
        background: #f0f2f5 !important;
        color: #8793a3 !important;
        font-size: 14px;
        font-weight: 650;
        line-height: 1;
        box-shadow: none !important;
        transition: background-color .15s ease, color .15s ease;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav .fh-page-arrow svg {
        width: 18px;
        height: 18px;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav .fh-page-number.is-current {
        border-color: #c99125 !important;
        background: #c99125 !important;
        color: #fff !important;
        box-shadow: 0 7px 17px rgba(201,145,37,.22) !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav :is(.fh-page-arrow,.fh-page-number):not(:disabled):hover:not(.is-current) {
        background: #e8ebf0 !important;
        color: #475467 !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav .fh-page-arrow:disabled {
        opacity: .45;
        cursor: not-allowed;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav :is(button):focus-visible {
        outline: 2px solid #b77b0d;
        outline-offset: 3px;
    }
    @media (max-width: 1190px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip > .fh-admin-filters {
            grid-template-columns: minmax(200px,1fr) minmax(130px,.45fr) minmax(150px,.5fr);
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-apply,
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-reset { grid-row: auto; }
    }
    @media (max-width: 700px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip > .fh-admin-filters {
            grid-template-columns: repeat(2,minmax(0,1fr));
            gap: 9px;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-search { grid-column: 1 / -1; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip :is(.fh-admin-apply,.fh-admin-reset) {
            grid-row: auto;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-admin-footer {
            padding: 12px 15px !important;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav :is(.fh-page-arrow,.fh-page-number) {
            flex-basis: 40px; width: 40px; height: 40px; border-radius: 12px !important;
        }
    }
    @media (max-width: 400px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip > .fh-admin-filters {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-search { grid-column: 1 / -1; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-select-wrap { grid-column: 1 / -1; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-admin-footer {
            flex-wrap: wrap;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-page-buttons { gap: 5px; }
    }


    /* Compact directory polish: maintain clear gaps, centered smaller pagination. */
    #logisticsContent #logisticsFacilitiesWorkspace > .fh-kpis {
        gap: 14px !important;
        margin-top: 16px !important;
        margin-bottom: 16px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace > .fh-kpis .fh-kpi {
        min-height: 96px !important;
        padding: 14px 16px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace > .fh-directory-filter-strip {
        margin: 0 0 14px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip > .fh-admin-filters {
        grid-template-columns: minmax(200px, 1fr) 145px 165px 120px 78px !important;
        gap: 10px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip :is(.fh-admin-search input, .fh-admin-select-wrap select, .fh-admin-apply, .fh-admin-reset) {
        box-sizing: border-box;
        height: 42px !important;
        min-height: 42px !important;
        border-radius: 10px !important;
        font-size: 10.5px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace > .fh-admin-directory {
        margin: 0 !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-panel-head {
        padding: 13px 18px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-table thead th {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td {
        padding-top: 11px !important;
        padding-bottom: 11px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-admin-footer {
        padding: 10px 18px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace > .fh-pagination-nav {
        display: flex;
        justify-content: center !important;
        align-items: center;
        gap: 5px !important;
        margin: 10px auto 0 !important;
        width: 100%;
        max-width: 100%;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav .fh-page-buttons {
        gap: 5px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav :is(.fh-page-arrow, .fh-page-number) {
        flex: 0 0 34px !important;
        width: 34px !important;
        height: 34px !important;
        min-height: 34px !important;
        padding: 0 !important;
        border-radius: 10px !important;
        font-size: 12px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav .fh-page-arrow svg {
        width: 15px !important;
        height: 15px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-pagination-nav .fh-page-number.is-current {
        box-shadow: 0 4px 10px rgba(201,145,37,.15) !important;
    }
    @media (max-width: 1100px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row {
            gap: 9px 14px !important;
            padding: 13px 15px !important;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td {
            padding: 0 !important;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row td:first-child {
            padding-right: 38px !important;
        }
    }
    @media (max-width: 1050px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip > .fh-admin-filters {
            grid-template-columns: minmax(160px,1fr) minmax(130px,.6fr) minmax(145px,.65fr) !important;
        }
    }
    @media (max-width: 700px) {
        #logisticsContent #logisticsFacilitiesWorkspace > .fh-kpis { gap: 10px !important; }
        #logisticsContent #logisticsFacilitiesWorkspace > .fh-kpis .fh-kpi { padding: 12px !important; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip > .fh-admin-filters {
            grid-template-columns: repeat(2,minmax(0,1fr)) !important;
            gap: 8px !important;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-search { grid-column: 1 / -1; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip :is(.fh-admin-apply,.fh-admin-reset) { grid-row: auto !important; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-panel-head { padding: 12px 14px !important; }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory > .fh-admin-footer { padding: 10px 14px !important; }
    }
    @media (max-width: 380px) {
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip > .fh-admin-filters {
            grid-template-columns: repeat(2,minmax(0,1fr)) !important;
        }
        #logisticsContent #logisticsFacilitiesWorkspace .fh-directory-filter-strip .fh-admin-select-wrap { grid-column: 1 / -1; }
    }


    /* Functional, focused sorting-hub editor. Scoped to prevent shared Buyer CSS bleed. */
    #logisticsFacilitiesWorkspace .fe-workspace {
        overflow: visible !important;
        border: 1px solid #e4e8ee !important;
        border-radius: 21px !important;
        background: #fff !important;
        box-shadow: 0 18px 42px rgba(18, 27, 44, .075), 0 4px 12px rgba(18, 27, 44, .035) !important;
    }
    #logisticsFacilitiesWorkspace .fe-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:23px 26px; border-bottom:1px solid #e9edf2; }
    #logisticsFacilitiesWorkspace .fe-heading { min-width:0; }
    #logisticsFacilitiesWorkspace .fe-kicker { margin:0 0 6px; color:#b67c0d; font-size:10px; font-weight:750; letter-spacing:.12em; text-transform:uppercase; }
    #logisticsFacilitiesWorkspace .fe-title { margin:0; font-size:clamp(22px,2vw,29px); line-height:1.18; color:#202833; font-weight:750; letter-spacing:-.035em; }
    #logisticsFacilitiesWorkspace .fe-intro { margin:8px 0 0; color:#818a97; font-size:12px; line-height:1.6; }
    #logisticsFacilitiesWorkspace .fe-header-actions { display:flex; flex:0 0 auto; align-items:center; gap:9px; }
    #logisticsFacilitiesWorkspace .fe-help-trigger, #logisticsFacilitiesWorkspace .fe-close { display:inline-flex; justify-content:center; align-items:center; min-height:38px; border:1px solid #e0e5eb; border-radius:11px; background:#fff; color:#596574; transition: border-color .15s ease, color .15s ease, background-color .15s ease; }
    #logisticsFacilitiesWorkspace .fe-help-trigger { width:38px; padding:0; }
    #logisticsFacilitiesWorkspace .fe-help-trigger svg { width:20px; height:20px; }
    #logisticsFacilitiesWorkspace .fe-close { padding:0 14px; font-size:11px; font-weight:650; }
    #logisticsFacilitiesWorkspace .fe-help-trigger:hover, #logisticsFacilitiesWorkspace .fe-close:hover { border-color:#e0bd79; color:#a57211; background:#fffcf6; }
    #logisticsFacilitiesWorkspace .fe-form { max-width:100%; padding:24px 26px 25px !important; }
    #logisticsFacilitiesWorkspace .fe-section-head { display:flex; gap:11px; align-items:center; padding-bottom:18px; margin-bottom:22px; border-bottom:1px solid #edf0f4; }
    #logisticsFacilitiesWorkspace .fe-section-number { display:grid; place-items:center; width:35px; height:35px; flex-shrink:0; border-radius:10px; background:#f2f4f7; color:#778494; font-size:12px; font-weight:750; }
    #logisticsFacilitiesWorkspace .fe-section-head h4 { margin:0; font-size:15px; font-weight:700; color:#27303a; }
    #logisticsFacilitiesWorkspace .fe-section-head p { margin:2px 0 0; color:#8c96a3; font-size:11px; }
    #logisticsFacilitiesWorkspace .fe-fields { display:grid; gap:20px; max-width:1050px; }
    #logisticsFacilitiesWorkspace .fe-location-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
    #logisticsFacilitiesWorkspace .fe-field { min-width:0; }
    #logisticsFacilitiesWorkspace .fe-field > label, #logisticsFacilitiesWorkspace .fe-map-header .fe-map-label { display:block; margin-bottom:7px; color:#414c59; font-size:12px; font-weight:650; }
    #logisticsFacilitiesWorkspace .fe-field label span, #logisticsFacilitiesWorkspace .fe-map-header .fe-map-label span { color:#ae7812; }
    #logisticsFacilitiesWorkspace .fe-field label em { font-size:11px; color:#919aa4; font-style:normal; font-weight:400; }
    #logisticsFacilitiesWorkspace .fe-control { display:block; box-sizing:border-box; width:100%; min-height:45px; padding:10px 13px; border:1px solid #dce3e9 !important; border-radius:11px !important; background:#fff !important; color:#2c3744 !important; font-family:inherit; font-size:12px !important; font-weight:500; line-height:1.5; outline:none; transition:border-color .13s ease,box-shadow .13s ease; }
    #logisticsFacilitiesWorkspace .fe-control::placeholder { color:#a6aeba; font-size:12px; font-weight:400; }
    #logisticsFacilitiesWorkspace .fe-control:focus { border-color:#c89330 !important; box-shadow:0 0 0 3px rgba(201,147,48,.10) !important; }
    #logisticsFacilitiesWorkspace .fe-control:disabled { background:#f6f8fa !important; color:#929aa5 !important; cursor:not-allowed; }
    #logisticsFacilitiesWorkspace .fe-control.fe-invalid { border-color:#d17070 !important; box-shadow:0 0 0 3px rgba(209,112,112,.09) !important; }
    #logisticsFacilitiesWorkspace .fe-textarea { min-height:82px; resize:vertical; }
    #logisticsFacilitiesWorkspace .fe-field-hint { margin:6px 0 0; color:#8b95a0; font-size:10.5px; line-height:1.5; }
    #logisticsFacilitiesWorkspace .fe-error { margin:5px 0 0; color:#bd4848; font-size:11px; font-weight:600; line-height:1.5; }
    #logisticsFacilitiesWorkspace .fe-address-status { margin:0; border:1px solid #e4eaf0; border-radius:10px; padding:9px 12px; color:#627080; background:#f8fafc; font-size:11px; line-height:1.5; }
    #logisticsFacilitiesWorkspace .fe-map-field { padding-top:6px; }
    #logisticsFacilitiesWorkspace .fe-map-header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:9px; }
    #logisticsFacilitiesWorkspace .fe-map-header .fe-map-label { margin:0; }
    #logisticsFacilitiesWorkspace .fe-map-header .fe-field-hint { margin:4px 0 0; }
    #logisticsFacilitiesWorkspace .fe-map-status { padding:6px 10px; background:#f3f5f7; color:#6b7682; border-radius:999px; font-size:9px; font-weight:750; white-space:nowrap; }
    #logisticsFacilitiesWorkspace .fe-map-status.is-set { color:#27754c; background:#edf8f2; }
    #logisticsFacilitiesWorkspace #sariFacilityMap { height:310px; width:100%; border:1px solid #e1e6eb; border-radius:13px; background:#f1f4f6; overflow:hidden; }
    #logisticsFacilitiesWorkspace .fe-coordinates { margin-top:12px; }
    #logisticsFacilitiesWorkspace .fe-active-row { display:flex; align-items:flex-start; gap:12px; border:1px solid #e5eaf0; border-radius:12px; padding:14px 16px; background:#fafbfc; cursor:pointer; }
    #logisticsFacilitiesWorkspace .fe-active-row input { width:17px; height:17px; margin:2px 0 0; flex:0 0 auto; accent-color:#c9911b; }
    #logisticsFacilitiesWorkspace .fe-active-row strong { display:block; color:#343d48; font-size:12px; font-weight:700; }
    #logisticsFacilitiesWorkspace .fe-active-row small { display:block; margin-top:3px; color:#8993a0; font-size:11px; line-height:1.55; }
    #logisticsFacilitiesWorkspace .fe-form-footer { display:flex; align-items:center; justify-content:space-between; gap:14px; padding-top:19px; margin-top:24px; border-top:1px solid #ecf0f4; }
    #logisticsFacilitiesWorkspace .fe-form-footer span { color:#929ba7; font-size:11px; }
    #logisticsFacilitiesWorkspace .fe-save { min-height:43px; padding:0 19px; border-radius:11px; border:1px solid #d9950b; background:#d9950b; color:white; font-size:12px; font-weight:700; box-shadow:0 6px 14px rgba(217,149,11,.12); cursor:pointer; }
    #logisticsFacilitiesWorkspace .fe-save:hover { background:#bb7f04; border-color:#bb7f04; }
    #logisticsFacilitiesWorkspace .hub-address-results { border-color:#e0e5eb; background:#fff; box-shadow:0 14px 30px rgba(20,33,52,.12); }
    #logisticsFacilitiesWorkspace .hub-address-results button { font-size:12px; color:#4a5664; }
    #logisticsFacilitiesWorkspace .hub-address-results button:hover, #logisticsFacilitiesWorkspace .hub-address-results button:focus { background:#f6f8fa; color:#97670f; }
    #facilityHelpDialog.fe-help-dialog { position:fixed; inset:0; width:min(560px,calc(100vw - 28px)); max-height:min(86vh,780px); margin:auto; padding:0; border:1px solid #e3e8ee; border-radius:20px; background:#fff; color:#283342; box-shadow:0 25px 85px rgba(15,23,42,.24); overflow-y:auto; }
    #facilityHelpDialog::backdrop { background:rgba(17,24,39,.54); }
    #facilityHelpDialog .fe-help-heading { display:flex; justify-content:space-between; gap:14px; align-items:flex-start; padding:22px 24px; border-bottom:1px solid #ebeff2; }
    #facilityHelpDialog .fe-kicker { margin:0 0 6px; color:#b57b0f; font-size:10px; font-weight:750; letter-spacing:.12em; }
    #facilityHelpDialog h3 { margin:0; font-size:22px; font-weight:750; color:#252e39; }
    #facilityHelpDialog .fe-help-heading p:last-child { margin:7px 0 0; color:#798594; font-size:11px; line-height:1.6; }
    #facilityHelpDialog .fe-help-close { display:grid; place-items:center; width:34px; height:34px; border:1px solid #e0e6ed; border-radius:9px; background:#fff; flex-shrink:0; color:#626d79; }
    #facilityHelpDialog .fe-help-close svg { width:18px; height:18px; }
    #facilityHelpDialog .fe-help-body { display:grid; gap:10px; padding:18px 24px 24px; }
    #facilityHelpDialog .fe-help-body section { padding:13px 15px; border:1px solid #e8edf2; border-radius:12px; background:#fafbfd; }
    #facilityHelpDialog .fe-help-body h4 { margin:0 0 7px; color:#a16f15; font-size:10px; font-weight:750; text-transform:uppercase; letter-spacing:.07em; }
    #facilityHelpDialog .fe-help-body strong { display:block; font-size:12px; font-weight:700; color:#3b4753; line-height:1.5; }
    #facilityHelpDialog .fe-help-body p, #facilityHelpDialog .fe-help-body li { color:#707d8c; font-size:11px; line-height:1.65; }
    #facilityHelpDialog .fe-help-body p { margin:5px 0 0; }
    #facilityHelpDialog .fe-help-body ul { list-style:disc; margin:0; padding-left:17px; }
    /* v12: Search -> preview pin -> confirm. No visible coordinate inputs. */
    /* Neutral location search — no gold border or nested focus rings. */
    #logisticsContent #logisticsFacilitiesWorkspace .fe-place-search {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 49px;
        box-sizing: border-box;
        border: 1px solid #dce3ea !important;
        border-radius: 13px;
        background: #fff !important;
        padding: 5px 6px 5px 16px;
        box-shadow: 0 1px 3px rgba(15,23,42,.025);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fe-place-search:focus-within {
        border-color: #aebbc9 !important;
        box-shadow: 0 0 0 3px rgba(93,112,132,.09) !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fe-place-search svg {
        width: 19px;
        height: 19px;
        color: #7f8c99;
        flex: none;
    }
    #logisticsContent #logisticsFacilitiesWorkspace #sariFacilityForm .fe-place-search input,
    #logisticsContent #logisticsFacilitiesWorkspace #sariFacilityForm .fe-place-search input:focus,
    #logisticsContent #logisticsFacilitiesWorkspace #sariFacilityForm .fe-place-search input:focus-visible {
        min-width: 0;
        flex: 1;
        width: 100%;
        height: 37px;
        padding: 0;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        font-family: inherit;
        font-size: 13px !important;
        color: #27323d !important;
        appearance: none;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fe-place-search button {
        flex: none;
        min-height: 37px;
        padding: 0 17px;
        border: 1px solid #e7ebf0;
        border-radius: 9px;
        background: #f4f6f8;
        color: #3f4e5d;
        font-size: 12px;
        font-weight: 650;
        transition: background .15s ease;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fe-place-search button:hover {
        background: #e9edf2;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fe-place-search button:focus-visible {
        outline: 2px solid #8d9bab;
        outline-offset: 2px;
    }
    #logisticsFacilitiesWorkspace .fe-place-results { display:grid; gap:5px; border:1px solid #e3e9ef; border-radius:12px; background:#fff; box-shadow:0 10px 25px rgba(15,23,42,.06); padding:7px; max-height:238px; overflow-y:auto; }
    #logisticsFacilitiesWorkspace .fe-place-results[hidden] { display:none !important; }
    #logisticsFacilitiesWorkspace .fe-place-result { display:flex; flex-direction:column; gap:3px; width:100%; min-width:0; text-align:left; padding:11px 12px; border:0; border-radius:9px; background:transparent; }
    #logisticsFacilitiesWorkspace .fe-place-result:hover, #logisticsFacilitiesWorkspace .fe-place-result:focus-visible { background:#f3f5f7; outline:none; }
    #logisticsFacilitiesWorkspace .fe-place-result strong { color:#283340; font-size:12px; font-weight:650; }
    #logisticsFacilitiesWorkspace .fe-place-result small { color:#7b8795; font-size:10px; line-height:1.5; }
    #logisticsFacilitiesWorkspace .fe-place-feedback { padding:9px 11px; font-size:11px; line-height:1.5; color:#667481; background:#f8fafc; border-radius:10px; }
    #logisticsFacilitiesWorkspace .fe-place-feedback.is-error { color:#9a4040; background:#fff4f4; }
    #logisticsFacilitiesWorkspace .fe-pin-preview { display:flex; align-items:center; gap:9px; padding:10px 12px; margin-top:10px; background:#f8fafc; border:1px solid #e4eaf1; border-radius:11px; color:#5c6876; font-size:11px; line-height:1.5; }
    #logisticsFacilitiesWorkspace .fe-pin-preview-icon svg { width:20px; height:20px; color:#c88912; flex:none; }
    #logisticsFacilitiesWorkspace .fe-confirm-location { margin-top:11px; width:100%; min-height:44px; border-radius:11px; background:#d9950b; color:#fff; font-size:12px; font-weight:700; }
    #logisticsFacilitiesWorkspace .fe-confirm-location:hover:not(:disabled) { background:#be8005; }
    #logisticsFacilitiesWorkspace .fe-confirm-location:disabled { opacity:.52; cursor:not-allowed; }
    #logisticsFacilitiesWorkspace .fe-confirmed-location { margin-top:10px; padding:12px 13px; border:1px solid #cce6d5; background:#f3faf5; border-radius:11px; color:#286647; font-size:11px; font-weight:600; line-height:1.55; }
    #logisticsFacilitiesWorkspace .fe-place-feedback[hidden], #logisticsFacilitiesWorkspace .fe-pin-preview[hidden], #logisticsFacilitiesWorkspace .fe-confirmed-location[hidden] { display:none !important; }
    @media (max-width:640px) {
        #logisticsFacilitiesWorkspace .fe-header { padding:18px; }
        #logisticsFacilitiesWorkspace .fe-form { padding:18px !important; }
        #logisticsFacilitiesWorkspace .fe-title { font-size:23px; }
        #logisticsFacilitiesWorkspace .fe-location-grid { grid-template-columns:1fr; gap:16px; }
        #logisticsFacilitiesWorkspace #sariFacilityMap { height:250px; }
        #logisticsFacilitiesWorkspace .fe-form-footer { align-items:stretch; flex-direction:column; }
        #logisticsFacilitiesWorkspace .fe-save { width:100%; }
        #facilityHelpDialog .fe-help-heading { padding:18px; }
        #facilityHelpDialog .fe-help-body { padding:14px 18px 20px; }
    }
    @media (prefers-reduced-motion:reduce) { #logisticsFacilitiesWorkspace .fe-control, #logisticsFacilitiesWorkspace .fe-help-trigger, #logisticsFacilitiesWorkspace .fe-close { transition:none !important; } }


    /* v11: Neutral directory hover. No gold inset stripe or warm cream. */
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row:hover,
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row:focus-within {
        background: #f2f4f6 !important;
        background-image: none !important;
        box-shadow: none !important;
        border-left-color: transparent !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row:hover td,
    #logisticsContent #logisticsFacilitiesWorkspace .fh-admin-directory .fh-hub-row:focus-within td {
        background: transparent !important;
        box-shadow: none !important;
    }

    /* Read-only saved map. A single modal scroll surface, with invisible scrollbar.
       Touch/keyboard scrolling remains available if the viewport is short. */
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog {
        overflow: hidden !important;
        max-height: calc(100dvh - 28px) !important;
        scrollbar-width: none;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-dialog-surface {
        max-height: calc(100dvh - 28px) !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        overscroll-behavior: contain;
        scrollbar-width: none !important;
        -ms-overflow-style: none;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog::-webkit-scrollbar,
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-dialog-surface::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-map-item {
        display: block !important;
        padding: 13px !important;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-map-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 0 2px 10px;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-map-hint {
        color: #8492a1;
        font-size: 10px;
        font-weight: 550;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-location-map {
        width: 100%;
        height: 184px;
        overflow: hidden;
        position: relative;
        border-radius: 10px;
        border: 1px solid #e4eaf0;
        background: #eef2f5;
    }
    #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-map-loading {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        color: #6c7784;
        font-size: 11px;
        line-height: 1.5;
        text-align: center;
    }
    /* Hide the scrollbar of the help dialog too, retaining short-screen scrolling. */
    #facilityHelpDialog.fe-help-dialog {
        scrollbar-width: none !important;
        -ms-overflow-style: none;
    }
    #facilityHelpDialog.fe-help-dialog::-webkit-scrollbar { display: none; width: 0; }
    @media (max-width: 540px) {
        #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-location-map {
            height: 164px;
        }
        #logisticsContent #logisticsFacilitiesWorkspace dialog.fh-hub-dialog .fh-hub-map-item {
            padding: 11px !important;
        }
    }

</style>
@endpush

@section('content')

<div
    id="logisticsFacilitiesWorkspace"
    class="sari-logistics-ui mx-auto w-full space-y-4"
>

    {{-- FLASH MESSAGES --}}

    @if(session('success'))
        <div
            class="rounded-xl border border-[#bfe0cd] bg-[#f1faf5] px-4 py-3 text-[10px] font-medium text-[#327a53]"
            role="status"
        >
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div
            id="facilityServerErrors"
            class="rounded-xl border border-[#efc6c6] bg-[#fff4f4] px-4 py-3 text-[10px] text-[#b53e3e]"
            role="alert"
        >
            <strong class="mb-2 block">
                Please review the following:
            </strong>

            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PAGE HEADING --}}

    <header class="fh-heading">

        <div class="min-w-0">

            <p class="fh-eyebrow">
                Logistics Network
            </p>

            <h2 class="fh-title">
                Municipal <span>Hubs</span>
            </h2>

        </div>

        <div class="fh-heading-actions">

            <div
                class="fh-network-state {{ $stats['routing_ready'] ? '' : 'is-warning' }}"
                role="status"
            >
                <small>Network Status</small>

                <strong>
                    {{
                        $stats['routing_ready']
                            ? '✓ Routing Ready'
                            : 'Inter-hub setup incomplete'
                    }}
                </strong>
            </div>

            <button
                id="hubEditorToggle"
                type="button"
                class="fh-primary"
                aria-controls="hubEditor"
                aria-expanded="{{ $editing || $errors->any() ? 'true' : 'false' }}"
            >
                {{ $editing ? 'Edit Hub' : '+ New Hub' }}
            </button>

        </div>

    </header>

    {{-- KPI CARDS --}}

    @php
        $facilityMetrics = [
            [
                'label' => 'Municipal Hubs',
                'value' => $stats['total'],
                'description' => 'Registered hub locations',
                'icon' => 'hub',
            ],
            [
                'label' => 'Active Hubs',
                'value' => $stats['active'],
                'description' => 'Eligible for new routes',
                'icon' => 'active',
            ],
            [
                'label' => 'Route History',
                'value' => $stats['used'],
                'description' => 'Hubs referenced by routes',
                'icon' => 'route',
            ],
            [
                'label' => 'Main Branch',
                'value' => $stats['main_branch_count'],
                'description' => 'Central treasury hub',
                'icon' => 'main',
            ],
        ];
    @endphp

    <section
        class="fh-kpis"
        aria-label="Municipal Hub overview"
    >

        @foreach($facilityMetrics as $metric)

            <article class="fh-kpi">

                <div class="min-w-0">

                    <p class="fh-kpi-label">
                        {{ $metric['label'] }}
                    </p>

                    <p class="fh-kpi-value">
                        {{ number_format($metric['value']) }}
                    </p>

                    <p class="fh-kpi-sub">
                        {{ $metric['description'] }}
                    </p>

                </div>

                <span class="fh-kpi-icon" aria-hidden="true">

                    @switch($metric['icon'])

                        @case('active')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 3 3 5-6"/>
                            </svg>
                            @break

                        @case('route')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="5" cy="6" r="2"/>
                                <circle cx="19" cy="18" r="2"/>
                                <path d="M7 6h7a4 4 0 0 1 0 8h-3a4 4 0 0 0 0 8h6"/>
                            </svg>
                            @break

                        @case('main')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 21h16M6 21V8l6-4 6 4v13"/>
                                <path d="M9 12h6M9 16h6"/>
                                <path d="m12 6 .7 1.4 1.6.2-1.1 1.1.3 1.6-1.5-.8-1.5.8.3-1.6-1.1-1.1 1.6-.2L12 6Z"/>
                            </svg>
                            @break

                        @default
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3M13 12h3M8 16h8"/>
                            </svg>

                    @endswitch

                </span>

            </article>

        @endforeach

    </section>

    @unless($stats['routing_ready'])

        <div
            class="rounded-xl border border-[#ead7ae] bg-[#fff9ec] px-4 py-3 text-[10px] leading-5 text-[#936719]"
            role="status"
        >
            Inter-hub routing requires at least two active municipal hubs.
            A single active hub can still serve valid local deliveries.
        </div>

    @endunless

    {{-- MUNICIPAL HUB DIRECTORY --}}

    {{-- Standalone Admin-style search and filters: above, not inside, the table card. --}}
    <section class="fh-directory-filter-strip" aria-label="Municipal hub search and filters">
    <div class="fh-admin-filters" aria-label="Search and filter municipal hubs">
        <label class="fh-admin-search" for="facilitySearchInput">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/></svg>
            <span class="sr-only">Search hub, city, or code</span>
            <input id="facilitySearchInput" type="search" placeholder="Search hub, city, or code..." autocomplete="off">
        </label>
        <label class="fh-admin-select-wrap">
            <span class="sr-only">Filter by hub status</span>
            <select id="facilityStatusFilter" aria-label="Filter by hub status">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
        </label>
        <label class="fh-admin-select-wrap">
            <span class="sr-only">Filter by branch role</span>
            <select id="facilityRoleFilter" aria-label="Filter by branch role">
                <option value="all">All Branch Roles</option>
                <option value="main">Main Branch</option>
                <option value="branch">Regular Branch</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
        </label>
        <button id="facilityApplyFilter" type="button" class="fh-admin-apply">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            Apply Filter
        </button>
        <button id="facilityResetFilter" type="button" class="fh-admin-reset">Reset</button>
    </div>
    </section>

    <section
        class="fh-panel fh-admin-directory"
        aria-labelledby="fhNetworkHeading"
    >

        <header class="fh-panel-head">

            <div>

                <h3
                    id="fhNetworkHeading"
                    class="fh-panel-title"
                >
                    Registered operational hubs
                </h3>

            </div>

            <span class="fh-counter" aria-label="Total registered hubs">
                {{ $facilities->count() }} HUBS
            </span>

        </header>

        <div id="facilityNetworkList" class="fh-hub-table-wrap">
            <table class="fh-hub-table {{ $facilities->count() === 0 ? 'is-empty' : '' }}" aria-label="Registered municipal hubs">
                <thead>
                    <tr>
                        <th class="w-[27%] text-left">Hub</th>
                        <th class="w-[19%] text-left">Location</th>
                        <th class="w-[10%] text-left">Status</th>
                        <th class="w-[12%] text-left">Branch Role</th>
                        <th class="w-[10%] text-left">Route Uses</th>
                        <th class="w-[15%] text-left">Code</th>
                        <th class="w-[7%] text-right">View</th>
                    </tr>
                </thead>

                @forelse($facilities as $facility)
                    @php
                        $facilitySearch = strtolower(implode(' ', [
                            $facility->name,
                            $facility->code,
                            $facility->address,
                            $facility->province_name,
                            $facility->municipality_name,
                            $facility->barangay_name,
                        ]));
                    @endphp

                    <tbody
                        data-facility-card
                        data-search="{{ $facilitySearch }}"
                        data-status="{{ $facility->is_active ? 'active' : 'inactive' }}"
                        data-role="{{ $facility->is_main_branch ? 'main' : 'branch' }}"
                    >
                        <tr class="fh-hub-row">
                            <td data-label="Hub">
                                <div class="fh-hub-identity">
                                    <span class="fh-hub-row-icon {{ $facility->is_active ? '' : 'is-inactive' }}" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3M13 12h3"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="fh-hub-row-name">{{ $facility->name }}</p>
                                        <p class="fh-hub-row-sub">{{ $facility->address }}</p>
                                    </div>
                                </div>
                            </td>

                            <td data-label="Location">
                                <p class="font-semibold text-[#4b5563]">{{ $facility->municipality_name ?: '—' }}</p>
                                <p class="mt-0.5 text-[8px] text-[#929aa4]">{{ $facility->province_name ?: 'Province not set' }}</p>
                            </td>

                            <td data-label="Status">
                                <span class="fh-route-badge {{ $facility->is_active ? 'is-active' : '' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                                    {{ $facility->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td data-label="Branch Role">
                                <span class="fh-role-badge {{ $facility->is_main_branch ? 'is-main' : '' }}">
                                    {{ $facility->is_main_branch ? 'Main Branch' : 'Branch' }}
                                </span>
                            </td>

                            <td data-label="Route Uses">
                                <p class="font-semibold text-[#4b5563]">{{ number_format((int) $facility->route_stops_count) }}</p>
                                <p class="mt-0.5 text-[8px] text-[#929aa4]">route uses</p>
                            </td>

                            <td data-label="Code">
                                <span class="font-mono text-[8px] font-semibold text-[#737d88]">{{ $facility->code }}</span>
                            </td>

                            <td data-label="View" class="text-right">
                                <button
                                    type="button"
                                    class="fh-view-hub"
                                    data-hub-toggle
                                    data-target="hubDetails-{{ $facility->id }}"
                                    aria-haspopup="dialog"
                                    aria-controls="hubDetails-{{ $facility->id }}"
                                    aria-label="View {{ $facility->name }} details"
                                    title="View hub details"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12Z"/>
                                        <circle cx="12" cy="12" r="2.5"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>


                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="7" class="fh-hub-table-empty">
                                <span class="fh-empty-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3M13 12h3M8 16h8"/></svg>
                                </span>
                                <p class="fh-empty-title">No municipal hubs yet</p>
                                <p class="fh-empty-copy">Create your first sorting hub using the + New Hub button above.</p>
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>

            {{-- Native dialog keeps hub details and the existing action forms out of the table. --}}
            @foreach($facilities as $facility)
                <dialog
                    id="hubDetails-{{ $facility->id }}"
                    class="fh-hub-dialog"
                    aria-labelledby="hubDialogTitle-{{ $facility->id }}"
                    aria-modal="true"
                >
                    <div class="fh-hub-dialog-surface">
                        <header class="fh-hub-dialog-header">
                            <div class="min-w-0">
                                <p class="fh-hub-dialog-kicker">Municipal Hub Details</p>
                                <h3 id="hubDialogTitle-{{ $facility->id }}" class="fh-hub-dialog-title">{{ $facility->name }}</h3>
                                <p class="fh-hub-dialog-subtitle">{{ $facility->code }}</p>
                            </div>
                            <button type="button" class="fh-hub-dialog-close" data-close-hub aria-label="Close hub details" title="Close">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                            </button>
                        </header>
                        <div class="fh-hub-detail-panel">

                                    <div class="fh-hub-detail-item">
                                        <small>Complete Address</small>
                                        <p>{{ $facility->address }}</p>
                                    </div>

                                    <div class="fh-hub-detail-item">
                                        <small>Coordinates</small>
                                        <strong>{{ number_format((float) $facility->latitude, 5) }}, {{ number_format((float) $facility->longitude, 5) }}</strong>
                                    </div>

                                    <div class="fh-hub-detail-item fh-hub-map-item">
                                        <div class="fh-hub-map-heading">
                                            <small>Saved Location</small>
                                            <span class="fh-hub-map-hint">Pinned facility</span>
                                        </div>
                                        <div
                                            class="fh-hub-location-map"
                                            data-hub-location-map
                                            data-lat="{{ $facility->latitude }}"
                                            data-lng="{{ $facility->longitude }}"
                                            aria-label="Map showing saved location of {{ $facility->name }}"
                                        >
                                            <p class="fh-hub-map-loading" role="status">Open map to view the saved facility pin.</p>
                                        </div>
                                    </div>

                                    <div class="fh-hub-detail-item">
                                        <small>Routing</small>
                                        <strong class="{{ $facility->is_active ? 'text-[#367b52]' : 'text-[#a65d5d]' }}">
                                            {{ $facility->is_active ? 'Active for new routes' : 'Inactive for new routes' }}
                                        </strong>
                                        <p>{{ $facility->route_stops_count }} historical route uses</p>
                                    </div>

                                    <div class="fh-hub-detail-item">
                                        <small>Branch Role</small>
                                        <strong>{{ $facility->is_main_branch ? 'Main Branch / Central Treasury' : 'Regular Operating Branch' }}</strong>
                                        <p>{{ $facility->is_main_branch ? 'Receives central branch treasury remittances.' : 'Selectable as an operating branch after Logistics login.' }}</p>
                                    </div>

                                    <div class="fh-hub-detail-actions">
                                        <a
                                            class="fh-action"
                                            href="{{ route('logistics.facilities.edit', $facility) }}#hubEditor"
                                        >
                                            Edit Hub
                                        </a>

                                        @if(!$facility->is_main_branch && $facility->is_active)
                                            <form
                                                method="POST"
                                                action="{{ route('logistics.facilities.main-branch', $facility) }}"
                                                onsubmit="return confirm('Set {{ addslashes($facility->name) }} as the Main Branch and central COD treasury?');"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="fh-action">Set Main Branch</button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('logistics.facilities.toggle', $facility) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="fh-action {{ $facility->is_active ? 'fh-deactivate-action' : 'is-warning' }}">
                                                {{ $facility->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        @if((int) $facility->route_stops_count === 0)
                                            <form
                                                method="POST"
                                                action="{{ route('logistics.facilities.destroy', $facility) }}"
                                                onsubmit="return confirm('Delete this unused municipal hub?');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="fh-action is-danger">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                    </div>
                </dialog>
            @endforeach

            <div id="facilitySearchEmpty" class="fh-hub-table-empty hidden">
                <span class="fh-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4.5 4.5M8 10.5h5"/></svg>
                </span>
                <p class="fh-empty-title">No matching hub</p>
                <p class="fh-empty-copy">Try a different search keyword.</p>
            </div>
        </div>

        <footer class="fh-admin-footer">
            <p id="facilityResultCount" aria-live="polite">Showing {{ $facilities->count() }} results</p>
            <label class="fh-page-size-label" for="facilityPageSize">Rows per page
                <select id="facilityPageSize" aria-label="Rows per page">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </label>
        </footer>

    </section>

    <nav class="fh-pagination-nav" aria-label="Municipal hub pages">
        <button type="button" id="facilityPreviousPage" class="fh-page-arrow" aria-label="Previous page" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <div id="facilityPageButtons" class="fh-page-buttons" role="group" aria-label="Page numbers">
            <button type="button" class="fh-page-number is-current" aria-current="page" aria-label="Page 1">1</button>
        </div>
        <button type="button" id="facilityNextPage" class="fh-page-arrow" aria-label="Next page" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </nav>

    {{-- Hub editor kept in its own Blade partial; collapsed until requested. --}}
    @include('logistics.partials.facility-editor')

</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | FORM REFERENCES
    |--------------------------------------------------------------------------
    */

    const facilityForm = document.getElementById('sariFacilityForm');
    const hubEditor = document.getElementById('hubEditor');
    const hubEditorToggle = document.getElementById('hubEditorToggle');
    const hubEditorClose = document.getElementById('hubEditorClose');
    let facilityMap = null;
    let addressDirectoryStarted = false;
    let addressDirectoryPromise = Promise.resolve();
    let mapScriptRequest = null;

    // MapLibre is loaded once, and only when the editor is used.
    function loadFacilityMapLibrary() {
        if (window.maplibregl) return Promise.resolve();
        if (mapScriptRequest) return mapScriptRequest;
        mapScriptRequest = new Promise(function (resolve, reject) {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.js';
            script.async = true;
            script.onload = resolve;
            script.onerror = function () { script.remove(); reject(new Error('Map library unavailable')); };
            document.head.appendChild(script);
        }).catch(function (error) { mapScriptRequest = null; throw error; });
        return mapScriptRequest;
    }

    const facilityHelpDialog = document.getElementById('facilityHelpDialog');
    const facilityHelpOpen = document.getElementById('facilityHelpOpen');
    const facilityHelpClose = document.getElementById('facilityHelpClose');
    facilityHelpOpen?.addEventListener('click', function () { if (!facilityHelpDialog?.open) facilityHelpDialog?.showModal(); });
    facilityHelpClose?.addEventListener('click', function () { facilityHelpDialog?.close(); facilityHelpOpen?.focus(); });
    facilityHelpDialog?.addEventListener('click', function (event) { if (event.target === facilityHelpDialog) facilityHelpDialog.close(); });

    if (!facilityForm) {
        return;
    }

    // The editor stays collapsed until requested. Preserve edit and error states.
    function setHubEditorVisible(visible, scrollToEditor = false) {
        if (!hubEditor) return;

        hubEditor.hidden = !visible;
        hubEditorToggle?.setAttribute('aria-expanded', String(visible));

        if (visible) {
            if (!addressDirectoryStarted) {
                addressDirectoryStarted = true;
                addressDirectoryPromise = initializeAddressDirectory();
            }

            loadFacilityMapLibrary().then(function () {
                if (hubEditor?.hidden) return;
                initializeFacilityMap();
                window.requestAnimationFrame(function () { facilityMap?.resize(); });
            }).catch(function () {
                const message = document.getElementById('facilityMapError');
                if (message) {
                    message.hidden = false;
                    message.textContent = 'Map unavailable. Please reopen the editor or try again with a working connection.';
                }
            });

            if (scrollToEditor) {
                hubEditor.scrollIntoView({
                    behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                    block: 'start'
                });
            }
        }
    }

    hubEditorToggle?.addEventListener('click', function () {
        setHubEditorVisible(hubEditor?.hidden ?? false, true);
    });

    hubEditorClose?.addEventListener('click', function () {
        setHubEditorVisible(false);
        hubEditorToggle?.focus();
    });

    /*
    |--------------------------------------------------------------------------
    | HUB SEARCH
    |--------------------------------------------------------------------------
    */

    const searchInput = document.getElementById('facilitySearchInput');
    const statusFilter = document.getElementById('facilityStatusFilter');
    const roleFilter = document.getElementById('facilityRoleFilter');
    const pageSizeControl = document.getElementById('facilityPageSize');
    const previousButton = document.getElementById('facilityPreviousPage');
    const nextButton = document.getElementById('facilityNextPage');
    const pageButtons = document.getElementById('facilityPageButtons');
    const resultCount = document.getElementById('facilityResultCount');

    const facilityCards = Array.from(document.querySelectorAll('[data-facility-card]'));
    const facilityIndex = facilityCards.map(function (card) {
        return {
            element: card,
            search: String(card.dataset.search || '').toLowerCase(),
            status: card.dataset.status || 'inactive',
            role: card.dataset.role || 'branch'
        };
    });
    const searchEmpty = document.getElementById('facilitySearchEmpty');
    const facilityTable = document.querySelector('.fh-hub-table');
    let currentHubPage = 1;
    let filterFrame = 0;

    // Render a maximum of five numbered pages (matching the Admin-style pagination).
    function renderHubPages(totalPages) {
        if (!pageButtons) return;
        const visibleCount = Math.min(5, totalPages);
        const start = Math.min(
            Math.max(1, currentHubPage - Math.floor(visibleCount / 2)),
            Math.max(1, totalPages - visibleCount + 1)
        );
        const fragment = document.createDocumentFragment();
        for (let page = start; page < start + visibleCount; page++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'fh-page-number' + (page === currentHubPage ? ' is-current' : '');
            button.textContent = String(page);
            button.setAttribute('aria-label', 'Page ' + page);
            if (page === currentHubPage) button.setAttribute('aria-current', 'page');
            button.addEventListener('click', function () {
                if (page === currentHubPage) return;
                currentHubPage = page;
                filterFacilities(false);
            });
            fragment.appendChild(button);
        }
        pageButtons.replaceChildren(fragment);
    }

    // Lightweight client-side filtering keeps the original hub table and modal data intact.
    function filterFacilities(resetPage = true) {
        if (resetPage) currentHubPage = 1;
        window.cancelAnimationFrame(filterFrame);
        filterFrame = window.requestAnimationFrame(function () {
            const keyword = String(searchInput?.value || '').trim().toLowerCase();
            const status = statusFilter?.value || 'all';
            const role = roleFilter?.value || 'all';
            const matching = facilityIndex.filter(function (item) {
                return (!keyword || item.search.includes(keyword))
                    && (status === 'all' || item.status === status)
                    && (role === 'all' || item.role === role);
            });

            const pageSize = Number(pageSizeControl?.value || 10);
            const totalPages = Math.max(1, Math.ceil(matching.length / pageSize));
            currentHubPage = Math.min(Math.max(1, currentHubPage), totalPages);
            const start = (currentHubPage - 1) * pageSize;
            const visible = new Set(matching.slice(start, start + pageSize).map(item => item.element));

            facilityCards.forEach(function (card) { card.hidden = !visible.has(card); });
            if (facilityCards.length) {
                searchEmpty?.classList.toggle('hidden', matching.length !== 0);
                facilityTable?.classList.toggle('is-filter-empty', matching.length === 0);
            }
            if (resultCount) {
                resultCount.textContent = matching.length
                    ? 'Showing ' + (start + 1) + '–' + Math.min(start + pageSize, matching.length) + ' of ' + matching.length + ' results'
                    : 'No matching hubs';
            }
            renderHubPages(totalPages);
            if (previousButton) previousButton.disabled = currentHubPage <= 1;
            if (nextButton) nextButton.disabled = currentHubPage >= totalPages;
        });
    }

    searchInput?.addEventListener('input', function () { filterFacilities(); }, { passive: true });
    statusFilter?.addEventListener('change', function () { filterFacilities(); });
    roleFilter?.addEventListener('change', function () { filterFacilities(); });
    pageSizeControl?.addEventListener('change', function () { filterFacilities(); });
    document.getElementById('facilityApplyFilter')?.addEventListener('click', function () { filterFacilities(); });
    document.getElementById('facilityResetFilter')?.addEventListener('click', function () {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = 'all';
        if (roleFilter) roleFilter.value = 'all';
        filterFacilities();
    });
    previousButton?.addEventListener('click', function () { currentHubPage--; filterFacilities(false); });
    nextButton?.addEventListener('click', function () { currentHubPage++; filterFacilities(false); });
    filterFacilities();


    /*
    |--------------------------------------------------------------------------
    | HUB ROW DETAIL TOGGLES
    |--------------------------------------------------------------------------
    */

    // Single trusted facility-icon factory for the draggable and saved-location pins.
    // No dynamic HTML or remote icon assets; the SVG is an inline warehouse glyph.
    function createFacilityPinElement(accessibleLabel) {
        const element = document.createElement('div');
        element.className = 'sari-facility-pin';
        element.setAttribute('role', 'img');
        element.setAttribute('aria-label', accessibleLabel);
        element.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">'
            + '<path d="M3 21V9l9-5 9 5v12H3Z"/>'
            + '<path d="M3 10h18M8 21v-7h8v7"/>'
            + '<path d="M8 13h.01M16 13h.01"/>'
            + '</svg>';
        return element;
    }

    // Read-only map in hub detail modal. Uses the already saved facility coordinates.
    // Lazily create one map at a time and release its WebGL context on close.
    let hubDetailMap = null;
    let hubDetailMapDialog = null;
    let hubMapRequest = 0;

    function destroyHubDetailMap() {
        ++hubMapRequest;
        if (hubDetailMap) {
            hubDetailMap.remove();
            hubDetailMap = null;
        }
        hubDetailMapDialog = null;
    }

    async function showHubSavedLocation(dialog) {
        const node = dialog?.querySelector('[data-hub-location-map]');
        if (!node) return;
        const lat = Number(node.dataset.lat);
        const lng = Number(node.dataset.lng);
        const valid = node.dataset.lat !== '' && node.dataset.lng !== ''
            && Number.isFinite(lat) && Number.isFinite(lng)
            && lat >= 4 && lat <= 22.5 && lng >= 116 && lng <= 127.5;
        if (!valid) {
            node.replaceChildren();
            const notice = document.createElement('p');
            notice.className = 'fh-hub-map-loading';
            notice.textContent = 'No valid location pin saved for this hub.';
            node.appendChild(notice);
            return;
        }

        destroyHubDetailMap();
        const request = ++hubMapRequest;
        try {
            await loadFacilityMapLibrary();
            if (!dialog.open || request !== hubMapRequest) return;
            node.replaceChildren();
            const map = new maplibregl.Map({
                container: node,
                style: {
                    version: 8,
                    sources: {
                        osm: {
                            type: 'raster',
                            tiles: ['https://tile.openstreetmap.org/{z}/{x}/{y}.png'],
                            tileSize: 256,
                            attribution: '© OpenStreetMap contributors'
                        }
                    },
                    layers: [{ id: 'osm', type: 'raster', source: 'osm' }]
                },
                center: [lng, lat],
                zoom: 14,
                interactive: false,
                attributionControl: true
            });
            hubDetailMap = map;
            hubDetailMapDialog = dialog;
            const pin = createFacilityPinElement('Saved sorting facility location');
            new maplibregl.Marker({ element: pin, anchor: 'bottom' })
                .setLngLat([lng, lat])
                .addTo(map);
            // Modal is displayed by now; give MapLibre its final dimensions.
            requestAnimationFrame(function () { if (hubDetailMap === map) map.resize(); });
        } catch (error) {
            if (!dialog.open || request !== hubMapRequest) return;
            node.replaceChildren();
            const notice = document.createElement('p');
            notice.className = 'fh-hub-map-loading';
            notice.textContent = 'Map tiles unavailable. Saved pin: '
                + lat.toFixed(5) + ', ' + lng.toFixed(5);
            node.appendChild(notice);
        }
    }

    document.querySelectorAll('[data-hub-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const dialog = document.getElementById(button.dataset.target);
            if (dialog && !dialog.open && typeof dialog.showModal === 'function') {
                dialog.showModal();
                showHubSavedLocation(dialog);
            }
        });
    });

    document.querySelectorAll('.fh-hub-dialog').forEach(function (dialog) {
        dialog.addEventListener('close', function () {
            if (hubDetailMapDialog === dialog || dialog.querySelector('[data-hub-location-map]')) {
                destroyHubDetailMap();
            }
        });
        dialog.querySelector('[data-close-hub]')?.addEventListener('click', function () {
            dialog.close();
        });
        // Clicking the backdrop closes the dialog; clicking content does not.
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) dialog.close();
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PHILIPPINE ADDRESS DIRECTORY
    |--------------------------------------------------------------------------
    */

    const addressKeys = [
        'province',
        'municipality',
        'barangay'
    ];

    const fields = Object.fromEntries(
        addressKeys.map(function (key) {

            return [
                key,
                {
                    search: document.getElementById('facility-' + key + '-search'),
                    results: document.getElementById('facility-' + key + '-results'),
                    name: document.getElementById('facility-' + key + '-name'),
                    code: document.getElementById('facility-' + key + '-code')
                }
            ];

        })
    );

    const addressStatus = document.getElementById('facilityAddressStatus');

    let provinces = [];
    let municipalities = [];
    let barangays = [];

    let manualMode = false;

    let cityRequestId = 0;
    let barangayRequestId = 0;

    const savedAddress = {
        province: fields.province.name.value.trim(),
        municipality: fields.municipality.name.value.trim(),
        barangay: fields.barangay.name.value.trim()
    };

    function normalizeItems(payload) {

        if (Array.isArray(payload)) {
            return payload;
        }

        if (Array.isArray(payload?.data)) {
            return payload.data;
        }

        if (Array.isArray(payload?.data?.data)) {
            return payload.data.data;
        }

        if (Array.isArray(payload?.results)) {
            return payload.results;
        }

        if (Array.isArray(payload?.items)) {
            return payload.items;
        }

        return [];
    }

    function locationCode(item) {

        return String(
            item.code ??
            item.psgc_code ??
            item.psgcCode ??
            item.id ??
            ''
        );
    }

    function locationName(item) {

        return String(
            item.name ??
            item.area_name ??
            item.areaName ??
            item.label ??
            ''
        );
    }

    async function fetchDirectory(url) {

        const response = await fetch(url, {

            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },

            cache: 'no-store'

        });

        if (!response.ok) {
            throw new Error('Address lookup failed: ' + response.status);
        }

        return normalizeItems(await response.json());
    }

    function setAddressStatus(message, error = false) {

        addressStatus.textContent = message;
        addressStatus.hidden = !error && message === 'Address directory ready.';

        addressStatus.style.color = error
            ? '#bd4848'
            : '#627080';
    }

    function enableManualAddress(message) {

        manualMode = true;

        setAddressStatus(
            message + ' Manual address entry has been enabled.',
            true
        );

        addressKeys.forEach(function (key) {

            const field = fields[key];

            field.search.disabled = false;

            field.search.placeholder = 'Enter ' + key;

            field.results.hidden = true;

            field.name.value = field.search.value.trim();

        });
    }

    function clearField(key) {

        const field = fields[key];

        field.search.value = '';
        field.name.value = '';
        field.code.value = '';

        field.results.hidden = true;
    }

    function resetBelow(key) {

        if (key === 'province') {

            cityRequestId++;
            municipalities = [];

            clearField('municipality');

            fields.municipality.search.disabled = true;

        }

        if (key === 'province' || key === 'municipality') {

            barangayRequestId++;
            barangays = [];

            clearField('barangay');

            fields.barangay.search.disabled = true;

        }
    }

    function showOptions(key, items) {

        const field = fields[key];

        const query = field.search.value.trim().toLowerCase();

        const matches = items.filter(function (item) {

            return locationName(item).toLowerCase().includes(query);

        }).slice(0, 40);

        field.results.innerHTML = '';

        if (!matches.length) {

            const empty = document.createElement('p');

            empty.className = 'px-3 py-3 text-[10px] text-[#918779]';

            empty.textContent = 'No matching location found.';

            field.results.appendChild(empty);

        } else {

            matches.forEach(function (item) {

                const button = document.createElement('button');

                button.type = 'button';

                button.textContent = locationName(item);

                button.addEventListener('mousedown', function (event) {

                    event.preventDefault();

                    selectLocation(key, item);

                });
                // Touch/assistive activation fallback without double-selecting on desktop.
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    if (field.code.value !== locationCode(item)) selectLocation(key, item);
                });

                field.results.appendChild(button);

            });

        }

        field.results.hidden = false;
    }

    function selectLocation(key, item, restore = '') {

        const field = fields[key];

        field.search.value = locationName(item);
        showFieldMessage(field.search, '', locationMessages[key]);

        field.name.value = locationName(item);

        field.code.value = locationCode(item);

        field.results.hidden = true;

        if (key === 'province') {

            resetBelow('province');

            loadMunicipalities(locationCode(item), restore);

        } else if (key === 'municipality') {

            resetBelow('municipality');

            loadBarangays(locationCode(item), restore);

        }
    }

    async function loadMunicipalities(provinceCode, restore = '') {

        const requestId = ++cityRequestId;

        setAddressStatus('Loading cities and municipalities...');

        try {

            const url =
                @json(url('/address/provinces')) +
                '/' +
                encodeURIComponent(provinceCode) +
                '/municipalities';

            const items = await fetchDirectory(url);

            if (requestId !== cityRequestId) {
                return;
            }

            municipalities = items;

            fields.municipality.search.disabled = false;

            fields.municipality.search.placeholder =
                'Search city / municipality...';

            setAddressStatus('Address directory ready.');

            if (restore) {

                const match = items.find(function (item) {

                    return locationName(item).toLowerCase() ===
                        restore.toLowerCase();

                });

                if (match) {

                    selectLocation(
                        'municipality',
                        match,
                        savedAddress.barangay
                    );

                } else {

                    fields.municipality.search.value = restore;

                    setAddressStatus(
                        'Previously saved municipality not found. Select a current result.',
                        true
                    );

                }

            }

        } catch (error) {

            if (requestId !== cityRequestId) {
                return;
            }

            console.error(error);

            enableManualAddress('Municipality directory unavailable.');

        }
    }

    async function loadBarangays(municipalityCode, restore = '') {

        const requestId = ++barangayRequestId;

        setAddressStatus('Loading barangays...');

        try {

            const url =
                @json(url('/address/municipalities')) +
                '/' +
                encodeURIComponent(municipalityCode) +
                '/barangays';

            const items = await fetchDirectory(url);

            if (requestId !== barangayRequestId) {
                return;
            }

            barangays = items;

            fields.barangay.search.disabled = false;

            fields.barangay.search.placeholder =
                'Search barangay (optional)...';

            setAddressStatus('Address directory ready.');

            if (restore) {

                const match = items.find(function (item) {

                    return locationName(item).toLowerCase() ===
                        restore.toLowerCase();

                });

                if (match) {

                    selectLocation('barangay', match);

                } else {

                    fields.barangay.search.value = restore;

                    setAddressStatus(
                        'Previously saved barangay not found. Select a current result.',
                        true
                    );

                }

            }

        } catch (error) {

            if (requestId !== barangayRequestId) {
                return;
            }

            console.error(error);

            enableManualAddress('Barangay directory unavailable.');

        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADDRESS SEARCH INTERACTIONS
    |--------------------------------------------------------------------------
    */

    addressKeys.forEach(function (key) {

        const field = fields[key];

        function currentItems() {

            if (key === 'province') {
                return provinces;
            }

            if (key === 'municipality') {
                return municipalities;
            }

            return barangays;
        }

        field.search.addEventListener('focus', function () {

            if (!manualMode && !field.search.disabled) {

                showOptions(key, currentItems());

            }
        });

        field.search.addEventListener('input', function () {

            if (manualMode) {

                field.name.value = field.search.value.trim();

                return;
            }

            if (field.search.value !== field.name.value) {

                field.name.value = '';

                field.code.value = '';

                if (key !== 'barangay') {
                    resetBelow(key);
                }

            }

            showOptions(key, currentItems());
        });

        field.search.addEventListener('keydown', function (event) {

            if (event.key === 'Escape') {

                field.results.hidden = true;

            }

            if (event.key === 'Enter' && !field.results.hidden) {

                const first = field.results.querySelector('button');

                if (first) {

                    event.preventDefault();

                    first.dispatchEvent(new MouseEvent('mousedown', {
                        bubbles: true
                    }));

                }

            }
        });

        document.addEventListener('click', function (event) {

            if (
                !field.search.contains(event.target) &&
                !field.results.contains(event.target)
            ) {
                field.results.hidden = true;
            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE ADDRESS DIRECTORY
    |--------------------------------------------------------------------------
    */

    async function initializeAddressDirectory() {

        try {

            provinces = await fetchDirectory(
                @json(route('address.provinces'))
            );

            if (!provinces.length) {
                throw new Error('No provinces returned.');
            }

            setAddressStatus('Address directory ready.');

            if (savedAddress.province) {

                const match = provinces.find(function (item) {

                    return locationName(item).toLowerCase() ===
                        savedAddress.province.toLowerCase();

                });

                if (match) {

                    selectLocation(
                        'province',
                        match,
                        savedAddress.municipality
                    );

                } else {

                    setAddressStatus(
                        'Previously saved province not found. Select a current result.',
                        true
                    );

                }

            }

        } catch (error) {

            console.error(error);

            enableManualAddress('Philippine address directory unavailable.');

        }
    }

    /*
    |--------------------------------------------------------------------------
    | MAPLIBRE MAP
    |--------------------------------------------------------------------------
    */

    const mapNode = document.getElementById('sariFacilityMap');

    const latitudeInput = document.getElementById(
        'sariFacilityLatitude'
    );

    const longitudeInput = document.getElementById(
        'sariFacilityLongitude'
    );

    const mapStatus = document.getElementById(
        'sariFacilityMapStatus'
    );

    // Draft pins do not write to form fields until the operator confirms the location.
    const placeSearch = document.getElementById('facilityPlaceSearch');
    const placeFind = document.getElementById('facilityPlaceSearchButton');
    const placeResults = document.getElementById('facilityPlaceResults');
    const placeFeedback = document.getElementById('facilityPlaceFeedback');
    const pinPreview = document.getElementById('facilityPinPreview');
    const pinPreviewText = document.getElementById('facilityPinPreviewText');
    const confirmPin = document.getElementById('facilityConfirmLocation');
    const confirmedLocation = document.getElementById('facilityLocationConfirmed');
    const mapMessage = document.getElementById('facilityMapError');
    const PH_BOUNDS = [[116,4],[127.5,22.5]];
    // Photon is a best-effort demo endpoint: keep calls user-triggered, limited and replaceable.
    const photonBase = window.SARI_FACILITY_GEOCODER_URL || 'https://photon.komoot.io';
    let lastPhotonRequest = 0;
    let photonQueue = Promise.resolve();
    let draftPin = null;
    let draftFeature = null;
    let marker = null;
    let locationConfirmed = Boolean(latitudeInput?.value && longitudeInput?.value);
    let locationLookupPending = false;
    let searchSequence = 0;
    let pinSequence = 0;
    const validPhilippinesPoint = (lat,lng) => Number.isFinite(lat) && Number.isFinite(lng) && lat>=4 && lat<=22.5 && lng>=116 && lng<=127.5;
    function locationText(message, error=false) {
        if (!placeFeedback) return;
        placeFeedback.textContent = message;
        placeFeedback.hidden = !message;
        placeFeedback.classList.toggle('is-error',error);
    }
    function statusForPin(confirmed) {
        if (mapStatus) {
            mapStatus.textContent = confirmed ? 'LOCATION SAVED' : 'CONFIRM PIN';
            mapStatus.classList.toggle('is-set',confirmed);
        }
    }
    // One search or reverse request at a time; never call on each keystroke or map movement.
    function photonRequest(path, parameters) {
        const url = new URL(path, photonBase);
        Object.entries(parameters).forEach(([key,value]) => url.searchParams.set(key,String(value)));
        const operation = photonQueue.catch(()=>{}).then(async function () {
            const delay = Math.max(0, 1100 - (Date.now() - lastPhotonRequest));
            if (delay) await new Promise(resolve => setTimeout(resolve,delay));
            lastPhotonRequest = Date.now();
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(),9000);
            try {
                const response = await fetch(url.toString(), { headers:{ Accept:'application/json' }, signal:controller.signal });
                if (!response.ok) throw new Error('Location lookup unavailable');
                return await response.json();
            } finally { clearTimeout(timer); }
        });
        photonQueue = operation.catch(()=>{});
        return operation;
    }
    function featureDetails(feature) {
        if (!feature || !Array.isArray(feature.geometry?.coordinates)) return null;
        const [lng,lat] = feature.geometry.coordinates.map(Number);
        if (!validPhilippinesPoint(lat,lng)) return null;
        const p = feature.properties || {};
        if (p.countrycode && String(p.countrycode).toUpperCase() !== 'PH') return null;
        const street = [p.housenumber, p.street].filter(Boolean).join(' ');
        const parts = [p.name, street, p.district || p.locality, p.city, p.county, p.state, p.country].filter(Boolean);
        const address = [...new Set(parts.map(x=>String(x).trim()))].join(', ');
        return {lat,lng,name:String(p.name||''),address,
                province:String(p.state||p.county||''),city:String(p.city||p.county||''),
                barangay:String(p.district||p.locality||'')};
    }
    function previewPin(lng,lat,feature=null) {
        if (!validPhilippinesPoint(Number(lat),Number(lng))) return;
        draftPin = {lat:Number(lat),lng:Number(lng)};
        draftFeature = feature;
        ++pinSequence;
        locationConfirmed = false;
        locationLookupPending = false;
        latitudeInput.value = '';
        longitudeInput.value = '';
        if (confirmedLocation) confirmedLocation.hidden = true;
        if (confirmPin) confirmPin.disabled = false;
        if (pinPreview) pinPreview.hidden = false;
        if (pinPreviewText) pinPreviewText.textContent = 'Pin selected. Adjust it if needed, then press Confirm Hub Location.';
        statusForPin(false);
        if (mapMessage) mapMessage.hidden = true;
        if (!facilityMap || !window.maplibregl) return;
        if (!marker) {
            const element = createFacilityPinElement('Drag to adjust facility location');
            marker = new maplibregl.Marker({element,anchor:'bottom',draggable:true}).setLngLat([lng,lat]).addTo(facilityMap);
            marker.on('dragend', function () {
                const point = marker.getLngLat();
                if (!validPhilippinesPoint(point.lat,point.lng)) {
                    marker.setLngLat([draftPin.lng,draftPin.lat]);
                    locationText('Choose a facility location within the Philippines.',true);
                    return;
                }
                previewPin(point.lng,point.lat);
            });
        } else marker.setLngLat([lng,lat]);
    }
    function initializeFacilityMap() {
        if (facilityMap || !mapNode || !latitudeInput || !longitudeInput || !window.maplibregl) return;
        const lat=Number(latitudeInput.value), lng=Number(longitudeInput.value);
        const hasInitial=Boolean(latitudeInput.value&&longitudeInput.value&&validPhilippinesPoint(lat,lng));
        const map = new maplibregl.Map({
            container:mapNode,
            style:{version:8,sources:{'osm-streets':{type:'raster',tiles:['https://tile.openstreetmap.org/{z}/{x}/{y}.png'],tileSize:256,minzoom:0,maxzoom:19,attribution:'© OpenStreetMap contributors'}},layers:[{id:'osm',type:'raster',source:'osm-streets'}]},
            center:hasInitial?[lng,lat]:[121,12.4],zoom:hasInitial?14:5.3,maxBounds:PH_BOUNDS,
            dragRotate:false,pitchWithRotate:false,attributionControl:true
        });
        facilityMap=map;
        map.addControl(new maplibregl.NavigationControl({showCompass:false}),'top-right');
        map.on('click', function (event) { previewPin(event.lngLat.lng,event.lngLat.lat); });
        if (hasInitial) {
            previewPin(lng,lat);
            latitudeInput.value=lat.toFixed(7);
            longitudeInput.value=lng.toFixed(7);
            locationConfirmed=true;
            if (confirmPin) confirmPin.disabled=true;
            if (pinPreview) pinPreview.hidden=true;
            statusForPin(true);
            if (confirmedLocation) { confirmedLocation.hidden=false; confirmedLocation.textContent='✓ Saved facility pin loaded. Click the map to change it.'; }
        }
        window.requestAnimationFrame(function(){map.resize();});
    }
    async function findPlace() {
        const query=String(placeSearch?.value||'').trim();
        if (query.length<3) { locationText('Enter at least 3 characters to search.',true); placeSearch?.focus();return; }
        const requestId=++searchSequence;
        if (placeResults) placeResults.hidden=true;
        locationText('Searching Philippine locations…');
        if (placeFind) placeFind.disabled=true;
        try {
            const response=await photonRequest('/api',{q:query,countrycode:'PH',limit:5,lang:'en'});
            if (requestId!==searchSequence) return;
            const matches=(Array.isArray(response.features)?response.features:[]).map(feature=>({feature,place:featureDetails(feature)})).filter(item=>item.place);
            placeResults.replaceChildren();
            if (!matches.length) { locationText('No location found. Try a nearby landmark, or tap the exact spot on the map.',true);return; }
            matches.forEach(({feature,place}) => {
                const button=document.createElement('button');
                button.type='button';button.className='fe-place-result';button.setAttribute('role','listitem');
                const name=document.createElement('strong');name.textContent=place.name||place.address||'Map location';
                const sub=document.createElement('small');sub.textContent=place.address||'Philippines';
                button.append(name,sub);
                button.addEventListener('click',function(){
                    previewPin(place.lng,place.lat,feature);
                    facilityMap?.jumpTo({center:[place.lng,place.lat],zoom:16});
                    placeResults.hidden=true;
                    locationText('Location found. Move the pin if necessary, then confirm.');
                    confirmPin?.focus();
                });
                placeResults.appendChild(button);
            });
            placeResults.hidden=false;
            locationText('Choose the matching place below.');
        } catch(error){
            if (requestId===searchSequence) locationText('Address search unavailable. You can still tap the map to select the location.',true);
        } finally { if (requestId===searchSequence && placeFind) placeFind.disabled=false; }
    }
    placeFind?.addEventListener('click',findPlace);
    placeSearch?.addEventListener('keydown',function(event){ if(event.key==='Enter'){event.preventDefault();findPlace();}});
    function normalizePlace(value) {
        return String(value||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/\b(city|municipality|province|of|region|the)\b/g,'').replace(/[^a-z0-9]+/g,' ').trim();
    }
    function findNamedItem(items, candidate) {
        const needle=normalizePlace(candidate);
        if (!needle) return null;
        return items.find(item=>normalizePlace(locationName(item))===needle) ||
            items.find(item=>normalizePlace(locationName(item)).includes(needle)&&needle.length>3) || null;
    }
    async function autofillDirectory(info, sequence) {
        await addressDirectoryPromise.catch(()=>{});
        if(sequence!==pinSequence) return;
        const provinceName=/national capital region|metro manila|\bncr\b/i.test(info.province) ? 'Metro Manila' : info.province;
        if (manualMode) {
            for(const [key,val] of Object.entries({province:provinceName,municipality:info.city,barangay:info.barangay})){
                if(val && fields[key]) { fields[key].search.value=val;fields[key].name.value=val;fields[key].search.disabled=false; }
            }
            return;
        }
        let province=findNamedItem(provinces,provinceName);
        if(!province && provinceName==='Metro Manila') province=provinces.find(i=>/ncr|national capital|metro manila/i.test(locationName(i)));
        if (!province) return;
        // Keep address directory codes authoritative instead of writing raw geocoder text.
        fields.province.search.value=locationName(province);
        fields.province.name.value=locationName(province);
        fields.province.code.value=locationCode(province);
        await loadMunicipalities(locationCode(province));
        if(sequence!==pinSequence) return;
        if (manualMode) {
            for(const [key,val] of Object.entries({municipality:info.city,barangay:info.barangay})) {
                if(val && fields[key]) { fields[key].search.value=val; fields[key].name.value=val; fields[key].search.disabled=false; }
            }
            return;
        }
        const city=findNamedItem(municipalities,info.city);
        if (!city) return;
        fields.municipality.search.value=locationName(city);
        fields.municipality.name.value=locationName(city);
        fields.municipality.code.value=locationCode(city);
        await loadBarangays(locationCode(city));
        if(sequence!==pinSequence) return;
        if (manualMode) {
            if(info.barangay) { fields.barangay.search.value=info.barangay;fields.barangay.name.value=info.barangay;fields.barangay.search.disabled=false; }
            return;
        }
        const barangay=findNamedItem(barangays,info.barangay);
        if (barangay) {
            fields.barangay.search.value=locationName(barangay);
            fields.barangay.name.value=locationName(barangay);
            fields.barangay.code.value=locationCode(barangay);
        }
    }
    confirmPin?.addEventListener('click',async function(){
        if (!draftPin||!validPhilippinesPoint(draftPin.lat,draftPin.lng)) return;
        const id=pinSequence, pin={...draftPin};
        confirmPin.disabled=true;
        // Store the operator-confirmed coordinates immediately; geocoding only enriches the address.
        latitudeInput.value=pin.lat.toFixed(7);
        longitudeInput.value=pin.lng.toFixed(7);
        locationConfirmed=true;
        locationLookupPending=true;
        statusForPin(true);
        if(pinPreview)pinPreview.hidden=true;
        if(mapMessage)mapMessage.hidden=true;
        if(confirmedLocation){confirmedLocation.hidden=false;confirmedLocation.textContent='✓ Location confirmed. Looking up address details…';}
        locationText('Filling address details…');
        try {
            // Use searched details when possible; reverse lookup only for a manually positioned pin.
            let details=featureDetails(draftFeature);
            if (!details) {
                const found=await photonRequest('/reverse',{lat:pin.lat,lon:pin.lng,limit:1,lang:'en'});
                details=featureDetails(found.features?.[0]);
            }
            if (id!==pinSequence) return;
            latitudeInput.value=pin.lat.toFixed(7);
            longitudeInput.value=pin.lng.toFixed(7);
            locationConfirmed=true;
            statusForPin(true);
            if (pinPreview) pinPreview.hidden=true;
            if (mapMessage) mapMessage.hidden=true;
            if (details) {
                const addressInput=document.getElementById('facilityAddress');
                // A searched/reversed address is a suggestion, not guaranteed to be the exact entrance.
                if (addressInput && details.address) {
                    addressInput.value=details.address;
                    addressInput.dispatchEvent(new Event('input',{bubbles:true}));
                }
                await autofillDirectory(details,id);
                if(id!==pinSequence)return;
            }
            const pending=fields.province && !fields.province.name.value || fields.municipality && !fields.municipality.name.value;
            if(confirmedLocation){
                confirmedLocation.hidden=false;
                confirmedLocation.textContent=pending
                    ? '✓ Pin confirmed. Please select the province/city below if they were not found automatically.'
                    : '✓ Location confirmed. Address details have been filled where available—please review them before saving.';
            }
            locationText('');
        } catch(error){
            if(id!==pinSequence)return;
            // Geocoder offline: retain the user's verified pin and existing typed details.
            latitudeInput.value=pin.lat.toFixed(7);
            longitudeInput.value=pin.lng.toFixed(7);
            locationConfirmed=true;
            statusForPin(true);
            if(pinPreview)pinPreview.hidden=true;
            if(confirmedLocation){confirmedLocation.hidden=false;confirmedLocation.textContent='✓ Pin confirmed. Address lookup unavailable; please enter or review the address and province/city.';}
            locationText('Address lookup unavailable. Your confirmed map pin is saved.',false);
        } finally { if (id===pinSequence) locationLookupPending=false; }
    });

    // Field guidance and feedback: names use letters; addresses/hub names may contain numbers.
    const nameInput = document.getElementById('facilityName');
    const addressInput = document.getElementById('facilityAddress');
    const locationMessages = {
        province: document.getElementById('facilityProvinceError'),
        municipality: document.getElementById('facilityMunicipalityError'),
        barangay: document.getElementById('facilityBarangayError')
    };
    const locationNameAllowed = /^[\p{L}\p{M} .,'’()\-/]+$/u;
    function showFieldMessage(field, message, messageElement) {
        if (!field) return;
        field.setCustomValidity(message || '');
        field.classList.toggle('fe-invalid', Boolean(message));
        if (messageElement) { messageElement.textContent = message || ''; messageElement.hidden = !message; }
    }
    function validateHubName() {
        const value = nameInput.value.trim();
        let message = '';
        if (!value) message = 'Enter the sorting hub name.';
        else if (!/[\p{L}]/u.test(value)) message = 'Hub name must contain letters.';
        else if (!/^[\p{L}\p{M}\p{N}\s.,'’()#&\-/]+$/u.test(value)) message = 'Use letters, numbers and standard punctuation only.';
        showFieldMessage(nameInput, message, document.getElementById('facilityNameError'));
        return !message;
    }
    function validateHubAddress() {
        const value = addressInput.value.trim();
        const message = !value ? 'Enter the complete physical address.' : !/[\p{L}]/u.test(value) ? 'Include a street, building or landmark name.' : '';
        showFieldMessage(addressInput, message, document.getElementById('facilityAddressError'));
        return !message;
    }
    nameInput?.addEventListener('blur', validateHubName);
    addressInput?.addEventListener('blur', validateHubAddress);
    nameInput?.addEventListener('input', function () { if (nameInput.classList.contains('fe-invalid')) validateHubName(); });
    addressInput?.addEventListener('input', function () { if (addressInput.classList.contains('fe-invalid')) validateHubAddress(); });

    addressKeys.forEach(function (key) {
        const field = fields[key];
        const input = field.search;
        // Prevent numeric characters in geographic name searches; directory selections remain authoritative.
        input?.addEventListener('beforeinput', function (event) {
            if (event.data && /[0-9]/.test(event.data) && event.cancelable) event.preventDefault();
        });
        input?.addEventListener('input', function () {
            if (/[0-9]/.test(input.value)) {
                const position = input.selectionStart;
                const removedBeforeCursor = (input.value.slice(0, position ?? 0).match(/[0-9]/g) || []).length;
                input.value = input.value.replace(/[0-9]/g, '');
                if (position != null) input.setSelectionRange(Math.max(0, position - removedBeforeCursor), Math.max(0, position - removedBeforeCursor));
                if (manualMode) field.name.value = input.value.trim();
            }
            const msg = input.value.trim() && manualMode && !locationNameAllowed.test(input.value.trim())
                ? 'Use letters and standard place-name punctuation only.' : '';
            showFieldMessage(input, msg, locationMessages[key]);
        });
        input?.addEventListener('blur', function () {
            if (!manualMode || !input.value.trim()) return;
            showFieldMessage(input, locationNameAllowed.test(input.value.trim()) ? '' : 'Enter a valid place name (letters only).', locationMessages[key]);
        });
    });

    function validManualCoordinates() {
        return locationConfirmed && Boolean(latitudeInput.value && longitudeInput.value) &&
            validPhilippinesPoint(Number(latitudeInput.value),Number(longitudeInput.value));
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL FORM VALIDATION
    |--------------------------------------------------------------------------
    */

    facilityForm.addEventListener('submit', function (event) {
        if (locationLookupPending) { event.preventDefault(); locationText('Finish checking the address before creating the hub.'); return; }
        if (!validateHubName()) { event.preventDefault(); nameInput.focus(); return; }
        if (!validateHubAddress()) { event.preventDefault(); addressInput.focus(); return; }
        for (const key of addressKeys) {
            const input = fields[key].search;
            if (manualMode && input.value.trim() && !locationNameAllowed.test(input.value.trim())) {
                event.preventDefault();
                showFieldMessage(input, 'Use letters and standard place-name punctuation only.', locationMessages[key]);
                input.focus(); return;
            }
        }
        if (!validManualCoordinates()) {
            event.preventDefault();
            const message = document.getElementById('facilityMapError');
            if (message) { message.hidden = false; message.textContent = 'Search or tap the map, then press Confirm Hub Location.'; }
            placeSearch?.focus();
            return;
        }

        /*
         * Validate address selection.
         */

        if (manualMode) {

            addressKeys.forEach(function (key) {

                fields[key].name.value =
                    fields[key].search.value.trim();

            });

        }

        for (const key of addressKeys) {

            const field = fields[key];

            const required =
                key !== 'barangay' ||
                field.search.value.trim().length > 0;

            if (
                required &&
                (
                    !field.name.value.trim() ||
                    (!manualMode && !field.code.value)
                )
            ) {

                event.preventDefault();

                setAddressStatus(
                    'Please select a valid ' +
                    key +
                    ' from the address suggestions.',
                    true
                );

                field.search.focus();

                return;
            }
        }

    });

    // Native HTML validation may stop form submission before the submit handler runs.
    // Give the user a visible, field-specific explanation in that case too.
    facilityForm.addEventListener('invalid', function (event) {
        const el = event.target;
        const id = el?.id || '';
        if (id === 'facilityName') showFieldMessage(el, el.validity.valueMissing ? 'Enter the sorting hub name.' : 'Enter a valid hub name.', document.getElementById('facilityNameError'));
        else if (id === 'facilityAddress') showFieldMessage(el, el.validity.valueMissing ? 'Enter the complete physical address.' : 'Enter a valid address.', document.getElementById('facilityAddressError'));
        else if (id.startsWith('facility-') && id.endsWith('-search')) {
            const key = id.slice(9, -7);
            showFieldMessage(el, 'Select a valid ' + key + '.', locationMessages[key]);
        } else if (id === 'sariFacilityLatitude' || id === 'sariFacilityLongitude') {
            const message = document.getElementById('facilityMapError');
            if (message) { message.hidden = false; message.textContent = 'Select and confirm the hub location on the map.'; }
        }
    }, true);

    // Edit links and validation redirects must remain usable without a second click.
    if (hubEditor && !hubEditor.hidden) {
        setHubEditorVisible(true);
    }

});
</script>

@endpush
