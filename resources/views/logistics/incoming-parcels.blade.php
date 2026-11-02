
@extends('layouts.logistics')

@section('title', 'Incoming Parcels — SARI Logistics')
@section('page-title', 'Incoming Parcels')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/logistics/incoming-parcels.css') }}?v=20261015-1">

<style>
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — ORIGIN HUB INTAKE
| Seller-style visual refinement
|--------------------------------------------------------------------------
| Page-scoped overrides only.
| The original incoming-parcels.css remains unchanged.
*/

#lgIncomingParcels {
    max-width: 1620px;
    --intake-gold: #d9950b;
    --intake-line: #e8e2d9;
    --intake-ink: #25211c;
    font-size: 10px;
}

#lgIncomingParcels [hidden] {
    display: none !important;
}

/* PAGE HEADING */

#lgIncomingParcels .intake-page-header {
    min-height: 0;
    padding: 5px 2px 17px;
    margin-bottom: 14px;
    gap: 16px;
    overflow: visible;
    border: 0;
    border-bottom: 1px solid #e4e1dc;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    flex-wrap: wrap;
}

#lgIncomingParcels .intake-page-header::after {
    display: none;
}

#lgIncomingParcels .intake-eyebrow {
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
}

#lgIncomingParcels .intake-page-header h1 {
    color: #24201b;
    font-size: clamp(23px, 2.15vw, 30px);
    font-weight: 750;
    letter-spacing: -.045em;
    line-height: 1.2;
}

#lgIncomingParcels .intake-page-header h1 span {
    color: #d9950b;
}

#lgIncomingParcels .intake-subtitle {
    max-width: 720px;
    margin-top: 6px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

/* HEADER ACTIONS */

#lgIncomingParcels .intake-header-actions {
    flex-wrap: wrap;
    gap: 8px;
}

#lgIncomingParcels .intake-page-header
.intake-button:not(.intake-button-primary) {
    min-height: 39px;
    padding: 0 14px;
    border: 1px solid #e4d8c6;
    border-radius: 10px;
    background: #fff;
    color: #776a5a;
    font-size: 10px;
    box-shadow: none;
}

#lgIncomingParcels .intake-page-header
.intake-button:not(.intake-button-primary):hover {
    background: #fff9ed;
    border-color: #d5bb87;
    color: #a36c0a;
}

#lgIncomingParcels .intake-button {
    min-height: 39px;
    padding: 9px 13px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 650;
}

#lgIncomingParcels .intake-button-primary {
    background: #d9950b;
    border-color: #d9950b;
    color: #fff;
}

#lgIncomingParcels .intake-button-primary:hover {
    background: #c88708;
    border-color: #c88708;
    color: #fff;
}

/* ALERTS */

#lgIncomingParcels .intake-alert strong {
    font-size: 10px;
}

#lgIncomingParcels .intake-alert p,
#lgIncomingParcels .intake-alert ul {
    font-size: 9px;
    line-height: 1.6;
}

/* KPI CARDS */

#lgIncomingParcels .intake-stats {
    gap: 12px;
}

#lgIncomingParcels .intake-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    min-height: 98px;
    padding: 14px;
    border: 1px solid #e8e2d9;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(33, 24, 14, .035);

    transition:
        transform .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgIncomingParcels .intake-stat::after {
    display: none;
}

#lgIncomingParcels .intake-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgIncomingParcels .intake-stat strong,
#lgIncomingParcels .intake-stat-amber strong,
#lgIncomingParcels .intake-stat-green strong,
#lgIncomingParcels .intake-stat-blue strong {
    margin: 8px 0 5px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.1;
}

#lgIncomingParcels .intake-stat span:not(.intake-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
}

#lgIncomingParcels .intake-stat .intake-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;
    width: 41px;
    height: 41px;
    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgIncomingParcels .intake-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgIncomingParcels .intake-stat-amber .intake-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgIncomingParcels .intake-stat-green .intake-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

#lgIncomingParcels .intake-stat-blue .intake-stat-icon {
    background: #eff5fa;
    color: #587ead;
}

/* WORKFLOW STEPPER */

#lgIncomingParcels .intake-flow {
    gap: 4px;
    margin: 14px 0;
    padding: 4px;
    border-radius: 10px;
}

#lgIncomingParcels .intake-flow :is(a, span) {
    min-height: 32px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgIncomingParcels .intake-flow b {
    flex: 0 0 21px;
    width: 21px;
    height: 21px;
    font-size: 9px;
}

/* WORKSPACE */

#lgIncomingParcels .intake-workspace {
    min-width: 0;
    border: 1px solid #e8e2d9;
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(33, 24, 14, .035);
}

#lgIncomingParcels .intake-toolbar {
    gap: 14px;
    padding: 15px 17px;
}

#lgIncomingParcels .intake-toolbar h2 {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
    color: #29231d;
}

#lgIncomingParcels .intake-toolbar p {
    margin-top: 4px;
    font-size: 9px;
    line-height: 1.55;
}

/* SEARCH */

#lgIncomingParcels .intake-search {
    width: min(315px, 100%);
}

#lgIncomingParcels .intake-search input {
    height: 39px;
    padding: 0 12px;
    border: 1px solid #dfe2e6;
    border-radius: 10px;
    font-size: 10px;
}

#lgIncomingParcels .intake-search input:focus,
#lgIncomingParcels .intake-receive-form textarea:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217, 149, 11, .09);
}

/* FILTERS */

#lgIncomingParcels .intake-filter-row {
    gap: 10px;
    padding: 0 17px 13px;
}

#lgIncomingParcels .intake-tabs {
    gap: 4px;
    padding: 4px;
    border: 1px solid #e6e1d9;
    border-radius: 10px;
    background: #f7f8fa;
}

#lgIncomingParcels .intake-tabs button {
    min-height: 30px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgIncomingParcels .intake-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: #fff;
    color: #a36c0a;
    box-shadow: 0 2px 5px rgba(35, 29, 22, .035);
}

#lgIncomingParcels .intake-tabs span {
    min-width: 21px;
    height: 19px;
    font-size: 8px;
}

#lgIncomingParcels .intake-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
}

/* DIRECTORY TABLE */

#lgIncomingParcels .intake-table-scroll {
    max-width: 100%;
    overflow-x: auto;
}

#lgIncomingParcels .intake-table {
    width: 100%;
    min-width: 1020px;
    table-layout: fixed;
    font-size: 10px;
}

#lgIncomingParcels .intake-table th:nth-child(1) {
    width: 21%;
}

#lgIncomingParcels .intake-table th:nth-child(2) {
    width: 15%;
}

#lgIncomingParcels .intake-table th:nth-child(3) {
    width: 16%;
}

#lgIncomingParcels .intake-table th:nth-child(4) {
    width: 13%;
}

#lgIncomingParcels .intake-table th:nth-child(5) {
    width: 14%;
}

#lgIncomingParcels .intake-table th:nth-child(6) {
    width: 21%;
}

#lgIncomingParcels .intake-table thead {
    background: #f7f8fa;
}

#lgIncomingParcels .intake-table th {
    padding: 12px 11px;
    color: #81776d;
    font-size: 8.5px;
    letter-spacing: .05em;
}

#lgIncomingParcels .intake-table td {
    min-width: 0;
    padding: 13px 11px;
    font-size: 10px;
    line-height: 1.55;
    vertical-align: top;
    overflow-wrap: anywhere;
}

#lgIncomingParcels .intake-table td strong {
    color: #443c33;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgIncomingParcels .intake-table td p {
    margin-top: 4px;
    color: #91887e;
    font-size: 9px;
    line-height: 1.55;
    overflow-wrap: anywhere;
}

#lgIncomingParcels .intake-table tbody tr:hover {
    background: #fffaf2;
}

/* PICKUP RIDER */

#lgIncomingParcels .intake-rider {
    min-width: 0;
    align-items: flex-start;
    gap: 8px;
}

#lgIncomingParcels .intake-rider > div {
    min-width: 0;
}

#lgIncomingParcels .intake-rider-avatar {
    display: grid;
    place-items: center;
    flex: 0 0 34px;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 700;
}

/* ROUTE */

#lgIncomingParcels .intake-route {
    flex-wrap: wrap;
    min-width: 0;
    gap: 4px;
}

#lgIncomingParcels .intake-route span {
    font-size: 8px;
    letter-spacing: .03em;
}

#lgIncomingParcels .intake-route-destination {
    max-width: 100%;
    font-size: 9px !important;
}

/* BADGES */

#lgIncomingParcels .intake-badge {
    min-height: 24px;
    padding: 5px 9px;
    font-size: 8.5px;
    font-weight: 650;
}

/* PARCEL DETAILS */

#lgIncomingParcels .intake-details {
    max-width: 100%;
    margin-top: 9px;
}

#lgIncomingParcels .intake-details summary {
    max-width: 100%;
    gap: 7px;
    font-size: 9px;
    font-weight: 650;
}

#lgIncomingParcels .intake-detail-body {
    width: 100%;
    min-width: 0;
    margin-top: 8px;
    padding: 10px;
    border: 1px solid #e8e2d9;
    border-radius: 9px;
    background: #fff;
}

#lgIncomingParcels .intake-detail-grid {
    grid-template-columns: 1fr;
    gap: 10px;
}

#lgIncomingParcels .intake-detail-grid strong {
    font-size: 9px !important;
}

#lgIncomingParcels .intake-detail-grid p {
    font-size: 9px !important;
}

#lgIncomingParcels .intake-label {
    margin-bottom: 5px;
    font-size: 8px;
    letter-spacing: .06em;
}

#lgIncomingParcels .intake-products li {
    gap: 8px;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

/* INTAKE CONTROLS */

#lgIncomingParcels .intake-receive-form,
#lgIncomingParcels .intake-received-info {
    width: 100%;
    max-width: none;
}

#lgIncomingParcels .intake-receive-form {
    margin-top: 10px;
}

#lgIncomingParcels .intake-receive-form textarea {
    min-height: 65px;
    padding: 9px 10px;
    border: 1px solid #dfe2e6;
    border-radius: 9px;
    background: #fff;
    font-size: 9px;
    line-height: 1.5;
}

#lgIncomingParcels .intake-receive-form .intake-button {
    min-height: 36px;
    margin-top: 8px;
    padding: 7px;
    width: 100%;
    font-size: 9px;
    white-space: normal;
    line-height: 1.4;
}

/* RECEIVED STATE */

#lgIncomingParcels .intake-received-info {
    margin: 10px 0 9px;
    padding: 11px;
    border: 1px solid #e8e2d9;
    border-radius: 9px;
    background: #fbfbfc;
}

#lgIncomingParcels .intake-received-info strong {
    color: #51483e !important;
    font-size: 9px !important;
}

#lgIncomingParcels .intake-received-info p {
    color: #897f74 !important;
    font-size: 9px !important;
}

#lgIncomingParcels .intake-received-info .intake-note {
    color: #82776a !important;
    border-top-color: #e8e2d9;
}

#lgIncomingParcels .intake-status-note {
    max-width: none;
    font-size: 9px !important;
}

/* EMPTY STATES AND FOOTER */

#lgIncomingParcels .intake-empty {
    padding: 40px 20px;
}

#lgIncomingParcels .intake-empty-icon {
    width: 43px;
    height: 43px;
    border-radius: 12px;
    background: #fff7e8;
}

#lgIncomingParcels .intake-empty h3 {
    font-size: 11px;
}

#lgIncomingParcels .intake-empty p {
    font-size: 9px;
    line-height: 1.6;
}

#lgIncomingParcels .intake-list-footer {
    padding: 12px 16px;
    background: #fff;
    font-size: 9px;
}

/* HOVER ANIMATION */

@media (hover: hover) and (pointer: fine) {
    #lgIncomingParcels .intake-stat:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43, 33, 21, .06);
    }
}

/* RESPONSIVE */

@media (max-width: 900px) {
    #lgIncomingParcels .intake-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    #lgIncomingParcels .intake-page-header,
    #lgIncomingParcels .intake-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgIncomingParcels .intake-filter-row {
        flex-direction: column;
        align-items: stretch;
        padding: 0 14px 13px;
    }

    #lgIncomingParcels .intake-search {
        width: 100%;
    }

    #lgIncomingParcels .intake-header-actions {
        width: 100%;
    }

    #lgIncomingParcels .intake-header-actions .intake-button {
        flex: 1;
    }
}

@media (max-width: 520px) {
    #lgIncomingParcels .intake-header-actions {
        flex-direction: column;
    }

    #lgIncomingParcels .intake-stat {
        padding: 12px;
    }

    #lgIncomingParcels .intake-stat strong {
        font-size: 22px;
    }

    #lgIncomingParcels .intake-stat .intake-stat-icon {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
    }

    #lgIncomingParcels .intake-detail-grid {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion: reduce) {
    #lgIncomingParcels .intake-stat:hover {
        transform: none !important;
    }
}

/* ============================================================
   SARI LOGISTICS — INCOMING PARCELS DESIGN PARITY
   No shared CSS, routes, models or server side code are changed.
   ============================================================ */
#lgIncomingParcels {
    --intake-ink: #222d3b;
    --intake-line: #e4e9ef;
    max-width: 1620px;
    font-size: 12px;
}
#lgIncomingParcels .intake-page-header {
    margin: 0 0 19px;
    padding: 2px 1px 13px;
    border: 0;
    background: transparent;
    box-shadow: none;
}
#lgIncomingParcels .intake-page-header::before,
#lgIncomingParcels .intake-page-header::after { display: none !important; }
#lgIncomingParcels .intake-page-header h1 {font-size: clamp(26px,2.15vw,32px); color:#202b39;}
#lgIncomingParcels .intake-page-header .intake-eyebrow { color: #ae750d; font-size: 11px; }
#lgIncomingParcels .intake-header-actions {gap:9px;}
#lgIncomingParcels .intake-page-header .intake-button:not(.intake-button-primary) {
    border-color:#dfe5eb; color:#526174; background:#fff;
}
#lgIncomingParcels .intake-page-header .intake-button:not(.intake-button-primary):hover {background:#f3f5f7; border-color:#cdd7df; color:#354355;}
#lgIncomingParcels .intake-page-header .intake-button {font-size:12px; min-height:42px;}
#lgIncomingParcels .intake-stats {gap:14px; margin-bottom:18px;}
#lgIncomingParcels .intake-stat {
    min-height:106px; padding:17px 18px; border:1px solid #e6ebf0; border-radius:19px;
    background:#fff; box-shadow:0 10px 27px rgba(15,23,42,.07); gap:10px;
}
#lgIncomingParcels .intake-stat::after {display:none!important;}
#lgIncomingParcels .intake-stat p {font-size:12px; color:#626e7d;}
#lgIncomingParcels .intake-stat strong,
#lgIncomingParcels .intake-stat-amber strong,
#lgIncomingParcels .intake-stat-green strong,
#lgIncomingParcels .intake-stat-blue strong {font-size:29px; line-height:1.1; color:#1f2937;}
#lgIncomingParcels .intake-stat span:not(.intake-stat-icon) {font-size:10px; color:#919cab;}
/* ICONS ONLY, NO COLORED ICON BACKGROUND */
#lgIncomingParcels .intake-stat .intake-stat-icon {
    width:34px; height:34px; flex:0 0 34px; padding:0;
    background:transparent!important; border:0!important;
    box-shadow:none!important; border-radius:0!important;
    color:#8b72b3;
}
#lgIncomingParcels .intake-stat-amber .intake-stat-icon {color:#c48a20;}
#lgIncomingParcels .intake-stat-green .intake-stat-icon {color:#398660;}
#lgIncomingParcels .intake-stat-blue .intake-stat-icon {color:#4a83b8;}
#lgIncomingParcels .intake-stat .intake-stat-icon svg {height:30px; width:30px; display:block;}
#lgIncomingParcels .intake-directory-controls {
    display:grid; grid-template-columns:minmax(0,1fr) minmax(160px,220px) auto auto;
    align-items:center; gap:12px; margin-bottom:15px;
}
#lgIncomingParcels .intake-directory-search {
    display:flex; align-items:center; position:relative; min-width:0; width:100%; height:48px;
    background:#fff; border:1px solid #dfe5eb; border-radius:13px;
}
#lgIncomingParcels .intake-directory-search > svg {position:absolute; left:16px; width:18px; height:18px; color:#98a6b5; pointer-events:none;}
#lgIncomingParcels .intake-directory-search input {
    width:100%; height:100%; padding:0 14px 0 46px; border:0!important; border-radius:13px;
    background:transparent!important; box-shadow:none!important; outline:0!important; color:#334155; font-size:12px;
}
#lgIncomingParcels .intake-directory-search:focus-within {border-color:#aab6c4; box-shadow:0 0 0 3px rgba(148,163,184,.12);}
#lgIncomingParcels .intake-status-select select {
    width:100%; height:48px; padding:0 14px; border:1px solid #dfe5eb; border-radius:13px;
    background:#fff; color:#334155; font-size:12px; outline:0;
}
#lgIncomingParcels .intake-status-select select:focus {border-color:#aab6c4; box-shadow:0 0 0 3px rgba(148,163,184,.12);}
#lgIncomingParcels .intake-apply-filter {
    display:inline-flex; gap:9px; align-items:center; justify-content:center;
    min-height:48px; padding:0 21px; border-radius:13px; font-size:12px; white-space:nowrap;
    border-color:#d9950b; background:#d9950b; color:#fff;
}
#lgIncomingParcels .intake-apply-filter svg {width:16px;height:16px;}
#lgIncomingParcels .intake-reset-filter {
    height:48px; min-width:92px; padding:0 17px; border:1px solid #dfe5eb;
    border-radius:13px; color:#526174; background:#fff; font-size:12px; font-weight:600;
}
#lgIncomingParcels .intake-reset-filter:hover {background:#f4f6f8;}
#lgIncomingParcels .intake-workspace {
    border:1px solid #e4eaf0; border-radius:21px; background:#fff;
    box-shadow:0 10px 32px rgba(15,23,42,.07); overflow:hidden;
}
#lgIncomingParcels .intake-toolbar.intake-directory-heading {
    display:flex; align-items:center; justify-content:space-between; gap:12px; padding:19px 22px;
    border-bottom:1px solid #edf0f3;
}
#lgIncomingParcels .intake-toolbar h2 {margin:0; color:#1f2937; font-size:19px; font-weight:750;}
#lgIncomingParcels .intake-directory-count {
    flex:0 0 auto; padding:7px 12px; border:1px solid #e4eaf0; border-radius:11px;
    background:#f8fafc; color:#68798b; font-size:10px; font-weight:700;
}
#lgIncomingParcels .intake-table-scroll {overflow:visible; max-width:100%;}
#lgIncomingParcels .intake-table {width:100%; min-width:0; table-layout:fixed; border-collapse:collapse;}
#lgIncomingParcels .intake-table thead {background:#f5f7f9;}
#lgIncomingParcels .intake-table th {padding:13px 12px; color:#718093; font-size:10px; letter-spacing:.07em; white-space:normal;}
#lgIncomingParcels .intake-table td {min-width:0; padding:15px 12px; font-size:11px; color:#4c5866; overflow-wrap:anywhere;}
#lgIncomingParcels .intake-table td strong {font-size:11px; color:#253244;}
#lgIncomingParcels .intake-table td p {font-size:10px; line-height:1.55; color:#7e8b9b;}
#lgIncomingParcels .intake-table th:nth-child(1) {width:21%;}
#lgIncomingParcels .intake-table th:nth-child(2) {width:16%;}
#lgIncomingParcels .intake-table th:nth-child(3) {width:17%;}
#lgIncomingParcels .intake-table th:nth-child(4) {width:15%;}
#lgIncomingParcels .intake-table th:nth-child(5) {width:14%;}
#lgIncomingParcels .intake-table th:nth-child(6) {width:17%;}
#lgIncomingParcels .intake-table tbody tr:hover {background:#f2f4f6!important; box-shadow:none!important;}
#lgIncomingParcels .intake-table tbody tr:hover td {border-color:#e8edf2;}
#lgIncomingParcels .intake-route {gap:4px;}
#lgIncomingParcels .intake-rider {gap:9px;}
#lgIncomingParcels .intake-rider-avatar {background:#edf2f6; color:#64748b; border-color:#e2e8ee;}
#lgIncomingParcels .intake-receive-form textarea {width:100%; font-size:11px; border:1px solid #dfe5eb; border-radius:10px; padding:10px;}
#lgIncomingParcels .intake-receive-form .intake-button {min-height:39px; font-size:10px; border-radius:10px;}
#lgIncomingParcels .intake-detail-body {background:#f9fafb; border-color:#e5ebf1;}
#lgIncomingParcels .intake-detail-grid strong,
#lgIncomingParcels .intake-detail-grid p,
#lgIncomingParcels .intake-products li {font-size:10px!important;}
#lgIncomingParcels .intake-empty {padding:55px 20px 49px; text-align:center;}
#lgIncomingParcels .intake-empty-icon {
    display:flex; width:92px; height:92px; margin:0 auto 12px; align-items:center; justify-content:center;
    padding:0; border:0!important; border-radius:0!important; background:transparent!important; color:#aeb7c1!important;
    box-shadow:none!important; font-size:0;
}
#lgIncomingParcels .intake-empty-icon svg {width:58px;height:58px;}
#lgIncomingParcels .intake-empty h3 {margin:0 0 7px; color:#899aaa; font-size:22px; font-weight:750; line-height:1.3;}
#lgIncomingParcels .intake-empty p {max-width:550px; margin:0 auto 17px; color:#93a2b3; font-size:12px; line-height:1.7;}
#lgIncomingParcels .intake-empty .intake-button-primary {padding:12px 22px; min-height:44px; font-size:12px; border-radius:12px; box-shadow:0 8px 17px rgba(217,149,11,.16);}
#lgIncomingParcels .intake-empty .intake-text-link {font-size:12px;}
#lgIncomingParcels .intake-list-footer {
    min-height:60px; padding:13px 21px; display:flex; align-items:center; gap:15px;
    justify-content:space-between; font-size:11px; color:#7d8998; background:#fff;
}
#lgIncomingParcels .intake-page-size-label {display:flex; align-items:center; gap:10px; font-size:11px; white-space:nowrap;}
#lgIncomingParcels .intake-page-size-label select {height:35px; padding:0 9px; border:1px solid #dfe5eb; border-radius:10px; background:#fff; color:#374151; font-size:12px;}
#lgIncomingParcels .intake-pagination {display:flex; align-items:center; justify-content:center; flex-wrap:wrap; gap:7px; min-height:49px; margin:15px auto 0;}
#lgIncomingParcels .intake-pagination button {
    height:35px; min-width:35px; padding:0 10px; background:#f6f7f9; border:1px solid transparent;
    border-radius:10px; color:#8492a5; font-size:12px; font-weight:650;
}
#lgIncomingParcels .intake-pagination button:hover:not(:disabled) {background:#e9edf1; color:#334155;}
#lgIncomingParcels .intake-pagination button[aria-current="page"] {background:#d09521; border-color:#d09521; color:#fff; box-shadow:0 6px 13px rgba(208,149,33,.15);}
#lgIncomingParcels .intake-pagination button:disabled {opacity:.36;cursor:not-allowed;}
@media (hover:hover) and (pointer:fine) {
  #lgIncomingParcels .intake-stat:hover {transform:translateY(-1px);border-color:#e3e8ed;box-shadow:0 13px 30px rgba(15,23,42,.075);}
}
@media (max-width:1050px) {
  #lgIncomingParcels .intake-table th, #lgIncomingParcels .intake-table td {padding-left:9px;padding-right:9px;}
  #lgIncomingParcels .intake-table td {font-size:10px;}
  #lgIncomingParcels .intake-directory-controls {grid-template-columns:minmax(0,1fr) minmax(150px,210px) auto auto; gap:9px;}
}
@media (max-width:900px) {
  #lgIncomingParcels .intake-directory-controls {grid-template-columns:1fr 1fr;}
  #lgIncomingParcels .intake-directory-search {grid-column:1/-1;}
  #lgIncomingParcels .intake-table thead {display:none;}
  #lgIncomingParcels .intake-table, #lgIncomingParcels .intake-table tbody {display:block; width:100%;}
  #lgIncomingParcels .intake-table tbody {padding:12px;}
  #lgIncomingParcels .intake-table tbody tr {display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:0 12px; border:1px solid #e5eaf0; border-radius:15px; margin-bottom:12px; padding:6px 12px; box-shadow:0 4px 12px rgba(15,23,42,.025);}
  #lgIncomingParcels .intake-table tbody tr:last-child {margin-bottom:0;}
  #lgIncomingParcels .intake-table tbody tr[hidden] {display:none!important;}
  #lgIncomingParcels .intake-table td {display:block; min-width:0; width:100%; border-bottom:1px solid #f0f2f5; padding:12px 0;}
  #lgIncomingParcels .intake-table td:before {content:attr(data-label);display:block;margin-bottom:6px;color:#93a0af;font-weight:700;font-size:10px;letter-spacing:.055em;text-transform:uppercase;}
  #lgIncomingParcels .intake-table td:first-child, #lgIncomingParcels .intake-table td:last-child {grid-column:1/-1;}
  #lgIncomingParcels .intake-table td:last-child {border:0;}
  #lgIncomingParcels .intake-receive-form {max-width:470px;}
  #lgIncomingParcels .intake-receive-form .intake-button {width:auto;padding:9px 19px;}
}
@media (max-width:600px) {
  #lgIncomingParcels .intake-page-header {align-items:stretch; gap:12px;}
  #lgIncomingParcels .intake-header-actions {display:flex; width:100%;}
  #lgIncomingParcels .intake-header-actions .intake-button {flex:1;}
  #lgIncomingParcels .intake-stats {gap:10px;}
  #lgIncomingParcels .intake-stat {min-height:110px;padding:13px;}
  #lgIncomingParcels .intake-stat p {font-size:11px;}
  #lgIncomingParcels .intake-stat strong, #lgIncomingParcels .intake-stat-amber strong, #lgIncomingParcels .intake-stat-green strong, #lgIncomingParcels .intake-stat-blue strong {font-size:23px;}
  #lgIncomingParcels .intake-stat span:not(.intake-stat-icon) {font-size:9px;}
  #lgIncomingParcels .intake-stat .intake-stat-icon {width:27px;height:27px;flex-basis:27px;}
  #lgIncomingParcels .intake-stat .intake-stat-icon svg {width:26px;height:26px;}
  #lgIncomingParcels .intake-directory-controls {grid-template-columns:minmax(0,1fr) auto; gap:9px;}
  #lgIncomingParcels .intake-status-select {grid-column:1/-1;}
  #lgIncomingParcels .intake-apply-filter, #lgIncomingParcels .intake-reset-filter {width:100%;}
  #lgIncomingParcels .intake-apply-filter {grid-column:1;}
  #lgIncomingParcels .intake-reset-filter {grid-column:2;}
  #lgIncomingParcels .intake-toolbar.intake-directory-heading {padding:15px 14px;}
  #lgIncomingParcels .intake-toolbar h2 {font-size:16px;}
  #lgIncomingParcels .intake-table tbody {padding:10px;}
  #lgIncomingParcels .intake-table tbody tr {grid-template-columns:1fr;}
  #lgIncomingParcels .intake-table td:first-child, #lgIncomingParcels .intake-table td:last-child {grid-column:1;}
  #lgIncomingParcels .intake-receive-form .intake-button {width:100%;}
  #lgIncomingParcels .intake-empty {padding:45px 15px;}
  #lgIncomingParcels .intake-empty-icon {width:64px;height:64px;}
  #lgIncomingParcels .intake-empty-icon svg {width:40px;height:40px;}
  #lgIncomingParcels .intake-empty h3 {font-size:20px;}
  #lgIncomingParcels .intake-empty p {font-size:11px;}
  #lgIncomingParcels .intake-list-footer {padding:12px 14px;flex-wrap:wrap;}
}
@media (prefers-reduced-motion:reduce) {
  #lgIncomingParcels .intake-stat:hover {transform:none!important;}
}



/* ============================================================
   V2 RESILIENT LAYOUT FIX — NOT DEPENDENT ON incoming-parcels.css
   The referenced legacy CSS is not present in the uploaded codebase.
   These page-only structural declarations remain valid if that file
   is restored, and also when buyer-parity.css is loaded afterward.
   ============================================================ */
#lgIncomingParcels,
#lgIncomingParcels *,
#lgIncomingParcels *::before,
#lgIncomingParcels *::after { box-sizing: border-box; }
#lgIncomingParcels { width:100%; min-width:0; margin-inline:auto; padding-bottom:24px; }
#lgIncomingParcels [hidden] { display:none!important; }

/* Header: keep navigation actions next to the heading on wide screens. */
#lgIncomingParcels .intake-page-header {
    display:flex; flex-direction:row; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:14px 20px; width:100%; min-width:0;
}
#lgIncomingParcels .intake-page-header > div:first-child {flex:1 1 280px;min-width:0;}
#lgIncomingParcels .intake-eyebrow {margin:0 0 4px;line-height:1.3;}
#lgIncomingParcels .intake-page-header h1 {margin:0;line-height:1.15;}
#lgIncomingParcels .intake-header-actions {
    display:flex; align-items:center; justify-content:flex-end;
    flex-direction:row; flex-wrap:wrap; gap:9px; margin-left:auto;
}
#lgIncomingParcels a.intake-button,
#lgIncomingParcels button.intake-button {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    width:auto; min-height:40px; padding:9px 14px;
    border:1px solid #dfe5eb; border-radius:11px; background:#fff;
    font-family:inherit; font-size:11px; font-weight:650; line-height:1.25;
    text-decoration:none; white-space:nowrap; cursor:pointer;
}
#lgIncomingParcels a.intake-button-primary,
#lgIncomingParcels button.intake-button-primary {
    border-color:#d9950b; background:#d9950b; color:white;
}
#lgIncomingParcels a.intake-button-primary:hover,
#lgIncomingParcels button.intake-button-primary:hover {
    border-color:#c88708; background:#c88708; color:white;
}

/* Four compact KPI cards, not one full-width card per row. */
#logisticsContent #lgIncomingParcels .intake-stats,
#lgIncomingParcels .intake-stats {
    display:grid!important;
    grid-template-columns:repeat(4,minmax(0,1fr));
    align-items:stretch; gap:13px; width:100%; min-width:0;
}
#logisticsContent #lgIncomingParcels .intake-stat,
#lgIncomingParcels .intake-stat {
    display:flex; align-items:flex-start; justify-content:space-between;
    flex-direction:row; gap:12px; min-width:0; min-height:108px;
    padding:15px 17px;
}
#lgIncomingParcels .intake-stat > div {min-width:0;flex:1 1 auto;}
#lgIncomingParcels .intake-stat p {margin:0;line-height:1.4;}
#lgIncomingParcels .intake-stat strong,
#lgIncomingParcels .intake-stat-amber strong,
#lgIncomingParcels .intake-stat-green strong,
#lgIncomingParcels .intake-stat-blue strong {
    display:block; margin:6px 0 4px; font-size:27px; line-height:1.1;
}
#lgIncomingParcels .intake-stat span:not(.intake-stat-icon) {display:block;line-height:1.45;}
#lgIncomingParcels .intake-stat .intake-stat-icon {
    display:grid; place-items:center; flex:0 0 34px; width:34px;height:34px;
    border:0!important; border-radius:0!important;
    background:transparent!important; box-shadow:none!important;
}
#lgIncomingParcels .intake-stat .intake-stat-icon svg {display:block;width:30px;height:30px;}

/* Independent search bar and table container. */
#lgIncomingParcels .intake-directory-controls {width:100%;min-width:0;}
#lgIncomingParcels :is(input,textarea,select,button) {font-family:inherit;}
#lgIncomingParcels .intake-table-scroll {min-width:0;width:100%;}
#lgIncomingParcels .intake-table {
    width:100%; border-collapse:collapse; border-spacing:0; text-align:left;
}
#lgIncomingParcels .intake-table th {
    text-align:left; font-weight:700; line-height:1.45;
    border-bottom:1px solid #e5ebf1;
}
#lgIncomingParcels .intake-table td {
    vertical-align:top; border-bottom:1px solid #edf0f3; line-height:1.5;
}
#lgIncomingParcels .intake-table tbody tr:last-child td {border-bottom:0;}
#lgIncomingParcels .intake-table td strong {display:block;font-weight:650;}
#lgIncomingParcels .intake-table td p {margin:4px 0 0;}
#lgIncomingParcels .intake-rider {display:flex;align-items:flex-start;gap:9px;min-width:0;}
#lgIncomingParcels .intake-rider > div {min-width:0;overflow-wrap:anywhere;}
#lgIncomingParcels .intake-rider-avatar {
    display:grid;place-items:center;flex:0 0 34px;width:34px;height:34px;
    border:1px solid #e2e8ee;border-radius:10px;
}
#lgIncomingParcels .intake-route {display:flex;align-items:center;flex-wrap:wrap;gap:5px;}
#lgIncomingParcels .intake-badge {
    display:inline-flex; align-items:center; min-height:25px; max-width:100%;
    padding:5px 9px; border:1px solid #dce5ee; border-radius:999px;
    color:#607086; background:#f4f7fa; font-size:10px; font-weight:650;
    line-height:1.3;
}
#lgIncomingParcels .intake-badge-waiting {color:#9c741e;background:#fff9ec;border-color:#efe3c6;}
#lgIncomingParcels .intake-badge-received {color:#39805a;background:#f1faf4;border-color:#d2e9da;}
#lgIncomingParcels .intake-badge-active {color:#587894;background:#f0f6fc;border-color:#d9e6f1;}
#lgIncomingParcels .intake-receive-form {width:100%;margin-top:10px;}
#lgIncomingParcels .intake-receive-form textarea {display:block;resize:vertical;width:100%;}
#lgIncomingParcels .intake-receive-form .intake-button {white-space:normal;}
#lgIncomingParcels .intake-details {margin-top:8px;}
#lgIncomingParcels .intake-details summary {display:inline-flex;align-items:center;gap:7px;cursor:pointer;list-style:none;color:#a66d0b;}
#lgIncomingParcels .intake-details summary::-webkit-details-marker {display:none;}
#lgIncomingParcels .intake-details summary > span {display:inline-block;}
#lgIncomingParcels .intake-details[open] summary > span {transform:rotate(45deg);}
#lgIncomingParcels .intake-detail-body {min-width:0;margin-top:8px;overflow-wrap:anywhere;}
#lgIncomingParcels .intake-detail-grid {display:grid;gap:10px;}
#lgIncomingParcels .intake-label {display:block;font-weight:650;text-transform:uppercase;}
#lgIncomingParcels .intake-products {list-style:none;margin:10px 0 0;padding:8px 0 0;border-top:1px solid #e5ebf1;}
#lgIncomingParcels .intake-products li {display:flex;justify-content:space-between;gap:10px;padding:5px 0;}
#lgIncomingParcels .intake-text-link {display:inline-flex;gap:5px;align-items:center;margin-top:7px;border:0;background:transparent;color:#b47b11;font-weight:650;text-decoration:none;cursor:pointer;}
#lgIncomingParcels .intake-text-link:hover {text-decoration:underline;}
#lgIncomingParcels .intake-received-info {min-width:0;overflow-wrap:anywhere;}
#lgIncomingParcels .intake-received-info strong {display:block;}
#lgIncomingParcels .intake-received-info p {margin:4px 0;}
#lgIncomingParcels .intake-status-note {line-height:1.6;}
#lgIncomingParcels .intake-empty {text-align:center;}
#lgIncomingParcels .intake-pagination:empty {display:none;}
#lgIncomingParcels .intake-page-size-label select {max-width:100%;}
#lgIncomingParcels .intake-alert {margin:0 0 14px;padding:14px 16px;border:1px solid #e4eaf0;border-radius:12px;background:#fff;}
#lgIncomingParcels .intake-alert-success {border-color:#cbe4d3;background:#f2faf4;color:#397553;}
#lgIncomingParcels .intake-alert-danger {border-color:#eecbc8;background:#fff5f3;color:#ad514b;}

@media (max-width:1000px) {
  #logisticsContent #lgIncomingParcels .intake-stats,
  #lgIncomingParcels .intake-stats {grid-template-columns:repeat(2,minmax(0,1fr));}
}
@media (max-width:680px) {
  #lgIncomingParcels .intake-page-header {gap:12px;}
  #lgIncomingParcels .intake-page-header > div:first-child {flex-basis:100%;}
  #lgIncomingParcels .intake-header-actions {width:100%;margin-left:0;justify-content:flex-start;}
  #lgIncomingParcels .intake-header-actions .intake-button {flex:1 1 auto;min-width:0;font-size:10px;}
  #logisticsContent #lgIncomingParcels .intake-stats,
  #lgIncomingParcels .intake-stats {grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
  #logisticsContent #lgIncomingParcels .intake-stat,
  #lgIncomingParcels .intake-stat {min-height:106px;padding:12px;}
  #lgIncomingParcels .intake-stat strong {font-size:23px;}
  #lgIncomingParcels .intake-stat .intake-stat-icon {flex-basis:26px;width:26px;height:26px;}
  #lgIncomingParcels .intake-stat .intake-stat-icon svg {width:25px;height:25px;}
}
@media (max-width:350px) {
  #lgIncomingParcels .intake-header-actions {gap:6px;}
  #lgIncomingParcels .intake-header-actions .intake-button {padding:8px;font-size:9px;}
  #lgIncomingParcels .intake-directory-controls {gap:8px;}
  #lgIncomingParcels .intake-stat p {font-size:10px;}
}


/* ============================================================
   V3 COMPACT INTAKE TABLE + REUSABLE DIALOGS
   Page-scoped: no backend changes, no extra CSS dependencies.
   ============================================================ */
#lgIncomingParcels .intake-table {
    width:100%; min-width:0; table-layout:fixed; border-collapse:collapse;
}
#lgIncomingParcels .intake-table th:nth-child(1) {width:29%;}
#lgIncomingParcels .intake-table th:nth-child(2) {width:36%;}
#lgIncomingParcels .intake-table th:nth-child(3) {width:17%;}
#lgIncomingParcels .intake-table th:nth-child(4) {width:18%;}
#lgIncomingParcels .intake-table th:nth-child(n+5) {width:auto;}
#lgIncomingParcels .intake-table td {vertical-align:middle; padding:14px 14px;}
#lgIncomingParcels .intake-table th {padding:13px 14px;}
#lgIncomingParcels .intake-table tbody tr:hover {background:#f2f4f6!important;box-shadow:none!important;}
#lgIncomingParcels .intake-table .intake-order-number {font-size:12px;line-height:1.45;font-weight:750;color:#263244;}
#lgIncomingParcels .intake-table .intake-primary-text {font-size:11px;color:#657285;}
#lgIncomingParcels .intake-table .intake-small-detail {margin-top:4px;color:#93a0ae;font-size:10px;}
#lgIncomingParcels .intake-compact-route {display:flex;align-items:center;gap:7px;min-width:0;color:#435165;font-size:11px;font-weight:650;line-height:1.55;}
#lgIncomingParcels .intake-route-point {min-width:0;overflow-wrap:anywhere;}
#lgIncomingParcels .intake-compact-route svg {width:15px;height:15px;flex:0 0 15px;color:#94a3b8;}
#lgIncomingParcels .intake-badge {gap:6px;font-size:10px;max-width:100%;}
#lgIncomingParcels .intake-status-dot {width:7px;height:7px;border-radius:50%;background:currentColor;flex:0 0 7px;}
#lgIncomingParcels .intake-row-actions {display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
#lgIncomingParcels .intake-view-icon {display:grid;place-items:center;width:32px;height:34px;padding:4px;border:0;border-radius:8px;background:transparent;color:#64748b;cursor:pointer;}
#lgIncomingParcels .intake-view-icon:hover {background:#e9edf2;color:#243244;}
#lgIncomingParcels .intake-view-icon svg {width:19px;height:19px;}
#lgIncomingParcels .intake-row-receive {display:inline-flex;align-items:center;justify-content:center;min-height:35px;padding:7px 13px;border:1px solid #d9950b;border-radius:9px;background:#d9950b;color:#fff;font-size:11px;font-weight:700;cursor:pointer;box-shadow:0 5px 12px rgba(217,149,11,.12);}
#lgIncomingParcels .intake-row-receive:hover {background:#c88708;border-color:#c88708;}
#lgIncomingParcels .intake-view-text {padding:6px 0;border:0;background:transparent;color:#a67520;font-size:11px;font-weight:650;cursor:pointer;}
#lgIncomingParcels .intake-view-text:hover {text-decoration:underline;}
#lgIncomingParcels .intake-table td > template {display:none;}

/* Dialog overlay and central panel: no nested floating card or extra radius. */
#lgIncomingParcels .intake-modal-overlay {position:fixed!important;inset:0!important;z-index:99999!important;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(18,26,39,.54);overflow-y:auto;}
#lgIncomingParcels .intake-modal-overlay[hidden] {display:none!important;}
#lgIncomingParcels .intake-modal-panel {width:min(670px,100%);max-height:min(88dvh,850px);display:flex;flex-direction:column;overflow:hidden;background:#fff!important;border:1px solid #e2e8ef;border-radius:20px!important;box-shadow:0 24px 70px rgba(15,23,42,.23)!important;color:#263244;}
#lgIncomingParcels .intake-modal-panel-small {width:min(525px,100%);}
#lgIncomingParcels .intake-modal-heading {display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:22px 24px 18px;background:#fff;border-bottom:1px solid #edf0f4;}
#lgIncomingParcels .intake-modal-heading h2 {margin:4px 0 0;font-size:24px;line-height:1.25;letter-spacing:-.035em;font-weight:750;color:#263244;}
#lgIncomingParcels .intake-modal-heading p {margin:5px 0 0;font-size:12px;line-height:1.6;color:#8391a2;overflow-wrap:anywhere;}
#lgIncomingParcels .intake-modal-eyebrow {font-size:10px;font-weight:750;letter-spacing:.12em;color:#ba811a;}
#lgIncomingParcels .intake-modal-close {display:grid;place-items:center;flex:0 0 36px;width:36px;height:36px;padding:0;background:#fff;border:1px solid #dfe6ee;border-radius:10px;font-size:24px;line-height:1;color:#64748b;cursor:pointer;}
#lgIncomingParcels .intake-modal-close:hover {background:#f1f4f7;color:#263244;}
#lgIncomingParcels .intake-modal-content {min-height:0;overflow-y:auto;padding:22px 24px;scrollbar-width:none;background:#fff;}
#lgIncomingParcels .intake-modal-content::-webkit-scrollbar,#lgIncomingParcels .intake-modal-overlay::-webkit-scrollbar {display:none;}
#lgIncomingParcels .intake-modal-info-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
#lgIncomingParcels .intake-modal-info {min-width:0;padding:13px 15px;border:1px solid #e7ecf2;border-radius:12px;background:#f8fafc;overflow-wrap:anywhere;}
#lgIncomingParcels .intake-modal-label {display:block;margin-bottom:7px;color:#8997a7;font-size:10px;font-weight:750;letter-spacing:.065em;text-transform:uppercase;}
#lgIncomingParcels .intake-modal-info strong {display:block;font-size:12px;line-height:1.55;color:#2b394b;font-weight:700;}
#lgIncomingParcels .intake-modal-info p {margin:4px 0 0;font-size:11px;line-height:1.6;color:#667487;white-space:pre-wrap;}
#lgIncomingParcels .intake-modal-info-full {grid-column:1/-1;}
#lgIncomingParcels .intake-modal-products {margin-top:16px;border:1px solid #e7ecf2;border-radius:12px;padding:16px;}
#lgIncomingParcels .intake-modal-products h3 {margin:0;color:#29374b;font-size:13px;font-weight:750;}
#lgIncomingParcels .intake-modal-products .intake-products {margin-top:10px;padding-top:8px;}
#lgIncomingParcels .intake-modal-products .intake-products li {font-size:11px;}
#lgIncomingParcels .intake-modal-sorting-link {display:inline-block;margin-top:16px;color:#af791a;text-decoration:none;font-size:12px;font-weight:750;}
#lgIncomingParcels .intake-modal-sorting-link:hover {text-decoration:underline;}
#lgIncomingParcels .intake-modal-receive-form {display:flex;flex-direction:column;min-height:0;}
#lgIncomingParcels .intake-modal-note-label {display:block;margin:17px 0 7px;color:#465467;font-size:12px;font-weight:700;}
#lgIncomingParcels .intake-modal-note-label span {font-size:11px;color:#8a97a6;font-weight:500;}
#lgIncomingParcels .intake-modal-receive-form textarea {display:block;resize:vertical;min-height:83px;width:100%;padding:12px 13px;border:1px solid #dce4ec;border-radius:10px;background:#fff;font-size:12px;color:#243244;line-height:1.55;}
#lgIncomingParcels .intake-modal-receive-form textarea:focus {outline:2px solid rgba(100,116,139,.18);border-color:#93a1b3;box-shadow:none;}
#lgIncomingParcels .intake-modal-footer {display:flex;justify-content:flex-end;gap:10px;padding:15px 24px 20px;border-top:1px solid #edf0f4;background:#fff;}
#lgIncomingParcels .intake-modal-secondary,#lgIncomingParcels .intake-modal-confirm {min-height:40px;padding:9px 17px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;}
#lgIncomingParcels .intake-modal-secondary {background:#fff;border:1px solid #dfe5ec;color:#46546a;}
#lgIncomingParcels .intake-modal-secondary:hover {background:#f4f6f8;}
#lgIncomingParcels .intake-modal-confirm {background:#d9950b;border:1px solid #d9950b;color:#fff;box-shadow:0 6px 14px rgba(217,149,11,.14);}
#lgIncomingParcels .intake-modal-confirm:hover {background:#c88708;border-color:#c88708;}
#lgIncomingParcels .intake-modal-confirm:disabled {opacity:.55;cursor:not-allowed;}
@media(max-width:950px) {
  #lgIncomingParcels .intake-table thead {display:none;}
  #lgIncomingParcels .intake-table, #lgIncomingParcels .intake-table tbody {display:block;width:100%;}
  #lgIncomingParcels .intake-table tbody {padding:12px;}
  #lgIncomingParcels .intake-table tbody tr {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 16px;margin-bottom:12px;border:1px solid #e5eaf0;border-radius:14px;padding:6px 14px;background:#fff;}
  #lgIncomingParcels .intake-table tbody tr[hidden] {display:none!important;}
  #lgIncomingParcels .intake-table td {display:block;width:100%;padding:12px 0;border:0;border-bottom:1px solid #f0f2f5;}
  #lgIncomingParcels .intake-table td::before {content:attr(data-label);display:block;margin-bottom:7px;color:#8c9aa9;font-size:10px;letter-spacing:.05em;font-weight:750;text-transform:uppercase;}
  #lgIncomingParcels .intake-table td:first-child {grid-column:1/-1;}
  #lgIncomingParcels .intake-table td:last-child {border:0;}
  #lgIncomingParcels .intake-compact-route {flex-wrap:wrap;}
}
@media(max-width:600px) {
  #lgIncomingParcels .intake-table tbody tr {grid-template-columns:1fr;}
  #lgIncomingParcels .intake-table td:first-child,#lgIncomingParcels .intake-table td:last-child {grid-column:1;}
  #lgIncomingParcels .intake-modal-overlay {padding:10px;align-items:center;}
  #lgIncomingParcels .intake-modal-panel {max-height:92dvh;border-radius:15px!important;}
  #lgIncomingParcels .intake-modal-heading {padding:17px 16px 14px;}
  #lgIncomingParcels .intake-modal-heading h2 {font-size:20px;}
  #lgIncomingParcels .intake-modal-content {padding:15px 16px;}
  #lgIncomingParcels .intake-modal-info-grid {grid-template-columns:1fr;gap:9px;}
  #lgIncomingParcels .intake-modal-info-full {grid-column:1;}
  #lgIncomingParcels .intake-modal-footer {padding:13px 16px 16px;}
  #lgIncomingParcels .intake-row-actions {gap:10px;}
}

</style>
@endpush

@section('content')

<div
    id="lgIncomingParcels"
    class="sari-logistics-ui intake-page"
    data-intake-workspace
>

    {{-- PAGE HEADER --}}

    <header class="intake-page-header">

        <div class="min-w-0">

            <p class="intake-eyebrow">
                ORIGIN HUB OPERATIONS
            </p>

            <h1>
                Incoming <span>Parcels</span>
            </h1>


        </div>

        <div class="intake-header-actions">

            <a
                href="{{ route('logistics.delivery-assignment') }}"
                class="intake-button"
            >
                <span aria-hidden="true">←</span>
                Pickup Assignment
            </a>

            <a
                href="{{ route('logistics.parcel-sorting') }}"
                class="intake-button intake-button-primary"
            >
                Parcel Sorting
                <span aria-hidden="true">→</span>
            </a>

        </div>

    </header>

    {{-- FLASH MESSAGES --}}

    @if(session('success'))

        <div
            class="intake-alert intake-alert-success"
            role="status"
        >
            <strong>Hub intake updated</strong>
            <p>{{ session('success') }}</p>
        </div>

    @endif

    @if($errors->any())

        <div
            class="intake-alert intake-alert-danger"
            role="alert"
        >
            <strong>Could not update parcel intake</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    {{-- PAGE DATA --}}

    @php
        $waitingCount = $orders->filter(function ($order) use ($parcels) {

            $parcel = $parcels->get($order->id);

            return ! $parcel
                || $parcel->status === 'awaiting_intake';

        })->count();

        $receivedCount = $orders->filter(function ($order) use ($parcels) {

            return $parcels
                ->get($order->id)
                ?->status === 'received';

        })->count();

        $pickupRiderCount = $orders
            ->pluck('courier_email')
            ->filter()
            ->map(
                fn ($email) => mb_strtolower(
                    trim((string) $email)
                )
            )
            ->unique()
            ->count();

        $originHubCount = $orders->map(function ($order) {

            return $order
                ->deliveryRouteStops
                ->firstWhere('stop_key', 'origin_facility')
                ?->logistics_facility_id;

        })->filter()->unique()->count();

        $summaryCards = [
            [
                'Incoming Parcels',
                $orders->count(),
                'Pickup Rider handoffs',
                'neutral',
            ],
            [
                'Awaiting Intake',
                $waitingCount,
                'Needs physical confirmation',
                'amber',
            ],
            [
                'Received',
                $receivedCount,
                'Ready for sorting',
                'green',
            ],
            [
                'Pickup Riders',
                $pickupRiderCount,
                $originHubCount . ' origin '
                    . ($originHubCount === 1 ? 'hub' : 'hubs'),
                'blue',
            ],
        ];
    @endphp

    {{-- KPI CARDS --}}

    <section
        class="intake-stats"
        aria-label="Origin Hub Intake overview"
    >

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="intake-stat intake-stat-{{ $tone }}">

                <div class="min-w-0">

                    <p>{{ $label }}</p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>{{ $note }}</span>

                </div>

                <span
                    class="intake-stat-icon"
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
                                <circle cx="8" cy="8" r="3"/>
                                <circle cx="17" cy="9" r="2.5"/>
                                <path d="M2.5 20c.5-4 2.5-6 5.5-6M13 15c3.2 0 5.2 1.7 5.7 5"/>
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

    {{-- ORIGIN HUB INTAKE WORKSPACE --}}

    {{-- Search and filters remain independent from the data table. --}}
    <div class="intake-directory-controls" role="search" aria-label="Search and filter incoming parcels">
        <label class="intake-search intake-directory-search">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
            <span class="sr-only">Search incoming parcels</span>
            <input type="search" data-intake-search autocomplete="off" placeholder="Search order, Seller, Rider, or hub...">
        </label>
        <label class="intake-status-select">
            <span class="sr-only">Intake status filter</span>
            <select data-intake-status>
                <option value="all">All Status</option>
                <option value="waiting">Awaiting Intake</option>
                <option value="received">Received</option>
                <option value="advanced">In Process</option>
            </select>
        </label>
        <button type="button" class="intake-button intake-button-primary intake-apply-filter" data-intake-apply>
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M7 12h10M10 17h4"/></svg>
            Apply Filter
        </button>
        <button type="button" class="intake-reset-filter" data-intake-clear>Reset</button>
    </div>

    <section
        class="intake-workspace"
        aria-labelledby="incoming-parcels-heading"
    >

        <header class="intake-toolbar intake-directory-heading">
            <h2 id="incoming-parcels-heading">Origin Facility Intake</h2>
            <span class="intake-directory-count">{{ $orders->count() }} PARCELS</span>
        </header>

        {{-- PARCEL TABLE --}}

        <div
            class="intake-table-scroll"
            @if($orders->isEmpty()) hidden @endif
        >

            <table class="intake-table">

                <thead>
                    <tr>
                        <th scope="col">Order / Seller</th>
                        <th scope="col">Route</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $order)
                        @php
                            $parcel = $parcels->get($order->id);
                            $routeStops = $order->deliveryRouteStops->keyBy('stop_key');
                            $originFacility = $routeStops->get('origin_facility');
                            $destinationFacility = $routeStops->get('destination_facility');
                            $isWaiting = ! $parcel || $parcel->status === 'awaiting_intake';
                            $isReceived = $parcel?->status === 'received';
                            $state = $isWaiting ? 'waiting' : ($isReceived ? 'received' : 'advanced');
                            $sellerName = $order->seller?->store_name ?: $order->pickup_name ?: 'SARI Seller';
                            $originName = $originFacility?->facility?->name ?: $originFacility?->name ?: 'Origin hub pending';
                            $destinationName = $destinationFacility?->facility?->name ?: $destinationFacility?->name ?: 'Destination pending';
                            $searchText = implode(' ', array_filter([
                                $order->order_number, $sellerName, $order->buyer_name,
                                $order->courier_name, $order->courier_email,
                                $order->pickup_address, $originName, $destinationName,
                            ]));
                        @endphp

                        <tr data-intake-row
                            data-state="{{ $state }}"
                            data-search="{{ $searchText }}"
                            data-order-number="{{ $order->order_number }}"
                            data-seller-name="{{ $sellerName }}"
                            data-rider-name="{{ $order->courier_name ?: 'Pickup Rider' }}"
                            data-origin-name="{{ $originName }}"
                            @if($isWaiting)
                                data-receive-url="{{ route('logistics.incoming-parcels.receive', $order) }}"
                            @endif
                        >
                            <td data-label="Order / Seller">
                                <strong class="intake-order-number">{{ $order->order_number }}</strong>
                                <p class="intake-primary-text">{{ $sellerName }}</p>
                                <p class="intake-small-detail">{{ $order->buyer_name }}</p>
                            </td>
                            <td data-label="Route">
                                <div class="intake-compact-route">
                                    <span class="intake-route-point">{{ $originName }}</span>
                                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16m-6-6 6 6-6 6"/></svg>
                                    <span class="intake-route-point">{{ $destinationName }}</span>
                                </div>
                                <p class="intake-small-detail">{{ $order->picked_up_at?->timezone('Asia/Manila')->format('M d, Y · g:i A') ?: 'Pickup time pending' }}</p>
                            </td>
                            <td data-label="Status">
                                @if($isWaiting)
                                    <span class="intake-badge intake-badge-waiting"><span class="intake-status-dot" aria-hidden="true"></span>Awaiting Intake</span>
                                @elseif($isReceived)
                                    <span class="intake-badge intake-badge-received"><span class="intake-status-dot" aria-hidden="true"></span>Received</span>
                                @else
                                    <span class="intake-badge intake-badge-active"><span class="intake-status-dot" aria-hidden="true"></span>{{ strtoupper(str_replace('_', ' ', $parcel?->status ?: 'in_process')) }}</span>
                                @endif
                            </td>
                            <td data-label="Action">
                                <div class="intake-row-actions">
                                    <button type="button" class="intake-view-icon" data-intake-open-details aria-label="View details for {{ $order->order_number }}" title="View parcel details">
                                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    @if($isWaiting)
                                        <button type="button" class="intake-row-receive" data-intake-open-receive>Receive</button>
                                    @else
                                        <button type="button" class="intake-view-text" data-intake-open-details>View</button>
                                    @endif
                                </div>
                                {{-- Inert markup: details are rendered in the modal only on demand. --}}
                                <template data-intake-details-template>
                                <div class="intake-modal-info-grid">
                                    <section class="intake-modal-info">
                                        <span class="intake-modal-label">Seller Pickup</span>
                                        <strong>{{ $order->pickup_name ?: $sellerName }}</strong>
                                        <p>{{ $order->pickup_address ?: 'No pickup address recorded.' }}</p>
                                    </section>
                                    <section class="intake-modal-info">
                                        <span class="intake-modal-label">Buyer</span>
                                        <strong>{{ $order->buyer_name ?: 'Not provided' }}</strong>
                                        <p>{{ $order->buyer_address ?: 'No buyer address recorded.' }}</p>
                                    </section>
                                    <section class="intake-modal-info">
                                        <span class="intake-modal-label">Pickup Rider</span>
                                        <strong>{{ $order->courier_name ?: 'Pickup Rider' }}</strong>
                                        <p>{{ $order->courier_email ?: 'No Rider email' }}</p>
                                    </section>
                                    <section class="intake-modal-info">
                                        <span class="intake-modal-label">Route</span>
                                        <strong>{{ $originName }}</strong>
                                        <p>Destination: {{ $destinationName }}</p>
                                        <p>Picked up: {{ $order->picked_up_at?->timezone('Asia/Manila')->format('M d, Y · g:i A') ?: 'Not recorded' }}</p>
                                    </section>
                                    <section class="intake-modal-info">
                                        <span class="intake-modal-label">Payment</span>
                                        <strong>{{ $order->payment_method ?: 'Not recorded' }} · {{ ucfirst($order->payment_status ?: 'pending') }}</strong>
                                        <p>Order total: ₱{{ number_format((float) $order->total, 2) }}</p>
                                    </section>
                                    <section class="intake-modal-info">
                                        <span class="intake-modal-label">Hub Intake</span>
                                        <strong>{{ $isWaiting ? 'Awaiting Intake' : ($isReceived ? 'Received' : strtoupper(str_replace('_', ' ', $parcel?->status ?: 'in_process'))) }}</strong>
                                        @if($isReceived)
                                            <p>Received: {{ $parcel->received_at?->timezone('Asia/Manila')->format('M d, Y · g:i A') ?: 'Confirmed' }}</p>
                                        @endif
                                        @if($parcel?->notes)
                                            <p>{{ $parcel->notes }}</p>
                                        @endif
                                    </section>
                                </div>
                                <section class="intake-modal-products">
                                    <h3>Parcel Items</h3>
                                    <ul class="intake-products">
                                        @forelse(($order->items ?? []) as $item)
                                            <li><span>{{ $item['name'] ?? 'Product' }}@if(!empty($item['variant_label'])) · {{ $item['variant_label'] }}@endif</span><strong>× {{ $item['qty'] ?? $item['quantity'] ?? 1 }}</strong></li>
                                        @empty
                                            <li><span>Product details unavailable.</span></li>
                                        @endforelse
                                    </ul>
                                </section>
                                @if($isReceived)
                                    <a href="{{ route('logistics.parcel-sorting') }}" class="intake-modal-sorting-link">Continue to Sorting →</a>
                                @endif
                                </template>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

        {{-- NO INCOMING PARCELS --}}

        @if($orders->isEmpty())

            <div class="intake-empty">

                <span
                    class="intake-empty-icon"
                    aria-hidden="true"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 7 9 5 9-5M3 7l9-4 9 4v10l-9 4-9-4V7Zm9 5v9"/></svg>
                </span>

                <h3>
                    No parcels waiting for origin hub intake
                </h3>

                <p>
                    Parcels will appear here after a Pickup Rider
                    collects them from a Seller.
                </p>

                <a
                    href="{{ route('logistics.delivery-assignment') }}"
                    class="intake-button intake-button-primary"
                >
                    View Pickup Assignment
                </a>

            </div>

        @endif

        {{-- SEARCH EMPTY STATE --}}

        <div
            class="intake-empty"
            data-intake-empty
            hidden
        >

            <span class="intake-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 7 9 5 9-5M3 7l9-4 9 4v10l-9 4-9-4V7Zm9 5v9"/></svg></span>
            <h3>
                No matching incoming parcels
            </h3>

            <p>
                Try another order number, Seller, Rider,
                origin hub, or status.
            </p>

            <button
                type="button"
                class="intake-text-link"
                data-intake-clear
            >
                Clear filters
            </button>

        </div>

        {{-- FOOTER --}}

        <footer class="intake-list-footer">
            <span data-intake-visible-count aria-live="polite">{{ $orders->count() }} parcels shown</span>
            <label class="intake-page-size-label">Rows per page
                <select data-intake-page-size aria-label="Rows per page">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </label>
        </footer>

    </section>

    <nav class="intake-pagination" data-intake-pagination aria-label="Incoming parcel pages"></nav>

    {{-- Reusable parcel detail dialog. Data is safely populated from each row's Blade template. --}}
    <div class="intake-modal-overlay" data-intake-details-modal hidden>
        <div class="intake-modal-panel" role="dialog" aria-modal="true" aria-labelledby="intake-details-heading" tabindex="-1">
            <header class="intake-modal-heading">
                <div>
                    <span class="intake-modal-eyebrow">PARCEL INFORMATION</span>
                    <h2 id="intake-details-heading">Parcel Details</h2>
                    <p data-intake-details-subtitle></p>
                </div>
                <button class="intake-modal-close" type="button" data-intake-modal-close aria-label="Close parcel details">×</button>
            </header>
            <div class="intake-modal-content" data-intake-details-content></div>
            <footer class="intake-modal-footer">
                <button class="intake-modal-secondary" type="button" data-intake-modal-close>Close</button>
            </footer>
        </div>
    </div>

    {{-- One modal form replaces the always-visible per-row intake notes and buttons. --}}
    <div class="intake-modal-overlay" data-intake-receive-modal hidden>
        <div class="intake-modal-panel intake-modal-panel-small" role="dialog" aria-modal="true" aria-labelledby="intake-receive-heading" tabindex="-1">
            <header class="intake-modal-heading">
                <div>
                    <span class="intake-modal-eyebrow">ORIGIN HUB HANDOFF</span>
                    <h2 id="intake-receive-heading">Receive Parcel</h2>
                    <p>Confirm physical receipt before moving this parcel to sorting.</p>
                </div>
                <button class="intake-modal-close" type="button" data-intake-modal-close aria-label="Close receive dialog">×</button>
            </header>
            <form data-intake-receive-form method="POST" action="" class="intake-modal-receive-form">
                @csrf
                <div class="intake-modal-content">
                    <div class="intake-modal-info-grid">
                        <section class="intake-modal-info">
                            <span class="intake-modal-label">Order Number</span>
                            <strong data-intake-receive-order></strong>
                        </section>
                        <section class="intake-modal-info">
                            <span class="intake-modal-label">Pickup Rider</span>
                            <strong data-intake-receive-rider></strong>
                        </section>
                        <section class="intake-modal-info intake-modal-info-full">
                            <span class="intake-modal-label">Receiving Hub</span>
                            <strong data-intake-receive-hub></strong>
                        </section>
                    </div>
                    <label class="intake-modal-note-label" for="intake-modal-notes">Intake Note <span>(optional)</span></label>
                    <textarea id="intake-modal-notes" name="notes" rows="3" maxlength="1000" placeholder="Parcel condition or handoff remarks..."></textarea>
                </div>
                <footer class="intake-modal-footer">
                    <button type="button" class="intake-modal-secondary" data-intake-modal-close>Cancel</button>
                    <button type="submit" class="intake-modal-confirm">Confirm Receipt</button>
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

    function initializeIncomingParcels() {

        document.querySelectorAll(
            '[data-intake-workspace]'
        ).forEach(root => {

            if (root.dataset.initialized) {
                return;
            }

            root.dataset.initialized = 'true';

            /*
            |--------------------------------------------------------------------------
            | ELEMENTS
            |--------------------------------------------------------------------------
            */

            const rows = [
                ...root.querySelectorAll('[data-intake-row]')
            ];

            const search = root.querySelector(
                '[data-intake-search]'
            );

            const status = root.querySelector('[data-intake-status]');
            const applyButton = root.querySelector('[data-intake-apply]');
            const empty = root.querySelector('[data-intake-empty]');
            const countNode = root.querySelector('[data-intake-visible-count]');
            const pageSizeControl = root.querySelector('[data-intake-page-size]');
            const pager = root.querySelector('[data-intake-pagination]');

            let activeStatus = 'all';
            let currentPage = 1;
            let pageSize = 10;
            const normalize = value => String(value || '').toLocaleLowerCase().trim();

            function renderPages(pages) {
                if (!pager) return;
                pager.replaceChildren();
                if (pages <= 1) return;

                function add(label, page, disabled, active, aria) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    if (aria) button.setAttribute('aria-label', aria);
                    if (active) button.setAttribute('aria-current', 'page');
                    button.disabled = disabled;
                    button.addEventListener('click', () => {
                        currentPage = page;
                        applyFilters();
                    });
                    pager.appendChild(button);
                }

                add('‹', Math.max(1, currentPage - 1), currentPage === 1, false, 'Previous page');
                const numbers = [];
                for (let p = 1; p <= pages; p++) {
                    if (pages <= 7 || p === 1 || p === pages || Math.abs(p - currentPage) <= 1) numbers.push(p);
                }
                let previous = 0;
                numbers.forEach(p => {
                    if (p - previous > 1) {
                        const ellipsis = document.createElement('span');
                        ellipsis.textContent = '…';
                        ellipsis.setAttribute('aria-hidden', 'true');
                        pager.appendChild(ellipsis);
                    }
                    add(String(p), p, false, p === currentPage, `Page ${p}`);
                    previous = p;
                });
                add('›', Math.min(pages, currentPage + 1), currentPage === pages, false, 'Next page');
            }

            function applyFilters() {
                const keyword = normalize(search?.value);
                const matches = rows.filter(row =>
                    (activeStatus === 'all' || row.dataset.state === activeStatus)
                    && normalize(row.dataset.search).includes(keyword)
                );
                const pages = Math.max(1, Math.ceil(matches.length / pageSize));
                currentPage = Math.min(currentPage, pages);
                const visibleSet = new Set(matches.slice((currentPage - 1) * pageSize, currentPage * pageSize));
                rows.forEach(row => { row.hidden = !visibleSet.has(row); });
                if (empty) empty.hidden = rows.length === 0 || matches.length > 0;
                if (countNode) {
                    const first = matches.length ? (currentPage - 1) * pageSize + 1 : 0;
                    const last = matches.length ? Math.min(currentPage * pageSize, matches.length) : 0;
                    countNode.textContent = `Showing ${first}–${last} of ${matches.length} parcels`;
                }
                renderPages(Math.ceil(matches.length / pageSize));
            }

            applyButton?.addEventListener('click', () => {
                activeStatus = status?.value || 'all';
                currentPage = 1;
                applyFilters();
            });
            status?.addEventListener('change', () => {
                activeStatus = status.value;
                currentPage = 1;
                applyFilters();
            });
            search?.addEventListener('input', () => {
                currentPage = 1;
                applyFilters();
            });
            pageSizeControl?.addEventListener('change', () => {
                pageSize = Number(pageSizeControl.value) || 10;
                currentPage = 1;
                applyFilters();
            });
            root.querySelectorAll('[data-intake-clear]').forEach(button => {
                button.addEventListener('click', () => {
                    if (search) search.value = '';
                    if (status) status.value = 'all';
                    activeStatus = 'all';
                    currentPage = 1;
                    applyFilters();
                });
            });

            /*
            |--------------------------------------------------------------------------
            | PARCEL DETAILS + RECEIVE DIALOGS
            |--------------------------------------------------------------------------
            | All POSTs still use the existing Laravel receive route, CSRF, and notes.
            */
            const detailsModal = root.querySelector('[data-intake-details-modal]');
            const receiveModal = root.querySelector('[data-intake-receive-modal]');
            const receiveForm = root.querySelector('[data-intake-receive-form]');
            const activeDialogs = [detailsModal, receiveModal].filter(Boolean);
            let returnFocus = null;
            let submitInProgress = false;

            function closeDialogs() {
                if (submitInProgress) return;
                activeDialogs.forEach(modal => { modal.hidden = true; });
                document.body.style.overflow = '';
                if (returnFocus?.isConnected) returnFocus.focus();
                returnFocus = null;
            }

            function openDialog(modal, trigger) {
                if (!modal) return;
                returnFocus = trigger;
                activeDialogs.forEach(dialog => { dialog.hidden = dialog !== modal; });
                document.body.style.overflow = 'hidden';
                const closeButton = modal.querySelector('[data-intake-modal-close]');
                (closeButton || modal.querySelector('[role="dialog"]'))?.focus();
            }

            root.querySelectorAll('[data-intake-open-details]').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('[data-intake-row]');
                    const template = row?.querySelector('[data-intake-details-template]');
                    const content = detailsModal?.querySelector('[data-intake-details-content]');
                    if (!template || !content) return;
                    content.replaceChildren(template.content.cloneNode(true));
                    detailsModal.querySelector('[data-intake-details-subtitle]').textContent =
                        `${row.dataset.orderNumber} · ${row.dataset.sellerName}`;
                    openDialog(detailsModal, button);
                });
            });

            root.querySelectorAll('[data-intake-open-receive]').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('[data-intake-row]');
                    if (!receiveForm || !row?.dataset.receiveUrl) return;
                    receiveForm.reset();
                    receiveForm.action = row.dataset.receiveUrl;
                    receiveModal.querySelector('[data-intake-receive-order]').textContent = row.dataset.orderNumber;
                    receiveModal.querySelector('[data-intake-receive-rider]').textContent = row.dataset.riderName;
                    receiveModal.querySelector('[data-intake-receive-hub]').textContent = row.dataset.originName;
                    openDialog(receiveModal, button);
                });
            });

            activeDialogs.forEach(modal => {
                modal.querySelectorAll('[data-intake-modal-close]').forEach(button => {
                    button.addEventListener('click', closeDialogs);
                });
                modal.addEventListener('click', event => {
                    if (event.target === modal) closeDialogs();
                });
                modal.addEventListener('keydown', event => {
                    if (event.key !== 'Tab') return;
                    const focusable = [...modal.querySelectorAll('button:not(:disabled), a[href], textarea:not(:disabled), input:not(:disabled), select:not(:disabled)')]
                        .filter(el => el.getClientRects().length);
                    if (!focusable.length) return;
                    const first = focusable[0];
                    const last = focusable[focusable.length - 1];
                    if (event.shiftKey && document.activeElement === first) {
                        event.preventDefault(); last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        event.preventDefault(); first.focus();
                    }
                });
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && activeDialogs.some(modal => !modal.hidden)) closeDialogs();
            });

            receiveForm?.addEventListener('submit', event => {
                if (submitInProgress || !receiveForm.action) {
                    event.preventDefault();
                    return;
                }
                // The modal is the confirmation step; do not show an extra browser confirm.
                submitInProgress = true;
                const button = receiveForm.querySelector('[type="submit"]');
                button.disabled = true;
                button.textContent = 'Receiving parcel…';
            });

            applyFilters();

        });

    }

    /*
    |--------------------------------------------------------------------------
    | INITIALIZATION
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            initializeIncomingParcels
        );

    } else {

        initializeIncomingParcels();

    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE FROM BROWSER CACHE
    |--------------------------------------------------------------------------
    */

    window.addEventListener('pageshow', event => {

        if (event.persisted) {
            window.location.reload();
        }

    });

})();
</script>

@endpush
