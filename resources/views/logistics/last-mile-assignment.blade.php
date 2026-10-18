@extends('layouts.logistics')

@section('title', 'Last Mile Assignment — SARI Logistics')
@section('page-title', 'Last Mile Assignment')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/logistics/last-mile-assignment.css') }}?v=20261015-1">

<style>
#lgLastMile {
    --lm-gold: #d9950b;
    --lm-ink: #25211c;
    --lm-line: #e8e2d9;
    --lm-muted: #8a8177;
    --lm-shadow: 0 6px 18px rgba(33,24,14,.035);
    width: 100%;
    max-width: 1620px;
    margin-inline: auto;
    font-size: 10px;
}

#lgLastMile [hidden] {
    display: none !important;
}

#lgLastMile :is(button,input,select,a,summary):focus-visible {
    outline: 2px solid rgba(217,149,11,.65);
    outline-offset: 2px;
}

/* PAGE HEADING */

#lgLastMile .lastmile-page-header {
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

#lgLastMile .lastmile-page-header::before,
#lgLastMile .lastmile-page-header::after {
    display: none;
}

#lgLastMile .lastmile-eyebrow {
    margin: 0 0 5px;
    color: #a76f11;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    line-height: 1.4;
    text-transform: uppercase;
}

#lgLastMile .lastmile-page-header .lastmile-eyebrow {
    color: #a76f11;
}

#lgLastMile .lastmile-page-header h1 {
    color: #24201b;
    font-size: clamp(23px,2.15vw,30px);
    font-weight: 750;
    line-height: 1.2;
    letter-spacing: -.045em;
}

#lgLastMile .lastmile-page-header h1 span {
    color: var(--lm-gold);
}

#lgLastMile .lastmile-subtitle {
    max-width: 740px;
    margin-top: 6px;
    color: #897f75;
    font-size: 10px;
    line-height: 1.7;
}

#lgLastMile .lastmile-header-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

/* BUTTONS */

#lgLastMile .lastmile-button {
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
    text-decoration: none;
    transition:
        background-color .16s ease,
        border-color .16s ease,
        color .16s ease,
        box-shadow .16s ease;
}

#lgLastMile .lastmile-button:hover {
    transform: none;
    background: #fff9ed;
    border-color: #d5bb87;
    color: #a36c0a;
}

#lgLastMile .lastmile-button-primary,
#lgLastMile .lastmile-page-header .lastmile-button-primary {
    border-color: #d9950b;
    background: #d9950b;
    color: white;
    box-shadow: none;
}

#lgLastMile .lastmile-button-primary:hover,
#lgLastMile .lastmile-page-header .lastmile-button-primary:hover {
    border-color: #c88708;
    background: #c88708;
    color: white;
}

#lgLastMile .lastmile-page-header
.lastmile-button:not(.lastmile-button-primary) {
    border-color: #e4d8c6;
    background: white;
    color: #776a5a;
}

#lgLastMile .lastmile-page-header
.lastmile-button:not(.lastmile-button-primary):hover {
    border-color: #d5bb87;
    background: #fff9ed;
    color: #a36c0a;
}

#lgLastMile .lastmile-button:disabled {
    opacity: .55;
    cursor: not-allowed;
}

/* ALERTS */

#lgLastMile .lastmile-alert {
    padding: 13px 16px;
    margin-bottom: 14px;
    border-radius: 12px;
}

#lgLastMile .lastmile-alert strong {
    font-size: 10px;
    font-weight: 700;
}

#lgLastMile .lastmile-alert p,
#lgLastMile .lastmile-alert li {
    font-size: 9px;
    line-height: 1.6;
}

/* KPI CARDS */

#lgLastMile .lastmile-stats {
    grid-template-columns: repeat(4,minmax(0,1fr));
    gap: 12px;
}

#lgLastMile .lastmile-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    min-width: 0;
    min-height: 98px;
    padding: 14px;
    border: 1px solid var(--lm-line);
    border-radius: 16px;
    background: white;
    box-shadow: var(--lm-shadow);
    transition:
        transform .18s cubic-bezier(.22,1,.36,1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgLastMile .lastmile-stat::after {
    display: none;
}

#lgLastMile .lastmile-stat p {
    margin: 0;
    color: #756b60;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
}

#lgLastMile .lastmile-stat strong,
#lgLastMile .lastmile-stat-amber strong,
#lgLastMile .lastmile-stat-blue strong,
#lgLastMile .lastmile-stat-green strong {
    display: block;
    margin: 8px 0 5px;
    color: #211d18;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.1;
    letter-spacing: -.045em;
}

#lgLastMile .lastmile-stat span:not(.lm-stat-icon) {
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
}

#lgLastMile .lm-stat-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;
    width: 41px;
    height: 41px;
    border-radius: 11px;
    background: #f1f6fd;
    color: #587ead;
}

#lgLastMile .lm-stat-icon svg {
    width: 18px;
    height: 18px;
}

#lgLastMile .lastmile-stat-amber .lm-stat-icon {
    background: #fff7e9;
    color: #bc8420;
}

#lgLastMile .lastmile-stat-neutral .lm-stat-icon {
    background: #f2f1ef;
    color: #80776d;
}

#lgLastMile .lastmile-stat-blue .lm-stat-icon {
    background: #eff5fa;
    color: #587ead;
}

#lgLastMile .lastmile-stat-green .lm-stat-icon {
    background: #eef8f2;
    color: #4c8663;
}

/* WORKFLOW STEPPER */

#lgLastMile .lastmile-flow {
    display: flex;
    max-width: 100%;
    gap: 4px;
    margin: 14px 0;
    padding: 4px;
    border: 1px solid var(--lm-line);
    border-radius: 10px;
    background: white;
}

#lgLastMile .lastmile-flow :is(a,span) {
    min-height: 32px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 9px;
}

#lgLastMile .lastmile-flow b {
    display: grid;
    place-items: center;
    flex: 0 0 21px;
    width: 21px;
    height: 21px;
    font-size: 9px;
}

/* WORKSPACES */

#lgLastMile .lastmile-workspace {
    min-width: 0;
    overflow: hidden;
    border: 1px solid var(--lm-line);
    border-radius: 17px;
    background: white;
    box-shadow: var(--lm-shadow);
}

#lgLastMile .lastmile-assignment-history {
    margin-top: 14px;
}

#lgLastMile .lastmile-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding: 15px 17px;
}

#lgLastMile .lastmile-toolbar h2 {
    margin: 4px 0 0;
    color: #29231d;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
}

#lgLastMile .lastmile-toolbar p:not(.lastmile-eyebrow) {
    margin-top: 4px;
    color: #93897e;
    font-size: 9px;
    line-height: 1.55;
}

/* SEARCH AND FILTERS */

#lgLastMile .lastmile-search {
    width: min(315px,100%);
    flex-shrink: 0;
}

#lgLastMile .lastmile-search input {
    height: 39px;
    padding: 0 12px;
    border: 1px solid #dfe2e6;
    border-radius: 10px;
    background: white;
    color: #50483e;
    font-size: 10px;
}

#lgLastMile .lastmile-search input:focus,
#lgLastMile .lastmile-dispatch-panel select:focus {
    border-color: #d9950b;
    box-shadow: 0 0 0 3px rgba(217,149,11,.09);
}

#lgLastMile .lastmile-filter-row {
    gap: 10px;
    padding: 0 17px 13px;
    border-bottom: 1px solid #eee8df;
}

#lgLastMile .lastmile-tabs {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 4px;
    padding: 4px;
    border: 1px solid #e6e1d9;
    border-radius: 10px;
    background: #f7f8fa;
}

#lgLastMile .lastmile-tabs button {
    min-height: 30px;
    padding: 5px 10px;
    border: 1px solid transparent;
    border-radius: 7px;
    background: transparent;
    color: #756d63;
    font-size: 9px;
    font-weight: 550;
}

#lgLastMile .lastmile-tabs button:hover {
    background: #fff9ee;
    color: #a36c0a;
}

#lgLastMile .lastmile-tabs button[aria-pressed="true"] {
    border-color: #e9dfce;
    background: white;
    color: #a36c0a;
    font-weight: 700;
    box-shadow: 0 2px 5px rgba(35,29,22,.035);
}

#lgLastMile .lastmile-tabs span {
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

#lgLastMile .lastmile-text-link {
    color: #a66d0b;
    font-size: 9px;
    font-weight: 650;
}

/* DELIVERY AREA CARDS */

#lgLastMile .lastmile-area-list,
#lgLastMile .lastmile-assignment-list {
    display: grid;
    gap: 12px;
    padding: 14px;
    background: white;
}

#lgLastMile .lastmile-area-card {
    min-width: 0;
    overflow: hidden;
    border: 1px solid var(--lm-line);
    border-radius: 13px;
    background: white;
    box-shadow: 0 3px 12px rgba(35,27,18,.025);
    transition:
        transform .18s cubic-bezier(.22,1,.36,1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgLastMile .lastmile-area-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    min-height: 0;
    padding: 14px 15px;
    border-bottom: 1px solid #eee8df;
    background: white;
}

#lgLastMile .lastmile-area-title {
    min-width: 0;
    gap: 11px;
}

#lgLastMile .lastmile-area-icon {
    display: grid;
    place-items: center;
    flex: 0 0 41px;
    width: 41px;
    height: 41px;
    border: 1px solid #eee3ce;
    border-radius: 11px;
    background: #fff5e1;
    color: #a97312;
    font-size: 12px;
    font-weight: 700;
}

#lgLastMile .lastmile-area-title h3 {
    color: #383027;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgLastMile .lastmile-area-title .lastmile-eyebrow {
    margin-bottom: 3px;
    color: #a68c61;
    font-size: 8px;
}

#lgLastMile .lastmile-area-title p:not(.lastmile-eyebrow) {
    margin-top: 4px;
    color: #8f8579;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgLastMile .lastmile-area-tags {
    gap: 6px;
}

#lgLastMile .lastmile-area-tags span {
    min-height: 25px;
    padding: 5px 9px;
    border: 1px solid #e8e2da;
    border-radius: 999px;
    background: #fbfbfc;
    color: #756b60;
    font-size: 8.5px;
    font-weight: 650;
}

#lgLastMile .lastmile-area-tags .is-serviceable {
    border-color: #d5e5da;
    background: #f0f8f2;
    color: #4f7c5b;
}

#lgLastMile .lastmile-area-tags .is-unserved {
    border-color: #ecd5cf;
    background: #fff4f2;
    color: #a85f58;
}

/* UNIFIED AREA LAYOUT */

#lgLastMile .lastmile-area-layout {
    display: grid;
    grid-template-columns: minmax(0,1fr) 294px;
    min-width: 0;
}

#lgLastMile .lastmile-area-main {
    min-width: 0;
    padding: 16px;
}

#lgLastMile .lastmile-dispatch-panel {
    min-width: 0;
    padding: 16px;
    border-left: 1px solid #eee8df;
    background: #fcfcfd;
}

/* ROUTE SUMMARY */

#lgLastMile .lastmile-route-summary {
    display: grid;
    grid-template-columns: minmax(0,1fr) auto minmax(0,1fr);
    align-items: start;
    gap: 13px;
    padding-bottom: 14px;
    border-bottom: 1px solid #eee8df;
}

#lgLastMile .lastmile-route-summary > div {
    min-width: 0;
}

#lgLastMile .lastmile-label {
    display: block;
    margin-bottom: 5px;
    color: #958879;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .065em;
    line-height: 1.5;
    text-transform: uppercase;
}

#lgLastMile .lastmile-route-summary strong {
    color: #443c33;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgLastMile .lastmile-route-summary p {
    margin-top: 4px;
    color: #91877b;
    font-size: 9px;
    line-height: 1.6;
}

#lgLastMile .lastmile-route-arrow {
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

/* PARCELS */

#lgLastMile .lastmile-parcel-heading {
    margin: 15px 0 10px;
}

#lgLastMile .lastmile-parcel-heading h4 {
    color: #403930;
    font-size: 10px;
    font-weight: 700;
}

#lgLastMile .lastmile-parcel-heading p {
    margin-top: 4px;
    color: #968b7f;
    font-size: 9px;
    line-height: 1.5;
}

#lgLastMile .lastmile-parcel-heading > span {
    min-width: 24px;
    padding: 5px 8px;
    border-radius: 7px;
    font-size: 8.5px;
}

#lgLastMile .lastmile-parcel-list {
    gap: 8px;
}

#lgLastMile .lastmile-parcel {
    min-width: 0;
    gap: 8px 12px;
    padding: 11px 12px;
    border: 1px solid #e9e4dd;
    border-radius: 10px;
    background: #fbfbfc;
}

#lgLastMile .lastmile-parcel-main {
    min-width: 0;
}

#lgLastMile .lastmile-parcel-main strong {
    color: #4b433a;
    font-size: 10px;
    font-weight: 650;
}

#lgLastMile .lastmile-parcel-main p {
    margin-top: 4px;
    color: #8f8579;
    font-size: 9px;
}

#lgLastMile .lastmile-parcel-status {
    padding: 5px 9px;
    font-size: 8px;
    line-height: 1.4;
    white-space: normal;
}

#lgLastMile .lastmile-parcel-address {
    padding-top: 8px;
    border-top-color: #eee8df;
}

#lgLastMile .lastmile-parcel-address p {
    color: #81786d;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

/* EXPANDABLE DETAILS */

#lgLastMile .lastmile-details summary {
    gap: 7px;
    color: #a47118;
    font-size: 9px;
    font-weight: 650;
}

#lgLastMile .lastmile-detail-body {
    min-width: 0;
    padding: 12px;
    border: 1px solid #e8e2d9;
    border-radius: 9px;
    background: white;
}

#lgLastMile .lastmile-detail-grid {
    grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 12px;
}

#lgLastMile .lastmile-detail-grid > div {
    min-width: 0;
}

#lgLastMile .lastmile-detail-grid strong {
    color: #50473d;
    font-size: 9px;
    overflow-wrap: anywhere;
}

#lgLastMile .lastmile-detail-grid p {
    margin-top: 4px;
    color: #8f8276;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

/* RIDER DISPATCH PANEL */

#lgLastMile .lastmile-dispatch-panel h4 {
    color: #37312a;
    font-size: 12px;
    font-weight: 700;
}

#lgLastMile .lastmile-dispatch-note {
    margin: 7px 0 15px;
    color: #908679;
    font-size: 9px;
    line-height: 1.6;
}

#lgLastMile .lastmile-dispatch-panel select {
    width: 100%;
    min-height: 40px;
    padding: 8px 11px;
    border: 1px solid #dfe2e6;
    border-radius: 10px;
    background: white;
    color: #50483e;
    font-size: 10px;
}

#lgLastMile .lastmile-rider-preview {
    min-width: 0;
    gap: 10px;
    margin-top: 12px;
    padding: 12px;
    border: 1px solid #e8e2d9;
    border-radius: 10px;
    background: white;
}

#lgLastMile .lastmile-rider-preview > div {
    min-width: 0;
}

#lgLastMile .lastmile-rider-avatar {
    display: grid;
    place-items: center;
    flex: 0 0 34px;
    width: 34px;
    height: 34px;
    border: 1px solid #eee3ce;
    border-radius: 10px;
    background: #fff5e1;
    color: #a97312;
    font-size: 11px;
    font-weight: 700;
}

#lgLastMile .lastmile-rider-preview strong {
    color: #443c33;
    font-size: 10px;
    font-weight: 700;
}

#lgLastMile .lastmile-rider-preview p {
    margin-top: 4px;
    color: #897f74;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgLastMile .lastmile-dispatch-total {
    margin-top: 15px;
    padding: 13px 0;
    border-top: 1px solid #eee8df;
    color: #807669;
    font-size: 9px;
}

#lgLastMile .lastmile-dispatch-total strong {
    color: #30271d;
    font-size: 19px;
    font-weight: 750;
}

#lgLastMile .lastmile-dispatch-panel .lastmile-button {
    width: 100%;
    min-height: 40px;
    font-size: 10px;
    font-weight: 700;
}

#lgLastMile .lastmile-dispatch-footnote {
    margin-top: 9px;
    color: #958a7e;
    font-size: 9px;
    line-height: 1.6;
}

#lgLastMile .lastmile-rider-warning {
    margin-top: 12px;
    padding: 12px;
    border: 1px solid #ecd5cf;
    border-radius: 10px;
    background: #fff7f4;
    color: #96625d;
}

#lgLastMile .lastmile-rider-warning strong {
    font-size: 10px;
    font-weight: 700;
}

#lgLastMile .lastmile-rider-warning p {
    margin-top: 6px;
    font-size: 9px;
    line-height: 1.6;
}

/* ASSIGNMENT HISTORY */

#lgLastMile .lastmile-assignment-card {
    display: grid;
    grid-template-columns:
        minmax(0,.8fr)
        minmax(0,1.45fr)
        minmax(0,1fr)
        minmax(65px,.45fr)
        minmax(105px,.7fr);
    align-items: center;
    gap: 13px;
    min-width: 0;
    padding: 14px 15px;
    border: 1px solid #e8e2d9;
    border-radius: 12px;
    background: white;
    transition:
        transform .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgLastMile .lastmile-assignment-card > div {
    min-width: 0;
}

#lgLastMile .lastmile-assignment-card strong {
    color: #443c33;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgLastMile .lastmile-assignment-card p {
    margin-top: 4px;
    color: #91877b;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

#lgLastMile .lastmile-assignment-route {
    grid-template-columns: minmax(0,1fr) auto minmax(0,1fr);
    gap: 8px;
    min-width: 0;
}

#lgLastMile .lastmile-assignment-route > div {
    min-width: 0;
}

#lgLastMile .lastmile-mini-arrow {
    color: #be8b30;
    font-size: 14px;
}

#lgLastMile .lastmile-assignment-rider {
    min-width: 0;
    gap: 9px;
}

#lgLastMile .lastmile-assignment-rider > div {
    min-width: 0;
}

#lgLastMile .lastmile-assignment-load strong {
    color: #30271d;
    font-size: 18px;
    font-weight: 750;
}

#lgLastMile .lastmile-status {
    min-height: 25px;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 8.5px;
    font-weight: 700;
    line-height: 1.4;
}

/* EMPTY STATES / FOOTER */

#lgLastMile .lastmile-empty {
    padding: 40px 20px;
}

#lgLastMile .lastmile-empty-icon {
    width: 43px;
    height: 43px;
    border-radius: 12px;
    background: #fff7e8;
}

#lgLastMile .lastmile-empty h3 {
    color: #51483e;
    font-size: 11px;
    font-weight: 700;
}

#lgLastMile .lastmile-empty p {
    color: #948a7e;
    font-size: 9px;
    line-height: 1.6;
}

#lgLastMile .lastmile-list-footer {
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

/* HOVER */

@media (hover:hover) and (pointer:fine) {
    #lgLastMile .lastmile-stat:hover,
    #lgLastMile .lastmile-area-card:hover,
    #lgLastMile .lastmile-assignment-card:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43,33,21,.055);
    }
}

/* RESPONSIVE */

@media (max-width:1200px) {
    #lgLastMile .lastmile-area-layout {
        grid-template-columns: minmax(0,1fr) 270px;
    }
}

@media (max-width:1050px) {
    #lgLastMile .lastmile-area-layout {
        grid-template-columns: 1fr;
    }

    #lgLastMile .lastmile-dispatch-panel {
        border-left: 0;
        border-top: 1px solid #eee8df;
    }

    #lgLastMile .lastmile-assignment-card {
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 15px;
    }
}

@media (max-width:900px) {
    #lgLastMile .lastmile-stats {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }
}

@media (max-width:767px) {
    #lgLastMile .lastmile-page-header,
    #lgLastMile .lastmile-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    #lgLastMile .lastmile-header-actions,
    #lgLastMile .lastmile-search {
        width: 100%;
    }

    #lgLastMile .lastmile-header-actions .lastmile-button {
        flex: 1;
    }

    #lgLastMile .lastmile-filter-row {
        flex-direction: column;
        align-items: stretch;
        padding: 0 14px 13px;
    }

    #lgLastMile .lastmile-area-list,
    #lgLastMile .lastmile-assignment-list {
        padding: 10px;
    }

    #lgLastMile .lastmile-area-main,
    #lgLastMile .lastmile-dispatch-panel {
        padding: 13px;
    }

    #lgLastMile .lastmile-flow {
        overflow-x: auto;
    }

    #lgLastMile .lastmile-flow :is(a,span) {
        flex-shrink: 0;
    }
}

@media (max-width:520px) {
    #lgLastMile .lastmile-header-actions {
        flex-direction: column;
    }

    #lgLastMile .lastmile-header-actions .lastmile-button {
        width: 100%;
    }

    #lgLastMile .lastmile-stat {
        padding: 12px;
    }

    #lgLastMile .lastmile-stat strong {
        font-size: 22px;
    }

    #lgLastMile .lm-stat-icon {
        flex-basis: 34px;
        width: 34px;
        height: 34px;
    }

    #lgLastMile .lastmile-route-summary {
        grid-template-columns: 1fr;
    }

    #lgLastMile .lastmile-route-arrow {
        transform: rotate(90deg);
        justify-self: start;
    }

    #lgLastMile .lastmile-detail-grid,
    #lgLastMile .lastmile-assignment-card {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion:reduce) {
    #lgLastMile *,
    #lgLastMile *::before,
    #lgLastMile *::after {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }

    #lgLastMile .lastmile-stat:hover,
    #lgLastMile .lastmile-area-card:hover,
    #lgLastMile .lastmile-assignment-card:hover {
        transform: none !important;
    }
}
</style>
@endpush

@section('content')

@php
    $readyParcelCount = $deliveryGroups->sum(
        fn ($group) => $group->count()
    );

    $deliveryAreaCount = $deliveryGroups->count();

    $availableRiderCount = $riders
        ->reject(
            fn ($rider) => $busyRiderIds->contains((int) $rider->id)
        )
        ->count();

    $activeAssignmentCount = $assignments
        ->whereIn('status', ['assigned', 'out_for_delivery'])
        ->count();

    $completedAssignmentCount = $assignments
        ->where('status', 'completed')
        ->count();

    $summaryCards = [
        [
            'Ready Parcels',
            $readyParcelCount,
            'At destination hubs',
            'amber',
        ],
        [
            'Delivery Areas',
            $deliveryAreaCount,
            'Barangay dispatch groups',
            'neutral',
        ],
        [
            'Available Riders',
            $availableRiderCount,
            'Online and without active batch',
            'blue',
        ],
        [
            'Active Batches',
            $activeAssignmentCount,
            'Assigned or out for delivery',
            'green',
        ],
    ];

    $serviceableGroupCount = 0;
    $unservedGroupCount = 0;

    foreach ($deliveryGroups as $group) {
        $first = $group->first();

        $groupEligibleRiders = $riders->filter(
            function ($rider) use ($first, $busyRiderIds) {
                return \App\Support\LogisticsServiceArea::matchesRider($rider, $first)
                    && ! $busyRiderIds->contains((int) $rider->id);
            }
        );

        if ($groupEligibleRiders->isNotEmpty()) {
            $serviceableGroupCount++;
        } else {
            $unservedGroupCount++;
        }
    }
@endphp

<div
    id="lgLastMile"
    class="lastmile-page"
    data-lastmile-workspace
>
    {{-- PAGE HEADING --}}

    <header class="lastmile-page-header">
        <div class="min-w-0">
            <p class="lastmile-eyebrow">
                DESTINATION HUB DISPATCH
            </p>

            <h1>
                Last Mile <span>Assignment</span>
            </h1>

            <p class="lastmile-subtitle">
                Dispatch destination-hub parcels to local Riders by
                municipality and barangay. Orders within the same delivery
                area can be assigned together as one Rider batch.
            </p>
        </div>

        <div class="lastmile-header-actions">
            <a
                href="{{ route('logistics.hub-transfers') }}"
                class="lastmile-button"
            >
                <span aria-hidden="true">←</span>
                Hub Transfers
            </a>

            <a
                href="{{ route('logistics.delivery-monitoring') }}"
                class="lastmile-button lastmile-button-primary"
            >
                Delivery Monitoring
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </header>

    {{-- FLASH MESSAGES --}}

    @if(session('success'))
        <div
            class="lastmile-alert lastmile-alert-success"
            role="status"
        >
            <strong>Last-mile assignment updated</strong>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div
            class="lastmile-alert lastmile-alert-danger"
            role="alert"
        >
            <strong>Could not assign Last-Mile Rider</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KPI CARDS --}}

    <section
        class="lastmile-stats"
        aria-label="Last-mile assignment overview"
    >
        @foreach($summaryCards as [$label, $value, $note, $tone])
            <article class="lastmile-stat lastmile-stat-{{ $tone }}">
                <div class="min-w-0">
                    <p>{{ $label }}</p>

                    <strong>
                        {{ number_format($value) }}
                    </strong>

                    <span>{{ $note }}</span>
                </div>

                <span
                    class="lm-stat-icon"
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
                                <path d="M4 5h16v14H4zM8 9h8M8 13h8"/>
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
                                <rect x="3" y="5" width="8" height="7" rx="1"/>
                                <rect x="13" y="5" width="8" height="7" rx="1"/>
                                <rect x="8" y="14" width="8" height="7" rx="1"/>
                            </svg>
                    @endswitch
                </span>
            </article>
        @endforeach
    </section>

    {{-- WORKFLOW STEPPER --}}

    <nav
        class="lastmile-flow"
        aria-label="Destination hub workflow"
    >
        <a href="{{ route('logistics.parcel-sorting') }}">
            <b>1</b>
            Origin Sorting
        </a>

        <a href="{{ route('logistics.hub-transfers') }}">
            <b>2</b>
            Hub Transfer
        </a>

        <span class="is-current" aria-current="step">
            <b>3</b>
            Last-Mile Assignment
        </span>

        <a href="{{ route('logistics.delivery-monitoring') }}">
            <b>4</b>
            Delivery Monitoring
        </a>
    </nav>

    {{-- BARANGAY DISPATCH QUEUE --}}

    <section
        class="lastmile-workspace"
        aria-labelledby="lastmile-queue-heading"
    >
        <header class="lastmile-toolbar">
            <div>
                <p class="lastmile-eyebrow">
                    DESTINATION DISPATCH
                </p>

                <h2 id="lastmile-queue-heading">
                    Barangay Dispatch Queue
                </h2>

                <p>
                    Destination-hub parcels are grouped by their
                    local service area before Rider assignment.
                </p>
            </div>

            <label class="lastmile-search">
                <span class="sr-only">
                    Search Last-Mile dispatch queue
                </span>

                <input
                    type="search"
                    data-area-search
                    autocomplete="off"
                    placeholder="Search barangay, hub, order, or Buyer…"
                >
            </label>
        </header>

        {{-- FILTERS --}}

        <div class="lastmile-filter-row">
            <div
                class="lastmile-tabs"
                role="group"
                aria-label="Filter delivery areas"
            >
                <button
                    type="button"
                    data-area-filter="all"
                    aria-pressed="true"
                >
                    All Areas
                    <span>{{ $deliveryAreaCount }}</span>
                </button>

                <button
                    type="button"
                    data-area-filter="serviceable"
                    aria-pressed="false"
                >
                    Rider Available
                    <span>{{ $serviceableGroupCount }}</span>
                </button>

                <button
                    type="button"
                    data-area-filter="unserved"
                    aria-pressed="false"
                >
                    Needs Rider
                    <span>{{ $unservedGroupCount }}</span>
                </button>
            </div>

            <button
                type="button"
                class="lastmile-text-link"
                data-area-clear
            >
                Clear filters
            </button>
        </div>

        {{-- DELIVERY AREA GROUPS --}}

        <div class="lastmile-area-list">
            @forelse($deliveryGroups as $groupKey => $group)
                @php
                    $first = $group->first();

                    $eligibleRiders = $riders
                        ->filter(
                            function ($rider) use ($first, $busyRiderIds) {
                                return \App\Support\LogisticsServiceArea::matchesRider($rider, $first)
                                    && ! $busyRiderIds->contains((int) $rider->id);
                            }
                        )
                        ->values();

                    $allAreaRiders = $riders->filter(
                        fn ($rider) => \App\Support\LogisticsServiceArea::matchesRider($rider, $first)
                    );

                    $busyAreaRiderCount = $allAreaRiders
                        ->filter(
                            fn ($rider) => $busyRiderIds->contains(
                                (int) $rider->id
                            )
                        )
                        ->count();

                    $areaState = $eligibleRiders->isNotEmpty()
                        ? 'serviceable'
                        : 'unserved';

                    $searchValues = [
                        $first->barangay_name,
                        $first->destination_facility_name,
                        $first->destination_municipality,
                    ];

                    foreach($group as $parcel) {
                        $searchValues[] = $parcel->order_number;
                        $searchValues[] = $parcel->buyer_name;
                        $searchValues[] = $parcel->buyer_address;
                        $searchValues[] = $parcel->pickup_rider_name;
                    }

                    $searchText = implode(
                        ' ',
                        array_filter($searchValues)
                    );

                    $formId = 'lastmile-form-' . md5($groupKey);
                @endphp

                <article
                    class="lastmile-area-card"
                    data-area-card
                    data-area-state="{{ $areaState }}"
                    data-search="{{ $searchText }}"
                >
                    {{-- AREA HEADER --}}

                    <header class="lastmile-area-header">
                        <div class="lastmile-area-title">
                            <span
                                class="lastmile-area-icon"
                                aria-hidden="true"
                            >
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            trim((string) $first->barangay_name),
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>

                            <div class="min-w-0">
                                <p class="lastmile-eyebrow">
                                    LOCAL DELIVERY AREA
                                </p>

                                <h3>
                                    {{ $first->barangay_name }}
                                </h3>

                                <p>
                                    {{ $first->destination_facility_name }}
                                    <span aria-hidden="true">·</span>
                                    {{ $first->destination_municipality }}
                                </p>
                            </div>
                        </div>

                        <div class="lastmile-area-tags">
                            <span>
                                {{ $group->count() }}
                                {{ $group->count() === 1 ? 'parcel' : 'parcels' }}
                            </span>

                            @if($eligibleRiders->isNotEmpty())
                                <span class="is-serviceable">
                                    {{ $eligibleRiders->count() }}
                                    {{
                                        $eligibleRiders->count() === 1
                                            ? 'Rider'
                                            : 'Riders'
                                    }}
                                    available
                                </span>
                            @else
                                <span class="is-unserved">
                                    No Rider available
                                </span>
                            @endif
                        </div>
                    </header>

                    {{-- AREA CONTENT --}}

                    <div class="lastmile-area-layout">
                        {{-- LEFT: PARCELS --}}

                        <div class="lastmile-area-main">
                            <div class="lastmile-route-summary">
                                <div>
                                    <span class="lastmile-label">
                                        Destination Hub
                                    </span>

                                    <strong>
                                        {{ $first->destination_facility_name }}
                                    </strong>

                                    <p>
                                        {{ $first->destination_municipality }}
                                    </p>
                                </div>

                                <span
                                    class="lastmile-route-arrow"
                                    aria-hidden="true"
                                >
                                    →
                                </span>

                                <div>
                                    <span class="lastmile-label">
                                        Local Delivery Area
                                    </span>

                                    <strong>
                                        Barangay {{ $first->barangay_name }}
                                    </strong>

                                    <p>
                                        One local Rider may deliver the entire batch.
                                    </p>
                                </div>
                            </div>

                            <div class="lastmile-parcel-heading">
                                <div>
                                    <h4>
                                        Parcels in This Delivery Batch
                                    </h4>

                                    <p>
                                        Different Buyers can share the same local
                                        Rider when the barangay matches.
                                    </p>
                                </div>

                                <span>
                                    {{ $group->count() }}
                                </span>
                            </div>

                            {{-- PARCEL LIST --}}

                            <div class="lastmile-parcel-list">
                                @foreach($group as $parcel)
                                    <input
                                        form="{{ $formId }}"
                                        type="hidden"
                                        name="order_ids[]"
                                        value="{{ $parcel->order_id }}"
                                    >

                                    <article class="lastmile-parcel">
                                        <div class="lastmile-parcel-main">
                                            <strong>
                                                {{ $parcel->order_number }}
                                            </strong>

                                            <p>
                                                {{ $parcel->buyer_name }}
                                            </p>
                                        </div>

                                        <span class="lastmile-parcel-status">
                                            DESTINATION RECEIVED
                                        </span>

                                        <div class="lastmile-parcel-address">
                                            <span class="lastmile-label">
                                                Delivery Address
                                            </span>

                                            <p>
                                                {{ $parcel->buyer_address }}
                                            </p>
                                        </div>

                                        <details class="lastmile-details">
                                            <summary>
                                                Handoff Details
                                                <span aria-hidden="true">+</span>
                                            </summary>

                                            <div class="lastmile-detail-body">
                                                <div class="lastmile-detail-grid">
                                                    <div>
                                                        <span class="lastmile-label">
                                                            Buyer
                                                        </span>

                                                        <strong>
                                                            {{ $parcel->buyer_name }}
                                                        </strong>

                                                        <p>
                                                            {{ $parcel->buyer_address }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <span class="lastmile-label">
                                                            Pickup Rider Snapshot
                                                        </span>

                                                        <strong>
                                                            {{
                                                                $parcel->pickup_rider_name
                                                                ?: 'Pickup Rider'
                                                            }}
                                                        </strong>

                                                        <p>
                                                            {{
                                                                $parcel->pickup_rider_email
                                                                ?: 'No pickup Rider email'
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <span class="lastmile-label">
                                                            Destination Hub
                                                        </span>

                                                        <strong>
                                                            {{ $parcel->destination_facility_name }}
                                                        </strong>

                                                        <p>
                                                            {{ $parcel->destination_municipality }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <span class="lastmile-label">
                                                            Last-Mile Area
                                                        </span>

                                                        <strong>
                                                            Barangay {{ $parcel->barangay_name }}
                                                        </strong>

                                                        <p>
                                                            Local Rider service area
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        {{-- RIGHT: RIDER DISPATCH --}}

                        <aside class="lastmile-dispatch-panel">
                            <h4>
                                Assign Local Rider
                            </h4>

                            <p class="lastmile-dispatch-note">
                                Only Riders assigned to
                                {{ $first->barangay_name }},
                                {{ $first->destination_municipality }}
                                can receive this batch.
                            </p>

                            <form
                                id="{{ $formId }}"
                                method="POST"
                                action="{{ route('logistics.last-mile-assignment.batch-assign') }}"
                                data-lastmile-form
                                data-area-name="{{ $first->barangay_name }}"
                            >
                                @csrf

                                <label>
                                    <span class="lastmile-label">
                                        Local Rider
                                    </span>

                                    <select
                                        name="rider_id"
                                        required
                                        data-lastmile-rider
                                        @disabled($eligibleRiders->isEmpty())
                                    >
                                        <option value="">
                                            Select local Rider
                                        </option>

                                        @foreach($eligibleRiders as $rider)
                                            @php
                                                $riderName = trim(
                                                    $rider->first_name
                                                    . ' '
                                                    . $rider->last_name
                                                );
                                            @endphp

                                            <option
                                                value="{{ $rider->id }}"
                                                data-rider-name="{{ $riderName }}"
                                                data-rider-area="{{ $rider->barangay_name }}, {{ $rider->municipality_name }}"
                                                data-rider-vehicle="{{ $rider->vehicle_type }}"
                                            >
                                                {{ $riderName }}

                                                @if($rider->vehicle_type)
                                                    — {{ $rider->vehicle_type }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </label>

                                {{-- RIDER PREVIEW --}}

                                <div
                                    class="lastmile-rider-preview"
                                    data-rider-preview
                                    hidden
                                >
                                    <span
                                        class="lastmile-rider-avatar"
                                        data-rider-initial
                                        aria-hidden="true"
                                    >
                                        R
                                    </span>

                                    <div class="min-w-0">
                                        <strong data-rider-preview-name></strong>
                                        <p data-rider-preview-area></p>
                                        <p data-rider-preview-vehicle></p>
                                    </div>
                                </div>

                                <div class="lastmile-dispatch-total">
                                    <span>
                                        Delivery Batch
                                    </span>

                                    <strong>
                                        {{ $group->count() }}
                                    </strong>
                                </div>

                                <button
                                    type="submit"
                                    class="lastmile-button lastmile-button-primary"
                                    @disabled($eligibleRiders->isEmpty())
                                >
                                    Assign {{ $group->count() }}
                                    {{
                                        $group->count() === 1
                                            ? 'Parcel'
                                            : 'Parcels'
                                    }}
                                </button>

                                <p class="lastmile-dispatch-footnote">
                                    This creates one Last-Mile Assignment
                                    for the selected local Rider.
                                </p>
                            </form>

                            {{-- NO RIDER AVAILABLE --}}

                            @if($eligibleRiders->isEmpty())
                                <div class="lastmile-rider-warning">
                                    <strong>
                                        No Available Rider
                                    </strong>

                                    <p>
                                        No free, online Rider is currently
                                        available for
                                        {{ $first->barangay_name }}.
                                    </p>

                                    @if($busyAreaRiderCount > 0)
                                        <p>
                                            {{ $busyAreaRiderCount }}
                                            local
                                            {{
                                                $busyAreaRiderCount === 1
                                                    ? 'Rider is'
                                                    : 'Riders are'
                                            }}
                                            currently handling another
                                            Last-Mile batch.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </aside>
                    </div>
                </article>
            @empty
                <div class="lastmile-empty">
                    <span
                        class="lastmile-empty-icon"
                        aria-hidden="true"
                    >
                        ✓
                    </span>

                    <h3>
                        Last-Mile Dispatch Queue Is Clear
                    </h3>

                    <p>
                        Parcels appear here after a Hub Transfer is
                        physically received at its destination facility.
                    </p>

                    <a
                        href="{{ route('logistics.hub-transfers') }}"
                        class="lastmile-button lastmile-button-primary"
                    >
                        View Hub Transfers
                    </a>
                </div>
            @endforelse

            <div
                class="lastmile-empty"
                data-area-empty
                hidden
            >
                <h3>
                    No Matching Delivery Areas
                </h3>

                <p>
                    Try another barangay, destination hub,
                    order number, or Buyer.
                </p>

                <button
                    type="button"
                    class="lastmile-text-link"
                    data-area-clear
                >
                    Clear filters
                </button>
            </div>
        </div>

        <footer class="lastmile-list-footer">
            <span
                data-area-visible-count
                aria-live="polite"
            >
                {{ $deliveryAreaCount }} delivery areas shown
            </span>

            <span>
                Exact municipality + barangay Rider matching
            </span>
        </footer>
    </section>

    {{-- EXISTING LAST-MILE ASSIGNMENTS --}}

    <section
        class="lastmile-workspace lastmile-assignment-history"
        aria-labelledby="lastmileHistoryHeading"
    >
        <header class="lastmile-toolbar">
            <div>
                <p class="lastmile-eyebrow">
                    ASSIGNMENT TRACKING
                </p>

                <h2 id="lastmileHistoryHeading">
                    Last-Mile Assignments
                </h2>

                <p>
                    Track Rider batches after destination-hub dispatch.
                </p>
            </div>

            <label class="lastmile-search">
                <span class="sr-only">
                    Search Last-Mile assignments
                </span>

                <input
                    type="search"
                    data-assignment-search
                    autocomplete="off"
                    placeholder="Search assignment, Rider, or area…"
                >
            </label>
        </header>

        @php
            $assignedCount = $assignments
                ->where('status', 'assigned')
                ->count();

            $outForDeliveryCount = $assignments
                ->where('status', 'out_for_delivery')
                ->count();
        @endphp

        <div class="lastmile-filter-row">
            <div
                class="lastmile-tabs"
                role="group"
                aria-label="Filter Last-Mile assignments"
            >
                <button
                    type="button"
                    data-assignment-filter="all"
                    aria-pressed="true"
                >
                    All
                    <span>{{ $assignments->count() }}</span>
                </button>

                <button
                    type="button"
                    data-assignment-filter="assigned"
                    aria-pressed="false"
                >
                    Assigned
                    <span>{{ $assignedCount }}</span>
                </button>

                <button
                    type="button"
                    data-assignment-filter="out_for_delivery"
                    aria-pressed="false"
                >
                    Out for Delivery
                    <span>{{ $outForDeliveryCount }}</span>
                </button>

                <button
                    type="button"
                    data-assignment-filter="completed"
                    aria-pressed="false"
                >
                    Completed
                    <span>{{ $completedAssignmentCount }}</span>
                </button>
            </div>

            <button
                type="button"
                class="lastmile-text-link"
                data-assignment-clear
            >
                Clear filters
            </button>
        </div>

        {{-- ASSIGNMENT LIST --}}

        <div class="lastmile-assignment-list">
            @forelse($assignments as $assignment)
                @php
                    $assignmentSearch = implode(
                        ' ',
                        array_filter([
                            $assignment->assignment_code,
                            $assignment->facility_name,
                            $assignment->delivery_area,
                            $assignment->first_name,
                            $assignment->last_name,
                            $assignment->vehicle_type,
                            $assignment->status,
                        ])
                    );

                    $assignmentStatusLabel = ucwords(
                        str_replace('_', ' ', $assignment->status)
                    );
                @endphp

                <article
                    class="lastmile-assignment-card"
                    data-assignment-card
                    data-assignment-state="{{ $assignment->status }}"
                    data-search="{{ $assignmentSearch }}"
                >
                    <div class="lastmile-assignment-code">
                        <span class="lastmile-label">
                            Assignment
                        </span>

                        <strong>
                            {{ $assignment->assignment_code }}
                        </strong>
                    </div>

                    <div class="lastmile-assignment-route">
                        <div>
                            <span class="lastmile-label">
                                Destination Hub
                            </span>

                            <strong>
                                {{ $assignment->facility_name }}
                            </strong>
                        </div>

                        <span
                            class="lastmile-mini-arrow"
                            aria-hidden="true"
                        >
                            →
                        </span>

                        <div>
                            <span class="lastmile-label">
                                Delivery Area
                            </span>

                            <strong>
                                Barangay {{ $assignment->delivery_area }}
                            </strong>
                        </div>
                    </div>

                    <div class="lastmile-assignment-rider">
                        <span
                            class="lastmile-rider-avatar"
                            aria-hidden="true"
                        >
                            {{
                                mb_strtoupper(
                                    mb_substr(
                                        trim((string) $assignment->first_name),
                                        0,
                                        1
                                    )
                                )
                            }}
                        </span>

                        <div class="min-w-0">
                            <span class="lastmile-label">
                                Local Rider
                            </span>

                            <strong>
                                {{ $assignment->first_name }}
                                {{ $assignment->last_name }}
                            </strong>

                            <p>
                                {{
                                    $assignment->vehicle_type
                                    ?: 'Vehicle not recorded'
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="lastmile-assignment-load">
                        <span class="lastmile-label">
                            Batch Load
                        </span>

                        <strong>
                            {{ $assignment->order_count }}
                        </strong>

                        <p>
                            {{
                                $assignment->order_count === 1
                                    ? 'parcel'
                                    : 'parcels'
                            }}
                        </p>
                    </div>

                    <div class="lastmile-assignment-status">
                        <span
                            class="lastmile-status lastmile-status-{{ $assignment->status }}"
                        >
                            {{ $assignmentStatusLabel }}
                        </span>
                    </div>
                </article>
            @empty
                <div class="lastmile-empty">
                    <h3>
                        No Last-Mile Assignments Yet
                    </h3>

                    <p>
                        Assigned Rider batches will appear here.
                    </p>
                </div>
            @endforelse

            <div
                class="lastmile-empty"
                data-assignment-empty
                hidden
            >
                <h3>
                    No Matching Last-Mile Assignments
                </h3>

                <p>
                    Try another assignment code, Rider,
                    hub, or delivery area.
                </p>

                <button
                    type="button"
                    class="lastmile-text-link"
                    data-assignment-clear
                >
                    Clear filters
                </button>
            </div>
        </div>

        <footer class="lastmile-list-footer">
            <span
                data-assignment-visible-count
                aria-live="polite"
            >
                {{ $assignments->count() }} assignments shown
            </span>

            <a
                href="{{ route('logistics.delivery-monitoring') }}"
                class="lastmile-text-link"
            >
                Open Delivery Monitoring →
            </a>
        </footer>
    </section>
</div>

@endsection

@push('scripts')
<script>
(() => {
    'use strict';

    function initializeLastMileAssignment() {
        const root = document.querySelector(
            '[data-lastmile-workspace]'
        );

        if (!root || root.dataset.initialized) {
            return;
        }

        root.dataset.initialized = 'true';

        const normalize = value =>
            String(value || '').toLocaleLowerCase().trim();

        /*
        |--------------------------------------------------------------------------
        | DELIVERY AREA SEARCH + FILTER
        |--------------------------------------------------------------------------
        */

        const areaCards = [
            ...root.querySelectorAll('[data-area-card]')
        ];

        const areaSearch = root.querySelector(
            '[data-area-search]'
        );

        const areaTabs = [
            ...root.querySelectorAll('[data-area-filter]')
        ];

        const areaEmpty = root.querySelector(
            '[data-area-empty]'
        );

        const areaVisibleCount = root.querySelector(
            '[data-area-visible-count]'
        );

        let areaFilter = 'all';

        function applyAreaFilters() {
            const keyword = normalize(areaSearch?.value);
            let shown = 0;

            areaCards.forEach(card => {
                const matchesState =
                    areaFilter === 'all'
                    || card.dataset.areaState === areaFilter;

                const matchesSearch =
                    normalize(card.dataset.search).includes(keyword);

                card.hidden = !matchesState || !matchesSearch;

                if (!card.hidden) {
                    shown++;
                }
            });

            areaTabs.forEach(tab => {
                tab.setAttribute(
                    'aria-pressed',
                    String(tab.dataset.areaFilter === areaFilter)
                );
            });

            if (areaEmpty) {
                areaEmpty.hidden = areaCards.length === 0 || shown > 0;
            }

            if (areaVisibleCount) {
                areaVisibleCount.textContent =
                    `${shown} of ${areaCards.length} `
                    + `${areaCards.length === 1 ? 'delivery area' : 'delivery areas'} shown`;
            }
        }

        areaTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                areaFilter = tab.dataset.areaFilter || 'all';
                applyAreaFilters();
            });
        });

        areaSearch?.addEventListener('input', applyAreaFilters);

        root.querySelectorAll('[data-area-clear]').forEach(button => {
            button.addEventListener('click', () => {
                areaFilter = 'all';

                if (areaSearch) {
                    areaSearch.value = '';
                }

                applyAreaFilters();
            });
        });

        /*
        |--------------------------------------------------------------------------
        | RIDER PREVIEW
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll('[data-lastmile-rider]').forEach(select => {
            const form = select.closest('[data-lastmile-form]');

            const preview = form?.querySelector(
                '[data-rider-preview]'
            );

            const initial = form?.querySelector(
                '[data-rider-initial]'
            );

            const name = form?.querySelector(
                '[data-rider-preview-name]'
            );

            const area = form?.querySelector(
                '[data-rider-preview-area]'
            );

            const vehicle = form?.querySelector(
                '[data-rider-preview-vehicle]'
            );

            function renderRider() {
                const option = select.options[select.selectedIndex];

                if (!option || !option.value) {
                    if (preview) {
                        preview.hidden = true;
                    }

                    return;
                }

                const riderName =
                    option.dataset.riderName
                    || option.textContent.trim();

                if (name) {
                    name.textContent = riderName;
                }

                if (area) {
                    area.textContent =
                        option.dataset.riderArea
                        || 'Local delivery area';
                }

                if (vehicle) {
                    vehicle.textContent =
                        option.dataset.riderVehicle
                        || 'Vehicle not recorded';
                }

                if (initial) {
                    initial.textContent =
                        riderName.trim().charAt(0).toUpperCase()
                        || 'R';
                }

                if (preview) {
                    preview.hidden = false;
                }
            }

            select.addEventListener('change', renderRider);
            renderRider();
        });

        /*
        |--------------------------------------------------------------------------
        | WHOLE-BATCH ASSIGNMENT CONFIRMATION
        |--------------------------------------------------------------------------
        */

        root.querySelectorAll('[data-lastmile-form]').forEach(form => {
            form.addEventListener('submit', event => {
                const select = form.querySelector(
                    '[data-lastmile-rider]'
                );

                if (!select || !select.value) {
                    return;
                }

                const option = select.options[select.selectedIndex];

                const riderName =
                    option.dataset.riderName
                    || 'this Rider';

                const area =
                    form.dataset.areaName
                    || 'this delivery area';

                const parcelCount = form.querySelectorAll(
                    'input[name="order_ids[]"]'
                ).length || root.querySelectorAll(
                    `input[name="order_ids[]"][form="${form.id}"]`
                ).length;

                const confirmed = window.confirm(
                    `Assign ${parcelCount} `
                    + `${parcelCount === 1 ? 'parcel' : 'parcels'} `
                    + `in ${area} to ${riderName}?`
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
                    button.textContent = 'Assigning Rider…';
                }
            });
        });

        /*
        |--------------------------------------------------------------------------
        | ASSIGNMENT HISTORY SEARCH + FILTER
        |--------------------------------------------------------------------------
        */

        const assignmentCards = [
            ...root.querySelectorAll('[data-assignment-card]')
        ];

        const assignmentSearch = root.querySelector(
            '[data-assignment-search]'
        );

        const assignmentTabs = [
            ...root.querySelectorAll('[data-assignment-filter]')
        ];

        const assignmentEmpty = root.querySelector(
            '[data-assignment-empty]'
        );

        const assignmentVisibleCount = root.querySelector(
            '[data-assignment-visible-count]'
        );

        let assignmentFilter = 'all';

        function applyAssignmentFilters() {
            const keyword = normalize(assignmentSearch?.value);
            let shown = 0;

            assignmentCards.forEach(card => {
                const matchesState =
                    assignmentFilter === 'all'
                    || card.dataset.assignmentState === assignmentFilter;

                const matchesSearch =
                    normalize(card.dataset.search).includes(keyword);

                card.hidden = !matchesState || !matchesSearch;

                if (!card.hidden) {
                    shown++;
                }
            });

            assignmentTabs.forEach(tab => {
                tab.setAttribute(
                    'aria-pressed',
                    String(
                        tab.dataset.assignmentFilter === assignmentFilter
                    )
                );
            });

            if (assignmentEmpty) {
                assignmentEmpty.hidden =
                    assignmentCards.length === 0 || shown > 0;
            }

            if (assignmentVisibleCount) {
                assignmentVisibleCount.textContent =
                    `${shown} of ${assignmentCards.length} `
                    + `${assignmentCards.length === 1 ? 'assignment' : 'assignments'} shown`;
            }
        }

        assignmentTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                assignmentFilter =
                    tab.dataset.assignmentFilter || 'all';

                applyAssignmentFilters();
            });
        });

        assignmentSearch?.addEventListener(
            'input',
            applyAssignmentFilters
        );

        root.querySelectorAll(
            '[data-assignment-clear]'
        ).forEach(button => {
            button.addEventListener('click', () => {
                assignmentFilter = 'all';

                if (assignmentSearch) {
                    assignmentSearch.value = '';
                }

                applyAssignmentFilters();
            });
        });

        applyAreaFilters();
        applyAssignmentFilters();
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initializeLastMileAssignment
        );
    } else {
        initializeLastMileAssignment();
    }
})();
</script>
@endpush