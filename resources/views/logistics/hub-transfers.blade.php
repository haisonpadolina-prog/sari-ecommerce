@extends('layouts.logistics')
@section('title', 'Hub Transfers — SARI Logistics')
@section('page-title', 'Hub Transfers')
@push('styles')
<style>
/* Hub Transfers v1 — standalone, page-scoped; no dependency on legacy hub-transfers.css */
#lgHubTransfers {--htv-gold:#d9950b;--htv-ink:#263244;--htv-line:#e3e8ef; width:100%;max-width:1620px;margin-inline:auto;color:var(--htv-ink);font-size:12px;min-width:0}
#lgHubTransfers *,#lgHubTransfers *::before,#lgHubTransfers *::after{box-sizing:border-box}
#lgHubTransfers [hidden]{display:none!important}
#lgHubTransfers button,#lgHubTransfers select,#lgHubTransfers textarea,#lgHubTransfers input{font:inherit}
#lgHubTransfers .htv-page-head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;padding:4px 0 16px;margin-bottom:17px;border:0;border-bottom:1px solid #e1e6ed;background:transparent!important;box-shadow:none!important;border-radius:0!important}
#lgHubTransfers .htv-eyebrow{display:block;margin:0 0 5px;color:#b47d17;font-size:10px;font-weight:750;letter-spacing:.125em;line-height:1.4;text-transform:uppercase}
#lgHubTransfers .htv-page-head h1{margin:0;color:#243144;font-size:clamp(25px,2.3vw,32px);font-weight:780;letter-spacing:-.045em;line-height:1.18}
#lgHubTransfers .htv-page-head h1 span{color:var(--htv-gold)}
#lgHubTransfers .htv-head-actions{display:flex;align-items:center;justify-content:flex-end;flex-wrap:wrap;gap:9px}
#lgHubTransfers .htv-link{display:inline-flex;align-items:center;justify-content:center;min-height:39px;padding:0 10px;color:#64748b;text-decoration:none;font-size:11px;font-weight:700;white-space:nowrap}
#lgHubTransfers .htv-link:hover{color:#263244}
#lgHubTransfers .htv-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:43px;padding:9px 17px;border:1px solid var(--htv-line);border-radius:11px;background:#fff;color:#4a5a6f;text-decoration:none;line-height:1.35;font-size:11px;font-weight:750;white-space:nowrap;cursor:pointer;transition:background-color .14s ease,border-color .14s ease}
#lgHubTransfers .htv-btn-primary{background:#d9950b;color:#fff;border-color:#d9950b;box-shadow:0 5px 14px rgba(217,149,11,.13)}
#lgHubTransfers .htv-btn-primary:hover{background:#c38507;color:#fff;border-color:#c38507}
#lgHubTransfers .htv-head-actions a.htv-btn.htv-btn-primary{display:inline-flex!important;align-items:center!important;justify-content:center!important;min-height:41px!important;padding:9px 17px!important;border:1px solid #d9950b!important;border-radius:11px!important;background:#d9950b!important;color:#fff!important;box-shadow:0 5px 14px rgba(217,149,11,.15)!important;text-decoration:none!important;font-weight:750!important}
#lgHubTransfers .htv-head-actions a.htv-btn.htv-btn-primary:hover{border-color:#c38507!important;background:#c38507!important;color:#fff!important}

#lgHubTransfers .htv-btn-reset{border-color:#dfe5ec;background:#fff;color:#526177;box-shadow:none}
#lgHubTransfers .htv-btn-reset:hover{background:#f4f6f8}
#lgHubTransfers .htv-btn-small{min-height:35px;padding:7px 12px;border-radius:9px;font-size:10px}
#lgHubTransfers .htv-alert{padding:13px 17px;margin:0 0 15px;border:1px solid #e5eaf0;border-radius:11px;background:#fff;font-size:12px}
#lgHubTransfers .htv-alert p{margin:4px 0 0;line-height:1.6}
#lgHubTransfers .htv-alert-good{border-color:#c5e4d3;color:#247a50;background:#f3fbf7}
#lgHubTransfers .htv-alert-error{border-color:#eebcba;color:#ad3d37;background:#fff7f6}
#lgHubTransfers .htv-stats{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr))!important;gap:13px!important;margin:0 0 18px!important;min-width:0}
#lgHubTransfers .htv-stat{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;min-height:111px;padding:16px 17px;border:1px solid #e2e8ef;border-radius:19px;background:#fff;box-shadow:0 9px 26px rgba(15,23,42,.075);min-width:0}
#lgHubTransfers .htv-stat > div{min-width:0}
#lgHubTransfers .htv-stat p{margin:0;color:#5d6c7f;font-size:12px;font-weight:650}
#lgHubTransfers .htv-stat strong{display:block;margin:6px 0;color:#223044;font-size:28px;font-weight:780;line-height:1.1}
#lgHubTransfers .htv-stat > div > span{display:block;color:#97a3b4;font-size:10px;line-height:1.5}
#lgHubTransfers .htv-stat-icon{display:flex!important;flex:0 0 32px!important;align-items:center;justify-content:center;width:32px!important;height:32px!important;padding:0!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important}
#lgHubTransfers .htv-stat-icon svg{display:block;width:30px!important;height:30px!important;stroke:currentColor}
#lgHubTransfers .htv-stat-amber .htv-stat-icon{color:#c48a20!important}
#lgHubTransfers .htv-stat-purple .htv-stat-icon{color:#8875b5!important}
#lgHubTransfers .htv-stat-blue .htv-stat-icon{color:#4a83b8!important}
#lgHubTransfers .htv-stat-green .htv-stat-icon{color:#398660!important}
#lgHubTransfers .htv-section{margin:0 0 23px;min-width:0}
#lgHubTransfers .htv-controls{display:grid;align-items:stretch;gap:11px;margin:0 0 14px;min-width:0}
#lgHubTransfers .htv-controls-ready{grid-template-columns:minmax(0,1fr) 92px}
#lgHubTransfers .htv-controls-manifests{grid-template-columns:minmax(0,1fr) minmax(160px,210px) 138px 92px}
#lgHubTransfers .htv-search{display:flex;align-items:center;position:relative;min-width:0;height:46px;border:1px solid #dfe6ed;border-radius:13px;background:#fff}
#lgHubTransfers .htv-search svg{position:absolute;left:15px;width:17px;height:17px;color:#94a3b8;pointer-events:none}
#lgHubTransfers .htv-search input{display:block;width:100%;height:100%;padding:0 15px 0 44px;border:0!important;outline:0!important;border-radius:13px;background:transparent!important;color:#334155;font-size:12px!important;box-shadow:none!important;min-width:0}
#lgHubTransfers .htv-search:focus-within{border-color:#a2afbf;box-shadow:0 0 0 3px rgba(148,163,184,.12)}
#lgHubTransfers .htv-select select{display:block;width:100%;height:46px;padding:0 12px;border:1px solid #dfe6ed;border-radius:13px;background:#fff;color:#334155;font-size:12px}
#lgHubTransfers .htv-controls>.htv-btn{height:46px}
#lgHubTransfers .htv-surface{width:100%;min-width:0;border:1px solid #e2e8ef;border-radius:20px;overflow:hidden;background:#fff;box-shadow:0 12px 30px rgba(15,23,42,.07)}
#lgHubTransfers .htv-surface-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 21px;border-bottom:1px solid #edf0f4;background:#fff}
#lgHubTransfers .htv-surface-heading h2{margin:0;color:#223044;font-size:18px;font-weight:760;letter-spacing:-.035em}
#lgHubTransfers .htv-count{display:inline-flex;align-items:center;min-height:30px;padding:6px 11px;border:1px solid #e2e9ef;border-radius:10px;background:#f8fafc;color:#718095;font-size:10px;font-weight:750;white-space:nowrap}
#lgHubTransfers .htv-table-shell{width:100%;min-width:0;overflow:visible}
#lgHubTransfers .htv-table{width:100%;table-layout:fixed;border-collapse:collapse;font-size:12px;text-align:left}
#lgHubTransfers .htv-table thead{background:#f5f7f9}
#lgHubTransfers .htv-table th{padding:13px 16px;border-bottom:1px solid #e6ebf0;color:#768396;font-size:10px;font-weight:750;letter-spacing:.055em;text-transform:uppercase}
#lgHubTransfers .htv-table td{padding:15px 16px;border-bottom:1px solid #edf1f5;vertical-align:middle;color:#536174;font-size:11px;overflow-wrap:anywhere;line-height:1.55}
#lgHubTransfers .htv-table tbody tr:hover{background:#f2f4f6!important;box-shadow:none!important}
#lgHubTransfers .htv-table td strong{display:block;color:#28364a;font-size:11px;font-weight:750;line-height:1.55}
#lgHubTransfers .htv-table td p{margin:4px 0 0;color:#8c99a8;font-size:10px;line-height:1.5}
#lgHubTransfers .htv-table th:last-child{white-space:nowrap}
#lgHubTransfers .htv-ready-table th:nth-child(1){width:51%}
#lgHubTransfers .htv-ready-table th:nth-child(2){width:12%}
#lgHubTransfers .htv-ready-table th:nth-child(3){width:18%}
#lgHubTransfers .htv-ready-table th:nth-child(4){width:19%}
#lgHubTransfers .htv-manifest-table th:nth-child(1){width:20%}
#lgHubTransfers .htv-manifest-table th:nth-child(2){width:30%}
#lgHubTransfers .htv-manifest-table th:nth-child(3){width:21%}
#lgHubTransfers .htv-manifest-table th:nth-child(4){width:13%}
#lgHubTransfers .htv-manifest-table th:nth-child(5){width:16%}
#lgHubTransfers .htv-route-arrow{color:#a5b0bc;padding:0 3px}
#lgHubTransfers .htv-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
#lgHubTransfers .htv-eye{display:grid;place-items:center;flex:0 0 30px;width:30px;height:34px;padding:0;border:0;border-radius:9px;background:transparent;color:#68798f;cursor:pointer}
#lgHubTransfers .htv-eye:hover{background:#e9edf2;color:#334155}
#lgHubTransfers .htv-eye svg{width:19px;height:19px}
#lgHubTransfers .htv-muted-action{color:#99a4b0;font-size:10px}
#lgHubTransfers .htv-text-button{display:inline-flex;padding:5px 0;border:0;background:none;color:#aa7517;font-size:11px;font-weight:750;text-decoration:none;cursor:pointer}
#lgHubTransfers .htv-text-button:hover{text-decoration:underline}
#lgHubTransfers .htv-badge{display:inline-flex;align-items:center;gap:7px;min-height:27px;padding:5px 10px;border:1px solid #e2e8f0;border-radius:999px;background:#f7f9fb;color:#69798a;font-size:10px;font-weight:700;white-space:nowrap}
#lgHubTransfers .htv-badge i{width:7px;height:7px;border-radius:50%;background:currentColor}
#lgHubTransfers .htv-badge-amber,#lgHubTransfers .htv-badge-preparing{background:#fff8ec;border-color:#f0dfbc;color:#b37d19}
#lgHubTransfers .htv-badge-in_transit{background:#eef5fb;border-color:#d7e6f4;color:#3b7dad}
#lgHubTransfers .htv-badge-received{background:#eff9f2;border-color:#d3e9da;color:#398660}
#lgHubTransfers .htv-empty{display:flex;align-items:center;justify-content:center;flex-direction:column;padding:45px 18px 35px;text-align:center}
#lgHubTransfers .htv-empty svg{width:58px;height:58px;margin-bottom:14px;color:#acb5bf}
#lgHubTransfers .htv-empty h3{margin:0 0 6px;color:#8191a4;font-size:21px;font-weight:750}
#lgHubTransfers .htv-empty p{margin:0 0 15px;color:#93a1b2;font-size:12px;line-height:1.6}
#lgHubTransfers .htv-footer{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:12px 21px;min-height:58px;color:#7c8b9c;font-size:11px;background:#fff}
#lgHubTransfers .htv-footer label{display:flex;align-items:center;gap:9px}
#lgHubTransfers .htv-footer select{height:35px;padding:0 10px;border:1px solid #dfe6ee;border-radius:10px;color:#334155;background:#fff}
#lgHubTransfers .htv-pagination{display:flex;align-items:center;justify-content:center;gap:7px;flex-wrap:wrap;margin:13px 0 0;min-height:35px}
#lgHubTransfers .htv-pagination button{display:inline-flex;align-items:center;justify-content:center;min-width:35px;height:35px;padding:0 9px;border:1px solid transparent;border-radius:10px;background:#f4f6f8;color:#8190a1;font-size:12px;font-weight:700;cursor:pointer}
#lgHubTransfers .htv-pagination button[aria-current=page]{background:#d09521;color:#fff;border-color:#d09521;box-shadow:0 6px 12px rgba(208,149,33,.15)}
#lgHubTransfers .htv-pagination button:disabled{opacity:.4;cursor:not-allowed}
#lgHubTransfers .htv-overlay{position:fixed!important;inset:0!important;z-index:99999!important;display:flex;justify-content:center;align-items:center;padding:20px;background:rgba(17,24,39,.56);overflow-y:auto}
#lgHubTransfers .htv-overlay[hidden]{display:none!important}
#lgHubTransfers .htv-dialog{display:flex;flex-direction:column;width:min(700px,100%);max-height:88dvh;overflow:hidden;border:1px solid #e1e8ef;border-radius:20px;background:#fff;box-shadow:0 25px 75px rgba(15,23,42,.24)}
#lgHubTransfers .htv-dialog-small{width:min(510px,100%)}
#lgHubTransfers .htv-dialog-header{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:21px 24px 18px;border-bottom:1px solid #edf1f5;background:#fff}
#lgHubTransfers .htv-dialog-header h2{margin:4px 0 0;color:#253247;font-size:23px;line-height:1.25;font-weight:770;letter-spacing:-.03em}
#lgHubTransfers .htv-dialog-header p{margin:6px 0 0;color:#8493a4;font-size:12px;line-height:1.55;overflow-wrap:anywhere}
#lgHubTransfers .htv-close{display:grid;place-items:center;flex:0 0 38px;width:38px;height:38px;padding:0;border:1px solid #dfe6ed;border-radius:10px;background:#fff;color:#607184;font-size:25px;cursor:pointer}
#lgHubTransfers .htv-dialog-body{min-height:0;overflow-y:auto;padding:20px 24px;scrollbar-width:none;overscroll-behavior:contain}
#lgHubTransfers .htv-dialog-body::-webkit-scrollbar{width:0;height:0}
#lgHubTransfers .htv-dialog-footer{display:flex;justify-content:flex-end;gap:10px;padding:15px 24px;border-top:1px solid #edf1f5;background:#fff}
#lgHubTransfers .htv-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px}
#lgHubTransfers .htv-info{min-width:0;padding:13px 15px;border:1px solid #e7ecf2;border-radius:12px;background:#f8fafc}
#lgHubTransfers .htv-info-full{grid-column:1/-1}
#lgHubTransfers .htv-info label,#lgHubTransfers .htv-form-grid>label>span{display:block;margin:0 0 6px;color:#8290a1;font-size:10px;font-weight:750;letter-spacing:.04em;text-transform:uppercase}
#lgHubTransfers .htv-info strong{display:block;color:#344155;font-size:12px;font-weight:750;line-height:1.55;overflow-wrap:anywhere}
#lgHubTransfers .htv-info p{margin:4px 0 0;color:#758397;font-size:11px;line-height:1.6;overflow-wrap:anywhere}
#lgHubTransfers .htv-parcels{display:grid;gap:8px;max-height:250px;overflow:auto;scrollbar-width:none}
#lgHubTransfers .htv-parcels>div{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;border:1px solid #e6ebf1;border-radius:10px;padding:10px;background:#fff}
#lgHubTransfers .htv-parcels strong{font-size:11px}
#lgHubTransfers .htv-parcels span{font-size:10px;color:#78889a}
#lgHubTransfers .htv-parcels b{font-size:10px;font-weight:750;color:#3d4c5f}
#lgHubTransfers .htv-manifest-summary{display:flex;justify-content:space-between;flex-wrap:wrap;gap:7px;padding:12px 14px;margin-bottom:18px;border-radius:12px;background:#f3f6f9;color:#607286;font-size:11px}
#lgHubTransfers .htv-manifest-summary strong{color:#2d3e51}
#lgHubTransfers .htv-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
#lgHubTransfers .htv-form-grid label{min-width:0}
#lgHubTransfers .htv-form-grid .htv-span-2{grid-column:1/-1}
#lgHubTransfers .htv-form-grid label b{color:#b47d17}
#lgHubTransfers .htv-form-grid input,#lgHubTransfers .htv-form-grid select,#lgHubTransfers .htv-form-grid textarea{display:block;width:100%;min-height:43px;padding:10px 12px;border:1px solid #dce4eb;border-radius:11px;background:#fff;color:#334155;outline:0;font-size:12px}
#lgHubTransfers .htv-form-grid textarea{resize:vertical;min-height:80px}
#lgHubTransfers .htv-form-grid :is(input,select,textarea):focus{border-color:#a0aec0;box-shadow:0 0 0 3px rgba(148,163,184,.13)}
#lgHubTransfers .htv-dialog-help{margin:12px 0 0;color:#77869a;font-size:12px;line-height:1.65}
#lgHubTransfers :is(button,a,input,select,textarea):focus-visible{outline:2px solid #8aa1bd;outline-offset:2px}
@media(max-width:1150px){#lgHubTransfers .htv-controls-manifests{grid-template-columns:minmax(0,1fr) 160px 118px 85px}#lgHubTransfers .htv-manifest-table th:nth-child(1){width:22%}#lgHubTransfers .htv-manifest-table th:nth-child(2){width:28%}#lgHubTransfers .htv-manifest-table th:nth-child(3){width:19%}#lgHubTransfers .htv-manifest-table th:nth-child(4){width:14%}#lgHubTransfers .htv-manifest-table th:nth-child(5){width:17%}}
@media(max-width:960px){#lgHubTransfers .htv-stats{grid-template-columns:repeat(2,minmax(0,1fr))!important}#lgHubTransfers .htv-controls-manifests{grid-template-columns:repeat(2,minmax(0,1fr))}#lgHubTransfers .htv-table thead{display:none}#lgHubTransfers .htv-table,#lgHubTransfers .htv-table tbody{display:block;width:100%}#lgHubTransfers .htv-table tbody tr{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:17px;border-bottom:1px solid #edf1f5}#lgHubTransfers .htv-table td{display:block;padding:0;border:0;min-width:0}#lgHubTransfers .htv-table td::before{content:attr(data-label);display:block;margin-bottom:5px;color:#8190a0;font-size:10px;letter-spacing:.04em;text-transform:uppercase;font-weight:750}#lgHubTransfers .htv-table td:last-child{grid-column:1/-1}#lgHubTransfers .htv-table td:last-child .htv-actions{justify-content:flex-start}#lgHubTransfers .htv-manifest-table td:nth-child(2){grid-row:2;grid-column:1/-1}#lgHubTransfers .htv-manifest-table td:nth-child(3){grid-row:1;grid-column:2}}
@media(max-width:620px){#lgHubTransfers .htv-page-head{align-items:stretch;flex-direction:column;gap:10px}#lgHubTransfers .htv-head-actions{justify-content:flex-start}#lgHubTransfers .htv-stats{gap:9px!important}#lgHubTransfers .htv-stat{padding:13px;min-height:108px;border-radius:15px}#lgHubTransfers .htv-stat strong{font-size:24px}#lgHubTransfers .htv-stat p{font-size:11px}#lgHubTransfers .htv-stat-icon{flex-basis:27px!important;width:27px!important;height:27px!important}#lgHubTransfers .htv-stat-icon svg{width:26px!important;height:26px!important}#lgHubTransfers .htv-controls-ready{grid-template-columns:1fr 80px}#lgHubTransfers .htv-controls-manifests{grid-template-columns:1fr 1fr}#lgHubTransfers .htv-controls-manifests .htv-search{grid-column:1/-1}#lgHubTransfers .htv-controls-manifests .htv-select{grid-column:1/-1}#lgHubTransfers .htv-surface-heading{padding:15px}#lgHubTransfers .htv-surface-heading h2{font-size:16px}#lgHubTransfers .htv-table tbody tr{grid-template-columns:1fr;gap:13px}#lgHubTransfers .htv-manifest-table td:nth-child(2),#lgHubTransfers .htv-manifest-table td:nth-child(3){grid-row:auto;grid-column:auto}#lgHubTransfers .htv-table td:last-child{grid-column:auto}#lgHubTransfers .htv-footer{padding:12px 15px;flex-wrap:wrap}#lgHubTransfers .htv-dialog-header{padding:17px}#lgHubTransfers .htv-dialog-body{padding:16px}#lgHubTransfers .htv-dialog-footer{padding:14px 17px}#lgHubTransfers .htv-info-grid,#lgHubTransfers .htv-form-grid{grid-template-columns:1fr}#lgHubTransfers .htv-form-grid .htv-span-2{grid-column:auto}#lgHubTransfers .htv-dialog-header h2{font-size:20px}}
@media(prefers-reduced-motion:reduce){#lgHubTransfers *{transition:none!important;animation:none!important}}

/* v2 — unified tabs: the workflow has ONE visual surface, not stacked cards */
#lgHubTransfers .htv-unified{min-width:0;border:1px solid #e2e8ef;border-radius:19px;background:#fff;box-shadow:0 12px 30px rgba(15,23,42,.07);overflow:hidden}
#lgHubTransfers .htv-unified-header{display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;padding:17px 20px 0;border-bottom:1px solid #e8edf2;background:#fff}
#lgHubTransfers .htv-unified-title{min-width:0;padding-bottom:16px}
#lgHubTransfers .htv-unified-title h2{margin:0;color:#27364b;font-size:18px;line-height:1.3;font-weight:780;letter-spacing:-.03em}
#lgHubTransfers .htv-unified-title p{margin:4px 0 0;color:#8795a7;font-size:11px}
/* v3 — text-only navigation: selection is communicated by gold typography, never pills or bars */
#lgHubTransfers .htv-tablist{display:flex;align-items:center;gap:5px;flex-wrap:wrap;max-width:100%;align-self:stretch}
#lgHubTransfers .htv-tab{position:relative;display:inline-flex;justify-content:center;align-items:center;gap:6px;min-height:44px;padding:10px 13px;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:#768497!important;font-size:12px;font-weight:700;white-space:nowrap;cursor:pointer;transition:color .14s ease}
#lgHubTransfers .htv-tab:hover{color:#425266!important}
#lgHubTransfers .htv-tab[aria-selected="true"],#lgHubTransfers .htv-tab[aria-selected="true"]:hover{color:#bf8314!important;background:transparent!important;border:0!important;box-shadow:none!important}
#lgHubTransfers .htv-tab-count{display:inline!important;min-width:0!important;width:auto!important;height:auto!important;margin:0!important;padding:0!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:inherit!important;font-size:inherit!important;font-weight:750;line-height:inherit}
#lgHubTransfers .htv-tab[aria-selected="true"] .htv-tab-count{color:inherit!important;background:transparent!important}
#lgHubTransfers .htv-unified .htv-panel{margin:0!important;padding:17px 0 0;min-width:0}
#lgHubTransfers .htv-unified .htv-panel[hidden]{display:none!important}
#lgHubTransfers .htv-unified .htv-controls{margin:0 20px 17px}
#lgHubTransfers .htv-unified .htv-surface{border:0!important;border-radius:0!important;box-shadow:none!important;overflow:visible}
#lgHubTransfers .htv-unified .htv-surface-heading{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
#lgHubTransfers .htv-unified .htv-table th:first-child,#lgHubTransfers .htv-unified .htv-table td:first-child{padding-left:22px}
#lgHubTransfers .htv-unified .htv-table th:last-child,#lgHubTransfers .htv-unified .htv-table td:last-child{padding-right:22px}
#lgHubTransfers .htv-unified .htv-footer{border-top:1px solid #edf1f5;padding:10px 21px;min-height:53px}
#lgHubTransfers .htv-unified .htv-pagination{margin:0;padding:12px 15px 17px;min-height:0}
#lgHubTransfers .htv-unified .htv-pagination:empty{display:none!important}
#lgHubTransfers .htv-unified .htv-empty{padding:32px 18px 39px;min-height:228px}
#lgHubTransfers .htv-unified .htv-empty p{max-width:410px}
#lgHubTransfers .htv-ready-zero{padding-top:22px!important}
@media(max-width:800px){
 #lgHubTransfers .htv-unified-header{padding:16px 15px 0;align-items:stretch;gap:5px;flex-direction:column}
 #lgHubTransfers .htv-unified-title{padding-bottom:10px}
 #lgHubTransfers .htv-unified .htv-controls{margin:0 14px 15px}
 #lgHubTransfers .htv-tablist{width:100%;gap:0;flex-wrap:nowrap}
 #lgHubTransfers .htv-tab{flex:1;min-width:0;padding:12px 8px;font-size:11px;gap:6px;white-space:normal;line-height:1.25}
 #lgHubTransfers .htv-unified .htv-table th:first-child,#lgHubTransfers .htv-unified .htv-table td:first-child{padding-left:0}
 #lgHubTransfers .htv-unified .htv-table th:last-child,#lgHubTransfers .htv-unified .htv-table td:last-child{padding-right:0}
}
@media(max-width:620px){
 #lgHubTransfers .htv-unified-header{padding:14px 12px 0}
 #lgHubTransfers .htv-unified-title h2{font-size:16px}
 #lgHubTransfers .htv-tab{font-size:10px}
 #lgHubTransfers .htv-tab-count{height:21px;min-width:21px;padding:0 5px}
 #lgHubTransfers .htv-unified .htv-panel{padding-top:13px}
 #lgHubTransfers .htv-unified .htv-controls{margin:0 12px 12px}
 #lgHubTransfers .htv-unified .htv-table tbody tr{padding:15px}
 #lgHubTransfers .htv-unified .htv-footer{padding:10px 14px}
}


/* v4 — Sorting Facilities parity: tab header and search filters OUTSIDE the floating table card. */
#lgHubTransfers .htv-unified{
  overflow:visible!important;
  border:0!important;
  border-radius:0!important;
  background:transparent!important;
  box-shadow:none!important;
}
#lgHubTransfers .htv-unified-header{
  padding:3px 0 11px!important;
  margin:0 0 13px;
  border:0!important;
  background:transparent!important;
  align-items:center;
}
#lgHubTransfers .htv-unified-title{padding:0!important}
#lgHubTransfers .htv-unified .htv-panel{
  margin:0!important;
  padding:0!important;
  min-width:0;
}
#lgHubTransfers .htv-unified .htv-panel[hidden]{display:none!important}
#lgHubTransfers .htv-unified .htv-controls{
  display:grid;
  margin:0 0 13px!important;
  padding:0!important;
  gap:11px;
  border:0!important;
  background:transparent!important;
  box-shadow:none!important;
}
#lgHubTransfers .htv-unified .htv-surface{
  overflow:hidden!important;
  border:1px solid #e2e8ef!important;
  border-radius:18px!important;
  background:#fff!important;
  box-shadow:0 12px 30px rgba(15,23,42,.065)!important;
}
#lgHubTransfers .htv-unified .htv-surface-heading{
  position:static!important;
  display:flex!important;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  width:auto!important;
  height:auto!important;
  padding:14px 19px!important;
  margin:0!important;
  overflow:visible!important;
  clip:auto!important;
  white-space:normal!important;
  border:0!important;
  border-bottom:1px solid #edf1f5!important;
  background:#fff!important;
}
#lgHubTransfers .htv-unified .htv-surface-heading h2{font-size:16px}
#lgHubTransfers .htv-unified .htv-pagination{padding:10px 0 3px!important;margin:0!important}
#lgHubTransfers .htv-unified .htv-panel>.htv-empty{
  min-height:225px;
  padding:28px 16px!important;
  border:1px solid #e2e8ef;
  border-radius:18px;
  background:#fff;
  box-shadow:0 12px 30px rgba(15,23,42,.065);
}
#lgHubTransfers .htv-route-arrow{padding:0 4px;color:#a2afbd;font-weight:800}
/* Keep the previously requested Last Mile actions solid gold even when shared CSS loads later. */
#lgHubTransfers .htv-actions a.htv-btn.htv-btn-primary{
  display:inline-flex!important;align-items:center!important;justify-content:center!important;
  padding:7px 12px!important;min-height:35px!important;
  border:1px solid #d9950b!important;border-radius:9px!important;
  background:#d9950b!important;color:#fff!important;
  box-shadow:0 4px 10px rgba(217,149,11,.12)!important;text-decoration:none!important;
}
#lgHubTransfers .htv-actions a.htv-btn.htv-btn-primary:hover{
  background:#c38507!important;border-color:#c38507!important;color:#fff!important;
}
@media(max-width:800px){
 #lgHubTransfers .htv-unified-header{padding:0 0 8px!important;gap:8px;margin-bottom:12px}
 #lgHubTransfers .htv-unified .htv-controls{margin:0 0 12px!important}
 #lgHubTransfers .htv-unified .htv-surface-heading{padding:12px 14px!important}
}
@media(max-width:620px){
 #lgHubTransfers .htv-unified-header{padding:0 0 8px!important}
 #lgHubTransfers .htv-unified .htv-controls{margin:0 0 11px!important}
 #lgHubTransfers .htv-unified .htv-panel>.htv-empty{padding:24px 12px!important}
}

</style>
@endpush

@section('content')
<div id="lgHubTransfers" class="sari-logistics-ui htv-page" data-transfer-workspace>
    <header class="htv-page-head">
        <div><p class="htv-eyebrow">LINEHAUL OPERATIONS</p><h1>Hub <span>Transfers</span></h1></div>
        <nav class="htv-head-actions" aria-label="Related Logistics pages">
            <a href="{{ route('logistics.parcel-scanner') }}" class="htv-link">Scan Parcel QR</a>
            <a href="{{ route('logistics.parcel-sorting') }}" class="htv-link">&lt; Parcel Sorting</a>
            <a href="{{ route('logistics.last-mile-assignment') }}" class="htv-btn htv-btn-primary">Last Mile &gt;</a>
        </nav>
    </header>

    @if(session('success'))<div class="htv-alert htv-alert-good" role="status"><strong>Hub transfer updated</strong><p>{{ session('success') }}</p></div>@endif
    @if($errors->any())<div class="htv-alert htv-alert-error" role="alert"><strong>Could not update Hub Transfer</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    @php
        $summaryCards = [
            ['Ready for Linehaul', $stats['ready_parcels'], $stats['ready_routes'] . ' route groups', 'amber'],
            ['Preparing', $stats['preparing'], 'Manifests awaiting departure', 'purple'],
            ['In Transit', $stats['in_transit'], 'Vehicles currently moving', 'blue'],
            ['Received Today', $stats['received_today'], 'Destination handoffs completed', 'green'],
        ];
        $receivedManifestCount = $transfers->where('status', 'received')->count();
    @endphp

    <section class="htv-stats" aria-label="Hub transfer overview">
        @foreach($summaryCards as [$label, $value, $note, $tone])
            <article class="htv-stat htv-stat-{{ $tone }}">
                <div><p>{{ $label }}</p><strong>{{ number_format($value) }}</strong><span>{{ $note }}</span></div>
                <span class="htv-stat-icon" aria-hidden="true">
                    @switch($tone)
                        @case('amber')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="7" height="7" rx="1"/><rect x="14" y="4" width="7" height="7" rx="1"/><rect x="8.5" y="14" width="7" height="7" rx="1"/></svg>@break
                        @case('purple')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>@break
                        @case('blue')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h12v10H3zM15 10h3l3 3v4h-6"/><circle cx="7.5" cy="18" r="1.5"/><circle cx="18" cy="18" r="1.5"/></svg>@break
                        @default<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                    @endswitch
                </span>
            </article>
        @endforeach
    </section>

    {{-- Unified linehaul workspace — select one workflow at a time. --}}
    @php
        $hasReadyRoutes = count($readyGroups) > 0;
        $defaultTransferTab = $hasReadyRoutes ? 'ready' : 'manifests';
    @endphp
    <div class="htv-unified" data-htv-tabs data-default-tab="{{ $defaultTransferTab }}">
        <header class="htv-unified-header">
            <div class="htv-unified-title">
                <h2>Transfer Operations</h2>
                <p>Ready routes and active manifests</p>
            </div>
            <div class="htv-tablist" role="tablist" aria-label="Hub transfer workspace">
                <button id="htvTabReady" type="button" class="htv-tab" role="tab" data-htv-tab="ready" aria-controls="htvPanelReady" aria-selected="{{ $defaultTransferTab === 'ready' ? 'true' : 'false' }}" tabindex="{{ $defaultTransferTab === 'ready' ? 0 : -1 }}">
                    Ready for Linehaul <span class="htv-tab-count">{{ count($readyGroups) }}</span>
                </button>
                <button id="htvTabManifests" type="button" class="htv-tab" role="tab" data-htv-tab="manifests" aria-controls="htvPanelManifests" aria-selected="{{ $defaultTransferTab === 'manifests' ? 'true' : 'false' }}" tabindex="{{ $defaultTransferTab === 'manifests' ? 0 : -1 }}">
                    Transfer Manifests <span class="htv-tab-count">{{ $transfers->count() }}</span>
                </button>
            </div>
        </header>

    {{-- READY ROUTES — compact content; no search or pager if zero routes --}}
    <section id="htvPanelReady" class="htv-section htv-panel" data-htv-panel="ready" role="tabpanel" aria-labelledby="htvTabReady" @if($defaultTransferTab !== 'ready') hidden @endif>
        @if($hasReadyRoutes)
        <div class="htv-controls htv-controls-ready">
            <label class="htv-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg><span class="sr-only">Search ready route groups</span><input data-ready-search type="search" autocomplete="off" placeholder="Search ready route, order, Seller…"></label>
            <button class="htv-btn htv-btn-reset" type="button" data-ready-clear>Reset</button>
        </div>
        <div class="htv-surface">
            <div class="htv-surface-heading"><h2 id="htvReadyHeading">Ready for Linehaul</h2><span class="htv-count">{{ count($readyGroups) }} ROUTES</span></div>
            <div class="htv-table-shell">
                <table class="htv-table htv-ready-table">
                    <thead><tr><th scope="col">Route</th><th scope="col">Parcels</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead>
                    <tbody>
                    @foreach($readyGroups as $group)
                        @php
                            $search = collect([$group['origin_name'], $group['origin_municipality'], $group['destination_name'], $group['destination_municipality']]);
                            foreach ($group['orders'] as $order) {
                                $search->push($order->order_number);
                                $search->push($order->buyer_name);
                                $search->push($order->seller?->store_name);
                            }
                        @endphp
                        <tr data-ready-group data-search="{{ $search->filter()->implode(' ') }}">
                            <td data-label="Route"><strong>{{ $group['origin_name'] }} <span class="htv-route-arrow">&gt;</span> {{ $group['destination_name'] }}</strong><p>{{ $group['origin_municipality'] ?: 'Origin hub' }} &gt; {{ $group['destination_municipality'] ?: 'Destination hub' }}</p></td>
                            <td data-label="Parcels"><strong>{{ $group['parcel_count'] }}</strong><p>Sorted parcels</p></td>
                            <td data-label="Status"><span class="htv-badge htv-badge-amber"><i></i>Ready to Manifest</span></td>
                            <td data-label="Action"><div class="htv-actions">
                                <button type="button" class="htv-eye" data-open-ready-details aria-label="View ready route details" title="View route details"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.7-6.5 10-6.5S22 12 22 12s-3.7 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
                                <button type="button" class="htv-btn htv-btn-primary htv-btn-small" data-open-modal="htv-create-{{ $loop->index }}">Create Manifest</button>
                            </div>
                            <template data-ready-details-template>
                                <div class="htv-info"><label>Origin Hub</label><strong>{{ $group['origin_name'] }}</strong><p>{{ $group['origin_municipality'] }}</p></div>
                                <div class="htv-info"><label>Destination Hub</label><strong>{{ $group['destination_name'] }}</strong><p>{{ $group['destination_municipality'] }}</p></div>
                                <div class="htv-info htv-info-full"><label>Included Parcels · {{ $group['parcel_count'] }}</label><div class="htv-parcels">
                                @foreach($group['parcels'] as $parcel)
                                    <div><strong>{{ $parcel->order?->order_number ?: 'Order unavailable' }}</strong><span>{{ $parcel->order?->buyer_name ?: 'Buyer not recorded' }} · Zone {{ $parcel->sorting_zone ?: '—' }}</span><b>₱{{ number_format((float) ($parcel->order?->total ?? 0), 2) }}</b></div>
                                @endforeach
                                </div></div>
                            </template></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="htv-empty" data-ready-empty hidden><h3>No Matching Route Groups</h3><p>Try another order, Seller, or hub.</p><button type="button" class="htv-text-button" data-ready-clear>Clear search</button></div>
            <footer class="htv-footer"><span data-ready-visible-count aria-live="polite">{{ count($readyGroups) }} route groups</span><label>Rows per page <select data-ready-page-size><option value="10">10</option><option value="20">20</option><option value="50">50</option></select></label></footer>
        </div>
        <nav class="htv-pagination" data-ready-pagination aria-label="Ready routes pages"></nav>
        @else
            <div class="htv-empty htv-ready-zero">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 7 9-5 9 5v10l-9 5-9-5zM3 7l9 5 9-5m-9 5v10"/></svg>
                <h3>No parcels ready for linehaul</h3>
                <p>Sorted parcels will appear here when they are ready for a hub transfer.</p>
                <a href="{{ route('logistics.parcel-sorting') }}" class="htv-btn htv-btn-primary">View Parcel Sorting &gt;</a>
            </div>
        @endif
    </section>

    {{-- TRANSFER MANIFESTS — shown in the same card via tabs --}}
    <section id="htvPanelManifests" class="htv-section htv-panel" data-htv-panel="manifests" role="tabpanel" aria-labelledby="htvTabManifests" @if($defaultTransferTab !== 'manifests') hidden @endif>
        @if($transfers->isNotEmpty())
        <div class="htv-controls htv-controls-manifests">
            <label class="htv-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg><span class="sr-only">Search transfers</span><input data-transfer-search type="search" autocomplete="off" placeholder="Search transfer, driver, hub, vehicle…"></label>
            <label class="htv-select"><span class="sr-only">Filter transfer status</span><select data-transfer-status><option value="all">All Status</option><option value="preparing">Preparing</option><option value="in_transit">In Transit</option><option value="received">Received</option></select></label>
            <button class="htv-btn htv-btn-primary" type="button" data-transfer-apply>Apply Filter</button>
            <button class="htv-btn htv-btn-reset" type="button" data-transfer-clear>Reset</button>
        </div>
        <div class="htv-surface">
            <div class="htv-surface-heading"><h2 id="htvManifestHeading">Transfer Manifests</h2><span class="htv-count">{{ $transfers->count() }} TRANSFERS</span></div>
            <div class="htv-table-shell">
                <table class="htv-table htv-manifest-table">
                    <thead><tr><th scope="col">Manifest</th><th scope="col">Route</th><th scope="col">Driver / Vehicle</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead>
                    <tbody>
                    @foreach($transfers as $transfer)
                        @php
                            $items = $transferParcels->get($transfer->id, collect());
                            $search = implode(' ', [$transfer->transfer_code, $transfer->origin_name, $transfer->destination_name, $transfer->driver_name, $transfer->vehicle_type, $transfer->vehicle_plate]);
                            $canDispatch = $transfer->status === 'preparing' && (! $hubFacility || (int) $transfer->origin_facility_id === (int) $hubFacility->id);
                            $canReceive = $transfer->status === 'in_transit' && (! $hubFacility || (int) $transfer->destination_facility_id === (int) $hubFacility->id);
                        @endphp
                        <tr data-transfer-row data-state="{{ $transfer->status }}" data-search="{{ $search }}">
                            <td data-label="Manifest"><strong>{{ $transfer->transfer_code }}</strong><p>{{ $items->count() }} {{ $items->count() === 1 ? 'parcel' : 'parcels' }} · {{ $transfer->created_at ? \Illuminate\Support\Carbon::parse($transfer->created_at)->timezone('Asia/Manila')->format('M d, Y') : 'Date not recorded' }}</p></td>
                            <td data-label="Route"><strong>{{ $transfer->origin_municipality ?: $transfer->origin_name }} <span class="htv-route-arrow">&gt;</span> {{ $transfer->destination_municipality ?: $transfer->destination_name }}</strong><p>{{ $transfer->origin_name }} &gt; {{ $transfer->destination_name }}</p></td>
                            <td data-label="Driver / Vehicle"><strong>{{ $transfer->driver_name }}</strong><p>{{ $transfer->vehicle_type }}{{ $transfer->vehicle_plate ? ' · ' . $transfer->vehicle_plate : '' }}</p></td>
                            <td data-label="Status"><span class="htv-badge htv-badge-{{ $transfer->status }}"><i></i>{{ $transfer->status === 'in_transit' ? 'In Transit' : ucfirst($transfer->status) }}</span></td>
                            <td data-label="Action"><div class="htv-actions">
                                <button type="button" class="htv-eye" data-open-transfer-details title="View manifest details" aria-label="View details of {{ $transfer->transfer_code }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.7-6.5 10-6.5S22 12 22 12s-3.7 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
                                @if($canDispatch)<button type="button" class="htv-btn htv-btn-primary htv-btn-small" data-open-modal="htv-action-{{ $transfer->id }}">Dispatch</button>
                                @elseif($canReceive)<button type="button" class="htv-btn htv-btn-primary htv-btn-small" data-open-modal="htv-action-{{ $transfer->id }}">Receive</button>
                                @elseif($transfer->status === 'received')<a href="{{ route('logistics.last-mile-assignment') }}" class="htv-btn htv-btn-primary htv-btn-small">Last Mile &gt;</a>
                                @else<span class="htv-muted-action">{{ $transfer->status === 'preparing' ? 'Origin only' : 'Destination only' }}</span>
                                @endif
                            </div>
                            <template data-transfer-details-template>
                                <div class="htv-info"><label>Origin Hub</label><strong>{{ $transfer->origin_name }}</strong><p>{{ $transfer->origin_municipality }}</p></div>
                                <div class="htv-info"><label>Destination Hub</label><strong>{{ $transfer->destination_name }}</strong><p>{{ $transfer->destination_municipality }}</p></div>
                                <div class="htv-info"><label>Vehicle</label><strong>{{ $transfer->vehicle_type }}</strong><p>{{ $transfer->vehicle_plate ?: 'No plate recorded' }}</p></div>
                                <div class="htv-info"><label>Driver</label><strong>{{ $transfer->driver_name }}</strong></div>
                                <div class="htv-info"><label>Created</label><strong>{{ $transfer->created_at ? \Illuminate\Support\Carbon::parse($transfer->created_at)->timezone('Asia/Manila')->format('M d, Y · g:i A') : 'Not recorded' }}</strong></div>
                                <div class="htv-info"><label>Transfer Status</label><strong>{{ strtoupper(str_replace('_', ' ', $transfer->status)) }}</strong><p>@if($transfer->dispatched_at)Dispatched {{ \Illuminate\Support\Carbon::parse($transfer->dispatched_at)->timezone('Asia/Manila')->format('M d, Y · g:i A') }}@else Awaiting next checkpoint @endif</p></div>
                                <div class="htv-info htv-info-full"><label>Manifest Parcels · {{ $items->count() }}</label><div class="htv-parcels">
                                    @forelse($items as $item)<div><strong>{{ $item->order_number }}</strong><span>{{ $item->buyer_name }} · {{ $item->sorting_zone ?: 'No zone' }}</span><b>{{ strtoupper(str_replace('_', ' ', $item->parcel_status)) }}</b></div>
                                    @empty<p>No parcel details available.</p>@endforelse
                                </div></div>
                                @if($transfer->notes)<div class="htv-info htv-info-full"><label>Transfer Note</label><p>{{ $transfer->notes }}</p></div>@endif
                            </template></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="htv-empty" data-transfer-empty hidden><h3>No Matching Transfer Manifests</h3><p>Try another code, hub, vehicle, driver, or status.</p><button type="button" class="htv-text-button" data-transfer-clear>Clear filters</button></div>
            <footer class="htv-footer"><span data-transfer-visible-count aria-live="polite">{{ $transfers->count() }} transfers</span><label>Rows per page <select data-transfer-page-size><option value="10">10</option><option value="20">20</option><option value="50">50</option></select></label></footer>
        </div>
        <nav class="htv-pagination" data-transfer-pagination aria-label="Transfer manifest pages"></nav>
        @else
            <div class="htv-empty htv-transfers-zero">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 7 9-5 9 5v10l-9 5-9-5zM3 7l9 5 9-5m-9 5v10"/></svg>
                <h3>No transfer manifests yet</h3>
                <p>Once a linehaul manifest is created, it will appear here for tracking.</p>
                @if($hasReadyRoutes)<button type="button" class="htv-btn htv-btn-primary" data-htv-switch="ready">View Ready Routes &gt;</button>@endif
            </div>
        @endif
    </section>
    </div>{{-- /unified transfer workspace --}}

    {{-- CREATE MANIFEST MODALS: preserve all original form names and Laravel routes --}}
    @foreach($readyGroups as $group)
        <div class="htv-overlay" id="htv-create-{{ $loop->index }}" data-htv-modal hidden>
            <div class="htv-dialog htv-dialog-form" role="dialog" aria-modal="true" aria-label="Create Transfer Manifest" tabindex="-1">
                <header class="htv-dialog-header"><div><span class="htv-eyebrow">LINEHAUL PLANNING</span><h2>Create Transfer Manifest</h2><p>{{ $group['origin_name'] }} &gt; {{ $group['destination_name'] }}</p></div><button type="button" class="htv-close" data-close-modal aria-label="Close">×</button></header>
                <form method="POST" action="{{ route('logistics.hub-transfers.store') }}" data-create-transfer>
                    @csrf
                    @foreach($group['parcels'] as $parcel)<input type="hidden" name="parcel_ids[]" value="{{ $parcel->id }}">@endforeach
                    <div class="htv-dialog-body">
                        <div class="htv-manifest-summary"><span>{{ $group['parcel_count'] }} {{ $group['parcel_count'] === 1 ? 'parcel' : 'parcels' }} selected</span><strong>{{ $group['origin_municipality'] ?: $group['origin_name'] }} &gt; {{ $group['destination_municipality'] ?: $group['destination_name'] }}</strong></div>
                        <div class="htv-form-grid"><label><span>Vehicle Type <b>*</b></span><select name="vehicle_type" required><option value="">Select vehicle</option><option value="Motorcycle">Motorcycle</option><option value="Multicab">Multicab</option><option value="L300">L300</option><option value="Van">Van</option><option value="Truck">Truck</option></select></label><label><span>Plate Number</span><input type="text" name="vehicle_plate" maxlength="40" placeholder="e.g. DEMO-1234" autocomplete="off"></label><label class="htv-span-2"><span>Driver Name <b>*</b></span><input type="text" name="driver_name" maxlength="140" required placeholder="Enter driver's name" autocomplete="off"></label><label class="htv-span-2"><span>Manifest Note (optional)</span><textarea name="notes" rows="3" maxlength="1500" placeholder="Optional handoff details…"></textarea></label></div>
                    </div>
                    <footer class="htv-dialog-footer"><button type="button" class="htv-btn htv-btn-reset" data-close-modal>Cancel</button><button type="submit" class="htv-btn htv-btn-primary">Create Manifest</button></footer>
                </form>
            </div>
        </div>
    @endforeach

    {{-- ROLE-GATED TRANSFER ACTION MODALS --}}
    @foreach($transfers as $transfer)
        @if($transfer->status === 'preparing' && (! $hubFacility || (int) $transfer->origin_facility_id === (int) $hubFacility->id))
            <div class="htv-overlay" id="htv-action-{{ $transfer->id }}" data-htv-modal hidden><div class="htv-dialog htv-dialog-form htv-dialog-small" role="dialog" aria-modal="true" aria-label="Dispatch transfer" tabindex="-1">
                <header class="htv-dialog-header"><div><span class="htv-eyebrow">ORIGIN HUB DISPATCH</span><h2>Dispatch Transfer</h2><p>{{ $transfer->transfer_code }}</p></div><button type="button" class="htv-close" data-close-modal aria-label="Close">×</button></header>
                <div class="htv-dialog-body"><div class="htv-info"><label>Route</label><strong>{{ $transfer->origin_name }} &gt; {{ $transfer->destination_name }}</strong></div><p class="htv-dialog-help">Confirm the vehicle and parcel manifest are ready to leave the origin hub.</p></div>
                <form method="POST" action="{{ route('logistics.hub-transfers.dispatch', $transfer->id) }}" data-dispatch-transfer data-transfer-code="{{ $transfer->transfer_code }}">@csrf<footer class="htv-dialog-footer"><button type="button" class="htv-btn htv-btn-reset" data-close-modal>Cancel</button><button type="submit" class="htv-btn htv-btn-primary">Confirm Dispatch</button></footer></form>
            </div></div>
        @elseif($transfer->status === 'in_transit' && (! $hubFacility || (int) $transfer->destination_facility_id === (int) $hubFacility->id))
            <div class="htv-overlay" id="htv-action-{{ $transfer->id }}" data-htv-modal hidden><div class="htv-dialog htv-dialog-form htv-dialog-small" role="dialog" aria-modal="true" aria-label="Confirm destination receipt" tabindex="-1">
                <header class="htv-dialog-header"><div><span class="htv-eyebrow">DESTINATION HUB INTAKE</span><h2>Confirm Destination Receipt</h2><p>{{ $transfer->transfer_code }}</p></div><button type="button" class="htv-close" data-close-modal aria-label="Close">×</button></header>
                <div class="htv-dialog-body"><div class="htv-info"><label>Destination Hub</label><strong>{{ $transfer->destination_name }}</strong></div><p class="htv-dialog-help">Only confirm after the manifest and its parcels have physically arrived.</p></div>
                <form method="POST" action="{{ route('logistics.hub-transfers.receive', $transfer->id) }}" data-receive-transfer data-transfer-code="{{ $transfer->transfer_code }}">@csrf<footer class="htv-dialog-footer"><button type="button" class="htv-btn htv-btn-reset" data-close-modal>Cancel</button><button type="submit" class="htv-btn htv-btn-primary">Confirm Receipt</button></footer></form>
            </div></div>
        @endif
    @endforeach

    {{-- One reusable read-only details modal for eye icons --}}
    <div class="htv-overlay" id="htv-details" data-htv-modal hidden><div class="htv-dialog" role="dialog" aria-modal="true" aria-labelledby="htvDetailsTitle" tabindex="-1"><header class="htv-dialog-header"><div><span class="htv-eyebrow">LOGISTICS DETAILS</span><h2 id="htvDetailsTitle">Hub Transfer Details</h2><p id="htvDetailsSubtitle"></p></div><button type="button" class="htv-close" data-close-modal aria-label="Close">×</button></header><div class="htv-dialog-body"><div class="htv-info-grid" id="htvDetailsBody"></div></div><footer class="htv-dialog-footer"><button type="button" class="htv-btn htv-btn-reset" data-close-modal>Close</button></footer></div></div>
</div>
@endsection
@push('scripts')
<script>
(() => {
    'use strict';
    function initializeHubTransfers() {
        const root = document.querySelector('[data-transfer-workspace]');
        if (!root || root.dataset.initialized) return;
        root.dataset.initialized = 'true';
        const normalize = value => String(value || '').toLocaleLowerCase().trim();

        // Switch between the two workflows without reloading or mutating server records.
        const tabRoot = root.querySelector('[data-htv-tabs]');
        const tabButtons = Array.from(root.querySelectorAll('[data-htv-tab]'));
        const tabPanels = Array.from(root.querySelectorAll('[data-htv-panel]'));
        const setActiveTab = (name, moveFocus = false) => {
            const selected = tabButtons.find(button => button.dataset.htvTab === name);
            if (!selected) return;
            tabButtons.forEach(button => {
                const active = button === selected;
                button.setAttribute('aria-selected', String(active));
                button.tabIndex = active ? 0 : -1;
            });
            tabPanels.forEach(panel => { panel.hidden = panel.dataset.htvPanel !== name; });
            if (moveFocus) selected.focus();
        };
        tabButtons.forEach((button, index) => {
            button.addEventListener('click', () => setActiveTab(button.dataset.htvTab));
            button.addEventListener('keydown', event => {
                let nextIndex = -1;
                if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabButtons.length;
                else if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabButtons.length) % tabButtons.length;
                else if (event.key === 'Home') nextIndex = 0;
                else if (event.key === 'End') nextIndex = tabButtons.length - 1;
                if (nextIndex < 0) return;
                event.preventDefault();
                setActiveTab(tabButtons[nextIndex].dataset.htvTab, true);
            });
        });
        root.querySelectorAll('[data-htv-switch]').forEach(button => {
            button.addEventListener('click', () => setActiveTab(button.dataset.htvSwitch, true));
        });
        setActiveTab(tabRoot?.dataset.defaultTab || 'manifests');

        function createPager(rows, searchNode, sizeNode, countNode, emptyNode, paginationNode, getState) {
            let page = 1;
            let matches = [];
            const emptyCount = rows.length === 0;
            const render = (resetPage = false) => {
                if (resetPage) page = 1;
                const q = normalize(searchNode?.value);
                const state = getState();
                matches = rows.filter(row => (state === 'all' || row.dataset.state === state) && normalize(row.dataset.search).includes(q));
                const size = Math.max(1, Number(sizeNode?.value) || 10);
                const count = matches.length;
                const pages = Math.max(1, Math.ceil(count / size));
                page = Math.max(1, Math.min(page, pages));
                rows.forEach(row => { row.hidden = true; });
                matches.slice((page - 1) * size, page * size).forEach(row => { row.hidden = false; });
                if (emptyNode) emptyNode.hidden = emptyCount || count > 0;
                const start = count ? ((page - 1) * size + 1) : 0;
                const end = count ? Math.min(page * size, count) : 0;
                if (countNode) countNode.textContent = `Showing ${start}–${end} of ${count} ${rows === readyRows ? 'route groups' : 'transfers'}`;
                if (!paginationNode) return;
                paginationNode.replaceChildren();
                // Avoid a redundant page 1 control for 0–10 matching records.
                if (pages <= 1) return;
                const add = (label, number, disabled, current, ariaLabel) => {
                    const b = document.createElement('button');
                    b.type = 'button'; b.textContent = label;
                    b.disabled = disabled;
                    if (current) b.setAttribute('aria-current', 'page');
                    b.setAttribute('aria-label', ariaLabel);
                    b.addEventListener('click', () => {page = number; render(); });
                    paginationNode.appendChild(b);
                };
                add('‹', page - 1, page === 1, false, 'Previous page');
                const first = Math.max(1, page - 2), last = Math.min(pages, page + 2);
                if (first > 1) {
                    add('1', 1, false, false, 'Page 1');
                    if (first > 2) {
                        const dots = document.createElement('span'); dots.textContent = '…'; dots.setAttribute('aria-hidden','true'); paginationNode.appendChild(dots);
                    }
                }
                for (let p = first; p <= last; p++) add(String(p), p, false, p === page, `Page ${p}`);
                if (last < pages) {
                    if (last < pages - 1) {
                        const dots = document.createElement('span'); dots.textContent = '…'; dots.setAttribute('aria-hidden','true'); paginationNode.appendChild(dots);
                    }
                    add(String(pages), pages, false, false, `Page ${pages}`);
                }
                add('›', page + 1, page === pages, false, 'Next page');
            };
            sizeNode?.addEventListener('change', () => render(true));
            return render;
        }
        const readyRows = Array.from(root.querySelectorAll('[data-ready-group]'));
        const readySearch = root.querySelector('[data-ready-search]');
        const updateReady = createPager(
            readyRows, readySearch, root.querySelector('[data-ready-page-size]'),
            root.querySelector('[data-ready-visible-count]'), root.querySelector('[data-ready-empty]'),
            root.querySelector('[data-ready-pagination]'), () => 'all'
        );
        readySearch?.addEventListener('input', () => updateReady(true));
        root.querySelectorAll('[data-ready-clear]').forEach(btn => btn.addEventListener('click', () => {
            if (readySearch) readySearch.value = '';
            updateReady(true);
        }));

        const transferRows = Array.from(root.querySelectorAll('[data-transfer-row]'));
        const transferSearch = root.querySelector('[data-transfer-search]');
        const transferStatus = root.querySelector('[data-transfer-status]');
        let activeStatus = 'all';
        const updateTransfers = createPager(
            transferRows, transferSearch, root.querySelector('[data-transfer-page-size]'),
            root.querySelector('[data-transfer-visible-count]'), root.querySelector('[data-transfer-empty]'),
            root.querySelector('[data-transfer-pagination]'), () => activeStatus
        );
        root.querySelector('[data-transfer-apply]')?.addEventListener('click', () => {
            activeStatus = transferStatus?.value || 'all'; updateTransfers(true);
        });
        transferSearch?.addEventListener('input', () => updateTransfers(true));
        transferSearch?.addEventListener('keydown', e => {
            if (e.key === 'Enter') {e.preventDefault(); activeStatus = transferStatus?.value || 'all'; updateTransfers(true);}
        });
        root.querySelectorAll('[data-transfer-clear]').forEach(btn => btn.addEventListener('click', () => {
            if (transferSearch) transferSearch.value = '';
            if (transferStatus) transferStatus.value = 'all';
            activeStatus = 'all'; updateTransfers(true);
        }));

        let currentModal = null, focusReturn = null, priorOverflow = '';
        const openModal = modal => {
            if (!modal) return;
            if (currentModal) closeModal();
            focusReturn = document.activeElement;
            currentModal = modal;
            priorOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            modal.hidden = false;
            const focusable = modal.querySelector('input:not([type="hidden"]),select,textarea,button:not([data-close-modal])');
            (focusable || modal.querySelector('[data-close-modal]') || modal.querySelector('[role="dialog"]'))?.focus();
        };
        function closeModal() {
            if (!currentModal) return;
            currentModal.hidden = true;
            currentModal = null;
            document.body.style.overflow = priorOverflow;
            if (focusReturn && typeof focusReturn.focus === 'function') focusReturn.focus();
        }
        root.querySelectorAll('[data-open-modal]').forEach(button => button.addEventListener('click', () => {
            const id = button.dataset.openModal;
            openModal(document.getElementById(id));
        }));
        const detailsModal = root.querySelector('#htv-details');
        const detailsTitle = root.querySelector('#htvDetailsTitle');
        const detailsSubtitle = root.querySelector('#htvDetailsSubtitle');
        const detailsBody = root.querySelector('#htvDetailsBody');
        root.querySelectorAll('[data-open-ready-details],[data-open-transfer-details]').forEach(button => button.addEventListener('click', () => {
            const row = button.closest('tr');
            const ready = button.matches('[data-open-ready-details]');
            const tpl = row?.querySelector(ready ? '[data-ready-details-template]' : '[data-transfer-details-template]');
            if (!tpl || !detailsBody) return;
            detailsBody.replaceChildren(tpl.content.cloneNode(true));
            if (detailsTitle) detailsTitle.textContent = ready ? 'Ready Route Details' : 'Transfer Manifest Details';
            if (detailsSubtitle) detailsSubtitle.textContent = row?.querySelector('td strong')?.textContent?.trim() || '';
            openModal(detailsModal);
        }));
        root.querySelectorAll('[data-close-modal]').forEach(btn => btn.addEventListener('click', closeModal));
        root.querySelectorAll('[data-htv-modal]').forEach(modal => modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        }));
        document.addEventListener('keydown', e => {
            if (!currentModal) return;
            if (e.key === 'Escape') { e.preventDefault(); closeModal(); return; }
            if (e.key !== 'Tab') return;
            const focusables = [...currentModal.querySelectorAll('button:not([disabled]),input:not([type="hidden"]):not([disabled]),select:not([disabled]),textarea:not([disabled]),a[href]')].filter(el => el.getClientRects().length > 0);
            if (!focusables.length) return;
            const first = focusables[0], last = focusables[focusables.length - 1];
            if (e.shiftKey && document.activeElement === first) {e.preventDefault();last.focus();}
            else if (!e.shiftKey && document.activeElement === last) {e.preventDefault();first.focus();}
        });
        root.querySelectorAll('[data-create-transfer],[data-dispatch-transfer],[data-receive-transfer]').forEach(form => {
            form.addEventListener('submit', e => {
                if (form.dataset.submitting === 'true') { e.preventDefault(); return; }
                const driver = form.querySelector('input[name="driver_name"]');
                if (driver && !driver.value.trim()) { e.preventDefault(); driver.setCustomValidity('Enter a driver name.'); driver.reportValidity(); return; }
                if (driver) driver.setCustomValidity('');
                if (!form.reportValidity()) { e.preventDefault(); return; }
                form.dataset.submitting = 'true';
                const button = form.querySelector('button[type="submit"]');
                if (button) { button.disabled = true; button.textContent = form.hasAttribute('data-create-transfer') ? 'Creating manifest…' : form.hasAttribute('data-dispatch-transfer') ? 'Dispatching…' : 'Confirming receipt…'; }
            });
            form.querySelector('input[name="driver_name"]')?.addEventListener('input', e => e.currentTarget.setCustomValidity(''));
        });
        updateReady(true); updateTransfers(true);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeHubTransfers);
    else initializeHubTransfers();
    window.addEventListener('pageshow', event => {if (event.persisted) window.location.reload();});
})();
</script>
@endpush
