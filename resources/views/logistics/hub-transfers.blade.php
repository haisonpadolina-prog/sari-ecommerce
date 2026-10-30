
@extends('layouts.logistics')

@section('title', 'Hub Transfers — SARI Logistics')
@section('page-title', 'Hub Transfers')

@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/logistics/hub-transfers.css') }}?v=20261015-1"
>

<style>
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — HUB TRANSFERS
| Seller-style UI refinement
|--------------------------------------------------------------------------
| All overrides are scoped to this page.
| The original hub-transfers.css remains unchanged.
*/

#lgHubTransfers {
    --ht-gold: #d9950b;
    --ht-gold-dark: #c88708;
    --ht-ink: #25211c;
    --ht-muted: #8a8177;
    --ht-line: #e8e2d9;
    --ht-shadow: 0 6px 18px rgba(33, 24, 14, .035);

    width: 100%;
    max-width: 1620px;
    margin-inline: auto;
    color: var(--ht-ink);
    font-size: 10px;
}

#lgHubTransfers [hidden] {
    display: none !important;
}

#lgHubTransfers :is(
    button,
    input,
    select,
    textarea,
    a,
    summary
):focus-visible {
    outline: 2px solid rgba(217, 149, 11, .65);
    outline-offset: 2px;
}

/* =========================================================
   PAGE HEADING
========================================================= */

#lgHubTransfers .transfer-page-header {
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

#lgHubTransfers .transfer-page-header::before,
#lgHubTransfers .transfer-page-header::after {
    display: none;
}

#lgHubTransfers .transfer-eyebrow {
    margin: 0 0 5px;
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    line-height: 1.4;
}

#lgHubTransfers .transfer-page-header .transfer-eyebrow {
    color: #a76f11;
}

#lgHubTransfers .transfer-page-header h1 {
    margin: 0;
    color: #24201b;
    font-size: clamp(23px, 2.15vw, 30px);
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.045em;
}

#lgHubTransfers .transfer-page-header h1 span {
    color: var(--ht-gold);
}

#lgHubTransfers .transfer-subtitle {
    max-width: 750px;
    margin-top: 6px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

/* =========================================================
   BUTTONS
========================================================= */

#lgHubTransfers .transfer-header-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

#lgHubTransfers .transfer-button {
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
    text-align: center;
    text-decoration: none;

    transition:
        background-color .16s ease,
        border-color .16s ease,
        color .16s ease,
        box-shadow .16s ease;
}

#lgHubTransfers .transfer-button:hover {
    background: #fff9ed;
    border-color: #d5bb87;
    color: #a36c0a;
}

#lgHubTransfers .transfer-page-header
.transfer-button:not(.transfer-button-primary) {
    border: 1px solid #e4d8c6;
    background: #fff;
    color: #776a5a;
}

#lgHubTransfers .transfer-page-header
.transfer-button:not(.transfer-button-primary):hover {
    background: #fff9ed;
    border-color: #d5bb87;
    color: #a36c0a;
}

#lgHubTransfers .transfer-button-primary,
#lgHubTransfers .transfer-page-header .transfer-button-primary {
    border-color: var(--ht-gold);
    background: var(--ht-gold);
    color: #fff;
}

#lgHubTransfers .transfer-button-primary:hover,
#lgHubTransfers .transfer-page-header .transfer-button-primary:hover {
    border-color: var(--ht-gold-dark);
    background: var(--ht-gold-dark);
    color: #fff;
}

#lgHubTransfers .transfer-button:disabled {
    opacity: .55;
    cursor: not-allowed;
}

/* =========================================================
   ALERTS
========================================================= */

#lgHubTransfers .transfer-alert {
    margin-bottom: 14px;
    padding: 13px 16px;
    border-radius: 12px;
}

#lgHubTransfers .transfer-alert strong {
    font-size: 10px;
    font-weight: 700;
}

#lgHubTransfers .transfer-alert p,
#lgHubTransfers .transfer-alert li {
    font-size: 9px;
    line-height: 1.6;
}

/* =========================================================
   KPI CARDS
========================================================= */

#lgHubTransfers .transfer-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

#lgHubTransfers .transfer-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;

    min-width: 0;
    min-height: 98px;
    padding: 14px;

    border: 1px solid #e8e2d9;
    border-radius: 16px;

    background: white;
    box-shadow: var(--ht-shadow);

    transition:
        transform .18s cubic-bezier(.22, 1, .36, 1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgHubTransfers .transfer-stat::after {
    display: none;
}

#lgHubTransfers .transfer-stat p {
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgHubTransfers .transfer-stat strong {
    display: block;
    margin: 8px 0 5px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.1;
    letter-spacing: -.045em;
}

#lgHubTransfers .transfer-stat span:not(.ht-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
}

#lgHubTransfers .ht-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;
    width: 41px;
    height: 41px;

    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgHubTransfers .ht-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgHubTransfers .transfer-stat-amber .ht-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgHubTransfers .transfer-stat-neutral .ht-stat-icon {
    background: #f2f1ef;
    color: #80776d;
}

#lgHubTransfers .transfer-stat-blue .ht-stat-icon {
    background: #eff5fa;
    color: #587ead;
}

#lgHubTransfers .transfer-stat-green .ht-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

/* =========================================================
   WORKFLOW STEPPER
========================================================= */

#lgHubTransfers .transfer-flow {
    display: flex;
    gap: 4px;
    max-width: 100%;
    margin: 14px 0;
    padding: 4px;

    border: 1px solid var(--ht-line);
    border-radius: 10px;
    background: white;
}

#lgHubTransfers .transfer-flow :is(a, span) {
    min-height: 32px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgHubTransfers .transfer-flow b {
    display: grid;
    place-items: center;
    flex: 0 0 21px;
    width: 21px;
    height: 21px;
    font-size: 9px;
}

/* =========================================================
   MAIN WORKSPACES
========================================================= */

#lgHubTransfers .transfer-workspace {
    min-width: 0;
    overflow: hidden;

    border: 1px solid #e8e2d9;
    border-radius: 17px;

    background: white;
    box-shadow: var(--ht-shadow);
}

#lgHubTransfers .transfer-active {
    margin-top: 14px;
}

#lgHubTransfers .transfer-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;

    padding: 15px 17px;
}

#lgHubTransfers .transfer-toolbar h2 {
    margin: 4px 0 0;
    color: #29231d;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
}

#lgHubTransfers .transfer-toolbar p:not(.transfer-eyebrow) {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.55;
}

/* =========================================================
   SEARCH
========================================================= */

#lgHubTransfers .transfer-search {
    width: min(315px, 100%);
    flex-shrink: 0;
}

#lgHubTransfers .transfer-search input {
    width: 100%;
    height: 39px;
    padding: 0 12px;

    border: 1px solid #dfe2e6;
    border-radius: 10px;

    background: white;
    color: #50483e;
    font-size: 10px;

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}

#lgHubTransfers .transfer-search input:focus,
#lgHubTransfers .transfer-create-panel
:is(input, select, textarea):focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217, 149, 11, .09);
}

/* =========================================================
   READY ROUTE CARDS
========================================================= */

#lgHubTransfers .transfer-ready-groups {
    display: grid;
    gap: 12px;
    padding: 14px;

    border-top: 1px solid #eee8df;
    background: #fff;
}

#lgHubTransfers .transfer-ready-card,
#lgHubTransfers .transfer-manifest {
    min-width: 0;
    overflow: hidden;

    border: 1px solid #e8e2d9;
    border-radius: 13px;

    background: white;
    box-shadow: 0 3px 12px rgba(35, 27, 18, .025);

    transition:
        transform .18s cubic-bezier(.22, 1, .36, 1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgHubTransfers .transfer-route-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;

    padding: 14px 15px;
    border-bottom: 1px solid #eee8df;
}

#lgHubTransfers .transfer-route-title {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 9px;

    min-width: 0;
}

#lgHubTransfers .transfer-route-title strong {
    color: #443c33;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

#lgHubTransfers .transfer-route-title span {
    color: #be8b30;
    font-size: 15px;
}

#lgHubTransfers .transfer-count-badge {
    display: inline-flex;
    align-items: center;
    min-height: 25px;
    padding: 5px 9px;

    border: 1px solid #eddfc3;
    border-radius: 999px;

    background: #fff8ec;
    color: #a36c16;

    font-size: 8.5px;
    font-weight: 650;
    white-space: nowrap;
}

#lgHubTransfers .transfer-ready-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 294px;
    min-width: 0;
}

#lgHubTransfers .transfer-manifest-preview {
    min-width: 0;
    padding: 16px;
}

/* HUB ROUTE */

#lgHubTransfers .transfer-hub-route,
#lgHubTransfers .transfer-manifest-route {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    align-items: start;
    gap: 13px;

    min-width: 0;
    padding-bottom: 14px;
    border-bottom: 1px solid #eee8df;
}

#lgHubTransfers .transfer-hub-route > div,
#lgHubTransfers .transfer-manifest-route > div {
    min-width: 0;
}

#lgHubTransfers .transfer-label {
    display: block;
    margin-bottom: 5px;
    color: #958879;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .065em;
    text-transform: uppercase;
    line-height: 1.5;
}

#lgHubTransfers .transfer-hub-route strong,
#lgHubTransfers .transfer-manifest-route strong {
    display: block;
    color: #443c33;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgHubTransfers .transfer-hub-route p,
#lgHubTransfers .transfer-manifest-route p {
    margin-top: 4px;
    color: #91877b;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

#lgHubTransfers .transfer-hub-route > b,
#lgHubTransfers .transfer-linehaul-arrow {
    display: grid;
    place-items: center;
    align-self: center;

    width: 28px;
    height: 28px;
    margin: 0;

    border: 1px solid #eddec4;
    border-radius: 9px;

    background: #fff7e9;
    color: #ae7819;
    font-size: 15px;
}

/* READY PARCEL LIST */

#lgHubTransfers .transfer-parcel-list {
    display: grid;
    gap: 8px;
    margin-top: 13px;
}

#lgHubTransfers .transfer-parcel-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    min-width: 0;

    padding: 11px 12px;

    border: 1px solid #e9e4dd;
    border-radius: 10px;
    background: #fbfbfc;
}

#lgHubTransfers .transfer-parcel-row > div {
    min-width: 0;
}

#lgHubTransfers .transfer-parcel-row strong {
    display: block;
    color: #4b433a;
    font-size: 10px;
    font-weight: 650;
}

#lgHubTransfers .transfer-parcel-row p {
    margin-top: 4px;
    color: #8f8579;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

#lgHubTransfers .transfer-parcel-row > span {
    color: #443a2e;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}

/* =========================================================
   CREATE MANIFEST PANEL
========================================================= */

#lgHubTransfers .transfer-create-panel {
    min-width: 0;
    padding: 16px;

    border-left: 1px solid #eee8df;

    background: #fcfcfd;
}

#lgHubTransfers .transfer-create-panel h3 {
    color: #37312a;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.4;
}

#lgHubTransfers .transfer-create-panel > p {
    margin: 7px 0 15px;
    color: #908679;
    font-size: 9px;
    line-height: 1.6;
}

#lgHubTransfers .transfer-create-panel label {
    display: block;
    margin-top: 10px;
}

#lgHubTransfers .transfer-create-panel
:is(input, select, textarea) {
    display: block;
    width: 100%;
    padding: 9px 10px;

    border: 1px solid #dfe2e6;
    border-radius: 9px;

    background: white;
    color: #50483e;

    font-size: 10px;
    line-height: 1.5;
    outline: none;

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}

#lgHubTransfers .transfer-create-panel
:is(input, select) {
    height: 39px;
}

#lgHubTransfers .transfer-create-panel textarea {
    min-height: 66px;
    resize: vertical;
}

#lgHubTransfers .transfer-manifest-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;

    margin-top: 13px;
    padding: 13px 0;

    border-top: 1px solid #eee8df;
    color: #807669;
    font-size: 9px;
}

#lgHubTransfers .transfer-manifest-total strong {
    color: #30271d;
    font-size: 19px;
    font-weight: 750;
}

#lgHubTransfers .transfer-create-panel .transfer-button {
    width: 100%;
    min-height: 40px;
    font-size: 10px;
    font-weight: 700;
}

/* =========================================================
   TRANSFER MANIFEST FILTERS
========================================================= */

#lgHubTransfers .transfer-filter-row {
    padding: 0 17px 13px;
    border-bottom: 1px solid #eee8df;
}

#lgHubTransfers .transfer-tabs {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 4px;

    padding: 4px;

    border: 1px solid #e6e1d9;
    border-radius: 10px;

    background: #f7f8fa;
}

#lgHubTransfers .transfer-tabs button {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    min-height: 30px;
    padding: 5px 10px;

    border: 1px solid transparent;
    border-radius: 7px;
    background: transparent;

    color: #756d63;
    font-size: 9px;
    font-weight: 550;
}

#lgHubTransfers .transfer-tabs button:hover {
    background: #fff9ee;
    color: #a36c0a;
}

#lgHubTransfers .transfer-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: white;
    color: #a36c0a;
    font-weight: 700;
    box-shadow: 0 2px 5px rgba(35, 29, 22, .035);
}

#lgHubTransfers .transfer-tabs span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 21px;
    height: 19px;
    padding: 0 5px;

    border-radius: 6px;
    background: #f0ece6;
    font-size: 8px;
}

/* =========================================================
   TRANSFER MANIFESTS
========================================================= */

#lgHubTransfers .transfer-manifest-list {
    display: grid;
    gap: 12px;
    padding: 14px;
    background: #fff;
}

#lgHubTransfers .transfer-manifest-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;

    padding: 14px 15px;
    border-bottom: 1px solid #eee8df;
}

#lgHubTransfers .transfer-manifest-header h3 {
    margin-top: 4px;
    color: #383027;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: -.02em;
    overflow-wrap: anywhere;
}

#lgHubTransfers .transfer-status {
    display: inline-flex;
    align-items: center;
    min-height: 25px;
    padding: 5px 9px;

    border-radius: 999px;
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: .025em;
    white-space: nowrap;
}

#lgHubTransfers .transfer-manifest-body {
    padding: 15px;
}

#lgHubTransfers .transfer-meta-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    margin-top: 13px;
}

#lgHubTransfers .transfer-meta-grid > div {
    min-width: 0;
    padding: 11px;

    border: 1px solid #eee8e0;
    border-radius: 9px;

    background: #fbfbfc;
}

#lgHubTransfers .transfer-meta-grid strong {
    display: block;
    color: #4b433a;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgHubTransfers .transfer-meta-grid p {
    margin-top: 4px;
    color: #8f8579;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

/* MANIFEST DETAILS */

#lgHubTransfers .transfer-details {
    margin-top: 13px;
}

#lgHubTransfers .transfer-details summary {
    gap: 7px;
    color: #a47118;
    font-size: 9px;
    font-weight: 650;
}

#lgHubTransfers .transfer-detail-body {
    gap: 7px;
    margin-top: 9px;
    padding: 10px;

    border: 1px solid #e8e2d9;
    border-radius: 10px;

    background: #fbfbfc;
}

#lgHubTransfers .transfer-detail-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    min-width: 0;
    padding: 10px;

    border: 1px solid #eee8e0;
    border-radius: 8px;
    background: white;
}

#lgHubTransfers .transfer-detail-row > div {
    min-width: 0;
}

#lgHubTransfers .transfer-detail-row strong {
    color: #4b433a;
    font-size: 10px;
    font-weight: 650;
}

#lgHubTransfers .transfer-detail-row p,
#lgHubTransfers .transfer-detail-row > span {
    color: #8f8579;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgHubTransfers .transfer-detail-row > span {
    text-align: right;
}

/* TRANSFER NOTE */

#lgHubTransfers .transfer-note {
    margin-top: 13px;
    padding: 12px;

    border: 1px solid #e8e2d9;
    border-radius: 9px;
    background: #fbfbfc;
}

#lgHubTransfers .transfer-note p {
    color: #746a5e;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

/* =========================================================
   MANIFEST FOOTER / ACTIONS
========================================================= */

#lgHubTransfers .transfer-manifest-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;

    padding: 12px 15px;

    border-top: 1px solid #eee8df;
    background: #fcfcfd;

    color: #8f8579;
    font-size: 9px;
    line-height: 1.6;
}

#lgHubTransfers .ht-footer-info {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

#lgHubTransfers .ht-footer-dot {
    display: inline-block;
    flex: 0 0 7px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #bd8d32;
}

#lgHubTransfers .ht-footer-dot.in-transit {
    background: #648eae;
}

#lgHubTransfers .ht-footer-dot.received {
    background: #57966b;
}

#lgHubTransfers .transfer-manifest-footer form {
    flex-shrink: 0;
}

#lgHubTransfers .transfer-manifest-footer .transfer-button {
    min-height: 37px;
    font-size: 9px;
    font-weight: 700;
}

#lgHubTransfers .transfer-text-link {
    display: inline-flex;
    align-items: center;
    color: #a66d0b;
    font-size: 9px;
    font-weight: 700;
    line-height: 1.5;
}

#lgHubTransfers .transfer-text-link:hover {
    text-decoration: underline;
    text-underline-offset: 3px;
}

#lgHubTransfers .ht-role-note {
    max-width: 260px;
    color: #8e8377;
    font-size: 9px;
    line-height: 1.5;
}

/* =========================================================
   EMPTY STATES / FOOTER
========================================================= */

#lgHubTransfers .transfer-empty {
    padding: 39px 20px;
}

#lgHubTransfers .transfer-empty-icon {
    display: grid;
    place-items: center;
    width: 43px;
    height: 43px;
    margin: 0 auto 12px;

    border-radius: 12px;
    background: #fff7e8;
    color: #b78327;
}

#lgHubTransfers .transfer-empty h3 {
    color: #51483e;
    font-size: 11px;
    font-weight: 700;
}

#lgHubTransfers .transfer-empty p {
    color: #948a7e;
    font-size: 9px;
    line-height: 1.6;
}

#lgHubTransfers .transfer-list-footer,
#lgHubTransfers .ht-list-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 9px;

    padding: 12px 16px;

    border-top: 1px solid #eee8df;
    background: white;

    color: #978d80;
    font-size: 9px;
}

/* =========================================================
   HOVER
========================================================= */

@media (hover: hover) and (pointer: fine) {
    #lgHubTransfers .transfer-stat:hover,
    #lgHubTransfers .transfer-ready-card:hover,
    #lgHubTransfers .transfer-manifest:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43, 33, 21, .055);
    }
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {
    #lgHubTransfers .transfer-ready-layout {
        grid-template-columns: minmax(0, 1fr) 270px;
    }
}

@media (max-width: 1050px) {
    #lgHubTransfers .transfer-ready-layout {
        grid-template-columns: 1fr;
    }

    #lgHubTransfers .transfer-create-panel {
        border-left: 0;
        border-top: 1px solid #eee8df;
    }
}

@media (max-width: 900px) {
    #lgHubTransfers .transfer-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    #lgHubTransfers .transfer-meta-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    #lgHubTransfers .transfer-page-header,
    #lgHubTransfers .transfer-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgHubTransfers .transfer-search {
        width: 100%;
    }

    #lgHubTransfers .transfer-header-actions {
        width: 100%;
    }

    #lgHubTransfers .transfer-header-actions .transfer-button {
        flex: 1;
    }

    #lgHubTransfers .transfer-flow {
        max-width: 100%;
        overflow-x: auto;
    }

    #lgHubTransfers .transfer-flow :is(a, span) {
        flex-shrink: 0;
        font-size: 9px;
    }

    #lgHubTransfers .transfer-route-header,
    #lgHubTransfers .transfer-manifest-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    #lgHubTransfers .transfer-filter-row {
        padding-inline: 14px;
    }
}

@media (max-width: 520px) {
    #lgHubTransfers .transfer-header-actions {
        flex-direction: column;
    }

    #lgHubTransfers .transfer-header-actions .transfer-button {
        width: 100%;
    }

    #lgHubTransfers .transfer-stat {
        padding: 12px;
    }

    #lgHubTransfers .transfer-stat strong {
        font-size: 22px;
    }

    #lgHubTransfers .ht-stat-icon {
        flex-basis: 34px;
        width: 34px;
        height: 34px;
    }

    #lgHubTransfers .transfer-hub-route,
    #lgHubTransfers .transfer-manifest-route {
        grid-template-columns: 1fr;
    }

    #lgHubTransfers .transfer-hub-route > b,
    #lgHubTransfers .transfer-linehaul-arrow {
        transform: rotate(90deg);
        justify-self: start;
    }

    #lgHubTransfers .transfer-manifest-preview,
    #lgHubTransfers .transfer-create-panel,
    #lgHubTransfers .transfer-manifest-body {
        padding: 13px;
    }

    #lgHubTransfers .transfer-meta-grid {
        gap: 7px;
    }

    #lgHubTransfers .transfer-manifest-footer form,
    #lgHubTransfers .transfer-manifest-footer .transfer-button {
        width: 100%;
    }
}

@media (prefers-reduced-motion: reduce) {
    #lgHubTransfers *,
    #lgHubTransfers *::before,
    #lgHubTransfers *::after {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }

    #lgHubTransfers .transfer-stat:hover,
    #lgHubTransfers .transfer-ready-card:hover,
    #lgHubTransfers .transfer-manifest:hover {
        transform: none !important;
    }
}
</style>
@endpush

@section('content')

<div
    id="lgHubTransfers"
    class="sari-logistics-ui transfer-page"
    data-transfer-workspace
>

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}

    <header class="transfer-page-header">

        <div class="min-w-0">

            <p class="transfer-eyebrow">
                LINEHAUL OPERATIONS
            </p>

            <h1>
                Hub <span>Transfers</span>
            </h1>

            <p class="transfer-subtitle">
                Build linehaul manifests from sorted parcels, assign the
                transport vehicle and driver, dispatch between sorting hubs,
                and confirm destination receipt.
            </p>

        </div>

        <div class="transfer-header-actions">

            <a
                href="{{ route('logistics.parcel-scanner') }}"
                class="transfer-button"
            >
                Scan Parcel QR
            </a>

            <a
                href="{{ route('logistics.parcel-sorting') }}"
                class="transfer-button"
            >
                ← Parcel Sorting
            </a>

            <a
                href="{{ route('logistics.last-mile-assignment') }}"
                class="transfer-button transfer-button-primary"
            >
                Last Mile
                <span aria-hidden="true">→</span>
            </a>

        </div>

    </header>

    {{-- =====================================================
        FLASH MESSAGES
    ====================================================== --}}

    @if(session('success'))

        <div
            class="transfer-alert transfer-alert-success"
            role="status"
        >
            <strong>
                Hub transfer updated
            </strong>

            <p>
                {{ session('success') }}
            </p>
        </div>

    @endif

    @if($errors->any())

        <div
            class="transfer-alert transfer-alert-danger"
            role="alert"
        >
            <strong>
                Could not update Hub Transfer
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
        $summaryCards = [
            [
                'Ready for Linehaul',
                $stats['ready_parcels'],
                $stats['ready_routes'] . ' route groups',
                'amber',
            ],
            [
                'Preparing',
                $stats['preparing'],
                'Manifests awaiting departure',
                'neutral',
            ],
            [
                'In Transit',
                $stats['in_transit'],
                'Vehicles currently moving',
                'blue',
            ],
            [
                'Received Today',
                $stats['received_today'],
                'Destination handoffs completed',
                'green',
            ],
        ];

        $receivedManifestCount = $transfers
            ->where('status', 'received')
            ->count();
    @endphp

    {{-- =====================================================
        KPI CARDS
    ====================================================== --}}

    <section
        class="transfer-stats"
        aria-label="Hub Transfer overview"
    >

        @foreach($summaryCards as [$label, $value, $note, $tone])

            <article class="transfer-stat transfer-stat-{{ $tone }}">

                <div class="min-w-0">

                    <p>{{ $label }}</p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>
                        {{ $note }}
                    </span>

                </div>

                <span
                    class="ht-stat-icon"
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
                                <rect x="3" y="5" width="8" height="7" rx="1"/>
                                <rect x="13" y="5" width="8" height="7" rx="1"/>
                                <rect x="8" y="14" width="8" height="7" rx="1"/>
                            </svg>

                            @break

                        @case('blue')

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 7h12v10H3z"/>
                                <path d="M15 10h3l3 3v4h-6"/>
                                <circle cx="7" cy="18" r="1.5"/>
                                <circle cx="18" cy="18" r="1.5"/>
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

                        @default

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>

                    @endswitch

                </span>

            </article>

        @endforeach

    </section>

    {{-- =====================================================
        WORKFLOW STEPPER
    ====================================================== --}}

    <nav
        class="transfer-flow"
        aria-label="Logistics workflow"
    >

        <a href="{{ route('logistics.incoming-parcels') }}">
            <b>1</b>
            Origin Intake
        </a>

        <a href="{{ route('logistics.parcel-sorting') }}">
            <b>2</b>
            Parcel Sorting
        </a>

        <span
            class="is-current"
            aria-current="step"
        >
            <b>3</b>
            Hub Transfer
        </span>

        <a href="{{ route('logistics.last-mile-assignment') }}">
            <b>4</b>
            Last Mile
        </a>

    </nav>

    {{-- =====================================================
        READY FOR LINEHAUL WORKSPACE
    ====================================================== --}}

    <section
        class="transfer-workspace"
        aria-labelledby="htReadyHeading"
    >

        {{-- TOOLBAR --}}

        <header class="transfer-toolbar">

            <div>

                <p class="transfer-eyebrow">
                    LINEHAUL PLANNING
                </p>

                <h2 id="htReadyHeading">
                    Ready for Linehaul
                </h2>

                <p>
                    Sorted parcels are automatically grouped by
                    origin and destination hub.
                </p>

            </div>

            <label class="transfer-search">

                <span class="sr-only">
                    Search transfer routes
                </span>

                <input
                    type="search"
                    data-ready-search
                    autocomplete="off"
                    placeholder="Search hub, order, Seller…"
                >

            </label>

        </header>

        {{-- READY GROUPS --}}

        <div class="transfer-ready-groups">

            @forelse($readyGroups as $group)

                @php
                    $search = collect([
                        $group['origin_name'],
                        $group['origin_municipality'],
                        $group['destination_name'],
                        $group['destination_municipality'],
                    ]);

                    foreach($group['orders'] as $order) {

                        $search->push($order->order_number);
                        $search->push($order->buyer_name);
                        $search->push($order->seller?->store_name);

                    }
                @endphp

                <article
                    class="transfer-ready-card"
                    data-ready-group
                    data-search="{{ $search->filter()->implode(' ') }}"
                >

                    {{-- ROUTE CARD HEADER --}}

                    <header class="transfer-route-header">

                        <div class="min-w-0">

                            <p class="transfer-eyebrow">
                                LINEHAUL ROUTE
                            </p>

                            <div class="transfer-route-title">

                                <strong>
                                    {{ $group['origin_name'] }}
                                </strong>

                                <span aria-hidden="true">
                                    →
                                </span>

                                <strong>
                                    {{ $group['destination_name'] }}
                                </strong>

                            </div>

                        </div>

                        <span class="transfer-count-badge">

                            {{ $group['parcel_count'] }}

                            {{
                                $group['parcel_count'] === 1
                                    ? 'parcel'
                                    : 'parcels'
                            }}

                        </span>

                    </header>

                    {{-- ROUTE BODY --}}

                    <div class="transfer-ready-layout">

                        {{-- LEFT: MANIFEST PREVIEW --}}

                        <div class="transfer-manifest-preview">

                            <div class="transfer-hub-route">

                                <div>

                                    <span class="transfer-label">
                                        Origin Hub
                                    </span>

                                    <strong>
                                        {{ $group['origin_name'] }}
                                    </strong>

                                    <p>
                                        {{
                                            $group['origin_municipality']
                                            ?: 'Origin facility'
                                        }}
                                    </p>

                                </div>

                                <b aria-hidden="true">
                                    →
                                </b>

                                <div>

                                    <span class="transfer-label">
                                        Destination Hub
                                    </span>

                                    <strong>
                                        {{ $group['destination_name'] }}
                                    </strong>

                                    <p>
                                        {{
                                            $group['destination_municipality']
                                            ?: 'Destination facility'
                                        }}
                                    </p>

                                </div>

                            </div>

                            {{-- PARCEL LIST --}}

                            <div class="transfer-parcel-list">

                                @foreach($group['parcels'] as $parcel)

                                    @php
                                        $order = $parcel->order;
                                    @endphp

                                    <div class="transfer-parcel-row">

                                        <div class="min-w-0">

                                            <strong>
                                                {{ $order?->order_number }}
                                            </strong>

                                            <p>
                                                {{ $order?->buyer_name }}

                                                · Zone

                                                {{
                                                    $parcel->sorting_zone
                                                    ?: '—'
                                                }}
                                            </p>

                                        </div>

                                        <span>
                                            ₱{{ number_format(
                                                (float) ($order?->total ?? 0),
                                                2
                                            ) }}
                                        </span>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                        {{-- RIGHT: CREATE MANIFEST --}}

                        <aside class="transfer-create-panel">

                            <h3>
                                Create Transfer Manifest
                            </h3>

                            <p>
                                Assign the linehaul vehicle and driver
                                before dispatch.
                            </p>

                            <form
                                method="POST"
                                action="{{ route('logistics.hub-transfers.store') }}"
                                data-create-transfer
                            >

                                @csrf

                                {{-- PARCEL IDS --}}

                                @foreach($group['parcels'] as $parcel)

                                    <input
                                        type="hidden"
                                        name="parcel_ids[]"
                                        value="{{ $parcel->id }}"
                                    >

                                @endforeach

                                {{-- VEHICLE TYPE --}}

                                <label>

                                    <span class="transfer-label">
                                        Vehicle Type
                                    </span>

                                    <select
                                        name="vehicle_type"
                                        required
                                    >

                                        <option value="">
                                            Select vehicle
                                        </option>

                                        <option value="Motorcycle">
                                            Motorcycle
                                        </option>

                                        <option value="Multicab">
                                            Multicab
                                        </option>

                                        <option value="L300">
                                            L300
                                        </option>

                                        <option value="Van">
                                            Van
                                        </option>

                                        <option value="Truck">
                                            Truck
                                        </option>

                                    </select>

                                </label>

                                {{-- PLATE NUMBER --}}

                                <label>

                                    <span class="transfer-label">
                                        Plate Number
                                    </span>

                                    <input
                                        type="text"
                                        name="vehicle_plate"
                                        maxlength="40"
                                        placeholder="e.g. DEMO-1234"
                                        autocomplete="off"
                                    >

                                </label>

                                {{-- DRIVER --}}

                                <label>

                                    <span class="transfer-label">
                                        Driver
                                    </span>

                                    <input
                                        type="text"
                                        name="driver_name"
                                        maxlength="140"
                                        required
                                        placeholder="Driver name"
                                        autocomplete="off"
                                    >

                                </label>

                                {{-- NOTES --}}

                                <label>

                                    <span class="transfer-label">
                                        Manifest Note
                                    </span>

                                    <textarea
                                        name="notes"
                                        rows="2"
                                        maxlength="1500"
                                        placeholder="Optional note…"
                                    ></textarea>

                                </label>

                                {{-- LOAD SUMMARY --}}

                                <div class="transfer-manifest-total">

                                    <span>
                                        Manifest Load
                                    </span>

                                    <strong>
                                        {{ $group['parcel_count'] }}
                                    </strong>

                                </div>

                                <button
                                    type="submit"
                                    class="transfer-button transfer-button-primary"
                                >
                                    Create Manifest
                                </button>

                            </form>

                        </aside>

                    </div>

                </article>

            @empty

                {{-- NO READY PARCELS --}}

                <div class="transfer-empty">

                    <span
                        class="transfer-empty-icon"
                        aria-hidden="true"
                    >
                        ✓
                    </span>

                    <h3>
                        No Parcels Waiting for Linehaul
                    </h3>

                    <p>
                        Parcels appear here after sorting is completed
                        at the origin hub.
                    </p>

                    <a
                        href="{{ route('logistics.parcel-sorting') }}"
                        class="transfer-button transfer-button-primary"
                    >
                        View Parcel Sorting
                    </a>

                </div>

            @endforelse

            {{-- SEARCH EMPTY STATE --}}

            <div
                class="transfer-empty"
                data-ready-empty
                hidden
            >

                <h3>
                    No Matching Route Groups
                </h3>

                <p>
                    Try another hub, order number, Seller, or Buyer.
                </p>

            </div>

        </div>

        {{-- READY GROUP FOOTER --}}

        <footer class="ht-list-footer">

            <span
                data-ready-visible-count
                aria-live="polite"
            >
                {{ count($readyGroups) }}
                route groups shown
            </span>

            <span>
                Grouped by Origin → Destination
            </span>

        </footer>

    </section>

    {{-- =====================================================
        TRANSFER MANIFEST WORKSPACE
    ====================================================== --}}

    <section
        class="transfer-workspace transfer-active"
        aria-labelledby="htManifestHeading"
    >

        {{-- TOOLBAR --}}

        <header class="transfer-toolbar">

            <div>

                <p class="transfer-eyebrow">
                    TRANSFER TRACKING
                </p>

                <h2 id="htManifestHeading">
                    Transfer Manifests
                </h2>

                <p>
                    Track preparing, in-transit, and completed
                    Hub Transfer operations.
                </p>

            </div>

            <label class="transfer-search">

                <span class="sr-only">
                    Search transfer manifests
                </span>

                <input
                    type="search"
                    data-transfer-search
                    autocomplete="off"
                    placeholder="Search transfer, driver, vehicle…"
                >

            </label>

        </header>

        {{-- STATUS FILTERS --}}

        <div class="transfer-filter-row">

            <div
                class="transfer-tabs"
                role="group"
                aria-label="Filter transfer manifests"
            >

                <button
                    type="button"
                    data-transfer-filter="all"
                    aria-pressed="true"
                >
                    All
                    <span>{{ $transfers->count() }}</span>
                </button>

                <button
                    type="button"
                    data-transfer-filter="preparing"
                    aria-pressed="false"
                >
                    Preparing
                    <span>{{ $stats['preparing'] }}</span>
                </button>

                <button
                    type="button"
                    data-transfer-filter="in_transit"
                    aria-pressed="false"
                >
                    In Transit
                    <span>{{ $stats['in_transit'] }}</span>
                </button>

                <button
                    type="button"
                    data-transfer-filter="received"
                    aria-pressed="false"
                >
                    Received
                    <span>{{ $receivedManifestCount }}</span>
                </button>

            </div>

        </div>

        {{-- =================================================
            EXISTING TRANSFER MANIFESTS
        ================================================== --}}

        <div class="transfer-manifest-list">

            @forelse($transfers as $transfer)

                @php
                    $items = $transferParcels->get(
                        $transfer->id,
                        collect()
                    );

                    $search = implode(' ', [
                        $transfer->transfer_code,
                        $transfer->origin_name,
                        $transfer->destination_name,
                        $transfer->driver_name,
                        $transfer->vehicle_type,
                        $transfer->vehicle_plate,
                    ]);
                @endphp

                <article
                    class="transfer-manifest"
                    data-transfer-row
                    data-state="{{ $transfer->status }}"
                    data-search="{{ $search }}"
                >

                    {{-- MANIFEST HEADER --}}

                    <header class="transfer-manifest-header">

                        <div class="min-w-0">

                            <p class="transfer-eyebrow">
                                TRANSFER MANIFEST
                            </p>

                            <h3>
                                {{ $transfer->transfer_code }}
                            </h3>

                        </div>

                        <span
                            class="transfer-status transfer-status-{{ $transfer->status }}"
                        >
                            {{
                                strtoupper(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $transfer->status
                                    )
                                )
                            }}
                        </span>

                    </header>

                    {{-- MANIFEST BODY --}}

                    <div class="transfer-manifest-body">

                        {{-- ORIGIN → DESTINATION --}}

                        <div class="transfer-manifest-route">

                            <div>

                                <span class="transfer-label">
                                    Origin Hub
                                </span>

                                <strong>
                                    {{ $transfer->origin_name }}
                                </strong>

                                <p>
                                    {{ $transfer->origin_municipality }}
                                </p>

                            </div>

                            <span
                                class="transfer-linehaul-arrow"
                                aria-hidden="true"
                            >
                                →
                            </span>

                            <div>

                                <span class="transfer-label">
                                    Destination Hub
                                </span>

                                <strong>
                                    {{ $transfer->destination_name }}
                                </strong>

                                <p>
                                    {{ $transfer->destination_municipality }}
                                </p>

                            </div>

                        </div>

                        {{-- MANIFEST DETAILS --}}

                        <div class="transfer-meta-grid">

                            {{-- VEHICLE --}}

                            <div>

                                <span class="transfer-label">
                                    Vehicle
                                </span>

                                <strong>
                                    {{ $transfer->vehicle_type }}
                                </strong>

                                <p>
                                    {{
                                        $transfer->vehicle_plate
                                        ?: 'No plate recorded'
                                    }}
                                </p>

                            </div>

                            {{-- DRIVER --}}

                            <div>

                                <span class="transfer-label">
                                    Driver
                                </span>

                                <strong>
                                    {{ $transfer->driver_name }}
                                </strong>

                            </div>

                            {{-- PARCEL COUNT --}}

                            <div>

                                <span class="transfer-label">
                                    Parcels
                                </span>

                                <strong>
                                    {{ $items->count() }}
                                </strong>

                            </div>

                            {{-- CREATED DATE --}}

                            <div>

                                <span class="transfer-label">
                                    Created
                                </span>

                                <strong>
                                    {{
                                        $transfer->created_at
                                            ? \Illuminate\Support\Carbon::parse(
                                                $transfer->created_at
                                            )
                                                ->timezone('Asia/Manila')
                                                ->format('M d · g:i A')
                                            : 'Not recorded'
                                    }}
                                </strong>

                            </div>

                        </div>

                        {{-- EXPANDABLE MANIFEST PARCELS --}}

                        <details class="transfer-details">

                            <summary>
                                View Manifest Parcels
                                <span aria-hidden="true">+</span>
                            </summary>

                            <div class="transfer-detail-body">

                                @forelse($items as $item)

                                    <div class="transfer-detail-row">

                                        <div class="min-w-0">

                                            <strong>
                                                {{ $item->order_number }}
                                            </strong>

                                            <p>
                                                {{ $item->buyer_name }}

                                                ·

                                                {{ $item->sorting_zone }}
                                            </p>

                                        </div>

                                        <span>
                                            {{
                                                strtoupper(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $item->parcel_status
                                                    )
                                                )
                                            }}
                                        </span>

                                    </div>

                                @empty

                                    <p class="text-[9px] text-[#92877c]">
                                        No parcel details available.
                                    </p>

                                @endforelse

                            </div>

                        </details>

                        {{-- TRANSFER NOTE --}}

                        @if($transfer->notes)

                            <div class="transfer-note">

                                <span class="transfer-label">
                                    Transfer Note
                                </span>

                                <p>
                                    {{ $transfer->notes }}
                                </p>

                            </div>

                        @endif

                    </div>

                    {{-- =================================================
                        MANIFEST FOOTER / ROLE-BASED ACTIONS
                    ================================================== --}}

                    <footer class="transfer-manifest-footer">

                        {{-- STATUS INFORMATION --}}

                        <div class="ht-footer-info">

                            <span
                                class="ht-footer-dot
                                    {{ $transfer->status === 'in_transit' ? 'in-transit' : '' }}
                                    {{ $transfer->status === 'received' ? 'received' : '' }}"
                                aria-hidden="true"
                            ></span>

                            @if($transfer->status === 'preparing')

                                <span>
                                    Manifest ready for departure
                                </span>

                            @elseif($transfer->status === 'in_transit')

                                <span>

                                    Dispatched

                                    @if($transfer->dispatched_at)

                                        {{
                                            \Illuminate\Support\Carbon::parse(
                                                $transfer->dispatched_at
                                            )->diffForHumans()
                                        }}

                                    @else

                                        · Time not recorded

                                    @endif

                                </span>

                            @else

                                <span>
                                    Destination receipt completed
                                </span>

                            @endif

                        </div>

                        {{-- =============================================
                            PREPARING: ORIGIN HUB ONLY
                        ============================================== --}}

                        @if($transfer->status === 'preparing')

                            @if(
                                ! $hubFacility
                                || (int) $transfer->origin_facility_id
                                    === (int) $hubFacility->id
                            )

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'logistics.hub-transfers.dispatch',
                                        $transfer->id
                                    ) }}"
                                    data-dispatch-transfer
                                    data-transfer-code="{{ $transfer->transfer_code }}"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="transfer-button transfer-button-primary"
                                    >
                                        Dispatch Transfer
                                    </button>

                                </form>

                            @else

                                <span class="ht-role-note">
                                    Only the origin hub can dispatch this transfer.
                                </span>

                            @endif

                        {{-- =============================================
                            IN TRANSIT: DESTINATION HUB ONLY
                        ============================================== --}}

                        @elseif($transfer->status === 'in_transit')

                            @if(
                                ! $hubFacility
                                || (int) $transfer->destination_facility_id
                                    === (int) $hubFacility->id
                            )

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'logistics.hub-transfers.receive',
                                        $transfer->id
                                    ) }}"
                                    data-receive-transfer
                                    data-transfer-code="{{ $transfer->transfer_code }}"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="transfer-button transfer-button-primary"
                                    >
                                        Confirm Destination Receipt
                                    </button>

                                </form>

                            @else

                                <span class="ht-role-note">
                                    Awaiting receipt by the destination hub.
                                </span>

                            @endif

                        {{-- =============================================
                            RECEIVED: COMPLETED TRANSFER
                        ============================================== --}}

                        @elseif($transfer->status === 'received')

                            <a
                                href="{{ route('logistics.last-mile-assignment') }}"
                                class="transfer-text-link"
                            >
                                Continue to Last Mile →
                            </a>

                        @endif

                    </footer>

                </article>

            @empty

                {{-- NO TRANSFER MANIFESTS --}}

                <div class="transfer-empty">

                    <span
                        class="transfer-empty-icon"
                        aria-hidden="true"
                    >
                        ✓
                    </span>

                    <h3>
                        No Hub Transfer Manifests Yet
                    </h3>

                    <p>
                        Create a manifest from sorted parcels above.
                    </p>

                </div>

            @endforelse

            {{-- FILTER EMPTY STATE --}}

            <div
                class="transfer-empty"
                data-transfer-empty
                hidden
            >

                <h3>
                    No Matching Transfer Manifests
                </h3>

                <p>
                    Try another manifest code, driver, vehicle,
                    hub, or transfer status.
                </p>

            </div>

        </div>

        {{-- MANIFEST FOOTER --}}

        <footer class="transfer-list-footer">

            <span
                data-transfer-visible-count
                aria-live="polite"
            >
                {{ $transfers->count() }} transfers shown
            </span>

            <span>
                Preparing → In Transit → Received
            </span>

        </footer>

    </section>

</div>

@endsection

@push('scripts')

<script>
(() => {

    'use strict';

    function initializeHubTransfers() {

        const root = document.querySelector(
            '[data-transfer-workspace]'
        );

        if (!root || root.dataset.initialized) {
            return;
        }

        root.dataset.initialized = 'true';

        /*
        |--------------------------------------------------------------------------
        | NORMALIZATION
        |--------------------------------------------------------------------------
        */

        const normalize = value =>
            String(value || '')
                .toLocaleLowerCase()
                .trim();

        /*
        |--------------------------------------------------------------------------
        | READY ROUTE SEARCH
        |--------------------------------------------------------------------------
        */

        const readySearch = root.querySelector(
            '[data-ready-search]'
        );

        const readyGroups = [
            ...root.querySelectorAll('[data-ready-group]')
        ];

        const readyEmpty = root.querySelector(
            '[data-ready-empty]'
        );

        const readyCount = root.querySelector(
            '[data-ready-visible-count]'
        );

        function filterReady() {

            const keyword = normalize(
                readySearch?.value
            );

            let visible = 0;

            readyGroups.forEach(card => {

                const show = normalize(
                    card.dataset.search
                ).includes(keyword);

                card.hidden = !show;

                if (show) {
                    visible++;
                }

            });

            if (readyEmpty) {

                readyEmpty.hidden =
                    readyGroups.length === 0 || visible > 0;

            }

            if (readyCount) {

                readyCount.textContent =
                    `${visible} of ${readyGroups.length} `
                    + `${readyGroups.length === 1 ? 'route group' : 'route groups'} shown`;

            }

        }

        readySearch?.addEventListener(
            'input',
            filterReady
        );

        /*
        |--------------------------------------------------------------------------
        | TRANSFER MANIFEST SEARCH / FILTER
        |--------------------------------------------------------------------------
        */

        const transferSearch = root.querySelector(
            '[data-transfer-search]'
        );

        const transferRows = [
            ...root.querySelectorAll('[data-transfer-row]')
        ];

        const tabs = [
            ...root.querySelectorAll('[data-transfer-filter]')
        ];

        const transferEmpty = root.querySelector(
            '[data-transfer-empty]'
        );

        const visibleCount = root.querySelector(
            '[data-transfer-visible-count]'
        );

        let filter = 'all';

        function filterTransfers() {

            const keyword = normalize(
                transferSearch?.value
            );

            let visible = 0;

            transferRows.forEach(row => {

                const matchesState =
                    filter === 'all'
                    || row.dataset.state === filter;

                const matchesSearch =
                    normalize(row.dataset.search)
                        .includes(keyword);

                row.hidden =
                    !matchesState || !matchesSearch;

                if (!row.hidden) {
                    visible++;
                }

            });

            tabs.forEach(tab => {

                tab.setAttribute(
                    'aria-pressed',
                    String(
                        tab.dataset.transferFilter === filter
                    )
                );

            });

            if (transferEmpty) {

                transferEmpty.hidden =
                    transferRows.length === 0 || visible > 0;

            }

            if (visibleCount) {

                visibleCount.textContent =
                    `${visible} of ${transferRows.length} `
                    + `${transferRows.length === 1 ? 'transfer' : 'transfers'} shown`;

            }

        }

        tabs.forEach(tab => {

            tab.addEventListener('click', () => {

                filter =
                    tab.dataset.transferFilter || 'all';

                filterTransfers();

            });

        });

        transferSearch?.addEventListener(
            'input',
            filterTransfers
        );

        /*
        |--------------------------------------------------------------------------
        | CREATE MANIFEST CONFIRMATION
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll(
            '[data-create-transfer]'
        ).forEach(form => {

            form.addEventListener('submit', event => {

                const count = form.querySelectorAll(
                    'input[name="parcel_ids[]"]'
                ).length;

                const confirmed = window.confirm(
                    `Create a Hub Transfer manifest `
                    + `containing ${count} `
                    + `${count === 1 ? 'parcel' : 'parcels'}?`
                );

                if (!confirmed) {

                    event.preventDefault();
                    return;

                }

                const button = form.querySelector(
                    'button[type="submit"]'
                );

                if (button) {

                    button.disabled = true;
                    button.textContent = 'Creating manifest…';

                }

            });

        });

        /*
        |--------------------------------------------------------------------------
        | DISPATCH CONFIRMATION
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll(
            '[data-dispatch-transfer]'
        ).forEach(form => {

            form.addEventListener('submit', event => {

                const code =
                    form.dataset.transferCode
                    || 'this transfer';

                const confirmed = window.confirm(
                    `Dispatch ${code} from the origin hub?`
                );

                if (!confirmed) {

                    event.preventDefault();
                    return;

                }

                const button = form.querySelector(
                    'button[type="submit"]'
                );

                if (button) {

                    button.disabled = true;
                    button.textContent = 'Dispatching transfer…';

                }

            });

        });

        /*
        |--------------------------------------------------------------------------
        | DESTINATION RECEIPT CONFIRMATION
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll(
            '[data-receive-transfer]'
        ).forEach(form => {

            form.addEventListener('submit', event => {

                const code =
                    form.dataset.transferCode
                    || 'this transfer';

                const confirmed = window.confirm(
                    `Confirm physical receipt of ${code} `
                    + 'at the destination hub?'
                );

                if (!confirmed) {

                    event.preventDefault();
                    return;

                }

                const button = form.querySelector(
                    'button[type="submit"]'
                );

                if (button) {

                    button.disabled = true;
                    button.textContent = 'Confirming receipt…';

                }

            });

        });

        /*
        |--------------------------------------------------------------------------
        | INITIAL RENDER
        |--------------------------------------------------------------------------
        */

        filterReady();
        filterTransfers();

    }

    /*
    |--------------------------------------------------------------------------
    | INITIALIZATION
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            initializeHubTransfers
        );

    } else {

        initializeHubTransfers();

    }

    /*
    |--------------------------------------------------------------------------
    | BROWSER BACK/FORWARD CACHE
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
