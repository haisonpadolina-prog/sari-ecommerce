@extends('layouts.logistics')

@section('title', 'Delivery Monitoring — SARI Logistics')
@section('page-title', 'Delivery Monitoring')

@push('styles')
<style>
/* Logistics Delivery Monitoring — page-scoped. No external page CSS dependency. */
#lgDeliveryMonitoring { --dm-gold:#d9950b;--dm-ink:#233044;--dm-muted:#8490a2;--dm-border:#e5eaf0;--dm-shadow:0 10px 30px rgba(25,38,55,.065);max-width:1620px;width:100%;margin-inline:auto;color:var(--dm-ink);font-size:13px; }
#lgDeliveryMonitoring *,#lgDeliveryMonitoring *::before,#lgDeliveryMonitoring *::after {box-sizing:border-box}
#lgDeliveryMonitoring [hidden] {display:none!important}
#lgDeliveryMonitoring button,#lgDeliveryMonitoring input,#lgDeliveryMonitoring select {font:inherit}
#lgDeliveryMonitoring button {cursor:pointer}
#lgDeliveryMonitoring :is(button,input,select,a):focus-visible{outline:2px solid #7e91aa;outline-offset:3px}
#lgDeliveryMonitoring .dm-head{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;margin:0 0 20px;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important;border-radius:0!important}
#lgDeliveryMonitoring .dm-head::before,#lgDeliveryMonitoring .dm-head::after{display:none!important}
#lgDeliveryMonitoring .dm-eyebrow{font-size:11px;letter-spacing:.13em;font-weight:750;color:#ac7516;margin:0 0 5px;text-transform:uppercase}
#lgDeliveryMonitoring .dm-head h1{margin:0;font-size:clamp(25px,2.15vw,34px);font-weight:760;letter-spacing:-.045em;line-height:1.18;color:#1e293b}
#lgDeliveryMonitoring .dm-head h1 span{color:var(--dm-gold)}
#lgDeliveryMonitoring .dm-head-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap}
#lgDeliveryMonitoring .dm-btn{display:inline-flex;justify-content:center;align-items:center;gap:6px;padding:10px 15px;min-height:41px;border-radius:11px;border:1px solid #dfe6ee;background:#fff;color:#46576c;text-decoration:none;font-size:12px;font-weight:700;white-space:nowrap;box-shadow:none;transition:background .15s,border-color .15s}
#lgDeliveryMonitoring .dm-btn:hover{background:#f3f5f7;border-color:#cbd5e1}
#lgDeliveryMonitoring .dm-btn-gold,#lgDeliveryMonitoring .dm-btn-gold:hover{border-color:#d9950b;background:#d9950b;color:white;box-shadow:0 5px 12px rgba(217,149,11,.14)}
#lgDeliveryMonitoring .dm-scope{display:flex;align-items:center;gap:10px;margin:0 0 16px;padding:11px 13px;background:#f7f9fb;border:1px solid var(--dm-border);border-radius:11px;color:#637287;font-size:12px;line-height:1.55}
#lgDeliveryMonitoring .dm-scope svg{width:18px;height:18px;flex:none;color:#8c9aab}
#lgDeliveryMonitoring .dm-kpis{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:13px;margin-bottom:20px!important;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important;border-radius:0!important}
#lgDeliveryMonitoring .dm-kpi{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;min-width:0;min-height:119px;padding:17px 18px;background:#fff;border:1px solid var(--dm-border);border-radius:17px;box-shadow:var(--dm-shadow)}
#lgDeliveryMonitoring .dm-kpi-label{font-weight:650;color:#65748a;font-size:12px;line-height:1.4;margin:0}
#lgDeliveryMonitoring .dm-kpi strong{display:block;font-size:27px;letter-spacing:-.045em;line-height:1.2;margin:7px 0 3px;color:#1f2937}
#lgDeliveryMonitoring .dm-kpi small{font-size:10px;color:#95a1b1;line-height:1.5;display:block}
#lgDeliveryMonitoring .dm-kpi-icon{display:block;flex:none;width:30px;height:30px;background:transparent!important;border:0!important;padding:0!important;border-radius:0!important}
#lgDeliveryMonitoring .dm-kpi-icon svg{display:block;width:30px;height:30px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
#lgDeliveryMonitoring .dm-ink-slate{color:#8b9baa}#lgDeliveryMonitoring .dm-ink-gold{color:#ca901b}#lgDeliveryMonitoring .dm-ink-blue{color:#4c8abc}#lgDeliveryMonitoring .dm-ink-violet{color:#8471b6}#lgDeliveryMonitoring .dm-ink-green{color:#3c9966}
#logisticsContent #lgDeliveryMonitoring > section[data-dm-panel]{display:block;padding:0!important;margin:0!important;border:0!important;background:transparent!important;border-radius:0!important;box-shadow:none!important;overflow:visible!important}
#logisticsContent #lgDeliveryMonitoring > section[data-dm-panel][hidden]{display:none!important}
#lgDeliveryMonitoring .dm-filters{display:flex;gap:12px;align-items:center;margin:0 0 15px;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important;min-width:0}
#lgDeliveryMonitoring .dm-search-wrap{display:flex;align-items:center;gap:10px;flex:1;min-width:0;height:48px;padding:0 15px;background:#fff;border:1px solid #dfe6ed;border-radius:12px}
#lgDeliveryMonitoring .dm-search-wrap:focus-within{border-color:#aab6c4;box-shadow:0 0 0 3px rgba(112,130,151,.08)}
#lgDeliveryMonitoring .dm-search-wrap svg{width:18px;height:18px;color:#92a0b0;flex:none;stroke:currentColor;fill:none;stroke-width:1.8}
#lgDeliveryMonitoring .dm-search-wrap input{min-width:0;width:100%;background:transparent!important;border:0!important;box-shadow:none!important;outline:0!important;padding:0!important;color:#283549;font-size:12px}
#lgDeliveryMonitoring .dm-search-wrap input::placeholder{color:#99a5b5}
#lgDeliveryMonitoring .dm-filter-select{min-width:175px;max-width:210px;width:19%;height:48px;border:1px solid #dfe6ed;border-radius:12px;background:#fff;padding:0 13px;font-size:12px;color:#334155;box-shadow:none}
#lgDeliveryMonitoring .dm-apply{min-width:135px;height:48px;border:1px solid #d9950b;border-radius:12px;background:#d9950b;color:white;font-size:12px;font-weight:750;box-shadow:0 5px 12px rgba(217,149,11,.13)}
#lgDeliveryMonitoring .dm-reset{min-width:87px;height:48px;border:1px solid #dfe6ed;border-radius:12px;background:#fff;color:#52637a;font-size:12px;font-weight:700}
#lgDeliveryMonitoring .dm-table-card{overflow:hidden;border:1px solid var(--dm-border);border-radius:18px;background:white;box-shadow:var(--dm-shadow)}
#lgDeliveryMonitoring .dm-table-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 21px;border-bottom:1px solid #edf0f4}
#lgDeliveryMonitoring .dm-table-head h2{margin:0;font-size:16px;font-weight:750;letter-spacing:-.025em;color:#223047}
#lgDeliveryMonitoring .dm-count{display:inline-flex;align-items:center;padding:6px 10px;white-space:nowrap;border:1px solid #e4e9ef;border-radius:9px;color:#687b94;background:#f9fafb;font-size:10px;font-weight:750}
#lgDeliveryMonitoring .dm-table{border-collapse:collapse;width:100%;table-layout:fixed;font-size:12px}
#lgDeliveryMonitoring .dm-table thead{background:#f8fafc}
#lgDeliveryMonitoring .dm-table th{padding:14px 15px;text-align:left;color:#7b889c;font-size:10px;font-weight:750;text-transform:uppercase;letter-spacing:.045em;line-height:1.4}
#lgDeliveryMonitoring .dm-table td{padding:16px 15px;border-top:1px solid #edf0f4;vertical-align:middle;min-width:0;overflow-wrap:anywhere;color:#42536a}
#lgDeliveryMonitoring .dm-table tbody tr:hover{background:#f2f4f6!important;box-shadow:none!important}
#lgDeliveryMonitoring .dm-primary{display:block;font-size:12px;line-height:1.4;font-weight:750;color:#253246}
#lgDeliveryMonitoring .dm-secondary{display:block;margin-top:5px;color:#92a0b0;font-size:10px;line-height:1.5}
#lgDeliveryMonitoring .dm-status{display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border:1px solid #e4e9ef;border-radius:999px;background:#f8fafc;color:#66758a;font-size:10px;font-weight:750}
#lgDeliveryMonitoring .dm-status-amber{background:#fff8eb;border-color:#f1dfbd;color:#a36d12}
#lgDeliveryMonitoring .dm-status-blue{background:#eff6ff;border-color:#d4e5f7;color:#407aab}
#lgDeliveryMonitoring .dm-status-green{background:#edf9f1;border-color:#d4ebdc;color:#348255}
#lgDeliveryMonitoring .dm-status-neutral{background:#f3f5f7;border-color:#e1e6ec;color:#6b7788}
#lgDeliveryMonitoring .dm-status-red{background:#fff1ef;border-color:#f4d7d3;color:#b95a54}
#lgDeliveryMonitoring .dm-progress{display:flex;align-items:center;gap:4px;max-width:190px;margin-top:8px}
#lgDeliveryMonitoring .dm-progress span{flex:1;height:5px;border-radius:6px;background:#e9eef3}
#lgDeliveryMonitoring .dm-progress span.is-done{background:#70b792}
#lgDeliveryMonitoring .dm-progress span.is-current{background:#d9950b}
#lgDeliveryMonitoring .dm-eye{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;padding:0;border:0!important;box-shadow:none!important;border-radius:9px;background:transparent!important;color:#64758b}
#lgDeliveryMonitoring .dm-eye:hover{background:#e8ecf0!important;color:#223047}
#lgDeliveryMonitoring .dm-eye svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8}
#lgDeliveryMonitoring .dm-table-foot{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:14px 20px;border-top:1px solid #edf0f4;color:#8693a5;font-size:11px}
#lgDeliveryMonitoring .dm-per-page{display:flex;align-items:center;gap:9px}
#lgDeliveryMonitoring .dm-per-page select{height:35px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;padding:0 9px;color:#475569}
#lgDeliveryMonitoring .dm-pager{display:flex;justify-content:center;align-items:center;gap:6px;margin:17px auto 4px}
#lgDeliveryMonitoring .dm-pager button{min-width:35px;height:35px;padding:0 9px;border:0;border-radius:9px;background:#f3f5f8;color:#68798d;font-size:12px;font-weight:750}
#lgDeliveryMonitoring .dm-pager button.is-active{background:#d9950b;color:white;box-shadow:0 5px 12px rgba(217,149,11,.18)}
#lgDeliveryMonitoring .dm-pager button:disabled{opacity:.4;cursor:default}
#lgDeliveryMonitoring .dm-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:255px;padding:35px 15px;text-align:center}
#lgDeliveryMonitoring .dm-empty-icon{width:58px;height:58px;color:#b8c0ca;background:none!important;border:0!important;margin:0 0 18px}
#lgDeliveryMonitoring .dm-empty-icon svg{width:58px;height:58px;fill:none;stroke:currentColor;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round}
#lgDeliveryMonitoring .dm-empty h3{margin:0 0 7px;font-size:22px;letter-spacing:-.025em;font-weight:750;color:#8492a5}
#lgDeliveryMonitoring .dm-empty p{margin:0;max-width:470px;font-size:12px;line-height:1.65;color:#9ba8b8}
#lgDeliveryMonitoring .dm-modal{position:fixed;inset:0;z-index:1500;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(25,37,52,.55);backdrop-filter:blur(2px)}
#lgDeliveryMonitoring .dm-modal-dialog{width:min(760px,100%);max-height:min(87vh,850px);display:flex;flex-direction:column;overflow:hidden;background:#fff;border:1px solid #e1e7ef;border-radius:20px;box-shadow:0 24px 75px rgba(8,24,42,.28)}
#lgDeliveryMonitoring .dm-modal-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:19px 23px;border-bottom:1px solid #edf0f4}
#lgDeliveryMonitoring .dm-modal-head p{margin:0 0 6px;color:#b77a16;font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}
#lgDeliveryMonitoring .dm-modal-head h2{margin:0;color:#202e41;font-size:21px;letter-spacing:-.03em;font-weight:750}
#lgDeliveryMonitoring .dm-close{display:grid;place-items:center;flex:none;width:35px;height:35px;background:#fff;border:1px solid #e1e7ef;border-radius:10px;color:#617189;font-size:22px}
#lgDeliveryMonitoring .dm-modal-body{padding:21px 23px;overflow-y:auto;overscroll-behavior:contain;scrollbar-width:thin}
#lgDeliveryMonitoring .dm-modal-status{display:flex;gap:10px;justify-content:space-between;flex-wrap:wrap;align-items:center;margin-bottom:18px}
#lgDeliveryMonitoring .dm-detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
#lgDeliveryMonitoring .dm-detail-box{padding:13px 14px;border:1px solid #e7ecf1;border-radius:11px;background:#fafbfc;min-width:0}
#lgDeliveryMonitoring .dm-detail-box small{display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:.055em;color:#91a0b1;font-size:9px;font-weight:750}
#lgDeliveryMonitoring .dm-detail-box strong{font-size:12px;line-height:1.55;color:#344357;overflow-wrap:anywhere}
#lgDeliveryMonitoring .dm-detail-box p{font-size:11px;line-height:1.6;color:#8492a4;margin:5px 0 0;overflow-wrap:anywhere}
#lgDeliveryMonitoring .dm-modal-footer{display:flex;justify-content:flex-end;gap:9px;padding:14px 23px;border-top:1px solid #edf0f4}
#lgDeliveryMonitoring .dm-modal[hidden]{display:none!important}
@media(max-width:1200px){#lgDeliveryMonitoring .dm-kpis{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:920px){#lgDeliveryMonitoring .dm-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}#lgDeliveryMonitoring .dm-filters{flex-wrap:wrap}#lgDeliveryMonitoring .dm-search-wrap{flex:1 1 100%}#lgDeliveryMonitoring .dm-filter-select{width:auto;max-width:none;flex:1}#lgDeliveryMonitoring .dm-table{table-layout:auto}}
@media(max-width:750px){#lgDeliveryMonitoring .dm-head{align-items:flex-start}#lgDeliveryMonitoring .dm-head-actions{width:100%}#lgDeliveryMonitoring .dm-head-actions .dm-btn{flex:1}#lgDeliveryMonitoring .dm-table thead{display:none}#lgDeliveryMonitoring .dm-table tbody{display:block;padding:10px}#lgDeliveryMonitoring .dm-table tbody tr{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0;margin-bottom:10px;border:1px solid #e5eaf0;border-radius:12px;overflow:hidden;background:white}#lgDeliveryMonitoring .dm-table td{display:block;min-width:0;border:0;padding:12px 13px}#lgDeliveryMonitoring .dm-table td::before{display:block;content:attr(data-label);font-size:9px;letter-spacing:.055em;font-weight:750;color:#9aa5b5;text-transform:uppercase;margin-bottom:7px}#lgDeliveryMonitoring .dm-table td:first-child{grid-column:1/-1}#lgDeliveryMonitoring .dm-table td:last-child{grid-column:1/-1;border-top:1px solid #edf0f4;text-align:right;padding:6px 10px}#lgDeliveryMonitoring .dm-table-head{padding:15px}#lgDeliveryMonitoring .dm-journey{grid-template-columns:repeat(4,minmax(0,1fr));row-gap:16px}#lgDeliveryMonitoring .dm-detail-grid{grid-template-columns:1fr}#lgDeliveryMonitoring .dm-modal-dialog{max-height:92vh}}
@media(max-width:480px){#lgDeliveryMonitoring .dm-kpis{gap:9px}#lgDeliveryMonitoring .dm-kpi{padding:13px;min-height:112px}#lgDeliveryMonitoring .dm-kpi strong{font-size:23px}#lgDeliveryMonitoring .dm-kpi-icon,#lgDeliveryMonitoring .dm-kpi-icon svg{width:26px;height:26px}#lgDeliveryMonitoring .dm-tab{font-size:12px}#lgDeliveryMonitoring .dm-tabs{gap:15px}#lgDeliveryMonitoring .dm-filter-select{flex:1 1 100%}#lgDeliveryMonitoring .dm-apply,#lgDeliveryMonitoring .dm-reset{flex:1;min-width:0}#lgDeliveryMonitoring .dm-empty h3{font-size:20px}#lgDeliveryMonitoring .dm-table-foot{padding:12px 14px}#lgDeliveryMonitoring .dm-modal-head,#lgDeliveryMonitoring .dm-modal-body,#lgDeliveryMonitoring .dm-modal-footer{padding-left:15px;padding-right:15px}}
@media(prefers-reduced-motion:reduce){#lgDeliveryMonitoring *{scroll-behavior:auto!important;transition:none!important}}

/* V2: Unified list selector inside the filters, quiet stage colors, progress-first modal */
#lgDeliveryMonitoring .dm-filters-unified{margin:0 0 15px!important;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important}
#lgDeliveryMonitoring .dm-filters-unified .dm-view-select{flex:0 0 210px;width:210px;max-width:210px}
#lgDeliveryMonitoring .dm-filters-unified .dm-stage-select{flex:0 0 155px;width:155px;max-width:155px}
#lgDeliveryMonitoring .dm-filters-unified .dm-stage-select[hidden]{display:none!important}
#lgDeliveryMonitoring .dm-status-current{border:1px solid #dfe5eb!important;background:#f4f6f8!important;color:#536173!important;box-shadow:none!important}
#lgDeliveryMonitoring .dm-progress span.is-done{background:#aab5c2!important}
#lgDeliveryMonitoring .dm-progress span.is-current{background:#56677b!important}
#lgDeliveryMonitoring .dm-journey-panel{padding:17px 18px 9px;margin:0 0 19px;border:1px solid #e4e9ef;border-radius:13px;background:#fbfcfd}
#lgDeliveryMonitoring .dm-journey-overview{display:flex;justify-content:space-between;align-items:start;gap:15px}
#lgDeliveryMonitoring .dm-journey-overview>div{min-width:0;flex:1}
#lgDeliveryMonitoring .dm-journey-kicker,#lgDeliveryMonitoring .dm-detail-heading{display:block;margin:0 0 6px;color:#8290a2;font-size:10px;font-weight:750;letter-spacing:.085em}
#lgDeliveryMonitoring .dm-journey-overview strong{display:block;color:#29384b;font-size:16px;letter-spacing:-.025em;font-weight:750}
#lgDeliveryMonitoring .dm-journey-overview p{margin:4px 0 0;color:#8290a2;font-size:11px;line-height:1.65}
#lgDeliveryMonitoring .dm-step-counter{white-space:nowrap;border-radius:9px;background:#eef1f5;color:#526176;padding:6px 10px;font-size:11px;font-weight:750}
#lgDeliveryMonitoring .dm-progress-track{height:7px;overflow:hidden;background:#e7ebef;border-radius:999px;margin:17px 0 19px}
#lgDeliveryMonitoring .dm-progress-track>span{display:block;height:100%;border-radius:999px;background:#52647b}
#lgDeliveryMonitoring .dm-journey{display:block;margin:0;padding:0 0 0 1px;list-style:none}
#lgDeliveryMonitoring .dm-journey li{position:relative;display:grid;grid-template-columns:34px minmax(0,1fr) auto;align-items:start;text-align:left;gap:12px;min-height:52px;padding:0 0 15px}
#lgDeliveryMonitoring .dm-journey li:not(:last-child)::after{content:'';position:absolute;top:29px;bottom:0;left:14px;width:2px;background:#e4e9ee}
#lgDeliveryMonitoring .dm-journey li.is-complete:not(:last-child)::after{background:#aebac8}
#lgDeliveryMonitoring .dm-journey-mark{position:relative;z-index:1;width:29px;height:29px;background:#fff;border:1px solid #dbe2e9;border-radius:50%;display:grid;place-items:center;color:#9ba7b4;font-size:11px;font-weight:750}
#lgDeliveryMonitoring .dm-journey li.is-complete .dm-journey-mark{background:#e9edf2;border-color:#c5cdd7;color:#526377}
#lgDeliveryMonitoring .dm-journey li.is-current .dm-journey-mark{background:#263548;border-color:#263548;color:white;box-shadow:0 0 0 4px #e9edf2}
#lgDeliveryMonitoring .dm-journey-copy{display:flex;flex-direction:column;align-items:flex-start;min-width:0;gap:3px;padding-top:2px}
#lgDeliveryMonitoring .dm-journey li strong{font-size:12px;line-height:1.4;color:#344256;font-weight:700}
#lgDeliveryMonitoring .dm-journey li.is-pending strong{color:#8e9bad}
#lgDeliveryMonitoring .dm-journey-copy small{font-size:10px;color:#96a2b2}
#lgDeliveryMonitoring .dm-journey-state{margin-top:6px;color:#93a0af;font-size:10px;font-weight:650;white-space:nowrap}
#lgDeliveryMonitoring .dm-journey li.is-current .dm-journey-state{color:#344357}
#lgDeliveryMonitoring .dm-detail-heading{margin:0 0 10px}
#lgDeliveryMonitoring .dm-journey-panel-complete{padding-bottom:1px}
#lgDeliveryMonitoring .dm-modal-dialog{width:min(720px,100%)}
@media(max-width:1080px){#lgDeliveryMonitoring .dm-filters-unified{flex-wrap:wrap}#lgDeliveryMonitoring .dm-filters-unified .dm-search-wrap{flex:1 1 100%}}
@media(max-width:640px){#lgDeliveryMonitoring .dm-filters-unified .dm-view-select{flex:1 1 100%;width:100%;max-width:none}#lgDeliveryMonitoring .dm-filters-unified .dm-stage-select{flex:1 1 100%;width:100%;max-width:none}#lgDeliveryMonitoring .dm-journey-panel{padding:14px 13px 4px}#lgDeliveryMonitoring .dm-journey-overview strong{font-size:14px}#lgDeliveryMonitoring .dm-journey li{grid-template-columns:32px minmax(0,1fr);gap:9px}#lgDeliveryMonitoring .dm-journey-state{grid-column:2;justify-self:start;margin:-10px 0 8px}}
</style>
@endpush

@section('content')
@php
    // The controller remains responsible for authorizing branch visibility and route editing.
    $isHubOperator = (bool) ($isHubOperator ?? false);
    $hubFacility = $hubFacility ?? null;
    $summaryCards = [
        ['Active Deliveries', $stats['active'], $isHubOperator ? 'Journeys involving this hub' : 'Across all delivery legs', 'slate'],
        ['At Hubs', $stats['at_hub'], 'Sorting or dispatch', 'gold'],
        ['In Linehaul', $stats['linehaul'], 'Moving between hubs', 'blue'],
        ['Out for Delivery', $stats['out_for_delivery'], 'With local Riders', 'violet'],
        ['Delivered Today', $stats['delivered_today'], 'Completed handoffs', 'green'],
    ];
    $activeCount = $activeOrders->count();
    $deliveredCount = $deliveredToday->count();
    $initialTab = ($activeCount === 0 && $deliveredCount > 0) ? 'delivered' : 'active';
@endphp
<div id="lgDeliveryMonitoring" class="sari-logistics-ui monitor-page" data-monitor-workspace>
    <header class="dm-head">
        <div>
            <p class="dm-eyebrow">{{ $isHubOperator ? 'ASSIGNED HUB · READ-ONLY' : 'DELIVERY OPERATIONS' }}</p>
            <h1>Delivery <span>Monitoring</span></h1>
        </div>
        <div class="dm-head-actions">
            <a class="dm-btn" href="{{ route('logistics.last-mile-assignment') }}">&lt; Last Mile</a>
            <a class="dm-btn dm-btn-gold" href="{{ route('logistics.dashboard') }}">Dashboard</a>
        </div>
    </header>

    @if($isHubOperator)
        <div class="dm-scope" role="note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
            <span><strong>{{ $hubFacility?->name ?: 'Assigned Municipal Hub' }}</strong> — You can view journeys routed through your assigned hub. Route editing is restricted to Main Logistics.</span>
        </div>
    @endif

    <div class="dm-kpis" aria-label="Delivery Monitoring overview">
        @foreach($summaryCards as $index => [$label,$value,$note,$tone])
            <article class="dm-kpi">
                <div><p class="dm-kpi-label">{{ $label }}</p><strong>{{ number_format($value) }}</strong><small>{{ $note }}</small></div>
                <span class="dm-kpi-icon dm-ink-{{ $tone }}" aria-hidden="true">
                    @switch($index)
                        @case(0)<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h7"/></svg>@break
                        @case(1)<svg viewBox="0 0 24 24"><path d="M3 10.5 12 4l9 6.5V21H3zM9 21v-7h6v7"/></svg>@break
                        @case(2)<svg viewBox="0 0 24 24"><path d="M3 7h12v11H3zM15 10h3l3 3v5h-6"/><circle cx="7" cy="18" r="1.5"/><circle cx="18" cy="18" r="1.5"/></svg>@break
                        @case(3)<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l4 2"/></svg>@break
                        @default<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16.5 9"/></svg>
                    @endswitch
                </span>
            </article>
        @endforeach
    </div>

    {{-- One shared filter toolbar. Switch delivery lists without extra navigation tabs. --}}
    <div class="dm-filters dm-filters-unified" aria-label="Delivery monitoring filters">
        <label class="dm-search-wrap">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
            <span class="sr-only">Search delivery records</span>
            <input type="search" data-dm-search-common autocomplete="off" value="{{ request()->query('q','') }}" placeholder="Search order, Buyer, Seller, Rider, or hub...">
        </label>
        <select class="dm-filter-select dm-view-select" data-dm-view aria-label="Select delivery records">
            <option value="active" @selected($initialTab === 'active')>Active Deliveries ({{ $activeCount }})</option>
            <option value="delivered" @selected($initialTab === 'delivered')>Delivered Today ({{ $deliveredCount }})</option>
        </select>
        <select class="dm-filter-select dm-stage-select" data-dm-status-common aria-label="Filter current delivery stage">
            <option value="all">All Stages</option>
            <option value="pickup">Pickup</option>
            <option value="hub">At Hub</option>
            <option value="linehaul">Linehaul</option>
            <option value="last_mile">Last Mile</option>
        </select>
        <button type="button" class="dm-apply" data-dm-apply-common>Apply Filter</button>
        <button type="button" class="dm-reset" data-dm-reset-common>Reset</button>
    </div>

    <section id="dmActivePanel" role="region" aria-label="Active Deliveries" data-dm-panel="active" @if($initialTab !== 'active') hidden @endif>
        <div class="dm-table-card">
            <div class="dm-table-head"><h2>Active Parcel Journeys</h2><span class="dm-count">{{ $activeCount }} ORDERS</span></div>
            <table class="dm-table" aria-label="Active parcel journey table">
                <colgroup><col style="width:25%"><col style="width:23%"><col style="width:21%"><col style="width:20%"><col style="width:11%"></colgroup>
                <thead><tr><th>Order / Buyer</th><th>Current Stage</th><th>Current Handler</th><th>Updated</th><th style="text-align:center">View</th></tr></thead>
                <tbody>
                @foreach($activeOrders as $order)
                    @php
                        $routeStops = $order->deliveryRouteStops->keyBy('stop_key');
                        $originStop = $routeStops->get('origin_facility');
                        $destinationStop = $routeStops->get('destination_facility');
                        $originName = $originStop?->facility?->name ?: $originStop?->name ?: 'Origin hub pending';
                        $destinationName = $destinationStop?->facility?->name ?: $destinationStop?->name ?: 'Destination hub pending';
                        $lastMile = $order->getAttribute('monitor_last_mile');
                        $transfer = $order->getAttribute('monitor_transfer');
                        $currentStep = (int) $order->getAttribute('monitor_stage_step');
                        $stageGroup = $order->getAttribute('monitor_stage_group');
                        $stageLabel = $order->getAttribute('monitor_stage_label');
                        $stageNote = $order->getAttribute('monitor_stage_note');
                        $handlerName = $order->getAttribute('monitor_handler_name');
                        $handlerRole = $order->getAttribute('monitor_handler_role');
                        $localRiderName = $lastMile ? trim($lastMile->first_name.' '.$lastMile->last_name) : null;
                        $searchText = implode(' ',array_filter([$order->order_number,$order->buyer_name,$order->buyer_address,$order->seller?->store_name,$order->pickup_name,$order->courier_name,$order->courier_email,$localRiderName,$lastMile?->delivery_area,$originName,$destinationName,$transfer?->transfer_code,$transfer?->driver_name,$stageLabel]));
                        $journeySteps = [['Seller Pickup','Pickup Rider'],['Origin Hub','Hub Intake'],['Sorting','Sort Zone'],['Hub Transfer','Linehaul'],['Destination Hub','Hub Receipt'],['Local Rider','Last Mile'],['Buyer','Delivery']];
                        $progressStep = max(1, min(7, $currentStep));
                    @endphp
                    <tr data-dm-row="active" data-stage="{{ $stageGroup }}" data-search="{{ $searchText }}">
                        <td data-label="Order / Buyer"><strong class="dm-primary">{{ $order->order_number }}</strong><span class="dm-secondary">{{ $order->seller?->store_name ?: 'SARI Seller' }} · {{ $order->buyer_name }}</span></td>
                        <td data-label="Current Stage"><span class="dm-status dm-status-current">{{ $stageLabel }}</span><div class="dm-progress" aria-label="Step {{ $currentStep }} of 7"><span class="{{ $currentStep > 1 ? 'is-done' : ($currentStep === 1 ? 'is-current' : '') }}"></span><span class="{{ $currentStep > 2 ? 'is-done' : ($currentStep === 2 ? 'is-current' : '') }}"></span><span class="{{ $currentStep > 3 ? 'is-done' : ($currentStep === 3 ? 'is-current' : '') }}"></span><span class="{{ $currentStep > 4 ? 'is-done' : ($currentStep === 4 ? 'is-current' : '') }}"></span><span class="{{ $currentStep > 5 ? 'is-done' : ($currentStep === 5 ? 'is-current' : '') }}"></span><span class="{{ $currentStep > 6 ? 'is-done' : ($currentStep === 6 ? 'is-current' : '') }}"></span><span class="{{ $currentStep >= 7 ? 'is-current' : '' }}"></span></div></td>
                        <td data-label="Handler"><strong class="dm-primary">{{ $handlerName }}</strong><span class="dm-secondary">{{ $handlerRole }}</span></td>
                        <td data-label="Updated"><strong class="dm-primary">{{ $order->updated_at?->diffForHumans() ?: '—' }}</strong><span class="dm-secondary">{{ $order->updated_at?->timezone('Asia/Manila')->format('M d, Y') ?: '' }}</span></td>
                        <td data-label="View" style="text-align:center"><button type="button" class="dm-eye" aria-label="View details for {{ $order->order_number }}" data-dm-open="dmOrder{{ $loop->index }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></td>
                    </tr>
                    <template id="dmOrder{{ $loop->index }}" data-dm-title="{{ $order->order_number }}">
                        <div class="dm-modal-status"><span class="dm-status dm-status-current">{{ $stageLabel }}</span><span class="dm-secondary">Updated {{ $order->updated_at?->diffForHumans() }}</span></div>
                        <div class="dm-journey-panel" aria-label="Parcel journey progress">
                            <div class="dm-journey-overview">
                                <div><span class="dm-journey-kicker">DELIVERY PROGRESS</span><strong>{{ $stageLabel }}</strong><p>{{ $stageNote }}</p></div>
                                <span class="dm-step-counter">Step {{ $progressStep }} / 7</span>
                            </div>
                            <div class="dm-progress-track" role="progressbar" aria-label="Parcel progress" aria-valuemin="0" aria-valuemax="7" aria-valuenow="{{ $progressStep }}"><span style="width:{{ ($progressStep / 7) * 100 }}%"></span></div>
                            <ol class="dm-journey">
                                @foreach($journeySteps as $stepIndex => [$stepName,$stepDetail])
                                    @php $stepNumber = $stepIndex + 1; $stepState = $stepNumber < $progressStep ? 'complete' : ($stepNumber === $progressStep ? 'current' : 'pending'); @endphp
                                    <li class="is-{{ $stepState }}"><span class="dm-journey-mark">{{ $stepState === 'complete' ? '✓' : $stepNumber }}</span><div class="dm-journey-copy"><strong>{{ $stepName }}</strong><small>{{ $stepDetail }}</small></div><span class="dm-journey-state">{{ $stepState === 'complete' ? 'Completed' : ($stepState === 'current' ? 'Current' : 'Upcoming') }}</span></li>
                                @endforeach
                            </ol>
                        </div>
                        <div class="dm-detail-heading">OPERATIONAL DETAILS</div>
                        <div class="dm-detail-grid">
                            <div class="dm-detail-box"><small>Buyer / Seller</small><strong>{{ $order->buyer_name }} · {{ $order->seller?->store_name ?: 'SARI Seller' }}</strong><p>{{ $order->buyer_address }}</p></div>
                            <div class="dm-detail-box"><small>Current Handler</small><strong>{{ $handlerName }}</strong><p>{{ $handlerRole }}</p></div>
                            <div class="dm-detail-box"><small>Pickup Rider</small><strong>{{ $order->courier_name ?: 'Not assigned' }}</strong><p>{{ $order->courier_email ?: 'No pickup Rider email' }}</p></div>
                            <div class="dm-detail-box"><small>Origin Hub</small><strong>{{ $originName }}</strong><p>{{ $order->logisticsParcel?->sorting_zone ? 'Zone '.$order->logisticsParcel->sorting_zone : 'Sorting zone not assigned' }}</p></div>
                            <div class="dm-detail-box"><small>Hub Transfer</small><strong>{{ $transfer?->transfer_code ?: 'Not yet assigned' }}</strong><p>@if($transfer){{ $transfer->vehicle_type }} {{ $transfer->vehicle_plate ? '· '.$transfer->vehicle_plate : '' }} · Driver: {{ $transfer->driver_name }}@else Awaiting linehaul stage @endif</p></div>
                            <div class="dm-detail-box"><small>Destination Hub</small><strong>{{ $destinationName }}</strong><p>{{ $lastMile?->delivery_area ? 'Barangay '.$lastMile->delivery_area : 'Last-mile area pending' }}</p></div>
                            <div class="dm-detail-box"><small>Local Rider</small><strong>{{ $localRiderName ?: 'Not yet assigned' }}</strong><p>@if($lastMile){{ $lastMile->vehicle_type ?: 'Vehicle not recorded' }} {{ $lastMile->plate_number ? '· '.$lastMile->plate_number : '' }} · {{ $lastMile->assignment_code }}@else Destination dispatch pending @endif</p></div>
                            <div class="dm-detail-box"><small>Parcel State</small><strong>{{ ucwords(str_replace('_',' ',$order->logisticsParcel?->status ?: $order->status)) }}</strong><p>{{ $isHubOperator ? 'Journey details · Read-only' : 'Full delivery route available' }}</p></div>
                        </div>
                        <div style="display:flex;justify-content:flex-end;margin-top:16px">
                            @if(!$isHubOperator)<a class="dm-btn dm-btn-gold" href="{{ route('logistics.delivery-routes.edit', $order) }}">View Delivery Route &gt;</a>@else<span class="dm-secondary">Read-only hub access</span>@endif
                        </div>
                    </template>
                @endforeach
                </tbody>
            </table>
            <div class="dm-empty" data-dm-empty="active" hidden>
                <span class="dm-empty-icon"><svg viewBox="0 0 64 64" aria-hidden="true"><rect x="12" y="14" width="40" height="38" rx="5"/><path d="M20 24h24M20 32h17M20 40h12"/><path d="m40 43 5 5 9-11"/></svg></span>
                <h3 data-dm-empty-title="active">No active deliveries</h3><p data-dm-empty-note="active">Active Seller pickup, hub, linehaul and last-mile operations will appear here.</p>
            </div>
            <div class="dm-table-foot"><span data-dm-range="active">Showing 0–0 of {{ $activeCount }} orders</span><label class="dm-per-page">Rows per page <select aria-label="Active deliveries per page" data-dm-page-size="active"><option value="10">10</option><option value="20">20</option><option value="50">50</option></select></label></div>
        </div>
        <div class="dm-pager" aria-label="Active deliveries pagination" data-dm-pager="active" hidden></div>
    </section>

    <section id="dmDeliveredPanel" role="region" aria-label="Delivered Today" data-dm-panel="delivered" @if($initialTab !== 'delivered') hidden @endif>
        <div class="dm-table-card">
            <div class="dm-table-head"><h2>Delivered Today</h2><span class="dm-count">{{ $deliveredCount }} RECORDS</span></div>
            <table class="dm-table" aria-label="Delivered today table">
                <colgroup><col style="width:29%"><col style="width:23%"><col style="width:24%"><col style="width:16%"><col style="width:8%"></colgroup>
                <thead><tr><th>Order</th><th>Buyer</th><th>Local Rider</th><th>Completed</th><th style="text-align:center">View</th></tr></thead>
                <tbody>
                @foreach($deliveredToday as $order)
                    @php
                        $lastMile = $order->getAttribute('monitor_last_mile');
                        $localRiderName = $lastMile ? trim($lastMile->first_name.' '.$lastMile->last_name) : null;
                        $deliveredSearch = implode(' ',array_filter([$order->order_number,$order->buyer_name,$localRiderName,$order->courier_name]));
                    @endphp
                    <tr data-dm-row="delivered" data-search="{{ $deliveredSearch }}">
                        <td data-label="Order"><strong class="dm-primary">{{ $order->order_number }}</strong><span class="dm-secondary">Delivered parcel</span></td>
                        <td data-label="Buyer"><strong class="dm-primary">{{ $order->buyer_name }}</strong></td>
                        <td data-label="Local Rider"><strong class="dm-primary">{{ $localRiderName ?: $order->courier_name ?: 'Rider' }}</strong></td>
                        <td data-label="Completed"><span class="dm-status dm-status-current">Delivered</span><span class="dm-secondary">{{ $order->delivered_at?->timezone('Asia/Manila')->format('M d · g:i A') ?: 'Completed' }}</span></td>
                        <td data-label="View" style="text-align:center"><button type="button" class="dm-eye" aria-label="View completed order {{ $order->order_number }}" data-dm-open="dmDelivered{{ $loop->index }}"><svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></td>
                    </tr>
                    <template id="dmDelivered{{ $loop->index }}" data-dm-title="{{ $order->order_number }}">
                        <div class="dm-modal-status"><span class="dm-status dm-status-current">Delivered</span><span class="dm-secondary">Buyer handoff completed</span></div>
                        <div class="dm-journey-panel dm-journey-panel-complete" aria-label="Completed delivery progress">
                            <div class="dm-journey-overview"><div><span class="dm-journey-kicker">DELIVERY PROGRESS</span><strong>Delivered to Buyer</strong><p>All seven parcel checkpoints completed.</p></div><span class="dm-step-counter">Step 7 / 7</span></div>
                            <div class="dm-progress-track" role="progressbar" aria-label="Parcel progress" aria-valuemin="0" aria-valuemax="7" aria-valuenow="7"><span style="width:100%"></span></div>
                        </div>
                        <div class="dm-detail-heading">DELIVERY DETAILS</div>
                        <div class="dm-detail-grid">
                            <div class="dm-detail-box"><small>Buyer</small><strong>{{ $order->buyer_name }}</strong></div>
                            <div class="dm-detail-box"><small>Local Rider</small><strong>{{ $localRiderName ?: $order->courier_name ?: 'Rider' }}</strong></div>
                            <div class="dm-detail-box"><small>Completed</small><strong>{{ $order->delivered_at?->timezone('Asia/Manila')->format('M d, Y · g:i A') ?: 'Delivered' }}</strong></div>
                            <div class="dm-detail-box"><small>Journey Access</small><strong>{{ $isHubOperator ? 'Read-only' : 'Delivery route available' }}</strong></div>
                        </div>
                        <div style="display:flex;justify-content:flex-end;margin-top:16px">
                            @if(!$isHubOperator)<a href="{{ route('logistics.delivery-routes.edit', $order) }}" class="dm-btn dm-btn-gold">View completed journey &gt;</a>@else<span class="dm-secondary">Completed journey · Read-only</span>@endif
                        </div>
                    </template>
                @endforeach
                </tbody>
            </table>
            <div class="dm-empty" data-dm-empty="delivered" hidden><span class="dm-empty-icon"><svg viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="22"/><path d="m21 32 8 8 15-17"/></svg></span><h3 data-dm-empty-title="delivered">No completed deliveries today</h3><p data-dm-empty-note="delivered">Completed Last-Mile deliveries will appear here.</p></div>
            <div class="dm-table-foot"><span data-dm-range="delivered">Showing 0–0 of {{ $deliveredCount }} deliveries</span><label class="dm-per-page">Rows per page <select data-dm-page-size="delivered" aria-label="Delivered orders per page"><option value="10">10</option><option value="20">20</option><option value="50">50</option></select></label></div>
        </div>
        <div class="dm-pager" aria-label="Delivered today pagination" data-dm-pager="delivered" hidden></div>
    </section>

    <div class="dm-modal" id="dmDetailsModal" role="dialog" aria-modal="true" aria-labelledby="dmDetailsTitle" hidden>
        <div class="dm-modal-dialog">
            <header class="dm-modal-head"><div><p>PARCEL INFORMATION</p><h2 id="dmDetailsTitle">Parcel Details</h2></div><button type="button" class="dm-close" data-dm-close aria-label="Close parcel details">×</button></header>
            <div class="dm-modal-body" id="dmDetailsBody"></div>
            <footer class="dm-modal-footer"><button type="button" class="dm-btn" data-dm-close>Close</button></footer>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    'use strict';
    const init = () => {
        const root = document.querySelector('[data-monitor-workspace]');
        if (!root || root.dataset.initialized) return;
        root.dataset.initialized = 'true';
        const normalize = v => String(v || '').toLocaleLowerCase().trim();
        const state = {};
        const viewSelect = root.querySelector('[data-dm-view]');
        const commonSearch = root.querySelector('[data-dm-search-common]');
        const commonStatus = root.querySelector('[data-dm-status-common]');
        const viewTypes = ['active','delivered'];
        viewTypes.forEach(type => {
            const panel = root.querySelector(`[data-dm-panel="${type}"]`);
            const rows = [...panel.querySelectorAll(`[data-dm-row="${type}"]`)];
            const size = panel.querySelector(`[data-dm-page-size="${type}"]`);
            const pager = panel.querySelector(`[data-dm-pager="${type}"]`);
            const range = panel.querySelector(`[data-dm-range="${type}"]`);
            const empty = panel.querySelector(`[data-dm-empty="${type}"]`);
            const emptyTitle = panel.querySelector(`[data-dm-empty-title="${type}"]`);
            const emptyNote = panel.querySelector(`[data-dm-empty-note="${type}"]`);
            const data = state[type] = {rows,size,pager,range,empty,emptyTitle,emptyNote,page:1,keyword:'',filter:'all'};
            data.render = () => {
                const matched = data.rows.filter(row => normalize(row.dataset.search).includes(data.keyword) && (data.filter === 'all' || row.dataset.stage === data.filter));
                const per = Number(data.size.value) || 10;
                const totalPages = Math.max(1,Math.ceil(matched.length/per));
                data.page = Math.min(data.page,totalPages);
                const from = (data.page-1)*per;
                const visible = new Set(matched.slice(from,from+per));
                data.rows.forEach(row => {row.hidden = !visible.has(row)});
                data.empty.hidden = matched.length > 0;
                if (!matched.length) {
                    const filtered = !!data.keyword || data.filter !== 'all';
                    data.emptyTitle.textContent = filtered ? (type === 'active' ? 'No matching active deliveries' : 'No matching deliveries') : (type === 'active' ? 'No active deliveries' : 'No completed deliveries today');
                    data.emptyNote.textContent = filtered ? 'Try a different search or reset the filters.' : (type === 'active' ? 'Active Seller pickup, hub, linehaul and last-mile operations will appear here.' : 'Completed Last-Mile deliveries will appear here.');
                }
                data.range.textContent = `Showing ${matched.length ? from+1 : 0}–${Math.min(from+per,matched.length)} of ${matched.length} ${type === 'active' ? 'orders' : 'deliveries'}`;
                data.pager.replaceChildren();
                data.pager.hidden = totalPages <= 1;
                if (totalPages > 1) {
                    const add = (label,target,disabled,active=false) => {
                        const button = document.createElement('button');
                        button.type = 'button';button.textContent = label;button.disabled = disabled;
                        if (active) {button.classList.add('is-active');button.setAttribute('aria-current','page')}
                        button.addEventListener('click',() => {data.page=target;data.render()});
                        data.pager.appendChild(button);
                    };
                    add('<',data.page-1,data.page===1);
                    const first=Math.max(1,Math.min(data.page-2,totalPages-4));
                    for(let p=first;p<=Math.min(totalPages,first+4);p++) add(String(p),p,false,p===data.page);
                    add('>',data.page+1,data.page===totalPages);
                }
            };
            data.size.addEventListener('change',() => {data.page=1;data.render()});
            data.render();
        });
        const selectView = (type, resetControls = false) => {
            if (!viewTypes.includes(type)) return;
            viewSelect.value = type;
            root.querySelectorAll('[data-dm-panel]').forEach(panel => {panel.hidden=panel.dataset.dmPanel!==type});
            commonStatus.hidden = type !== 'active';
            if (resetControls) {
                commonSearch.value = '';
                commonStatus.value = 'all';
                state[type].keyword = '';
                state[type].filter = 'all';
                state[type].page = 1;
            }
            // Mirror the current view's applied search, without affecting the other list.
            else {
                commonSearch.value = state[type].keyword;
                commonStatus.value = state[type].filter;
            }
            state[type].render();
        };
        viewSelect.addEventListener('change', () => selectView(viewSelect.value, true));
        const apply = () => {
            const data = state[viewSelect.value];
            data.keyword = normalize(commonSearch.value);
            data.filter = viewSelect.value === 'active' ? commonStatus.value : 'all';
            data.page = 1;
            data.render();
        };
        root.querySelector('[data-dm-apply-common]').addEventListener('click', apply);
        commonSearch.addEventListener('keydown', event => {if(event.key==='Enter'){event.preventDefault();apply()}});
        root.querySelector('[data-dm-reset-common]').addEventListener('click', () => selectView(viewSelect.value, true));
        const initialView = viewSelect.value;
        state[initialView].keyword = normalize(commonSearch.value);
        selectView(initialView);
        const modal=root.querySelector('#dmDetailsModal');
        const modalBody=root.querySelector('#dmDetailsBody');
        const modalTitle=root.querySelector('#dmDetailsTitle');
        let previousFocus=null;
        const closeModal=() => {modal.hidden=true;modalBody.replaceChildren();document.body.style.overflow='';previousFocus?.focus?.();previousFocus=null};
        root.addEventListener('click',event => {
            const trigger=event.target.closest('[data-dm-open]');
            if (trigger) {
                const template=document.getElementById(trigger.dataset.dmOpen);
                if(!template) return;
                previousFocus=trigger;
                modalBody.replaceChildren(template.content.cloneNode(true));
                modalTitle.textContent=template.dataset.dmTitle || 'Parcel Details';
                modal.hidden=false;
                document.body.style.overflow='hidden';
                modal.querySelector('.dm-close').focus();
                return;
            }
            if(event.target.closest('[data-dm-close]') || event.target===modal) closeModal();
        });
        document.addEventListener('keydown',e => {if(modal.hidden)return;if(e.key==='Escape'){e.preventDefault();closeModal()} else if(e.key==='Tab'){const focusable=[...modal.querySelectorAll('a[href],button:not([disabled])')];if(!focusable.length)return;const first=focusable[0],last=focusable[focusable.length-1];if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus()}else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus()}}});
    };
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
</script>
@endpush
