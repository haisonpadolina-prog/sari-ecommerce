@extends('layouts.courier')



@section('title', 'Pickup Assignments')

@section('header-title', 'Pickup Assignments')

@section('header-subtitle', 'Dispatched by SARI Logistics')



@push('styles')

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

@verbatim

/* SARI Rider — Pickup Assignments / modern rider UI

   View-scoped presentation only: routes, controllers and job progression are untouched. */

body:has(#pickupAssignmentsPage),

body:has(#pickupAssignmentsPage) #courierMain { background: #F4F5F7 !important; color: #1d2939; }

body:has(#pickupAssignmentsPage) #courierSidebar { background:#fff !important; border-right:1px solid #e9edf2 !important; width:244px; box-shadow:none; }

body:has(#pickupAssignmentsPage) #courierSidebar > div:first-child { min-height:78px !important; border-color:#edf0f4 !important; }

body:has(#pickupAssignmentsPage) #courierSidebarLogo { min-height:78px; }

body:has(#pickupAssignmentsPage) #courierSidebarLogo img { width:112px; }

body:has(#pickupAssignmentsPage) #courierSidebarNav { padding:13px 12px 16px; }

body:has(#pickupAssignmentsPage) #courierSidebarNav .courier-sidebar-section { font-size:8px; font-weight:500; letter-spacing:.12em; color:#98a2b3; }

body:has(#pickupAssignmentsPage) #courierSidebarNav [data-courier-sidebar-item] { min-height:41px; border-radius:10px; padding:10px 12px; font-size:11px; font-weight:400; box-shadow:none; gap:11px; }

body:has(#pickupAssignmentsPage) #courierSidebarNav [data-courier-sidebar-item] svg { width:17px; height:17px; stroke-width:1.7; }

body:has(#pickupAssignmentsPage) #courierSidebarNav a[title="Pickup Assignments"] { background:#f3f6fa !important; color:#192638 !important; font-weight:500 !important; position:relative; }

body:has(#pickupAssignmentsPage) #courierSidebarNav a[title="Pickup Assignments"]::before { content:''; position:absolute; width:3px; height:25px; left:-12px; top:50%; transform:translateY(-50%); background:#d99a0b; border-radius:0 3px 3px 0; }

body:has(#pickupAssignmentsPage) #courierSidebarProfile { background:#fff !important; border-color:#e9edf2 !important; }

body:has(#pickupAssignmentsPage) #courierSidebarProfile > a { border-color:#e9edf2; box-shadow:none; }

body:has(#pickupAssignmentsPage) #courierMain > header { background:rgba(255,255,255,.97) !important; border-bottom:1px solid #e9edf2 !important; box-shadow:none !important; }

body:has(#pickupAssignmentsPage) #courierMain > header > div:first-child { min-height:70px; padding-inline:clamp(16px,2vw,28px); }

body:has(#pickupAssignmentsPage) #courierMain > header input[type="search"] { border-color:#e8ecf1 !important; border-radius:12px !important; background:#f8fafc !important; font-weight:400; font-size:11px; }

body:has(#pickupAssignmentsPage) #courierMain > header button { box-shadow:none !important; border-color:transparent !important; background:transparent !important; }

body:has(#pickupAssignmentsPage) #courierMain > header h1 { font-weight:600 !important; font-size:18px; letter-spacing:-.03em; }

@media (min-width:1024px) {

  body:has(#pickupAssignmentsPage):not(.courier-sidebar-collapsed) #courierMain { padding-left:244px !important; }

  body:has(#pickupAssignmentsPage).courier-sidebar-collapsed #courierSidebar { width:92px; }

  body:has(#pickupAssignmentsPage).courier-sidebar-collapsed #courierMain { padding-left:92px !important; }

}

#courierMain > main:has(#pickupAssignmentsPage) { padding:24px clamp(16px,2vw,30px) 40px !important; background:transparent !important; }

#pickupAssignmentsPage {

  --pa-ink:#1d2939; --pa-muted:#7b8797; --pa-border:#e7ebf1;

  --pa-shadow:0 3px 12px rgba(26,39,57,.026), 0 12px 32px rgba(26,39,57,.052);

  width:100%; max-width:none; margin:0; padding:0; background:transparent;

  color:var(--pa-ink); font:400 12px/1.65 'Poppins',system-ui,sans-serif;

}

#pickupAssignmentsPage, #pickupAssignmentsPage *, #pickupAssignmentsPage *::before, #pickupAssignmentsPage *::after { box-sizing:border-box; }

#pickupAssignmentsPage a { text-decoration:none; }

#pickupAssignmentsPage :is(a,button,input):focus-visible { outline:2px solid #d99a0b; outline-offset:3px; }

#pickupAssignmentsPage button, #pickupAssignmentsPage input { font:inherit; }

#pickupAssignmentsPage .pa-alert { margin-bottom:16px; padding:13px 17px; border-radius:12px; font-size:11px; line-height:1.6; }

#pickupAssignmentsPage .pa-alert--error { background:#fff1f2; border:1px solid #facdd3; color:#a23c4c; }

#pickupAssignmentsPage .pa-alert--success { background:#edf9f1; border:1px solid #bde7cd; color:#24734f; }

#pickupAssignmentsPage .pa-top { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; }

#pickupAssignmentsPage .pa-kicker { display:flex; align-items:center; gap:8px; margin:0 0 7px; color:#929eac; font-size:10px; font-weight:400; }

#pickupAssignmentsPage .pa-kicker i { display:block; width:6px; height:6px; border-radius:50%; background:#d69c20; }

#courierMain #pickupAssignmentsPage .pa-title { margin:0; color:#142235; font-size:clamp(21px,2vw,27px); line-height:1.3; font-weight:600; letter-spacing:-.045em; }

#pickupAssignmentsPage .pa-description { margin:7px 0 0; max-width:680px; color:#8491a1; font-size:11px; font-weight:400; line-height:1.7; }

#pickupAssignmentsPage .pa-top-actions { display:flex; flex-wrap:wrap; gap:9px; padding-top:4px; }

#pickupAssignmentsPage .pa-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:40px; padding:10px 15px; border:1px solid #e4e9ef; border-radius:10px; background:#fff; color:#39485b; font-size:10.5px; font-weight:500; line-height:1.4; white-space:nowrap; transition:background-color .18s ease,border-color .18s ease,transform .18s ease; cursor:pointer; }

#pickupAssignmentsPage .pa-btn:hover { background:#f8fafc; border-color:#ced8e4; }

#pickupAssignmentsPage .pa-btn svg { width:16px; height:16px; flex:none; stroke-width:1.8; }

#pickupAssignmentsPage .pa-btn--dark { background:#192231; border-color:#192231; color:#fff; }

#pickupAssignmentsPage .pa-btn--dark:hover { background:#2f3c4e; border-color:#2f3c4e; }

#pickupAssignmentsPage .pa-kpis { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:15px; margin-bottom:17px; }

#pickupAssignmentsPage .pa-kpi, #pickupAssignmentsPage .pa-card, #pickupAssignmentsPage .pa-job, #pickupAssignmentsPage .pa-empty {

  background:#fff; border:1px solid rgba(231,236,243,.92); border-radius:15px; box-shadow:var(--pa-shadow);

}

#pickupAssignmentsPage .pa-kpi { display:flex; align-items:center; gap:14px; min-width:0; min-height:98px; padding:17px 20px; }

#pickupAssignmentsPage .pa-kpi-icon { display:flex; align-items:center; justify-content:center; width:30px; height:31px; flex:0 0 30px; color:#4882e9; background:transparent; border:0; box-shadow:none; }

#pickupAssignmentsPage .pa-kpi:nth-child(2) .pa-kpi-icon { color:#17a575; }

#pickupAssignmentsPage .pa-kpi:nth-child(3) .pa-kpi-icon { color:#805ced; }

#pickupAssignmentsPage .pa-kpi:nth-child(4) .pa-kpi-icon { color:#d4a021; }

#pickupAssignmentsPage .pa-kpi-icon svg { width:24px; height:24px; stroke-width:1.7; }

#pickupAssignmentsPage .pa-kpi-copy { min-width:0; }

#pickupAssignmentsPage .pa-kpi-label { margin:0; color:#7c899b; font-size:11px; font-weight:400; }

#pickupAssignmentsPage .pa-kpi-number { display:block; margin-top:4px; color:#152136; font-size:24px; line-height:1.25; letter-spacing:-.04em; font-weight:600; font-variant-numeric:tabular-nums; }

#pickupAssignmentsPage .pa-kpi-note { display:block; margin-top:3px; font-size:9px; font-weight:300; color:#9ca6b4; }

#pickupAssignmentsPage .pa-card { overflow:hidden; }

#pickupAssignmentsPage .pa-active { position:relative; display:grid; grid-template-columns:minmax(0,1fr) auto; gap:24px; padding:24px 25px; margin-bottom:20px; }

#pickupAssignmentsPage .pa-active::before { content:''; position:absolute; width:3px; top:22px; bottom:22px; left:0; background:#4b83ef; border-radius:0 3px 3px 0; }

#pickupAssignmentsPage .pa-active-heading { display:flex; flex-wrap:wrap; gap:9px; align-items:center; margin-bottom:12px; }

#pickupAssignmentsPage .pa-eyebrow { color:#8995a4; font-size:10px; font-weight:500; letter-spacing:.025em; }

#pickupAssignmentsPage .pa-pill { display:inline-flex; align-items:center; gap:6px; min-height:24px; border-radius:99px; padding:4px 10px; background:#e9f1ff; color:#3575e0; font-size:9px; line-height:1.2; font-weight:500; }

#pickupAssignmentsPage .pa-pill::before { content:''; width:5px; height:5px; background:currentColor; border-radius:50%; opacity:.75; }

#pickupAssignmentsPage .pa-pill--green { background:#e6f7ef; color:#178659; }

#pickupAssignmentsPage .pa-pill--amber { background:#fff4e1; color:#a7771a; }

#courierMain #pickupAssignmentsPage .pa-active-title { margin:0; color:#1d2b3b; font-weight:600; font-size:17px; letter-spacing:-.026em; overflow-wrap:anywhere; }

#pickupAssignmentsPage .pa-active-sub { margin:4px 0 0; color:#8693a4; font-size:10px; font-weight:400; }

#pickupAssignmentsPage .pa-active-route { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:16px 0 0; color:#526276; font-size:11px; font-weight:400; }

#pickupAssignmentsPage .pa-active-route b { color:#29384b; font-weight:500; }

#pickupAssignmentsPage .pa-active-route svg { width:15px; height:15px; flex:none; color:#b0bbc9; }

#pickupAssignmentsPage .pa-active-note { margin:12px 0 0; color:#718096; font-size:10px; line-height:1.75; font-weight:400; }

#pickupAssignmentsPage .pa-active-action { display:flex; align-items:center; justify-content:flex-end; }

#pickupAssignmentsPage .pa-section-heading { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:15px; margin:24px 1px 15px; }

#courierMain #pickupAssignmentsPage .pa-section-heading h3 { color:#1e293b; font-size:15px; font-weight:600; line-height:1.4; margin:0; letter-spacing:-.025em; }

#pickupAssignmentsPage .pa-section-heading p { margin:4px 0 0; color:#96a1b0; font-size:10.5px; font-weight:400; }

#pickupAssignmentsPage .pa-section-count { background:#fff; border:1px solid #e8edf3; border-radius:99px; min-width:30px; padding:5px 11px; font-size:10px; color:#718095; font-weight:500; }

#pickupAssignmentsPage .pa-toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:15px; }

#pickupAssignmentsPage .pa-search { display:flex; gap:10px; align-items:center; min-width:240px; max-width:460px; height:43px; flex:1; padding:0 13px; background:#fff; border:1px solid #e5eaf0; border-radius:11px; color:#91a0b1; box-shadow:0 2px 7px rgba(26,39,57,.018); }

#pickupAssignmentsPage .pa-search:focus-within { border-color:#a7c2ef; box-shadow:0 0 0 3px rgba(64,121,220,.08); }

#pickupAssignmentsPage .pa-search svg { width:16px; height:16px; flex:none; stroke-width:1.8; }

#pickupAssignmentsPage .pa-search input { width:100%; min-width:0; border:0; outline:0; box-shadow:none; background:transparent; color:#344054; font-size:10.5px; font-weight:400; }

#pickupAssignmentsPage .pa-search input::placeholder { color:#a5afbb; }

#pickupAssignmentsPage .pa-search input:focus-visible { outline:0; }

#pickupAssignmentsPage .pa-filters { display:flex; gap:5px; flex-wrap:wrap; }

#pickupAssignmentsPage .pa-filter { padding:9px 12px; background:transparent; border:1px solid transparent; border-radius:9px; color:#7d899a; font-size:10px; font-weight:400; cursor:pointer; transition:background .15s ease,color .15s ease; }

#pickupAssignmentsPage .pa-filter:hover { color:#344054; background:#fff; }

#pickupAssignmentsPage .pa-filter.is-active { color:#26384c; background:#fff; border-color:#e5eaf0; box-shadow:0 2px 8px rgba(26,39,57,.03); font-weight:500; }

#pickupAssignmentsPage .pa-job-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px; }

#pickupAssignmentsPage .pa-job { min-width:0; padding:22px; transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease; }

#pickupAssignmentsPage .pa-job:hover { transform:translateY(-2px); border-color:#d3dfed; box-shadow:0 6px 18px rgba(26,39,57,.045),0 18px 38px rgba(26,39,57,.07); }

#pickupAssignmentsPage .pa-job-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:10px; }

#pickupAssignmentsPage .pa-job-type { display:flex; align-items:center; gap:7px; font-size:9px; color:#8a97a6; font-weight:400; }

#pickupAssignmentsPage .pa-job-type svg { width:16px; height:16px; stroke-width:1.7; color:#b18a3b; }

#courierMain #pickupAssignmentsPage .pa-job-id { margin:9px 0 0; font-size:14px; letter-spacing:-.02em; font-weight:600; color:#1c293a; overflow-wrap:anywhere; }

#pickupAssignmentsPage .pa-seller-name { margin:4px 0 0; color:#8390a1; font-size:10px; }

#pickupAssignmentsPage .pa-timeline { display:grid; gap:0; margin:22px 0 18px; }

#pickupAssignmentsPage .pa-stop { display:grid; grid-template-columns:17px minmax(0,1fr); gap:12px; }

#pickupAssignmentsPage .pa-stop-rail { display:flex; align-items:center; flex-direction:column; padding-top:5px; }

#pickupAssignmentsPage .pa-stop-dot { width:9px; height:9px; border-radius:50%; background:#3f83ea; box-shadow:0 0 0 3px #eef4ff; flex:none; }

#pickupAssignmentsPage .pa-stop:last-child .pa-stop-dot { background:#1da773; box-shadow:0 0 0 3px #e7f7ee; }

#pickupAssignmentsPage .pa-stop-line { width:1px; min-height:40px; flex:1; margin:6px 0; background:#dfe6ee; }

#pickupAssignmentsPage .pa-stop-body { padding:0 0 17px; min-width:0; }

#pickupAssignmentsPage .pa-stop:last-child .pa-stop-body { padding-bottom:0; }

#pickupAssignmentsPage .pa-stop-label { color:#98a3b1; font-size:9px; font-weight:400; }

#pickupAssignmentsPage .pa-stop-name { margin:3px 0 0; font-size:11px; color:#2d3b4e; font-weight:500; overflow-wrap:anywhere; }

#pickupAssignmentsPage .pa-stop-address { margin:3px 0 0; color:#8896a5; font-size:9.5px; font-weight:400; line-height:1.7; overflow-wrap:anywhere; }

#pickupAssignmentsPage .pa-delivery-note { display:flex; align-items:flex-start; gap:8px; padding:11px 12px; border-radius:10px; background:#f7f9fc; color:#778596; font-size:9px; line-height:1.7; }

#pickupAssignmentsPage .pa-delivery-note svg { width:15px; height:15px; flex:none; margin-top:1px; color:#6685b2; }

#pickupAssignmentsPage .pa-job-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:20px; padding-top:16px; border-top:1px solid #eef1f5; }

#pickupAssignmentsPage .pa-job-footer > span { font-size:9.5px; color:#96a1b0; }

#pickupAssignmentsPage .pa-job-footer .pa-btn { min-height:38px; padding:9px 13px; }

#pickupAssignmentsPage .pa-empty { display:flex; align-items:center; justify-content:center; flex-direction:column; gap:7px; min-height:238px; padding:32px 18px; text-align:center; }

#pickupAssignmentsPage .pa-empty > svg { width:40px; height:40px; color:#aeb9c8; stroke-width:1.4; margin-bottom:5px; }

#courierMain #pickupAssignmentsPage .pa-empty h4 { margin:0; color:#46566a; font-size:12px; font-weight:500; }

#pickupAssignmentsPage .pa-empty p { margin:0; max-width:370px; font-size:10px; color:#98a3b2; line-height:1.75; }

#pickupAssignmentsPage .pa-empty .pa-btn { margin-top:9px; }

#pickupAssignmentsPage [data-pickup-card][hidden], #pickupAssignmentsPage [data-filter-empty][hidden] { display:none !important; }

@media (max-width:1180px) {

  #pickupAssignmentsPage .pa-kpis { gap:11px; }

  #pickupAssignmentsPage .pa-kpi { padding:14px; }

  #pickupAssignmentsPage .pa-kpi-number { font-size:21px; }

  #pickupAssignmentsPage .pa-job-grid { grid-template-columns:minmax(0,1fr); }

}

@media (max-width:800px) {

  #pickupAssignmentsPage .pa-kpis { grid-template-columns:repeat(2,minmax(0,1fr)); }

  #pickupAssignmentsPage .pa-active { grid-template-columns:1fr; gap:16px; }

  #pickupAssignmentsPage .pa-active-action { justify-content:flex-start; }

  #pickupAssignmentsPage .pa-top { flex-direction:column; }

}

@media (max-width:500px) {

  #courierMain > main:has(#pickupAssignmentsPage) { padding:16px 12px 28px !important; }

  #pickupAssignmentsPage .pa-kpis { gap:9px; }

  #pickupAssignmentsPage .pa-kpi { gap:8px; min-height:82px; padding:12px; }

  #pickupAssignmentsPage .pa-kpi-icon { width:24px; flex-basis:24px; }

  #pickupAssignmentsPage .pa-kpi-icon svg { width:21px; height:21px; }

  #pickupAssignmentsPage .pa-kpi-number { font-size:19px; }

  #pickupAssignmentsPage .pa-kpi-label { font-size:9.5px; }

  #pickupAssignmentsPage .pa-active { padding:20px; }

  #pickupAssignmentsPage .pa-job { padding:18px; }

  #pickupAssignmentsPage .pa-search { flex-basis:100%; max-width:none; min-width:0; }

  #pickupAssignmentsPage .pa-filters { width:100%; }

  #pickupAssignmentsPage .pa-filter { flex:1; padding:9px 7px; }

  #pickupAssignmentsPage .pa-top-actions { width:100%; }

  #pickupAssignmentsPage .pa-top-actions .pa-btn { flex:1; }

}

@media(prefers-reduced-motion:reduce) {

  #pickupAssignmentsPage *, #pickupAssignmentsPage *::before, #pickupAssignmentsPage *::after { transition:none !important; animation:none !important; }

}

@endverbatim



@verbatim

/* Buyer-cart inspired refinement: readable text, neutral outlines, warm primary action. */

#pickupAssignmentsPage { font-size:13px; }

#courierMain #pickupAssignmentsPage .pa-title { font-size:clamp(26px,2.3vw,33px); }

#pickupAssignmentsPage .pa-description { font-size:13px; }

#pickupAssignmentsPage .pa-kicker { font-size:11px; }

#pickupAssignmentsPage .pa-kpi { min-height:116px; padding:21px 23px; }

#pickupAssignmentsPage .pa-kpi-label { font-size:13px; color:#667588; }

#pickupAssignmentsPage .pa-kpi-number { font-size:clamp(28px,2.5vw,36px); }

#pickupAssignmentsPage .pa-kpi-note { font-size:11px; color:#929dab; }

#pickupAssignmentsPage .pa-kpi-icon,

#pickupAssignmentsPage .pa-kpi:nth-child(2) .pa-kpi-icon,

#pickupAssignmentsPage .pa-kpi:nth-child(3) .pa-kpi-icon,

#pickupAssignmentsPage .pa-kpi:nth-child(4) .pa-kpi-icon { color:#8995a5; }

#pickupAssignmentsPage .pa-kpi-icon svg { stroke-width:1.6; }

#courierMain #pickupAssignmentsPage .pa-section-heading h3 { font-size:19px; }

#pickupAssignmentsPage .pa-section-heading p { font-size:12px; }

#pickupAssignmentsPage .pa-section-count { font-size:12px; }

#pickupAssignmentsPage .pa-job { padding:25px; }

#courierMain #pickupAssignmentsPage .pa-job-id { font-size:18px; }

#pickupAssignmentsPage .pa-seller-name,

#pickupAssignmentsPage .pa-stop-name { font-size:13px; }

#pickupAssignmentsPage .pa-stop-label,

#pickupAssignmentsPage .pa-job-type,

#pickupAssignmentsPage .pa-job-footer > span { font-size:11px; }

#pickupAssignmentsPage .pa-stop-address,

#pickupAssignmentsPage .pa-delivery-note { font-size:12px; }

#pickupAssignmentsPage .pa-search input,

#pickupAssignmentsPage .pa-filter { font-size:12px; }

#pickupAssignmentsPage .pa-btn { font-size:12px; min-height:43px; font-weight:500; }

#pickupAssignmentsPage .pa-btn--dark { background:#D7A223; border-color:#D7A223; color:#211B0C; }

#pickupAssignmentsPage .pa-btn--dark:hover { background:#E3B23E; border-color:#E3B23E; }

#pickupAssignmentsPage .pa-job-type svg,

#pickupAssignmentsPage .pa-delivery-note svg { color:#8793a3; }

#pickupAssignmentsPage .pa-stop-dot,

#pickupAssignmentsPage .pa-stop:last-child .pa-stop-dot { background:#9CA7B6; box-shadow:0 0 0 3px #F0F2F5; }

#pickupAssignmentsPage .pa-pill,

#pickupAssignmentsPage .pa-pill--green,

#pickupAssignmentsPage .pa-pill--amber { background:#F2F4F7; color:#667588; }

#pickupAssignmentsPage .pa-active::before { background:#C8A052; }

#pickupAssignmentsPage .pa-empty { min-height:280px; padding:42px 22px; }

#pickupAssignmentsPage .pa-empty > svg { width:48px; height:48px; color:#9AA5B3; stroke-width:1.5; }

#courierMain #pickupAssignmentsPage .pa-empty h4 { font-size:17px; color:#687688; font-weight:500; }

#pickupAssignmentsPage .pa-empty p { max-width:460px; font-size:13px; color:#8B97A6; }

@media (max-width:500px) {

 #pickupAssignmentsPage .pa-kpi { padding:15px; min-height:104px; }

 #pickupAssignmentsPage .pa-kpi-number { font-size:27px; }

 #pickupAssignmentsPage .pa-kpi-label { font-size:11px; }

 #pickupAssignmentsPage .pa-kpi-note { font-size:10px; }

 #pickupAssignmentsPage .pa-job { padding:19px; }

}


/* Pickup Assignments — visual alignment with Active Deliveries, Last-Mile and Rider Earnings.
   Scoped, presentation-only. Do not alter job filtering, dispatch polling or courier routes. */
#pickupAssignmentsPage {
  --pa-ink:#2f3742;
  --pa-muted:#7a8593;
  --pa-border:#e5e7eb;
  --pa-gold:#c88912;
  --pa-gold-hover:#b47b0e;
  --pa-shadow:0 2px 5px rgba(15,23,42,.045),
              0 10px 24px rgba(15,23,42,.075),
              0 24px 54px rgba(15,23,42,.095),
              inset 0 1px 0 rgba(255,255,255,.92);
  max-width:1400px;
  margin:0 auto;
  font:400 13px/1.65 'Poppins',ui-sans-serif,system-ui,sans-serif;
}
#courierMain > main:has(#pickupAssignmentsPage),
#courierPageContent:has(#pickupAssignmentsPage) {
  padding:22px clamp(14px,2.2vw,30px) 42px!important;
}
#pickupAssignmentsPage :is(a,button,input):focus-visible {
  outline:2px solid var(--pa-gold);
  outline-offset:3px;
}
#pickupAssignmentsPage .pa-top { align-items:flex-end; margin-bottom:18px; gap:13px 22px; }
#pickupAssignmentsPage .pa-kicker {
  display:block;
  margin:0 0 6px;
  color:var(--pa-gold);
  font-size:10.5px;
  font-weight:800;
  line-height:1.4;
  letter-spacing:.14em;
  text-transform:uppercase;
}
#pickupAssignmentsPage .pa-kicker i { display:none; }
#courierMain #pickupAssignmentsPage .pa-title,
#courierPageContent #pickupAssignmentsPage .pa-title {
  margin:0;
  font-size:clamp(28px,2.35vw,33px);
  font-weight:760;
  line-height:1.05;
  letter-spacing:-.04em;
  color:var(--pa-ink);
}
#pickupAssignmentsPage .pa-title-accent { color:var(--pa-gold); }
#pickupAssignmentsPage .pa-kpis { gap:14px; margin-bottom:18px; }
#pickupAssignmentsPage .pa-kpi,
#pickupAssignmentsPage .pa-card,
#pickupAssignmentsPage .pa-job,
#pickupAssignmentsPage .pa-empty {
  border:1px solid var(--pa-border);
  border-radius:18px;
  background:#fff;
  box-shadow:var(--pa-shadow);
}
#pickupAssignmentsPage .pa-kpi { min-height:110px; padding:14px 17px; gap:14px; }
#pickupAssignmentsPage .pa-kpi-icon { width:29px; height:29px; flex-basis:29px; color:var(--pa-gold); }
#pickupAssignmentsPage .pa-kpi:nth-child(1) .pa-kpi-icon { color:var(--pa-gold); }
#pickupAssignmentsPage .pa-kpi:nth-child(2) .pa-kpi-icon { color:#3865c7; }
#pickupAssignmentsPage .pa-kpi:nth-child(3) .pa-kpi-icon { color:#0f8a6b; }
#pickupAssignmentsPage .pa-kpi:nth-child(4) .pa-kpi-icon { color:#8b5cf6; }
#pickupAssignmentsPage .pa-kpi-icon svg { width:25px; height:25px; stroke-width:1.8; }
#pickupAssignmentsPage .pa-kpi-label { color:#5f6d7c; font-size:12.5px; font-weight:600; line-height:1.4; }
#pickupAssignmentsPage .pa-kpi-number {
  margin-top:5px;
  color:#1f2937;
  font-size:clamp(24px,1.95vw,29px);
  font-weight:650;
  letter-spacing:-.045em;
  line-height:1.12;
}
#pickupAssignmentsPage .pa-kpi-note { margin-top:4px; color:#929eab; font-size:10.5px; font-weight:400; line-height:1.5; }
#pickupAssignmentsPage .pa-active {
  gap:18px;
  margin-bottom:18px;
  padding:19px 22px;
}
#pickupAssignmentsPage .pa-active::before { background:var(--pa-gold); top:19px; bottom:19px; }
#pickupAssignmentsPage .pa-active-heading { margin-bottom:10px; }
#pickupAssignmentsPage .pa-eyebrow { color:#8b96a4; font-size:9.5px; font-weight:700; letter-spacing:.06em; }
#pickupAssignmentsPage .pa-pill { border:1px solid #e5e7eb; background:#f8fafc; color:#647384; font-size:10px; font-weight:650; }
#pickupAssignmentsPage .pa-pill--green { background:#effcf3; color:#18794e; border-color:#bde7c9; }
#pickupAssignmentsPage .pa-pill--amber { background:#f8fafc; color:#647384; border-color:#e5e7eb; }
#courierMain #pickupAssignmentsPage .pa-active-title,
#courierPageContent #pickupAssignmentsPage .pa-active-title { color:#293440; font-size:16px; font-weight:750; }
#pickupAssignmentsPage .pa-active-sub { font-size:11px; color:#8b96a4; }
#pickupAssignmentsPage .pa-active-route { margin-top:14px; color:#566579; font-size:11.5px; }
#pickupAssignmentsPage .pa-active-route b { font-weight:700; color:#344357; }
#pickupAssignmentsPage .pa-active-note { margin-top:10px; color:#647384; font-size:11px; }
#pickupAssignmentsPage .pa-btn { min-height:42px; padding:10px 15px; border-radius:12px; font-size:11.5px; font-weight:700; }
#pickupAssignmentsPage .pa-btn--dark { background:var(--pa-gold); border-color:var(--pa-gold); color:#fff; }
#pickupAssignmentsPage .pa-btn--dark:hover { background:var(--pa-gold-hover); border-color:var(--pa-gold-hover); color:#fff; }
#pickupAssignmentsPage .pa-btn--dark:focus-visible { outline-color:var(--pa-gold); }
#pickupAssignmentsPage .pa-section-heading { margin:20px 1px 13px; gap:10px 16px; }
#courierMain #pickupAssignmentsPage .pa-section-heading h3,
#courierPageContent #pickupAssignmentsPage .pa-section-heading h3 {
  color:#2f3742;
  font-size:17px;
  font-weight:750;
  line-height:1.4;
  letter-spacing:-.025em;
}
#pickupAssignmentsPage .pa-section-heading p { font-size:11.5px; color:#8b96a4; }
#pickupAssignmentsPage .pa-section-count { border-color:#e5e7eb; background:#f8fafc; color:#647384; font-size:10.5px; font-weight:650; }
#pickupAssignmentsPage .pa-toolbar { margin-bottom:14px; gap:12px; }
#pickupAssignmentsPage .pa-search { height:45px; max-width:480px; border:1px solid #dce2e8; border-radius:12px; color:#97a3b2; box-shadow:none; }
#pickupAssignmentsPage .pa-search:focus-within { border-color:var(--pa-gold); background:#fff; box-shadow:none; }
#pickupAssignmentsPage .pa-search input { font-size:11.5px; color:#344357; }
#pickupAssignmentsPage .pa-filter { font-size:11px; font-weight:650; border-radius:10px; min-height:39px; padding:9px 12px; color:#7b8794; }
#pickupAssignmentsPage .pa-filter:hover { background:#f1f3f5; color:#344357; }
#pickupAssignmentsPage .pa-filter.is-active { background:#fff; border-color:#d9b977; color:#9b6b14; box-shadow:none; font-weight:750; }
#pickupAssignmentsPage .pa-job-grid { gap:14px; }
#pickupAssignmentsPage .pa-job { padding:20px 21px; transition:background-color .16s ease,border-color .16s ease,box-shadow .16s ease; }
#pickupAssignmentsPage .pa-job:hover { transform:none; background:#fdfefe; border-color:#d5dbe4; box-shadow:var(--pa-shadow); }
#pickupAssignmentsPage .pa-job-type { color:#8b96a4; font-size:10px; font-weight:650; }
#pickupAssignmentsPage .pa-job-type svg { color:var(--pa-gold); }
#courierMain #pickupAssignmentsPage .pa-job-id,
#courierPageContent #pickupAssignmentsPage .pa-job-id { margin-top:7px; color:#293440; font-size:15px; font-weight:750; }
#pickupAssignmentsPage .pa-seller-name { color:#8190a0; font-size:11px; }
#pickupAssignmentsPage .pa-timeline { margin:17px 0 15px; }
#pickupAssignmentsPage .pa-stop-dot,
#pickupAssignmentsPage .pa-stop:last-child .pa-stop-dot { background:var(--pa-gold); box-shadow:0 0 0 3px #f1f3f5; }
#pickupAssignmentsPage .pa-stop:last-child .pa-stop-dot { background:#94a3b8; }
#pickupAssignmentsPage .pa-stop-line { background:#dce3eb; }
#pickupAssignmentsPage .pa-stop-label { color:#929eab; font-size:10px; font-weight:650; }
#pickupAssignmentsPage .pa-stop-name { color:#465363; font-size:12px; font-weight:650; }
#pickupAssignmentsPage .pa-stop-address { color:#8290a0; font-size:11px; line-height:1.6; }
#pickupAssignmentsPage .pa-delivery-note { padding:11px 13px; border:1px solid #e2e8f0; background:#f8fafc; color:#64748b; font-size:10.5px; line-height:1.65; }
#pickupAssignmentsPage .pa-delivery-note svg { color:var(--pa-gold); }
#pickupAssignmentsPage .pa-job-footer { margin-top:17px; padding-top:15px; border-color:#edf0f3; }
#pickupAssignmentsPage .pa-job-footer > span { color:#8b96a4; font-size:10.5px; }
#pickupAssignmentsPage .pa-job-footer .pa-btn { min-height:41px; font-size:11.5px; }
#pickupAssignmentsPage .pa-empty { min-height:265px; padding:40px 20px; }
#pickupAssignmentsPage .pa-empty > svg { color:#98a3ae; }
#courierMain #pickupAssignmentsPage .pa-empty h4,
#courierPageContent #pickupAssignmentsPage .pa-empty h4 { color:#546172; font-size:17px; font-weight:750; }
#pickupAssignmentsPage .pa-empty p { color:#8290a0; font-size:11.5px; }
@media (max-width:1180px) {
  #pickupAssignmentsPage .pa-job-grid { grid-template-columns:1fr; }
  #pickupAssignmentsPage .pa-kpi { padding:13px 14px; gap:12px; }
  #pickupAssignmentsPage .pa-kpi-number { font-size:25px; }
}
@media (max-width:800px) {
  #pickupAssignmentsPage .pa-kpis { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media (max-width:767px) {
  #courierMain > main:has(#pickupAssignmentsPage),
  #courierPageContent:has(#pickupAssignmentsPage) { padding:16px 14px 28px!important; }
  #courierMain #pickupAssignmentsPage .pa-title,
  #courierPageContent #pickupAssignmentsPage .pa-title { font-size:25px; }
  #pickupAssignmentsPage .pa-kicker { font-size:9.5px; }
  #pickupAssignmentsPage .pa-kpi { min-height:106px; padding:13px 14px; }
  #pickupAssignmentsPage .pa-kpi-number { font-size:25px; }
  #pickupAssignmentsPage .pa-active { padding:17px 18px; }
  #pickupAssignmentsPage .pa-job { padding:18px; }
}
@media (max-width:500px) {
  #courierMain > main:has(#pickupAssignmentsPage),
  #courierPageContent:has(#pickupAssignmentsPage) { padding:16px 12px 28px!important; }
  #pickupAssignmentsPage .pa-kpis { gap:9px; }
  #pickupAssignmentsPage .pa-kpi { min-height:102px; padding:12px 13px; gap:9px; }
  #pickupAssignmentsPage .pa-kpi-icon { width:25px; height:25px; flex-basis:25px; }
  #pickupAssignmentsPage .pa-kpi-icon svg { width:23px; height:23px; }
  #pickupAssignmentsPage .pa-kpi-label { font-size:11px; }
  #pickupAssignmentsPage .pa-kpi-number { font-size:24px; }
  #pickupAssignmentsPage .pa-kpi-note { font-size:10px; }
  #pickupAssignmentsPage .pa-job { padding:17px; }
  #pickupAssignmentsPage .pa-search { min-width:0; max-width:none; width:100%; }
  #pickupAssignmentsPage .pa-filters { gap:4px; }
  #pickupAssignmentsPage .pa-filter { padding:8px 7px; font-size:10.5px; }
}

@endverbatim

</style>

@endpush



@section('content')

@php

    // Counts are based only on the courier_accepted queue supplied by CourierDeliveryController::requests().

    $queuedCount = $orders->count();

    $logisticsCount = $orders->filter(fn ($order) => (bool) $order->logisticsParcel)->count();

    $directCount = $queuedCount - $logisticsCount;

    $activeIsLogistics = $activeOrder ? (bool) $activeOrder->logisticsParcel : false;

@endphp



<div id="pickupAssignmentsPage" data-sari-ui="PICKUP-ASSIGNMENTS-ALIGNED-V1">

    @if($errors->any())

        <div class="pa-alert pa-alert--error" role="alert">{{ $errors->first() }}</div>

    @endif

    @if(session('success'))

        <div class="pa-alert pa-alert--success" role="status">{{ session('success') }}</div>

    @endif



    <div class="pa-top">

        <div>

            <p class="pa-kicker">Rider Operations / Dispatch</p>

            <h1 class="pa-title">Pickup <span class="pa-title-accent">Assignments</span></h1>

            

        </div>
</div>



    <section class="pa-kpis" aria-label="Pickup assignment summary">

        <div class="pa-kpi">

            <span class="pa-kpi-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7 12 3l9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7M12 11v10"/></svg></span>

            <div class="pa-kpi-copy"><p class="pa-kpi-label">Pickup queue</p><strong class="pa-kpi-number">{{ number_format($queuedCount) }}</strong><span class="pa-kpi-note">Waiting assignments</span></div>

        </div>

        <div class="pa-kpi">

            <span class="pa-kpi-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l4 2"/></svg></span>

            <div class="pa-kpi-copy"><p class="pa-kpi-label">Active work</p><strong class="pa-kpi-number">{{ $activeOrder ? '1' : '0' }}</strong><span class="pa-kpi-note">Current job</span></div>

        </div>

        <div class="pa-kpi">

            <span class="pa-kpi-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 19h18M5 19V8l7-4 7 4v11M9 19v-5h6v5M9 10h.01M15 10h.01"/></svg></span>

            <div class="pa-kpi-copy"><p class="pa-kpi-label">Logistics pickups</p><strong class="pa-kpi-number">{{ number_format($logisticsCount) }}</strong><span class="pa-kpi-note">Origin hub handoff</span></div>

        </div>

        <div class="pa-kpi">

            <span class="pa-kpi-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h11v10H3zM14 10h3l4 4v3h-7"/><circle cx="7.5" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg></span>

            <div class="pa-kpi-copy"><p class="pa-kpi-label">Direct deliveries</p><strong class="pa-kpi-number">{{ number_format($directCount) }}</strong><span class="pa-kpi-note">Buyer handoff</span></div>

        </div>

    </section>



    @if($activeOrder)

        <section class="pa-card pa-active" aria-label="Current active pickup">

            <div>

                <div class="pa-active-heading">

                    <span class="pa-eyebrow">CURRENT ASSIGNMENT</span>

                    <span class="pa-pill pa-pill--green">{{ $activeOrder->statusLabel() }}</span>

                    <span class="pa-pill">{{ $activeIsLogistics ? 'Seller pickup' : 'Direct delivery' }}</span>

                </div>

                <h3 class="pa-active-title">{{ $activeOrder->order_number }}</h3>

                <p class="pa-active-sub">Your active workflow is ready to continue.</p>

                <p class="pa-active-route">

                    <b>{{ $activeOrder->pickup_name ?: 'Seller pickup' }}</b>

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>

                    <b>{{ $activeIsLogistics ? 'Origin hub' : ($activeOrder->buyer_name ?: 'Buyer') }}</b>

                </p>

                @if($activeIsLogistics && $activeOrder->status === 'in_transit')

                    <p class="pa-active-note">Pickup completed. Bring the parcel to the assigned origin hub for physical intake by SARI Logistics.</p>

                @endif

            </div>

            <div class="pa-active-action">

                <a class="pa-btn pa-btn--dark" href="{{ route('courier.delivery.show', ['order' => $activeOrder->id]) }}">

                    Continue workflow

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>

                </a>

            </div>

        </section>

    @endif



    <section aria-label="Assigned pickup queue">

        <div class="pa-section-heading">

            <div><h3>Assigned pickup queue</h3><p>Orders dispatched to you, ready for pickup and handoff.</p></div>

            <span class="pa-section-count">{{ $queuedCount }} {{ $queuedCount === 1 ? 'assignment' : 'assignments' }}</span>

        </div>



        @if($queuedCount > 0)

            <div class="pa-toolbar">

                <label class="pa-search">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4 4"/></svg>

                    <input type="search" data-pickup-search placeholder="Search orders, seller, or address..." aria-label="Search pickup assignments" autocomplete="off">

                </label>

                <div class="pa-filters" role="group" aria-label="Filter assignment type">

                    <button type="button" class="pa-filter is-active" data-pickup-filter="all" aria-pressed="true">All ({{ $queuedCount }})</button>

                    <button type="button" class="pa-filter" data-pickup-filter="logistics" aria-pressed="false">Seller pickup ({{ $logisticsCount }})</button>

                    <button type="button" class="pa-filter" data-pickup-filter="direct" aria-pressed="false">Direct ({{ $directCount }})</button>

                </div>

            </div>



            <div class="pa-job-grid" data-pickup-grid>

                @foreach($orders as $order)

                    @php $isLogisticsPickup = (bool) $order->logisticsParcel; @endphp

                    <article class="pa-job" data-pickup-card data-kind="{{ $isLogisticsPickup ? 'logistics' : 'direct' }}">

                        <div class="pa-job-head">

                            <div>

                                <p class="pa-job-type">

                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7 12 3l9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7M12 11v10"/></svg>

                                    {{ $isLogisticsPickup ? 'Seller pickup · Logistics' : 'Direct delivery' }}

                                </p>

                                <h4 class="pa-job-id">{{ $order->order_number }}</h4>

                                <p class="pa-seller-name">{{ $order->seller?->store_name ?: $order->pickup_name ?: 'SARI Seller' }}</p>

                            </div>

                            <span class="pa-pill pa-pill--amber">Pickup assigned</span>

                        </div>



                        <div class="pa-timeline" aria-label="Pickup and handoff details">

                            <div class="pa-stop">

                                <span class="pa-stop-rail"><span class="pa-stop-dot"></span><span class="pa-stop-line"></span></span>

                                <div class="pa-stop-body">

                                    <div class="pa-stop-label">Seller pickup</div>

                                    <div class="pa-stop-name">{{ $order->pickup_name ?: 'Seller' }}</div>

                                    <p class="pa-stop-address">{{ $order->pickup_address ?: 'Pickup address unavailable' }}</p>

                                </div>

                            </div>

                            <div class="pa-stop">

                                <span class="pa-stop-rail"><span class="pa-stop-dot"></span></span>

                                <div class="pa-stop-body">

                                    <div class="pa-stop-label">Next handoff</div>

                                    <div class="pa-stop-name">{{ $isLogisticsPickup ? 'Origin Sorting Hub' : ($order->buyer_name ?: 'Buyer') }}</div>

                                    <p class="pa-stop-address">{{ $isLogisticsPickup ? 'SARI Logistics intake' : ($order->buyer_address ?: 'Buyer delivery') }}</p>

                                </div>

                            </div>

                        </div>



                        @if($isLogisticsPickup)

                            <div class="pa-delivery-note">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>

                                <span>Pickup-only assignment. The marketplace delivery fee belongs to the final-mile rider, not the seller pickup rider.</span>

                            </div>

                        @endif



                        <div class="pa-job-footer">

                            <span>Assigned by SARI Logistics</span>

                            <a class="pa-btn pa-btn--dark" href="{{ route('courier.delivery.show', ['order' => $order->id]) }}">

                                Start pickup

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>

                            </a>

                        </div>

                    </article>

                @endforeach

            </div>



            <div class="pa-empty" data-filter-empty hidden role="status">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m15.5 15.5 5 5"/></svg>

                <h4>No matching assignments</h4>

                <p>Try another order number, seller, address or assignment type.</p>

                <button type="button" class="pa-btn" data-clear-filters>Clear filters</button>

            </div>

        @else

            <div class="pa-empty" role="status">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7 12 3l9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7M12 11v10"/></svg>

                <h4>All caught up!</h4>

                <p>No active seller pickups right now. New work will appear once Logistics assigns a pickup.</p>

                <a class="pa-btn" href="{{ route('courier.last-mile') }}">Check last-mile deliveries

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>

                </a>

            </div>

        @endif

    </section>

</div>

@endsection



@push('scripts')

<script>

(() => {

    'use strict';



    const root = document.getElementById('pickupAssignmentsPage');

    if (!root) return;



    // UI-only filtering: no backend actions, no mutation to courier assignments.

    const search = root.querySelector('[data-pickup-search]');

    const cards = Array.from(root.querySelectorAll('[data-pickup-card]'));

    const filterButtons = Array.from(root.querySelectorAll('[data-pickup-filter]'));

    const noMatches = root.querySelector('[data-filter-empty]');

    let activeFilter = 'all';



    const applyFilters = () => {

        const query = (search?.value ?? '').trim().toLocaleLowerCase();

        let visible = 0;

        cards.forEach(card => {

            const typeMatches = activeFilter === 'all' || card.dataset.kind === activeFilter;

            const textMatches = !query || card.textContent.toLocaleLowerCase().includes(query);

            card.hidden = !(typeMatches && textMatches);

            if (!card.hidden) visible++;

        });

        if (noMatches) noMatches.hidden = visible > 0;

    };



    filterButtons.forEach(button => button.addEventListener('click', () => {

        activeFilter = button.dataset.pickupFilter || 'all';

        filterButtons.forEach(item => {

            const selected = item === button;

            item.classList.toggle('is-active', selected);

            item.setAttribute('aria-pressed', selected ? 'true' : 'false');

        });

        applyFilters();

    }));

    search?.addEventListener('input', applyFilters);

    root.querySelector('[data-clear-filters]')?.addEventListener('click', () => {

        if (search) search.value = '';

        filterButtons.find(item => item.dataset.pickupFilter === 'all')?.click();

        search?.focus();

    });



    // Preserve the existing 3-second polling check for newly dispatched assignments.

    // The endpoint's assigned_count is ONLY the courier_accepted queue; do not include the active job.

    const currentCount = @json($queuedCount);

    const liveStateUrl = @json(route('courier.orders.live-state'));

    let checking = false;



    async function checkAssignments() {

        if (document.hidden || checking) return;

        checking = true;

        try {

            const response = await fetch(liveStateUrl, {

                headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },

                credentials:'same-origin', cache:'no-store'

            });

            if (!response.ok) return;

            const data = await response.json();

            if (Number.isFinite(Number(data.assigned_count)) && Number(data.assigned_count) !== Number(currentCount)) {

                window.location.reload();

            }

        } catch (error) {

            console.debug('Rider assignment polling temporarily unavailable.');

        } finally {

            checking = false;

        }

    }



    window.setInterval(checkAssignments, 3000);

})();

</script>

@endpush
