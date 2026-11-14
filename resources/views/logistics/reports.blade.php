@extends('layouts.logistics')

@section('title', 'Reports | SARI Logistics')
@section('page-title', 'Reports')

@push('styles')
<style>
#lgReports {
    --lr-bg: var(--sari-dark-4d21eb669b, #f4f6f8);
    --lr-card: var(--sari-dark-950baec030, #ffffff);
    --lr-line: var(--sari-dark-d8c2716225, #e5ebf2);
    --lr-line-strong: var(--sari-dark-d8c2716225, #d8e0e9);
    --lr-text: var(--sari-dark-e23e64dd1e, #223044);
    --lr-text-soft: var(--sari-dark-a63aa16d7c, #59697c);
    --lr-muted: var(--sari-dark-a63aa16d7c, #8894a3);
    --lr-gold: #d9950b;
    --lr-gold-soft: rgba(217, 149, 11, .10);
    --lr-blue: #4f83b5;
    --lr-green: #4f9c73;
    --lr-purple: #8a79b2;
    --lr-red: #c87575;
    --lr-shadow: 0 12px 30px var(--sari-dark-7c935c8886, rgba(26, 39, 53, .07));
    width: 100%;
    max-width: 1640px;
    margin: 0 auto;
    color: var(--lr-text);
    font-family: inherit;
}
#lgReports,
#lgReports * { box-sizing: border-box; }
#lgReports [hidden] { display: none !important; }
#lgReports .sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}
#lgReports .lr-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
    margin: 2px 0 20px;
}
#lgReports .lr-kicker {
    margin: 0 0 4px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .14em;
    color: var(--sari-dark-c8bdcce6ab, #b17811);
    text-transform: uppercase;
}
#lgReports .lr-title {
    margin: 0;
    font-size: clamp(28px, 2.9vw, 40px);
    line-height: 1.08;
    font-weight: 800;
    letter-spacing: -.05em;
    color: var(--sari-dark-e23e64dd1e, #203047);
}
#lgReports .lr-title em { font-style: normal; color: var(--lr-gold); }
#lgReports .lr-header-sub {
    margin: 7px 0 0;
    color: var(--lr-muted);
    font-size: 13px;
    font-weight: 500;
}
#lgReports .lr-period {
    min-width: 240px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
    padding: 16px 18px;
    border: 1px solid var(--lr-line);
    border-radius: 16px;
    background: var(--sari-dark-a1345396a6, rgba(255, 255, 255, .78));
    box-shadow: var(--lr-shadow);
}
#lgReports .lr-period-label {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--sari-dark-a63aa16d7c, #a27d31);
}
#lgReports .lr-period strong {
    font-size: 15px;
    color: var(--sari-dark-e23e64dd1e, #24354a);
    letter-spacing: -.02em;
}
#lgReports .lr-period span {
    font-size: 11px;
    color: var(--lr-muted);
}
#lgReports .lr-card {
    min-width: 0;
    background: var(--lr-card);
    border: 1px solid var(--lr-line);
    border-radius: 20px;
    box-shadow: var(--lr-shadow);
}
#lgReports .lr-filter-card {
    padding: 18px;
    margin-bottom: 18px;
}
#lgReports .lr-filter-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 14px;
}
#lgReports .lr-filter-head h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 760;
    color: var(--sari-dark-e23e64dd1e, #24354a);
    letter-spacing: -.025em;
}
#lgReports .lr-filter-head p {
    margin: 4px 0 0;
    font-size: 12px;
    color: var(--lr-muted);
}
#lgReports .lr-filter-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto auto;
    gap: 12px;
    align-items: end;
}
#lgReports .lr-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
}
#lgReports .lr-field span {
    font-size: 11px;
    font-weight: 700;
    color: var(--sari-dark-a63aa16d7c, #728196);
}
#lgReports .lr-input {
    width: 100%;
    height: 46px;
    padding: 0 14px;
    border: 1px solid var(--lr-line-strong);
    border-radius: 12px;
    background: var(--sari-dark-950baec030, #fff);
    color: var(--sari-dark-e23e64dd1e, #334359);
    font-size: 12px;
    outline: 0;
    transition: border-color .16s ease, box-shadow .16s ease;
}
#lgReports .lr-input:focus,
#lgReports .lr-search-input:focus {
    border-color: var(--sari-dark-d8c2716225, #aebbc9);
    box-shadow: 0 0 0 3px var(--sari-dark-3b2338cbf0, rgba(123, 143, 166, .12));
}
#lgReports :is(button, a, input, select):focus-visible {
    outline: 2px solid var(--sari-dark-d8c2716225, #8ca0b7);
    outline-offset: 2px;
}
#lgReports .lr-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 46px;
    padding: 0 18px;
    border-radius: 12px;
    border: 1px solid var(--lr-line-strong);
    text-decoration: none;
    font-size: 12px;
    font-weight: 760;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color .16s ease, border-color .16s ease, color .16s ease;
}
#lgReports .lr-btn-gold {
    background: var(--lr-gold);
    border-color: var(--lr-gold);
    color: #fff;
}
#lgReports .lr-btn-gold:hover { background: #be8208; border-color: #be8208; }
#lgReports .lr-btn-soft {
    background: var(--sari-dark-950baec030, #fff);
    color: var(--sari-dark-a63aa16d7c, #516174);
}
#lgReports .lr-btn-soft:hover {
    background: var(--sari-dark-950baec030, #f6f8fa);
    color: var(--sari-dark-e23e64dd1e, #304154);
}
#lgReports .lr-kpis {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 14px;
}
#lgReports .lr-kpi {
    position: relative;
    display: flex;
    justify-content: space-between;
    gap: 12px;
    min-width: 0;
    padding: 18px 18px 17px;
    border-radius: 20px;
    background: var(--sari-dark-950baec030, #fff);
    border: 1px solid var(--lr-line);
    box-shadow: var(--lr-shadow);
}
#lgReports .lr-kpi::after {
    content: '';
    position: absolute;
    left: 18px;
    right: 18px;
    bottom: 0;
    height: 3px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(217,149,11,.9), rgba(217,149,11,0));
    opacity: .18;
}
#lgReports .lr-kpi-label {
    margin: 0;
    font-size: 12px;
    font-weight: 700;
    color: var(--sari-dark-a63aa16d7c, #66778c);
}
#lgReports .lr-kpi strong {
    display: block;
    margin: 10px 0 4px;
    font-size: 30px;
    line-height: 1.05;
    letter-spacing: -.05em;
    color: var(--sari-dark-e23e64dd1e, #1f2d40);
    overflow-wrap: anywhere;
}
#lgReports .lr-kpi-note {
    font-size: 11px;
    color: var(--sari-dark-a63aa16d7c, #96a2b0);
}
#lgReports .lr-kpi-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    flex: 0 0 42px;
}
#lgReports .lr-kpi-icon svg {
    width: 22px;
    height: 22px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}
#lgReports .lr-kpi-icon.lr-blue { color: var(--lr-blue); background: rgba(79,131,181,.12); }
#lgReports .lr-kpi-icon.lr-green { color: var(--lr-green); background: rgba(79,156,115,.13); }
#lgReports .lr-kpi-icon.lr-amber { color: var(--sari-dark-93ff5b7313, #bd8421); background: rgba(217,149,11,.12); }
#lgReports .lr-kpi-icon.lr-purple { color: var(--lr-purple); background: rgba(138,121,178,.12); }
#lgReports .lr-secondary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 18px;
}
#lgReports .lr-secondary-card {
    padding: 14px 16px;
    border-radius: 16px;
    border: 1px solid var(--lr-line);
    background: var(--sari-dark-9b2add9551, rgba(255,255,255,.92));
    box-shadow: var(--lr-shadow);
}
#lgReports .lr-secondary-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: var(--sari-dark-a63aa16d7c, #738295);
}
#lgReports .lr-secondary-card strong {
    display: block;
    margin-top: 8px;
    font-size: 22px;
    line-height: 1.05;
    letter-spacing: -.04em;
    color: var(--sari-dark-e23e64dd1e, #203146);
}
#lgReports .lr-insights-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.28fr) minmax(0, .92fr);
    gap: 16px;
    margin-bottom: 16px;
}
#lgReports .lr-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    flex-wrap: wrap;
    padding: 18px 20px;
    border-bottom: 1px solid var(--sari-dark-d8c2716225, #edf1f5);
}
#lgReports .lr-card-head h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 760;
    color: var(--sari-dark-e23e64dd1e, #24354a);
    letter-spacing: -.03em;
}
#lgReports .lr-card-head p {
    margin: 5px 0 0;
    color: var(--lr-muted);
    font-size: 12px;
    line-height: 1.55;
}
#lgReports .lr-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 10px;
    border-radius: 10px;
    border: 1px solid var(--lr-line);
    background: var(--sari-dark-950baec030, #f8fafb);
    font-size: 10px;
    font-weight: 800;
    color: var(--sari-dark-a63aa16d7c, #7b8797);
    letter-spacing: .07em;
    text-transform: uppercase;
}
#lgReports .lr-chart-tabs {
    display: inline-flex;
    gap: 8px;
    padding: 4px;
    border-radius: 999px;
    background: var(--sari-dark-950baec030, #f5f7fa);
    border: 1px solid var(--sari-dark-d8c2716225, #e7edf3);
}
#lgReports .lr-chart-tabs button {
    border: 0;
    background: transparent;
    color: var(--sari-dark-a63aa16d7c, #7f8c9c);
    font-size: 11px;
    font-weight: 700;
    padding: 8px 12px;
    border-radius: 999px;
    cursor: pointer;
    transition: background-color .16s ease, color .16s ease;
}
#lgReports .lr-chart-tabs button[aria-pressed="true"] {
    background: var(--sari-dark-950baec030, #fff);
    color: var(--sari-dark-c8bdcce6ab, #b17811);
    box-shadow: 0 1px 4px var(--sari-dark-c33e3d1bb4, rgba(24, 38, 55, .08));
}
#lgReports .lr-chart-wrap { padding: 20px 20px 18px; }
#lgReports .lr-chart-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 14px;
}
#lgReports .lr-chart-summary div {
    padding: 12px 13px;
    border: 1px solid var(--sari-dark-d8c2716225, #edf1f4);
    border-radius: 14px;
    background: var(--sari-dark-950baec030, #fafbfc);
}
#lgReports .lr-chart-summary span {
    display: block;
    font-size: 10px;
    font-weight: 700;
    color: var(--sari-dark-a63aa16d7c, #8894a3);
    text-transform: uppercase;
    letter-spacing: .06em;
}
#lgReports .lr-chart-summary strong {
    display: block;
    margin-top: 6px;
    font-size: 18px;
    color: var(--sari-dark-e23e64dd1e, #223044);
    letter-spacing: -.03em;
}
#lgReports .lr-plot-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 12px;
    align-items: end;
    min-height: 230px;
    padding: 6px 2px 12px;
    border-bottom: 1px solid var(--sari-dark-d8c2716225, #e8edf2);
    background: repeating-linear-gradient(to top, transparent 0, transparent calc(25% - 1px), var(--sari-dark-4d21eb669b, #eef2f6) calc(25% - 1px), var(--sari-dark-4d21eb669b, #eef2f6) 25%);
}
#lgReports .lr-day {
    height: 226px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}
#lgReports .lr-day-value {
    font-size: 10px;
    color: var(--sari-dark-a63aa16d7c, #506073);
    font-weight: 750;
    font-variant-numeric: tabular-nums;
}
#lgReports .lr-bar-holder {
    width: 100%;
    height: 176px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
#lgReports .lr-vertical-bar {
    display: block;
    width: clamp(16px, 48%, 40px);
    min-height: 6px;
    border-radius: 10px 10px 4px 4px;
    background: linear-gradient(180deg, var(--sari-dark-4d21eb669b, #d7e1eb), var(--sari-dark-4d21eb669b, #c3cedb));
    transition: height .18s ease, background .18s ease, transform .18s ease;
}
#lgReports .lr-vertical-bar.is-positive {
    background: linear-gradient(180deg, #efb84c, #d9950b);
}
#lgReports .lr-day:hover .lr-vertical-bar { transform: translateY(-2px); }
#lgReports .lr-day-label {
    text-align: center;
    color: var(--sari-dark-a63aa16d7c, #768496);
    font-size: 10px;
    font-weight: 700;
}
#lgReports .lr-day-label small {
    display: block;
    margin-top: 2px;
    color: var(--sari-dark-a63aa16d7c, #a2acb9);
    font-size: 9px;
    font-weight: 500;
}
#lgReports .lr-chart-footer {
    padding-top: 13px;
    color: var(--lr-muted);
    font-size: 10px;
}
#lgReports .lr-pipeline {
    padding: 18px 20px 20px;
}
#lgReports .lr-pipeline-grid {
    display: grid;
    gap: 12px;
}
#lgReports .lr-pipeline-item {
    padding: 14px 14px 13px;
    border: 1px solid var(--sari-dark-d8c2716225, #edf1f5);
    border-radius: 16px;
    background: var(--sari-dark-950baec030, #fbfcfd);
}
#lgReports .lr-pipeline-top {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: baseline;
    margin-bottom: 8px;
}
#lgReports .lr-pipeline-top span {
    font-size: 12px;
    font-weight: 700;
    color: var(--sari-dark-a63aa16d7c, #5f6f83);
}
#lgReports .lr-pipeline-top strong {
    font-size: 18px;
    font-weight: 800;
    color: var(--sari-dark-e23e64dd1e, #24354a);
}
#lgReports .lr-progress {
    height: 9px;
    border-radius: 999px;
    background: var(--sari-dark-4d21eb669b, #edf1f5);
    overflow: hidden;
}
#lgReports .lr-progress > span {
    display: block;
    height: 100%;
    border-radius: inherit;
    background: #a9b6c6;
}
#lgReports .lr-progress > span.is-highlight {
    background: linear-gradient(90deg, #efb84c, #d9950b);
}
#lgReports .lr-pipeline-note {
    margin: 8px 0 0;
    font-size: 10px;
    color: var(--sari-dark-a63aa16d7c, #98a3b0);
}
#lgReports .lr-section-spacer { margin-bottom: 16px; }
#lgReports .lr-riders-list {
    display: grid;
    gap: 12px;
    padding: 18px 20px 20px;
}
#lgReports .lr-rider-row {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    gap: 12px;
    align-items: center;
    padding: 13px 14px;
    border: 1px solid var(--sari-dark-d8c2716225, #edf1f5);
    border-radius: 16px;
    background: var(--sari-dark-950baec030, #fbfcfd);
}
#lgReports .lr-rank {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: var(--lr-gold-soft);
    color: var(--sari-dark-c8bdcce6ab, #b17811);
    font-size: 12px;
    font-weight: 800;
}
#lgReports .lr-rider-name {
    font-size: 13px;
    font-weight: 760;
    color: var(--sari-dark-e23e64dd1e, #314256);
}
#lgReports .lr-rider-detail {
    margin: 4px 0 0;
    font-size: 10.5px;
    color: var(--sari-dark-a63aa16d7c, #95a0ad);
}
#lgReports .lr-rider-total {
    text-align: right;
}
#lgReports .lr-rider-total strong {
    display: block;
    font-size: 14px;
    color: var(--sari-dark-e23e64dd1e, #24354a);
}
#lgReports .lr-rider-total span {
    display: block;
    margin-top: 4px;
    font-size: 10px;
    color: var(--sari-dark-a63aa16d7c, #96a1ae);
}
#lgReports .lr-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 34px 20px;
    text-align: center;
}
#lgReports .lr-empty svg {
    width: 46px;
    height: 46px;
    color: var(--sari-dark-a63aa16d7c, #b2bcc8);
    fill: none;
    stroke: currentColor;
    stroke-width: 1.6;
    stroke-linecap: round;
    stroke-linejoin: round;
}
#lgReports .lr-empty strong {
    font-size: 18px;
    color: var(--sari-dark-a63aa16d7c, #92a0af);
    letter-spacing: -.02em;
}
#lgReports .lr-empty p {
    margin: 0;
    font-size: 12px;
    color: var(--sari-dark-a63aa16d7c, #9ba7b4);
}
#lgReports .lr-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}
#lgReports .lr-search-field {
    position: relative;
    width: min(420px, 100%);
    min-width: 0;
}
#lgReports .lr-search-field svg {
    position: absolute;
    left: 14px;
    top: 50%;
    width: 16px;
    height: 16px;
    transform: translateY(-50%);
    fill: none;
    stroke: var(--sari-dark-a63aa16d7c, #97a4b3);
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
    pointer-events: none;
}
#lgReports .lr-search-input {
    width: 100%;
    height: 46px;
    padding: 0 14px 0 42px;
    border: 1px solid var(--lr-line-strong);
    border-radius: 12px;
    background: var(--sari-dark-950baec030, #fff);
    color: var(--sari-dark-e23e64dd1e, #344358);
    font-size: 12px;
    outline: 0;
}
#lgReports .lr-results-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: 0 12px;
    border-radius: 999px;
    border: 1px solid var(--lr-line);
    background: var(--sari-dark-950baec030, #f8fafb);
    color: var(--sari-dark-a63aa16d7c, #6e7d90);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .05em;
    text-transform: uppercase;
}
#lgReports .lr-table-card .lr-card-head { padding-bottom: 16px; }
#lgReports .lr-table-scroll { width: 100%; overflow-x: auto; }
#lgReports .lr-delivery-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
}
#lgReports .lr-delivery-table th {
    height: 44px;
    padding: 0 18px;
    background: var(--sari-dark-950baec030, #f7f9fb);
    color: var(--sari-dark-a63aa16d7c, #8a96a5);
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .065em;
    text-transform: uppercase;
    white-space: nowrap;
}
#lgReports .lr-delivery-table th.lr-money,
#lgReports .lr-delivery-table td.lr-fee { text-align: right; }
#lgReports .lr-delivery-table td {
    padding: 14px 18px;
    border-top: 1px solid var(--sari-dark-d8c2716225, #edf1f5);
    color: var(--sari-dark-a63aa16d7c, #667589);
    font-size: 11.5px;
    line-height: 1.45;
    vertical-align: middle;
    overflow-wrap: anywhere;
}
#lgReports .lr-delivery-table tbody tr:hover { background: var(--sari-dark-950baec030, #f7f9fb); }
#lgReports .lr-delivery-table td strong {
    display: block;
    color: var(--sari-dark-e23e64dd1e, #2a3a4d);
    font-size: 12px;
    font-weight: 760;
}
#lgReports .lr-cell-detail {
    display: block;
    margin-top: 4px;
    font-size: 10.5px;
    color: var(--sari-dark-a63aa16d7c, #96a1af);
}
#lgReports .lr-date-primary {
    display: block;
    color: var(--sari-dark-a63aa16d7c, #506073);
    font-weight: 650;
}
#lgReports .lr-table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding: 12px 20px;
    border-top: 1px solid var(--sari-dark-d8c2716225, #e9edf2);
    color: var(--sari-dark-a63aa16d7c, #8894a3);
    font-size: 11px;
}
#lgReports .lr-perpage {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}
#lgReports .lr-perpage select {
    height: 34px;
    padding: 0 24px 0 10px;
    border: 1px solid var(--lr-line-strong);
    border-radius: 9px;
    background: var(--sari-dark-950baec030, #fff);
    color: var(--sari-dark-a63aa16d7c, #4e5f73);
    font-size: 11px;
}
#lgReports .lr-pages {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 6px;
    padding: 14px 0 0;
}
#lgReports .lr-pages button {
    min-width: 34px;
    min-height: 34px;
    padding: 0 10px;
    border: 1px solid var(--sari-dark-d8c2716225, #dfe6ed);
    border-radius: 10px;
    background: var(--sari-dark-950baec030, #fff);
    color: var(--sari-dark-a63aa16d7c, #5f7084);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}
#lgReports .lr-pages button.is-active {
    background: var(--lr-gold);
    border-color: var(--lr-gold);
    color: #fff;
}
#lgReports .lr-pages button:disabled {
    opacity: .45;
    cursor: default;
}
#lgReports .lr-empty-search { padding: 26px 18px 28px; }
@media (max-width: 1200px) {
    #lgReports .lr-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    #lgReports .lr-insights-grid { grid-template-columns: 1fr; }
}
@media (max-width: 960px) {
    #lgReports .lr-filter-grid { grid-template-columns: 1fr 1fr; }
    #lgReports .lr-kpi strong { font-size: 26px; }
}
@media (max-width: 760px) {
    #lgReports .lr-period { width: 100%; align-items: flex-start; }
    #lgReports .lr-secondary { grid-template-columns: 1fr; }
    #lgReports .lr-chart-summary { grid-template-columns: 1fr; }
    #lgReports .lr-rider-row { grid-template-columns: auto minmax(0,1fr); }
    #lgReports .lr-rider-total { grid-column: 2; text-align: left; }
    #lgReports .lr-toolbar { align-items: stretch; }
}
@media (max-width: 640px) {
    #lgReports .lr-filter-grid { grid-template-columns: 1fr; }
    #lgReports .lr-kpis { grid-template-columns: 1fr; }
    #lgReports .lr-card-head,
    #lgReports .lr-chart-wrap,
    #lgReports .lr-pipeline,
    #lgReports .lr-riders-list,
    #lgReports .lr-filter-card { padding-left: 15px; padding-right: 15px; }
    #lgReports .lr-plot-grid { gap: 8px; }
    #lgReports .lr-day { height: 212px; }
    #lgReports .lr-day-label { font-size: 9px; }
    #lgReports .lr-day-label small { font-size: 8px; }
    #lgReports .lr-table-scroll { overflow: visible; }
    #lgReports .lr-delivery-table thead { display: none; }
    #lgReports .lr-delivery-table,
    #lgReports .lr-delivery-table tbody { display: block; width: 100%; }
    #lgReports .lr-delivery-table tr {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 14px;
        padding: 15px;
        border-top: 1px solid var(--sari-dark-d8c2716225, #e9edf2);
    }
    #lgReports .lr-delivery-table td {
        display: block;
        padding: 0;
        border: 0;
    }
    #lgReports .lr-delivery-table td::before {
        content: attr(data-label);
        display: block;
        margin-bottom: 4px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .05em;
        color: var(--sari-dark-a63aa16d7c, #99a5b3);
        text-transform: uppercase;
    }
    #lgReports .lr-delivery-table td.lr-order-cell,
    #lgReports .lr-delivery-table td[colspan] { grid-column: 1 / -1; }
    #lgReports .lr-delivery-table td.lr-fee,
    #lgReports .lr-delivery-table td.lr-date-cell { text-align: left; }
    #lgReports .lr-table-footer { padding: 12px 15px; }
}

/* Consistent Logistics UI proportions; presentation only. */

#logisticsContent #lgReports {max-width:1520px;font-family:'Poppins',sans-serif;--lr-gold:#c88912;--lr-shadow:0 4px 8px rgba(25,42,65,.025),0 12px 32px rgba(25,42,65,.065);}
#logisticsContent #lgReports .lr-title {font-size:clamp(27px,2.3vw,31px);font-weight:760;letter-spacing:-.04em;}
#logisticsContent #lgReports .lr-kicker {font-size:10.5px;letter-spacing:.14em;}
#logisticsContent #lgReports .lr-header-sub {font-size:11.5px;line-height:1.75;max-width:650px;}
#logisticsContent #lgReports .lr-period {background:var(--lr-card);border-radius:14px;padding:12px 16px;}
#logisticsContent #lgReports .lr-period strong {font-size:12px;}
#logisticsContent #lgReports .lr-card {border-radius:18px;}
#logisticsContent #lgReports .lr-kpi {min-height:110px;padding:14px 16px;border-radius:18px;}
#logisticsContent #lgReports .lr-kpi::after {display:none;}
#logisticsContent #lgReports .lr-kpi-label {font-size:13px;font-weight:600;}
#logisticsContent #lgReports .lr-kpi strong {font-size:clamp(25px,1.9vw,30px);font-weight:650;}
#logisticsContent #lgReports .lr-kpi-icon {width:27px;height:27px;flex-basis:27px;background:transparent!important;border:0;border-radius:0;}
#logisticsContent #lgReports .lr-kpi-icon svg {width:27px;height:27px;}
#logisticsContent #lgReports .lr-input,#logisticsContent #lgReports .lr-btn,#logisticsContent #lgReports .lr-search-field {height:48px;min-height:48px;border-radius:14px;font-size:11.5px;}
#logisticsContent #lgReports .lr-search-input {font-size:11.5px;}
#logisticsContent #lgReports .lr-card-head {padding:19px 20px;}
#logisticsContent #lgReports .lr-card-head h2 {font-size:16px;font-weight:700;}
#logisticsContent #lgReports .lr-card-head p {font-size:11.5px;line-height:1.7;}
#logisticsContent #lgReports .lr-delivery-table th {font-size:11px;padding:15px 16px;}
#logisticsContent #lgReports .lr-delivery-table td {font-size:12px;padding:17px 16px;}
#logisticsContent #lgReports .lr-table-footer {min-height:64px;padding:13px 20px;font-size:11.5px;}
#logisticsContent #lgReports .lr-pages {justify-content:flex-end;gap:6px;}
#logisticsContent #lgReports .lr-pages button {min-width:34px;height:34px;border-radius:10px;font-size:11px;}
#logisticsContent #lgReports .lr-pages button.is-active {background:#c88912;}
#logisticsContent #lgReports .lr-chart-tabs {background:var(--sari-dark-4d21eb669b,#f5f7fa);}
@media(max-width:640px){#logisticsContent #lgReports .lr-title{font-size:25px;}#logisticsContent #lgReports .lr-kpis{grid-template-columns:1fr;}}


#logisticsContent #lgReports .lr-earnings-section {margin:0 0 20px;overflow:hidden;}
#logisticsContent #lgReports .lr-earnings-kpis {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:0 20px 16px;}
#logisticsContent #lgReports .lr-earnings-kpi {padding:16px;border:1px solid var(--lr-line);border-radius:14px;background:var(--lr-card);box-shadow:0 3px 12px rgba(25,42,65,.04);min-width:0;}
#logisticsContent #lgReports .lr-earnings-kpi p {margin:0;font-size:11.5px;font-weight:600;color:var(--lr-text-soft);}
#logisticsContent #lgReports .lr-earnings-kpi strong {display:block;margin:8px 0;font-size:clamp(21px,1.8vw,27px);font-weight:650;color:var(--lr-text);overflow-wrap:anywhere;}
#logisticsContent #lgReports .lr-earnings-kpi small {font-size:10.5px;color:var(--lr-muted);}
#logisticsContent #lgReports .lr-earnings-note {margin:0;padding:0 20px 14px;font-size:11px;line-height:1.75;color:var(--lr-muted);}
#logisticsContent #lgReports .lr-earnings-table {min-width:1060px;}
#logisticsContent #lgReports .lr-earnings-pagination {padding:14px 20px;border-top:1px solid var(--lr-line);font-size:11px;}
@media(max-width:900px){#logisticsContent #lgReports .lr-earnings-kpis{grid-template-columns:repeat(2,minmax(0,1fr));}}
@media(max-width:540px){#logisticsContent #lgReports .lr-earnings-kpis{grid-template-columns:1fr;padding-inline:16px;}}


#lgReports .lr-print-actions{display:flex;justify-content:flex-end;margin-bottom:16px;gap:10px;}
#lgReports .lr-report-meta{font-size:11px;color:var(--lr-muted);margin:0 0 18px;}
#lgReports .lr-finance-visuals{display:grid;grid-template-columns:1.2fr 1fr;gap:16px;padding:0 20px 20px;}
#lgReports .lr-finance-visuals article{min-width:0;padding:18px;border:1px solid var(--lr-line);border-radius:15px;background:var(--lr-card);}
#lgReports .lr-finance-visuals h3{margin:0 0 16px;font-size:13px;font-weight:650;}
#lgReports .lr-allocation-body{display:flex;align-items:center;gap:18px;}
#lgReports .lr-donut{width:160px;max-width:42%;flex-shrink:0;}
#lgReports .lr-donut-caption{font-size:8px;fill:var(--lr-muted);letter-spacing:1px;}
#lgReports .lr-donut-value{font-size:19px;font-weight:700;fill:var(--lr-text);}
#lgReports .lr-chart-legend{flex:1;min-width:0;}
#lgReports .lr-chart-legend>div{display:flex;gap:8px;align-items:center;margin:14px 0;font-size:10.5px;}
#lgReports .lr-chart-legend strong{margin-left:auto;font-size:10px;}
#lgReports .lr-chart-legend small{display:block;margin-top:4px;color:var(--lr-muted);}
#lgReports .lr-legend-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
#lgReports .lr-availability-chart>strong{font-size:32px;font-weight:650;}
#lgReports .lr-availability-chart p,#lgReports .lr-availability-chart small{font-size:11px;color:var(--lr-muted);line-height:1.7;}
#lgReports .lr-availability-track{height:10px;background:#e8edf3;border-radius:20px;overflow:hidden;margin:18px 0;}
#lgReports .lr-availability-track span{display:block;height:100%;background:#c88912;}
#lgReports .lr-daily-print{width:100%;border-collapse:collapse;font-size:11px;}
#lgReports .lr-daily-print th,#lgReports .lr-daily-print td{padding:8px;border-bottom:1px solid #ddd;text-align:left;}
@media(max-width:900px){#lgReports .lr-finance-visuals{grid-template-columns:1fr;}}
@page{size:A4 portrait;margin:15mm;}
@media print{
 html,body{background:#fff!important;color:#171717!important;height:auto!important;overflow:visible!important;}
 #logisticsSidebar,#logisticsHeader,#lgReports .lr-print-actions,#lgReports .lr-filter-card,#lgReports .lr-toolbar,#lgReports .lr-pages,#lgReports .lr-perpage,#lgReports .lr-table-footer,#lgReports .lr-earnings-pagination,#lgReports .lr-chart-tabs{display:none!important;}
 #logisticsContent{padding:0!important;margin:0!important;width:100%!important;max-width:none!important;}
 #lgReports{width:100%!important;max-width:none!important;font-family:Arial,Helvetica,sans-serif!important;}
 #lgReports,#lgReports *{box-shadow:none!important;text-shadow:none!important;}
 #lgReports .lr-header{display:flex!important;border-bottom:2px solid #222;padding-bottom:14px;}
 #lgReports .lr-title{font-size:25px!important;color:#111!important;}
 #lgReports .lr-header-sub{display:none;}
 #lgReports .lr-card,#lgReports .lr-kpi,#lgReports .lr-secondary-card,#lgReports .lr-period{border-radius:0!important;background:#fff!important;border-color:#ccc!important;}
 #lgReports .lr-insights-grid{display:block!important;}
 #lgReports .lr-riders-list,#lgReports .lr-pipeline,#lgReports .lr-chart-wrap{max-height:none!important;overflow:visible!important;}
 #lgReports .lr-insights-grid>article{margin-bottom:18px;}
 #lgReports .lr-finance-visuals{grid-template-columns:1fr 1fr!important;break-inside:avoid;}
 #lgReports .lr-earnings-kpis{grid-template-columns:repeat(3,minmax(0,1fr))!important;}
 #lgReports .lr-card-head{padding:12px!important;break-after:avoid;}
 #lgReports .lr-card-head h2{font-size:14px!important;}
 #lgReports .lr-chart-wrap{padding:12px!important;}
 #lgReports .lr-table-scroll{overflow:visible!important;max-height:none!important;}
 #lgReports table{display:table!important;width:100%!important;min-width:0!important;table-layout:fixed!important;border-collapse:collapse!important;}
 #lgReports thead{display:table-header-group!important;}
 #lgReports tbody{display:table-row-group!important;}
 #lgReports tr{display:table-row!important;break-inside:avoid;}
 #lgReports td,#lgReports th{display:table-cell!important;padding:8px 6px!important;font-size:8px!important;overflow-wrap:anywhere;}
 #lgReports td::before{display:none!important;}
 #lgReports tr[hidden]{display:table-row!important;}
 #lgReports .lr-cell-detail{font-size:8px!important;}
 #lgReports .lr-kpi,#lgReports .lr-secondary-card,#lgReports .lr-rider-row{break-inside:avoid;}
 #lgReports .lr-kpi strong{font-size:20px!important;}
 #lgReports .lr-earnings-kpi strong{font-size:17px!important;}
 #lgReports svg{print-color-adjust:exact;-webkit-print-color-adjust:exact;}
 #lgReports .lr-legend-dot,#lgReports .lr-availability-track,#lgReports .lr-availability-track span{print-color-adjust:exact;-webkit-print-color-adjust:exact;}
}


/* Compact report header and cohesive analytics cards. */
#logisticsContent #lgReports .lr-header {display:grid;grid-template-columns:minmax(0,1fr) auto;gap:14px 24px;align-items:start;margin-bottom:18px;}
#logisticsContent #lgReports .lr-header>div:first-child {grid-column:1;grid-row:1 / 3;}
#logisticsContent #lgReports .lr-header .lr-print-actions {grid-column:2;grid-row:1;margin:0;}
#logisticsContent #lgReports .lr-header .lr-period {grid-column:2;grid-row:2;min-width:0;padding:0;border:0;background:transparent;box-shadow:none;gap:3px;}
#logisticsContent #lgReports .lr-header-sub {max-width:590px;font-size:11.5px;line-height:1.7;}
#logisticsContent #lgReports .lr-print-actions .lr-btn {min-height:44px;height:44px;font-size:11.5px;border-radius:12px;padding:0 18px;}
#logisticsContent #lgReports .lr-filter-card {padding:18px 20px;}
#logisticsContent #lgReports .lr-kpi {padding:14px 16px;min-height:110px;}
#logisticsContent #lgReports .lr-card {box-shadow:0 4px 8px rgba(25,42,65,.025),0 12px 28px rgba(25,42,65,.055);}
#logisticsContent #lgReports .lr-finance-visuals {gap:14px;}
#logisticsContent #lgReports .lr-earnings-kpis {gap:12px;}
@media(max-width:760px){#logisticsContent #lgReports .lr-header{grid-template-columns:1fr;}#logisticsContent #lgReports .lr-header>div:first-child{grid-column:1;grid-row:1;}#logisticsContent #lgReports .lr-header .lr-print-actions{grid-column:1;grid-row:2;justify-content:flex-start;}#logisticsContent #lgReports .lr-header .lr-period{grid-column:1;grid-row:3;align-items:flex-start;}}

</style>
@endpush

@section('content')
@php
    $volumePeak = max(1, (int) $deliveryTrend->max('count'));
    $feePeak = max(1, (float) $deliveryTrend->max('fees'));
    $pipelinePeak = max(1, (int) $pipeline->max('count'));
    $riderPeak = max(1, (int) $topLastMileRiders->max('delivered_count'));
    $totalDeliveriesTrend = (int) $deliveryTrend->sum('count');
    $totalFeesTrend = (float) $deliveryTrend->sum('fees');
    $peakVolumeDay = $deliveryTrend->sortByDesc('count')->first();
    $peakFeeDay = $deliveryTrend->sortByDesc('fees')->first();
    $reportKpis = [
        ['Active Deliveries', (int) $stats['active_deliveries'], 'Current active orders', 'blue', 'activity'],
        ['Delivered Today', (int) $stats['delivered_today'], 'Today · Asia/Manila', 'green', 'check'],
        ['Delivered in Period', (int) $stats['delivered_this_month'], 'Selected date range', 'amber', 'calendar'],
        ['Delivery Fees in Period', '₱' . number_format((float) $stats['delivery_fees_this_month'], 2), 'Selected date range', 'purple', 'wallet'],
    ];
    $secondaryStats = [
        ['Waiting Assignment', (int) $stats['waiting_assignment']],
        ['Origin Parcels Processed', (int) $stats['origin_processed']],
        ['Ready for Linehaul', (int) $stats['ready_linehaul']],
    ];
@endphp

<div id="lgReports" class="sari-logistics-ui" data-reports-workspace data-print-report="{{ $printReport ? 'true' : 'false' }}">
    <header class="lr-header">
        <div>
            <p class="lr-kicker">LOGISTICS ANALYTICS</p>
            <h1 class="lr-title">Delivery <em>Reports</em></h1>
          
        </div>
    <div class="lr-print-actions">
        @if($printReport)
            <button class="lr-btn lr-btn-gold" type="button" onclick="window.print()">Print / Save PDF</button>
        @else
            <a class="lr-btn lr-btn-gold" target="_blank" rel="noopener" href="{{ url()->current().'?'.http_build_query(['from' => $from, 'to' => $to, 'print' => 1]) }}">Download Full Report (PDF)</a>
        @endif
    </div>
        <div class="lr-period" aria-label="Selected reporting period">
            <span class="lr-period-label">Reporting Period</span>
            <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>
            <span>Asia/Manila</span>
        </div>
    </header>

    <p class="lr-report-meta">Branch: <strong>{{ $hubFacility?->name ?: 'Current operating scope' }}</strong> · Generated {{ $reportGeneratedAt->format('M d, Y · g:i A') }} Asia/Manila{{ $printReport ? ' · Full filtered report' : '' }}</p>
    @if(isset($errors) && $errors->any())
        <div role="alert" style="margin:0 0 14px;padding:12px 14px;border-radius:12px;background:var(--sari-dark-950baec030, #fff5f3);border:1px solid var(--sari-dark-d8c2716225, #f0d6d1);color:var(--sari-dark-a63aa16d7c, #9b4f48);font-size:12px;box-shadow:var(--lr-shadow)">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="lr-card lr-filter-card">
        <div class="lr-filter-head">
            <div>
                <h2>Filter Delivery Reports</h2>
                <p>Review operational delivery metrics and completed orders for your selected date range.</p>
            </div>
        </div>
        <form method="GET" class="lr-filter-grid" aria-label="Reporting date range">
            <label class="lr-field">
                <span>From</span>
                <input class="lr-input" type="date" name="from" value="{{ $from }}" required>
            </label>
            <label class="lr-field">
                <span>To</span>
                <input class="lr-input" type="date" name="to" value="{{ $to }}" required>
            </label>
            <button type="submit" class="lr-btn lr-btn-gold">Apply Filter</button>
            <a href="?" class="lr-btn lr-btn-soft">Reset</a>
        </form>
    </section>

    <section class="lr-kpis" aria-label="Key delivery metrics">
        @foreach($reportKpis as [$label, $value, $note, $tone, $icon])
            <article class="lr-kpi">
                <div>
                    <p class="lr-kpi-label">{{ $label }}</p>
                    <strong>{{ is_numeric($value) ? number_format($value) : $value }}</strong>
                    <span class="lr-kpi-note">{{ $note }}</span>
                </div>
                <span class="lr-kpi-icon lr-{{ $tone }}" aria-hidden="true">
                    @switch($icon)
                        @case('activity')
                            <svg viewBox="0 0 24 24"><path d="M3 12h4l3-7 4 14 3-7h4"/></svg>
                            @break
                        @case('check')
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                            @break
                        @case('calendar')
                            <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg>
                            @break
                        @default
                            <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18M16 15h2"/></svg>
                    @endswitch
                </span>
            </article>
        @endforeach
    </section>

    @if(! $isHubOperator && $earningsSummary && $reportEarnings)
    <section class="lr-card lr-earnings-section" aria-labelledby="lrEarningsHeading">
        <div class="lr-card-head">
            <div>
                <h2 id="lrEarningsHeading">Earnings &amp; Commissions</h2>
                <p>Recorded shipping earnings for delivered orders in this branch and selected date range.</p>
            </div>
            <span class="lr-chip">{{ number_format((int) $earningsSummary->recorded_orders) }} recorded orders</span>
        </div>
        @php
            $allocationParts = [
                ['Pickup Rider Share', max(0, (float) $earningsSummary->pickup_cost), '#8ba7c4'],
                ['Last-Mile Rider Share', max(0, (float) $earningsSummary->last_mile_cost), '#637d9d'],
                ['Logistics Commission', max(0, (float) $earningsSummary->net_logistics), '#c88912'],
            ];
            $allocationTotal = array_sum(array_column($allocationParts, 1));
            $allocationOffset = 0;
            $availablePercent = (float) $earningsSummary->net_logistics > 0
                ? min(100, max(0, (float) $earningsSummary->available_earnings / (float) $earningsSummary->net_logistics * 100)) : 0;
        @endphp
        <div class="lr-finance-visuals">
            <article class="lr-allocation-chart">
                <h3>Shipping Earnings Allocation</h3>
                <div class="lr-allocation-body">
                    <svg viewBox="0 0 160 160" class="lr-donut" role="img" aria-label="Recorded shipping allocation: pickup Rider, last-mile Rider and Logistics commission">
                        <circle cx="80" cy="80" r="60" fill="none" stroke="#e8edf3" stroke-width="18"/>
                        @foreach($allocationParts as [$partLabel, $partAmount, $partColor])
                            @php $partPercent = $allocationTotal > 0 ? $partAmount / $allocationTotal * 100 : 0; @endphp
                            <circle cx="80" cy="80" r="60" fill="none" stroke="{{ $partColor }}" stroke-width="18" pathLength="100" stroke-dasharray="{{ $partPercent }} {{ 100 - $partPercent }}" stroke-dashoffset="{{ -$allocationOffset }}" transform="rotate(-90 80 80)"><title>{{ $partLabel }}: ₱{{ number_format($partAmount, 2) }} ({{ number_format($partPercent, 1) }}%)</title></circle>
                            @php $allocationOffset += $partPercent; @endphp
                        @endforeach
                        <text x="80" y="77" text-anchor="middle" class="lr-donut-caption">ALLOCATED</text>
                        <text x="80" y="96" text-anchor="middle" class="lr-donut-value">{{ $allocationTotal > 0 ? '100%' : 'No data' }}</text>
                    </svg>
                    <div class="lr-chart-legend">
                        @foreach($allocationParts as [$partLabel, $partAmount, $partColor])
                            <div><span class="lr-legend-dot" style="background:{{ $partColor }}"></span><span>{{ $partLabel }}<small>₱{{ number_format($partAmount, 2) }}</small></span><strong>{{ number_format($allocationTotal > 0 ? $partAmount / $allocationTotal * 100 : 0, 1) }}%</strong></div>
                        @endforeach
                    </div>
                </div>
            </article>
            <article class="lr-availability-chart">
                <h3>Net Earnings Availability</h3>
                <strong>{{ number_format($availablePercent, 1) }}%</strong>
                <p>Available portion of recorded net Logistics earnings</p>
                <div class="lr-availability-track" role="progressbar" aria-label="Available net earnings" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ round($availablePercent, 1) }}"><span style="width:{{ $availablePercent }}%"></span></div>
                <p>Available ₱{{ number_format((float) $earningsSummary->available_earnings, 2) }} · Pending ₱{{ number_format((float) $earningsSummary->pending_earnings, 2) }}</p>
                <small>Period earnings only; withdrawals can change the current wallet balance.</small>
            </article>
        </div>
        <div class="lr-earnings-kpis">
            @foreach([
                ['Gross Shipping Earnings', $earningsSummary->gross_shipping, 'Before Rider shares'],
                ['Pickup Rider Share', $earningsSummary->pickup_cost, 'Recorded pickup allocation'],
                ['Last-Mile Rider Share', $earningsSummary->last_mile_cost, 'Recorded delivery allocation'],
                ['Net Logistics Commission', $earningsSummary->net_logistics, 'Logistics margin after Rider shares'],
                ['Pending COD Earnings', $earningsSummary->pending_earnings, 'Awaiting cash reconciliation / receipt'],
                ['Available Earnings', $earningsSummary->available_earnings, 'Recognized as available; not wallet balance'],
            ] as [$label, $amount, $note])
                <article class="lr-earnings-kpi">
                    <p>{{ $label }}</p>
                    <strong>₱{{ number_format((float) $amount, 2) }}</strong>
                    <small>{{ $note }}</small>
                </article>
            @endforeach
        </div>
        <p class="lr-earnings-note">Logistics commission is the recorded shipping margin, not the platform's product commission. Pending and available are portions of net earnings. Available earnings are not a current withdrawable wallet balance.</p>
        @if((int) $earningsSummary->recorded_orders < $periodDelivered)
            <p class="lr-earnings-note">{{ number_format($periodDelivered - (int) $earningsSummary->recorded_orders) }} delivered order(s) have no Logistics earning record in this report. Their earnings are not estimated.</p>
        @endif
        <div class="lr-table-scroll">
            <table class="lr-delivery-table lr-earnings-table">
                <thead><tr><th>Order / Delivered</th><th class="lr-money">Gross Shipping</th><th class="lr-money">Pickup Share</th><th class="lr-money">Last-Mile Share</th><th class="lr-money">Net Commission</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($reportEarnings as $earning)
                        <tr>
                            <td data-label="Order / Delivered"><strong>{{ $earning->order?->order_number ?: 'Order' }}</strong><span class="lr-cell-detail">{{ $earning->order?->delivered_at?->timezone('Asia/Manila')->format('M d, Y · g:i A') ?: '—' }}</span></td>
                            <td data-label="Gross Shipping" class="lr-money">₱{{ number_format((float) $earning->gross_shipping_revenue, 2) }}</td>
                            <td data-label="Pickup Share" class="lr-money">₱{{ number_format((float) $earning->pickup_rider_cost, 2) }}</td>
                            <td data-label="Last-Mile Share" class="lr-money">₱{{ number_format((float) $earning->last_mile_rider_cost, 2) }}</td>
                            <td data-label="Net Commission" class="lr-money"><strong>₱{{ number_format((float) $earning->net_logistics_amount, 2) }}</strong></td>
                            <td data-label="Status">{{ ucwords(str_replace('_', ' ', $earning->status)) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="lr-empty"><strong>No recorded earnings in this period</strong><p>Try another date range or operating branch.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(! $printReport)<div class="lr-earnings-pagination">{{ $reportEarnings->links() }}</div>@endif
    </section>
    @endif

    <section class="lr-secondary" aria-label="Operational snapshot">
        @foreach($secondaryStats as [$label, $value])
            <article class="lr-secondary-card">
                <span class="lr-secondary-label">{{ $label }}</span>
                <strong>{{ number_format($value) }}</strong>
            </article>
        @endforeach
    </section>

    <section class="lr-insights-grid">
        <article class="lr-card">
            <div class="lr-card-head">
                <div>
                    <h2>Delivery Performance</h2>
                    <p>{{ $printReport ? 'Daily completions throughout the selected date range.' : 'Daily completions over the last seven days within your selected date range.' }}</p>
                </div>
                <div class="lr-chart-tabs" role="group" aria-label="Graph metric switch">
                    <button type="button" data-chart-metric="count" aria-pressed="true">Deliveries</button>
                    <button type="button" data-chart-metric="fees" aria-pressed="false">Fees</button>
                </div>
            </div>
            <div class="lr-chart-wrap">
                <div class="lr-chart-summary">
                    <div>
                        <span>{{ $printReport ? 'Total in period' : 'Total over 7 days' }}</span>
                        <strong data-chart-summary="count">{{ number_format($totalDeliveriesTrend) }}</strong>
                    </div>
                    <div>
                        <span>Peak day</span>
                        <strong data-chart-summary="peak">{{ $peakVolumeDay['label'] ?? '—' }}{{ isset($peakVolumeDay['count']) ? ' · '.number_format((int) $peakVolumeDay['count']) : '' }}</strong>
                    </div>
                    <div>
                        <span>Current metric</span>
                        <strong data-chart-summary="mode">Deliveries</strong>
                    </div>
                </div>
                @if($printReport)
                    <table class="lr-daily-print"><thead><tr><th>Date</th><th>Delivered</th><th>Delivery Fees</th></tr></thead><tbody>
                        @foreach($deliveryTrend as $day)
                            <tr><td>{{ $day['date']->format('M d, Y') }}</td><td>{{ number_format((int) $day['count']) }}</td><td>₱{{ number_format((float) $day['fees'], 2) }}</td></tr>
                        @endforeach
                    </tbody></table>
                @else
                <div class="lr-plot-grid" role="group" aria-label="Daily delivery performance" style="grid-template-columns:repeat({{ max(1, $deliveryTrend->count()) }}, minmax(0,1fr))">
                    @foreach($deliveryTrend as $day)
                        @php
                            $count = (int) $day['count'];
                            $fees = (float) $day['fees'];
                            $countHeight = max(3, ($count / $volumePeak) * 100);
                        @endphp
                        <div class="lr-day"
                             data-chart-day
                             data-label="{{ $day['label'] }}"
                             data-short-date="{{ $day['short_date'] }}"
                             data-count="{{ $count }}"
                             data-fees="{{ $fees }}"
                             data-max-count="{{ $volumePeak }}"
                             data-max-fees="{{ $feePeak }}">
                            <span class="lr-day-value" data-chart-value>{{ $count }}</span>
                            <div class="lr-bar-holder">
                                <span class="lr-vertical-bar {{ $count > 0 ? 'is-positive' : '' }}" data-chart-bar style="height:{{ $countHeight }}%" title="{{ $day['short_date'] }}: {{ $count }} delivered"></span>
                            </div>
                            <div class="lr-day-label">{{ $day['label'] }}<small>{{ $day['short_date'] }}</small></div>
                        </div>
                    @endforeach
                </div>
                @endif
                <div class="lr-chart-footer">Graph view for daily deliveries and fees. Philippine peso (₱) totals are shown when Fees is selected.</div>
            </div>
        </article>

        <article class="lr-card">
            <div class="lr-card-head">
                <div>
                    <h2>Operational Pipeline</h2>
                    <p>Current parcel workload by stage. This is a live workload snapshot, not a historical trend.</p>
                </div>
                <span class="lr-chip">{{ $pipeline->count() }} stages</span>
            </div>
            <div class="lr-pipeline">
                <div class="lr-pipeline-grid">
                    @foreach($pipeline as $entry)
                        @php $count = max(0, (int) $entry['count']); @endphp
                        <div class="lr-pipeline-item">
                            <div class="lr-pipeline-top">
                                <span>{{ $entry['label'] }}</span>
                                <strong>{{ number_format($count) }}</strong>
                            </div>
                            <div class="lr-progress" role="progressbar" aria-label="{{ $entry['label'] }}" aria-valuemin="0" aria-valuemax="{{ $pipelinePeak }}" aria-valuenow="{{ $count }}">
                                <span class="{{ $loop->first ? 'is-highlight' : '' }}" style="width:{{ round(($count / $pipelinePeak) * 100, 2) }}%"></span>
                            </div>
                            <p class="lr-pipeline-note">{{ $entry['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>

    <section class="lr-card lr-section-spacer">
        <div class="lr-card-head">
            <div>
                <h2>{{ $printReport ? 'Last-Mile Rider Performance' : 'Top Last-Mile Riders' }}</h2>
                <p>Completed deliveries and delivery fees recorded within the selected reporting period.</p>
            </div>
            @if($topLastMileRiders->isNotEmpty())
                <span class="lr-chip">{{ $topLastMileRiders->count() }} ranked riders</span>
            @endif
        </div>
        @if($topLastMileRiders->isNotEmpty())
            <div class="lr-riders-list">
                @foreach($topLastMileRiders as $rider)
                    <div class="lr-rider-row">
                        <span class="lr-rank">{{ $loop->iteration }}</span>
                        <div>
                            <div class="lr-rider-name">{{ trim(($rider->first_name ?? '').' '.($rider->last_name ?? '')) ?: 'Rider' }}</div>
                            <p class="lr-rider-detail">{{ number_format((int) $rider->delivered_count) }} completed deliveries · ₱{{ number_format((float) $rider->delivered_fees, 2) }} in delivery fees</p>
                            <div class="lr-progress" style="margin-top:9px" role="progressbar" aria-label="Rider completed deliveries" aria-valuemin="0" aria-valuemax="{{ $riderPeak }}" aria-valuenow="{{ (int) $rider->delivered_count }}">
                                <span class="{{ $loop->first ? 'is-highlight' : '' }}" style="width:{{ round(((int) $rider->delivered_count) / $riderPeak * 100, 2) }}%"></span>
                            </div>
                        </div>
                        <div class="lr-rider-total">
                            <strong>{{ number_format((int) $rider->delivered_count) }}</strong>
                            <span>Delivered</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="lr-empty" role="status">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 21v-2c0-3 2-5 6-5s6 2 6 5v2M17 6h4M19 4v4"/></svg>
                <strong>No rider performance recorded</strong>
                <p>No ranked Last-Mile Riders for this reporting period.</p>
            </div>
        @endif
    </section>

    <div class="lr-toolbar" aria-label="Search completed deliveries">
        <label class="lr-search-field">
            <span class="sr-only">Search completed deliveries</span>
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="7"/><path d="m16.2 16.2 4.8 4.8"/></svg>
            <input class="lr-search-input" type="search" data-report-search placeholder="Search order, Seller, Buyer, or Rider…" autocomplete="off" maxlength="120">
        </label>
        <span class="lr-results-pill">{{ number_format($recentDelivered->count()) }} {{ \Illuminate\Support\Str::plural('delivery', $recentDelivered->count()) }}</span>
    </div>

    <section class="lr-card lr-table-card">
        <div class="lr-card-head">
            <div>
                <h2>{{ $printReport ? 'All Completed Deliveries' : 'Recent Completed Deliveries' }}</h2>
                <p>Completed orders in the selected date range.</p>
            </div>
        </div>
        <div class="lr-table-scroll" data-report-table-wrap>
            <table class="lr-delivery-table">
                <thead>
                    <tr>
                        <th style="width:30%">Order / Seller</th>
                        <th style="width:16%">Buyer</th>
                        <th style="width:16%">Rider</th>
                        <th class="lr-money" style="width:13%">Fee</th>
                        <th style="width:25%">Delivered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDelivered as $order)
                        <tr data-report-row data-search="{{ implode(' ', array_filter([$order->order_number, $order->seller?->store_name, $order->buyer_name, $order->courier_name])) }}">
                            <td class="lr-order-cell" data-label="Order / Seller">
                                <strong>{{ $order->order_number }}</strong>
                                <span class="lr-cell-detail">{{ $order->seller?->store_name ?: 'SARI Seller' }}</span>
                            </td>
                            <td data-label="Buyer">{{ $order->buyer_name ?: '—' }}</td>
                            <td data-label="Rider">{{ $order->courier_name ?: '—' }}</td>
                            <td class="lr-fee" data-label="Fee">₱{{ number_format((float) $order->delivery_fee, 2) }}</td>
                            <td class="lr-date-cell" data-label="Delivered">
                                <span class="lr-date-primary">{{ $order->delivered_at?->copy()->timezone('Asia/Manila')->format('M d, Y') ?: '—' }}</span>
                                @if($order->delivered_at)
                                    <span class="lr-cell-detail">{{ $order->delivered_at->copy()->timezone('Asia/Manila')->format('g:i A') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr data-report-no-data>
                            <td colspan="5">
                                <div class="lr-empty">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v16H4zM8 3v4M16 3v4M4 10h16"/><path d="M9 15h6"/></svg>
                                    <strong>No completed deliveries yet</strong>
                                    <p>Delivered orders will appear here when available.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div data-report-empty-search class="lr-empty lr-empty-search" hidden role="status">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
            <strong>No matching deliveries</strong>
            <p>Try a different order, Seller, Buyer, or Rider.</p>
        </div>

        @if($recentDelivered->isNotEmpty())
            <footer class="lr-table-footer">
                <span data-report-range>Showing 1–{{ min(10, $recentDelivered->count()) }} of {{ $recentDelivered->count() }} deliveries</span>
                <label class="lr-perpage" data-report-perpage @if($recentDelivered->count() <= 10) hidden @endif>
                    Rows per page
                    <select data-report-size aria-label="Rows per page">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </label>
            </footer>
        @endif
    </section>
    <nav class="lr-pages" aria-label="Recent completed deliveries pagination" data-report-pages hidden></nav>
</div>
@endsection

@push('scripts')
<script>
(() => {
    'use strict';

    function initReports() {
        const root = document.querySelector('[data-reports-workspace]');
        if (!root || root.dataset.reportsInitialized) return;
        root.dataset.reportsInitialized = '1';
        if (root.dataset.printReport === 'true') return;

        const dayItems = [...root.querySelectorAll('[data-chart-day]')];
        const metricButtons = [...root.querySelectorAll('[data-chart-metric]')];
        const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 });
        const compactPeso = new Intl.NumberFormat('en-PH', { notation: 'compact', maximumFractionDigits: 1 });
        const intFmt = new Intl.NumberFormat('en-PH');
        const summaryCount = root.querySelector('[data-chart-summary="count"]');
        const summaryPeak = root.querySelector('[data-chart-summary="peak"]');
        const summaryMode = root.querySelector('[data-chart-summary="mode"]');

        function computeMetricSummary(metric) {
            if (!dayItems.length) return { total: 0, peakLabel: '—' };
            let total = 0;
            let peakValue = -1;
            let peakLabel = '—';
            dayItems.forEach((item) => {
                const value = Number(item.dataset[metric] || 0);
                total += value;
                if (value > peakValue) {
                    peakValue = value;
                    peakLabel = `${item.dataset.label || ''} · ${metric === 'count' ? intFmt.format(value) : peso.format(value)}`.trim();
                }
            });
            return { total, peakLabel };
        }

        function setMetric(metric) {
            dayItems.forEach((item) => {
                const value = Number(item.dataset[metric] || 0);
                const peak = Math.max(1, Number(item.dataset[metric === 'count' ? 'maxCount' : 'maxFees'] || 1));
                const bar = item.querySelector('[data-chart-bar]');
                const label = item.querySelector('[data-chart-value]');
                bar.style.height = Math.max(3, (value / peak) * 100) + '%';
                bar.classList.toggle('is-positive', value > 0);
                label.textContent = metric === 'count' ? intFmt.format(value) : '₱' + compactPeso.format(value);
                bar.title = `${item.dataset.shortDate}: ${metric === 'count' ? `${intFmt.format(value)} delivered` : `${peso.format(value)} in fees`}`;
            });

            const summary = computeMetricSummary(metric);
            if (summaryCount) summaryCount.textContent = metric === 'count' ? intFmt.format(summary.total) : peso.format(summary.total);
            if (summaryPeak) summaryPeak.textContent = summary.peakLabel;
            if (summaryMode) summaryMode.textContent = metric === 'count' ? 'Deliveries' : 'Fees';

            metricButtons.forEach((button) => {
                button.setAttribute('aria-pressed', String(button.dataset.chartMetric === metric));
            });
        }

        metricButtons.forEach((button) => {
            button.addEventListener('click', () => setMetric(button.dataset.chartMetric));
        });
        setMetric('count');

        const rows = [...root.querySelectorAll('[data-report-row]')];
        const search = root.querySelector('[data-report-search]');
        const size = root.querySelector('[data-report-size]');
        const pages = root.querySelector('[data-report-pages]');
        const range = root.querySelector('[data-report-range]');
        const noSearch = root.querySelector('[data-report-empty-search]');
        const tableWrap = root.querySelector('[data-report-table-wrap]');
        const perPage = root.querySelector('[data-report-perpage]');
        let currentPage = 1;

        function renderTable() {
            const phrase = String(search?.value || '').trim().toLocaleLowerCase();
            const matches = rows.filter((row) => String(row.dataset.search || '').toLocaleLowerCase().includes(phrase));
            const pageSize = Number(size?.value || 10);
            const pageCount = Math.max(1, Math.ceil(matches.length / pageSize));
            currentPage = Math.max(1, Math.min(pageCount, currentPage));

            rows.forEach((row) => { row.hidden = true; });
            matches.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach((row) => { row.hidden = false; });

            const start = matches.length ? ((currentPage - 1) * pageSize) + 1 : 0;
            const end = Math.min(currentPage * pageSize, matches.length);
            if (range) range.textContent = `Showing ${start}–${end} of ${matches.length} deliveries`;
            if (noSearch) noSearch.hidden = rows.length === 0 || matches.length !== 0;
            if (tableWrap) tableWrap.hidden = rows.length !== 0 && matches.length === 0;
            if (perPage) perPage.hidden = rows.length <= 10;

            if (!pages) return;
            pages.innerHTML = '';
            pages.hidden = pageCount <= 1 || matches.length === 0;
            if (pages.hidden) return;

            const addButton = (text, target, disabled, current) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = text;
                button.disabled = disabled;
                if (current) {
                    button.classList.add('is-active');
                    button.setAttribute('aria-current', 'page');
                }
                button.addEventListener('click', () => {
                    currentPage = target;
                    renderTable();
                });
                pages.appendChild(button);
            };

            addButton('<', Math.max(1, currentPage - 1), currentPage === 1, false);
            const firstPage = Math.max(1, Math.min(currentPage - 2, pageCount - 4));
            const lastPage = Math.min(pageCount, firstPage + 4);
            for (let page = firstPage; page <= lastPage; page += 1) {
                addButton(String(page), page, false, currentPage === page);
            }
            addButton('>', Math.min(pageCount, currentPage + 1), currentPage === pageCount, false);
        }

        search?.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
        });
        size?.addEventListener('change', () => {
            currentPage = 1;
            renderTable();
        });
        renderTable();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReports);
    } else {
        initReports();
    }
})();
</script>
@endpush
