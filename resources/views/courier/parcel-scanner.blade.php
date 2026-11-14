@extends('layouts.courier')

@section('title', 'Parcel QR Scanner — SARI Rider')
@section('page-title', 'Parcel QR Scanner')
@section('header-title', 'Parcel QR Scanner')
@section('header-subtitle', 'Rider operations')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
@verbatim
/* SARI Rider QR Scanner — immersive gold UI, scoped to rider only. */
body:has(#sariRiderQrPage),body:has(#sariRiderQrPage) #courierMain{background:var(--sari-dark-4d21eb669b, #f4f5f7)!important;color:var(--sari-dark-e23e64dd1e, #1c2738);}
body:has(#sariRiderQrPage) #courierSidebar{background:var(--sari-dark-950baec030, #fff)!important;border-right:1px solid var(--sari-dark-d8c2716225, #e8ecf1)!important;box-shadow:none!important;}
body:has(#sariRiderQrPage) #courierSidebarProfile{background:var(--sari-dark-950baec030, #fff)!important;border-color:var(--sari-dark-d8c2716225, #e6e9ef)!important;}
body:has(#sariRiderQrPage) #courierMain>header{background:var(--sari-dark-950baec030, #fff)!important;border-bottom:1px solid var(--sari-dark-d8c2716225, #e8ecf1)!important;box-shadow:none!important;}
body:has(#sariRiderQrPage) #courierMain>main{padding:10px clamp(10px,1vw,19px) 22px!important;}
@media(min-width:1024px){body:has(#sariRiderQrPage):not(.courier-sidebar-collapsed) #courierMain{padding-left:244px!important;}body:has(#sariRiderQrPage):not(.courier-sidebar-collapsed) #courierSidebar{width:244px!important;}body:has(#sariRiderQrPage).courier-sidebar-collapsed #courierMain{padding-left:92px!important;}}
@media(max-width:1023px){body:has(#sariRiderQrPage) #courierMain{padding-left:0!important;}}
#sariRiderQrPage{--sqr-ink:var(--sari-dark-e23e64dd1e, #172436);--sqr-muted:var(--sari-dark-a63aa16d7c, #8491a2);--sqr-gold:#e0ac28;--sqr-gold-hover:#c99719;--sqr-gold-bright:#f5c751;--sqr-gold-line:rgba(230,179,52,.6);width:100%;max-width:none;margin:0;font:400 12px/1.6 'Poppins',system-ui,sans-serif;color:var(--sqr-ink);}
#sariRiderQrPage *,#sariRiderQrPage *::before,#sariRiderQrPage *::after{box-sizing:border-box;}
#sariRiderQrPage button,#sariRiderQrPage input,#sariRiderQrPage textarea{font:inherit;}
#sariRiderQrPage button:focus-visible,#sariRiderQrPage a:focus-visible,#sariRiderQrPage input:focus-visible,#sariRiderQrPage textarea:focus-visible,#sariRiderQrPage summary:focus-visible{outline:2px solid var(--sqr-gold-bright);outline-offset:3px;}
#sariRiderQrPage [hidden],#sariRiderQrPage .hidden{display:none!important;}
#sariRiderQrPage .sqr-alert{margin-bottom:9px;border-radius:12px;padding:12px 15px;background:var(--sari-dark-4d21eb669b, #eaf9f0);border:1px solid var(--sari-dark-d8c2716225, #cce9d6);color:var(--sari-dark-e23e64dd1e, #276949);font-size:11px;}
#sariRiderQrPage .sqr-shell{width:100%;}
#sariRiderQrPage .sqr-scanner{position:relative;display:grid;grid-template-columns:minmax(0,1fr) minmax(290px,32%);gap:18px;align-items:stretch;padding:17px;background:var(--sari-dark-f3318ab620, #171716);border:1px solid #7b652c;border-radius:19px;box-shadow:0 5px 13px var(--sari-dark-fbd3b3b9f6, rgba(24,26,30,.055)),0 24px 58px var(--sari-dark-a2f72dee3d, rgba(24,30,44,.095));min-height:clamp(635px,calc(100svh - 126px),850px);overflow:hidden;}
#sariRiderQrPage .sqr-camera-zone{position:relative;isolation:isolate;min-width:0;min-height:590px;border-radius:14px;overflow:hidden;background:var(--sari-dark-f3318ab620, #11141a);border:1px solid var(--sari-dark-d8c2716225, #34312c);}
#sariRiderQrPage .sqr-camera-feed{position:absolute;inset:0;overflow:hidden;background:var(--sari-dark-f3318ab620, #11151b);}
#sariRiderQrPage #sariQrReader{position:absolute;inset:0;width:100%;height:100%;overflow:hidden;background:var(--sari-dark-f3318ab620, #11151b);color:#fff;border:0!important;}
#sariRiderQrPage #sariQrReader video{width:100%!important;height:100%!important;object-fit:cover!important;}
#sariRiderQrPage #sariQrReader img{max-width:100%;}
#sariRiderQrPage .sqr-placeholder{position:absolute;inset:0;z-index:2;display:flex;align-items:center;justify-content:center;text-align:center;background:var(--sari-dark-f3318ab620, #11151b);pointer-events:none;padding:25px;}
#sariRiderQrPage .sqr-placeholder-content{transform:translateY(10px);display:grid;justify-items:center;gap:5px;}
#sariRiderQrPage .sqr-placeholder svg{width:44px;height:44px;stroke:var(--sari-dark-0ce263e540, #ddb65b);margin-bottom:12px;}
#sariRiderQrPage .sqr-placeholder strong{font-size:16px;font-weight:500;color:#fff;letter-spacing:-.02em;}
#sariRiderQrPage .sqr-placeholder span{font-size:11px;color:var(--sari-dark-a63aa16d7c, #adb6c1);}
#sariRiderQrPage .sqr-scan-guide{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:min(285px,70%);aspect-ratio:1;z-index:3;pointer-events:none;filter:drop-shadow(0 4px 14px rgba(0,0,0,.24));}
#sariRiderQrPage .sqr-scan-guide span{position:absolute;width:55px;height:55px;border:3px solid #f3c04a;}
#sariRiderQrPage .sqr-scan-guide span:nth-child(1){left:0;top:0;border-right:0;border-bottom:0;border-radius:14px 0 0 0;}
#sariRiderQrPage .sqr-scan-guide span:nth-child(2){right:0;top:0;border-left:0;border-bottom:0;border-radius:0 14px 0 0;}
#sariRiderQrPage .sqr-scan-guide span:nth-child(3){left:0;bottom:0;border-right:0;border-top:0;border-radius:0 0 0 14px;}
#sariRiderQrPage .sqr-scan-guide span:nth-child(4){right:0;bottom:0;border-left:0;border-top:0;border-radius:0 0 14px 0;}
#sariRiderQrPage .sqr-scan-guide i{position:absolute;top:50%;left:0;right:0;height:2px;background:#f5c651;box-shadow:0 0 8px var(--sari-dark-152c2221a5, rgba(245,198,81,.62)),0 0 24px var(--sari-dark-817538610c, rgba(245,198,81,.26));}
#sariRiderQrPage .sqr-camera-head{position:absolute;top:15px;left:15px;right:15px;z-index:5;display:flex;justify-content:space-between;align-items:flex-start;gap:12px;pointer-events:none;}
#sariRiderQrPage .sqr-camera-head-left{display:flex;align-items:center;gap:12px;padding:13px 15px;background:var(--sari-dark-87391f3ed6, rgba(16,18,22,.88));border:1px solid var(--sari-dark-d3797a028a, rgba(255,255,255,.17));border-radius:15px;box-shadow:0 9px 25px var(--sari-dark-3b2338cbf0, rgba(0,0,0,.12));}
#sariRiderQrPage .sqr-head-gold-dot{width:12px;height:12px;flex:none;border-radius:50%;background:#ecc044;box-shadow:0 0 0 5px var(--sari-dark-3e32d50f5e, rgba(236,192,68,.13));}
#sariRiderQrPage .sqr-card-title{font-size:14px;font-weight:600;color:#fff;margin:0;line-height:1.4;letter-spacing:-.018em;}
#sariRiderQrPage .sqr-camera-sub{margin:3px 0 0;font-size:10px;font-weight:400;color:var(--sari-dark-a63aa16d7c, #bdc3cb);}
#sariRiderQrPage .sqr-camera-security{display:flex;align-items:center;gap:7px;padding:11px 12px;background:var(--sari-dark-87391f3ed6, rgba(14,17,21,.88));color:#ecedf0;border:1px solid rgba(236,191,75,.46);border-radius:999px;font-size:10px;white-space:nowrap;}
#sariRiderQrPage .sqr-camera-security svg{width:16px;height:16px;color:var(--sari-dark-9382bf9707, #ecc04a);}
#sariRiderQrPage .sqr-camera-status{position:absolute;top:105px;left:17px;z-index:5;display:inline-flex;align-items:center;gap:8px;background:var(--sari-dark-fbcdab705f, rgba(12,15,20,.78));border:1px solid var(--sari-dark-ab70a14a3e, rgba(255,255,255,.18));border-radius:999px;padding:8px 11px;color:#e5e8ec;font-size:10px;font-weight:500;}
#sariRiderQrPage .sqr-camera-status::before{content:'';display:inline-block;width:7px;height:7px;flex:none;border-radius:50%;background:#aeb5be;}
#sariRiderQrPage .sqr-camera-status[data-state=live]::before{background:#29d48f;box-shadow:0 0 0 3px var(--sari-dark-3e32d50f5e, rgba(41,212,143,.13));}
#sariRiderQrPage .sqr-camera-status[data-state=error]::before{background:#f17879;}
#sariRiderQrPage .sqr-camera-actions{position:absolute;bottom:43px;left:0;right:0;z-index:5;display:flex;align-items:center;justify-content:center;gap:11px;flex-wrap:wrap;padding:12px 20px;}
#sariRiderQrPage .sqr-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:47px;border-radius:11px;padding:11px 18px;font-size:11px;font-weight:600;border:1px solid transparent;cursor:pointer;white-space:nowrap;transition:transform .16s ease,box-shadow .16s ease,background .16s ease;}
#sariRiderQrPage .sqr-button svg{width:17px;height:17px;flex:none;}
#sariRiderQrPage .sqr-button:disabled{cursor:not-allowed;opacity:.48;}
#sariRiderQrPage .sqr-primary{background:#e0ac28;color:#20180a;border-color:#e0ac28;box-shadow:0 6px 20px var(--sari-dark-3135cb7628, rgba(0,0,0,.17));}
#sariRiderQrPage .sqr-primary:hover:not(:disabled){background:#f0bb35;border-color:#f0bb35;transform:translateY(-1px);box-shadow:0 8px 22px var(--sari-dark-e267f3cebc, rgba(0,0,0,.22));}
#sariRiderQrPage .sqr-outline{background:var(--sari-dark-0ed950d48f, rgba(22,22,21,.62));color:#faf6ea;border-color:rgba(238,189,67,.62);}
#sariRiderQrPage .sqr-outline:hover:not(:disabled){background:var(--sari-dark-fbb35de0d1, #292620);}
#sariRiderQrPage .sqr-camera-foot{position:absolute;bottom:14px;left:0;right:0;z-index:4;text-align:center;color:var(--sari-dark-a63aa16d7c, #aeb2b9);font-size:9px;line-height:1.4;margin:0;pointer-events:none;}
#sariRiderQrPage .sqr-right{min-width:0;align-self:center;max-height:100%;overflow:auto;scrollbar-width:thin;display:grid;gap:0;padding:21px 19px 18px;background:var(--sari-dark-269821c871, rgba(28,27,24,.95));border:1px solid rgba(231,188,85,.58);border-radius:18px;box-shadow:0 16px 44px var(--sari-dark-ef4e8433d9, rgba(0,0,0,.19));color:#f8f8f8;}
#sariRiderQrPage .sqr-status-card,#sariRiderQrPage .sqr-assist-card{min-width:0;}
#sariRiderQrPage .sqr-panel-heading{display:flex;align-items:center;gap:11px;min-width:0;}
#sariRiderQrPage .sqr-panel-icon{width:37px;height:37px;flex:none;border-radius:12px;background:var(--sari-dark-fbb35de0d1, #3f3522);color:var(--sari-dark-01583dc20b, #f0bd45);display:grid;place-items:center;}
#sariRiderQrPage .sqr-panel-icon svg{width:20px;height:20px;}
#sariRiderQrPage .sqr-section-title{font-size:13px;color:#fff;font-weight:600;margin:0;letter-spacing:-.02em;}
#sariRiderQrPage .sqr-result{margin-top:17px;background:var(--sari-dark-fbb35de0d1, #34312c);border:1px solid var(--sari-dark-d8c2716225, #50473a);border-radius:13px;padding:18px;}
#sariRiderQrPage .sqr-result-title{margin:0;display:flex;align-items:center;gap:11px;color:#fff;font-size:13px;font-weight:600;}
#sariRiderQrPage .sqr-result-title::before{content:'';width:9px;height:9px;border-radius:50%;background:#edc762;flex:none;box-shadow:0 0 0 3px var(--sari-dark-c33e3d1bb4, rgba(237,199,98,.08));}
#sariRiderQrPage .sqr-result-message{margin:10px 0 0;color:var(--sari-dark-a63aa16d7c, #c4c0b9);font-size:10.5px;line-height:1.75;overflow-wrap:anywhere;}
#sariRiderQrPage .sqr-result[data-state=success]{border-color:#3c8661;background:var(--sari-dark-fbb35de0d1, #24352c);}
#sariRiderQrPage .sqr-result[data-state=success] .sqr-result-title::before{background:#40d78a;}
#sariRiderQrPage .sqr-result[data-state=error]{border-color:#ac5b5d;background:var(--sari-dark-fbb35de0d1, #38292b);}
#sariRiderQrPage .sqr-result[data-state=error] .sqr-result-title::before{background:#ff8d93;}
#sariRiderQrPage .sqr-order{border-top:1px solid var(--sari-dark-70334c7f17, rgba(255,255,255,.13));margin-top:13px;padding-top:12px;}
#sariRiderQrPage .sqr-order-label{display:block;color:var(--sari-dark-a63aa16d7c, #b8ada0);font-size:10px;}
#sariRiderQrPage .sqr-order-number{display:block;margin-top:4px;color:#fff;font-weight:600;overflow-wrap:anywhere;}
#sariRiderQrPage .sqr-order-stage{display:block;margin-top:4px;color:var(--sari-dark-b78aa78968, #f0c451);font-weight:500;font-size:10px;}
#sariRiderQrPage .sqr-next-link{display:inline-flex;margin-top:14px;padding:8px 12px;background:#e0ac28;color:#20180a;border-radius:8px;font-size:10px;font-weight:500;text-decoration:none;}
#sariRiderQrPage .sqr-panel-divider{height:1px;background:rgba(238,205,130,.19);margin:20px 0;}
#sariRiderQrPage .sqr-fallback summary{display:flex;align-items:center;justify-content:space-between;gap:12px;cursor:pointer;list-style:none;}
#sariRiderQrPage .sqr-fallback summary::-webkit-details-marker{display:none;}
#sariRiderQrPage .sqr-chevron{width:16px;height:16px;flex:none;color:#d9c294;transition:transform .16s ease;}
#sariRiderQrPage .sqr-fallback[open] .sqr-chevron{transform:rotate(180deg);}
#sariRiderQrPage .sqr-fallback-copy{margin:10px 0 0 48px;color:var(--sari-dark-a63aa16d7c, #bcb8b1);font-size:10px;}
#sariRiderQrPage .sqr-manual{margin-top:14px;}
#sariRiderQrPage .sqr-manual label{display:block;margin:0 0 7px;font-size:10.5px;color:#eae6de;font-weight:500;}
#sariRiderQrPage .sqr-manual textarea{display:block;width:100%;min-width:0;resize:vertical;border:1px solid var(--sari-dark-d8c2716225, #716555);border-radius:10px;outline:0;background:var(--sari-dark-fbb35de0d1, #493e31);color:#fff;font-size:10.5px;line-height:1.55;padding:12px;}
#sariRiderQrPage .sqr-manual textarea::placeholder{color:var(--sari-dark-a63aa16d7c, #c6b9a9);}
#sariRiderQrPage .sqr-manual textarea:focus{border-color:#e0ac28;box-shadow:0 0 0 3px var(--sari-dark-59ed29fe9d, rgba(224,172,40,.14));}
#sariRiderQrPage .sqr-verify{width:100%;min-height:45px;margin-top:11px;}
#sariRiderQrPage .sqr-help-note{display:flex;align-items:flex-start;gap:9px;margin:18px 0 0;color:var(--sari-dark-a63aa16d7c, #c6bdac);font-size:9.5px;line-height:1.65;}
#sariRiderQrPage .sqr-help-note svg{width:16px;height:16px;flex:none;color:#ecc35d;margin-top:1px;}
/* COD modal remains screen-centered; no gradient fills; gold confirmation. */
#sariRiderQrPage .sqr-modal{position:fixed;inset:0;z-index:1000;display:grid;place-items:center;background:var(--sari-dark-253f8b6fa3, rgba(10,14,20,.72));backdrop-filter:blur(5px);padding:15px;}
#sariRiderQrPage .sqr-dialog{width:min(100%,450px);max-height:92svh;overflow:auto;border-radius:20px;background:var(--sari-dark-950baec030, #fff);border:1px solid var(--sari-dark-d8c2716225, #e8eaf0);box-shadow:0 36px 110px var(--sari-dark-152c2221a5, rgba(5,10,18,.35));padding:28px;}
#sariRiderQrPage .sqr-dialog-top{display:flex;align-items:center;gap:12px;}
#sariRiderQrPage .sqr-dialog-icon{width:46px;height:46px;border-radius:13px;display:grid;place-items:center;flex:none;background:var(--sari-dark-4d21eb669b, #fff4d8);color:var(--sari-dark-bc0de3730f, #b5810b);}
#sariRiderQrPage .sqr-dialog-icon svg{width:24px;height:24px;}
#sariRiderQrPage .sqr-dialog-kicker{font-size:10px;color:var(--sari-dark-bc0de3730f, #aa7909);font-weight:500;margin:0 0 3px;}
#sariRiderQrPage .sqr-dialog-title{font-size:20px;color:var(--sari-dark-e23e64dd1e, #1b2637);letter-spacing:-.03em;font-weight:600;line-height:1.25;margin:0;}
#sariRiderQrPage .sqr-payment-summary{padding:20px;background:var(--sari-dark-950baec030, #f8f9fb);border:1px solid var(--sari-dark-d8c2716225, #e7eaf1);border-radius:13px;margin-top:21px;}
#sariRiderQrPage .sqr-payment-label{color:var(--sari-dark-a63aa16d7c, #778395);font-size:11px;margin:0;}
#sariRiderQrPage .sqr-payment-amount{display:block;color:var(--sari-dark-bc0de3730f, #a6780f);letter-spacing:-.045em;font-size:clamp(31px,5vw,40px);font-weight:600;line-height:1.2;margin:8px 0 4px;overflow-wrap:anywhere;font-variant-numeric:tabular-nums;}
#sariRiderQrPage .sqr-payment-meta{display:flex;justify-content:space-between;gap:8px;border-top:1px solid var(--sari-dark-d8c2716225, #e4e8ee);padding-top:13px;margin-top:14px;color:var(--sari-dark-a63aa16d7c, #778395);font-size:10px;}
#sariRiderQrPage .sqr-payment-meta strong{color:var(--sari-dark-e23e64dd1e, #364457);font-weight:500;overflow-wrap:anywhere;}
#sariRiderQrPage .sqr-cash-affirm{display:flex;align-items:flex-start;gap:10px;border:1px solid var(--sari-dark-d8c2716225, #e0e6ee);border-radius:11px;padding:14px 13px;margin:18px 0 12px;color:var(--sari-dark-e23e64dd1e, #3c4757);font-size:11px;cursor:pointer;line-height:1.6;}
#sariRiderQrPage .sqr-cash-affirm input{width:18px;height:18px;accent-color:#bd8b14;margin:0;flex:none;}
#sariRiderQrPage .sqr-pay-confirm{width:100%;background:#e0ac28;color:#241a08;border:1px solid #e0ac28;border-radius:10px;min-height:48px;padding:11px 15px;font-weight:600;font-size:11px;cursor:pointer;transition:background .16s ease;}
#sariRiderQrPage .sqr-pay-confirm:hover:not(:disabled){background:#efbe3d;}
#sariRiderQrPage .sqr-pay-confirm:disabled{opacity:.47;cursor:not-allowed;}
#sariRiderQrPage .sqr-payment-error{font-size:10.5px;color:var(--sari-dark-a63aa16d7c, #ac3542);background:var(--sari-dark-950baec030, #fff1f2);border-radius:9px;padding:10px;margin:11px 0;}
#sariRiderQrPage .sqr-pay-cancel{display:block;margin:13px auto 0;padding:6px 10px;color:var(--sari-dark-a63aa16d7c, #6e798b);border:0;background:transparent;cursor:pointer;font-size:10px;}
#sariRiderQrPage .sqr-pay-disclaimer{margin:12px 0 0;color:var(--sari-dark-a63aa16d7c, #98a3b0);font-size:9px;text-align:center;line-height:1.65;}
body.sqr-payment-open{overflow:hidden;}
@media(max-width:1250px){#sariRiderQrPage .sqr-scanner{grid-template-columns:minmax(0,1fr) minmax(270px,35%);gap:13px;padding:13px;}#sariRiderQrPage .sqr-right{padding:18px 15px;}#sariRiderQrPage .sqr-camera-security{display:none;}}
@media(max-width:860px){#sariRiderQrPage .sqr-scanner{grid-template-columns:minmax(0,1fr);min-height:0;padding:12px;gap:12px;}#sariRiderQrPage .sqr-camera-zone{min-height:clamp(380px,65svh,650px);}#sariRiderQrPage .sqr-right{align-self:stretch;max-height:none;padding:18px;}}
@media(max-width:540px){body:has(#sariRiderQrPage) #courierMain>main{padding:8px 9px 22px!important;}#sariRiderQrPage .sqr-scanner{border-radius:15px;padding:8px;gap:9px;}#sariRiderQrPage .sqr-camera-zone{min-height:430px;border-radius:11px;}#sariRiderQrPage .sqr-camera-head{top:9px;left:9px;right:9px;}#sariRiderQrPage .sqr-camera-head-left{padding:10px 12px;}#sariRiderQrPage .sqr-card-title{font-size:12px;}#sariRiderQrPage .sqr-camera-sub{font-size:9px;}#sariRiderQrPage .sqr-camera-status{left:auto;right:11px;top:21px;}#sariRiderQrPage .sqr-scan-guide{width:min(285px,70%);}#sariRiderQrPage .sqr-camera-actions{bottom:35px;gap:7px;padding-inline:9px;}#sariRiderQrPage .sqr-button{min-height:43px;font-size:10px;padding:9px 12px;}#sariRiderQrPage .sqr-right{padding:17px 13px;border-radius:13px;}#sariRiderQrPage .sqr-dialog{padding:23px 18px;border-radius:16px;}#sariRiderQrPage .sqr-fallback-copy{margin-left:0;}}
@media(max-width:360px){#sariRiderQrPage .sqr-camera-status{top:81px;right:auto;left:11px;}}
/* v2 fix: full-BLEED LIVE CAMERA, not just a dark parent behind a left-only video.
   The reader is a direct child of .sqr-scanner and covers its ENTIRE area;
   the live status + backup form float OVER that same video on desktop. */
#sariRiderQrPage .sqr-scanner{
    grid-template-columns:minmax(0,1fr) minmax(300px,32%);
    gap:14px;
    padding:0 15px 0 0;
    isolation:isolate;
    background:var(--sari-dark-f3318ab620, #11151b);
    min-height:clamp(660px,calc(100svh - 110px),920px);
}
#sariRiderQrPage .sqr-camera-feed{
    position:absolute;
    inset:0;
    z-index:0;
    width:100%;
    height:100%;
    background:var(--sari-dark-f3318ab620, #11151b);
    border:0;
    overflow:hidden;
    pointer-events:none;
}
#sariRiderQrPage #sariQrReader{
    position:absolute;
    inset:0;
    display:block;
    width:100%!important;
    height:100%!important;
    background:transparent;
}
#sariRiderQrPage #sariQrReader video{
    position:absolute!important;
    inset:0!important;
    display:block!important;
    width:100%!important;
    height:100%!important;
    object-fit:cover!important;
}
#sariRiderQrPage .sqr-placeholder{z-index:1;}
#sariRiderQrPage .sqr-placeholder-content{
    position:absolute;
    top:50%;left:0;
    width:67%;
    transform:translateY(-50%);
}
#sariRiderQrPage .sqr-camera-zone{
    z-index:2;
    position:relative;
    isolation:auto;
    border:0;
    border-radius:0;
    background:transparent;
    overflow:visible;
    min-height:clamp(660px,calc(100svh - 110px),920px);
    pointer-events:none;
}
#sariRiderQrPage .sqr-camera-zone button{pointer-events:auto;}
#sariRiderQrPage .sqr-right{
    position:relative;
    z-index:3;
    background:var(--sari-dark-976b0cda21, rgba(26,25,23,.91));
    backdrop-filter:blur(14px);
    -webkit-backdrop-filter:blur(14px);
    max-height:calc(100% - 24px);
}
#sariRiderQrPage .sqr-camera-security,
#sariRiderQrPage .sqr-camera-head-left,
#sariRiderQrPage .sqr-camera-status{backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);}
/* Mobile: full-width live feed first, then the accessible status panel below. */
@media(max-width:860px){
    #sariRiderQrPage .sqr-scanner{
        display:flex;flex-direction:column;gap:12px;
        padding:10px;min-height:0;
    }
    #sariRiderQrPage .sqr-camera-feed{
        inset:10px 10px auto 10px;
        width:calc(100% - 20px);
        height:clamp(410px,66svh,660px);
        border-radius:13px;
        z-index:0;
    }
    #sariRiderQrPage .sqr-camera-zone{
        flex:none;
        height:clamp(410px,66svh,660px);
        min-height:0;
    }
    #sariRiderQrPage .sqr-placeholder-content{width:100%;}
    #sariRiderQrPage .sqr-right{
        flex:none;align-self:stretch;
        width:100%;max-height:none;
        backdrop-filter:none;
    }
}
@media(max-width:540px){
    #sariRiderQrPage .sqr-scanner{padding:8px;gap:8px;}
    #sariRiderQrPage .sqr-camera-feed{
        inset:8px 8px auto 8px;width:calc(100% - 16px);
        height:clamp(415px,62svh,560px);border-radius:12px;
    }
    #sariRiderQrPage .sqr-camera-zone{height:clamp(415px,62svh,560px);min-height:0;}
}
@media(prefers-reduced-motion:reduce){#sariRiderQrPage *,#sariRiderQrPage *::before,#sariRiderQrPage *::after{transition:none!important;}}
@endverbatim
</style>
@endpush

@section('content')
<div id="sariRiderQrPage">
    @if(session('success'))
        <div class="sqr-alert" role="status">{{ session('success') }}</div>
    @endif
    @include('courier.partials.parcel-qr-scanner-modern', [
        'scanEndpoint' => route('courier.parcel-scanner.scan'),
        'backUrl' => route('courier.dashboard'),
    ])
</div>
@endsection
