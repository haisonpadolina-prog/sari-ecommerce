@extends('layouts.courier')



@section('title', 'Active Deliveries')

@section('header-title', 'Active Deliveries')

@section('header-subtitle', 'Direct Delivery Operations')



@push('styles')

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

@verbatim

/* Buyer Cart-inspired Rider Active Deliveries.

   Styles limited to this view; all order data and routes remain unchanged. */

body:has(#riderActiveDeliveries),body:has(#riderActiveDeliveries) #courierMain{background:#f4f5f7!important;color:#243246;}

body:has(#riderActiveDeliveries) #courierSidebar{background:#fff!important;border-right:1px solid #e9edf2!important;box-shadow:none!important;}

body:has(#riderActiveDeliveries) #courierSidebarProfile{background:#fff!important;border-color:#e9edf2!important;}

body:has(#riderActiveDeliveries) #courierMain>header{background:#fff!important;border-bottom:1px solid #e9edf2!important;box-shadow:none!important;}

body:has(#riderActiveDeliveries) #courierMain>header h1{font-size:18px!important;font-weight:600!important;letter-spacing:-.03em;}

body:has(#riderActiveDeliveries) #courierMain>main{padding:25px clamp(16px,2.15vw,34px) 42px!important;background:transparent!important;}

@media(min-width:1024px){body:has(#riderActiveDeliveries):not(.courier-sidebar-collapsed) #courierMain{padding-left:244px!important;}body:has(#riderActiveDeliveries):not(.courier-sidebar-collapsed) #courierSidebar{width:244px!important;}body:has(#riderActiveDeliveries).courier-sidebar-collapsed #courierMain{padding-left:92px!important;}}

@media(max-width:1023px){body:has(#riderActiveDeliveries) #courierMain{padding-left:0!important;}}

#riderActiveDeliveries{--ad-ink:#202c3d;--ad-body:#43536a;--ad-muted:#8190a1;--ad-border:#e7ebf0;--ad-gold:#c98d19;--ad-gold-hover:#ac7814;--ad-float:0 2px 5px rgba(15,23,42,.04),0 10px 24px rgba(15,23,42,.07),0 24px 54px rgba(15,23,42,.075),inset 0 1px 0 rgba(255,255,255,.92);width:100%;max-width:none;margin:0;color:var(--ad-ink);font:400 13px/1.65 'Poppins',ui-sans-serif,system-ui,sans-serif;}

#riderActiveDeliveries *,#riderActiveDeliveries *::before,#riderActiveDeliveries *::after{box-sizing:border-box;}

#riderActiveDeliveries a{text-decoration:none;font-family:inherit;}

#riderActiveDeliveries a:focus-visible{outline:2px solid var(--ad-gold);outline-offset:3px;}

#riderActiveDeliveries svg{display:block;flex:none;fill:none;stroke:currentColor;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round;}

#riderActiveDeliveries .ad-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-bottom:22px;}

#riderActiveDeliveries .ad-eyebrow{display:block;color:#a57723;letter-spacing:.13em;text-transform:uppercase;font-size:10px;font-weight:600;margin:0;}

#riderActiveDeliveries .ad-title{font-size:clamp(30px,3vw,38px);line-height:1.12;letter-spacing:-.046em;font-weight:700;color:#1a2737;margin:8px 0;}

#riderActiveDeliveries .ad-intro{font-size:13px;line-height:1.75;color:#728094;max-width:680px;margin:0;}

#riderActiveDeliveries .ad-head-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap;}

#riderActiveDeliveries .ad-btn{display:inline-flex;justify-content:center;align-items:center;gap:9px;min-height:44px;padding:10px 17px;border-radius:11px;border:1px solid transparent;font-size:12px;font-weight:500;line-height:1.45;white-space:nowrap;transition:background .16s ease,box-shadow .16s ease,border-color .16s ease,transform .16s ease;}

#riderActiveDeliveries .ad-btn svg{height:17px;width:17px;}

#riderActiveDeliveries .ad-btn-neutral{background:#fff;color:#4d5d6f;border-color:#dce4ec;box-shadow:0 2px 7px rgba(15,23,42,.03);}

#riderActiveDeliveries .ad-btn-neutral:hover{background:#f9fafc;border-color:#c1ccd8;}

#riderActiveDeliveries .ad-btn-gold{background:var(--ad-gold);border-color:var(--ad-gold);color:#fff;box-shadow:0 5px 13px rgba(166,105,5,.12);}

#riderActiveDeliveries .ad-btn-gold:hover{background:var(--ad-gold-hover);border-color:var(--ad-gold-hover);box-shadow:0 7px 18px rgba(166,105,5,.17);}

#riderActiveDeliveries .ad-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:20px;}

#riderActiveDeliveries .ad-stat{min-width:0;display:flex;align-items:center;gap:18px;padding:20px 22px;background:#fff;border:1px solid var(--ad-border);border-radius:16px;box-shadow:var(--ad-float);}

#riderActiveDeliveries .ad-stat-icon{width:31px;height:31px;display:grid;place-items:center;color:#929dad;}

#riderActiveDeliveries .ad-stat:first-child .ad-stat-icon{color:#b68429;}

#riderActiveDeliveries .ad-stat-icon svg{width:28px;height:28px;}

#riderActiveDeliveries .ad-stat-label{color:#77869a;font-size:12px;line-height:1.5;margin:0;}

#riderActiveDeliveries .ad-stat-value{display:block;color:#202d40;font-size:30px;font-weight:600;letter-spacing:-.05em;line-height:1.12;margin:5px 0 4px;font-variant-numeric:tabular-nums;}

#riderActiveDeliveries .ad-stat-note{color:#98a3b0;font-size:11px;line-height:1.5;margin:0;}

#riderActiveDeliveries .ad-surface{background:#fff;border:1px solid var(--ad-border);border-radius:17px;box-shadow:var(--ad-float);overflow:hidden;}

#riderActiveDeliveries .ad-surface-head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:23px 25px 20px;border-bottom:1px solid #edf0f4;}

#riderActiveDeliveries .ad-surface-title{color:#243248;font-size:18px;line-height:1.4;letter-spacing:-.03em;font-weight:600;margin:0;}

#riderActiveDeliveries .ad-surface-desc{color:#8895a5;font-size:12px;line-height:1.65;margin:4px 0 0;}

#riderActiveDeliveries .ad-count{display:flex;gap:10px;align-items:center;color:#8895a4;font-size:12px;font-weight:500;}

#riderActiveDeliveries .ad-count strong{color:#455568;font-size:16px;font-weight:600;}

#riderActiveDeliveries .ad-orders{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:stretch;}

#riderActiveDeliveries .ad-order{min-width:0;display:flex;flex-direction:column;padding:25px;border-bottom:1px solid #edf0f4;background:#fff;}

#riderActiveDeliveries .ad-order:nth-child(odd){border-right:1px solid #edf0f4;}

#riderActiveDeliveries .ad-order-top{display:flex;align-items:start;justify-content:space-between;gap:15px;flex-wrap:wrap;}

#riderActiveDeliveries .ad-order-heading{display:flex;align-items:flex-start;gap:12px;min-width:0;}

#riderActiveDeliveries .ad-order-icon{width:25px;height:25px;color:#9b7a3d;margin-top:2px;}

#riderActiveDeliveries .ad-order-icon svg{width:100%;height:100%;}

#riderActiveDeliveries .ad-overline{font-size:10px;letter-spacing:.07em;text-transform:uppercase;color:#93a0ae;font-weight:500;line-height:1.5;margin:0 0 3px;}

#riderActiveDeliveries .ad-order-number{font-size:16px;font-weight:600;letter-spacing:-.025em;line-height:1.45;margin:0;color:#27364b;overflow-wrap:anywhere;}

#riderActiveDeliveries .ad-badge{display:inline-flex;align-items:center;gap:7px;border:1px solid #e4e9ef;background:#f7f9fb;color:#68778b;border-radius:999px;padding:6px 10px;font-size:10px;font-weight:500;line-height:1.45;text-transform:capitalize;overflow-wrap:anywhere;max-width:100%;}

#riderActiveDeliveries .ad-badge::before{content:'';height:6px;width:6px;border-radius:50%;background:#9aa5b2;flex:none;}

#riderActiveDeliveries .ad-badge--arrived{border-color:#d3e8d9;background:#f1f9f3;color:#397d5a;}

#riderActiveDeliveries .ad-badge--arrived::before{background:#49a475;}

#riderActiveDeliveries .ad-route{margin-top:23px;display:grid;grid-template-columns:21px minmax(0,1fr);gap:0 12px;}

#riderActiveDeliveries .ad-route-track{grid-row:1/3;display:flex;flex-direction:column;align-items:center;padding-top:5px;gap:5px;}

#riderActiveDeliveries .ad-route-dot{flex:none;height:10px;width:10px;background:#fff;border:2px solid #d49b30;border-radius:50%;}

#riderActiveDeliveries .ad-route-dot--end{border-color:#8fa2b5;}

#riderActiveDeliveries .ad-route-line{width:1px;background:#dce3eb;min-height:36px;flex:1;}

#riderActiveDeliveries .ad-route-point{min-width:0;padding-bottom:18px;}

#riderActiveDeliveries .ad-route-point:last-child{padding-bottom:0;}

#riderActiveDeliveries .ad-label{display:block;margin:0 0 3px;font-size:10px;color:#9aa5b2;letter-spacing:.07em;text-transform:uppercase;font-weight:500;}

#riderActiveDeliveries .ad-route-name{font-size:13px;color:#425168;font-weight:500;line-height:1.6;margin:0;overflow-wrap:anywhere;}

#riderActiveDeliveries .ad-destination{margin-top:20px;display:flex;align-items:flex-start;gap:10px;padding-top:17px;border-top:1px solid #eff1f4;min-width:0;}

#riderActiveDeliveries .ad-destination-icon{color:#929dad;width:18px;height:18px;margin-top:2px;}

#riderActiveDeliveries .ad-destination-icon svg{width:100%;height:100%;}

#riderActiveDeliveries .ad-address{margin:4px 0 0;font-size:12.5px;color:#6d7a8c;line-height:1.7;overflow-wrap:anywhere;}

#riderActiveDeliveries .ad-meta{margin:19px 0 0;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;padding-top:17px;border-top:1px solid #eff1f4;}

#riderActiveDeliveries .ad-meta-value{margin:4px 0 0;font-size:15px;color:#293a4e;font-weight:600;line-height:1.5;overflow-wrap:anywhere;}

#riderActiveDeliveries .ad-meta-value--status{font-size:12.5px;font-weight:500;color:#526277;}

#riderActiveDeliveries .ad-order-bottom{margin-top:auto;padding-top:22px;}

#riderActiveDeliveries .ad-order-bottom .ad-btn{min-height:43px;width:100%;font-size:12px;}

#riderActiveDeliveries .ad-empty{grid-column:1/-1;display:flex;align-items:center;justify-content:center;flex-direction:column;text-align:center;padding:66px 24px 70px;min-height:325px;}

#riderActiveDeliveries .ad-empty-icon{width:52px;height:52px;display:grid;place-items:center;color:#9ca7b5;}

#riderActiveDeliveries .ad-empty-icon svg{width:42px;height:42px;stroke-width:1.45;}

#riderActiveDeliveries .ad-empty-title{font-size:19px;font-weight:600;letter-spacing:-.025em;color:#657386;margin:15px 0 0;}

#riderActiveDeliveries .ad-empty-description{font-size:13px;color:#8190a1;line-height:1.75;max-width:490px;margin:7px 0 0;}

#riderActiveDeliveries .ad-empty-actions{display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin-top:22px;}

#riderActiveDeliveries .ad-foot{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:14px 25px;border-top:1px solid #edf0f4;font-size:11px;line-height:1.6;color:#97a2b0;}

#riderActiveDeliveries .ad-foot p{margin:0;}

@media(hover:hover){#riderActiveDeliveries .ad-btn:hover{transform:translateY(-1px);}#riderActiveDeliveries .ad-order:hover{background:#fcfdff;}}

@media(max-width:950px){#riderActiveDeliveries .ad-orders{grid-template-columns:1fr;}#riderActiveDeliveries .ad-order:nth-child(odd){border-right:0;}}

@media(max-width:760px){#riderActiveDeliveries .ad-stats{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}#riderActiveDeliveries .ad-stat:first-child{grid-column:1/-1;}#riderActiveDeliveries .ad-stat{padding:17px 18px;}#riderActiveDeliveries .ad-stat-value{font-size:27px;}#riderActiveDeliveries .ad-surface-head{padding:20px;}#riderActiveDeliveries .ad-order{padding:21px;}#riderActiveDeliveries .ad-heading{align-items:flex-start;}#riderActiveDeliveries .ad-head-actions{width:100%;}#riderActiveDeliveries .ad-head-actions .ad-btn{flex:1;}#riderActiveDeliveries .ad-title{font-size:29px;}}

@media(max-width:460px){body:has(#riderActiveDeliveries) #courierMain>main{padding:16px 12px 30px!important;}#riderActiveDeliveries .ad-stats{gap:9px;}#riderActiveDeliveries .ad-stat{gap:12px;padding:14px 12px;}#riderActiveDeliveries .ad-stat-icon{width:26px;height:26px;}#riderActiveDeliveries .ad-stat-icon svg{width:24px;height:24px;}#riderActiveDeliveries .ad-stat-label{font-size:11px;}#riderActiveDeliveries .ad-stat-note{font-size:10px;}#riderActiveDeliveries .ad-stat-value{font-size:25px;}#riderActiveDeliveries .ad-head-actions{flex-direction:column;}#riderActiveDeliveries .ad-head-actions .ad-btn{width:100%;}#riderActiveDeliveries .ad-surface-title{font-size:17px;}#riderActiveDeliveries .ad-order{padding:18px;}#riderActiveDeliveries .ad-meta{gap:10px;}#riderActiveDeliveries .ad-order-top{gap:10px;}#riderActiveDeliveries .ad-empty{padding:47px 17px;min-height:280px;}#riderActiveDeliveries .ad-empty-actions{flex-direction:column;width:100%;max-width:260px;}#riderActiveDeliveries .ad-empty-actions .ad-btn{width:100%;}}

@media(prefers-reduced-motion:reduce){#riderActiveDeliveries *{transition:none!important;}}


/* Active Deliveries — aligned with COD Remittance, Delivery History & Rider Earnings.
   UI-only scoped overrides; status counts and delivery actions are unchanged. */
#riderActiveDeliveries {
    --ad-ink: #2f3742;
    --ad-body: #536273;
    --ad-muted: #7a8593;
    --ad-border: #e5e7eb;
    --ad-gold: #c88912;
    --ad-gold-hover: #b47b0e;
    --ad-float: 0 2px 5px rgba(15,23,42,.045),
                0 10px 24px rgba(15,23,42,.075),
                0 24px 54px rgba(15,23,42,.095),
                inset 0 1px 0 rgba(255,255,255,.92);
    max-width: 1400px;
    margin: 0 auto;
    font: 400 13px/1.65 'Poppins',ui-sans-serif,system-ui,sans-serif;
}
body:has(#riderActiveDeliveries) #courierMain > main,
#courierMain > main:has(#riderActiveDeliveries),
#courierPageContent:has(#riderActiveDeliveries) {
    padding: 22px clamp(14px,2.2vw,30px) 42px !important;
}
#riderActiveDeliveries .ad-heading {
    gap: 13px 22px;
    margin-bottom: 18px;
    align-items: flex-end;
}
#riderActiveDeliveries .ad-eyebrow {
    margin: 0 0 6px;
    color: var(--ad-gold);
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .14em;
    line-height: 1.4;
}
#courierMain #riderActiveDeliveries .ad-title,
#courierPageContent #riderActiveDeliveries .ad-title {
    margin: 0;
    color: var(--ad-ink);
    font-size: clamp(28px,2.35vw,33px);
    font-weight: 760;
    line-height: 1.05;
    letter-spacing: -.04em;
}
#riderActiveDeliveries .ad-title-accent { color: var(--ad-gold); }
#riderActiveDeliveries .ad-intro {
    max-width: 690px;
    margin: 9px 0 0;
    color: #7a8593;
    font-size: 11.5px;
    line-height: 1.65;
}
#riderActiveDeliveries .ad-head-actions { gap: 9px; }
#riderActiveDeliveries .ad-btn {
    min-height: 43px;
    padding: 10px 15px;
    border-radius: 12px;
    font-size: 11.5px;
    font-weight: 700;
    line-height: 1.4;
    box-shadow: none;
}
#riderActiveDeliveries .ad-btn svg { width: 16px; height: 16px; }
#riderActiveDeliveries .ad-btn-neutral { border-color: #dce2e8; color: #465363; }
#riderActiveDeliveries .ad-btn-neutral:hover { background: #f3f4f6; border-color: #cbd5df; }
#riderActiveDeliveries .ad-btn-gold { background: var(--ad-gold); border-color: var(--ad-gold); color: #fff; }
#riderActiveDeliveries .ad-btn-gold:hover { background: var(--ad-gold-hover); border-color: var(--ad-gold-hover); }
#riderActiveDeliveries .ad-stats { gap: 14px; margin-bottom: 18px; }
#riderActiveDeliveries .ad-stat {
    min-height: 110px;
    gap: 15px;
    padding: 14px 17px;
    border-color: var(--ad-border);
    border-radius: 18px;
}
#riderActiveDeliveries .ad-stat-icon { width: 29px; height: 29px; color: #c88912; }
#riderActiveDeliveries .ad-stat-icon svg { width: 25px; height: 25px; stroke-width: 1.8; }
#riderActiveDeliveries .ad-stat:nth-child(1) .ad-stat-icon { color: #c88912; }
#riderActiveDeliveries .ad-stat:nth-child(2) .ad-stat-icon { color: #3865c7; }
#riderActiveDeliveries .ad-stat:nth-child(3) .ad-stat-icon { color: #0f8a6b; }
#riderActiveDeliveries .ad-stat-label { color: #5f6d7c; font-size: 12.5px; font-weight: 600; line-height: 1.4; }
#riderActiveDeliveries .ad-stat-value {
    margin: 5px 0 3px;
    color: #1f2937;
    font-size: clamp(24px,1.95vw,29px);
    font-weight: 650;
    line-height: 1.12;
    letter-spacing: -.045em;
}
#riderActiveDeliveries .ad-stat-note { color: #929eab; font-size: 10.5px; line-height: 1.5; }
#riderActiveDeliveries .ad-surface { border-color: var(--ad-border); border-radius: 18px; }
#riderActiveDeliveries .ad-surface-head { padding: 19px 22px 16px; border-bottom-color: #e9edf1; }
#courierMain #riderActiveDeliveries .ad-surface-title,
#courierPageContent #riderActiveDeliveries .ad-surface-title {
    color: #2f3742;
    font-size: 17px;
    font-weight: 750;
    line-height: 1.4;
    letter-spacing: -.025em;
}
#riderActiveDeliveries .ad-surface-desc { font-size: 11.5px; color: #8b96a4; }
#riderActiveDeliveries .ad-count { gap: 7px; color: #7b8794; font-size: 11px; font-weight: 600; }
#riderActiveDeliveries .ad-count strong { color: #293440; font-size: 14px; font-weight: 750; }
#riderActiveDeliveries .ad-order { padding: 21px 22px; border-bottom-color: #edf0f3; }
#riderActiveDeliveries .ad-order-heading { gap: 11px; }
#riderActiveDeliveries .ad-order-icon { color: var(--ad-gold); width: 23px; height: 23px; }
#riderActiveDeliveries .ad-overline { color: #929eab; font-size: 9.5px; font-weight: 700; }
#riderActiveDeliveries .ad-order-number { color: #293440; font-size: 14px; font-weight: 750; line-height: 1.4; }
#riderActiveDeliveries .ad-badge { font-size: 10px; font-weight: 650; border-color: #e5e7eb; background: #f8fafc; color: #647384; }
#riderActiveDeliveries .ad-badge--arrived { border-color: #bde7c9; background: #effcf3; color: #18794e; }
#riderActiveDeliveries .ad-route { margin-top: 19px; }
#riderActiveDeliveries .ad-route-dot { border-color: var(--ad-gold); }
#riderActiveDeliveries .ad-route-dot--end { border-color: #94a3b8; }
#riderActiveDeliveries .ad-route-name { font-size: 12px; color: #465363; font-weight: 600; }
#riderActiveDeliveries .ad-label { color: #929eab; font-size: 9.5px; font-weight: 650; }
#riderActiveDeliveries .ad-destination { margin-top: 17px; padding-top: 15px; }
#riderActiveDeliveries .ad-destination-icon { color: #8796a8; }
#riderActiveDeliveries .ad-address { color: #647384; font-size: 11.5px; line-height: 1.65; }
#riderActiveDeliveries .ad-meta { margin-top: 17px; padding-top: 15px; gap: 13px; }
#riderActiveDeliveries .ad-meta-value { color: #293440; font-size: 13px; font-weight: 700; }
#riderActiveDeliveries .ad-meta-value--status { color: #566579; font-size: 11.5px; font-weight: 600; }
#riderActiveDeliveries .ad-order-bottom { padding-top: 18px; }
#riderActiveDeliveries .ad-order-bottom .ad-btn { min-height: 42px; font-size: 11.5px; }
#riderActiveDeliveries .ad-foot { padding: 13px 22px; color: #8b96a4; font-size: 10.5px; }
#riderActiveDeliveries .ad-empty { padding: 54px 21px 58px; min-height: 290px; }
#riderActiveDeliveries .ad-empty-icon { color: #98a3ae; }
#riderActiveDeliveries .ad-empty-title { font-size: 17px; font-weight: 750; color: #546172; }
#riderActiveDeliveries .ad-empty-description { font-size: 11.5px; color: #8290a0; }
@media (max-width: 767px) {
    body:has(#riderActiveDeliveries) #courierMain > main,
    #courierMain > main:has(#riderActiveDeliveries),
    #courierPageContent:has(#riderActiveDeliveries) { padding: 16px 14px 28px !important; }
    #courierMain #riderActiveDeliveries .ad-title,
    #courierPageContent #riderActiveDeliveries .ad-title { font-size: 25px; }
    #riderActiveDeliveries .ad-eyebrow { font-size: 9.5px; }
    #riderActiveDeliveries .ad-heading { margin-bottom: 16px; }
    #riderActiveDeliveries .ad-stat { min-height: 106px; padding: 13px 14px; gap: 12px; }
    #riderActiveDeliveries .ad-stat-value { font-size: 25px; }
    #riderActiveDeliveries .ad-surface-head { padding: 17px 18px; }
    #riderActiveDeliveries .ad-order { padding: 18px; }
}
@media (max-width: 460px) {
    body:has(#riderActiveDeliveries) #courierMain > main,
    #courierMain > main:has(#riderActiveDeliveries),
    #courierPageContent:has(#riderActiveDeliveries) { padding: 16px 12px 28px !important; }
    #riderActiveDeliveries .ad-stats { gap: 9px; }
    #riderActiveDeliveries .ad-stat { padding: 12px 13px; gap: 11px; min-height: 102px; }
    #riderActiveDeliveries .ad-stat-value { font-size: 24px; }
    #riderActiveDeliveries .ad-stat-icon { width: 25px; height: 25px; }
    #riderActiveDeliveries .ad-stat-icon svg { width: 23px; height: 23px; }
    #riderActiveDeliveries .ad-stat-label { font-size: 11px; }
    #riderActiveDeliveries .ad-stat-note { font-size: 10px; }
    #riderActiveDeliveries .ad-surface-head { padding: 17px; }
    #riderActiveDeliveries .ad-order { padding: 17px; }
    #riderActiveDeliveries .ad-surface-title { font-size: 16px; }
}

@endverbatim

</style>

@endpush



@section('content')

@php

    // Existing rider-scoped direct-delivery records; presentation counts only.

    $activeDeliveryCount = $orders->count();

    $inTransitCount = collect($orders)->where('status', 'in_transit')->count();

    $atBuyerCount = collect($orders)->where('status', 'arrived_buyer')->count();

@endphp



<div id="riderActiveDeliveries" data-sari-ui="ACTIVE-DELIVERIES-CLEAN-V2-NO-HEADER-SHORTCUTS">

    <header class="ad-heading">

        <div>

            <p class="ad-eyebrow">Rider Operations / Direct Deliveries</p>

            <h1 class="ad-title">Active <span class="ad-title-accent">Deliveries</span></h1>

        </div>
    </header>



    <section class="ad-stats" aria-label="Direct delivery statistics">

        <article class="ad-stat">

            <span class="ad-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M3 8v9l9 5 9-5V8M12 13v9"/></svg></span>

            <div>

                <p class="ad-stat-label">Active direct deliveries</p>

                <strong class="ad-stat-value">{{ number_format($activeDeliveryCount) }}</strong>

                <p class="ad-stat-note">Seller-to-buyer jobs</p>

            </div>

        </article>

        <article class="ad-stat">

            <span class="ad-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 6h11v11H3zM14 10h4l3 4v3h-7"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg></span>

            <div>

                <p class="ad-stat-label">In transit</p>

                <strong class="ad-stat-value">{{ number_format($inTransitCount) }}</strong>

                <p class="ad-stat-note">On the way to buyer</p>

            </div>

        </article>

        <article class="ad-stat">

            <span class="ad-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg></span>

            <div>

                <p class="ad-stat-label">At buyer</p>

                <strong class="ad-stat-value">{{ number_format($atBuyerCount) }}</strong>

                <p class="ad-stat-note">Awaiting final handoff</p>

            </div>

        </article>

    </section>



    <section class="ad-surface" aria-labelledby="adQueueTitle">

        <header class="ad-surface-head">

            <div>

                <h3 class="ad-surface-title" id="adQueueTitle">Direct Delivery Queue</h3>

                <p class="ad-surface-desc">Your assigned orders, destinations, and current stages.</p>

            </div>

            <span class="ad-count"><strong>{{ number_format($activeDeliveryCount) }}</strong> {{ $activeDeliveryCount === 1 ? 'delivery' : 'deliveries' }}</span>

        </header>

        <div class="ad-orders">

            @forelse($orders as $order)

                <article class="ad-order" aria-labelledby="ad-order-{{ $order->id }}">

                    <div class="ad-order-top">

                        <div class="ad-order-heading">

                            <span class="ad-order-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 8 12 3l9 5v10l-9 5-9-5zM3 8l9 5 9-5M12 13v10"/></svg></span>

                            <div>

                                <p class="ad-overline">Direct delivery</p>

                                <h4 class="ad-order-number" id="ad-order-{{ $order->id }}">{{ $order->order_number }}</h4>

                            </div>

                        </div>

                        <span class="ad-badge {{ $order->status === 'arrived_buyer' ? 'ad-badge--arrived' : '' }}">{{ $order->statusLabel() }}</span>

                    </div>

                    <div class="ad-route">

                        <div class="ad-route-track" aria-hidden="true">

                            <span class="ad-route-dot"></span><span class="ad-route-line"></span><span class="ad-route-dot ad-route-dot--end"></span>

                        </div>

                        <div class="ad-route-point">

                            <span class="ad-label">Pickup · Seller</span>

                            <p class="ad-route-name">{{ $order->pickup_name ?: 'Seller' }}</p>

                        </div>

                        <div class="ad-route-point">

                            <span class="ad-label">Drop-off · Buyer</span>

                            <p class="ad-route-name">{{ $order->buyer_name ?: 'Buyer' }}</p>

                        </div>

                    </div>

                    <div class="ad-destination">

                        <span class="ad-destination-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg></span>

                        <div>

                            <span class="ad-label">Buyer destination</span>

                            <p class="ad-address">{{ $order->buyer_address ?: 'Delivery address unavailable' }}</p>

                        </div>

                    </div>

                    <div class="ad-meta">

                        <div>

                            <span class="ad-label">Delivery fee</span>

                            <p class="ad-meta-value">₱{{ number_format($order->courierDeliveryFee(), 2) }}</p>

                        </div>

                        <div>

                            <span class="ad-label">Current stage</span>

                            <p class="ad-meta-value ad-meta-value--status">{{ $order->statusLabel() }}</p>

                        </div>

                    </div>

                    <div class="ad-order-bottom">

                        <a class="ad-btn ad-btn-gold" href="{{ route('courier.delivery.show', ['order' => $order->id]) }}" aria-label="Continue direct delivery for {{ $order->order_number }}">

                            Continue Direct Delivery

                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>

                        </a>

                    </div>

                </article>

            @empty

                <div class="ad-empty">

                    <span class="ad-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 7h11v10H3zM14 10h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg></span>

                    <h4 class="ad-empty-title">All caught up!</h4>

                    <p class="ad-empty-description">No active direct deliveries right now. New jobs will appear here when assigned.</p>

                    <div class="ad-empty-actions">

                        <a href="{{ route('courier.requests') }}" class="ad-btn ad-btn-neutral">Pickup Assignments</a>

                        <a href="{{ route('courier.last-mile') }}" class="ad-btn ad-btn-gold">View Last-Mile Deliveries <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>

                    </div>

                </div>

            @endforelse

        </div>

        <footer class="ad-foot">

            <p>@if($activeDeliveryCount > 0)Showing {{ $activeDeliveryCount }} {{ $activeDeliveryCount === 1 ? 'active direct delivery' : 'active direct deliveries' }}.@else Seller-to-Buyer direct delivery workflow.@endif</p>

            <p>Hub deliveries are managed under Last-Mile.</p>

        </footer>

    </section>

</div>

@endsection
