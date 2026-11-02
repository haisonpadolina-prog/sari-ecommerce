@extends('layouts.courier')

@section('title', 'Rider Dashboard')
@section('header-title', 'Rider Dashboard')
@section('header-subtitle', 'Operations Overview')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
@verbatim
/* SARI Rider Operations v3 — view-scoped design based on rider reference.
   The Laravel courier sidebar and header are styled ONLY on this dashboard.
   No changes to routes, business rules, remittance, or delivery progression. */
body:has(#riderDashboard), body:has(#riderDashboard) #courierMain {
    background: #f4f5f7 !important;
    color: #202a38;
}
body:has(#riderDashboard) #courierSidebar {
    background: #fff !important;
    border-right: 1px solid #e9edf2 !important;
    width: 244px;
    box-shadow: none;
}
body:has(#riderDashboard) #courierSidebarLogo img {width: 112px;}
body:has(#riderDashboard) #courierSidebar > div:first-child {min-height:78px!important;border-color:#edf0f4!important;}
body:has(#riderDashboard) #courierSidebarLogo {min-height: 78px;}
body:has(#riderDashboard) #courierSidebarNav {padding: 13px 12px 16px;}
body:has(#riderDashboard) #courierSidebarNav .courier-sidebar-section {
    font-size: 8px; font-weight: 500; letter-spacing: .12em; color: #98a2b3;
}
body:has(#riderDashboard) #courierSidebarNav [data-courier-sidebar-item] {
    min-height: 41px; border-radius: 10px; padding: 10px 12px;
    font-size: 11px; font-weight: 400;
    box-shadow: none; gap: 11px;
}
body:has(#riderDashboard) #courierSidebarNav [data-courier-sidebar-item] svg {width: 17px; height: 17px; stroke-width: 1.7;}
body:has(#riderDashboard) #courierSidebarNav a:not([title="Dashboard Overview"]) {transition: background-color .15s ease;}
body:has(#riderDashboard) #courierSidebarNav a[title="Dashboard Overview"] {
    background: #f3f6fa !important; color: #192638 !important;
    font-weight: 500 !important; position: relative;
}
body:has(#riderDashboard) #courierSidebarNav a[title="Dashboard Overview"]::before {
    content: ''; position: absolute; width: 3px; height: 25px;
    left: -12px; top: 50%; transform: translateY(-50%);
    background: #d99a0b; border-radius: 0 3px 3px 0;
}
body:has(#riderDashboard) #courierSidebarProfile {background: #fff !important; border-color: #e9edf2 !important;}
body:has(#riderDashboard) #courierSidebarProfile > a {border-color: #e9edf2; box-shadow: none;}
body:has(#riderDashboard) #courierMain > header {
    background: rgba(255,255,255,.97) !important;
    border-bottom: 1px solid #e9edf2 !important;
    box-shadow: none !important;
}
body:has(#riderDashboard) #courierMain > header > div:first-child {min-height: 70px; padding-inline: clamp(16px,2vw,28px);}
body:has(#riderDashboard) #courierMain > header input[type="search"] {
    border-color: #e8ecf1 !important; border-radius: 12px !important;
    background: #f8fafc !important; font-weight: 400; font-size: 11px;
}
body:has(#riderDashboard) #courierMain > header button {
    box-shadow: none !important; border-color: transparent !important;
    background: transparent !important;
}
body:has(#riderDashboard) #courierMain > header h1 {font-weight: 600 !important; font-size: 18px; letter-spacing: -.03em;}
@media(min-width:1024px) {
    body:has(#riderDashboard):not(.courier-sidebar-collapsed) #courierMain {padding-left:244px !important;}
    body:has(#riderDashboard).courier-sidebar-collapsed #courierSidebar {width:92px;}
    body:has(#riderDashboard).courier-sidebar-collapsed #courierMain {padding-left:92px !important;}
}
#courierMain > main:has(#riderDashboard) {
    padding: 24px clamp(16px,2vw,30px) 36px !important;
    background: transparent !important;
}
#riderDashboard {
    --rx-ink:#1d2939; --rx-muted:#7b8797; --rx-line:#e8edf2;
    --rx-gold:#d59a16; --rx-blue:#397cf5; --rx-green:#169b6c;
    --rx-shadow:0 3px 12px rgba(26,39,57,.026),0 12px 32px rgba(26,39,57,.052);
    width:100%; max-width:none; margin:0; padding:0;
    border:0; border-radius:0; background:transparent;
    color:var(--rx-ink); font:400 12px/1.6 'Poppins',system-ui,sans-serif;
}
#riderDashboard, #riderDashboard *,#riderDashboard *::before,#riderDashboard *::after {box-sizing:border-box;}
#riderDashboard :is(button,input,a) {font-family:inherit;}
#riderDashboard :is(button,a,input):focus-visible {outline:2px solid #d79e23;outline-offset:3px;}
#riderDashboard a {text-decoration:none;}
#riderDashboard .rx-alert {padding:12px 16px; border-radius:12px; margin-bottom:15px;}
#riderDashboard .rx-alert-success {background:#ecf9f1;border:1px solid #bce7ce;color:#226f4c;}
#riderDashboard .rx-alert-error {background:#fff0f2;border:1px solid #f1c4c9;color:#a43747;}
#riderDashboard .rx-topbar {display:flex;justify-content:space-between;align-items:flex-start;gap:14px;margin-bottom:19px;}
#courierMain #riderDashboard .rx-title {margin:0;font-size:clamp(19px,1.85vw,26px);font-weight:600;letter-spacing:-.045em;line-height:1.22;color:#142235;}
#courierMain #riderDashboard .rx-subtitle {margin:7px 0 0;color:#7d8999;font-size:11.5px;font-weight:400;line-height:1.6;}
#riderDashboard .rx-time-chip {display:flex;align-items:center;white-space:nowrap;gap:6px;color:#667386;font-size:10.5px;font-weight:400;padding-top:7px;}
#riderDashboard .rx-time-chip svg {width:15px;height:15px;color:#8d98a6;}
#riderDashboard .rx-metrics {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:17px;}
#riderDashboard .rx-metric, #riderDashboard .rx-column, #riderDashboard .rx-lower-card {
    background:#fff; border:1px solid rgba(231,236,243,.9);
    border-radius:15px; box-shadow:var(--rx-shadow);
}
#riderDashboard .rx-metric {
    min-width:0;min-height:100px;padding:17px 20px;display:flex;align-items:center;gap:15px;
    transition:transform .18s ease,box-shadow .18s ease;
}
#riderDashboard .rx-metric-icon {
    flex:0 0 30px;width:30px;height:34px;display:flex;align-items:center;justify-content:center;
    background:transparent!important;border:0!important;box-shadow:none!important;color:#4483fa;
}
#riderDashboard .rx-metric:nth-child(2) .rx-metric-icon {color:#20a779;}
#riderDashboard .rx-metric:nth-child(3) .rx-metric-icon {color:#805cf4;}
#riderDashboard .rx-metric:nth-child(4) .rx-metric-icon {color:#d59a16;}
#riderDashboard .rx-metric-icon svg {width:24px;height:24px;stroke-width:1.8;}
#riderDashboard .rx-metric-copy {min-width:0;}
#riderDashboard .rx-metric-label {display:block;color:#758195;font-size:11px;font-weight:400;line-height:1.4;}
#riderDashboard .rx-metric-value {display:block;margin:5px 0 0;color:#152136;font-size:24px;font-weight:600;line-height:1.25;letter-spacing:-.045em;font-variant-numeric:tabular-nums;overflow-wrap:anywhere;}
#riderDashboard .rx-metric-note {display:block;margin-top:4px;color:#9aa4b2;font-size:9px;font-weight:300;}
#riderDashboard .rx-workspace {display:grid;grid-template-columns:minmax(235px,.95fr) minmax(390px,1.72fr) minmax(245px,.91fr);align-items:stretch;gap:16px;}
#riderDashboard .rx-assignment-stack {display:flex;min-width:0;flex-direction:column;gap:16px;}
#riderDashboard .rx-column {min-width:0;overflow:hidden;}
#riderDashboard .rx-assignment-card {min-height:300px;}
#riderDashboard .rx-wait-card {flex:1;min-height:245px;}
#riderDashboard .rx-column-head,#riderDashboard .rx-map-top {display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:18px 18px 14px;}
#courierMain #riderDashboard .rx-section-title {margin:0;color:#1e293b;font-size:13px;font-weight:600;letter-spacing:-.027em;line-height:1.4;}
#courierMain #riderDashboard .rx-section-sub {margin:4px 0 0;color:#98a2b1;font-size:10px;font-weight:400;line-height:1.6;}
#riderDashboard .rx-count {display:inline-flex;align-items:center;justify-content:center;min-width:24px;height:24px;padding:0 7px;border-radius:999px;background:#f5f7fa;color:#647287;font-size:10px;font-weight:500;}
#riderDashboard .rx-search {margin:3px 17px 16px;display:flex;align-items:center;gap:9px;padding:0 12px;height:40px;border:1px solid #e6ebf1;border-radius:10px;background:#f8f9fb;color:#8995a6;}
#riderDashboard .rx-search:focus-within {border-color:#b0c9ef;box-shadow:0 0 0 3px rgba(70,128,226,.08);}
#riderDashboard .rx-search svg {width:15px;height:15px;flex:none;}
#riderDashboard .rx-search input {width:100%;min-width:0;border:0;outline:0;background:transparent;font-size:10.5px;font-weight:400;color:#344054;}
#riderDashboard .rx-search input::placeholder {color:#a3adba;}
#riderDashboard .rx-search input:focus-visible {outline:0;}
#riderDashboard .rx-list {display:grid;gap:8px;padding:0 16px 16px;}
#riderDashboard .rx-list-label {margin:2px 1px 5px;font-size:9px;letter-spacing:.12em;color:#8c98a7;font-weight:500;text-transform:uppercase;}
#riderDashboard .rx-job {display:block;border:1px solid #e9edf3;border-radius:12px;padding:13px;background:#fff;color:#314256;transition:border-color .16s ease,background .16s ease,transform .16s ease;}
#riderDashboard .rx-job:hover {border-color:#cbd9ed;background:#f8fbff;}
#riderDashboard .rx-job-primary {border-color:#bcd4ff;background:linear-gradient(135deg,#f5f9ff,#ffffff);box-shadow:inset 0 0 0 1px rgba(75,132,235,.04);}
#riderDashboard .rx-job-primary:hover {border-color:#8fb8fc;background:#f1f7ff;}
#riderDashboard .rx-job-top {display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
#riderDashboard .rx-job-code {font-size:10.5px;color:#172a48;font-weight:600;overflow-wrap:anywhere;}
#riderDashboard .rx-job-route {display:block;margin:10px 0 0;font-size:10.3px;line-height:1.6;color:#56667c;font-weight:400;overflow-wrap:anywhere;}
#riderDashboard .rx-job-footer {display:flex;justify-content:space-between;align-items:center;gap:8px;margin-top:10px;color:#748196;font-size:9px;}
#riderDashboard .rx-job-sub {display:block;color:#8c97a6;font-size:9px;margin-top:5px;}
#riderDashboard .rx-tag {display:inline-flex;align-items:center;justify-content:center;gap:5px;max-width:100%;border-radius:999px;background:#e8f1ff;padding:4px 9px;font-size:9px;font-weight:500;line-height:1.5;color:#3575e1;text-transform:capitalize;overflow-wrap:anywhere;}
#riderDashboard .rx-tag-green {background:#e5f7ed;color:#158858;}
#riderDashboard .rx-tag-amber {background:#fff3dc;color:#a56e0d;}
#riderDashboard .rx-mini-empty {padding:16px 12px;color:#8491a3;font-size:10px;line-height:1.65;}
#riderDashboard .rx-empty-illustration {display:grid;place-items:center;gap:10px;min-height:145px;text-align:center;color:#94a0b0;padding:14px;}
#riderDashboard .rx-empty-illustration svg {width:38px;height:38px;stroke:#aeb8c6;stroke-width:1.4;}
#riderDashboard .rx-empty-illustration strong {display:block;font-size:10.5px;font-weight:500;color:#637083;}
#riderDashboard .rx-empty-illustration span {display:block;max-width:220px;color:#96a2af;font-size:9px;line-height:1.7;}
#riderDashboard [data-job-item][hidden],#riderDashboard [data-search-empty][hidden] {display:none!important;}
#riderDashboard .rx-map-panel {display:flex;flex-direction:column;min-height:555px;}
#riderDashboard .rx-map-top {min-height:66px;}
#riderDashboard .rx-map-tools {display:flex;gap:7px;align-items:center;flex-wrap:wrap;}
#riderDashboard .rx-map-stage {position:relative;flex:1;height:410px;min-height:410px;isolation:isolate;overflow:hidden;background:#e7eef0;margin:0 6px;border-radius:10px;}
#riderDashboard .rx-leaflet-map {position:absolute;inset:0;height:100%;width:100%;background:#e8edef;}
#riderDashboard .rx-map-maptools {position:absolute;left:13px;top:12px;z-index:501;display:flex;align-items:center;gap:8px;border:1px solid #e6ebf1;border-radius:999px;padding:7px 10px;background:rgba(255,255,255,.95);box-shadow:0 4px 16px rgba(23,40,58,.1);color:#5c687b;font-size:9px;font-weight:500;pointer-events:none;}
#riderDashboard .rx-live-map-dot {display:block;width:7px;height:7px;border-radius:50%;background:#19a774;box-shadow:0 0 0 3px rgba(25,167,116,.11);}
#riderDashboard .rx-map-locate {position:absolute;top:13px;right:13px;z-index:501;display:grid;gap:7px;}
#riderDashboard .rx-map-locate button {width:34px;height:34px;border:1px solid #e2e8ef;border-radius:9px;background:#fff;box-shadow:0 4px 13px rgba(23,40,58,.11);color:#31445b;display:grid;place-items:center;cursor:pointer;}
#riderDashboard .rx-map-locate button svg {width:17px;height:17px;stroke-width:1.7;}
#riderDashboard .rx-map-locate button:hover {background:#f4f8fd;}
#riderDashboard .rx-map-stage .leaflet-control-zoom {border:1px solid #e4e9f0;box-shadow:0 4px 14px rgba(31,45,61,.1);}
#riderDashboard .rx-map-stage .leaflet-top.leaflet-left {margin-top:50px;}
#riderDashboard .rx-map-stage .leaflet-control-zoom a {color:#2d4057;}
#riderDashboard .rx-map-stage .leaflet-control-attribution {background:rgba(255,255,255,.88);font-size:9px;}
#riderDashboard .rx-map-stage .leaflet-container {font-family:'Poppins',sans-serif;}
#riderDashboard .rx-map-note {position:absolute;left:14px;bottom:13px;max-width:min(375px,calc(100% - 28px));z-index:500;border-radius:9px;border:1px solid #e5eaf0;background:rgba(255,255,255,.96);box-shadow:0 4px 17px rgba(21,43,63,.1);padding:9px 11px;color:#647287;font-size:9px;line-height:1.55;pointer-events:none;}
#riderDashboard .rx-pin {display:block;width:25px;height:25px;border:5px solid #fff;border-radius:50%;background:#e09c18;box-shadow:0 0 0 2px #e09c18,0 4px 12px rgba(29,41,60,.28);}
#riderDashboard .rx-pin--pickup {background:#377cf4;box-shadow:0 0 0 2px #377cf4,0 4px 12px rgba(29,41,60,.28);}
#riderDashboard .rx-route-footer {padding:14px 18px;display:grid;gap:10px;background:#fff;}
#riderDashboard .rx-route-locations {display:grid;grid-template-columns:minmax(0,1fr) 18px minmax(0,1fr);align-items:center;gap:11px;}
#riderDashboard .rx-route-location {display:flex;gap:8px;align-items:center;min-width:0;}
#riderDashboard .rx-route-location-icon {flex:none;display:grid;place-items:center;color:#16895f;}
#riderDashboard .rx-route-location-icon svg {width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.9;}
#riderDashboard .rx-route-location.is-destination .rx-route-location-icon {color:#e05e5e;}
#riderDashboard .rx-route-location span {display:block;color:#95a0af;font-size:9px;}
#riderDashboard .rx-route-location strong {display:block;color:#273448;font-size:10px;font-weight:500;line-height:1.5;overflow-wrap:anywhere;}
#riderDashboard .rx-route-arrow {font-size:17px;color:#aab3c0;text-align:center;}
#riderDashboard .rx-route-meta {display:flex;align-items:center;justify-content:space-between;gap:10px;border-top:1px solid #edf0f4;padding-top:11px;}
#riderDashboard .rx-route-code {color:#68768b;font-size:9px;font-weight:500;overflow-wrap:anywhere;}
#riderDashboard .rx-route-disclaimer {color:#a1aab7;font-size:9px;font-weight:400;}
#riderDashboard .rx-route-button {display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:9px 13px;border-radius:9px;background:#1c2432;color:#fff!important;font-size:10px;font-weight:500;white-space:nowrap;}
#riderDashboard .rx-route-button:hover {background:#344055;}
#riderDashboard .rx-route-button svg {width:14px;height:14px;}
#riderDashboard .rx-side {display:flex;flex-direction:column;min-height:555px;padding:0 17px 17px;}
#riderDashboard .rx-side .rx-column-head {padding:18px 0 15px;border-bottom:1px solid #edf0f4;}
#riderDashboard .rx-side .rx-column-head a {font-size:10px;font-weight:500;color:#357dec;}
#riderDashboard .rx-rider-head {display:flex;align-items:center;gap:11px;padding:17px 0 14px;}
#riderDashboard .rx-avatar {flex:0 0 43px;width:43px;height:43px;border-radius:50%;background:#e8eef8;color:#3b678d;font-weight:500;font-size:17px;display:grid;place-items:center;border:1px solid #dce5f0;}
#riderDashboard .rx-rider-name {margin:0;color:#203049;font-size:11.5px;font-weight:600;overflow-wrap:anywhere;}
#riderDashboard .rx-rider-role {margin:3px 0 0;color:#98a2af;font-size:9px;}
#riderDashboard .rx-availability {margin:0 0 14px;display:flex;align-items:center;gap:7px;color:#2b936b;font-size:10px;font-weight:500;}
#riderDashboard .rx-status-dot {width:7px;height:7px;border-radius:50%;display:inline-block;flex:none;background:#aab1b9;}
#riderDashboard .rx-status-dot[data-state="online"] {background:#20ab74;box-shadow:0 0 0 3px rgba(32,171,116,.1);}
#riderDashboard .rx-status-dot[data-state="busy"] {background:#e0a120;}
#riderDashboard .rx-rider-stats {display:grid;gap:0;padding:8px 0 12px;border-top:1px solid #edf0f4;border-bottom:1px solid #edf0f4;}
#riderDashboard .rx-rider-stat {display:flex;align-items:center;justify-content:space-between;gap:10px;padding:7px 0;}
#riderDashboard .rx-rider-stat span {font-size:10px;color:#7d8a9c;font-weight:400;}
#riderDashboard .rx-rider-stat strong {font-size:11px;color:#263649;font-weight:500;font-variant-numeric:tabular-nums;}
#riderDashboard .rx-focus-card {padding:17px 0 0;border:0;border-radius:0;background:#fff;}
#riderDashboard .rx-focus-title {font-size:11px;font-weight:600;margin:0 0 10px;color:#263446;}
#riderDashboard .rx-focus-data {display:grid;gap:8px;}
#riderDashboard .rx-focus-row {display:flex;justify-content:space-between;gap:9px;align-items:flex-start;font-size:9px;line-height:1.55;}
#riderDashboard .rx-focus-row span {color:#8793a3;}
#riderDashboard .rx-focus-row strong {font-size:9.5px;text-align:right;font-weight:500;color:#344457;overflow-wrap:anywhere;}
#riderDashboard .rx-focus-desc {margin:12px 0 0;color:#909ca9;font-size:9px;line-height:1.7;}
#riderDashboard .rx-side-actions {display:grid;gap:2px;margin-top:auto;padding-top:16px;}
#riderDashboard .rx-side-actions > .rx-route-button {width:100%;margin-bottom:11px;}
#riderDashboard .rx-side-actions > a:not(.rx-route-button) {display:flex;align-items:center;justify-content:space-between;padding:8px 2px;color:#68788b;font-size:9.5px;font-weight:400;}
#riderDashboard .rx-side-actions > a:not(.rx-route-button):hover {color:#367aec;}
#riderDashboard .rx-lower {margin-top:17px;display:grid;grid-template-columns:1.12fr 1fr;gap:16px;}
#riderDashboard .rx-lower-card {padding:17px 19px;min-width:0;}
#riderDashboard .rx-lower-head {display:flex;align-items:flex-start;justify-content:space-between;gap:12px;}
#riderDashboard .rx-lower-head > a {color:#607184;border:1px solid #e9edf2;border-radius:9px;padding:7px 10px;font-size:9.5px;font-weight:500;white-space:nowrap;}
#riderDashboard .rx-lower-head > a:hover {background:#f8fafc;}
#riderDashboard .rx-chart-body {display:flex;align-items:center;gap:24px;padding:15px 0 6px;}
#riderDashboard .rx-donut {width:126px;height:126px;flex:none;display:grid;place-items:center;border-radius:50%;position:relative;background:conic-gradient(#377df2 0 var(--donut-value),#e4a924 var(--donut-value) 100%);}
#riderDashboard .rx-donut.is-empty {background:#e9edf3;}
#riderDashboard .rx-donut::after {content:'';position:absolute;inset:14px;border-radius:50%;background:#fff;}
#riderDashboard .rx-donut-center {position:relative;z-index:1;text-align:center;}
#riderDashboard .rx-donut-center strong {display:block;color:#1d2939;font-size:23px;font-weight:600;line-height:1.2;}
#riderDashboard .rx-donut-center span {display:block;color:#9aa6b4;font-size:9px;}
#riderDashboard .rx-chart-legend {flex:1;min-width:0;display:grid;gap:9px;}
#riderDashboard .rx-chart-legend-row {display:flex;align-items:center;justify-content:space-between;gap:10px;color:#69788a;font-size:10px;}
#riderDashboard .rx-chart-legend-row strong {color:#354257;font-size:10px;font-weight:500;}
#riderDashboard .rx-chart-dot {width:8px;height:8px;flex:none;border-radius:50%;background:#377df2;}
#riderDashboard .rx-chart-dot--gold {background:#e4a924;}
#riderDashboard .rx-chart-dot--muted {background:#bfc7d2;}
#riderDashboard .rx-chart-legend-label {display:flex;align-items:center;gap:8px;}
#riderDashboard .rx-lower-note {margin:8px 0 0;color:#a1aab6;font-size:9px;line-height:1.6;}
#riderDashboard .rx-today-grid {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:15px;}
#riderDashboard .rx-today {min-width:0;min-height:113px;padding:15px 11px;border:1px solid #f1f3f6;border-radius:11px;background:#fcfdff;}
#riderDashboard .rx-today svg {width:17px;height:17px;stroke-width:1.9;fill:none;stroke:currentColor;}
#riderDashboard .rx-today-icon {display:block;height:20px;color:#1eaf79;}
#riderDashboard .rx-today:nth-child(2) .rx-today-icon {color:#3c82f5;}
#riderDashboard .rx-today:nth-child(3) .rx-today-icon {color:#805ff1;}
#riderDashboard .rx-today:nth-child(4) .rx-today-icon {color:#d59a16;}
#riderDashboard .rx-today span:not(.rx-today-icon) {display:block;margin-top:11px;color:#8490a0;font-size:9px;font-weight:400;}
#riderDashboard .rx-today strong {display:block;margin-top:5px;color:#253246;font-size:17px;line-height:1.3;font-weight:600;overflow-wrap:anywhere;}
@media(hover:hover){#riderDashboard .rx-metric:hover {transform:translateY(-2px);box-shadow:0 7px 22px rgba(28,39,55,.045),0 18px 42px rgba(28,39,55,.07);}}
@media(max-width:1360px){
    #riderDashboard .rx-workspace {grid-template-columns:minmax(230px,.9fr) minmax(350px,1.6fr);}
    #riderDashboard .rx-side {grid-column:1/-1;min-height:0;display:grid;grid-template-columns:minmax(230px,.8fr) minmax(220px,1fr) minmax(170px,.65fr);column-gap:18px;align-content:start;}
    #riderDashboard .rx-side .rx-column-head {grid-column:1/-1;}
    #riderDashboard .rx-rider-head {grid-column:1;}
    #riderDashboard .rx-availability {grid-column:1;}
    #riderDashboard .rx-rider-stats {grid-column:2;grid-row:2/4;}
    #riderDashboard .rx-focus-card {grid-column:3;grid-row:2/4;padding-top:14px;}
    #riderDashboard .rx-side-actions {grid-column:1/-1;display:flex;align-items:center;justify-content:space-between;padding:9px 0 0;}
    #riderDashboard .rx-side-actions > .rx-route-button {width:auto;margin:0;}
}
@media(max-width:1050px){
    #riderDashboard .rx-metrics {grid-template-columns:repeat(2,minmax(0,1fr));}
    #riderDashboard .rx-workspace {grid-template-columns:minmax(0,1fr);}
    #riderDashboard .rx-assignment-stack {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));}
    #riderDashboard .rx-side {grid-column:auto;}
    #riderDashboard .rx-map-stage {height:430px;min-height:430px;}
}
@media(max-width:730px){
    #riderDashboard .rx-assignment-stack {display:flex;}
    #riderDashboard .rx-side {display:flex;min-height:0;}
    #riderDashboard .rx-side-actions {display:grid;}
    #riderDashboard .rx-side-actions > .rx-route-button {width:100%;}
    #riderDashboard .rx-rider-stats {width:100%;}
    #riderDashboard .rx-lower {grid-template-columns:minmax(0,1fr);}
    #riderDashboard .rx-map-stage {height:365px;min-height:365px;}
    #riderDashboard .rx-topbar {flex-direction:column;}
    #riderDashboard .rx-time-chip {padding-top:0;}
}
@media(max-width:480px){
    #courierMain > main:has(#riderDashboard) {padding:16px 12px 26px!important;}
    #riderDashboard .rx-metrics {gap:9px;}
    #riderDashboard .rx-metric {gap:8px;min-height:85px;padding:12px;}
    #riderDashboard .rx-metric-icon {flex-basis:23px;width:23px;}
    #riderDashboard .rx-metric-icon svg {width:21px;height:21px;}
    #riderDashboard .rx-metric-value {font-size:20px;}
    #riderDashboard .rx-metric-label {font-size:9px;}
    #riderDashboard .rx-metric-note {font-size:8px;}
    #riderDashboard .rx-route-locations {gap:6px;}
    #riderDashboard .rx-route-meta {flex-wrap:wrap;}
    #riderDashboard .rx-map-stage {height:295px;min-height:295px;}
    #riderDashboard .rx-chart-body {gap:14px;}
    #riderDashboard .rx-donut {height:110px;width:110px;}
    #riderDashboard .rx-today-grid {grid-template-columns:repeat(2,minmax(0,1fr));}
}
@media(prefers-reduced-motion:reduce){#riderDashboard *,#riderDashboard *::before,#riderDashboard *::after {transition:none!important;animation:none!important;}}


/* SARI Rider / Buyer Cart visual alignment, October 2026.
   Only the Rider Dashboard scope is affected. All routes, QR, COD,
   assignments, earnings, and authenticated map data remain unchanged. */
#riderDashboard {
  --rx-ink: #1f2933;
  --rx-muted: #697789;
  --rx-line: #e5e7eb;
  --rx-gold: #c98d19;
  --rx-gold-hover: #b47b0f;
  --rx-shadow: 0 2px 5px rgba(15,23,42,.045),
    0 10px 24px rgba(15,23,42,.075),
    0 24px 54px rgba(15,23,42,.095),
    inset 0 1px 0 rgba(255,255,255,.92);
  --rx-shadow-soft: 0 1px 3px rgba(15,23,42,.035),
    0 7px 18px rgba(15,23,42,.06),
    0 15px 32px rgba(15,23,42,.065),
    inset 0 1px 0 rgba(255,255,255,.9);
  font-size: 12.5px;
  line-height: 1.62;
  color: var(--rx-ink);
}
body:has(#riderDashboard), body:has(#riderDashboard) #courierMain {background:#f4f5f7!important;}
body:has(#riderDashboard) #courierSidebar {border-right:1px solid var(--rx-line)!important;}
body:has(#riderDashboard) #courierSidebarNav [data-courier-sidebar-item] {
  font-size:12px; font-weight:400; min-height:43px; border-radius:10px; gap:12px;
}
body:has(#riderDashboard) #courierSidebarNav [data-courier-sidebar-item] svg,
body:has(#riderDashboard) #courierSidebarNav [data-courier-sidebar-item] i {
  width:18px; height:18px; stroke-width:1.75;
}
body:has(#riderDashboard) #courierSidebarNav a[title="Dashboard Overview"] {
  color:#27303a!important; background:#f6f7f9!important; font-weight:500!important;
}
body:has(#riderDashboard) #courierSidebarNav a[title="Dashboard Overview"]::before {background:var(--rx-gold);}
body:has(#riderDashboard) #courierMain > header {background:#fff!important;border-bottom:1px solid var(--rx-line)!important;}
body:has(#riderDashboard) #courierMain > header h1 {font-size:19px; font-weight:600;}
body:has(#riderDashboard) #courierMain > header input[type="search"] {
  background:#fff!important; border:1px solid #e5e7eb!important; border-radius:12px!important; font-size:12px;
}
#courierMain > main:has(#riderDashboard) {padding:26px clamp(18px,2vw,34px) 52px!important;}
#riderDashboard .rx-topbar {align-items:center;gap:18px;margin-bottom:23px;}
#riderDashboard .rx-eyebrow {
  display:block; color:var(--rx-gold); font-size:10px; letter-spacing:.14em;
  font-weight:600; line-height:1.4; margin:0 0 9px; text-transform:uppercase;
}
#courierMain #riderDashboard .rx-title {
  font-size:clamp(30px,3vw,38px); font-weight:700;
  letter-spacing:-.045em; line-height:1.1; color:#1f2933;
}
#courierMain #riderDashboard .rx-subtitle {margin-top:10px;color:#748091;font-size:12.5px;font-weight:400;}
#riderDashboard .rx-time-chip {
  padding:12px 15px; color:#5c6879; font-size:11.5px;
  border:1px solid #e5e7eb; background:#fff; border-radius:12px;
  box-shadow:var(--rx-shadow-soft); height:max-content; align-self:center;
}
#riderDashboard .rx-time-chip svg {color:var(--rx-gold);width:17px;height:17px;stroke-width:1.7;}
#riderDashboard .rx-metrics {gap:18px;margin-bottom:21px;}
#riderDashboard .rx-metric,
#riderDashboard .rx-column,
#riderDashboard .rx-lower-card {
  background:#fff; border:1px solid var(--rx-line); border-radius:16px;
  box-shadow:var(--rx-shadow); overflow:hidden;
}
#riderDashboard .rx-metric {
  min-height:116px; padding:19px 20px; gap:16px;
  transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
}
#riderDashboard .rx-metric-icon {
  flex:0 0 34px; width:34px;height:34px; background:transparent!important;
  color:#617083!important; box-shadow:none!important; border:none!important;
}
#riderDashboard .rx-metric:nth-child(4) .rx-metric-icon {color:var(--rx-gold)!important;}
#riderDashboard .rx-metric-icon svg {width:25px;height:25px;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round;}
#riderDashboard .rx-metric-label {font-size:12px;line-height:1.5;font-weight:500;color:#6d7988;}
#riderDashboard .rx-metric-value {font-size:25px;font-weight:600;color:#1f2933;margin-top:6px;}
#riderDashboard .rx-metric-note {margin-top:4px;color:#8a96a3;font-size:10px;font-weight:400;line-height:1.5;}
#riderDashboard .rx-workspace {gap:19px;}
#riderDashboard .rx-assignment-stack {gap:19px;}
#riderDashboard .rx-column-head, #riderDashboard .rx-map-top {padding:21px 21px 15px;gap:12px;}
#courierMain #riderDashboard .rx-section-title {
  display:flex; gap:10px; align-items:center; margin:0;
  font-size:17.5px; font-weight:600; line-height:1.35;
  letter-spacing:-.032em; color:#1f2933;
}
#riderDashboard .rx-heading-icon {flex:none;width:20px;height:20px;color:#a67a24;stroke-width:1.75;}
#courierMain #riderDashboard .rx-section-sub {font-size:11.5px;color:#87919e;margin-top:7px;line-height:1.55;}
#riderDashboard .rx-count {min-width:29px;height:29px;font-size:11.5px;color:#66717e;border:1px solid #edf0f3;background:#f8f9fa;}
#riderDashboard .rx-search {
  height:43px;margin:4px 20px 15px;border:1px solid #e5e7eb;
  border-radius:11px;background:#fafbfc;color:#808d9a;
}
#riderDashboard .rx-search input {font-size:11.5px;color:#344252;}
#riderDashboard .rx-search:focus-within {border-color:#d5af66;box-shadow:0 0 0 3px rgba(201,141,25,.08);}
#riderDashboard .rx-list {gap:9px;padding:0 20px 20px;}
#riderDashboard .rx-list-label {font-size:10px;color:#8a93a1;margin-top:5px;letter-spacing:.12em;font-weight:500;}
/* Cart hierarchy: only the major white cards float. Nested order rows stay flat. */
#riderDashboard .rx-job {
  border:1px solid #e8ebef; background:#fff; border-radius:11px;
  padding:14px 15px; color:#344154; box-shadow:none;
  transition:border-color .15s ease,background-color .15s ease;
}
#riderDashboard .rx-job:hover {background:#fdfdfd;border-color:#d6a449;transform:none;}
#riderDashboard .rx-job-primary {
  border:1px solid #e4d6b7; border-left:3px solid var(--rx-gold);
  padding-left:13px;background:#fff;box-shadow:none;
}
#riderDashboard .rx-job-primary:hover {border-color:#c98d19;background:#fff;}
#riderDashboard .rx-job-code {font-size:12px;line-height:1.5;font-weight:600;color:#252f3b;}
#riderDashboard .rx-job-route {font-size:12px;line-height:1.65;color:#536073;margin-top:11px;}
#riderDashboard .rx-job-footer {font-size:10.5px;margin-top:12px;color:#7d8996;}
#riderDashboard .rx-job-footer span:last-child {color:#a87513;font-weight:600;}
#riderDashboard .rx-job-sub {font-size:10.5px;line-height:1.5;margin-top:8px;}
#riderDashboard .rx-tag {
  font-size:10px;padding:5px 9px;background:#f5f6f8;color:#5e6878;
  line-height:1.35;border:1px solid #edf0f3;font-weight:500;
}
#riderDashboard .rx-tag-green {background:#eef8f3;color:#197b56;border-color:#dcefe3;}
#riderDashboard .rx-tag-amber {background:#fff9ec;color:#926415;border-color:#f2e5c4;}
#riderDashboard .rx-mini-empty {font-size:11.5px;line-height:1.75;color:#758091;}
#riderDashboard .rx-empty-illustration {min-height:150px;gap:12px;}
#riderDashboard .rx-empty-illustration svg {width:41px;height:41px;stroke:#b49d70;stroke-width:1.5;}
#riderDashboard .rx-empty-illustration strong {font-size:12px;color:#556273;}
#riderDashboard .rx-empty-illustration span {font-size:10.5px;color:#8b96a3;}
/* Cart-inspired idle assignment state: only the parent card floats. */
#riderDashboard .rx-assignment-card .rx-list--idle {padding:0 19px 19px;}
#riderDashboard .rx-idle {
  min-height:208px;padding:23px 16px 22px;display:flex;
  align-items:center;justify-content:center;flex-direction:column;text-align:center;
  background:#fafbfc;border:1px solid #edf0f3;border-radius:12px;
}
#riderDashboard .rx-idle-icon {width:44px;height:44px;flex:none;color:#b58b38;stroke-width:1.45;}
#riderDashboard .rx-idle-title {margin:13px 0 0;font-size:14px;font-weight:600;letter-spacing:-.025em;color:#293748;line-height:1.3;}
#riderDashboard .rx-idle-copy {max-width:260px;margin:7px 0 18px;font-size:11px;font-weight:400;color:#7e8996;line-height:1.7;}
#riderDashboard .rx-idle-action {
  display:inline-flex;align-items:center;justify-content:center;gap:12px;
  min-height:41px;max-width:100%;padding:10px 16px;border-radius:10px;
  background:#c98d19;color:#fff!important;border:1px solid #c98d19;
  font-size:11px;font-weight:600;line-height:1.3;white-space:nowrap;
  box-shadow:0 5px 13px rgba(145,97,12,.13);
  transition:background-color .16s ease,box-shadow .16s ease,transform .16s ease;
}
#riderDashboard .rx-idle-action svg {width:15px;height:15px;flex:none;stroke-width:1.8;}
#riderDashboard .rx-idle-action:hover {background:#af7810;border-color:#af7810;box-shadow:0 7px 17px rgba(145,97,12,.2);transform:translateY(-1px);}
#riderDashboard .rx-idle-action:focus-visible {outline:2px solid #8a6218;outline-offset:3px;}
@media(max-width:590px) {
  #riderDashboard .rx-idle {padding:24px 16px;min-height:210px;}
  #riderDashboard .rx-idle-action {min-height:43px;}
}
@media(prefers-reduced-motion:reduce) {
  #riderDashboard .rx-idle-action {transition:none;}
  #riderDashboard .rx-idle-action:hover {transform:none;}
}
#riderDashboard .rx-map-panel {min-height:610px;}
#riderDashboard .rx-map-top {min-height:77px;}
#riderDashboard .rx-map-stage {margin:0 9px;border-radius:12px;height:455px;min-height:455px;background:#e9edef;}
#riderDashboard .rx-map-maptools {
  font-size:10px;background:#fff;border:1px solid #e5e7eb;box-shadow:var(--rx-shadow-soft);
}
#riderDashboard .rx-map-locate button {border:1px solid #e5e7eb;border-radius:10px;box-shadow:var(--rx-shadow-soft);}
#riderDashboard .rx-map-note {font-size:10px;line-height:1.65;background:#fff;border:1px solid #e5e7eb;}
#riderDashboard .rx-route-footer {padding:17px 21px 20px;gap:15px;}
#riderDashboard .rx-route-location span {font-size:10.5px;color:#8a95a2;}
#riderDashboard .rx-route-location strong {font-size:12px;font-weight:500;line-height:1.5;}
#riderDashboard .rx-route-location-icon {color:#9c741d;}
#riderDashboard .rx-route-location.is-destination .rx-route-location-icon {color:#b26458;}
#riderDashboard .rx-route-meta {padding-top:13px;}
#riderDashboard .rx-route-code {font-size:10.5px;color:#617084;}
#riderDashboard .rx-route-disclaimer {font-size:10px;}
#riderDashboard .rx-route-button {
  min-height:43px; padding:11px 17px;border-radius:10px;
  background:var(--rx-gold);color:#fff!important;
  font-size:11.5px;font-weight:600;box-shadow:0 5px 13px rgba(159,108,10,.13);
  transition:background-color .15s ease,transform .15s ease;
}
#riderDashboard .rx-route-button:hover {background:var(--rx-gold-hover);transform:translateY(-1px);}
#riderDashboard .rx-side {padding:0 20px 20px;min-height:610px;}
#riderDashboard .rx-side .rx-column-head {padding:21px 0 16px;}
#riderDashboard .rx-side .rx-column-head a {font-size:11.5px;color:#94670f;font-weight:500;}
#riderDashboard .rx-rider-head {gap:13px;padding:20px 0 15px;}
#riderDashboard .rx-avatar {width:46px;height:46px;flex-basis:46px;background:#f5f6f8;border:1px solid #e5e7eb;color:#3c4b5b;font-size:17px;}
#riderDashboard .rx-rider-name {font-size:13.5px;color:#233140;font-weight:600;}
#riderDashboard .rx-rider-role {font-size:10.5px;color:#8b96a2;}
#riderDashboard .rx-availability {font-size:11px;color:#22815e;}
#riderDashboard .rx-rider-stats {padding:10px 0 13px;}
#riderDashboard .rx-rider-stat {padding:9px 0;}
#riderDashboard .rx-rider-stat span {font-size:11px;color:#7b8794;}
#riderDashboard .rx-rider-stat strong {font-size:12.5px;color:#273342;font-weight:600;}
#riderDashboard .rx-focus-card {padding-top:19px;background:transparent;}
#riderDashboard .rx-focus-title {font-size:13.5px;font-weight:600;}
#riderDashboard .rx-focus-data {gap:10px;}
#riderDashboard .rx-focus-row {font-size:10.5px;line-height:1.7;gap:12px;}
#riderDashboard .rx-focus-row span {color:#8993a1;}
#riderDashboard .rx-focus-row strong {font-size:11px;line-height:1.5;color:#344051;}
#riderDashboard .rx-focus-desc {font-size:10.5px;color:#8993a0;line-height:1.8;}
#riderDashboard .rx-side-actions {gap:5px;}
#riderDashboard .rx-side-actions > a:not(.rx-route-button) {font-size:11px;color:#657383;padding:10px 2px;}
#riderDashboard .rx-side-actions > a:not(.rx-route-button):hover {color:#a87513;}
#riderDashboard .rx-lower {margin-top:21px;gap:19px;}
#riderDashboard .rx-lower-card {padding:21px 23px;}
#riderDashboard .rx-lower-head {align-items:center;}
#riderDashboard .rx-lower-head > a {font-size:11px;border-radius:10px;padding:9px 12px;border-color:#e5e7eb;color:#715a31;}
#riderDashboard .rx-lower-head > a:hover {background:#fafafa;border-color:#d1b57f;}
#riderDashboard .rx-chart-body {padding:24px 0 11px;gap:27px;}
#riderDashboard .rx-donut {width:133px;height:133px;background:conic-gradient(#c98d19 0 var(--donut-value),#d9dfe6 var(--donut-value) 100%);}
#riderDashboard .rx-donut.is-empty {background:#e9edf2;}
#riderDashboard .rx-donut-center strong {font-size:24px;color:#26323f;}
#riderDashboard .rx-donut-center span {font-size:10px;color:#8794a2;}
#riderDashboard .rx-chart-legend {gap:11px;}
#riderDashboard .rx-chart-legend-row {font-size:11.5px;color:#657382;}
#riderDashboard .rx-chart-legend-row strong {font-size:12px;}
#riderDashboard .rx-chart-dot {background:#c98d19;}
#riderDashboard .rx-chart-dot--gold {background:#d9dfe6;}
#riderDashboard .rx-chart-dot--muted {background:#97a4b3;}
#riderDashboard .rx-lower-note {font-size:10.5px;color:#8794a1;}
#riderDashboard .rx-today-grid {gap:11px;margin-top:20px;}
#riderDashboard .rx-today {min-height:123px;background:#fff;border:1px solid #edf0f2;border-radius:12px;padding:16px 13px;box-shadow:none;}
#riderDashboard .rx-today-icon {color:#9e7528!important;}
#riderDashboard .rx-today:nth-child(2) .rx-today-icon,
#riderDashboard .rx-today:nth-child(3) .rx-today-icon,
#riderDashboard .rx-today:nth-child(4) .rx-today-icon {color:#9e7528!important;}
#riderDashboard .rx-today svg {width:19px;height:19px;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;}
#riderDashboard .rx-today span:not(.rx-today-icon) {font-size:10.5px;color:#7b8794;}
#riderDashboard .rx-today strong {font-size:20px;font-weight:600;color:#283342;}
#riderDashboard .rx-map-stage .leaflet-container {font-family:'Poppins',sans-serif;}
@media(hover:hover) {
  #riderDashboard .rx-metric:hover {transform:translateY(-3px);box-shadow:0 5px 16px rgba(15,23,42,.06),0 18px 37px rgba(15,23,42,.095),0 27px 60px rgba(15,23,42,.10);}
}
@media(max-width:1480px) {
  #riderDashboard .rx-workspace {grid-template-columns:minmax(270px,.92fr) minmax(400px,1.5fr);}
  #riderDashboard .rx-side {grid-column:1/-1;min-height:0;display:grid;grid-template-columns:minmax(230px,.8fr) minmax(220px,1fr) minmax(180px,.6fr);column-gap:25px;}
  #riderDashboard .rx-side .rx-column-head {grid-column:1/-1;}
  #riderDashboard .rx-rider-head,#riderDashboard .rx-availability {grid-column:1;}
  #riderDashboard .rx-rider-stats {grid-column:2;grid-row:2/4;}
  #riderDashboard .rx-focus-card {grid-column:3;grid-row:2/4;padding-top:18px;}
  #riderDashboard .rx-side-actions {grid-column:1/-1;display:flex;justify-content:space-between;align-items:center;padding:12px 0 0;}
  #riderDashboard .rx-side-actions > .rx-route-button {width:auto;margin:0;}
}
@media(max-width:1100px) {
  #riderDashboard .rx-metrics {grid-template-columns:repeat(2,minmax(0,1fr));}
  #riderDashboard .rx-workspace {grid-template-columns:minmax(0,1fr);}
  #riderDashboard .rx-assignment-stack {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));}
  #riderDashboard .rx-map-panel {min-height:0;}
  #riderDashboard .rx-map-stage {height:440px;min-height:440px;}
  #riderDashboard .rx-side {grid-column:auto;}
}
@media(max-width:730px) {
  #riderDashboard .rx-assignment-stack {display:flex;}
  #riderDashboard .rx-side {display:flex;min-height:0;}
  #riderDashboard .rx-side-actions {display:grid;}
  #riderDashboard .rx-side-actions > .rx-route-button {width:100%;}
  #riderDashboard .rx-topbar {flex-direction:column;align-items:stretch;}
  #riderDashboard .rx-time-chip {align-self:flex-start;}
  #riderDashboard .rx-map-stage {height:365px;min-height:365px;}
  #riderDashboard .rx-lower {grid-template-columns:minmax(0,1fr);}
}
@media(max-width:590px) {
  #courierMain > main:has(#riderDashboard) {padding:18px 13px 35px!important;}
  #riderDashboard .rx-title {font-size:29px!important;}
  #riderDashboard .rx-eyebrow {font-size:9.5px;}
  #riderDashboard .rx-metrics {gap:11px;}
  #riderDashboard .rx-metric {min-height:105px;padding:15px 13px;gap:9px;}
  #riderDashboard .rx-metric-icon {flex:0 0 25px;width:25px;}
  #riderDashboard .rx-metric-icon svg {width:22px;height:22px;}
  #riderDashboard .rx-metric-label {font-size:10.5px;}
  #riderDashboard .rx-metric-value {font-size:21px;}
  #riderDashboard .rx-metric-note {font-size:9px;}
  #riderDashboard .rx-column-head,#riderDashboard .rx-map-top {padding:18px 16px 13px;}
  #courierMain #riderDashboard .rx-section-title {font-size:16px;}
  #riderDashboard .rx-heading-icon {width:19px;height:19px;}
  #riderDashboard .rx-list {padding:0 15px 17px;}
  #riderDashboard .rx-search {margin-inline:15px;}
  #riderDashboard .rx-route-footer {padding:15px 16px 18px;}
  #riderDashboard .rx-side {padding-inline:16px;}
  #riderDashboard .rx-lower-card {padding:18px 16px;}
  #riderDashboard .rx-chart-body {gap:15px;}
  #riderDashboard .rx-donut {width:109px;height:109px;}
  #riderDashboard .rx-today-grid {grid-template-columns:repeat(2,minmax(0,1fr));}
  #riderDashboard .rx-today {min-height:115px;padding:14px 12px;}
  #riderDashboard .rx-map-stage {height:320px;min-height:320px;}
}


/* Readability refresh — modest scale increase across the rider dashboard.
   Cart-like typography; flat neutral fills with only major cards floating. */
#riderDashboard {font-size:13.5px;line-height:1.65;}
body:has(#riderDashboard) #courierSidebarNav [data-courier-sidebar-item] {font-size:13px;}
body:has(#riderDashboard) #courierSidebarNav .courier-sidebar-section {font-size:10px;}
body:has(#riderDashboard) #courierMain > header h1 {font-size:20px;}
body:has(#riderDashboard) #courierMain > header input[type="search"] {font-size:13px;}
#courierMain #riderDashboard .rx-subtitle {font-size:14px;line-height:1.6;}
#riderDashboard .rx-time-chip {font-size:12.5px;}
#riderDashboard .rx-metric {min-height:121px;padding:19px 20px;}
#riderDashboard .rx-metric-label {font-size:13px;}
#riderDashboard .rx-metric-value {font-size:clamp(26px,1.9vw,30px);line-height:1.2;}
#riderDashboard .rx-metric-note {font-size:11.5px;line-height:1.55;}
#courierMain #riderDashboard .rx-section-title {font-size:18.5px;line-height:1.35;}
#courierMain #riderDashboard .rx-section-sub {font-size:12.5px;line-height:1.65;}
#riderDashboard .rx-count {font-size:13px;min-width:31px;height:31px;}
#riderDashboard .rx-search input {font-size:12.5px;}
#riderDashboard .rx-list-label {font-size:11px;}
#riderDashboard .rx-job-code {font-size:13px;}
#riderDashboard .rx-job-route {font-size:13px;}
#riderDashboard .rx-job-footer,#riderDashboard .rx-job-sub {font-size:11.5px;}
#riderDashboard .rx-tag {font-size:11px;}
#riderDashboard .rx-mini-empty {font-size:12.5px;}
#riderDashboard .rx-empty-illustration {min-height:165px;gap:13px;}
#riderDashboard .rx-empty-illustration strong {font-size:14px;}
#riderDashboard .rx-empty-illustration span {font-size:12px;max-width:270px;}
#riderDashboard .rx-idle-title {font-size:16px;}
#riderDashboard .rx-idle-copy {font-size:12.5px;max-width:285px;}
#riderDashboard .rx-idle-action {font-size:12.5px;min-height:44px;}
#riderDashboard .rx-map-maptools {font-size:11.5px;}
#riderDashboard .rx-map-note {font-size:11.5px;max-width:min(410px,calc(100% - 24px));}
#riderDashboard .rx-route-location span {font-size:11.5px;}
#riderDashboard .rx-route-location strong {font-size:13px;}
#riderDashboard .rx-route-code,#riderDashboard .rx-route-disclaimer {font-size:11px;}
#riderDashboard .rx-route-button {font-size:12.5px;}
#riderDashboard .rx-side .rx-column-head a {font-size:12.5px;}
#riderDashboard .rx-rider-name {font-size:14.5px;}
#riderDashboard .rx-rider-role {font-size:11.5px;}
#riderDashboard .rx-availability {font-size:12px;}
#riderDashboard .rx-rider-stat span {font-size:12.5px;}
#riderDashboard .rx-rider-stat strong {font-size:14px;}
#riderDashboard .rx-focus-title {font-size:15px;}
#riderDashboard .rx-focus-row {font-size:11.5px;}
#riderDashboard .rx-focus-row strong {font-size:12px;}
#riderDashboard .rx-focus-desc {font-size:11.5px;}
#riderDashboard .rx-side-actions > a:not(.rx-route-button) {font-size:12.5px;}
#riderDashboard .rx-lower-head > a {font-size:12px;min-height:36px;display:inline-flex;align-items:center;}
#riderDashboard .rx-donut-center strong {font-size:26px;}
#riderDashboard .rx-donut-center span {font-size:11px;}
#riderDashboard .rx-chart-legend-row {font-size:12.5px;}
#riderDashboard .rx-chart-legend-row strong {font-size:13.5px;}
#riderDashboard .rx-lower-note {font-size:11.5px;}

/* Today at a glance: one calm floating white card; flat four-cell statistics.
   Fine separators instead of four competing inset cards. */
#riderDashboard .rx-glance-card {padding:23px 24px 25px;}
#riderDashboard .rx-glance-card .rx-lower-head {gap:12px;}
#riderDashboard .rx-glance-card .rx-lower-head > a {border-color:#ece5d8;color:#936718;white-space:nowrap;}
#riderDashboard .rx-glance-card .rx-lower-head > a:hover {background:#fffaf1;}
#riderDashboard .rx-today-grid {
  display:grid;grid-template-columns:repeat(2,minmax(0,1fr));
  gap:0;margin-top:22px;border:1px solid #e8edf1;border-radius:14px;
  overflow:hidden;background:#fff;
}
#riderDashboard .rx-today {
  display:flex;flex-direction:column;align-items:flex-start;justify-content:center;
  min-width:0;min-height:132px;padding:19px 21px;
  margin:0;background:#fff;border:0;border-radius:0;box-shadow:none;
}
#riderDashboard .rx-today:nth-child(odd) {border-right:1px solid #e8edf1;}
#riderDashboard .rx-today:nth-child(-n+2) {border-bottom:1px solid #e8edf1;}
#riderDashboard .rx-today-top {display:flex;align-items:center;gap:10px;min-width:0;}
#riderDashboard .rx-today-icon,#riderDashboard .rx-today:nth-child(n) .rx-today-icon {
  display:grid;place-items:center;flex:none;width:21px;height:21px;
  background:transparent;color:#a47a2c!important;
}
#riderDashboard .rx-today svg {width:21px;height:21px;stroke-width:1.75;}
#riderDashboard .rx-today .rx-today-top .rx-today-label {
  display:block;margin:0;font-size:12.5px;font-weight:500;
  line-height:1.5;color:#6b7683;
}
#riderDashboard .rx-today strong {
  display:block;margin:14px 0 0;color:#1c2938;
  font-size:clamp(23px,2vw,29px);font-weight:600;
  letter-spacing:-.042em;line-height:1.25;font-variant-numeric:tabular-nums;
}
#riderDashboard .rx-today--earnings strong {color:#956612;}

@media(max-width:590px) {
  #riderDashboard {font-size:13px;}
  #riderDashboard .rx-metric {min-height:112px;padding:15px 12px;}
  #riderDashboard .rx-metric-label {font-size:11.5px;}
  #riderDashboard .rx-metric-value {font-size:clamp(21px,4.8vw,25px);}
  #riderDashboard .rx-metric-note {font-size:10px;}
  #courierMain #riderDashboard .rx-section-title {font-size:17px;}
  #courierMain #riderDashboard .rx-section-sub {font-size:12px;}
  #riderDashboard .rx-glance-card {padding:19px 15px 18px;}
  #riderDashboard .rx-today-grid {margin-top:17px;}
  #riderDashboard .rx-today {min-height:116px;padding:15px 13px;}
  #riderDashboard .rx-today .rx-today-top .rx-today-label {font-size:11px;}
  #riderDashboard .rx-today strong {font-size:clamp(20px,5vw,25px);margin-top:12px;}
  #riderDashboard .rx-today-icon {width:18px;height:18px;}
  #riderDashboard .rx-today svg {width:18px;height:18px;}
  #riderDashboard .rx-today-top {gap:6px;}
  #riderDashboard .rx-map-note {font-size:10.5px;}
  #riderDashboard .rx-route-location strong {font-size:12px;}
}
@media(max-width:350px) {
  #riderDashboard .rx-today {padding:13px 10px;}
  #riderDashboard .rx-today .rx-today-top .rx-today-label {font-size:10.5px;}
  #riderDashboard .rx-today strong {font-size:19px;}
}



/* SARI-RIDER-DASHBOARD-CLEAN-UI-20261101
   SARI RIDER DASHBOARD — clean professional visual refresh.
   CSS-only visual changes; all dashboard queries, actions, routes, clock and map JS stay intact. */
#riderDashboard {
  --rx-gold:#c88912;
  --rx-gold-hover:#b47b0e;
  --rx-ink:#2f3742;
  --rx-line:#e5e7eb;
  --rx-shadow:0 2px 5px rgba(15,23,42,.045),
              0 10px 24px rgba(15,23,42,.075),
              0 24px 54px rgba(15,23,42,.095),
              inset 0 1px 0 rgba(255,255,255,.92);
  max-width:1400px;
  margin-inline:auto;
  font:400 13px/1.65 'Poppins',ui-sans-serif,system-ui,sans-serif;
}
#courierMain > main:has(#riderDashboard) {
  padding:22px clamp(14px,2.2vw,30px) 42px!important;
}
#riderDashboard .rx-topbar {
  align-items:flex-end;
  margin-bottom:18px;
  gap:14px 22px;
}
#riderDashboard .rx-eyebrow {
  margin:0 0 6px;
  color:var(--rx-gold);
  font-size:10.5px;
  font-weight:800;
  line-height:1.4;
  letter-spacing:.14em;
  text-transform:uppercase;
}
#courierMain #riderDashboard .rx-title {
  color:var(--rx-ink);
  font-size:clamp(28px,2.35vw,33px);
  font-weight:760;
  letter-spacing:-.04em;
  line-height:1.05;
}
#riderDashboard .rx-time-chip {
  display:inline-flex;
  align-items:center;
  align-self:flex-end;
  gap:7px;
  margin:0;
  padding:9px 12px;
  border:1px solid #e5e7eb;
  border-radius:11px;
  background:#fff;
  color:#64748b;
  box-shadow:none;
  font-size:11px;
  font-weight:600;
  line-height:1.45;
}
#riderDashboard .rx-time-chip svg {
  width:15px;
  height:15px;
  color:var(--rx-gold);
}
#riderDashboard .rx-metrics {
  gap:14px;
  margin-bottom:18px;
}
#riderDashboard .rx-metric,
#riderDashboard .rx-column,
#riderDashboard .rx-lower-card {
  background:#fff;
  border:1px solid var(--rx-line);
  border-radius:18px;
  box-shadow:var(--rx-shadow);
}
#riderDashboard .rx-metric {
  min-height:110px;
  padding:14px 16px;
  gap:14px;
  align-items:center;
}
#riderDashboard .rx-metric-icon {
  flex:0 0 29px;
  width:29px;
  height:29px;
  background:transparent!important;
  border:0!important;
  box-shadow:none!important;
}
#riderDashboard .rx-metric:nth-child(1) .rx-metric-icon {color:#c88912!important;}
#riderDashboard .rx-metric:nth-child(2) .rx-metric-icon {color:#168a65!important;}
#riderDashboard .rx-metric:nth-child(3) .rx-metric-icon {color:#3b78d1!important;}
#riderDashboard .rx-metric:nth-child(4) .rx-metric-icon {color:#c88912!important;}
#riderDashboard .rx-metric-icon svg {width:25px;height:25px;stroke-width:1.8;}
#riderDashboard .rx-metric-label {
  color:#5f6d7c;
  font-size:12.5px;
  line-height:1.45;
  font-weight:600;
}
#riderDashboard .rx-metric-value {
  margin:5px 0 0;
  color:#1f2937;
  font-size:clamp(23px,1.9vw,29px);
  font-weight:650;
  line-height:1.13;
  letter-spacing:-.04em;
}
#riderDashboard .rx-metric-note {
  margin:5px 0 0;
  font-size:10.5px;
  line-height:1.5;
  font-weight:400;
  color:#8995a3;
}
#riderDashboard .rx-workspace,
#riderDashboard .rx-assignment-stack,
#riderDashboard .rx-lower {
  gap:18px;
}
#riderDashboard .rx-lower {margin-top:18px;}
#riderDashboard .rx-column-head,
#riderDashboard .rx-map-top {padding:19px 20px 15px;}
#courierMain #riderDashboard .rx-section-title {
  margin:0;
  color:var(--rx-ink);
  font-size:17px;
  font-weight:750;
  line-height:1.4;
  letter-spacing:-.025em;
}
#riderDashboard .rx-heading-icon {
  width:19px;
  height:19px;
  color:var(--rx-gold);
  stroke-width:1.8;
}
#courierMain #riderDashboard .rx-section-sub {
  color:#8b96a4;
  font-size:11.5px;
  line-height:1.6;
  margin-top:4px;
}
#riderDashboard .rx-count {
  min-width:27px;
  height:27px;
  background:#f8fafc;
  border:1px solid #e5e7eb;
  color:#64748b;
  font-size:11px;
  font-weight:700;
}
#riderDashboard .rx-search {
  height:43px;
  margin:4px 20px 15px;
  border:1px solid #dce2e8;
  border-radius:11px;
  background:#fff;
}
#riderDashboard .rx-search:focus-within {
  border-color:var(--rx-gold);
  box-shadow:none;
}
#riderDashboard .rx-search input {font-size:11.5px;}
#riderDashboard .rx-list {gap:9px;padding:0 20px 20px;}
#riderDashboard .rx-job {
  padding:13px 14px;
  border:1px solid #e7ebf0;
  background:#fff;
  border-radius:11px;
}
#riderDashboard .rx-job-primary {
  border:1px solid #e5e7eb;
  border-left:3px solid var(--rx-gold);
  padding-left:12px;
  background:#fff;
  box-shadow:none;
}
#riderDashboard .rx-job:hover,
#riderDashboard .rx-job-primary:hover {
  border-color:#d6ad63;
  background:#fafbfc;
  transform:none;
}
#riderDashboard .rx-job-code {color:#293440;font-size:11.5px;font-weight:750;}
#riderDashboard .rx-job-route {color:#5b6777;font-size:11.5px;}
#riderDashboard .rx-job-footer,
#riderDashboard .rx-job-sub {font-size:10.5px;}
#riderDashboard .rx-job-footer span:last-child {color:#a57317;font-weight:700;}
#riderDashboard .rx-tag {
  color:#667385;
  border:1px solid #e5e7eb;
  background:#f8fafc;
  font-size:10px;
  font-weight:650;
}
#riderDashboard .rx-tag-green {color:#18794e;border-color:#d1e7da;background:#f1faf5;}
#riderDashboard .rx-tag-amber {color:#8a651b;border-color:#e8dfcb;background:#faf9f7;}
#riderDashboard .rx-idle {
  min-height:195px;
  border:1px solid #e8edf2;
  background:#f8fafc;
  border-radius:12px;
}
#riderDashboard .rx-idle-icon {color:#c88912;}
#riderDashboard .rx-idle-action,
#riderDashboard .rx-route-button {
  background:var(--rx-gold);
  border:1px solid var(--rx-gold);
  color:#fff!important;
  border-radius:11px;
  box-shadow:none;
  font-weight:700;
}
#riderDashboard .rx-idle-action:hover,
#riderDashboard .rx-route-button:hover {
  background:var(--rx-gold-hover);
  border-color:var(--rx-gold-hover);
  box-shadow:none;
}
#riderDashboard .rx-map-panel {min-height:570px;}
#riderDashboard .rx-map-top {min-height:70px;}
#riderDashboard .rx-map-stage {
  height:415px;
  min-height:415px;
  margin:0 8px;
  border-radius:12px;
  background:#e8edf2;
}
#riderDashboard .rx-map-maptools,
#riderDashboard .rx-map-note {
  border:1px solid #e2e8f0;
  background:rgba(255,255,255,.97);
  color:#64748b;
  box-shadow:0 3px 10px rgba(15,23,42,.075);
}
#riderDashboard .rx-route-footer {gap:12px;padding:15px 20px 18px;}
#riderDashboard .rx-route-location span {font-size:10.5px;color:#8b96a4;}
#riderDashboard .rx-route-location strong {font-size:11.5px;color:#344357;}
#riderDashboard .rx-route-code,
#riderDashboard .rx-route-disclaimer {font-size:10px;}
#riderDashboard .rx-side {min-height:570px;padding:0 20px 18px;}
#riderDashboard .rx-side .rx-column-head {padding:19px 0 15px;}
#riderDashboard .rx-side .rx-column-head a {color:#996e16;font-size:11px;font-weight:700;}
#riderDashboard .rx-rider-head {padding:18px 0 14px;}
#riderDashboard .rx-rider-name {font-size:13px;}
#riderDashboard .rx-availability {font-size:11px;}
#riderDashboard .rx-rider-stat {padding:8px 0;}
#riderDashboard .rx-rider-stat span {font-size:11px;}
#riderDashboard .rx-rider-stat strong {font-size:12px;}
#riderDashboard .rx-focus-title {font-size:12.5px;font-weight:750;}
#riderDashboard .rx-focus-row {font-size:10.5px;}
#riderDashboard .rx-focus-row strong {font-size:11px;}
#riderDashboard .rx-side-actions > a:not(.rx-route-button) {font-size:11px;}
#riderDashboard .rx-lower-card {padding:19px 20px;}
#riderDashboard .rx-donut {
  width:122px;
  height:122px;
  background:conic-gradient(var(--rx-gold) 0 var(--donut-value),#e2e8f0 var(--donut-value) 100%);
}
#riderDashboard .rx-donut.is-empty {background:#e9edf2;}
#riderDashboard .rx-donut-center strong {font-size:23px;}
#riderDashboard .rx-chart-legend-row {font-size:11.5px;}
#riderDashboard .rx-chart-legend-row strong {font-size:12px;}
#riderDashboard .rx-chart-dot {background:var(--rx-gold);}
#riderDashboard .rx-chart-dot--gold {background:#cbd5e1;}
#riderDashboard .rx-chart-dot--muted {background:#94a3b8;}
#riderDashboard .rx-glance-card {padding:19px 20px;}
#riderDashboard .rx-today-grid {margin-top:17px;border-color:#e5e7eb;}
#riderDashboard .rx-today {
  min-height:112px;
  padding:15px 17px;
  background:#fff;
}
#riderDashboard .rx-today:nth-child(odd) {border-right-color:#e5e7eb;}
#riderDashboard .rx-today:nth-child(-n+2) {border-bottom-color:#e5e7eb;}
#riderDashboard .rx-today:nth-child(1) .rx-today-icon {color:#168a65!important;}
#riderDashboard .rx-today:nth-child(2) .rx-today-icon {color:#3b78d1!important;}
#riderDashboard .rx-today:nth-child(3) .rx-today-icon {color:#8b5cf6!important;}
#riderDashboard .rx-today:nth-child(4) .rx-today-icon {color:var(--rx-gold)!important;}
#riderDashboard .rx-today .rx-today-top .rx-today-label {
  color:#657487;
  font-size:11px;
  font-weight:600;
}
#riderDashboard .rx-today strong {
  margin-top:10px;
  color:#293440;
  font-size:clamp(21px,1.8vw,25px);
  line-height:1.15;
  font-weight:650;
}
#riderDashboard .rx-today--earnings strong {color:#936915;}
@media (hover:hover) {
  #riderDashboard .rx-metric:hover {
    transform:translateY(-2px);
    border-color:#d5dce4;
    box-shadow:0 5px 16px rgba(15,23,42,.045),0 16px 30px rgba(15,23,42,.07);
  }
}
@media (max-width:1480px) {
  #riderDashboard .rx-workspace {
    grid-template-columns:minmax(250px,.92fr) minmax(0,1.5fr);
  }
  #riderDashboard .rx-side {min-height:0;}
}
@media (max-width:1100px) {
  #riderDashboard .rx-workspace {grid-template-columns:minmax(0,1fr);}
  #riderDashboard .rx-map-panel {min-height:0;}
  #riderDashboard .rx-map-stage {height:400px;min-height:400px;}
}
@media (max-width:767px) {
  #courierMain > main:has(#riderDashboard) {padding:16px 14px 28px!important;}
  #courierMain #riderDashboard .rx-title {font-size:25px;}
  #riderDashboard .rx-eyebrow {font-size:9.5px;}
  #riderDashboard .rx-metrics {gap:10px;}
  #riderDashboard .rx-metric {min-height:106px;padding:13px 14px;gap:9px;}
  #riderDashboard .rx-metric-icon {flex:0 0 25px;width:25px;height:25px;}
  #riderDashboard .rx-metric-icon svg {width:22px;height:22px;}
  #riderDashboard .rx-metric-value {font-size:24px;}
  #riderDashboard .rx-metric-label {font-size:11px;}
  #riderDashboard .rx-metric-note {font-size:10px;}
  #riderDashboard .rx-column-head,#riderDashboard .rx-map-top {padding:17px 16px 13px;}
  #riderDashboard .rx-list {padding:0 16px 17px;}
  #riderDashboard .rx-search {margin-inline:16px;}
  #riderDashboard .rx-map-stage {height:345px;min-height:345px;}
  #riderDashboard .rx-side {padding-inline:16px;}
  #riderDashboard .rx-lower-card,#riderDashboard .rx-glance-card {padding:17px 16px;}
}
@media (max-width:590px) {
  #courierMain #riderDashboard .rx-title {font-size:25px!important;}
  #riderDashboard .rx-time-chip {font-size:10.5px;padding:8px 10px;}
  #riderDashboard .rx-metric {min-height:103px;padding:12px 11px;}
  #riderDashboard .rx-metric-value {font-size:22px;}
  #riderDashboard .rx-metric-label {font-size:10.5px;}
  #riderDashboard .rx-metric-note {font-size:9.5px;}
  #courierMain #riderDashboard .rx-section-title {font-size:16px;}
  #riderDashboard .rx-map-stage {height:305px;min-height:305px;}
  #riderDashboard .rx-today {min-height:106px;padding:13px 12px;}
  #riderDashboard .rx-today strong {font-size:21px;}
  #riderDashboard .rx-today .rx-today-top .rx-today-label {font-size:10.5px;}
}
@media (max-width:380px) {
  #riderDashboard .rx-metric {gap:6px;padding:11px 9px;}
  #riderDashboard .rx-metric-value {font-size:20px;}
  #riderDashboard .rx-metric-label {font-size:10px;}
  #riderDashboard .rx-today {padding:11px 10px;}
}


/* SARI-RIDER-MULTI-PARCEL-ETA-V2: road distance + ETA for same-barangay delivery batches */
#riderDashboard .rx-route-estimates {
  display:grid;grid-template-columns:repeat(2,minmax(0,1fr));
  gap:10px;padding:12px 16px 0;
}
#riderDashboard .rx-route-estimate {
  min-width:0;display:flex;flex-direction:column;gap:4px;
  padding:12px 14px;border:1px solid #e5eaf0;border-radius:11px;
  background:#f8fafc;
}
#riderDashboard .rx-route-estimate .rx-estimate-heading {
  display:flex;align-items:center;gap:6px;min-width:0;
  font-size:10.5px;font-weight:650;color:#697789;
}
#riderDashboard .rx-route-estimate .rx-estimate-heading svg {width:15px;height:15px;color:#b07c17;flex:0 0 15px;}
#riderDashboard .rx-route-estimate strong {font-size:20px;font-weight:750;color:#273448;line-height:1.4;font-variant-numeric:tabular-nums;overflow-wrap:anywhere;}
#riderDashboard .rx-route-estimate small {font-size:10px;line-height:1.5;color:#7a889a;}
#riderDashboard .rx-route-estimate.is-gps {background:#f7faf8;}
#riderDashboard .rx-route-estimate.is-gps .rx-estimate-heading svg {color:#168a65;}
#riderDashboard .rx-route-summary-foot {display:flex;align-items:center;justify-content:space-between;gap:10px 14px;flex-wrap:wrap;padding:11px 17px 2px;}
#riderDashboard .rx-route-source {margin:0;flex:1;min-width:150px;font-size:10px;line-height:1.65;color:#7b8794;}
#riderDashboard .rx-refresh-route {display:inline-flex;gap:6px;align-items:center;justify-content:center;padding:6px 9px;border:1px solid #e3e7eb;border-radius:8px;background:#fff;color:#966b18;font-size:10.5px;font-weight:700;cursor:pointer;}
#riderDashboard .rx-refresh-route:hover {border-color:#c88912;background:#f8fafc;}
#riderDashboard .rx-refresh-route:disabled {opacity:.55;cursor:default;}
#riderDashboard .rx-refresh-route svg {width:14px;height:14px;}
#riderDashboard .rx-map-note {z-index:500;}
@media(max-width:480px) {
  #riderDashboard .rx-route-estimates {gap:8px;padding:10px 11px 0;}
  #riderDashboard .rx-route-estimate {padding:11px 10px;}
  #riderDashboard .rx-route-estimate strong {font-size:17px;}
  #riderDashboard .rx-route-estimate .rx-estimate-heading {font-size:9.5px;}
  #riderDashboard .rx-route-summary-foot {padding:10px 11px 2px;}
}

@endverbatim
</style>
@endpush

@section('content')
@php
    // Presentation-only values: no database operations or status mutations.
    $hasMarketplaceWork = $currentDelivery instanceof \App\Models\Orders\MarketplaceOrder;
    $isLogisticsPickup = $hasMarketplaceWork && (bool) $currentDelivery->logisticsParcel;
    $riderName = trim((string) ($rider->first_name ?? '').' '.(string) ($rider->last_name ?? '')) ?: 'SARI Rider';
    $riderInitial = mb_strtoupper(mb_substr($riderName, 0, 1));
    $availability = strtolower(trim((string) ($rider->availability_status ?? 'offline')));

    $pickupWaiting = max(0, (int) ($stats['pickup_waiting'] ?? 0));
    $pickupActive = max(0, (int) ($stats['pickup_active'] ?? 0));
    $lastMileWaiting = max(0, (int) ($stats['last_mile_waiting'] ?? 0));
    $lastMileActive = max(0, (int) ($stats['last_mile_active'] ?? 0));
    $lastMileTotal = $lastMileWaiting + $lastMileActive;
    $completedToday = max(0, (int) ($stats['completed_today'] ?? 0));
    $earningsToday = (float) ($stats['earnings_today'] ?? 0);

    // An active marketplace order is the primary focus; a last-mile batch is the fallback.
    $focusType = $hasMarketplaceWork ? ($isLogisticsPickup ? 'pickup' : 'direct') : ($currentLastMile ? 'lastmile' : 'idle');
    $focusTitle = match ($focusType) {
        'pickup' => 'Seller pickup → Origin hub',
        'direct' => 'Seller → Buyer delivery',
        'lastmile' => 'Destination hub → Buyer',
        default => 'No active route',
    };
    $focusCode = $hasMarketplaceWork
        ? (string) $currentDelivery->order_number
        : ($currentLastMile ? (string) $currentLastMile->assignment_code : 'Awaiting dispatch');
    $focusStatus = $hasMarketplaceWork
        ? (string) $currentDelivery->statusLabel()
        : ($currentLastMile ? str_replace('_', ' ', (string) $currentLastMile->status) : 'Standby');
    $routeStart = $hasMarketplaceWork
        ? ((string) ($currentDelivery->pickup_name ?: $currentDelivery->seller?->store_name ?: 'Seller pickup'))
        : ($currentLastMile ? (string) $currentLastMile->facility_name : 'Start');
    $routeEnd = $hasMarketplaceWork
        ? ($isLogisticsPickup ? 'Origin hub' : ((string) ($currentDelivery->buyer_name ?: 'Buyer')))
        : ($currentLastMile ? (string) $currentLastMile->delivery_area : 'Destination');
    $focusUrl = $hasMarketplaceWork
        ? route('courier.delivery.show', ['order' => $currentDelivery->id])
        : ($currentLastMile ? route('courier.last-mile') : route('courier.requests'));
    $focusAction = $hasMarketplaceWork || $currentLastMile ? 'Continue workflow' : 'Browse pickup work';

    // Avoid displaying the current order again in the waiting list.
    $waitingAssignments = $assignments->reject(fn ($order) => $hasMarketplaceWork && $order->id === $currentDelivery->id)->take(5);
    $otherLastMileAssignments = $lastMileAssignments->reject(fn ($batch) => $currentLastMile && $batch->id === $currentLastMile->id);

    // No geocoding or guessed positions: use persisted points for this rider's assigned work.
    $mapStops = collect();
    if ($hasMarketplaceWork) {
        $allowedStopKeys = $isLogisticsPickup
            ? ['seller', 'origin_facility']
            : ['seller', 'buyer'];
        foreach ($currentDelivery->deliveryRouteStops as $stop) {
            if (! in_array((string) $stop->stop_key, $allowedStopKeys, true)) continue;
            if ($stop->latitude === null || $stop->longitude === null) continue;
            $lat = (float) $stop->latitude;
            $lng = (float) $stop->longitude;
            if (! is_finite($lat) || ! is_finite($lng) || abs($lat) > 90 || abs($lng) > 180 || ($lat === 0.0 && $lng === 0.0)) continue;
            $mapStops->push([
                'lat' => $lat,
                'lng' => $lng,
                'name' => (string) ($stop->name ?: ($stop->stop_key === 'seller' ? 'Seller pickup' : 'Destination')),
                'type' => $stop->stop_key === 'seller' ? 'pickup' : 'destination',
            ]);
        }
    } elseif ($currentLastMile) {
        // Before starting: route begins at the hub. Once out for delivery:
        // start with remaining Buyer destinations; GPS estimates next stop.
        if ($currentLastMile->status === 'assigned'
            && $currentLastMile->facility_latitude !== null
            && $currentLastMile->facility_longitude !== null) {
            $lat = (float) $currentLastMile->facility_latitude;
            $lng = (float) $currentLastMile->facility_longitude;
            if (is_finite($lat) && is_finite($lng) && abs($lat) <= 90 && abs($lng) <= 180 && ($lat !== 0.0 || $lng !== 0.0)) {
                $mapStops->push(['lat' => $lat, 'lng' => $lng, 'name' => (string) $currentLastMile->facility_name, 'type' => 'pickup']);
            }
        }
        // Destination pins are limited to this rider's current last-mile batch.
        foreach (($lastMileDeliveryMapStops ?? collect()) as $stop) {
            if ($stop->latitude === null || $stop->longitude === null) continue;
            $lat = (float) $stop->latitude;
            $lng = (float) $stop->longitude;
            if (! is_finite($lat) || ! is_finite($lng) || abs($lat) > 90 || abs($lng) > 180 || ($lat === 0.0 && $lng === 0.0)) continue;
            $mapStops->push(['lat' => $lat, 'lng' => $lng, 'name' => (string) ($stop->name ?: 'Delivery destination').' · '.(string) ($stop->order_number ?? ''), 'type' => 'destination']);
        }
    }

    $workloadScale = max(1, $pickupWaiting + $pickupActive, $lastMileTotal);
    $pickupWaitingWidth = ($pickupWaiting / $workloadScale) * 100;
    $pickupActiveWidth = ($pickupActive / $workloadScale) * 100;
    $lastMileWaitingWidth = ($lastMileWaiting / $workloadScale) * 100;
    $lastMileActiveWidth = ($lastMileActive / $workloadScale) * 100;

    // Chart values are computed solely from real dashboard statistics.
    $totalWorkload = $pickupWaiting + $pickupActive + $lastMileWaiting + $lastMileActive;
    $workInProgress = $pickupActive + $lastMileActive;
    $workWaiting = $pickupWaiting + $lastMileWaiting;
    $progressChartPercent = $totalWorkload > 0 ? round(($workInProgress / $totalWorkload) * 100, 2) : 0;
    $pickupChartPercent = $totalWorkload > 0 ? round((($pickupWaiting + $pickupActive) / $totalWorkload) * 100, 2) : 0;
    $visibleAssignmentsCount = (int) $hasMarketplaceWork + (int) (bool) $currentLastMile;
    $waitingAssignmentsCount = $waitingAssignments->count() + $otherLastMileAssignments->count();
@endphp

<div id="riderDashboard">
    @if(session('success'))
        <div class="rx-alert rx-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rx-alert rx-alert-error" role="alert">{{ $errors->first() }}</div>
    @endif

    {{-- The existing Courier layout owns the sidebar, global search, and notifications. --}}
    <div class="rx-topbar">
        <div>
            <span class="rx-eyebrow">RIDER OPERATIONS</span>
            <h2 class="rx-title" data-rider-greeting>Welcome back, {{ $riderName }}</h2>
        </div>
        <div class="rx-time-chip" aria-label="Current date and time in Manila">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            <span data-rider-day>Today</span><span aria-hidden="true">·</span><span data-rider-clock>--:--</span> PHT
        </div>
    </div>

    <section class="rx-metrics" aria-label="Rider performance summary">
        <article class="rx-metric">
            <span class="rx-metric-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 8h16v12H4zM8 8V5h8v3M4 13h16"/></svg></span>
            <div class="rx-metric-copy"><span class="rx-metric-label">Pickup queue</span><strong class="rx-metric-value">{{ number_format($pickupWaiting) }}</strong><span class="rx-metric-note">Waiting for pickup</span></div>
        </article>
        <article class="rx-metric">
            <span class="rx-metric-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 3h12v18H6zM9 8h6m-6 4h6m-6 4h3"/><path d="m14.5 16 1.3 1.3 2.7-3"/></svg></span>
            <div class="rx-metric-copy"><span class="rx-metric-label">Active work</span><strong class="rx-metric-value">{{ number_format((int) ($stats['active_deliveries'] ?? 0)) }}</strong><span class="rx-metric-note">Pickups &amp; deliveries</span></div>
        </article>
        <article class="rx-metric">
            <span class="rx-metric-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h12v11H3zM15 10h4l2 4v4h-6"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg></span>
            <div class="rx-metric-copy"><span class="rx-metric-label">Last-mile batches</span><strong class="rx-metric-value">{{ number_format($lastMileTotal) }}</strong><span class="rx-metric-note">Assigned batches</span></div>
        </article>
        <article class="rx-metric">
            <span class="rx-metric-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M16 6V4H5v2M16 12h5M16 15h2"/><circle cx="15" cy="15" r=".5"/></svg></span>
            <div class="rx-metric-copy"><span class="rx-metric-label">Today's earnings</span><strong class="rx-metric-value">₱{{ number_format($earningsToday, 2) }}</strong><span class="rx-metric-note">{{ number_format($completedToday) }} completed today</span></div>
        </article>
    </section>

    <section class="rx-workspace" aria-label="Courier dispatch workspace">
        {{-- Assignment panels are distinct floating cards, not one enclosing container. --}}
        <div class="rx-assignment-stack">
            <section class="rx-column rx-assignment-card" aria-labelledby="rxMyAssignmentsTitle">
                <div class="rx-column-head">
                    <div><h3 id="rxMyAssignmentsTitle" class="rx-section-title"><svg class="rx-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h10l4 4v14H3V3h4Zm6 0v5h8M7 12h10M7 16h7"/></svg>My assignments</h3><p class="rx-section-sub">Your current pickup and delivery work</p></div>
                    <span class="rx-count" title="Current assignments">{{ $visibleAssignmentsCount }}</span>
                </div>
                @if($hasMarketplaceWork || $currentLastMile)
                    <label class="rx-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4 4"/></svg>
                        <input type="search" data-job-search autocomplete="off" placeholder="Search order or batch..." aria-label="Filter my assignments">
                    </label>
                @endif
                <div class="rx-list {{ $hasMarketplaceWork || $currentLastMile ? '' : 'rx-list--idle' }}" data-job-list>
                    @if($hasMarketplaceWork || $currentLastMile)
                        <p class="rx-list-label">Current work</p>
                    @endif
                    @if($hasMarketplaceWork)
                        <a data-job-item class="rx-job rx-job-primary" href="{{ route('courier.delivery.show', ['order' => $currentDelivery->id]) }}">
                            <span class="rx-job-top"><span class="rx-job-code">{{ $currentDelivery->order_number }}</span><span class="rx-tag">{{ $currentDelivery->statusLabel() }}</span></span>
                            <span class="rx-job-route">{{ $currentDelivery->pickup_name ?: $currentDelivery->seller?->store_name ?: 'Seller' }} → {{ $isLogisticsPickup ? 'Origin hub' : ($currentDelivery->buyer_name ?: 'Buyer') }}</span>
                            <span class="rx-job-footer"><span>{{ $isLogisticsPickup ? 'Seller pickup' : 'Direct delivery' }}</span><span>Open →</span></span>
                        </a>
                    @endif
                    @if($currentLastMile)
                        <a data-job-item class="rx-job {{ $hasMarketplaceWork ? '' : 'rx-job-primary' }}" href="{{ route('courier.last-mile') }}">
                            <span class="rx-job-top"><span class="rx-job-code">{{ $currentLastMile->assignment_code }}</span><span class="rx-tag rx-tag-green">{{ str_replace('_', ' ', (string) $currentLastMile->status) }}</span></span>
                            <span class="rx-job-route">{{ $currentLastMile->facility_name }} → {{ $currentLastMile->delivery_area }}</span>
                            <span class="rx-job-footer"><span>{{ $currentLastMile->parcel_count }} {{ $currentLastMile->parcel_count == 1 ? 'parcel' : 'parcels' }}</span><span>Open →</span></span>
                        </a>
                    @endif
                    @unless($hasMarketplaceWork || $currentLastMile)
                        <div class="rx-idle" role="status">
                            <svg class="rx-idle-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M10 15.5 24 8l14 7.5v17L24 40l-14-7.5z"/>
                                <path d="m10 15.5 14 8 14-8M24 23.5V40"/>
                                <path d="m18.8 31 3.4 3.4 7-7"/>
                            </svg>
                            <h4 class="rx-idle-title">All caught up!</h4>
                            <p class="rx-idle-copy">No active assignments. New jobs will appear here when Logistics dispatches them.</p>
                            <a class="rx-idle-action" href="{{ route('courier.requests') }}" aria-label="View your pickup queue">
                                View Pickup Queue
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </a>
                        </div>
                    @endunless
                    <div class="rx-mini-empty" data-search-empty hidden>No assignments match your search.</div>
                </div>
            </section>

            <section class="rx-column rx-wait-card" aria-labelledby="rxWaitingAssignmentsTitle">
                <div class="rx-column-head">
                    <div><h3 id="rxWaitingAssignmentsTitle" class="rx-section-title"><svg class="rx-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4m8-4v4M4 10h16m-12 4h4m-4 3h7"/></svg>Waiting assignments</h3><p class="rx-section-sub">Upcoming jobs and delivery batches</p></div>
                    <span class="rx-count">{{ $waitingAssignmentsCount }}</span>
                </div>
                <div class="rx-list" data-job-list>
                    @foreach($waitingAssignments as $order)
                        <a data-job-item class="rx-job" href="{{ route('courier.delivery.show', ['order' => $order->id]) }}">
                            <span class="rx-job-top"><span class="rx-job-code">{{ $order->order_number }}</span><span class="rx-tag rx-tag-amber">Assigned</span></span>
                            <span class="rx-job-route">{{ $order->pickup_name ?: $order->seller?->store_name ?: 'Seller' }} → {{ $order->logisticsParcel ? 'Origin hub' : ($order->buyer_name ?: 'Buyer') }}</span>
                            <span class="rx-job-sub">{{ $order->logisticsParcel ? 'Seller pickup' : 'Direct delivery' }}</span>
                        </a>
                    @endforeach
                    @foreach($otherLastMileAssignments as $batch)
                        <a data-job-item class="rx-job" href="{{ route('courier.last-mile') }}">
                            <span class="rx-job-top"><span class="rx-job-code">{{ $batch->assignment_code }}</span><span class="rx-tag rx-tag-green">{{ str_replace('_', ' ', (string) $batch->status) }}</span></span>
                            <span class="rx-job-route">{{ $batch->facility_name }} → {{ $batch->delivery_area }}</span>
                            <span class="rx-job-sub">{{ $batch->parcel_count }} {{ $batch->parcel_count == 1 ? 'parcel' : 'parcels' }}</span>
                        </a>
                    @endforeach
                    @if($waitingAssignmentsCount === 0)
                        <div class="rx-empty-illustration" data-no-waiting>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 8 12 3l9 5v10l-9 5-9-5zM3 8l9 5 9-5M12 13v10"/></svg>
                            <div><strong>No additional jobs waiting.</strong><span>You'll see new assignments here once they're available.</span></div>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Real OpenStreetMap tiles. Saved-stop routing/ETA uses a configurable OSRM-compatible provider. --}}
        <section class="rx-column rx-map-panel" aria-labelledby="rxMapTitle">
            <div class="rx-map-top">
                <div><h3 id="rxMapTitle" class="rx-section-title"><svg class="rx-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h4m10 12h4M7 6c4 0 4 12 10 12M12 8l2-2-2-2"/><circle cx="6" cy="6" r="2"/><circle cx="18" cy="18" r="2"/></svg>Delivery route overview</h3><p class="rx-section-sub">{{ $focusTitle }}</p></div>
                <div class="rx-map-tools"><span class="rx-tag rx-tag-green">Street map</span>@if($currentLastMile && !$hasMarketplaceWork)<span class="rx-tag">{{ (int) $currentLastMile->parcel_count }} parcels · {{ $currentLastMile->delivery_area }}</span>@endif</div>
            </div>
            <div class="rx-map-stage" aria-label="Interactive OpenStreetMap showing saved delivery locations">
                <div id="sariRiderLeafletMap" class="rx-leaflet-map" role="region" aria-label="Interactive street map"></div>
                <div class="rx-map-maptools"><i class="rx-live-map-dot" aria-hidden="true"></i><span>OpenStreetMap</span></div>
                <div class="rx-map-locate">
                    <button type="button" data-map-fit title="Fit saved delivery locations" aria-label="Fit map to delivery locations"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M8 3H4v4M16 3h4v4M8 21H4v-4M16 21h4v-4"/><circle cx="12" cy="12" r="3"/></svg></button>
                    <button type="button" data-map-my-location title="Show my device position (permission required)" aria-label="Show my device location"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m12 22 7-17-17 7 8 2z"/></svg></button>
                </div>
                <div class="rx-map-note" role="status" aria-live="polite" data-map-note>Loading OpenStreetMap…</div>
            </div>

{{-- Road-route estimates; only populated after the router returns a valid driving route. --}}
<div class="rx-route-estimates" aria-label="Driving distance and estimated travel time">
    <div class="rx-route-estimate">
        <span class="rx-estimate-heading"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 17h4l4-10 4 10h6M12 3v3M12 18v3"/></svg>Assigned driving route</span>
        <strong data-map-route-distance>— km</strong>
        <small data-map-route-duration>ETA unavailable</small>
    </div>
    <div class="rx-route-estimate is-gps">
        <span class="rx-estimate-heading"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 22 7-17-17 7 8 2z"/></svg>My location → next stop</span>
        <strong data-map-gps-distance>— km</strong>
        <small data-map-gps-duration>Tap My Location to estimate</small>
    </div>
</div>
<div class="rx-route-summary-foot">
    <p class="rx-route-source" data-map-route-status role="status" aria-live="polite">Checking saved route coordinates…</p>
    <button type="button" class="rx-refresh-route" data-map-route-refresh title="Recalculate road distance and travel estimate">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 11a8 8 0 1 0-2 6M20 4v7h-7"/></svg>Refresh ETA
    </button>
</div>
            <div class="rx-route-footer">
                <div class="rx-route-locations">
                    <div class="rx-route-location"><i class="rx-route-location-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></i><div><span>Starting point</span><strong>{{ $routeStart }}</strong></div></div>
                    <div class="rx-route-arrow" aria-hidden="true">→</div>
                    <div class="rx-route-location is-destination"><i class="rx-route-location-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></i><div><span>Next destination</span><strong>{{ $routeEnd }}</strong></div></div>
                </div>
                <div class="rx-route-meta"><span class="rx-route-code">{{ $focusCode }} · {{ $focusStatus }}</span><span class="rx-route-disclaimer">Driving only · no live traffic/stop time</span></div>
            </div>
        </section>

        <aside class="rx-column rx-side" aria-labelledby="rxRiderStatusTitle">
            <div class="rx-column-head"><h3 id="rxRiderStatusTitle" class="rx-section-title"><svg class="rx-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21c0-4.3 3-7 7-7s7 2.7 7 7"/></svg>Rider status</h3><a href="{{ route('courier.profile') }}">Manage</a></div>
            <div class="rx-rider-head"><span class="rx-avatar" aria-hidden="true">{{ $riderInitial }}</span><div><p class="rx-rider-name">{{ $riderName }}</p><p class="rx-rider-role">SARI delivery partner</p></div></div>
            <div class="rx-availability"><i class="rx-status-dot" data-state="{{ $availability }}" aria-hidden="true"></i><span>{{ ucfirst($availability) }} availability</span></div>
            <div class="rx-rider-stats">
                <div class="rx-rider-stat"><span>Completed today</span><strong>{{ number_format($completedToday) }}</strong></div>
                <div class="rx-rider-stat"><span>Active jobs</span><strong>{{ number_format((int) ($stats['active_deliveries'] ?? 0)) }}</strong></div>
                <div class="rx-rider-stat"><span>Pickup waiting</span><strong>{{ number_format($pickupWaiting) }}</strong></div>
                <div class="rx-rider-stat"><span>Last-mile active</span><strong>{{ number_format($lastMileActive) }}</strong></div>
            </div>
            <div class="rx-focus-card">
                <h3 class="rx-focus-title">Current focus</h3>
                <div class="rx-focus-data">
                    <div class="rx-focus-row"><span>Job / batch</span><strong>{{ $focusCode }}</strong></div>
                    <div class="rx-focus-row"><span>Status</span><strong>{{ $focusStatus }}</strong></div>
                    <div class="rx-focus-row"><span>Workflow</span><strong>{{ $focusTitle }}</strong></div>
                    @if($currentLastMile && !$hasMarketplaceWork)
                        <div class="rx-focus-row"><span>Parcels in batch</span><strong>{{ number_format((int) $currentLastMile->parcel_count) }}</strong></div>
                    @endif
                </div>
                <p class="rx-focus-desc">@if($focusType === 'pickup') Continue the seller pickup until the origin hub accepts the parcel. @elseif($focusType === 'direct') Continue the assigned direct delivery until the buyer handoff. @elseif($focusType === 'lastmile') Deliver the destination-hub batch to its assigned buyers. @else You have no active route. Waiting assignments can be viewed in Pickup Assignments. @endif</p>
            </div>
            <nav class="rx-side-actions" aria-label="Rider shortcuts">
                <a class="rx-route-button" href="{{ $focusUrl }}">{{ $focusAction }} →</a>
                <a href="{{ route('courier.last-mile') }}">Last-mile deliveries <span aria-hidden="true">→</span></a>
                <a href="{{ route('courier.messages') }}">Messages <span aria-hidden="true">→</span></a>
                <a href="{{ route('courier.profile') }}">Rider profile <span aria-hidden="true">→</span></a>
            </nav>
        </aside>
    </section>

    <section class="rx-lower" aria-label="Daily performance">
        <article class="rx-lower-card">
            <div class="rx-lower-head"><div><h3 class="rx-section-title"><svg class="rx-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v9l7.5 4.5"/><circle cx="12" cy="12" r="9"/></svg>Current workload breakdown</h3><p class="rx-section-sub">Waiting vs. in-progress assignments</p></div><a href="{{ route('courier.requests') }}">View assignments →</a></div>
            <div class="rx-chart-body">
                <div class="rx-donut {{ $totalWorkload === 0 ? 'is-empty' : '' }}" style="--donut-value: {{ $progressChartPercent }}%" role="img" aria-label="{{ $totalWorkload }} total workload units: {{ $workInProgress }} in progress, {{ $workWaiting }} waiting"><div class="rx-donut-center"><strong>{{ number_format($totalWorkload) }}</strong><span>Total work</span></div></div>
                <div class="rx-chart-legend">
                    <div class="rx-chart-legend-row"><span class="rx-chart-legend-label"><i class="rx-chart-dot"></i>In progress</span><strong>{{ $workInProgress }}</strong></div>
                    <div class="rx-chart-legend-row"><span class="rx-chart-legend-label"><i class="rx-chart-dot rx-chart-dot--gold"></i>Waiting</span><strong>{{ $workWaiting }}</strong></div>
                    <div class="rx-chart-legend-row"><span class="rx-chart-legend-label"><i class="rx-chart-dot rx-chart-dot--muted"></i>Pickup / direct</span><strong>{{ $pickupWaiting + $pickupActive }}</strong></div>
                    <div class="rx-chart-legend-row"><span class="rx-chart-legend-label"><i class="rx-chart-dot rx-chart-dot--muted"></i>Last-mile batches</span><strong>{{ $lastMileTotal }}</strong></div>
                </div>
            </div>
            <p class="rx-lower-note">Last-mile values represent batches, not individual parcels.</p>
        </article>
        <article class="rx-lower-card rx-glance-card">
            <div class="rx-lower-head"><div><h3 class="rx-section-title"><svg class="rx-heading-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 14l3-3 2 2 3-4"/></svg>Today at a glance</h3><p class="rx-section-sub">Your latest operational totals</p></div><a href="{{ route('courier.earnings') }}">Earnings →</a></div>
            <div class="rx-today-grid" aria-label="Today’s activity summary">
                <div class="rx-today">
                    <div class="rx-today-top">
                        <span class="rx-today-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m4 12 5 5L20 6"/></svg></span>
                        <span class="rx-today-label">Completed jobs</span>
                    </div>
                    <strong>{{ number_format($completedToday) }}</strong>
                </div>
                <div class="rx-today">
                    <div class="rx-today-top">
                        <span class="rx-today-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h16v13H4zM8 7V4h8v3M4 12h16"/></svg></span>
                        <span class="rx-today-label">Pickup active</span>
                    </div>
                    <strong>{{ number_format($pickupActive) }}</strong>
                </div>
                <div class="rx-today">
                    <div class="rx-today-top">
                        <span class="rx-today-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h11v12H4zM15 10h4l2 4v5h-6"/><circle cx="8" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg></span>
                        <span class="rx-today-label">Last-mile active</span>
                    </div>
                    <strong>{{ number_format($lastMileActive) }}</strong>
                </div>
                <div class="rx-today rx-today--earnings">
                    <div class="rx-today-top">
                        <span class="rx-today-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M16 12h5"/></svg></span>
                        <span class="rx-today-label">Earned today</span>
                    </div>
                    <strong>₱{{ number_format($earningsToday, 2) }}</strong>
                </div>
            </div>
        </article>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<script>
(() => {
    const root = document.getElementById('riderDashboard');
    if (!root) return;

    // A simple client-side filter for the current rider's rendered list.
    const input = root.querySelector('[data-job-search]');
    const jobItems = [...root.querySelectorAll('[data-job-item]')];
    const emptySearch = root.querySelector('[data-search-empty]');
    const filterJobs = () => {
        const search = (input?.value ?? '').trim().toLocaleLowerCase();
        let visible = 0;
        jobItems.forEach(item => {
            const match = item.textContent.toLocaleLowerCase().includes(search);
            item.hidden = !match;
            if (match) visible++;
        });
        if (emptySearch) emptySearch.hidden = !search || visible !== 0;
    };
    input?.addEventListener('input', filterJobs);



    // Saved, rider-authorized route points come from Laravel. OSRM returns a road-based
    // distance/duration; it does NOT include live traffic, parcel handling, or wait times.
    // Config override for production: config('services.sari_routing_url').
    // The public OSRM demo fallback is for limited local/preview testing ONLY.
    const recordedStops = @json($mapStops->values()->all());
    const routingBase = @json((string) (config('services.sari_routing_url') ?: 'https://router.project-osrm.org'));
    const mapElement = root.querySelector('#sariRiderLeafletMap');
    const mapNote = root.querySelector('[data-map-note]');
    const routeDistanceElement = root.querySelector('[data-map-route-distance]');
    const routeDurationElement = root.querySelector('[data-map-route-duration]');
    const gpsDistanceElement = root.querySelector('[data-map-gps-distance]');
    const gpsDurationElement = root.querySelector('[data-map-gps-duration]');
    const routeStatusElement = root.querySelector('[data-map-route-status]');
    const refreshRouteButton = root.querySelector('[data-map-route-refresh]');
    const mapFitButton = root.querySelector('[data-map-fit]');
    const mapGpsButton = root.querySelector('[data-map-my-location]');
    const MAX_WAYPOINTS_PER_REQUEST = 12;
    const MAX_ROUTABLE_STOPS = 67; // Max six sequential segments on the demo (no unbounded requests).
    let map = null;
    let ownLocationMarker = null;
    let assignedRoadLine = null;
    let gpsRoadLine = null;
    let lastDevicePoint = null;
    let activeRoutingRequest = null;
    let gpsRoutingRequest = null;

    const validCoord = s => s && Number.isFinite(Number(s.lat)) && Number.isFinite(Number(s.lng)) &&
        Math.abs(Number(s.lat)) <= 90 && Math.abs(Number(s.lng)) <= 180 &&
        (Number(s.lat) !== 0 || Number(s.lng) !== 0);
    const validStops = Array.isArray(recordedStops) ? recordedStops.filter(validCoord) : [];
    const routeStops = validStops.slice(0, MAX_ROUTABLE_STOPS);
    const formatDistance = meters => {
        const km = meters / 1000;
        return `${km < 10 ? km.toFixed(1) : km.toFixed(0)} km`;
    };
    const formatDuration = seconds => {
        if (seconds < 60) return '<1 min';
        const mins = Math.ceil(seconds / 60);
        if (mins < 60) return `≈ ${mins} min`;
        const hours = Math.floor(mins / 60);
        const remaining = mins % 60;
        return `≈ ${hours} hr${remaining ? ` ${remaining} min` : ''}`;
    };
    const setRouteStatus = message => { if (routeStatusElement) routeStatusElement.textContent = message; };
    const setMapNote = message => { if (mapNote) mapNote.textContent = message; };
    const ROUTING_TIMEOUT_MS = 15000;
    const routingURL = stops => {
        const coordinates = stops.map(s => `${Number(s.lng).toFixed(6)},${Number(s.lat).toFixed(6)}`).join(';');
        return `${routingBase.replace(/\/+$/, '')}/route/v1/driving/${coordinates}?overview=full&geometries=geojson&steps=false`;
    };
    async function requestRoadRoute(stops, controller) {
        // Browser makes a request to the configured routing server with the necessary
        // location coordinates. Never use an untrusted response for HTML rendering.
        const timeout = setTimeout(() => controller.abort(), ROUTING_TIMEOUT_MS);
        try {
            const response = await fetch(routingURL(stops), {
                method: 'GET', mode: 'cors', cache: 'no-store', signal: controller.signal,
                headers: { Accept: 'application/json' }
            });
            if (!response.ok) throw new Error(`Routing HTTP ${response.status}`);
            const data = await response.json();
            const route = data && data.code === 'Ok' && Array.isArray(data.routes) ? data.routes[0] : null;
            if (!route || !Number.isFinite(route.distance) || !Number.isFinite(route.duration) ||
                route.distance < 0 || route.duration < 0 ||
                !route.geometry || !Array.isArray(route.geometry.coordinates) || route.geometry.coordinates.length < 2) {
                throw new Error('No driving route available for these saved coordinates.');
            }
            const polyline = route.geometry.coordinates.map(point => [Number(point[1]), Number(point[0])]);
            if (polyline.some(point => !validCoord({lat:point[0],lng:point[1]}))) {
                throw new Error('Routing provider returned invalid path coordinates.');
            }
            return { distance: route.distance, duration: route.duration, polyline };
        } finally { clearTimeout(timeout); }
    }
    // Route every saved parcel destination in stable assignment order.
    // Split long multi-parcel routes into overlapping legs to avoid a
    // single huge OSRM request; adjacent legs share the connecting stop.
    // Do not silently claim full-batch ETA if the configured safe cap is hit.
    async function routeAcrossAllStops(points, controller) {
        let meters = 0, seconds = 0;
        const combinedPolyline = [];
        for (let start = 0; start < points.length - 1; start += MAX_WAYPOINTS_PER_REQUEST - 1) {
            if (controller.signal.aborted) throw new DOMException('Cancelled', 'AbortError');
            const segment = points.slice(start, start + MAX_WAYPOINTS_PER_REQUEST);
            // Respect the OSRM public demo's <=1 req/second guidance. A private
            // provider may remove the delay; this never auto-polls in background.
            if (start > 0 && routingBase.includes('router.project-osrm.org')) {
                await new Promise(resolve => setTimeout(resolve, 1150));
            }
            if (controller.signal.aborted) throw new DOMException('Cancelled', 'AbortError');
            const leg = await requestRoadRoute(segment, controller);
            meters += leg.distance;
            seconds += leg.duration;
            combinedPolyline.push(...(start ? leg.polyline.slice(1) : leg.polyline));
        }
        return {distance: meters, duration: seconds, polyline: combinedPolyline};
    }
    const updateRouteButton = () => {
        if (refreshRouteButton) refreshRouteButton.disabled = routeStops.length < 2 || !!activeRoutingRequest;
    };
    async function loadAssignedRoute() {
        if (!map) return;
        if (routeStops.length < 2) {
            if (routeDistanceElement) routeDistanceElement.textContent = '— km';
            if (routeDurationElement) routeDurationElement.textContent = routeStops.length === 1 ? 'Need another saved location' : 'No saved locations';
            setRouteStatus('Road distance and ETA require at least two valid saved route stops.');
            updateRouteButton();
            return;
        }
        if (activeRoutingRequest) activeRoutingRequest.abort();
        const controller = new AbortController();
        activeRoutingRequest = controller;
        updateRouteButton();
        setRouteStatus('Calculating driving route…');
        try {
            const result = await routeAcrossAllStops(routeStops, controller);
            if (activeRoutingRequest !== controller || !root.isConnected) return;
            if (assignedRoadLine) map.removeLayer(assignedRoadLine);
            assignedRoadLine = L.polyline(result.polyline, { color: '#c88912', weight: 5, opacity: .88, lineJoin: 'round' }).addTo(map);
            if (routeDistanceElement) routeDistanceElement.textContent = formatDistance(result.distance);
            if (routeDurationElement) routeDurationElement.textContent = `${formatDuration(result.duration)} driving`;
            const limited = validStops.length > routeStops.length;
            setRouteStatus(`${limited ? `Partial route: first ${routeStops.length} of ${validStops.length} saved stops (not full batch)` : `${routeStops.length} saved stops · full routable batch`} · Driving estimate excludes traffic and handoff time.`);
            setMapNote('Road route calculated from saved stops. Actual driving time may vary with traffic.');
            fitSavedStops();
        } catch (error) {
            if (activeRoutingRequest !== controller || error?.name === 'AbortError') return;
            if (assignedRoadLine) { map.removeLayer(assignedRoadLine); assignedRoadLine = null; }
            if (routeDistanceElement) routeDistanceElement.textContent = '— km';
            if (routeDurationElement) routeDurationElement.textContent = 'ETA unavailable';
            setRouteStatus('Driving route unavailable. Check routing service, connectivity, or saved coordinates.');
            setMapNote('Saved delivery pins remain visible. No straight-line distance was substituted for a road route.');
        } finally {
            if (activeRoutingRequest === controller) activeRoutingRequest = null;
            updateRouteButton();
        }
    }
    async function loadGpsToFirstStop(here) {
        if (!map || routeStops.length < 1) {
            if (gpsDurationElement) gpsDurationElement.textContent = 'No saved destination';
            return;
        }
        if (gpsRoutingRequest) gpsRoutingRequest.abort();
        const controller = new AbortController();
        gpsRoutingRequest = controller;
        if (gpsDurationElement) gpsDurationElement.textContent = 'Calculating driving ETA…';
        if (gpsDistanceElement) gpsDistanceElement.textContent = '— km';
        try {
            const result = await requestRoadRoute([{lat:here[0],lng:here[1]}, routeStops[0]], controller);
            if (gpsRoutingRequest !== controller || !root.isConnected) return;
            if (gpsRoadLine) map.removeLayer(gpsRoadLine);
            gpsRoadLine = L.polyline(result.polyline, { color: '#168a65', weight: 4, opacity: .9, dashArray: '9 7' }).addTo(map);
            if (gpsDistanceElement) gpsDistanceElement.textContent = formatDistance(result.distance);
            if (gpsDurationElement) gpsDurationElement.textContent = `${formatDuration(result.duration)} driving · to first saved stop`;
            setMapNote('Green dashed route: your last GPS reading → first saved stop. Gold route: assigned stops.');
        } catch (error) {
            if (gpsRoutingRequest !== controller || error?.name === 'AbortError') return;
            if (gpsRoadLine) { map.removeLayer(gpsRoadLine); gpsRoadLine = null; }
            if (gpsDistanceElement) gpsDistanceElement.textContent = '— km';
            if (gpsDurationElement) gpsDurationElement.textContent = 'GPS driving ETA unavailable';
            setMapNote('Device location found, but road routing could not be calculated.');
        } finally {
            if (gpsRoutingRequest === controller) gpsRoutingRequest = null;
        }
    }

    if (mapElement && typeof L !== 'undefined') {
        map = L.map(mapElement, { zoomControl: true, scrollWheelZoom: false, preferCanvas: true });
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a> contributors',
        }).addTo(map);
        const allPoints = [];
        validStops.forEach(stop => {
            const latlng = [Number(stop.lat), Number(stop.lng)];
            const isPickup = stop.type === 'pickup';
            const pin = L.divIcon({
                className: '', html: `<span class="rx-pin ${isPickup ? 'rx-pin--pickup' : ''}"></span>`,
                iconSize: [26, 26], iconAnchor: [13, 13]
            });
            const popup = document.createElement('div');
            const label = document.createElement('strong');
            const name = document.createElement('div');
            label.textContent = isPickup ? 'Pickup / hub' : 'Delivery stop';
            name.textContent = String(stop.name ?? 'Saved location');
            popup.append(label, name);
            L.marker(latlng, { icon: pin }).bindPopup(popup).addTo(map);
            allPoints.push(latlng);
        });
        function fitSavedStops() {
            if (assignedRoadLine) {
                map.fitBounds(assignedRoadLine.getBounds(), { padding: [32, 32], maxZoom: 15 });
            } else if (allPoints.length > 1) {
                map.fitBounds(L.latLngBounds(allPoints), { padding: [42, 42], maxZoom: 14 });
            } else if (allPoints.length === 1) {
                map.setView(allPoints[0], 13);
            } else {
                map.setView([12.8797, 121.7740], 6); // General Philippines map, not the rider position.
            }
        }
        fitSavedStops();
        setMapNote(allPoints.length === 0
            ? 'OpenStreetMap loaded. No saved coordinates for the current assignment.'
            : `${allPoints.length} saved location ${allPoints.length === 1 ? 'pin' : 'pins'} available.`);
        updateRouteButton();
        // One request on load, one only on manual refresh: no repeated polling.
        void loadAssignedRoute();
        mapFitButton?.addEventListener('click', fitSavedStops);
        refreshRouteButton?.addEventListener('click', () => { void loadAssignedRoute(); });
        mapGpsButton?.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setMapNote('This browser does not support device location.');
                return;
            }
            setMapNote('Waiting for device location permission…');
            navigator.geolocation.getCurrentPosition(position => {
                const here = [position.coords.latitude, position.coords.longitude];
                if (!validCoord({lat:here[0],lng:here[1]})) {
                    setMapNote('Device returned invalid location coordinates.');
                    return;
                }
                lastDevicePoint = here;
                if (ownLocationMarker) map.removeLayer(ownLocationMarker);
                ownLocationMarker = L.circleMarker(here, { radius: 8, color: '#fff', weight: 3, fillColor: '#289a76', fillOpacity: 1 }).addTo(map);
                map.setView(here, 15);
                setMapNote('Showing the device location. Calculating your road route to the first saved stop…');
                void loadGpsToFirstStop(here);
            }, () => {
                setMapNote('Location permission was unavailable or declined. Saved delivery pins remain visible.');
            }, { enableHighAccuracy: false, timeout: 12000, maximumAge: 60000 });
        });
        // Sidebar collapse changes map width; recalculate Leaflet tiles after resize.
        const resizeObserver = typeof ResizeObserver !== 'undefined'
            ? new ResizeObserver(() => map.invalidateSize({ pan: false, animate: false })) : null;
        resizeObserver?.observe(mapElement);
        window.addEventListener('pagehide', () => resizeObserver?.disconnect());
        window.addEventListener('pageshow', () => { resizeObserver?.observe(mapElement); map.invalidateSize({ pan: false, animate: false }); });
        window.addEventListener('pagehide', () => {
            activeRoutingRequest?.abort();
            gpsRoutingRequest?.abort();
        }, { once: true });
    } else {
        setMapNote('OpenStreetMap could not load. Check internet access or network restrictions.');
        setRouteStatus('Routing unavailable because the map library did not load.');
    }

    // Preserve the Asia/Manila clock convention from the existing dashboard.
    if (typeof window.__sariRiderClockCleanup === 'function') window.__sariRiderClockCleanup();
    const greeting = root.querySelector('[data-rider-greeting]');
    const clock = root.querySelector('[data-rider-clock]');
    const day = root.querySelector('[data-rider-day]');
    const riderName = @json($riderName);
    const timeZone = 'Asia/Manila';
    const hours = new Intl.DateTimeFormat('en-GB', { timeZone, hour: '2-digit', hourCycle: 'h23' });
    const timeFormat = new Intl.DateTimeFormat('en-PH', { timeZone, hour: '2-digit', minute: '2-digit', hour12: true });
    const dayFormat = new Intl.DateTimeFormat('en-PH', { timeZone, weekday: 'short', month: 'short', day: 'numeric' });
    let timer = null;
    const stopClock = () => { if (timer !== null) { clearTimeout(timer); timer = null; } };
    const renderClock = () => {
        const now = new Date();
        const hour = Number(hours.formatToParts(now).find(part => part.type === 'hour')?.value ?? 0);
        const opening = hour >= 5 && hour < 12 ? 'Good morning' : (hour >= 12 && hour < 18 ? 'Good afternoon' : 'Good evening');
        if (greeting) greeting.textContent = `${opening}, ${riderName}.`;
        if (clock) clock.textContent = timeFormat.format(now);
        if (day) day.textContent = dayFormat.format(now);
    };
    const scheduleClock = () => {
        stopClock();
        if (!root.isConnected) return cleanup();
        renderClock();
        if (!document.hidden) timer = setTimeout(scheduleClock, 60000 - (Date.now() % 60000) + 25);
    };
    const handleVisibility = () => { if (document.hidden) stopClock(); else scheduleClock(); };
    function cleanup() {
        stopClock();
        document.removeEventListener('visibilitychange', handleVisibility);
        window.removeEventListener('pagehide', stopClock);
        window.removeEventListener('pageshow', scheduleClock);
        input?.removeEventListener('input', filterJobs);
        if (window.__sariRiderClockCleanup === cleanup) delete window.__sariRiderClockCleanup;
    }
    document.addEventListener('visibilitychange', handleVisibility);
    window.addEventListener('pagehide', stopClock);
    window.addEventListener('pageshow', scheduleClock);
    window.__sariRiderClockCleanup = cleanup;
    scheduleClock();
})();
</script>
@endpush
