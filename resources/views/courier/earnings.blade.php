@extends('layouts.courier')



@section('title', 'Rider Earnings')

@section('header-title', 'Rider Earnings')

@section('header-subtitle', 'Delivery Fee Ledger')



@push('styles')

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

@verbatim

/* SARI Earnings • Buyer Cart / Rider visual language. Scoped to this view. */

body:has(#sariRiderEarnings),

body:has(#sariRiderEarnings) #courierMain,

body:has(#sariRiderEarnings) #courierPageContent { background:#f4f5f7 !important; }

#courierMain > main:has(#sariRiderEarnings),

#courierPageContent:has(#sariRiderEarnings) { padding:clamp(16px,2.2vw,30px) !important; }

#sariRiderEarnings { --re-ink:#1c2a3e; --re-muted:#728095; --re-light:#98a4b2; --re-line:#e7ebf1; --re-gold:#c88912; --re-shadow:0 3px 12px rgba(26,39,57,.025),0 13px 33px rgba(26,39,57,.057); max-width:1620px; margin:0 auto; color:var(--re-ink); font:400 13px/1.65 'Poppins',system-ui,sans-serif; }

#sariRiderEarnings, #sariRiderEarnings *, #sariRiderEarnings *::before, #sariRiderEarnings *::after { box-sizing:border-box; }

#sariRiderEarnings :is(button,input,select) { font:inherit; }

#sariRiderEarnings a { text-decoration:none; }

#sariRiderEarnings :is(a,button,input,select):focus-visible { outline:2px solid #c88912; outline-offset:3px; }

#sariRiderEarnings .re-card { min-width:0; background:#fff; border:1px solid var(--re-line); border-radius:17px; box-shadow:var(--re-shadow); }

#sariRiderEarnings .re-head { display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:20px; margin:0 0 20px; }

#sariRiderEarnings .re-eyebrow { display:flex; align-items:center; gap:8px; margin:0 0 7px; color:#8793a3; font-size:11px; font-weight:500; }

#sariRiderEarnings .re-eyebrow::before { content:''; height:7px; width:7px; border-radius:50%; background:#c88912; }

#courierMain #sariRiderEarnings .re-page-title, #courierPageContent #sariRiderEarnings .re-page-title { margin:0; font-size:clamp(29px,2.7vw,38px); line-height:1.2; letter-spacing:-.045em; font-weight:600; color:#152235; }

#sariRiderEarnings .re-intro { max-width:760px; margin:9px 0 0; color:var(--re-muted); font-size:13px; line-height:1.75; }

#sariRiderEarnings .re-actions { display:flex; flex-wrap:wrap; align-items:center; gap:9px; }

#sariRiderEarnings .re-btn { min-height:43px; padding:10px 16px; border:1px solid #dfe5ec; border-radius:11px; background:#fff; color:#425169; display:inline-flex; gap:9px; align-items:center; justify-content:center; font-size:12px; font-weight:500; line-height:1.4; cursor:pointer; transition:background-color .16s ease,border-color .16s ease,transform .16s ease; }

#sariRiderEarnings .re-btn:hover { background:#f7f9fc; border-color:#c6d0dc; }

#sariRiderEarnings .re-btn svg { height:17px; width:17px; stroke-width:1.7; flex:none; }

#sariRiderEarnings .re-btn-gold { background:var(--re-gold); border-color:var(--re-gold); color:#fff; }

#sariRiderEarnings .re-btn-gold:hover { background:#b47b0e; border-color:#b47b0e; }

#sariRiderEarnings .re-btn:disabled { cursor:not-allowed; opacity:.48; }

#sariRiderEarnings .re-alert { padding:13px 16px; border-radius:12px; margin-bottom:17px; font-size:12px; }

#sariRiderEarnings .re-alert-success { background:#ecf8f0; border:1px solid #c6e6d0; color:#31724c; }

#sariRiderEarnings .re-alert-error { background:#fff0f1; border:1px solid #efc7cc; color:#a13e4b; }

#sariRiderEarnings .re-metrics { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:14px; }

#sariRiderEarnings .re-metric { min-height:139px; padding:20px 19px 18px; display:flex; flex-direction:column; justify-content:space-between; gap:15px; }

#sariRiderEarnings .re-metric-top { display:flex; align-items:flex-start; justify-content:space-between; gap:9px; }

#sariRiderEarnings .re-metric-label { margin:0; color:#66768b; font-weight:400; font-size:13px; }

#sariRiderEarnings .re-metric-icon { display:grid; place-items:center; height:29px; width:29px; flex:none; color:#8591a2; }

#sariRiderEarnings .re-metric-icon svg { height:23px; width:23px; stroke-width:1.65; }

#sariRiderEarnings .re-metric-number { margin:0; color:#172439; font-size:clamp(21px,1.85vw,28px); font-weight:600; line-height:1.3; letter-spacing:-.045em; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }

#sariRiderEarnings .re-metric-feature .re-metric-icon { color:#c88912; }

#sariRiderEarnings .re-help { display:flex; align-items:flex-start; gap:10px; margin:14px 0 17px; padding:13px 15px; background:#fff; border:1px solid #e8edf2; border-radius:12px; color:#78869a; font-size:12px; line-height:1.65; }

#sariRiderEarnings .re-help svg { width:17px; height:17px; flex:none; margin-top:2px; stroke-width:1.7; }

#sariRiderEarnings .re-help strong { font-weight:500; color:#354458; }

#sariRiderEarnings .re-submetrics { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin:0 0 18px; }

#sariRiderEarnings .re-submetric { padding:18px 20px; }

#sariRiderEarnings .re-submetric-label { color:#728197; font-size:12px; margin:0; }

#sariRiderEarnings .re-submetric-number { margin:7px 0 2px; color:#243348; font-size:22px; font-weight:600; font-variant-numeric:tabular-nums; letter-spacing:-.035em; overflow-wrap:anywhere; }

#sariRiderEarnings .re-submetric-note { margin:4px 0 0; color:#96a1af; font-size:11px; line-height:1.65; }

#sariRiderEarnings .re-grid { display:grid; grid-template-columns:minmax(0,1.45fr) minmax(310px,.8fr); gap:17px; align-items:start; }

#sariRiderEarnings .re-section-card { padding:22px 23px; }

#sariRiderEarnings .re-panel-header { display:flex; align-items:flex-start; justify-content:space-between; gap:13px; flex-wrap:wrap; margin-bottom:18px; }

#courierMain #sariRiderEarnings .re-section-title, #courierPageContent #sariRiderEarnings .re-section-title { margin:0; color:#253448; font-size:18px; font-weight:600; letter-spacing:-.025em; line-height:1.4; }

#sariRiderEarnings .re-section-description { margin:4px 0 0; color:#95a1af; font-size:12px; }

#sariRiderEarnings .re-tag { display:inline-flex; align-items:center; padding:5px 10px; border-radius:99px; background:#f1f4f8; color:#6b788a; font-size:11px; font-weight:500; }

#sariRiderEarnings .re-chart { height:240px; display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); gap:clamp(5px,1vw,18px); align-items:stretch; padding:16px 3px 0; border-bottom:1px solid #edf0f4; }

#sariRiderEarnings .re-chart-col { min-width:0; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; gap:7px; }

#sariRiderEarnings .re-chart-value { width:100%; text-align:center; color:#5c6b7e; font-size:10px; line-height:1.3; font-weight:500; white-space:nowrap; }

#sariRiderEarnings .re-chart-bar-track { width:min(100%,48px); height:175px; display:flex; align-items:end; justify-content:center; }

#sariRiderEarnings .re-chart-bar { display:block; width:100%; border-radius:8px 8px 0 0; background:#8f9dad; min-height:3px; transition:background-color .15s ease; }

#sariRiderEarnings .re-chart-col:hover .re-chart-bar { background:#c88912; }

#sariRiderEarnings .re-chart-bar.is-zero { background:#e6ebf0; }

#sariRiderEarnings .re-chart-day { color:#8793a4; font-size:11px; font-weight:500; text-align:center; padding-bottom:9px; }

#sariRiderEarnings .re-balance { background:#f7f9fc; border:1px solid #e8edf3; border-radius:13px; padding:18px 19px; }

#sariRiderEarnings .re-balance-label { margin:0; color:#778699; font-size:12px; }

#sariRiderEarnings .re-balance-number { margin:7px 0; font-size:clamp(26px,2.6vw,34px); font-weight:600; letter-spacing:-.045em; line-height:1.3; color:#233247; overflow-wrap:anywhere; font-variant-numeric:tabular-nums; }

#sariRiderEarnings .re-balance-note { margin:0; color:#8c98a8; font-size:11px; line-height:1.65; }

#sariRiderEarnings .re-form { margin-top:20px; display:grid; gap:13px; }

#sariRiderEarnings .re-field { display:grid; gap:6px; }

#sariRiderEarnings .re-field-label { color:#526176; font-size:11px; font-weight:500; }

#sariRiderEarnings .re-field-input { display:block; min-height:45px; width:100%; padding:11px 13px; background:#fff; border:1px solid #dee5ed; border-radius:10px; color:#2b3b4e; font-size:12px; outline:none; }

#sariRiderEarnings .re-field-input:focus { border-color:#d2a34b; box-shadow:0 0 0 3px rgba(201,141,25,.10); }

#sariRiderEarnings .re-field-input::placeholder { color:#a7b0bb; }

#sariRiderEarnings .re-form .re-btn { width:100%; margin-top:3px; }

#sariRiderEarnings .re-payout-history { margin-top:20px; border-top:1px solid #edf0f4; padding-top:17px; }

#sariRiderEarnings .re-payout-title { margin:0 0 11px; font-size:13px; font-weight:600; color:#344357; }

#sariRiderEarnings .re-payout-entry { padding:10px 0; display:flex; justify-content:space-between; align-items:center; gap:12px; border-bottom:1px solid #eff2f6; font-size:12px; }

#sariRiderEarnings .re-payout-entry:last-child { border-bottom:0; }

#sariRiderEarnings .re-payout-entry strong { color:#263649; font-weight:500; }

#sariRiderEarnings .re-payout-entry span { color:#6b7788; }

#sariRiderEarnings .re-payout-empty { padding:17px 8px; color:#929eab; background:#f9fafc; border:1px dashed #e1e6ed; border-radius:11px; text-align:center; font-size:12px; }

/* The ledger sits directly below Weekly Performance in the same left column. */

#sariRiderEarnings .re-performance-stack { display:grid; gap:17px; align-content:start; min-width:0; }

#sariRiderEarnings .re-recent-card { overflow:hidden; padding:0; }

#sariRiderEarnings .re-recent-heading { padding:21px 23px 18px; margin:0; }

#sariRiderEarnings .re-recent-list { list-style:none; padding:0; margin:0; border-top:1px solid #edf0f4; }

#sariRiderEarnings .re-recent-row { min-width:0; display:grid; grid-template-columns:minmax(0,1.15fr) minmax(0,1fr) auto; align-items:center; gap:12px 18px; padding:17px 23px; border-bottom:1px solid #f0f2f5; }

#sariRiderEarnings .re-recent-row:last-child { border-bottom:0; }

#sariRiderEarnings .re-recent-row:hover { background:#fafbfd; }

#sariRiderEarnings .re-recent-primary, #sariRiderEarnings .re-recent-secondary { display:grid; align-content:center; gap:5px; min-width:0; }

#sariRiderEarnings .re-recent-order { font-size:12px; font-weight:600; color:#26374b; line-height:1.5; overflow-wrap:anywhere; }

#sariRiderEarnings .re-recent-date { font-size:11px; font-weight:400; color:#8794a4; line-height:1.55; overflow-wrap:anywhere; }

#sariRiderEarnings .re-recent-type { font-size:11px; font-weight:500; color:#566579; line-height:1.5; overflow-wrap:anywhere; }

#sariRiderEarnings .re-recent-status { display:inline-flex; align-items:center; align-self:start; width:fit-content; max-width:100%; border:1px solid #e8edf2; background:#f5f7fa; color:#68788b; border-radius:999px; padding:4px 9px; font-size:10px; font-weight:500; line-height:1.5; overflow-wrap:anywhere; }

#sariRiderEarnings .re-recent-amount { font-size:15px; font-weight:600; color:#26374b; font-variant-numeric:tabular-nums; letter-spacing:-.025em; white-space:nowrap; text-align:right; }

#sariRiderEarnings .re-recent-empty { padding:36px 20px; color:#8c99a9; font-size:12px; text-align:center; }
#sariRiderEarnings .re-recent-row[hidden] { display:none !important; }
#sariRiderEarnings .re-metric--today .re-metric-icon { color:#3b82f6; }
#sariRiderEarnings .re-metric--week .re-metric-icon { color:#c88912; }
#sariRiderEarnings .re-metric--month .re-metric-icon { color:#10b981; }
#sariRiderEarnings .re-metric--potential .re-metric-icon { color:#8b5cf6; }
#sariRiderEarnings .re-metric--available .re-metric-icon { color:#c88912; }

@media(max-width:1220px) { #sariRiderEarnings .re-recent-row { grid-template-columns:minmax(0,1fr) auto; } #sariRiderEarnings .re-recent-secondary { grid-column:1/-1; grid-row:2; } #sariRiderEarnings .re-recent-amount { grid-column:2; grid-row:1; } }

@media(min-width:1221px) and (max-width:1490px) { #sariRiderEarnings .re-recent-row { grid-template-columns:minmax(0,1fr) auto; } #sariRiderEarnings .re-recent-secondary { grid-column:1/-1; grid-row:2; } #sariRiderEarnings .re-recent-amount { grid-column:2; grid-row:1; } }

@media(max-width:650px) { #sariRiderEarnings .re-recent-heading { padding:18px; } #sariRiderEarnings .re-recent-row { padding:15px 18px; column-gap:12px; } #sariRiderEarnings .re-recent-order { font-size:12px; } #sariRiderEarnings .re-recent-amount { font-size:14px; } }

@media(max-width:1220px){ #sariRiderEarnings .re-metrics { grid-template-columns:repeat(3,minmax(0,1fr)); } #sariRiderEarnings .re-grid { grid-template-columns:minmax(0,1.1fr) minmax(300px,.9fr); } }

@media(max-width:960px){ #sariRiderEarnings .re-grid { grid-template-columns:1fr; } }

@media(max-width:650px){ #sariRiderEarnings .re-metrics { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; } #sariRiderEarnings .re-metric { min-height:118px; padding:16px; } #sariRiderEarnings .re-metric-feature { grid-column:span 2; } #sariRiderEarnings .re-metric-number { font-size:23px; } #sariRiderEarnings .re-submetrics { grid-template-columns:1fr; gap:10px; } #sariRiderEarnings .re-submetric { padding:15px 17px; } #sariRiderEarnings .re-section-card { padding:18px; } #sariRiderEarnings .re-chart { gap:3px; } #sariRiderEarnings .re-chart-value { font-size:9px; } #sariRiderEarnings .re-intro { font-size:12px; } #sariRiderEarnings .re-actions { width:100%; } #sariRiderEarnings .re-actions .re-btn { flex:1; } }

@media(max-width:380px){ #sariRiderEarnings .re-chart-value { font-size:8px; } #sariRiderEarnings .re-metric-number { font-size:21px; } }

@media(prefers-reduced-motion:reduce){ #sariRiderEarnings * { transition:none !important; } }


/* SARI Earnings UI alignment: COD Remittance / Delivery History typography.
   Presentation only. All earnings, form values, routes and payout logic remain untouched. */
#courierMain > main:has(#sariRiderEarnings),
#courierPageContent:has(#sariRiderEarnings) {
    padding:22px clamp(14px,2.2vw,30px) 42px !important;
}
#sariRiderEarnings {
    --re-ink:#2f3742;
    --re-muted:#7a8593;
    --re-light:#94a0ad;
    --re-line:#e5e7eb;
    --re-gold:#c88912;
    --re-shadow:0 2px 5px rgba(15,23,42,.045),
                0 10px 24px rgba(15,23,42,.075),
                0 24px 54px rgba(15,23,42,.095),
                inset 0 1px 0 rgba(255,255,255,.92);
    max-width:1400px;
    font:400 13px/1.65 'Poppins',ui-sans-serif,system-ui,sans-serif;
}
#sariRiderEarnings .re-card {
    border:1px solid var(--re-line);
    border-radius:18px;
    box-shadow:var(--re-shadow);
}
#sariRiderEarnings .re-head { gap:13px 22px; margin:0 0 18px; align-items:flex-end; }
#sariRiderEarnings .re-eyebrow {
    display:block;
    margin:0 0 6px;
    color:var(--re-gold);
    font-size:10.5px;
    font-weight:800;
    line-height:1.4;
    letter-spacing:.14em;
    text-transform:uppercase;
}
#sariRiderEarnings .re-eyebrow::before { content:none; }
#courierMain #sariRiderEarnings .re-page-title,
#courierPageContent #sariRiderEarnings .re-page-title {
    margin:0;
    color:var(--re-ink);
    font-size:clamp(28px,2.35vw,33px);
    line-height:1.05;
    font-weight:760;
    letter-spacing:-.04em;
}
#sariRiderEarnings .re-title-accent { color:var(--re-gold); }
#sariRiderEarnings .re-intro {
    max-width:740px;
    margin:9px 0 0;
    color:var(--re-muted);
    font-size:11.5px;
    line-height:1.7;
}
#sariRiderEarnings .re-actions { gap:9px; }
#sariRiderEarnings .re-btn {
    min-height:43px;
    padding:10px 15px;
    border-radius:12px;
    border-color:#dce2e8;
    color:#465363;
    font-size:11.5px;
    font-weight:700;
}
#sariRiderEarnings .re-btn:hover { background:#f3f4f6; border-color:#cbd5df; color:#293440; }
#sariRiderEarnings .re-btn-gold { background:var(--re-gold); border-color:var(--re-gold); color:#fff; }
#sariRiderEarnings .re-btn-gold:hover { background:#b47b0e; border-color:#b47b0e; color:#fff; }
#sariRiderEarnings .re-btn svg { width:16px; height:16px; }
#sariRiderEarnings .re-metrics { gap:14px; }
#sariRiderEarnings .re-metric {
    min-height:110px;
    padding:14px 16px;
    gap:10px;
}
#sariRiderEarnings .re-metric-label { color:#5f6d7c; font-size:13px; font-weight:600; line-height:1.4; }
#sariRiderEarnings .re-metric-icon { width:27px; height:27px; }
#sariRiderEarnings .re-metric-icon svg { width:23px; height:23px; stroke-width:1.8; }
#sariRiderEarnings .re-metric-number {
    color:#1f2937;
    font-size:clamp(22px,1.9vw,30px);
    line-height:1.12;
    font-weight:650;
    letter-spacing:-.045em;
}
#sariRiderEarnings .re-help {
    display:flex;
    align-items:flex-start;
    gap:10px;
    margin:14px 0 17px;
    padding:12px 15px;
    border:1px solid #e2e8f0;
    background:#f8fafc;
    color:#64748b;
    font-size:11.5px;
}
#sariRiderEarnings .re-help svg { color:var(--re-gold); }
#sariRiderEarnings .re-help strong { color:#334155; font-weight:700; }
#sariRiderEarnings .re-submetrics { gap:14px; }
#sariRiderEarnings .re-submetric { padding:15px 17px; }
#sariRiderEarnings .re-submetric-label { color:#657487; font-size:11.5px; font-weight:600; }
#sariRiderEarnings .re-submetric-number {
    margin:7px 0 3px;
    color:#243348;
    font-size:21px;
    font-weight:650;
    line-height:1.22;
}
#sariRiderEarnings .re-submetric-note { font-size:10.5px; line-height:1.6; }
#sariRiderEarnings .re-grid { gap:18px; }
#sariRiderEarnings .re-performance-stack { gap:18px; }
#sariRiderEarnings .re-section-card { padding:20px 21px; }
#sariRiderEarnings .re-panel-header { margin-bottom:17px; }
#courierMain #sariRiderEarnings .re-section-title,
#courierPageContent #sariRiderEarnings .re-section-title {
    color:#2f3742;
    font-size:17px;
    font-weight:750;
    line-height:1.4;
    letter-spacing:-.025em;
}
#sariRiderEarnings .re-section-description { color:#8b96a4; font-size:11.5px; line-height:1.65; }
#sariRiderEarnings .re-tag {
    background:#fff8eb;
    border:1px solid #efdfb8;
    color:#9a6a12;
    font-size:10px;
    font-weight:700;
}
#sariRiderEarnings .re-chart-value { color:#7a8797; font-size:10px; font-weight:600; }
#sariRiderEarnings .re-chart-bar {
    background:linear-gradient(180deg,#dfb35a 0%, #c88912 100%);
}
#sariRiderEarnings .re-chart-col:hover .re-chart-bar {
    background:linear-gradient(180deg,#e7be69 0%, #b77b0d 100%);
}
#sariRiderEarnings .re-chart-bar.is-zero {
    background:#e7edf4;
}
#sariRiderEarnings .re-chart-day { color:#7f8a98; font-size:10.5px; }
#sariRiderEarnings .re-balance { background:#f8fafc; border-color:#e5e7eb; border-radius:13px; padding:16px 17px; }
#sariRiderEarnings .re-balance-label { color:#657487; font-size:11.5px; font-weight:600; }
#sariRiderEarnings .re-balance-number { color:#243348; font-size:clamp(25px,2.35vw,30px); font-weight:650; line-height:1.18; }
#sariRiderEarnings .re-balance-note { font-size:10.5px; line-height:1.65; }
#sariRiderEarnings .re-form { margin-top:18px; gap:12px; }
#sariRiderEarnings .re-field-label { color:#465363; font-size:11.5px; font-weight:700; }
#sariRiderEarnings .re-field-input {
    min-height:45px;
    padding:10px 13px;
    border:1px solid #d7dee7;
    border-radius:11px;
    color:#2e3948;
    font-size:11.5px;
}
#sariRiderEarnings .re-field-input:focus,
#sariRiderEarnings .re-field-input:focus-visible {
    background:#fff;
    border-color:var(--re-gold);
    outline:none;
    box-shadow:none;
}
#sariRiderEarnings .re-payout-title { color:#344357; font-size:12.5px; font-weight:750; }
#sariRiderEarnings .re-payout-entry { font-size:11.5px; }
#sariRiderEarnings .re-payout-entry strong { font-weight:700; }
#sariRiderEarnings .re-recent-heading {
    padding:19px 21px 16px;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
}
#sariRiderEarnings .re-recent-head-copy { min-width:0; }
#sariRiderEarnings .re-recent-more {
    display:inline-flex;
    align-items:center;
    gap:7px;
    min-height:36px;
    padding:8px 12px;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#fff;
    color:#475569;
    font-size:11px;
    font-weight:700;
    cursor:pointer;
    transition:background-color .16s ease,border-color .16s ease,color .16s ease;
}
#sariRiderEarnings .re-recent-more:hover {
    background:#f8fafc;
    border-color:#cbd5e1;
    color:#293440;
}
#sariRiderEarnings .re-recent-more svg {
    width:14px;
    height:14px;
    stroke-width:2;
    transition:transform .16s ease;
}
#sariRiderEarnings .re-recent-more[aria-expanded="true"] svg {
    transform:rotate(180deg);
}
#sariRiderEarnings .re-recent-row { padding:15px 21px; gap:10px 16px; }
#sariRiderEarnings .re-recent-order { color:#293440; font-size:11.5px; font-weight:750; }
#sariRiderEarnings .re-recent-date { color:#8b96a4; font-size:10.5px; }
#sariRiderEarnings .re-recent-type { font-size:11px; font-weight:600; }
#sariRiderEarnings .re-recent-status { border-color:#e5e7eb; background:#f8fafc; font-size:10px; font-weight:650; }
#sariRiderEarnings .re-recent-amount { color:#293440; font-size:14px; font-weight:750; }
@media (max-width:767px) {
    #courierMain > main:has(#sariRiderEarnings),
    #courierPageContent:has(#sariRiderEarnings) { padding:16px 14px 28px !important; }
    #courierMain #sariRiderEarnings .re-page-title,
    #courierPageContent #sariRiderEarnings .re-page-title { font-size:25px; }
    #sariRiderEarnings .re-eyebrow { font-size:9.5px; }
    #sariRiderEarnings .re-metric { min-height:106px; padding:13px 14px; }
    #sariRiderEarnings .re-metric-number { font-size:25px; }
    #sariRiderEarnings .re-section-card { padding:18px; }
    #sariRiderEarnings .re-recent-heading { padding:18px; }
    #sariRiderEarnings .re-recent-row { padding:14px 18px; }
}
@media (max-width:650px) {
    #sariRiderEarnings .re-metric { min-height:106px; padding:13px 14px; }
    #sariRiderEarnings .re-metric-number { font-size:24px; }
    #sariRiderEarnings .re-submetric { padding:15px 17px; }
    #sariRiderEarnings .re-section-card { padding:18px; }
    #sariRiderEarnings .re-intro { font-size:11.5px; }
}
@media (max-width:380px) {
    #sariRiderEarnings .re-metric-number { font-size:22px; }
}

@endverbatim

</style>

@endpush



@section('content')

<div id="sariRiderEarnings">

    @if(session('success'))

        <div class="re-alert re-alert-success" role="status">{{ session('success') }}</div>

    @endif

    @if($errors->any())

        <div class="re-alert re-alert-error" role="alert">{{ $errors->first() }}</div>

    @endif



    <header class="re-head">

        <div>

            <p class="re-eyebrow">Rider operations / Finance</p>

            <h1 class="re-page-title">Rider <span class="re-title-accent">Earnings</span></h1>


        </div>

        <div class="re-actions">

            <a href="{{ route('courier.remittances.index') }}" class="re-btn">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16v12H4zM8 7V5h8v2M7 13h10M7 16h6"/></svg>

                COD Remittance

            </a>

            <a href="{{ route('courier.earnings.statement') }}" class="re-btn re-btn-gold">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5M4 17v3h16v-3"/></svg>

                Download Statement

            </a>

        </div>

    </header>



    <section class="re-metrics" aria-label="Earnings overview">

        @foreach([

            ['label'=>'Today','amount'=>$earnings['today'],'icon'=>'calendar'],

            ['label'=>'This Week','amount'=>$earnings['week'],'icon'=>'trend'],

            ['label'=>'This Month','amount'=>$earnings['month'],'icon'=>'calendar'],

            ['label'=>'Potential','amount'=>$earnings['pending'],'icon'=>'clock'],

            ['label'=>'Available','amount'=>$earnings['available'],'icon'=>'wallet'],

        ] as $metric)

            <article class="re-card re-metric {{ $metric['label'] === 'Available' ? 're-metric-feature' : '' }}">

                <div class="re-metric-top">

                    <p class="re-metric-label">{{ $metric['label'] }}</p>

                    <span class="re-metric-icon" aria-hidden="true">

                        @if($metric['icon'] === 'wallet')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M16 12h5M4 6V4h14v2"/></svg>

                        @elseif($metric['icon'] === 'trend')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m3 17 6-6 4 4 8-9M16 6h5v5"/></svg>

                        @elseif($metric['icon'] === 'clock')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>

                        @else

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg>

                        @endif

                    </span>

                </div>

                <p class="re-metric-number">₱{{ number_format((float)$metric['amount'], 2) }}</p>

            </article>

        @endforeach

    </section>



    <p class="re-help">

        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>

        <span><strong>About potential earnings:</strong> Active direct-delivery and last-mile work is not yet payable. Earnings become available after successful delivery and finance recognition.</span>

    </p>



    <section class="re-submetrics" aria-label="Ledger balance details">

        <article class="re-card re-submetric">

            <p class="re-submetric-label">Awaiting COD Remittance</p>

            <p class="re-submetric-number">₱{{ number_format($earnings['awaiting_remittance'], 2) }}</p>

            <p class="re-submetric-note">Earned but locked until rider cash remittance is verified.</p>

        </article>

        <article class="re-card re-submetric">

            <p class="re-submetric-label">Reserved for Payout</p>

            <p class="re-submetric-number">₱{{ number_format($earnings['reserved'], 2) }}</p>

            <p class="re-submetric-note">Already included in a withdrawal request.</p>

        </article>

        <article class="re-card re-submetric">

            <p class="re-submetric-label">Lifetime Paid Out</p>

            <p class="re-submetric-number">₱{{ number_format($earnings['paid'], 2) }}</p>

            <p class="re-submetric-note">Confirmed external payouts.</p>

        </article>

    </section>



    <section class="re-grid" aria-label="Performance, earnings history, and withdrawals">

        <div class="re-performance-stack">

        <article class="re-card re-section-card">

            <div class="re-panel-header">

                <div>

                    <h3 class="re-section-title">Weekly Performance</h3>

                    <p class="re-section-description">Recognized rider earnings for the current week.</p>

                </div>

                <span class="re-tag">{{ $weeklyChange === null ? 'No prior week' : (($weeklyChange >= 0 ? '+' : '').number_format($weeklyChange, 1).'%') }}</span>

            </div>

            @php $maxAmount = max(1, max(array_column($daily, 'amount') ?: [0])); @endphp

            <div class="re-chart" role="img" aria-label="Weekly earnings chart. Exact amounts appear above each bar.">

                @foreach($daily as $day)

                    @php

                        $dayAmount = max(0, (float) ($day['amount'] ?? 0));

                        $height = $dayAmount > 0 ? max(6, round(($dayAmount / $maxAmount) * 170)) : 3;

                    @endphp

                    <div class="re-chart-col">

                        <div class="re-chart-value">₱{{ number_format($dayAmount, 2) }}</div>

                        <div class="re-chart-bar-track"><span class="re-chart-bar {{ $dayAmount <= 0 ? 'is-zero' : '' }}" style="height:{{ $height }}px" aria-hidden="true"></span></div>

                        <div class="re-chart-day">{{ $day['day'] }}</div>

                    </div>

                @endforeach

            </div>

        </article>



        <section class="re-card re-recent-card" aria-labelledby="reRecentEarningsTitle">

            <header class="re-recent-heading">

                <div class="re-recent-head-copy">
                    <h3 id="reRecentEarningsTitle" class="re-section-title">Recent Earnings</h3>

                    <p class="re-section-description">Delivery fees recognized for this rider.</p>
                </div>

                @if(count($transactions) > 3)
                    <button type="button" class="re-recent-more" data-recent-more-toggle aria-expanded="false" aria-controls="reRecentEarningsList">
                        <span data-recent-more-label>See more</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                @endif

            </header>

            <ul class="re-recent-list" id="reRecentEarningsList" aria-label="Recent earnings transactions">

                @forelse($transactions as $transaction)

                    <li class="re-recent-row" @if($loop->index >= 3) data-recent-extra hidden @endif>

                        <div class="re-recent-primary">

                            <span class="re-recent-order">{{ $transaction['order'] }}</span>

                            <span class="re-recent-date">{{ $transaction['date'] }}</span>

                        </div>

                        <div class="re-recent-secondary">

                            <span class="re-recent-type">{{ $transaction['type'] }}</span>

                            <span class="re-recent-status">{{ $transaction['status'] }}</span>

                        </div>

                        <strong class="re-recent-amount">+₱{{ number_format($transaction['amount'], 2) }}</strong>

                    </li>

                @empty

                    <li class="re-recent-empty">No recognized rider earnings yet.</li>

                @endforelse

            </ul>

        </section>

        </div>



        <aside class="re-card re-section-card" aria-label="Payout summary">

            <div class="re-panel-header">

                <div>

                    <h3 class="re-section-title">Payout Summary</h3>

                    <p class="re-section-description">Request a withdrawal from your available ledger balance.</p>

                </div>

            </div>

            <div class="re-balance">

                <p class="re-balance-label">Available Balance</p>

                <p class="re-balance-number">₱{{ number_format($earnings['available'], 2) }}</p>

                <p class="re-balance-note">This amount is available in your SARI rider ledger, not yet an external payout.</p>

            </div>

            <form method="POST" action="{{ route('courier.earnings.payout') }}" class="re-form">

                @csrf

                <label class="re-field"><span class="re-field-label">Withdrawal amount (₱)</span><input class="re-field-input" name="amount" type="number" min="1" step="0.01" max="{{ $earnings['available'] }}" value="{{ old('amount') }}" placeholder="Enter amount" required></label>

                <label class="re-field"><span class="re-field-label">Payout method</span><select class="re-field-input" name="payout_method" required><option value="gcash" @selected(old('payout_method') === 'gcash')>GCash</option><option value="maya" @selected(old('payout_method') === 'maya')>Maya</option><option value="bank" @selected(old('payout_method') === 'bank')>Bank Transfer</option><option value="manual_cash" @selected(old('payout_method') === 'manual_cash')>Manual Cash Release</option></select></label>

                <label class="re-field"><span class="re-field-label">Account name</span><input class="re-field-input" name="account_name" type="text" minlength="2" maxlength="120" value="{{ old('account_name') }}" placeholder="Account holder name" required></label>

                <label class="re-field"><span class="re-field-label">Mobile number / bank account</span><input class="re-field-input" name="account_reference" type="text" minlength="4" maxlength="120" value="{{ old('account_reference') }}" placeholder="Enter payout account" required></label>

                <button class="re-btn re-btn-gold" type="submit" @disabled($earnings['available'] <= 0)>Request Withdrawal <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>

            </form>

            <div class="re-payout-history">

                <h4 class="re-payout-title">Recent Payout Requests</h4>

                @forelse($payoutRequests as $payout)

                    <div class="re-payout-entry"><strong>₱{{ number_format((float)$payout->amount, 2) }}</strong><span>{{ strtoupper($payout->status) }}</span></div>

                @empty

                    <div class="re-payout-empty">No payout requests yet.</div>

                @endforelse

            </div>

        </aside>

    </section>





</div>

@endsection



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('sariRiderEarnings');
    if (!root) return;

    const toggle = root.querySelector('[data-recent-more-toggle]');
    if (toggle) {
        const label = toggle.querySelector('[data-recent-more-label]');
        const extras = [...root.querySelectorAll('[data-recent-extra]')];
        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            const next = !expanded;
            toggle.setAttribute('aria-expanded', next ? 'true' : 'false');
            if (label) label.textContent = next ? 'See less' : 'See more';
            extras.forEach(item => { item.hidden = !next; });
        });
    }
});
</script>
@endpush
