
@extends('layouts.logistics')

@section('title', 'Parcel Sorting — SARI Logistics')
@section('page-title', 'Parcel Sorting')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/logistics/parcel-sorting.css') }}?v=20261015-1">

<style>
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — PARCEL SORTING
| Seller-style visual refinement
|--------------------------------------------------------------------------
| Scoped to this Blade only.
| The original parcel-sorting.css remains unchanged.
*/

#lgParcelSorting {
    --sorting-gold: #d9950b;
    --sorting-line: #e8e2d9;
    --sorting-ink: #25211c;

    width: 100%;
    max-width: 1620px;
    font-size: 10px;
}

#lgParcelSorting [hidden] {
    display: none !important;
}

/* =========================================================
   PAGE HEADING
========================================================= */

#lgParcelSorting .sorting-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;

    min-height: 0;
    margin-bottom: 14px;
    padding: 5px 2px 17px;

    overflow: visible;

    border: 0;
    border-bottom: 1px solid #e4e1dc;
    border-radius: 0;

    background: transparent;
    box-shadow: none;
}

#lgParcelSorting .sorting-page-header::after {
    display: none;
}

#lgParcelSorting .sorting-eyebrow {
    margin-bottom: 5px;
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    line-height: 1.4;
    letter-spacing: .14em;
    text-transform: uppercase;
}

#lgParcelSorting .sorting-page-header h1 {
    color: #24201b;
    font-size: clamp(23px, 2.15vw, 30px);
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.045em;
}

#lgParcelSorting .sorting-page-header h1 span {
    color: #d9950b;
}

#lgParcelSorting .sorting-subtitle {
    max-width: 720px;
    margin-top: 6px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

/* =========================================================
   HEADER ACTIONS
========================================================= */

#lgParcelSorting .sorting-header-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

#lgParcelSorting .sorting-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;

    min-height: 39px;
    padding: 9px 14px;

    border: 1px solid #e4d8c6;
    border-radius: 10px;

    background: #fff;
    color: #776a5a;

    font-size: 10px;
    font-weight: 650;
    line-height: 1.4;

    box-shadow: none;

    transition:
        transform .16s ease,
        border-color .16s ease,
        background .16s ease,
        color .16s ease;
}

#lgParcelSorting .sorting-page-header
.sorting-button:not(.sorting-button-primary) {
    border: 1px solid #e4d8c6;
    background: #fff;
    color: #776a5a;
}

#lgParcelSorting .sorting-page-header
.sorting-button:not(.sorting-button-primary):hover {
    border-color: #d5bb87;
    background: #fff9ed;
    color: #a36c0a;
}

#lgParcelSorting .sorting-button-primary {
    border-color: #d9950b;
    background: #d9950b;
    color: #fff;
}

#lgParcelSorting .sorting-button-primary:hover {
    border-color: #c88708;
    background: #c88708;
    color: #fff;
}

/* =========================================================
   ALERTS
========================================================= */

#lgParcelSorting .sorting-alert {
    margin-bottom: 14px;
    border-radius: 12px;
}

#lgParcelSorting .sorting-alert strong {
    font-size: 10px;
    font-weight: 700;
}

#lgParcelSorting .sorting-alert p,
#lgParcelSorting .sorting-alert ul {
    font-size: 9px;
    line-height: 1.6;
}

/* =========================================================
   KPI CARDS
========================================================= */

#lgParcelSorting .sorting-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

#lgParcelSorting .sorting-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;

    min-width: 0;
    min-height: 98px;
    padding: 14px;

    border: 1px solid #e8e2d9;
    border-radius: 16px;
    background: #fff;

    box-shadow: 0 6px 18px rgba(33, 24, 14, .035);

    transition:
        transform .18s cubic-bezier(.22, 1, .36, 1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgParcelSorting .sorting-stat::after {
    display: none;
}

#lgParcelSorting .sorting-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgParcelSorting .sorting-stat strong,
#lgParcelSorting .sorting-stat-amber strong,
#lgParcelSorting .sorting-stat-green strong,
#lgParcelSorting .sorting-stat-blue strong {
    display: block;
    margin: 8px 0 5px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.1;
    letter-spacing: -.045em;
}

#lgParcelSorting .sorting-stat span:not(.sorting-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
}

#lgParcelSorting .sorting-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;

    width: 41px;
    height: 41px;

    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgParcelSorting .sorting-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgParcelSorting .sorting-stat-amber .sorting-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgParcelSorting .sorting-stat-green .sorting-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

#lgParcelSorting .sorting-stat-blue .sorting-stat-icon {
    background: #eff5fa;
    color: #587ead;
}

/* =========================================================
   WORKFLOW STEPPER
========================================================= */

#lgParcelSorting .sorting-flow {
    gap: 4px;
    margin: 14px 0;
    padding: 4px;

    border: 1px solid #e8e2d9;
    border-radius: 10px;

    background: #fff;
}

#lgParcelSorting .sorting-flow :is(a, span) {
    min-height: 32px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgParcelSorting .sorting-flow b {
    display: grid;
    place-items: center;
    flex: 0 0 21px;

    width: 21px;
    height: 21px;
    font-size: 9px;
}

/* =========================================================
   WORKSPACE
========================================================= */

#lgParcelSorting .sorting-workspace {
    min-width: 0;
    overflow: hidden;

    border: 1px solid #e8e2d9;
    border-radius: 17px;

    background: #fff;
    box-shadow: 0 6px 18px rgba(33, 24, 14, .035);
}

#lgParcelSorting .sorting-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding: 15px 17px;
}

#lgParcelSorting .sorting-toolbar h2 {
    margin: 4px 0 0;
    color: #29231d;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
}

#lgParcelSorting .sorting-toolbar p:not(.sorting-eyebrow) {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.55;
}

/* =========================================================
   SEARCH
========================================================= */

#lgParcelSorting .sorting-search {
    flex-shrink: 0;
    width: min(315px, 100%);
}

#lgParcelSorting .sorting-search input {
    width: 100%;
    height: 39px;
    padding: 0 12px;

    border: 1px solid #dfe2e6;
    border-radius: 10px;

    background: #fff;
    color: #50483e;
    font-size: 10px;

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}

#lgParcelSorting .sorting-search input:focus,
#lgParcelSorting .sorting-form input:focus,
#lgParcelSorting .sorting-form textarea:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217, 149, 11, .09);
}

/* =========================================================
   FILTERS
========================================================= */

#lgParcelSorting .sorting-filter-row {
    gap: 10px;
    padding: 0 17px 13px;
}

#lgParcelSorting .sorting-tabs {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    padding: 4px;

    border: 1px solid #e6e1d9;
    border-radius: 10px;

    background: #f7f8fa;
}

#lgParcelSorting .sorting-tabs button {
    min-height: 30px;
    padding: 5px 10px;

    border: 1px solid transparent;
    border-radius: 7px;
    background: transparent;

    color: #756d63;
    font-size: 9px;
    font-weight: 550;
}

#lgParcelSorting .sorting-tabs button:hover {
    background: #fff9ee;
    color: #a36c0a;
}

#lgParcelSorting .sorting-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: #fff;
    color: #a36c0a;
    font-weight: 700;
    box-shadow: 0 2px 5px rgba(35, 29, 22, .035);
}

#lgParcelSorting .sorting-tabs span {
    min-width: 21px;
    height: 19px;
    border-radius: 6px;
    font-size: 8px;
}

#lgParcelSorting .sorting-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
    line-height: 1.5;
}

/* =========================================================
   TABLE
========================================================= */

#lgParcelSorting .sorting-table-scroll {
    width: 100%;
    overflow-x: auto;
}

#lgParcelSorting .sorting-table {
    width: 100%;
    min-width: 1000px;
    table-layout: fixed;
    border-collapse: collapse;
    text-align: left;
    font-size: 10px;
}

#lgParcelSorting .sorting-table th:nth-child(1) {
    width: 20%;
}

#lgParcelSorting .sorting-table th:nth-child(2) {
    width: 17%;
}

#lgParcelSorting .sorting-table th:nth-child(3) {
    width: 17%;
}

#lgParcelSorting .sorting-table th:nth-child(4) {
    width: 12%;
}

#lgParcelSorting .sorting-table th:nth-child(5) {
    width: 14%;
}

#lgParcelSorting .sorting-table th:nth-child(6) {
    width: 20%;
}

#lgParcelSorting .sorting-table thead {
    background: #f7f8fa;
}

#lgParcelSorting .sorting-table th {
    padding: 12px 11px;
    color: #81776d;
    font-size: 8.5px;
    font-weight: 650;
    letter-spacing: .05em;
    text-transform: uppercase;
    white-space: nowrap;
}

#lgParcelSorting .sorting-table td {
    min-width: 0;
    padding: 13px 11px;

    border-bottom: 1px solid #f0ebe5;

    color: #61584e;
    font-size: 10px;
    line-height: 1.55;
    vertical-align: top;

    overflow-wrap: anywhere;
}

#lgParcelSorting .sorting-table td strong {
    display: block;
    color: #443c33;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgParcelSorting .sorting-table td p {
    margin-top: 4px;
    color: #91887e;
    font-size: 9px;
    line-height: 1.55;
    overflow-wrap: anywhere;
}

#lgParcelSorting .sorting-table tbody tr {
    transition: background-color .16s ease;
}

#lgParcelSorting .sorting-table tbody tr:hover {
    background: #fffaf2;
}

/* =========================================================
   HUB DISPLAY
========================================================= */

#lgParcelSorting .sorting-hub {
    display: flex;
    align-items: flex-start;
    min-width: 0;
    gap: 8px;
}

#lgParcelSorting .sorting-hub > div {
    min-width: 0;
}

#lgParcelSorting .sorting-hub-icon {
    display: grid;
    place-items: center;
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    border-radius: 9px;
    font-size: 9px;
    font-weight: 700;
}

#lgParcelSorting .sorting-hub-origin {
    background: #fff5df;
    color: #ad781b;
}

#lgParcelSorting .sorting-hub-destination {
    background: #f1f6fb;
    color: #587b99;
}

/* =========================================================
   ZONE DISPLAY
========================================================= */

#lgParcelSorting .sorting-zone-display {
    min-width: 0;
}

#lgParcelSorting .sorting-zone-code {
    display: inline-flex;
    max-width: 100%;
    min-height: 27px;
    padding: 6px 9px;

    border: 1px solid #d9e5dc;
    border-radius: 8px;

    background: #f2f8f4;
    color: #4f795a;

    font-size: 9px;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgParcelSorting .sorting-zone-unassigned {
    padding: 6px 9px;
    border: 1px solid #e8e2db;
    border-radius: 8px;
    background: #f6f5f3;
    color: #8b8175;
    font-size: 9px;
}

/* =========================================================
   STATUS BADGES
========================================================= */

#lgParcelSorting .sorting-badge {
    min-height: 24px;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 8.5px;
    font-weight: 650;
}

/* =========================================================
   UNIFIED ACTION AREA
========================================================= */

#lgParcelSorting .ps-action-panel {
    width: 100%;
    min-width: 0;
    margin-top: 9px;
    padding: 10px;

    border: 1px solid #e9e5df;
    border-radius: 10px;

    background: #fbfbfc;
}

/* SORTING FORM */

#lgParcelSorting .sorting-form {
    width: 100%;
    max-width: none;
    margin: 0;
}

#lgParcelSorting .sorting-form label {
    display: block;
}

#lgParcelSorting .sorting-form label + label {
    margin-top: 9px;
}

#lgParcelSorting .sorting-label {
    display: block;
    margin-bottom: 5px;
    color: #958879;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
}

#lgParcelSorting .sorting-form input,
#lgParcelSorting .sorting-form textarea {
    display: block;
    width: 100%;
    padding: 9px 10px;

    border: 1px solid #dfe2e6;
    border-radius: 9px;

    background: #fff;
    color: #50483e;
    font-size: 9px;
    line-height: 1.5;
}

#lgParcelSorting .sorting-form input {
    height: 37px;
}

#lgParcelSorting .sorting-form textarea {
    min-height: 65px;
    resize: vertical;
}

#lgParcelSorting .sorting-form .sorting-button {
    width: 100%;
    min-height: 37px;
    margin-top: 10px;
    padding: 8px;
    border-radius: 9px;
    font-size: 9px;
    font-weight: 700;
    white-space: normal;
    line-height: 1.5;
}

/* SORTED INFORMATION */

#lgParcelSorting .sorting-completed {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
}

#lgParcelSorting .sorting-completed strong {
    color: #51483e !important;
    font-size: 9px !important;
    font-weight: 700 !important;
}

#lgParcelSorting .sorting-completed p {
    color: #897f74 !important;
    font-size: 9px !important;
    line-height: 1.6;
}

#lgParcelSorting .sorting-completed .sorting-note {
    border-top-color: #e8e2d9;
    color: #82776a !important;
}

#lgParcelSorting .ps-action-panel > .sorting-text-link {
    display: inline-flex;
    margin-top: 10px;
}

/* =========================================================
   EXPANDABLE PARCEL DETAILS
========================================================= */

#lgParcelSorting .sorting-details {
    max-width: 100%;
    margin-top: 9px;
}

#lgParcelSorting .sorting-details summary {
    max-width: 100%;
    gap: 7px;
    color: #a47118;
    font-size: 9px;
    font-weight: 650;
}

#lgParcelSorting .sorting-detail-body {
    width: 100%;
    min-width: 0;
    margin-top: 8px;
    padding: 10px;

    border: 1px solid #e8e2d9;
    border-radius: 9px;

    background: #fff;
}

#lgParcelSorting .sorting-detail-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
}

#lgParcelSorting .sorting-detail-grid strong {
    font-size: 9px !important;
}

#lgParcelSorting .sorting-detail-grid p {
    font-size: 9px !important;
}

#lgParcelSorting .sorting-existing-note {
    border-top-color: #e8e2d9;
}

#lgParcelSorting .sorting-existing-note p {
    font-size: 9px !important;
    line-height: 1.6;
}

#lgParcelSorting .sorting-products {
    border-top-color: #e8e2d9;
}

#lgParcelSorting .sorting-products li {
    gap: 8px;
    color: #6f665d;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

/* =========================================================
   EMPTY STATES
========================================================= */

#lgParcelSorting .sorting-empty {
    padding: 40px 20px;
}

#lgParcelSorting .sorting-empty-icon {
    width: 43px;
    height: 43px;
    border-radius: 12px;
    background: #fff7e8;
}

#lgParcelSorting .sorting-empty h3 {
    font-size: 11px;
    font-weight: 700;
}

#lgParcelSorting .sorting-empty p {
    font-size: 9px;
    line-height: 1.6;
}

#lgParcelSorting .sorting-list-footer {
    padding: 12px 16px;
    background: #fff;
    font-size: 9px;
}

/* =========================================================
   HOVER / RESPONSIVE
========================================================= */

@media (hover: hover) and (pointer: fine) {
    #lgParcelSorting .sorting-stat:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43, 33, 21, .06);
    }
}

@media (max-width: 900px) {
    #lgParcelSorting .sorting-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    #lgParcelSorting .sorting-page-header,
    #lgParcelSorting .sorting-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgParcelSorting .sorting-search {
        width: 100%;
    }

    #lgParcelSorting .sorting-header-actions {
        width: 100%;
    }

    #lgParcelSorting .sorting-header-actions .sorting-button {
        flex: 1;
    }

    #lgParcelSorting .sorting-filter-row {
        flex-direction: column;
        align-items: stretch;
        padding: 0 14px 13px;
    }

    #lgParcelSorting .sorting-flow {
        max-width: 100%;
        overflow-x: auto;
    }

    #lgParcelSorting .sorting-flow :is(a, span) {
        flex-shrink: 0;
        font-size: 9px;
    }
}

@media (max-width: 520px) {
    #lgParcelSorting .sorting-header-actions {
        flex-direction: column;
    }

    #lgParcelSorting .sorting-header-actions .sorting-button {
        width: 100%;
    }

    #lgParcelSorting .sorting-stat {
        padding: 12px;
    }

    #lgParcelSorting .sorting-stat strong {
        font-size: 22px;
    }

    #lgParcelSorting .sorting-stat-icon {
        flex-basis: 34px;
        width: 34px;
        height: 34px;
    }
}

@media (prefers-reduced-motion: reduce) {
    #lgParcelSorting .sorting-stat:hover {
        transform: none !important;
    }
}

/* Parcel Sorting compact workspace: self-contained structure with shared-style resilience. */
#lgParcelSorting,#lgParcelSorting *,#lgParcelSorting *::before,#lgParcelSorting *::after {box-sizing:border-box;}
#lgParcelSorting {width:100%;min-width:0;margin-inline:auto;padding-bottom:22px;color:#263244;font-family:Poppins,system-ui,sans-serif;font-size:12px;}
#lgParcelSorting [hidden] {display:none!important;}
#lgParcelSorting .sorting-page-header {display:flex;flex-direction:row;align-items:center;justify-content:space-between;gap:14px 20px;margin:0 0 17px;padding:0 0 12px;border:0;border-radius:0;background:transparent;box-shadow:none;}
#lgParcelSorting .sorting-page-header::before,#lgParcelSorting .sorting-page-header::after {display:none!important;}
#lgParcelSorting .sorting-page-header h1 {margin:0;font-size:clamp(26px,2.2vw,32px);color:#202b39;}
#lgParcelSorting .sorting-page-header h1 span {color:#d9950b;}
#lgParcelSorting .sorting-page-header .sorting-eyebrow {font-size:11px;color:#a97716;margin:0 0 3px;}
#lgParcelSorting .sorting-header-actions {display:flex;align-items:center;gap:9px;flex-wrap:wrap;justify-content:flex-end;}
#lgParcelSorting a.sorting-button,#lgParcelSorting button.sorting-button {display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:39px;padding:9px 15px;border:1px solid #dfe5eb;border-radius:11px;background:#fff;color:#526174;font-size:11px;font-weight:650;text-decoration:none;white-space:nowrap;}
#lgParcelSorting a.sorting-button-primary,#lgParcelSorting button.sorting-button-primary {background:#d9950b;color:white;border-color:#d9950b;}
#lgParcelSorting a.sorting-button-primary:hover,#lgParcelSorting button.sorting-button-primary:hover {background:#c88708;border-color:#c88708;color:white;}
#logisticsContent #lgParcelSorting .sorting-stats,#lgParcelSorting .sorting-stats {display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr));align-items:stretch;gap:13px;margin:0 0 17px;width:100%;}
#logisticsContent #lgParcelSorting .sorting-stat,#lgParcelSorting .sorting-stat {display:flex;align-items:flex-start;justify-content:space-between;flex-direction:row;gap:10px;min-width:0;min-height:107px;padding:16px;border:1px solid #e5eaf0;border-radius:19px;background:#fff;box-shadow:0 9px 26px rgba(15,23,42,.07);}
#lgParcelSorting .sorting-stat::after {display:none!important;}
#lgParcelSorting .sorting-stat>div {min-width:0;flex:1 1 auto;}
#lgParcelSorting .sorting-stat p {margin:0;font-size:12px;color:#626e7d;font-weight:600;}
#lgParcelSorting .sorting-stat strong,#lgParcelSorting .sorting-stat-amber strong,#lgParcelSorting .sorting-stat-green strong,#lgParcelSorting .sorting-stat-blue strong {display:block;margin:5px 0 4px;color:#1f2937;font-size:28px;font-weight:750;line-height:1.15;}
#lgParcelSorting .sorting-stat span:not(.sorting-stat-icon) {display:block;color:#919cab;font-size:10px;line-height:1.5;}
#lgParcelSorting .sorting-stat .sorting-stat-icon {display:grid;place-items:center;flex:0 0 34px;width:34px;height:34px;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:#8b72b3;}
#lgParcelSorting .sorting-stat-amber .sorting-stat-icon {color:#c48a20;}
#lgParcelSorting .sorting-stat-green .sorting-stat-icon {color:#398660;}
#lgParcelSorting .sorting-stat-blue .sorting-stat-icon {color:#4a83b8;}
#lgParcelSorting .sorting-stat .sorting-stat-icon svg {display:block;width:30px;height:30px;}
#lgParcelSorting .sorting-directory-controls {display:grid;grid-template-columns:minmax(0,1fr) minmax(155px,220px) auto auto;align-items:center;gap:11px;margin-bottom:15px;width:100%;min-width:0;}
#lgParcelSorting .sorting-directory-search {display:flex;align-items:center;position:relative;width:100%;min-width:0;height:48px;background:#fff;border:1px solid #dfe5eb;border-radius:13px;}
#lgParcelSorting .sorting-directory-search>svg {position:absolute;left:15px;width:18px;height:18px;color:#94a3b8;pointer-events:none;}
#lgParcelSorting .sorting-directory-search input {width:100%;height:100%;padding:0 14px 0 46px;border:0!important;border-radius:13px;background:transparent!important;outline:0!important;box-shadow:none!important;font-size:12px;color:#334155;}
#lgParcelSorting .sorting-directory-search:focus-within {border-color:#9cabbc;box-shadow:0 0 0 3px rgba(148,163,184,.14);}
#lgParcelSorting .sorting-status-select select {width:100%;height:48px;padding:0 13px;border:1px solid #dfe5eb;border-radius:13px;background:#fff;color:#334155;font-size:12px;}
#lgParcelSorting .sorting-status-select select:focus {outline:2px solid rgba(148,163,184,.25);outline-offset:1px;}
#lgParcelSorting .sorting-apply-filter,#lgParcelSorting .sorting-reset-filter {display:inline-flex;align-items:center;justify-content:center;gap:8px;height:48px;padding:0 20px;border:1px solid #d9950b;border-radius:13px;background:#d9950b;color:white;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;}
#lgParcelSorting .sorting-apply-filter svg {width:16px;height:16px;}
#lgParcelSorting .sorting-reset-filter {min-width:90px;padding:0 16px;border-color:#dfe5eb;color:#526174;background:#fff;}
#lgParcelSorting .sorting-apply-filter:hover {background:#c88708;}
#lgParcelSorting .sorting-reset-filter:hover {background:#f4f6f8;}
#lgParcelSorting .sorting-workspace {width:100%;min-width:0;overflow:hidden;border:1px solid #e4eaf0;border-radius:20px;background:#fff;box-shadow:0 10px 32px rgba(15,23,42,.07);}
#lgParcelSorting .sorting-toolbar.sorting-directory-heading {display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 21px;border-bottom:1px solid #edf0f3;background:#fff;}
#lgParcelSorting .sorting-toolbar h2 {margin:0;font-size:19px;color:#1f2937;font-weight:750;letter-spacing:-.035em;}
#lgParcelSorting .sorting-directory-count {padding:7px 12px;border:1px solid #e4eaf0;border-radius:11px;background:#f8fafc;color:#68798b;font-size:10px;font-weight:700;white-space:nowrap;}
#lgParcelSorting .sorting-table-scroll {display:block;width:100%;min-width:0;max-width:100%;overflow:visible;}
#lgParcelSorting .sorting-table {width:100%;min-width:0;table-layout:fixed;border-collapse:collapse;text-align:left;}
#lgParcelSorting .sorting-table thead {background:#f5f7f9;}
#lgParcelSorting .sorting-table th {padding:13px 16px;border-bottom:1px solid #e5ebf1;color:#718093;font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;text-align:left;white-space:normal;}
#lgParcelSorting .sorting-table th:nth-child(1) {width:27%;}
#lgParcelSorting .sorting-table th:nth-child(2) {width:36%;}
#lgParcelSorting .sorting-table th:nth-child(3) {width:20%;}
#lgParcelSorting .sorting-table th:nth-child(4) {width:17%;}
#lgParcelSorting .sorting-table td {min-width:0;padding:15px 16px;border-bottom:1px solid #edf0f3;font-size:11px;color:#4c5866;line-height:1.55;vertical-align:middle;overflow-wrap:anywhere;}
#lgParcelSorting .sorting-table td strong {font-size:12px;color:#253244;font-weight:700;line-height:1.5;}
#lgParcelSorting .sorting-table td p {margin:4px 0 0;font-size:10px;color:#8390a0;line-height:1.5;}
#lgParcelSorting .sorting-table tbody tr:hover {background:#f2f4f6!important;box-shadow:none!important;}
#lgParcelSorting .sorting-compact-route {display:flex;align-items:center;gap:8px;min-width:0;font-size:11px;font-weight:650;color:#455568;line-height:1.6;}
#lgParcelSorting .sorting-compact-route span {min-width:0;overflow-wrap:anywhere;}
#lgParcelSorting .sorting-compact-route svg {width:15px;height:15px;flex:0 0 15px;color:#94a3b8;}
#lgParcelSorting .sorting-badge {display:inline-flex;align-items:center;gap:6px;min-height:26px;padding:5px 10px;border:1px solid #e9d9b4;border-radius:999px;background:#fff8e9;color:#a87513;font-size:10px;font-weight:700;line-height:1.2;white-space:nowrap;}
#lgParcelSorting .sorting-badge-sorted {border-color:#d3e6d8;background:#edf8f0;color:#34835b;}
#lgParcelSorting .sorting-status-dot {width:7px;height:7px;display:inline-block;border-radius:999px;background:currentColor;opacity:.8;}
#lgParcelSorting .sorting-table td .sorting-zone-small {margin-top:6px;color:#8997a5;font-size:10px;}
#lgParcelSorting .sorting-row-actions {display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
#lgParcelSorting .sorting-view-icon {display:grid;place-items:center;flex:0 0 28px;width:28px;height:32px;padding:0;background:transparent;border:0;color:#64748b;cursor:pointer;}
#lgParcelSorting .sorting-view-icon svg {width:20px;height:20px;}
#lgParcelSorting .sorting-view-icon:hover {color:#29394b;}
#lgParcelSorting .sorting-row-sort {min-height:34px;padding:7px 12px;background:#d9950b;border:1px solid #d9950b;border-radius:9px;color:#fff;font-size:11px;font-weight:700;cursor:pointer;}
#lgParcelSorting .sorting-row-sort:hover {background:#c88708;border-color:#c88708;}
#lgParcelSorting .sorting-view-text {padding:7px 0;border:0;background:transparent;color:#af7818;font-size:11px;font-weight:700;cursor:pointer;}
#lgParcelSorting .sorting-list-footer {display:flex;justify-content:space-between;align-items:center;gap:12px;min-height:60px;padding:13px 21px;background:#fff;color:#7d8998;font-size:11px;}
#lgParcelSorting .sorting-page-size-label {display:flex;align-items:center;gap:9px;font-size:11px;white-space:nowrap;}
#lgParcelSorting .sorting-page-size-label select {height:35px;padding:0 9px;border:1px solid #dfe5eb;border-radius:10px;background:#fff;color:#334155;font-size:12px;}
#lgParcelSorting .sorting-pagination {display:flex;justify-content:center;align-items:center;flex-wrap:wrap;gap:7px;min-height:49px;margin:14px auto 0;}
#lgParcelSorting .sorting-pagination:empty {display:none;}
#lgParcelSorting .sorting-pagination button {height:35px;min-width:35px;padding:0 10px;border:1px solid transparent;border-radius:10px;background:#f6f7f9;color:#8492a5;font-size:12px;font-weight:650;cursor:pointer;}
#lgParcelSorting .sorting-pagination button[aria-current="page"] {background:#d09521;border-color:#d09521;color:#fff;box-shadow:0 6px 13px rgba(208,149,33,.15);}
#lgParcelSorting .sorting-pagination button:disabled {opacity:.35;cursor:not-allowed;}
#lgParcelSorting .sorting-empty {padding:50px 18px;text-align:center;}
#lgParcelSorting .sorting-empty-icon {display:flex;align-items:center;justify-content:center;width:88px;height:88px;margin:0 auto 12px;padding:0;border:0!important;border-radius:0!important;background:transparent!important;color:#aeb7c1!important;box-shadow:none!important;}
#lgParcelSorting .sorting-empty-icon svg {width:58px;height:58px;}
#lgParcelSorting .sorting-empty h3 {margin:0 0 7px;color:#899aaa;font-size:22px;font-weight:750;line-height:1.3;}
#lgParcelSorting .sorting-empty p {max-width:560px;margin:0 auto 17px;color:#93a2b3;font-size:12px;line-height:1.7;}
#lgParcelSorting .sorting-empty .sorting-button-primary {min-height:42px;padding:11px 20px;font-size:12px;}
#lgParcelSorting .sorting-text-link {font-size:12px;color:#af7818;}
#lgParcelSorting .sorting-modal-overlay {position:fixed!important;inset:0!important;z-index:99999!important;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(18,26,39,.54);overflow-y:auto;}
#lgParcelSorting .sorting-modal-overlay[hidden] {display:none!important;}
#lgParcelSorting .sorting-modal-panel {display:flex;flex-direction:column;width:min(680px,100%);max-height:88dvh;overflow:hidden;border:1px solid #e2e8ef;border-radius:20px!important;background:#fff!important;box-shadow:0 24px 70px rgba(15,23,42,.23)!important;color:#263244;}
#lgParcelSorting .sorting-modal-panel-small {width:min(520px,100%);}
#lgParcelSorting .sorting-modal-heading {display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:22px 24px 18px;border-bottom:1px solid #edf0f4;background:#fff;}
#lgParcelSorting .sorting-modal-eyebrow {font-size:10px;letter-spacing:.12em;color:#ba811a;font-weight:750;}
#lgParcelSorting .sorting-modal-heading h2 {margin:4px 0 0;font-size:24px;line-height:1.25;color:#263244;font-weight:750;letter-spacing:-.035em;}
#lgParcelSorting .sorting-modal-heading p {margin:5px 0 0;color:#8391a2;font-size:12px;line-height:1.6;overflow-wrap:anywhere;}
#lgParcelSorting .sorting-modal-close {display:grid;place-items:center;flex:0 0 36px;width:36px;height:36px;padding:0;border:1px solid #dfe6ee;border-radius:10px;background:#fff;color:#64748b;font-size:24px;cursor:pointer;}
#lgParcelSorting .sorting-modal-content {min-height:0;padding:22px 24px;overflow-y:auto;scrollbar-width:none;background:#fff;}
#lgParcelSorting .sorting-modal-content::-webkit-scrollbar,#lgParcelSorting .sorting-modal-overlay::-webkit-scrollbar {display:none;}
#lgParcelSorting .sorting-modal-info-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
#lgParcelSorting .sorting-modal-info {min-width:0;padding:13px 15px;border:1px solid #e7ecf2;border-radius:12px;background:#f8fafc;overflow-wrap:anywhere;}
#lgParcelSorting .sorting-modal-label {display:block;margin-bottom:7px;color:#8997a7;font-size:10px;font-weight:750;letter-spacing:.065em;text-transform:uppercase;}
#lgParcelSorting .sorting-modal-info strong {display:block;color:#2b394b;font-size:12px;font-weight:700;line-height:1.55;}
#lgParcelSorting .sorting-modal-info p {margin:4px 0 0;color:#667487;font-size:11px;line-height:1.6;white-space:pre-wrap;}
#lgParcelSorting .sorting-modal-info-full {grid-column:1/-1;}
#lgParcelSorting .sorting-modal-products {margin-top:16px;padding:16px;border:1px solid #e7ecf2;border-radius:12px;}
#lgParcelSorting .sorting-modal-products h3 {margin:0 0 8px;color:#29374b;font-size:13px;font-weight:750;}
#lgParcelSorting .sorting-modal-products-list {list-style:none;margin:0;padding:0;}
#lgParcelSorting .sorting-modal-products-list li {display:flex;justify-content:space-between;gap:10px;padding:8px 0;color:#667487;font-size:11px;border-top:1px solid #f1f3f6;}
#lgParcelSorting .sorting-modal-products-list li strong {white-space:nowrap;color:#334155;}
#lgParcelSorting .sorting-modal-hub-link {display:inline-block;margin-top:16px;font-size:12px;font-weight:700;color:#af7818;text-decoration:none;}
#lgParcelSorting .sorting-modal-sort-form {display:flex;flex-direction:column;min-height:0;}
#lgParcelSorting .sorting-modal-field-label {display:block;margin:15px 0 6px;color:#465467;font-size:12px;font-weight:700;}
#lgParcelSorting .sorting-modal-field-label span {font-weight:500;color:#96a1ae;}
#lgParcelSorting .sorting-modal-sort-form :is(input[type=text],textarea) {display:block;width:100%;min-height:42px;padding:11px 12px;border:1px solid #dce4ec;border-radius:10px;background:#fff;color:#243244;font-size:12px;line-height:1.55;}
#lgParcelSorting .sorting-modal-sort-form textarea {min-height:83px;resize:vertical;}
#lgParcelSorting .sorting-modal-field-hint {margin:6px 0 1px;color:#93a0af;font-size:10px;line-height:1.55;}
#lgParcelSorting .sorting-auto-locator {margin-top:14px;padding:14px 16px;border:1px solid #dbe6f2;border-radius:12px;background:#f7faff;}
#lgParcelSorting .sorting-auto-locator .sorting-auto-label {display:block;color:#64748b;font-size:10px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;}
#lgParcelSorting .sorting-auto-locator strong {display:block;margin:6px 0;color:#1e3a5f;font-size:14px;font-weight:600;letter-spacing:.015em;overflow-wrap:anywhere;}
#lgParcelSorting .sorting-auto-locator p {margin:0;color:#64748b;font-size:10px;line-height:1.7;}
#lgParcelSorting .sorting-physical-toggle {border:0;background:transparent;padding:10px 0 0;cursor:pointer;color:#235d91;font-size:11px;font-weight:600;text-decoration:underline;text-underline-offset:3px;}
#lgParcelSorting .sorting-physical-fields {margin-top:5px;}
#lgParcelSorting .sorting-physical-fields[hidden] {display:none!important;}

#lgParcelSorting .sorting-modal-sort-form :is(input,textarea):focus {outline:2px solid rgba(100,116,139,.18);border-color:#93a1b3;box-shadow:none;}
#lgParcelSorting .sorting-modal-footer {display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:15px 24px 20px;border-top:1px solid #edf0f4;background:#fff;}
#lgParcelSorting .sorting-modal-secondary,#lgParcelSorting .sorting-modal-confirm {min-height:40px;padding:9px 17px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;}
#lgParcelSorting .sorting-modal-secondary {border:1px solid #dfe5ec;background:#fff;color:#46546a;}
#lgParcelSorting .sorting-modal-confirm {border:1px solid #d9950b;background:#d9950b;color:#fff;box-shadow:0 6px 14px rgba(217,149,11,.14);}
#lgParcelSorting .sorting-modal-confirm:hover {background:#c88708;border-color:#c88708;}
#lgParcelSorting .sorting-modal-confirm:disabled {opacity:.55;cursor:not-allowed;}
@media(max-width:1100px) {#lgParcelSorting .sorting-directory-controls {grid-template-columns:minmax(0,1fr) minmax(140px,210px) auto auto;gap:8px;} #lgParcelSorting .sorting-table th,#lgParcelSorting .sorting-table td {padding-left:10px;padding-right:10px;}}
@media(max-width:900px) {#logisticsContent #lgParcelSorting .sorting-stats,#lgParcelSorting .sorting-stats {grid-template-columns:repeat(2,minmax(0,1fr));} #lgParcelSorting .sorting-directory-controls {grid-template-columns:minmax(0,1fr) minmax(140px,180px);} #lgParcelSorting .sorting-apply-filter,#lgParcelSorting .sorting-reset-filter {width:100%;} #lgParcelSorting .sorting-table thead {display:none;} #lgParcelSorting .sorting-table,#lgParcelSorting .sorting-table tbody {display:block;width:100%;} #lgParcelSorting .sorting-table tbody {padding:12px;display:grid;gap:12px;} #lgParcelSorting .sorting-table tbody tr {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:14px;border:1px solid #e5eaf0;border-radius:13px;background:#fff;box-shadow:0 4px 12px rgba(15,23,42,.035);} #lgParcelSorting .sorting-table tbody tr[hidden] {display:none!important;} #lgParcelSorting .sorting-table td {display:block;width:auto!important;min-width:0!important;padding:0;border:0;} #lgParcelSorting .sorting-table td::before {content:attr(data-label);display:block;margin-bottom:5px;color:#93a0af;font-size:10px;font-weight:700;letter-spacing:.055em;text-transform:uppercase;} #lgParcelSorting .sorting-table td:first-child,#lgParcelSorting .sorting-table td:last-child {grid-column:1/-1;} }
@media(max-width:600px) {#lgParcelSorting .sorting-page-header {align-items:stretch;} #lgParcelSorting .sorting-header-actions {width:100%;justify-content:flex-start;} #lgParcelSorting .sorting-header-actions .sorting-button {flex:1;} #lgParcelSorting .sorting-stat {min-height:109px;padding:12px;} #lgParcelSorting .sorting-stat p {font-size:11px;} #lgParcelSorting .sorting-stat strong,#lgParcelSorting .sorting-stat-amber strong,#lgParcelSorting .sorting-stat-green strong,#lgParcelSorting .sorting-stat-blue strong {font-size:23px;} #lgParcelSorting .sorting-stat span:not(.sorting-stat-icon) {font-size:9px;} #lgParcelSorting .sorting-stat .sorting-stat-icon {flex-basis:27px;width:27px;height:27px;} #lgParcelSorting .sorting-stat .sorting-stat-icon svg {width:26px;height:26px;} #lgParcelSorting .sorting-directory-controls {grid-template-columns:minmax(0,1fr) minmax(80px,120px);gap:9px;} #lgParcelSorting .sorting-directory-search,#lgParcelSorting .sorting-status-select {grid-column:1/-1;} #lgParcelSorting .sorting-table tbody tr {grid-template-columns:1fr;} #lgParcelSorting .sorting-table td:first-child,#lgParcelSorting .sorting-table td:last-child {grid-column:1;} #lgParcelSorting .sorting-toolbar.sorting-directory-heading {padding:15px 14px;} #lgParcelSorting .sorting-toolbar h2 {font-size:16px;} #lgParcelSorting .sorting-list-footer {padding:12px 14px;flex-wrap:wrap;} #lgParcelSorting .sorting-empty-icon {width:64px;height:64px;} #lgParcelSorting .sorting-empty-icon svg {width:40px;height:40px;} #lgParcelSorting .sorting-empty h3 {font-size:20px;} #lgParcelSorting .sorting-modal-heading {padding:17px 16px 14px;} #lgParcelSorting .sorting-modal-heading h2 {font-size:21px;} #lgParcelSorting .sorting-modal-content {padding:16px;} #lgParcelSorting .sorting-modal-info-grid {grid-template-columns:1fr;} #lgParcelSorting .sorting-modal-footer {padding:14px 16px 16px;} }
@media(prefers-reduced-motion:reduce) {#lgParcelSorting *,#lgParcelSorting *::before,#lgParcelSorting *::after {animation:none!important;transition:none!important;}}
</style>
@endpush

@section('content')

<div
    id="lgParcelSorting"
    class="sorting-page"
    data-sorting-workspace
>

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <header class="sorting-page-header">

        <div class="min-w-0">

            <p class="sorting-eyebrow">
                ORIGIN HUB OPERATIONS
            </p>

            <h1>
                Parcel <span>Sorting</span>
            </h1>

        </div>

        <div class="sorting-header-actions">

            <a
                href="{{ route('logistics.incoming-parcels') }}"
                class="sorting-button"
            >
                <span aria-hidden="true">←</span>
                Incoming Parcels
            </a>

            <a
                href="{{ route('logistics.hub-transfers') }}"
                class="sorting-button sorting-button-primary"
            >
                Hub Transfers
                <span aria-hidden="true">→</span>
            </a>

        </div>

    </header>

    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))

        <div
            class="sorting-alert sorting-alert-success"
            role="status"
        >
            <strong>
                Sorting queue updated
            </strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif

    @if($errors->any())

        <div
            class="sorting-alert sorting-alert-danger"
            role="alert"
        >
            <strong>
                Could not sort parcel
            </strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    {{-- =====================================================
        SUMMARY DATA
    ====================================================== --}}

    @php
        $receivedCount = $parcels
            ->where('status', 'received')
            ->count();

        $sortedCount = $parcels
            ->where('status', 'sorted')
            ->count();

        $originHubCount = $parcels
            ->map(function ($parcel) {

                return $parcel
                    ->order
                    ?->deliveryRouteStops
                    ?->firstWhere(
                        'stop_key',
                        'origin_facility'
                    )
                    ?->logistics_facility_id;

            })
            ->filter()
            ->unique()
            ->count();

        $destinationHubCount = $parcels
            ->map(function ($parcel) {

                return $parcel
                    ->order
                    ?->deliveryRouteStops
                    ?->firstWhere(
                        'stop_key',
                        'destination_facility'
                    )
                    ?->logistics_facility_id;

            })
            ->filter()
            ->unique()
            ->count();

        // Count parcels with a recorded locator, not "physical sorting zones":
        // each generated staging reference intentionally belongs to one parcel.
        $referenceCount = $parcels
            ->where('status', 'sorted')
            ->filter(fn ($parcel) => filled($parcel->sorting_zone))
            ->count();

        $summaryCards = [
            [
                'Sorting Queue',
                $parcels->count(),
                'Received and sorted parcels',
                'neutral',
            ],
            [
                'Needs Sorting',
                $receivedCount,
                'Awaiting sorting confirmation',
                'amber',
            ],
            [
                'Sorted',
                $sortedCount,
                'Ready for transfer planning',
                'green',
            ],
            [
                'Sorting References',
                $referenceCount,
                $destinationHubCount . ' destination '
                    . ($destinationHubCount === 1 ? 'hub' : 'hubs'),
                'blue',
            ],
        ];
    @endphp

    {{-- =====================================================
        KPI CARDS
    ====================================================== --}}

    <section
        class="sorting-stats"
        aria-label="Parcel sorting overview"
    >

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="sorting-stat sorting-stat-{{ $tone }}">

                <div class="min-w-0">

                    <p>{{ $label }}</p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>{{ $note }}</span>

                </div>

                <span
                    class="sorting-stat-icon"
                    aria-hidden="true"
                >

                    @switch($tone)

                        @case('amber')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>

                            @break

                        @case('green')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>

                            @break

                        @case('blue')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect x="3" y="5" width="8" height="7" rx="1"/>
                                <rect x="13" y="5" width="8" height="7" rx="1"/>
                                <rect x="8" y="14" width="8" height="7" rx="1"/>
                            </svg>

                            @break

                        @default

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 5h16v14H4zM8 9h8M8 13h8"/>
                            </svg>

                    @endswitch

                </span>

            </article>

        @endforeach

    </section>

    {{-- =====================================================
        SORTING WORKSPACE
    ====================================================== --}}

    <div class="sorting-directory-controls" aria-label="Search and filter sorting queue">
        <label class="sorting-directory-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
            <span class="sr-only">Search parcels by order, Seller, Buyer, hub, or zone</span>
            <input type="search" data-sorting-search autocomplete="off" placeholder="Search order, Seller, hub, or zone...">
        </label>
        <label class="sorting-status-select">
            <span class="sr-only">Filter sorting status</span>
            <select data-sorting-status-select>
                <option value="all">All Status</option>
                <option value="received">Needs Sorting</option>
                <option value="sorted">Sorted</option>
            </select>
        </label>
        <button type="button" class="sorting-apply-filter" data-sorting-apply>
            <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            Apply Filter
        </button>
        <button type="button" class="sorting-reset-filter" data-sorting-clear>Reset</button>
    </div>

    <section class="sorting-workspace" aria-labelledby="sorting-queue-heading">
        <header class="sorting-toolbar sorting-directory-heading">
            <h2 id="sorting-queue-heading">Origin Hub Sorting Queue</h2>
            <span class="sorting-directory-count">{{ $parcels->count() }} PARCELS</span>
        </header>

        {{-- =================================================
            SORTING TABLE
        ================================================== --}}

        <div
            class="sorting-table-scroll"
            @if($parcels->isEmpty()) hidden @endif
        >

            <table class="sorting-table">

                <thead>
                    <tr>
                        <th scope="col">Order / Seller</th>
                        <th scope="col">Route</th>
                        <th scope="col">Status / Reference</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parcels as $parcel)
                        @php
                            $order = $parcel->order;
                            $routeStops = $order?->deliveryRouteStops?->keyBy('stop_key') ?? collect();
                            $originStop = $routeStops->get('origin_facility');
                            $destinationStop = $routeStops->get('destination_facility');
                            $originName = $originStop?->facility?->name ?: $originStop?->name ?: 'Origin hub pending';
                            $destinationName = $destinationStop?->facility?->name ?: $destinationStop?->name ?: 'Destination pending';
                            $originMunicipality = $originStop?->facility?->municipality_name;
                            $destinationMunicipality = $destinationStop?->facility?->municipality_name;
                            $sellerName = $order?->seller?->store_name ?: $order?->pickup_name ?: 'SARI Seller';
                            $state = $parcel->status === 'sorted' ? 'sorted' : 'received';
                            $isPhysicalZone = $parcel->sorting_zone && !\App\Support\ParcelSortingLocator::isGenerated($parcel->sorting_zone);
                            $canPreviewLocator = (int) ($originStop?->logistics_facility_id ?? 0) > 0
                                && (int) ($destinationStop?->logistics_facility_id ?? 0) > 0;
                            $autoLocator = $canPreviewLocator
                                ? \App\Support\ParcelSortingLocator::forParcel(
                                    (int) $parcel->id,
                                    (int) $originStop->logistics_facility_id,
                                    (int) $destinationStop->logistics_facility_id
                                  )
                                : 'Pending verified hub route';
                            $searchText = implode(' ', array_filter([
                                $order?->order_number, $sellerName, $order?->buyer_name,
                                $order?->pickup_address, $originName, $originMunicipality,
                                $destinationName, $destinationMunicipality,
                                $parcel->sorting_zone, $parcel->status,
                            ]));
                        @endphp
                        <tr data-sorting-row data-state="{{ $state }}"
                            data-search="{{ $searchText }}"
                            data-order-number="{{ $order?->order_number ?: 'Parcel' }}"
                            data-origin-name="{{ $originName }}"
                            data-destination-name="{{ $destinationName }}"
                            data-auto-locator="{{ $autoLocator }}"
                            @if($parcel->status === 'received')
                                data-sort-url="{{ route('logistics.parcel-sorting.sort', $parcel) }}"
                            @endif>
                            <td data-label="Order / Seller">
                                <strong class="sorting-order-number">{{ $order?->order_number ?: 'Order unavailable' }}</strong>
                                <p>{{ $sellerName }}</p>
                                <p>{{ $order?->buyer_name ?: 'Buyer not recorded' }}</p>
                            </td>
                            <td data-label="Route">
                                <div class="sorting-compact-route">
                                    <span>{{ $originMunicipality ?: $originName }}</span>
                                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 12h16m-6-6 6 6-6 6"/></svg>
                                    <span>{{ $destinationMunicipality ?: $destinationName }}</span>
                                </div>
                                <p>{{ $parcel->received_at?->timezone('Asia/Manila')->format('M d, Y · g:i A') ?: 'Receipt time not recorded' }}</p>
                            </td>
                            <td data-label="Status / Reference">
                                @if($parcel->status === 'sorted')
                                    <span class="sorting-badge sorting-badge-sorted"><span class="sorting-status-dot"></span>Sorted</span>
                                    <p class="sorting-zone-small">{{ $isPhysicalZone ? 'Physical rack' : 'Staging reference' }}: {{ $parcel->sorting_zone ?: 'Not recorded' }}</p>
                                @else
                                    <span class="sorting-badge sorting-badge-received"><span class="sorting-status-dot"></span>Needs Sorting</span>
                                    <p class="sorting-zone-small">Staging reference pending</p>
                                @endif
                            </td>
                            <td data-label="Action">
                                <div class="sorting-row-actions">
                                    <button type="button" class="sorting-view-icon" data-sorting-open-details title="View parcel details" aria-label="View details for {{ $order?->order_number ?: 'parcel' }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    @if($parcel->status === 'received')
                                        <button type="button" class="sorting-row-sort" data-sorting-open-sort>Sort Parcel</button>
                                    @else
                                        <button type="button" class="sorting-view-text" data-sorting-open-details>View</button>
                                    @endif
                                </div>
                                <template data-sorting-details-template>
                                    <div class="sorting-modal-info-grid">
                                        <section class="sorting-modal-info"><span class="sorting-modal-label">Seller Pickup</span><strong>{{ $order?->pickup_name ?: $sellerName }}</strong><p>{{ $order?->pickup_address ?: 'No pickup address recorded' }}</p></section>
                                        <section class="sorting-modal-info"><span class="sorting-modal-label">Buyer Destination</span><strong>{{ $order?->buyer_name ?: 'Not recorded' }}</strong><p>{{ $order?->buyer_address ?: 'No buyer address recorded' }}</p></section>
                                        <section class="sorting-modal-info"><span class="sorting-modal-label">Pickup Rider</span><strong>{{ $order?->courier_name ?: 'Pickup Rider' }}</strong><p>{{ $order?->courier_email ?: 'No Rider email recorded' }}</p></section>
                                        <section class="sorting-modal-info"><span class="sorting-modal-label">Route</span><strong>{{ $originName }} → {{ $destinationName }}</strong><p>Received: {{ $parcel->received_at?->timezone('Asia/Manila')->format('M d, Y · g:i A') ?: 'Not recorded' }}</p></section>
                                        <section class="sorting-modal-info"><span class="sorting-modal-label">Sorting Status</span><strong>{{ $parcel->status === 'sorted' ? 'Sorted' : 'Needs Sorting' }}</strong><p>{{ $isPhysicalZone ? 'Physical rack' : 'Staging reference' }}: {{ $parcel->sorting_zone ?: 'Not assigned' }}</p>@if($parcel->sorted_at)<p>Sorted at: {{ $parcel->sorted_at->timezone('Asia/Manila')->format('M d, Y · g:i A') }}</p>@endif</section>
                                        <section class="sorting-modal-info"><span class="sorting-modal-label">Order Value</span><strong>₱{{ number_format((float) ($order?->total ?? 0),2) }}</strong><p>Marketplace order total</p></section>
                                        @if($parcel->notes)<section class="sorting-modal-info sorting-modal-info-full"><span class="sorting-modal-label">{{ $parcel->status === 'received' ? 'Intake Note' : 'Sorting Note' }}</span><p>{{ $parcel->notes }}</p></section>@endif
                                    </div>
                                    <section class="sorting-modal-products"><h3>Parcel Items</h3><ul class="sorting-modal-products-list">
                                        @forelse(($order?->items ?? []) as $item)
                                            <li><span>{{ $item['name'] ?? 'Product' }}@if(!empty($item['variant_label'])) · {{ $item['variant_label'] }}@endif</span><strong>× {{ $item['qty'] ?? $item['quantity'] ?? 1 }}</strong></li>
                                        @empty
                                            <li><span>Product details unavailable.</span></li>
                                        @endforelse
                                    </ul></section>
                                    @if($parcel->status === 'sorted')<a class="sorting-modal-hub-link" href="{{ route('logistics.hub-transfers') }}">Continue to Hub Transfer →</a>@endif
                                </template>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

        {{-- =================================================
            NO PARCELS
        ================================================== --}}

        @if($parcels->isEmpty())

            <div class="sorting-empty">

                <span
                    class="sorting-empty-icon"
                    aria-hidden="true"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round"><path d="m3 7 9-4 9 4v10l-9 4-9-4Z"/><path d="m3 7 9 5 9-5M12 12v9"/></svg>
                </span>

                <h3>
                    Sorting Queue Is Clear
                </h3>

                <p>
                    Parcels appear here after their physical receipt
                    is confirmed at the origin sorting hub.
                </p>

                <a
                    href="{{ route('logistics.incoming-parcels') }}"
                    class="sorting-button sorting-button-primary"
                >
                    View Incoming Parcels
                </a>

            </div>

        @endif

        {{-- =================================================
            FILTER EMPTY STATE
        ================================================== --}}

        <div
            class="sorting-empty"
            data-sorting-empty
            hidden
        >

            <span class="sorting-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg></span>
            <h3>
                No Matching Parcels
            </h3>

            <p>
                Try another order number, Seller, sorting zone,
                origin hub, or destination hub.
            </p>

            <button
                type="button"
                class="sorting-text-link"
                data-sorting-clear
            >
                Clear filters
            </button>

        </div>

        {{-- =================================================
            FOOTER
        ================================================== --}}

        <footer class="sorting-list-footer">
            <span data-sorting-visible-count aria-live="polite">Showing 0 of {{ $parcels->count() }} parcels</span>
            <label class="sorting-page-size-label">Rows per page
                <select data-sorting-page-size aria-label="Rows per page">
                    <option value="10">10</option><option value="20">20</option><option value="50">50</option>
                </select>
            </label>
        </footer>

    </section>

    <nav class="sorting-pagination" data-sorting-pagination aria-label="Sorting queue pages"></nav>

    <div class="sorting-modal-overlay" data-sorting-details-modal hidden>
        <div class="sorting-modal-panel" role="dialog" aria-modal="true" aria-labelledby="sorting-details-heading" tabindex="-1">
            <header class="sorting-modal-heading">
                <div><span class="sorting-modal-eyebrow">PARCEL INFORMATION</span><h2 id="sorting-details-heading">Parcel Details</h2><p data-sorting-details-subtitle></p></div>
                <button class="sorting-modal-close" type="button" data-sorting-modal-close aria-label="Close parcel details">×</button>
            </header>
            <div class="sorting-modal-content" data-sorting-details-content></div>
            <footer class="sorting-modal-footer"><button type="button" class="sorting-modal-secondary" data-sorting-modal-close>Close</button></footer>
        </div>
    </div>

    <div class="sorting-modal-overlay" data-sorting-sort-modal hidden>
        <div class="sorting-modal-panel sorting-modal-panel-small" role="dialog" aria-modal="true" aria-labelledby="sorting-sort-heading" tabindex="-1">
            <header class="sorting-modal-heading">
                <div><span class="sorting-modal-eyebrow">ORIGIN HUB SORTING</span><h2 id="sorting-sort-heading">Sort Parcel</h2><p>Sorting reference is generated automatically. No manual rack code needed.</p></div>
                <button class="sorting-modal-close" type="button" data-sorting-modal-close aria-label="Close sorting dialog">×</button>
            </header>
            <form data-sorting-form method="POST" action="" class="sorting-modal-sort-form">
                @csrf
                <div class="sorting-modal-content">
                    <div class="sorting-modal-info-grid">
                        <section class="sorting-modal-info"><span class="sorting-modal-label">Order Number</span><strong data-sorting-sort-order></strong></section>
                        <section class="sorting-modal-info"><span class="sorting-modal-label">Origin Hub</span><strong data-sorting-sort-origin></strong></section>
                        <section class="sorting-modal-info sorting-modal-info-full"><span class="sorting-modal-label">Destination Hub</span><strong data-sorting-sort-destination></strong></section>
                    </div>
                    <div class="sorting-auto-locator" aria-live="polite">
                        <span class="sorting-auto-label">Auto-generated staging reference</span>
                        <strong data-sorting-auto-locator>Calculated when you select a parcel</strong>
                        <p>Unique to this parcel and grouped by destination hub. This is a <b>logical sorting reference</b>, not a verified physical rack or shelf.</p>
                    </div>
                    <button type="button" class="sorting-physical-toggle" data-sorting-custom-toggle aria-expanded="false" aria-controls="sorting-physical-rack-fields">Use an actual physical rack instead</button>
                    <div id="sorting-physical-rack-fields" class="sorting-physical-fields" data-sorting-physical-fields hidden>
                        <label class="sorting-modal-field-label" for="sorting-modal-zone">Actual Zone / Rack <span>(optional override)</span></label>
                        <input type="text" id="sorting-modal-zone" name="sorting_zone" disabled maxlength="80" autocomplete="off" placeholder="e.g. ZONE-B / RACK-01 / SHELF-02">
                        <p class="sorting-modal-field-hint">Only enter a location if the parcel was actually placed there. Leave auto mode on otherwise.</p>
                    </div>
                    <label class="sorting-modal-field-label" for="sorting-modal-notes">Sorting Note <span>(optional)</span></label>
                    <textarea id="sorting-modal-notes" name="notes" rows="3" maxlength="1000" placeholder="Add sorting remarks if needed..."></textarea>
                </div>
                <footer class="sorting-modal-footer">
                    <button class="sorting-modal-secondary" type="button" data-sorting-modal-close>Cancel</button>
                    <button class="sorting-modal-confirm" type="submit">Confirm Sorting</button>
                </footer>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(() => {
    'use strict';
    function initializeSorting() {
        document.querySelectorAll('[data-sorting-workspace]').forEach(root => {
            if (root.dataset.initialized === 'true') return;
            root.dataset.initialized = 'true';

            const rows = [...root.querySelectorAll('[data-sorting-row]')];
            const search = root.querySelector('[data-sorting-search]');
            const statusSelect = root.querySelector('[data-sorting-status-select]');
            const applyButton = root.querySelector('[data-sorting-apply]');
            const pageSizeSelect = root.querySelector('[data-sorting-page-size]');
            const pagination = root.querySelector('[data-sorting-pagination]');
            const count = root.querySelector('[data-sorting-visible-count]');
            const filteredEmpty = root.querySelector('[data-sorting-empty]');
            let appliedStatus = 'all';
            let page = 1;
            let pageSize = 10;
            const normalize = value => String(value || '').toLocaleLowerCase().trim();
            const matches = row =>
                (appliedStatus === 'all' || row.dataset.state === appliedStatus)
                && normalize(row.dataset.search).includes(normalize(search?.value));

            function renderPagination(totalPages) {
                if (!pagination) return;
                pagination.replaceChildren();
                if (totalPages <= 1) return;
                function addButton(label, newPage, active = false, disabled = false) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    button.disabled = disabled;
                    button.setAttribute('aria-label', label === '‹' ? 'Previous page' : label === '›' ? 'Next page' : `Page ${label}`);
                    if (active) button.setAttribute('aria-current', 'page');
                    button.addEventListener('click', () => {
                        page = newPage;
                        applyFilters();
                    });
                    pagination.appendChild(button);
                }
                addButton('‹', Math.max(1, page - 1), false, page === 1);
                const pages = new Set([1, totalPages, page, page - 1, page + 1]);
                let previous = 0;
                [...pages].filter(p => p >= 1 && p <= totalPages).sort((a,b) => a-b).forEach(p => {
                    if (p - previous > 1) {
                        const dots = document.createElement('span');
                        dots.textContent = '…';
                        pagination.appendChild(dots);
                    }
                    addButton(String(p), p, p === page);
                    previous = p;
                });
                addButton('›', Math.min(totalPages, page + 1), false, page === totalPages);
            }
            function applyFilters() {
                const matching = rows.filter(matches);
                const totalPages = Math.max(1, Math.ceil(matching.length / pageSize));
                page = Math.min(Math.max(1, page), totalPages);
                rows.forEach(row => { row.hidden = true; });
                matching.slice((page - 1) * pageSize, page * pageSize).forEach(row => {row.hidden = false;});
                if (filteredEmpty) filteredEmpty.hidden = rows.length === 0 || matching.length > 0;
                if (count) {
                    const start = matching.length ? (page - 1) * pageSize + 1 : 0;
                    const end = Math.min(page * pageSize, matching.length);
                    count.textContent = `Showing ${start}–${end} of ${matching.length} ${matching.length === 1 ? 'parcel' : 'parcels'}`;
                }
                renderPagination(totalPages);
            }
            search?.addEventListener('input', () => {page = 1; applyFilters();});
            applyButton?.addEventListener('click', () => {appliedStatus = statusSelect?.value || 'all';page=1;applyFilters();});
            pageSizeSelect?.addEventListener('change', () => {
                pageSize = Math.max(1, Number(pageSizeSelect.value) || 10);
                page = 1;
                applyFilters();
            });
            root.querySelectorAll('[data-sorting-clear]').forEach(button => button.addEventListener('click', () => {
                if (search) search.value = '';
                if (statusSelect) statusSelect.value = 'all';
                appliedStatus = 'all';
                page = 1;
                applyFilters();
            }));

            const detailsModal = root.querySelector('[data-sorting-details-modal]');
            const sortModal = root.querySelector('[data-sorting-sort-modal]');
            const sortForm = root.querySelector('[data-sorting-form]');
            const zoneInput = root.querySelector('#sorting-modal-zone');
            const physicalToggle = root.querySelector('[data-sorting-custom-toggle]');
            const physicalFields = root.querySelector('[data-sorting-physical-fields]');
            const setPhysicalOverride = (enabled) => {
                if (physicalFields) physicalFields.hidden = !enabled;
                if (zoneInput) {
                    zoneInput.disabled = !enabled;
                    if (!enabled) { zoneInput.value = ''; zoneInput.setCustomValidity(''); }
                }
                if (physicalToggle) {
                    physicalToggle.setAttribute('aria-expanded', String(enabled));
                    physicalToggle.textContent = enabled
                        ? 'Use automatic staging reference'
                        : 'Use an actual physical rack instead';
                }
            };
            physicalToggle?.addEventListener('click', () => {
                const enabled = zoneInput?.disabled !== false;
                setPhysicalOverride(enabled);
                if (enabled) zoneInput?.focus();
            });
            const dialogs = [detailsModal, sortModal].filter(Boolean);
            let returnFocus = null;
            let submitting = false;
            const setText = (selector,value) => {
                const el = root.querySelector(selector);
                if (el) el.textContent = value || '';
            };
            function openDialog(dialog, trigger) {
                if (!dialog) return;
                returnFocus = trigger;
                dialogs.forEach(d => {d.hidden = d !== dialog;});
                document.body.style.overflow = 'hidden';
                (dialog.querySelector('[data-sorting-modal-close]') || dialog).focus();
            }
            function closeDialogs() {
                if (submitting) return;
                dialogs.forEach(dialog => {dialog.hidden = true;});
                document.body.style.overflow = '';
                returnFocus?.focus();
            }
            root.querySelectorAll('[data-sorting-open-details]').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('[data-sorting-row]');
                    const template = row?.querySelector('[data-sorting-details-template]');
                    const content = root.querySelector('[data-sorting-details-content]');
                    if (!row || !template || !content) return;
                    content.replaceChildren(template.content.cloneNode(true));
                    setText('[data-sorting-details-subtitle]',row.dataset.orderNumber);
                    openDialog(detailsModal, button);
                });
            });
            root.querySelectorAll('[data-sorting-open-sort]').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('[data-sorting-row]');
                    if (!row || !row.dataset.sortUrl || !sortForm) return;
                    sortForm.action = row.dataset.sortUrl;
                    sortForm.reset();
                    setPhysicalOverride(false);
                    setText('[data-sorting-auto-locator]', row.dataset.autoLocator);
                    setText('[data-sorting-sort-order]', row.dataset.orderNumber);
                    setText('[data-sorting-sort-origin]', row.dataset.originName);
                    setText('[data-sorting-sort-destination]', row.dataset.destinationName);
                    openDialog(sortModal, button);
                });
            });
            root.querySelectorAll('[data-sorting-modal-close]').forEach(button => button.addEventListener('click', closeDialogs));
            dialogs.forEach(dialog => dialog.addEventListener('click', event => {
                if (event.target === dialog) closeDialogs();
            }));
            root.addEventListener('keydown', event => {
                const active = dialogs.find(dialog => !dialog.hidden);
                if (!active) return;
                if (event.key === 'Escape') { event.preventDefault(); closeDialogs(); }
                if (event.key === 'Tab') {
                    const focusable = [...active.querySelectorAll('button:not([disabled]),input:not([disabled]),textarea:not([disabled]),a[href]')].filter(el => el.getClientRects().length);
                    if (!focusable.length) return;
                    const first = focusable[0], last = focusable[focusable.length - 1];
                    if (event.shiftKey && document.activeElement === first) {event.preventDefault();last.focus();}
                    else if (!event.shiftKey && document.activeElement === last) {event.preventDefault();first.focus();}
                }
            });
            sortForm?.addEventListener('submit', event => {
                if (submitting) {event.preventDefault();return;}
                if (zoneInput && !zoneInput.disabled) {
                    // Manual override only; the normal mode submits NO zone value.
                    zoneInput.value = zoneInput.value.trim();
                    if (!zoneInput.value) zoneInput.disabled = true;
                }
                submitting = true;
                const submit = sortForm.querySelector('[type="submit"]');
                if (submit) {submit.disabled = true;submit.textContent = 'Saving sorting result…';}
            });
            zoneInput?.addEventListener('input', () => zoneInput.setCustomValidity(''));
            applyFilters();
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeSorting);
    else initializeSorting();
    window.addEventListener('pageshow', event => {if(event.persisted) window.location.reload();});
})();
</script>
@endpush
