@extends('layouts.courier')



@section('title', 'Last-Mile Deliveries')

@section('header-title', 'Last-Mile Deliveries')

@section('header-subtitle', 'Destination Hub → Buyer')



@push('styles')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

@verbatim

/* Scoped to last-mile Rider page. Other Logistics and Rider views are not affected. */

body:has(#sariLastMileModern),body:has(#sariLastMileModern) #courierMain{background:#F4F5F7!important;color:#273447;}

body:has(#sariLastMileModern) #courierSidebar{background:#fff!important;border-right:1px solid #e8edf3!important;box-shadow:none!important;}

body:has(#sariLastMileModern) #courierSidebarProfile{background:#fff!important;border-color:#e8edf3!important;}

body:has(#sariLastMileModern) #courierMain>header{background:#fff!important;border-bottom:1px solid #e8edf3!important;box-shadow:none!important;}

body:has(#sariLastMileModern) #courierMain>main{background:transparent!important;padding:24px clamp(16px,2.3vw,32px) 42px!important;}

@media(min-width:1024px){body:has(#sariLastMileModern):not(.courier-sidebar-collapsed) #courierMain{padding-left:244px!important;}body:has(#sariLastMileModern):not(.courier-sidebar-collapsed) #courierSidebar{width:244px!important;}body:has(#sariLastMileModern).courier-sidebar-collapsed #courierMain{padding-left:92px!important;}}

@media(max-width:1023px){body:has(#sariLastMileModern) #courierMain{padding-left:0!important;}}

#sariLastMileModern{--lm-ink:#202D3F;--lm-muted:#7D8A9A;--lm-line:#E7EBF1;--lm-gold:#C98D19;--lm-shadow:0 5px 16px rgba(30,42,61,.035),0 16px 42px rgba(30,42,61,.068);width:100%;max-width:none;margin:0;font:400 13px/1.65 'Poppins',system-ui,sans-serif;color:var(--lm-ink);}

#sariLastMileModern *,#sariLastMileModern *::before,#sariLastMileModern *::after{box-sizing:border-box;}

#sariLastMileModern a{text-decoration:none;}

#sariLastMileModern a:focus-visible,#sariLastMileModern button:focus-visible{outline:2px solid #C98D19;outline-offset:3px;}

#sariLastMileModern .lm-top{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:22px;}

#sariLastMileModern .lm-kicker{color:#9AA5B3;font-size:11px;font-weight:500;margin:0 0 4px;}

#courierMain #sariLastMileModern .lm-title{font-weight:600;color:#1B293A;font-size:clamp(29px,2.55vw,38px);letter-spacing:-.045em;line-height:1.2;margin:0;}

#sariLastMileModern .lm-desc{margin:8px 0 0;color:#7B889A;font-size:13px;max-width:690px;}

#sariLastMileModern .lm-top-actions{display:flex;flex-wrap:wrap;gap:10px;justify-content:flex-end;}

#sariLastMileModern .lm-btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:44px;max-width:100%;padding:11px 17px;border:1px solid #DDE4EC;border-radius:11px;background:#fff;color:#506074;font-size:12px;font-weight:500;white-space:normal;text-align:center;cursor:pointer;transition:background .18s,border-color .18s,box-shadow .18s,transform .18s;}

#sariLastMileModern .lm-btn:hover{background:#F7F9FC;border-color:#C8D2DE;}

#sariLastMileModern .lm-btn-gold{color:#fff;background:#C98D19;border-color:#C98D19;box-shadow:0 6px 15px rgba(201,141,25,.14);}

#sariLastMileModern .lm-btn-gold:hover{background:#B67E10;border-color:#B67E10;transform:translateY(-1px);}

#sariLastMileModern .lm-btn svg{width:17px;height:17px;stroke-width:1.8;flex-shrink:0;}

#sariLastMileModern .lm-card{background:#fff;border:1px solid #E8ECF2;border-radius:17px;box-shadow:var(--lm-shadow);min-width:0;}

#sariLastMileModern .lm-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:18px;}

#sariLastMileModern .lm-stat{padding:19px 23px;display:flex;align-items:center;gap:16px;min-height:111px;}

#sariLastMileModern .lm-stat-icon{color:#9AA5B3;flex:0 0 32px;display:flex;justify-content:center;align-items:center;}

#sariLastMileModern .lm-stat-icon svg{width:26px;height:26px;fill:none;stroke:currentColor;stroke-width:1.55;stroke-linecap:round;stroke-linejoin:round;}

#sariLastMileModern .lm-stat-label{font-size:12px;color:#7D8B9D;margin:0;}

#sariLastMileModern .lm-stat-value{display:block;color:#1B293A;font-size:30px;line-height:1.2;letter-spacing:-.04em;font-weight:600;margin:3px 0;}

#sariLastMileModern .lm-stat-note{color:#9CA6B4;font-size:11px;margin:0;}

#sariLastMileModern .lm-steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0;margin-bottom:22px;padding:13px;}

#sariLastMileModern .lm-step{display:flex;align-items:center;gap:13px;padding:12px 16px;min-width:0;}

#sariLastMileModern .lm-step+.lm-step{border-left:1px solid #ECF0F4;}

#sariLastMileModern .lm-step-num{width:35px;height:35px;flex:0 0 35px;border:1px solid #DCE3EC;border-radius:10px;color:#8591A1;background:#F9FAFC;display:grid;place-items:center;font-size:13px;font-weight:500;}

#sariLastMileModern .lm-step strong{display:block;color:#2B394C;font-weight:500;font-size:13px;}

#sariLastMileModern .lm-step small{display:block;color:#9CA6B4;font-size:11px;}

#sariLastMileModern .lm-section-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin:16px 0 13px;}

#courierMain #sariLastMileModern .lm-section-head h3{font-size:19px;font-weight:600;margin:0;color:#263448;}

#sariLastMileModern .lm-section-head p{font-size:12px;color:#8A97A7;margin:4px 0 0;}

#sariLastMileModern .lm-section-count{color:#667589;font-size:12px;padding:6px 11px;border:1px solid #E4E9F0;border-radius:999px;background:#fff;white-space:nowrap;}

#sariLastMileModern .lm-batch{overflow:hidden;margin:0 0 18px;}

#sariLastMileModern .lm-batch-head{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;padding:23px 25px;border-bottom:1px solid #EDF0F4;}

#sariLastMileModern .lm-code{font-size:11px;font-weight:500;color:#8B97A6;letter-spacing:.015em;margin:0 0 6px;}

#courierMain #sariLastMileModern .lm-batch-title{font-size:20px;line-height:1.35;font-weight:600;color:#1D2C3F;margin:0;letter-spacing:-.025em;overflow-wrap:anywhere;}

#sariLastMileModern .lm-batch-sub{font-size:12px;margin:6px 0 0;color:#8B97A6;}

#sariLastMileModern .lm-chip{display:inline-flex;align-items:center;gap:6px;background:#F2F4F7;color:#657488;border-radius:999px;padding:6px 11px;font-size:11px;font-weight:500;text-transform:capitalize;}

#sariLastMileModern .lm-chip::before{content:'';width:7px;height:7px;border-radius:50%;background:#9DA9B7;}

#sariLastMileModern .lm-chip--active{background:#FDF5E7;color:#9A721E;}

#sariLastMileModern .lm-chip--active::before{background:#C98D19;}

#sariLastMileModern .lm-chip--done{background:#EEF8F2;color:#28764F;}

#sariLastMileModern .lm-chip--done::before{background:#2AA875;}

#sariLastMileModern .lm-map-section{padding:22px 24px 20px;border-bottom:1px solid #EDF0F4;}

#sariLastMileModern .lm-map-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:13px;}

#courierMain #sariLastMileModern .lm-map-head h4{font-size:16px;font-weight:600;color:#29384B;margin:0;}

#sariLastMileModern .lm-map-head p{margin:3px 0 0;font-size:12px;color:#8D98A7;}

#sariLastMileModern .sari-batch-map{height:360px;min-height:360px;border-radius:12px;background:#E7EDF2;overflow:hidden;}

#sariLastMileModern .sari-batch-map.leaflet-container{font-family:Poppins,system-ui,sans-serif;}

#sariLastMileModern .sari-batch-map .leaflet-control-attribution{font-size:9px;}

#sariLastMileModern .sari-batch-map-note{margin:9px 0 0;font-size:11px;color:#8B97A7;}

#sariLastMileModern .lm-detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px;padding:19px 24px;border-bottom:1px solid #EDF0F4;}

#sariLastMileModern .lm-detail{padding:14px 16px;border-radius:11px;background:#F9FAFC;border:1px solid #EFF1F4;}

#sariLastMileModern .lm-label{display:block;font-size:11px;color:#96A1B0;margin:0 0 5px;}

#sariLastMileModern .lm-detail strong{display:block;font-size:13px;font-weight:500;color:#304054;overflow-wrap:anywhere;}

#sariLastMileModern .lm-detail p{font-size:12px;color:#8592A3;margin:5px 0 0;overflow-wrap:anywhere;}

#sariLastMileModern .lm-ready{margin:20px 24px;padding:15px 17px;background:#F8FAFC;border:1px solid #E8EDF3;border-radius:11px;}

#sariLastMileModern .lm-ready strong{font-size:13px;color:#364557;font-weight:500;}

#sariLastMileModern .lm-ready p{font-size:12px;color:#8794A4;margin:5px 0 0;}

#sariLastMileModern .lm-ready-actions{padding:0 24px 23px;}

#sariLastMileModern .lm-parcel-section{padding:0 24px 23px;}

#sariLastMileModern .lm-parcel-heading{padding:20px 0 13px;display:flex;justify-content:space-between;align-items:center;gap:10px;}

#courierMain #sariLastMileModern .lm-parcel-heading h4{font-size:16px;font-weight:600;color:#28384B;margin:0;}

#sariLastMileModern .lm-parcel-heading span{font-size:11px;color:#97A2B0;}

#sariLastMileModern .lm-parcel-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}

#sariLastMileModern .lm-parcel{background:#fff;border:1px solid #E6EBF1;border-radius:13px;padding:18px;min-width:0;}

#sariLastMileModern .lm-parcel-head{display:flex;align-items:start;justify-content:space-between;flex-wrap:wrap;gap:9px;margin-bottom:13px;}

#sariLastMileModern .lm-parcel-id{display:block;color:#27384A;font-size:14px;font-weight:600;overflow-wrap:anywhere;}

#sariLastMileModern .lm-parcel-buyer{display:block;margin-top:4px;color:#637388;font-size:13px;font-weight:500;overflow-wrap:anywhere;}

#sariLastMileModern .lm-parcel-address{font-size:12px;line-height:1.75;color:#8592A3;margin:0 0 6px;overflow-wrap:anywhere;}

#sariLastMileModern .lm-parcel-phone{color:#778697;font-size:12px;margin:4px 0 0;}

#sariLastMileModern .lm-parcel-actions{display:flex;flex-wrap:wrap;gap:9px;margin:13px 0 0;}

#sariLastMileModern .lm-parcel-actions .lm-btn{flex:1;}

#sariLastMileModern .lm-checkpoint{margin-top:13px;padding:12px 13px;border-radius:9px;background:#F8FAFC;border:1px solid #E9EEF4;font-size:12px;color:#748296;}

#sariLastMileModern .lm-checkpoint--done{background:#F0F8F3;border-color:#D6ECDF;color:#2D7956;}

#sariLastMileModern .lm-checkpoint--warn{color:#99742B;background:#FFFAEE;border-color:#F4E5C3;}

#sariLastMileModern .lm-checkpoint p{margin:0;}

#sariLastMileModern .lm-checkpoint+.lm-btn{width:100%;margin-top:10px;}

#sariLastMileModern .lm-arrival-form{margin-top:11px;}

#sariLastMileModern .lm-arrival-form .lm-btn{width:100%;}

#sariLastMileModern .lm-empty{text-align:center;display:flex;align-items:center;justify-content:center;flex-direction:column;padding:48px 24px;min-height:255px;}

#sariLastMileModern .lm-empty svg{width:46px;height:46px;stroke:#9BA6B4;stroke-width:1.4;fill:none;}

#courierMain #sariLastMileModern .lm-empty h3{font-size:18px;font-weight:500;color:#647387;margin:16px 0 6px;}

#sariLastMileModern .lm-empty p{font-size:13px;max-width:420px;color:#8B97A7;margin:0;}

#sariLastMileModern .lm-empty .lm-btn{margin-top:19px;}

#sariLastMileModern .lm-alert{padding:14px 17px;margin-bottom:16px;border-radius:11px;font-size:12px;}

#sariLastMileModern .lm-alert--ok{background:#EAF8F0;border:1px solid #C6E8D3;color:#2B7751;}

#sariLastMileModern .lm-alert--bad{background:#FFF1F2;border:1px solid #F2C9D0;color:#A13F4B;}

@media(max-width:980px){#sariLastMileModern .lm-top{align-items:flex-start;flex-direction:column;}#sariLastMileModern .lm-top-actions{justify-content:flex-start;}#sariLastMileModern .lm-parcel-list{grid-template-columns:1fr;}}

@media(max-width:680px){body:has(#sariLastMileModern) #courierMain>main{padding:16px 12px 30px!important;}#sariLastMileModern .lm-summary{gap:9px;}#sariLastMileModern .lm-stat{padding:14px;min-height:90px;gap:9px;}#sariLastMileModern .lm-stat-icon svg{width:21px;height:21px;}#sariLastMileModern .lm-stat-icon{flex-basis:21px;}#sariLastMileModern .lm-stat-value{font-size:24px;}#sariLastMileModern .lm-stat-note{font-size:10px;}#sariLastMileModern .lm-stat-label{font-size:11px;}#sariLastMileModern .lm-steps{grid-template-columns:1fr;}#sariLastMileModern .lm-step+.lm-step{border-left:0;border-top:1px solid #ECF0F4;}#sariLastMileModern .lm-detail-grid{grid-template-columns:1fr;}#sariLastMileModern .lm-map-section,#sariLastMileModern .lm-parcel-section{padding-left:16px;padding-right:16px;}#sariLastMileModern .lm-batch-head{padding:18px 16px;}#sariLastMileModern .lm-detail-grid{padding:16px;}#sariLastMileModern .lm-ready{margin:16px;}#sariLastMileModern .lm-ready-actions{padding:0 16px 16px;}#sariLastMileModern .sari-batch-map{height:285px;min-height:285px;}}

@media(max-width:480px){#sariLastMileModern .lm-summary{grid-template-columns:1fr;}#sariLastMileModern .lm-stat{min-height:83px;}#sariLastMileModern .lm-top-actions{width:100%;}#sariLastMileModern .lm-top-actions .lm-btn{flex:1;}#sariLastMileModern .lm-map-head{align-items:flex-start;flex-direction:column;}#sariLastMileModern .lm-map-head .lm-btn{width:100%;}}

@media(prefers-reduced-motion:reduce){#sariLastMileModern *,#sariLastMileModern *::before,#sariLastMileModern *::after{transition:none!important;animation:none!important;}}



/* Compact, collapsed completion history; no address, phone or stale directions in archive. */

#sariLastMileModern .lm-history{margin:24px 0 0;overflow:hidden;}

#sariLastMileModern .lm-history-toggle{display:flex;justify-content:space-between;align-items:center;gap:14px;cursor:pointer;padding:19px 23px;list-style:none;user-select:none;}

#sariLastMileModern .lm-history-toggle::-webkit-details-marker{display:none;}

#sariLastMileModern .lm-history-toggle:focus-visible{outline:2px solid #C98D19;outline-offset:-4px;border-radius:16px;}

#sariLastMileModern .lm-history-heading{display:flex;align-items:center;gap:13px;min-width:0;}

#sariLastMileModern .lm-history-icon{flex:0 0 38px;width:38px;height:38px;display:grid;place-items:center;border:1px solid #E7ECF2;border-radius:11px;color:#909CAC;background:#F8FAFC;}

#sariLastMileModern .lm-history-icon svg{width:21px;height:21px;}

#sariLastMileModern .lm-history-heading-copy{display:grid;gap:3px;min-width:0;}

#sariLastMileModern .lm-history-heading-copy strong{color:#29384B;font-size:16px;font-weight:600;}

#sariLastMileModern .lm-history-heading-copy small{color:#909CAB;font-size:11px;line-height:1.5;}

#sariLastMileModern .lm-history-toggle-end{display:flex;align-items:center;gap:12px;flex:none;}

#sariLastMileModern .lm-history-count{color:#68778A;background:#F4F6F8;border:1px solid #E8ECF1;border-radius:999px;padding:6px 10px;font-size:11px;white-space:nowrap;}

#sariLastMileModern .lm-history-chevron{width:18px;height:18px;color:#8390A0;transition:transform .18s;}

#sariLastMileModern .lm-history[open] .lm-history-chevron{transform:rotate(180deg);}

#sariLastMileModern .lm-history-body{padding:3px 23px 15px;border-top:1px solid #ECF0F4;}

#sariLastMileModern .lm-history-item{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:15px 2px;}

#sariLastMileModern .lm-history-item+.lm-history-item{border-top:1px solid #EEF1F5;}

#sariLastMileModern .lm-history-item-main{min-width:0;display:grid;gap:3px;}

#sariLastMileModern .lm-history-code{font-size:11px;color:#8A97A6;overflow-wrap:anywhere;}

#sariLastMileModern .lm-history-route{font-size:13px;font-weight:500;color:#344357;line-height:1.5;overflow-wrap:anywhere;}

#sariLastMileModern .lm-history-parcels{font-size:11px;color:#929EAB;line-height:1.6;overflow-wrap:anywhere;}

#sariLastMileModern .lm-history-item>.lm-chip{flex:none;}

@media(max-width:560px){#sariLastMileModern .lm-history-toggle{padding:16px;align-items:flex-start;}#sariLastMileModern .lm-history-heading{gap:9px;}#sariLastMileModern .lm-history-heading-copy strong{font-size:14px;}#sariLastMileModern .lm-history-heading-copy small{font-size:10px;}#sariLastMileModern .lm-history-toggle-end{gap:5px;}#sariLastMileModern .lm-history-count{font-size:10px;padding:5px 8px;}#sariLastMileModern .lm-history-body{padding:0 16px 10px;}#sariLastMileModern .lm-history-item{align-items:flex-start;gap:8px;flex-wrap:wrap;}#sariLastMileModern .lm-history-item>.lm-chip{font-size:10px;}}




/* 2026-11 SARI Rider Last-Mile polish: same compact visual scale as Active Deliveries.
   Scoped view-only CSS; parcel status, QR, order actions, maps and counts are unchanged. */
#sariLastMileModern {
    --lm-ink:#2f3742;
    --lm-muted:#7a8593;
    --lm-line:#e5e7eb;
    --lm-gold:#c88912;
    --lm-shadow:0 2px 5px rgba(15,23,42,.045),
                0 10px 24px rgba(15,23,42,.075),
                0 24px 54px rgba(15,23,42,.085),
                inset 0 1px 0 rgba(255,255,255,.92);
    max-width:1400px;
    margin:0 auto;
    font:400 13px/1.65 'Poppins',ui-sans-serif,system-ui,sans-serif;
}
body:has(#sariLastMileModern) #courierMain > main {
    padding:22px clamp(14px,2.2vw,30px) 42px!important;
}
#sariLastMileModern .lm-top { align-items:flex-end; gap:12px 20px; margin-bottom:18px; }
#sariLastMileModern .lm-kicker {
    margin:0 0 6px;
    color:var(--lm-gold);
    font-size:10.5px;
    font-weight:800;
    line-height:1.4;
    letter-spacing:.14em;
    text-transform:uppercase;
}
#courierMain #sariLastMileModern .lm-title {
    margin:0;
    color:var(--lm-ink);
    font-size:clamp(28px,2.35vw,33px);
    line-height:1.05;
    font-weight:760;
    letter-spacing:-.04em;
}
#sariLastMileModern .lm-title-accent { color:var(--lm-gold); }
#sariLastMileModern .lm-btn {
    min-height:43px;
    padding:10px 15px;
    border-radius:11px;
    border-color:#dce2e8;
    font-size:11.5px;
    font-weight:700;
    color:#465363;
    box-shadow:none;
}
#sariLastMileModern .lm-btn:hover { background:#f3f4f6; border-color:#cbd5df; color:#293440; }
#sariLastMileModern .lm-btn-gold {
    background:var(--lm-gold);
    border-color:var(--lm-gold);
    color:#fff;
}
#sariLastMileModern .lm-btn-gold:hover {
    background:#b47b0e;
    border-color:#b47b0e;
    color:#fff;
}
#sariLastMileModern .lm-card {
    border:1px solid var(--lm-line);
    border-radius:17px;
    box-shadow:var(--lm-shadow);
}
#sariLastMileModern .lm-summary { gap:14px; margin-bottom:17px; }
#sariLastMileModern .lm-stat {
    min-height:105px;
    padding:14px 17px;
    gap:14px;
}
#sariLastMileModern .lm-stat-icon { flex-basis:28px; }
#sariLastMileModern .lm-stat-icon svg { width:25px; height:25px; stroke-width:1.8; }
#sariLastMileModern .lm-stat:nth-child(1) .lm-stat-icon { color:#c88912; }
#sariLastMileModern .lm-stat:nth-child(2) .lm-stat-icon { color:#3865c7; }
#sariLastMileModern .lm-stat:nth-child(3) .lm-stat-icon { color:#0f8a6b; }
#sariLastMileModern .lm-stat-label { font-size:12px; color:#5f6d7c; font-weight:600; }
#sariLastMileModern .lm-stat-value {
    color:#1f2937;
    font-size:clamp(23px,2vw,28px);
    font-weight:650;
    line-height:1.12;
    letter-spacing:-.045em;
    margin:4px 0;
    font-variant-numeric:tabular-nums;
}
#sariLastMileModern .lm-stat-note { color:#8b96a4; font-size:10.5px; }
#sariLastMileModern .lm-steps { padding:8px 10px; margin-bottom:18px; }
#sariLastMileModern .lm-step { padding:10px 12px; gap:11px; }
#sariLastMileModern .lm-step-num {
    width:32px;
    height:32px;
    flex-basis:32px;
    font-size:11.5px;
    font-weight:750;
    border-radius:9px;
    color:#9a6a12;
    border-color:#ebddb7;
    background:#fffaf0;
}
#sariLastMileModern .lm-step strong { font-size:12px; font-weight:700; color:#344357; }
#sariLastMileModern .lm-step small { font-size:10.5px; }
#sariLastMileModern .lm-section-head { margin:15px 0 12px; }
#courierMain #sariLastMileModern .lm-section-head h3 {
    font-size:17px;
    font-weight:750;
    color:#2f3742;
    line-height:1.35;
}
#sariLastMileModern .lm-section-head p { font-size:11.5px; }
#sariLastMileModern .lm-section-count { font-size:10.5px; font-weight:700; }
#sariLastMileModern .lm-batch { margin-bottom:16px; }
#sariLastMileModern .lm-batch-head { padding:19px 21px; }
#sariLastMileModern .lm-code { font-size:10.5px; font-weight:600; }
#courierMain #sariLastMileModern .lm-batch-title {
    font-size:17px;
    font-weight:750;
    line-height:1.35;
    color:#293440;
}
#sariLastMileModern .lm-batch-sub { font-size:11px; }
#sariLastMileModern .lm-chip { font-size:10px; font-weight:700; padding:5px 10px; }
#sariLastMileModern .lm-chip--active { background:#fff8eb; color:#936411; }
#sariLastMileModern .lm-chip--active::before { background:var(--lm-gold); }
#sariLastMileModern .lm-map-section { padding:18px 21px; }
#courierMain #sariLastMileModern .lm-map-head h4 { font-size:14px; font-weight:750; }
#sariLastMileModern .lm-map-head p { font-size:11px; }
#sariLastMileModern .sari-batch-map { height:320px; min-height:320px; }
#sariLastMileModern .lm-detail-grid { padding:17px 21px; gap:12px; }
#sariLastMileModern .lm-detail { padding:13px 14px; }
#sariLastMileModern .lm-detail strong { font-size:12px; font-weight:700; }
#sariLastMileModern .lm-ready { margin:17px 21px; padding:13px 15px; }
#sariLastMileModern .lm-ready strong { font-weight:700; }
#sariLastMileModern .lm-ready-actions { padding:0 21px 20px; }
#sariLastMileModern .lm-parcel-section { padding:0 21px 21px; }
#sariLastMileModern .lm-parcel-heading { padding:18px 0 12px; }
#courierMain #sariLastMileModern .lm-parcel-heading h4 { font-size:14px; font-weight:750; }
#sariLastMileModern .lm-parcel { padding:16px; border-radius:12px; }
#sariLastMileModern .lm-parcel-id { font-size:12px; font-weight:750; }
#sariLastMileModern .lm-parcel-buyer { font-size:11.5px; font-weight:600; }
#sariLastMileModern .lm-parcel-address { font-size:11.5px; }
#sariLastMileModern .lm-history { margin-top:20px; }
#sariLastMileModern .lm-history-toggle { padding:17px 20px; }
#sariLastMileModern .lm-history-heading-copy strong { font-size:14px; font-weight:750; }
#sariLastMileModern .lm-history-body { padding:2px 20px 12px; }
@media (max-width:767px) {
    body:has(#sariLastMileModern) #courierMain > main { padding:16px 14px 28px!important; }
    #courierMain #sariLastMileModern .lm-title { font-size:25px; }
    #sariLastMileModern .lm-kicker { font-size:9.5px; }
    #sariLastMileModern .lm-stat { min-height:100px; padding:13px 14px; }
    #sariLastMileModern .lm-stat-value { font-size:25px; }
    #sariLastMileModern .lm-map-section,
    #sariLastMileModern .lm-parcel-section { padding-left:16px; padding-right:16px; }
    #sariLastMileModern .lm-batch-head { padding:17px 16px; }
    #sariLastMileModern .lm-detail-grid { padding:16px; }
    #sariLastMileModern .lm-ready { margin:16px; }
    #sariLastMileModern .lm-ready-actions { padding:0 16px 17px; }
    #sariLastMileModern .sari-batch-map { height:285px; min-height:285px; }
}
@media (max-width:480px) {
    #sariLastMileModern .lm-summary { grid-template-columns:1fr; gap:10px; }
    #sariLastMileModern .lm-stat { min-height:87px; }
    #sariLastMileModern .lm-top-actions { width:100%; }
    #sariLastMileModern .lm-top-actions .lm-btn { width:100%; }
}

@endverbatim

</style>

@endpush



@section('content')

@php

    $activeAssignments = $assignments->whereIn('status', ['assigned','out_for_delivery']);

    $completedAssignments = $assignments->where('status','completed');

    // Keep completed work accessible as history, but never mix it into active cards.

    $ongoingAssignments = $assignments->filter(fn ($assignment) => $assignment->status !== 'completed');

    $activeParcelCount = $activeAssignments->sum(fn ($assignment) => $assignment->orders->count());

@endphp



<div id="sariLastMileModern">

    @if(session('success'))

        <div class="lm-alert lm-alert--ok" role="status">{{ session('success') }}</div>

    @endif

    @if($errors->any())

        <div class="lm-alert lm-alert--bad" role="alert">{{ $errors->first() }}</div>

    @endif



    <header class="lm-top">

        <div>

            <p class="lm-kicker">Rider operations / Last-mile</p>

            <h1 class="lm-title">Last-Mile <span class="lm-title-accent">Deliveries</span></h1>

            

        </div>

        <div class="lm-top-actions">

            <a class="lm-btn lm-btn-gold" href="{{ route('courier.parcel-scanner') }}">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9V4h5M15 4h5v5M20 15v5h-5M9 20H4v-5"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>

                Scan Parcel QR

            </a>

        </div>

    </header>



    <section class="lm-summary" aria-label="Last-mile workload summary">

        <article class="lm-card lm-stat">

            <span class="lm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="6" width="16" height="14" rx="2"/><path d="M8 6V4h8v2M4 11h16"/></svg></span>

            <div><p class="lm-stat-label">Active batches</p><strong class="lm-stat-value">{{ number_format($activeAssignments->count()) }}</strong><p class="lm-stat-note">Assigned delivery groups</p></div>

        </article>

        <article class="lm-card lm-stat">

            <span class="lm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 7 12 3l9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7M12 11v10"/></svg></span>

            <div><p class="lm-stat-label">Active parcels</p><strong class="lm-stat-value">{{ number_format($activeParcelCount) }}</strong><p class="lm-stat-note">In active batches</p></div>

        </article>

        <article class="lm-card lm-stat">

            <span class="lm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="9"/></svg></span>

            <div><p class="lm-stat-label">Completed batches</p><strong class="lm-stat-value">{{ number_format($completedAssignments->count()) }}</strong><p class="lm-stat-note">Finished assignments</p></div>

        </article>

    </section>



    <section class="lm-card lm-steps" aria-label="Last-mile workflow">

        <div class="lm-step"><span class="lm-step-num">01</span><div><strong>Hub collection</strong><small>Scan assigned parcels</small></div></div>

        <div class="lm-step"><span class="lm-step-num">02</span><div><strong>Local delivery</strong><small>Navigate to the Buyer</small></div></div>

        <div class="lm-step"><span class="lm-step-num">03</span><div><strong>Buyer handoff</strong><small>Confirm arrival and scan QR</small></div></div>

    </section>



    <div class="lm-section-head"><div><h3>Delivery assignments</h3><p>Only ongoing destination-hub batches are shown here.</p></div><span class="lm-section-count">{{ $ongoingAssignments->count() }} {{ $ongoingAssignments->count() === 1 ? 'active batch' : 'active batches' }}</span></div>



    @foreach($ongoingAssignments as $assignment)

        @php

            $isAssigned = $assignment->status === 'assigned';

            $isOut = $assignment->status === 'out_for_delivery';

            $isCompleted = $assignment->status === 'completed';

        @endphp

        <article class="lm-card lm-batch">

            <div class="lm-batch-head">

                <div>

                    <p class="lm-code">Batch {{ $assignment->assignment_code }}</p>

                    <h3 class="lm-batch-title">{{ $assignment->facility_name }} → {{ $assignment->delivery_area }}</h3>

                    <p class="lm-batch-sub">{{ $assignment->orders->count() }} {{ $assignment->orders->count() === 1 ? 'parcel' : 'parcels' }} in this batch</p>

                </div>

                <span class="lm-chip {{ $isCompleted ? 'lm-chip--done' : ($isOut ? 'lm-chip--active' : '') }}">{{ str_replace('_', ' ', $assignment->status) }}</span>

            </div>



            @if(! $isCompleted)

                @php

                    // Saved hub and buyer locations only; never infer or manufacture a route.

                    $batchStops = [];

                    $hubLat = $assignment->facility_latitude;

                    $hubLng = $assignment->facility_longitude;

                    if ($hubLat !== null && $hubLng !== null && is_finite((float)$hubLat) && is_finite((float)$hubLng)

                        && abs((float)$hubLat) <= 90 && abs((float)$hubLng) <= 180

                        && ! ((float)$hubLat === 0.0 && (float)$hubLng === 0.0)) {

                        $batchStops[] = ['lat'=>(float)$hubLat,'lng'=>(float)$hubLng,'name'=>(string)$assignment->facility_name,'kind'=>'hub'];

                    }

                    foreach ($assignment->orders as $batchOrder) {

                        $lat = $batchOrder->buyer_latitude;

                        $lng = $batchOrder->buyer_longitude;

                        if ($lat !== null && $lng !== null && is_finite((float)$lat) && is_finite((float)$lng)

                            && abs((float)$lat) <= 90 && abs((float)$lng) <= 180

                            && ! ((float)$lat === 0.0 && (float)$lng === 0.0)) {

                            $batchStops[] = ['lat'=>(float)$lat,'lng'=>(float)$lng,'name'=>(string)$batchOrder->order_number.' · Buyer','kind'=>'buyer'];

                        }

                    }

                @endphp

                <div class="lm-map-section">

                    <div class="lm-map-head"><div><h4>Delivery area map</h4><p>Saved hub and Buyer locations</p></div><a class="lm-btn" href="{{ route('courier.parcel-scanner') }}">Scan Parcel QR</a></div>

                    <div id="sariBatchMap{{ $assignment->id }}" class="sari-batch-map" data-sari-batch-map role="region" aria-label="Saved destinations for batch {{ $assignment->assignment_code }}"></div>

                    <script type="application/json" id="sariBatchStops{{ $assignment->id }}">@json($batchStops)</script>

                    <p class="sari-batch-map-note" data-batch-map-note>No road route or ETA calculated. Use each Buyer's directions link to navigate.</p>

                </div>

            @endif



            <div class="lm-detail-grid">

                <div class="lm-detail"><span class="lm-label">Destination hub</span><strong>{{ $assignment->facility_name }}</strong><p>{{ $assignment->facility_address ?: 'Hub address unavailable' }}</p></div>

                <div class="lm-detail"><span class="lm-label">Delivery area</span><strong>{{ $assignment->delivery_area }}</strong><p>Assigned service area for this batch</p></div>

            </div>



            @if($isAssigned)

                <div class="lm-ready"><strong>Ready for hub pickup</strong><p>Scan each assigned parcel at the destination hub. Your route starts after all parcels are verified.</p></div>

                <div class="lm-ready-actions"><a class="lm-btn lm-btn-gold" style="width:100%" href="{{ route('courier.parcel-scanner') }}">Scan All Parcels to Start Route <span aria-hidden="true">→</span></a></div>

            @endif



            <div class="lm-parcel-section">

                <div class="lm-parcel-heading"><h4>Parcels in this batch</h4><span>{{ $assignment->orders->count() }} {{ $assignment->orders->count() === 1 ? 'parcel' : 'parcels' }}</span></div>

                <div class="lm-parcel-list">

                    @foreach($assignment->orders as $order)

                        @php

                            $parcelStatus = $order->parcel_status ?? $order->order_status;

                            $isDelivered = $parcelStatus === 'delivered';

                        @endphp

                        <section class="lm-parcel" aria-label="Parcel {{ $order->order_number }}">

                            <div class="lm-parcel-head"><div><strong class="lm-parcel-id">{{ $order->order_number }}</strong><span class="lm-parcel-buyer">{{ $order->buyer_name }}</span></div><span class="lm-chip {{ $isDelivered ? 'lm-chip--done' : ($parcelStatus === 'arrived_buyer' ? 'lm-chip--active' : '') }}">{{ str_replace('_', ' ', $parcelStatus) }}</span></div>

                            <p class="lm-parcel-address">{{ $order->buyer_address }}</p>

                            @if($order->buyer_phone)<p class="lm-parcel-phone">{{ $order->buyer_phone }}</p>@endif



                            @if($order->buyer_latitude !== null && $order->buyer_longitude !== null

                                && is_finite((float)$order->buyer_latitude) && is_finite((float)$order->buyer_longitude)

                                && abs((float)$order->buyer_latitude) <= 90 && abs((float)$order->buyer_longitude) <= 180

                                && ! ((float)$order->buyer_latitude === 0.0 && (float)$order->buyer_longitude === 0.0))

                                <div class="lm-parcel-actions"><a class="lm-btn" href="https://www.google.com/maps/dir/?api=1&destination={{ rawurlencode((string)$order->buyer_latitude.','.(string)$order->buyer_longitude) }}&travelmode=driving" target="_blank" rel="noopener noreferrer">Open Buyer Directions ↗</a></div>

                            @else

                                <div class="lm-checkpoint lm-checkpoint--warn">Buyer coordinates unavailable. Verify the written address before traveling.</div>

                            @endif



                            @if($isOut && $order->parcel_status === 'out_for_delivery')

                                <div class="lm-checkpoint"><p>Confirm arrival after physically reaching the Buyer. QR verification follows this step.</p></div>

                                <form class="lm-arrival-form" method="POST" action="{{ route('courier.last-mile.arrived', ['assignment' => $assignment->id, 'order' => $order->id]) }}">

                                    @csrf

                                    <button type="submit" class="lm-btn lm-btn-gold">Confirm Arrived at Buyer <span aria-hidden="true">→</span></button>

                                </form>

                            @elseif($isOut && $order->parcel_status === 'arrived_buyer')

                                <div class="lm-checkpoint"><p>Arrival confirmed. After Buyer receives the parcel, scan its QR to complete the delivery.</p></div>

                                <a href="{{ route('courier.parcel-scanner') }}" class="lm-btn lm-btn-gold" style="width:100%;margin-top:12px">Scan Parcel QR to Complete Delivery ↗</a>

                            @elseif($isDelivered)

                                <div class="lm-checkpoint lm-checkpoint--done"><p>Delivered successfully</p></div>

                            @endif

                        </section>

                    @endforeach

                </div>

            </div>

        </article>

    @endforeach



    @if($ongoingAssignments->isEmpty())

        <section class="lm-card lm-empty" role="status" aria-label="No ongoing last-mile work">

            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8 12 3l9 5v10l-9 5-9-5zM3 8l9 5 9-5M12 13v10"/></svg>

            <h3>All caught up!</h3>

            <p>There are no ongoing last-mile batches. Completed deliveries are available in Delivery History below.</p>

            <a href="{{ route('courier.dashboard') }}" class="lm-btn lm-btn-gold">Back to Dashboard <span aria-hidden="true">→</span></a>

        </section>

    @endif



    @if($completedAssignments->isNotEmpty())

        {{-- Native details keeps history collapsed until the rider needs it. --}}

        <details class="lm-history lm-card" id="lmDeliveryHistory">

            <summary class="lm-history-toggle">

                <span class="lm-history-heading">

                    <span class="lm-history-icon" aria-hidden="true">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 14h6"/></svg>

                    </span>

                    <span class="lm-history-heading-copy"><strong>Delivery History</strong><small>Completed batches are safely tucked away here.</small></span>

                </span>

                <span class="lm-history-toggle-end"><span class="lm-history-count">{{ number_format($completedAssignments->count()) }} completed</span><svg class="lm-history-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg></span>

            </summary>

            <div class="lm-history-body">

                @foreach($completedAssignments as $completedBatch)

                    <div class="lm-history-item">

                        <div class="lm-history-item-main">

                            <span class="lm-history-code">{{ $completedBatch->assignment_code }}</span>

                            <strong class="lm-history-route">{{ $completedBatch->facility_name }} → {{ $completedBatch->delivery_area }}</strong>

                            <span class="lm-history-parcels">{{ $completedBatch->orders->count() }} {{ $completedBatch->orders->count() === 1 ? 'parcel' : 'parcels' }} · {{ $completedBatch->orders->pluck('order_number')->filter()->implode(', ') }}</span>

                        </div>

                        <span class="lm-chip lm-chip--done">Completed</span>

                    </div>

                @endforeach

            </div>

        </details>

    @endif

</div>

@endsection



@push('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>

<script>

(() => {

    document.querySelectorAll('[data-sari-batch-map]').forEach(el => {

        const dataEl=document.getElementById(el.id.replace('sariBatchMap','sariBatchStops'));

        const note=el.parentElement?.querySelector('[data-batch-map-note]');

        if(typeof L==='undefined'){if(note)note.textContent='OpenStreetMap could not load. Open Buyer Road Directions from the parcel details.';return;}

        let stops=[];

        try{stops=JSON.parse(dataEl?.textContent||'[]');}catch(e){stops=[];}

        const valid=stops.filter(p=>Number.isFinite(Number(p.lat))&&Number.isFinite(Number(p.lng))

             &&Math.abs(Number(p.lat))<=90&&Math.abs(Number(p.lng))<=180

             &&!(Number(p.lat)===0&&Number(p.lng)===0));

        const map=L.map(el,{scrollWheelZoom:false});

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{

            maxZoom:19,attribution:'© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a> contributors'

        }).addTo(map);

        const coords=[];

        valid.forEach(p=>{

            const ll=[Number(p.lat),Number(p.lng)];coords.push(ll);

            const content=document.createElement('div');

            const strong=document.createElement('strong');strong.textContent=p.kind==='hub'?'Destination hub':'Buyer destination';

            const label=document.createElement('div');label.textContent=String(p.name||'Saved stop');

            content.append(strong,label);

            L.circleMarker(ll,{radius:8,color:'#fff',weight:2,fillColor:p.kind==='hub'?'#3b82f6':'#20a679',fillOpacity:1})

             .addTo(map).bindPopup(content);

        });

        if(coords.length>1)map.fitBounds(coords,{padding:[35,35],maxZoom:15});

        else if(coords.length)map.setView(coords[0],15);

        else map.setView([12.8797,121.7740],6);

        if(note)note.textContent=coords.length?`${coords.length} saved hub/buyer pin(s). Select a buyer's Directions link for road navigation; SARI does not calculate ETA.`:'No saved coordinates for this batch. Confirm the address before departing.';

        setTimeout(()=>map.invalidateSize(),200);

    });

})();

</script>

@endpush
