@extends('layouts.courier')

@section('title', 'Seller Pickups')
@section('header-title', 'Seller Pickups')
@section('header-subtitle', 'Seller Collection Workflow')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
@verbatim
/* SARI rider-only Seller Pickups — typography / floating card language from Buyer Cart.
   Visual changes are scoped to this view, never to other rider or logistics pages. */
body:has(#sariSellerPickupPage),body:has(#sariSellerPickupPage) #courierMain{background:#f4f5f7!important;color:#1f2937;}
body:has(#sariSellerPickupPage) #courierSidebar{background:#fff!important;border-right:1px solid #e9edf2!important;box-shadow:none!important;}
body:has(#sariSellerPickupPage) #courierSidebarProfile{background:#fff!important;border-color:#e9edf2!important;}
body:has(#sariSellerPickupPage) #courierMain>header{background:#fff!important;border-bottom:1px solid #e9edf2!important;box-shadow:none!important;}
body:has(#sariSellerPickupPage) #courierMain>header h1{font-weight:600!important;font-size:18px!important;letter-spacing:-.03em;}
body:has(#sariSellerPickupPage) #courierMain>main{padding:25px clamp(16px,2.15vw,34px) 40px!important;}
@media(min-width:1024px){body:has(#sariSellerPickupPage):not(.courier-sidebar-collapsed) #courierMain{padding-left:244px!important;}body:has(#sariSellerPickupPage):not(.courier-sidebar-collapsed) #courierSidebar{width:244px!important;}body:has(#sariSellerPickupPage).courier-sidebar-collapsed #courierMain{padding-left:92px!important;}}
@media(max-width:1023px){body:has(#sariSellerPickupPage) #courierMain{padding-left:0!important;}}
#sariSellerPickupPage{--sp-ink:#202c3d;--sp-text:#445368;--sp-muted:#8290a1;--sp-line:#e5eaf0;--sp-gold:#c98d19;--sp-gold-dark:#ac7610;--sp-shadow:0 2px 5px rgba(15,23,42,.04),0 10px 24px rgba(15,23,42,.07),0 24px 54px rgba(15,23,42,.075),inset 0 1px 0 rgba(255,255,255,.92);width:100%;max-width:none;margin:0;font:400 13px/1.6 'Poppins',system-ui,sans-serif;color:var(--sp-ink);}
#sariSellerPickupPage *,#sariSellerPickupPage *:before,#sariSellerPickupPage *:after{box-sizing:border-box;}
#sariSellerPickupPage button,#sariSellerPickupPage input,#sariSellerPickupPage a{font-family:inherit;}
#sariSellerPickupPage a{text-decoration:none;}
#sariSellerPickupPage a:focus-visible,#sariSellerPickupPage button:focus-visible,#sariSellerPickupPage input:focus-visible{outline:2px solid var(--sp-gold);outline-offset:3px;}
#sariSellerPickupPage [hidden]{display:none!important;}
#sariSellerPickupPage .sp-page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;flex-wrap:wrap;margin-bottom:22px;}
#sariSellerPickupPage .sp-eyebrow{font-size:10px;font-weight:600;letter-spacing:.13em;text-transform:uppercase;color:#ae7c21;}
#sariSellerPickupPage .sp-title{font-size:clamp(30px,2.9vw,38px);font-weight:700;line-height:1.12;letter-spacing:-.046em;color:#1a2737;margin:7px 0 8px;}
#sariSellerPickupPage .sp-intro{font-size:13px;line-height:1.75;color:#748295;max-width:680px;margin:0;}
#sariSellerPickupPage .sp-head-actions{display:flex;flex-wrap:wrap;gap:10px;}
#sariSellerPickupPage .sp-btn{min-height:44px;padding:10px 17px;border-radius:11px;border:1px solid transparent;display:inline-flex;gap:9px;align-items:center;justify-content:center;font-size:12px;font-weight:500;white-space:nowrap;transition:background .15s ease,border-color .15s ease,box-shadow .15s ease,transform .15s ease;cursor:pointer;}
#sariSellerPickupPage .sp-btn svg{height:17px;width:17px;flex:none;stroke-width:1.8;}
#sariSellerPickupPage .sp-btn-outline{background:#fff;border-color:#dce4ec;color:#435166;box-shadow:0 2px 7px rgba(15,23,42,.03);}
#sariSellerPickupPage .sp-btn-outline:hover{background:#f9fafc;border-color:#c2cbd7;color:#1f2937;}
#sariSellerPickupPage .sp-btn-gold{background:#c98d19;color:#fff;border-color:#c98d19;box-shadow:0 5px 13px rgba(166,105,5,.12);}
#sariSellerPickupPage .sp-btn-gold:hover{background:#ae7914;border-color:#ae7914;box-shadow:0 7px 18px rgba(166,105,5,.17);}
#sariSellerPickupPage .sp-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:20px;}
#sariSellerPickupPage .sp-stat{background:#fff;border:1px solid #e7ebf0;border-radius:16px;box-shadow:var(--sp-shadow);padding:21px 23px;min-width:0;display:flex;align-items:center;gap:17px;}
#sariSellerPickupPage .sp-stat-icon{color:#8995a5;width:29px;height:29px;flex:none;}
#sariSellerPickupPage .sp-stat-icon.gold{color:#c98d19;}
#sariSellerPickupPage .sp-stat-icon svg{width:100%;height:100%;stroke-width:1.7;}
#sariSellerPickupPage .sp-stat-label{display:block;font-size:12px;font-weight:400;color:#79869a;line-height:1.45;}
#sariSellerPickupPage .sp-stat-number{display:block;margin:3px 0 2px;font-size:29px;letter-spacing:-.045em;line-height:1.15;font-weight:600;color:#202c3f;font-variant-numeric:tabular-nums;}
#sariSellerPickupPage .sp-stat-note{display:block;font-size:11px;font-weight:400;color:#98a3b1;}
#sariSellerPickupPage .sp-surface{background:#fff;border:1px solid #e7ebf0;border-radius:17px;box-shadow:var(--sp-shadow);overflow:hidden;}
#sariSellerPickupPage .sp-panel-head{padding:24px 25px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;border-bottom:1px solid #edf0f3;}
#sariSellerPickupPage .sp-panel-title{font-size:18px;font-weight:600;letter-spacing:-.03em;color:#243248;line-height:1.35;margin:0;}
#sariSellerPickupPage .sp-panel-desc{font-size:12px;line-height:1.6;color:#8995a5;margin:5px 0 0;}
#sariSellerPickupPage .sp-panel-count{font-size:12px;color:#8b97a5;font-weight:500;display:flex;align-items:center;gap:10px;white-space:nowrap;}
#sariSellerPickupPage .sp-panel-count strong{font-size:16px;font-weight:600;color:#4b5a6a;}
#sariSellerPickupPage .sp-toolbar{display:flex;align-items:center;gap:12px;justify-content:space-between;flex-wrap:wrap;padding:16px 24px;border-bottom:1px solid #eff2f5;}
#sariSellerPickupPage .sp-search{display:flex;align-items:center;gap:10px;min-height:43px;max-width:390px;min-width:200px;flex:1;border:1px solid #e4e9ef;background:#fbfcfe;border-radius:11px;padding:0 14px;color:#8995a5;}
#sariSellerPickupPage .sp-search svg{width:17px;height:17px;stroke-width:1.8;flex:none;}
#sariSellerPickupPage .sp-search:focus-within{border-color:#d4b06e;box-shadow:0 0 0 3px rgba(201,141,25,.09);}
#sariSellerPickupPage .sp-search input{width:100%;min-width:0;background:transparent;border:0;outline:0;font-size:12px;color:#233249;}
#sariSellerPickupPage .sp-search input::placeholder{color:#9ca6b4;}
#sariSellerPickupPage .sp-search input:focus-visible{outline:none;}
#sariSellerPickupPage .sp-filters{display:flex;align-items:center;gap:5px;padding:4px;background:#f6f8fa;border:1px solid #ebeff3;border-radius:11px;}
#sariSellerPickupPage .sp-filter{background:transparent;border:0;border-radius:8px;font-size:11px;color:#8290a1;font-weight:500;padding:8px 12px;cursor:pointer;}
#sariSellerPickupPage .sp-filter[aria-pressed="true"]{background:#fff;color:#9f7017;box-shadow:0 1px 5px rgba(30,41,59,.09);}
#sariSellerPickupPage .sp-order-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0;align-items:stretch;}
#sariSellerPickupPage .sp-job{position:relative;min-width:0;padding:23px 25px;display:flex;flex-direction:column;border-bottom:1px solid #edf0f3;}
#sariSellerPickupPage .sp-job:nth-child(odd){border-right:1px solid #edf0f3;}
#sariSellerPickupPage .sp-job-top{display:flex;align-items:start;gap:14px;justify-content:space-between;}
#sariSellerPickupPage .sp-job-identity{display:flex;gap:12px;align-items:flex-start;min-width:0;}
#sariSellerPickupPage .sp-job-icon{width:26px;height:26px;flex:none;color:#aa7c25;margin-top:3px;}
#sariSellerPickupPage .sp-job-icon svg{width:100%;height:100%;stroke-width:1.7;}
#sariSellerPickupPage .sp-order-number{margin:0;font-size:15px;font-weight:600;letter-spacing:-.02em;color:#26364b;overflow-wrap:anywhere;}
#sariSellerPickupPage .sp-seller{margin:4px 0 0;font-size:12px;color:#8793a4;overflow-wrap:anywhere;}
#sariSellerPickupPage .sp-status{align-self:flex-start;border:1px solid #f0e2c1;background:#fcf7eb;color:#9e6d11;border-radius:999px;padding:5px 10px;font-size:10px;font-weight:500;line-height:1.45;text-align:center;max-width:48%;overflow-wrap:anywhere;}
#sariSellerPickupPage .sp-details{margin:21px 0 0;display:grid;gap:17px;}
#sariSellerPickupPage .sp-detail{display:flex;align-items:flex-start;gap:11px;min-width:0;}
#sariSellerPickupPage .sp-detail-icon{height:19px;width:19px;flex:none;color:#8f9bad;margin-top:2px;}
#sariSellerPickupPage .sp-detail-icon svg{height:100%;width:100%;stroke-width:1.7;}
#sariSellerPickupPage .sp-detail-label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.075em;color:#98a2b0;font-weight:500;}
#sariSellerPickupPage .sp-detail-value{display:block;font-size:12.5px;line-height:1.7;color:#4c5b6e;margin-top:4px;overflow-wrap:anywhere;}
#sariSellerPickupPage .sp-job-bottom{margin-top:auto;padding-top:22px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;}
#sariSellerPickupPage .sp-method{color:#778497;font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.05em;}
#sariSellerPickupPage .sp-method:before{content:"";display:inline-block;width:7px;height:7px;border-radius:50%;background:#d9a63e;margin-right:7px;vertical-align:middle;}
#sariSellerPickupPage .sp-method.direct:before{background:#6a9c89;}
#sariSellerPickupPage .sp-job-bottom .sp-btn{min-height:40px;padding:8px 14px;font-size:11.5px;}
#sariSellerPickupPage .sp-empty{padding:60px 22px 64px;text-align:center;display:flex;flex-direction:column;align-items:center;gap:10px;}
#sariSellerPickupPage .sp-empty-icon{height:52px;width:52px;color:#98a3b2;}
#sariSellerPickupPage .sp-empty-icon svg{height:100%;width:100%;stroke-width:1.35;}
#sariSellerPickupPage .sp-empty-title{font-size:18px;font-weight:600;color:#64748b;letter-spacing:-.025em;margin:6px 0 0;}
#sariSellerPickupPage .sp-empty-desc{margin:0;font-size:12px;color:#7f8b9b;max-width:430px;line-height:1.75;}
#sariSellerPickupPage .sp-empty .sp-btn{margin-top:12px;}
#sariSellerPickupPage .sp-no-filter{padding:40px 18px;text-align:center;font-size:12px;color:#8a96a6;}
#sariSellerPickupPage .sp-footnote{display:flex;gap:9px;align-items:flex-start;margin-top:17px;padding:0 2px;color:#8591a1;font-size:11px;line-height:1.65;}
#sariSellerPickupPage .sp-footnote svg{height:17px;width:17px;flex:none;stroke-width:1.7;margin-top:1px;}
@media(hover:hover){#sariSellerPickupPage .sp-btn:hover{transform:translateY(-1px);}#sariSellerPickupPage .sp-job:hover{background:#fcfdff;}}
@media(max-width:1000px){#sariSellerPickupPage .sp-order-grid{grid-template-columns:1fr;}#sariSellerPickupPage .sp-job:nth-child(odd){border-right:0;}#sariSellerPickupPage .sp-job{padding:22px 23px;}}
@media(max-width:750px){#sariSellerPickupPage .sp-stats{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}#sariSellerPickupPage .sp-stat:nth-child(1){grid-column:1/-1;}#sariSellerPickupPage .sp-stat{padding:17px;}#sariSellerPickupPage .sp-stat-number{font-size:25px;}#sariSellerPickupPage .sp-toolbar{padding:16px;gap:10px;}#sariSellerPickupPage .sp-search{max-width:none;flex-basis:100%;}#sariSellerPickupPage .sp-filters{width:100%;justify-content:space-between;}#sariSellerPickupPage .sp-filter{flex:1;}#sariSellerPickupPage .sp-panel-head{padding:19px 18px;}#sariSellerPickupPage .sp-page-head{align-items:start;}#sariSellerPickupPage .sp-head-actions{width:100%;}#sariSellerPickupPage .sp-head-actions .sp-btn{flex:1;}#sariSellerPickupPage .sp-title{font-size:29px;}}
@media(max-width:460px){body:has(#sariSellerPickupPage) #courierMain>main{padding:16px 12px 30px!important;}#sariSellerPickupPage .sp-stat{gap:10px;padding:13px;}#sariSellerPickupPage .sp-stat-icon{width:23px;height:23px;}#sariSellerPickupPage .sp-stat-label{font-size:11px;}#sariSellerPickupPage .sp-stat-number{font-size:23px;}#sariSellerPickupPage .sp-stat-note{font-size:10px;}#sariSellerPickupPage .sp-head-actions{flex-direction:column;}#sariSellerPickupPage .sp-head-actions .sp-btn{width:100%;}#sariSellerPickupPage .sp-job{padding:19px 17px;}#sariSellerPickupPage .sp-job-top{flex-wrap:wrap;}#sariSellerPickupPage .sp-status{max-width:100%;}#sariSellerPickupPage .sp-job-bottom .sp-btn{width:100%;}#sariSellerPickupPage .sp-job-bottom .sp-method{width:100%;}#sariSellerPickupPage .sp-panel-title{font-size:17px;}}
@media(prefers-reduced-motion:reduce){#sariSellerPickupPage *{transition:none!important;}}
@endverbatim
</style>
@endpush

@section('content')
@php
    // View-only counts from the existing rider-scoped pickup orders collection.
    $pickupOrders = collect($orders);
    $logisticsPickupCount = $pickupOrders->filter(fn ($order) => (bool) $order->logisticsParcel)->count();
    $directPickupCount = $pickupOrders->count() - $logisticsPickupCount;
@endphp

<div id="sariSellerPickupPage">
    <header class="sp-page-head">
        <div>
            <span class="sp-eyebrow">Rider operations / Seller collections</span>
            <h2 class="sp-title">Seller Pickups</h2>
            <p class="sp-intro">Manage seller collections and continue your assigned pickup workflow.</p>
        </div>
        <nav class="sp-head-actions" aria-label="Pickup shortcuts">
            <a class="sp-btn sp-btn-outline" href="{{ route('courier.parcel-scanner') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3"/><path d="M8 9h3v3H8zM14 9h3v3h-3zM8 15h3v3H8zM14 15h3v3h-3z"/></svg>
                Scan Parcel QR
            </a>
            <a class="sp-btn sp-btn-gold" href="{{ route('courier.requests') }}">
                Pickup Assignments
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
            </a>
        </nav>
    </header>

    <section class="sp-stats" aria-label="Seller pickup summary">
        <article class="sp-stat">
            <span class="sp-stat-icon gold" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="7" width="16" height="14" rx="2"/><path d="M8 7V4h8v3M4 13h16"/></svg></span>
            <div><span class="sp-stat-label">Pickup-stage jobs</span><strong class="sp-stat-number">{{ number_format($pickupOrders->count()) }}</strong><span class="sp-stat-note">In this pickup view</span></div>
        </article>
        <article class="sp-stat">
            <span class="sp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v11H3V10zM3 11h18M8 21v-7h8v7"/></svg></span>
            <div><span class="sp-stat-label">To origin hub</span><strong class="sp-stat-number">{{ number_format($logisticsPickupCount) }}</strong><span class="sp-stat-note">Logistics collections</span></div>
        </article>
        <article class="sp-stat">
            <span class="sp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h12v11H3V6zm12 4h3l3 4v3h-6M7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm11 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></span>
            <div><span class="sp-stat-label">Direct delivery</span><strong class="sp-stat-number">{{ number_format($directPickupCount) }}</strong><span class="sp-stat-note">Seller to buyer</span></div>
        </article>
    </section>

    <section class="sp-surface" aria-labelledby="spPickupStageTitle">
        <div class="sp-panel-head">
            <div>
                <h3 class="sp-panel-title" id="spPickupStageTitle">Pickup Stage</h3>
                <p class="sp-panel-desc">Assigned pickup orders waiting for the next action.</p>
            </div>
            <div class="sp-panel-count"><strong>{{ number_format($pickupOrders->count()) }}</strong> {{ $pickupOrders->count() === 1 ? 'order' : 'orders' }}</div>
        </div>

        @if($pickupOrders->isNotEmpty())
            <div class="sp-toolbar">
                <label class="sp-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4 4"/></svg>
                    <input type="search" data-pickup-search autocomplete="off" placeholder="Search order, seller, or address" aria-label="Search pickup orders">
                </label>
                <div class="sp-filters" role="group" aria-label="Filter pickup orders">
                    <button class="sp-filter" type="button" data-pickup-filter="all" aria-pressed="true">All</button>
                    <button class="sp-filter" type="button" data-pickup-filter="logistics" aria-pressed="false">Logistics</button>
                    <button class="sp-filter" type="button" data-pickup-filter="direct" aria-pressed="false">Direct</button>
                </div>
            </div>
            <div class="sp-order-grid" data-pickup-grid>
                @foreach($pickupOrders as $order)
                    @php($isLogisticsPickup = (bool) $order->logisticsParcel)
                    <article class="sp-job" data-pickup-item data-pickup-type="{{ $isLogisticsPickup ? 'logistics' : 'direct' }}">
                        <div class="sp-job-top">
                            <div class="sp-job-identity">
                                <span class="sp-job-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h16v12H4zM7 8V5h10v3M4 13h16"/></svg></span>
                                <div>
                                    <h4 class="sp-order-number">{{ $order->order_number }}</h4>
                                    <p class="sp-seller">{{ $order->pickup_name ?: 'SARI Seller' }}</p>
                                </div>
                            </div>
                            <span class="sp-status">{{ $order->statusLabel() }}</span>
                        </div>
                        <div class="sp-details">
                            <div class="sp-detail">
                                <span class="sp-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                                <div><span class="sp-detail-label">Pickup address</span><span class="sp-detail-value">{{ $order->pickup_address ?: 'Pickup address unavailable' }}</span></div>
                            </div>
                            <div class="sp-detail">
                                <span class="sp-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h17m-6-6 6 6-6 6"/><path d="M4 5v14"/></svg></span>
                                <div><span class="sp-detail-label">After pickup</span><span class="sp-detail-value">{{ $isLogisticsPickup ? 'Origin Hub Intake' : 'Direct Buyer Delivery' }}</span></div>
                            </div>
                        </div>
                        <div class="sp-job-bottom">
                            <span class="sp-method {{ $isLogisticsPickup ? '' : 'direct' }}">{{ $isLogisticsPickup ? 'Logistics pickup' : 'Direct delivery' }}</span>
                            <a class="sp-btn sp-btn-gold" href="{{ route('courier.delivery.show', ['order' => $order->id]) }}">
                                Open Workflow
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="sp-no-filter" data-pickup-filter-empty hidden>No pickups match your search or filter.</div>
        @else
            <div class="sp-empty">
                <span class="sp-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m3 8 9-5 9 5v9l-9 5-9-5V8zM3 8l9 5 9-5m-9 5v9"/><path d="m16 3 2 2 3-3"/></svg></span>
                <h4 class="sp-empty-title">All caught up!</h4>
                <p class="sp-empty-desc">No active seller pickups right now. New work will appear once Logistics assigns a pickup.</p>
                <a class="sp-btn sp-btn-gold" href="{{ route('courier.requests') }}">View Pickup Assignments
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>
        @endif
    </section>

    <p class="sp-footnote">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-9v1"/></svg>
        For logistics pickups, the rider's leg ends when the origin hub physically accepts the parcel. A separate assigned rider handles last-mile delivery.
    </p>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const root = document.getElementById('sariSellerPickupPage');
    if (!root) return;

    const input = root.querySelector('[data-pickup-search]');
    const buttons = [...root.querySelectorAll('[data-pickup-filter]')];
    const jobs = [...root.querySelectorAll('[data-pickup-item]')];
    const empty = root.querySelector('[data-pickup-filter-empty]');
    const grid = root.querySelector('[data-pickup-grid]');
    let filter = 'all';

    const refresh = () => {
        const query = (input?.value ?? '').trim().toLocaleLowerCase();
        let visible = 0;
        for (const job of jobs) {
            const matchesType = filter === 'all' || job.dataset.pickupType === filter;
            const matchesSearch = !query || job.textContent.toLocaleLowerCase().includes(query);
            job.hidden = !(matchesType && matchesSearch);
            if (!job.hidden) visible++;
        }
        if (empty) empty.hidden = visible !== 0;
        if (grid) grid.hidden = visible === 0;
    };

    input?.addEventListener('input', refresh);
    buttons.forEach(button => button.addEventListener('click', () => {
        filter = button.dataset.pickupFilter || 'all';
        buttons.forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        refresh();
    }));
})();
</script>
@endpush
