@extends('layouts.courier')

@section('title', 'Pickup Workflow')
@section('header-title', 'Pickup Workflow')
@section('header-subtitle', 'Dedicated Rider Job Workspace')

{{-- Single-file Rider design: all page-specific CSS is embedded below. --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">
<style>
@verbatim
/* SARI Rider | Pickup / Direct Delivery Workflow — UI ONLY */
.courier-shell #courierPageContent:has(#riderPickupWorkflow) { padding: 28px 40px; }
#riderPickupWorkflow {
    --pw-ink:#22211f; --pw-muted:#77808e; --pw-stroke:#e7e9ee;
    --pw-gold:#d69a1a; --pw-blue:#3c6e91; --pw-green:#4f7d63;
    width:100%; max-width:1500px; margin:0 auto; color:var(--pw-ink);
    font:400 12px/1.65 "Poppins",ui-sans-serif,system-ui,sans-serif;
}
#riderPickupWorkflow, #riderPickupWorkflow * { box-sizing:border-box; }
#riderPickupWorkflow :is(h2,h3,h4,p,dl,dd,dt,ol) { margin:0; }
#riderPickupWorkflow :is(a,button):focus-visible { outline:3px solid #c28c20; outline-offset:3px; }
#riderPickupWorkflow button { font:inherit; cursor:pointer; }
#riderPickupWorkflow a { text-decoration:none; }
#riderPickupWorkflow > * + * { margin-top:18px; }
#riderPickupWorkflow :is(article,section,aside,div) { min-width:0; }
/* Feedback */
#riderPickupWorkflow .pw-alert { padding:14px 18px; border-radius:13px; border:1px solid; font-size:12px; line-height:1.6; }
#riderPickupWorkflow .pw-alert-ok { color:#286744; background:#eef8f1; border-color:#c5e4d0; }
#riderPickupWorkflow .pw-alert-error { color:#9e3542; background:#fff1f3; border-color:#efc9d0; }
/* Shared buttons */
#riderPickupWorkflow .pw-button {
    display:inline-flex; align-items:center; justify-content:center; gap:9px; padding:11px 16px;
    min-height:43px; border:1px solid #e4e6ea; border-radius:11px;
    background:white; color:#4b5563; font-size:11px; line-height:1.4;
    font-weight:600; text-align:center; transition:background .18s,border-color .18s,transform .18s;
}
#riderPickupWorkflow .pw-button:hover { border-color:#d8bb87; background:#fffaf0; }
#riderPickupWorkflow .pw-button:active { transform:translateY(1px); }
#riderPickupWorkflow .pw-button-gold { color:#221a0d; border-color:#e5ac37; background:#e9af39; }
#riderPickupWorkflow .pw-button-gold:hover { background:#f5bb44; border-color:#efba4a; }
#riderPickupWorkflow .pw-button-blue { color:white; border-color:#3c6e91; background:#3c6e91; }
#riderPickupWorkflow .pw-button-blue:hover { background:#315d7d; border-color:#315d7d; }
#riderPickupWorkflow .pw-button-green { color:white; border-color:#4f7d63; background:#4f7d63; }
#riderPickupWorkflow .pw-button-green:hover { background:#3b694f; border-color:#3b694f; }
#riderPickupWorkflow .pw-button-full { width:100%; min-height:48px; padding:13px 18px; font-size:12px; }
/* Dark hero — aligned with Rider Dashboard / Active Deliveries */
#riderPickupWorkflow .pw-hero {
    position:relative; overflow:hidden; display:flex; flex-wrap:wrap; align-items:center;
    justify-content:space-between; gap:20px; padding:27px 30px;
    border:1px solid #35322d; border-radius:19px; background:#20201f; color:#fff;
}
#riderPickupWorkflow .pw-hero::before {
    content:""; pointer-events:none; position:absolute; inset:0;
    background:radial-gradient(circle at 5% 55%,rgb(228 166 28 / 12%),transparent 36%),
               linear-gradient(110deg,rgb(255 255 255 / 3%),transparent 55%);
}
#riderPickupWorkflow .pw-hero-copy, #riderPickupWorkflow .pw-hero-side { position:relative; z-index:1; }
#riderPickupWorkflow .pw-hero-copy { flex:1 1 470px; }
#riderPickupWorkflow .pw-breadcrumb { display:flex; flex-wrap:wrap; align-items:center; gap:8px; margin-bottom:15px; font-size:10px; }
#riderPickupWorkflow .pw-breadcrumb a { color:#d3b989; }
#riderPickupWorkflow .pw-breadcrumb a:hover { color:#ffdb8e; }
#riderPickupWorkflow .pw-breadcrumb span { color:#92877a; }
#riderPickupWorkflow .pw-breadcrumb strong { font-weight:600; color:#faf3e5; }
#riderPickupWorkflow .pw-eyebrow { display:flex; align-items:center; gap:8px; color:#ebc36b; font-size:10px; letter-spacing:.12em; font-weight:600; text-transform:uppercase; }
#riderPickupWorkflow .pw-eyebrow::before { content:""; width:7px; height:7px; border-radius:100%; background:#e9ac2d; box-shadow:0 0 0 4px rgb(233 172 45 / 15%); }
#courierPageContent #riderPickupWorkflow .pw-hero-title { margin-top:9px; color:#fff; font-size:clamp(23px,2.3vw,32px); letter-spacing:-.042em; font-weight:700; line-height:1.25; }
#riderPickupWorkflow .pw-hero-title span { color:#e9b34b; }
#riderPickupWorkflow .pw-hero-description { max-width:660px; margin-top:10px; font-size:11px; line-height:1.8; color:#c7c4c0; }
#riderPickupWorkflow .pw-hero-side { flex:0 1 250px; display:flex; flex-direction:column; align-items:flex-end; gap:13px; }
#riderPickupWorkflow .pw-order-label { font-size:9px; color:#afaba5; letter-spacing:.1em; font-weight:600; text-transform:uppercase; }
#riderPickupWorkflow .pw-order-number { font-size:20px; line-height:1.3; font-weight:700; color:white; letter-spacing:-.03em; overflow-wrap:anywhere; }
#riderPickupWorkflow .pw-status-badge { display:inline-flex; align-items:center; gap:7px; padding:5px 11px; border-radius:999px; color:#b9d5eb; font-size:10px; font-weight:700; border:1px solid #477493; background:rgb(60 110 145 / 20%); }
#riderPickupWorkflow .pw-status-badge::before { content:""; height:6px; width:6px; border-radius:50%; background:#8bc1e7; }
#riderPickupWorkflow .pw-status-badge.pw-completed { background:rgb(79 125 99 / 20%); border-color:#4f7d63; color:#b5dfc3; }
#riderPickupWorkflow .pw-status-badge.pw-completed::before { background:#8bcba0; }
#riderPickupWorkflow .pw-hero-actions { display:flex; flex-wrap:wrap; gap:8px; justify-content:flex-end; }
#riderPickupWorkflow .pw-hero-actions .pw-button { min-height:36px; font-size:10px; padding:8px 12px; }
/* Progress tracker */
#riderPickupWorkflow .pw-panel { background:#fff; border:1px solid var(--pw-stroke); border-radius:18px; overflow:hidden; box-shadow:0 2px 14px rgb(18 31 46 / 2%); }
#riderPickupWorkflow .pw-panel-heading { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:18px 21px; border-bottom:1px solid #eef0f2; }
#riderPickupWorkflow .pw-section-title { font-size:14px; font-weight:700; letter-spacing:-.02em; color:#26313d; }
#riderPickupWorkflow .pw-section-subtitle { margin-top:2px; color:var(--pw-muted); font-size:10px; }
#riderPickupWorkflow .pw-heading-pill { flex-shrink:0; font-size:10px; font-weight:600; border:1px solid #e8ebed; border-radius:100px; padding:5px 11px; color:#687584; background:#f9fafb; }
#riderPickupWorkflow .pw-progress-wrap { padding:19px 20px 20px; overflow-x:auto; scrollbar-color:#d5d8dc transparent; }
#riderPickupWorkflow .pw-progress { display:grid; gap:9px; list-style:none; padding:0; }
#riderPickupWorkflow .pw-progress.pw-six { min-width:680px; grid-template-columns:repeat(6,minmax(0,1fr)); }
#riderPickupWorkflow .pw-progress.pw-four { min-width:500px; grid-template-columns:repeat(4,minmax(0,1fr)); }
#riderPickupWorkflow .pw-step { position:relative; padding:14px 12px 12px; min-height:110px; background:#f8f9fb; border:1px solid #e9ebf0; border-radius:13px; }
#riderPickupWorkflow .pw-step-number { display:grid; place-items:center; width:29px; height:29px; border-radius:9px; background:#e8ebef; color:#7c8692; font-weight:700; font-size:12px; }
#riderPickupWorkflow .pw-step-name { margin-top:9px; color:#46505b; font-size:10px; font-weight:700; line-height:1.4; }
#riderPickupWorkflow .pw-step-detail { margin-top:3px; color:#8b94a0; font-size:9px; }
#riderPickupWorkflow .pw-step.is-done { background:#f0f8f2; border-color:#cbe6d3; }
#riderPickupWorkflow .pw-step.is-done .pw-step-number { color:white; background:#4f7d63; }
#riderPickupWorkflow .pw-step.is-done .pw-step-name { color:#336b48; }
#riderPickupWorkflow .pw-step.is-current { background:#fff8e9; border-color:#e7c47d; box-shadow:inset 0 0 0 1px rgb(217 153 24 / 8%); }
#riderPickupWorkflow .pw-step.is-current .pw-step-number { color:#28210e; background:#e9b13f; }
#riderPickupWorkflow .pw-step.is-current .pw-step-name { color:#7e5513; }
#riderPickupWorkflow .pw-step.is-current .pw-step-detail { color:#9a701f; }
/* Main workflow area */
#riderPickupWorkflow .pw-columns { display:grid; grid-template-columns:minmax(0,1.25fr) minmax(290px,.75fr); align-items:start; gap:18px; }
#riderPickupWorkflow .pw-side { display:grid; gap:18px; }
#riderPickupWorkflow .pw-action-body { padding:23px; }
#riderPickupWorkflow .pw-action-intro { display:flex; gap:13px; align-items:flex-start; }
#riderPickupWorkflow .pw-action-symbol { display:grid; place-items:center; width:44px; height:44px; flex:0 0 44px; border-radius:14px; background:#fff3d9; color:#9a6611; }
#riderPickupWorkflow .pw-action-symbol svg { width:23px; height:23px; }
#riderPickupWorkflow .pw-overline { color:#b17c20; font-size:9px; font-weight:700; letter-spacing:.13em; text-transform:uppercase; }
#riderPickupWorkflow .pw-action-title { margin-top:3px; color:#1f2937; font-size:20px; font-weight:700; line-height:1.35; letter-spacing:-.03em; }
#riderPickupWorkflow .pw-action-kind { display:inline-flex; padding:5px 10px; border:1px solid #ece6da; border-radius:99px; color:#7b6c58; background:#faf8f4; font-size:10px; font-weight:600; white-space:nowrap; }
#riderPickupWorkflow .pw-action-top { display:flex; flex-wrap:wrap; justify-content:space-between; gap:14px; align-items:center; }
#riderPickupWorkflow .pw-instruction { margin-top:22px; background:#f6f8fb; border:1px solid #e6eaf0; padding:19px; border-radius:14px; }
#riderPickupWorkflow .pw-instruction-label { display:flex; align-items:center; gap:8px; color:#4f6377; font-size:11px; font-weight:700; }
#riderPickupWorkflow .pw-instruction-label::before { content:"i"; display:grid; place-items:center; height:19px; width:19px; border-radius:50%; background:#dfeaf4; color:#3c6e91; font-size:12px; }
#riderPickupWorkflow .pw-instruction p { margin-top:7px; color:#617084; font-size:11px; line-height:1.85; }
#riderPickupWorkflow .pw-instruction.pw-instruction-gold { background:#fffaed; border-color:#f0dfb7; }
#riderPickupWorkflow .pw-instruction.pw-instruction-gold .pw-instruction-label { color:#936a21; }
#riderPickupWorkflow .pw-instruction.pw-instruction-gold .pw-instruction-label::before { background:#f9e8ba; color:#936a21; }
#riderPickupWorkflow .pw-instruction.pw-instruction-green { background:#f1f9f3; border-color:#cee7d6; }
#riderPickupWorkflow .pw-instruction.pw-instruction-green .pw-instruction-label { color:#3c7552; }
#riderPickupWorkflow .pw-instruction.pw-instruction-green .pw-instruction-label::before { content:"✓"; background:#d9eee0; color:#346a47; }
#riderPickupWorkflow .pw-instruction.pw-instruction-green p { color:#567565; }
#riderPickupWorkflow .pw-action-form { margin-top:18px; }
#riderPickupWorkflow .pw-handoff { padding:14px 16px; background:white; border:1px solid #d5e8dc; border-radius:11px; margin-top:15px; }
#riderPickupWorkflow .pw-handoff-label { color:#6d8b77; font-weight:600; font-size:9px; text-transform:uppercase; letter-spacing:.1em; }
#riderPickupWorkflow .pw-handoff strong { display:block; margin-top:5px; font-size:12px; color:#326a48; }
#riderPickupWorkflow .pw-footnote { margin-top:12px; font-size:10px; color:#708678; line-height:1.7; }
/* Job detail & timeline */
#riderPickupWorkflow .pw-details { padding:17px 19px 19px; }
#riderPickupWorkflow .pw-detail-grid { display:grid; gap:0; }
#riderPickupWorkflow .pw-detail-row { padding:12px 0; border-bottom:1px solid #eff1f3; }
#riderPickupWorkflow .pw-detail-row:last-child { border-bottom:none; }
#riderPickupWorkflow .pw-field-label { color:#87909b; font-size:9px; font-weight:700; letter-spacing:.09em; text-transform:uppercase; }
#riderPickupWorkflow .pw-field-value { margin-top:5px; color:#2b3340; font-size:12px; line-height:1.65; overflow-wrap:anywhere; font-weight:600; }
#riderPickupWorkflow .pw-field-value.pw-normal { font-size:11px; font-weight:500; color:#627084; }
#riderPickupWorkflow .pw-route-track { display:grid; grid-template-columns:17px minmax(0,1fr); gap:10px; margin-top:6px; }
#riderPickupWorkflow .pw-route-marks { display:flex; align-items:center; flex-direction:column; padding:6px 0 7px; }
#riderPickupWorkflow .pw-route-dot { height:9px; width:9px; border:2px solid #d59b23; background:#fff; border-radius:50%; }
#riderPickupWorkflow .pw-route-line { flex:1; min-height:30px; margin:5px 0; border-left:2px dashed #dfe3e8; }
#riderPickupWorkflow .pw-route-dot.pw-end { background:#3c6e91; border-color:#3c6e91; }
#riderPickupWorkflow .pw-route-content { display:grid; gap:18px; }
#riderPickupWorkflow .pw-route-item .pw-field-value { margin-top:2px; }
#riderPickupWorkflow .pw-timeline { padding:18px 19px; }
#riderPickupWorkflow .pw-events { list-style:none; padding:0; margin-top:8px; }
#riderPickupWorkflow .pw-event { display:grid; grid-template-columns:13px minmax(0,1fr) auto; align-items:center; gap:9px; padding:10px 0; border-bottom:1px solid #f0f1f3; }
#riderPickupWorkflow .pw-event:last-child { border-bottom:0; }
#riderPickupWorkflow .pw-event-dot { display:block; width:8px; height:8px; border-radius:50%; background:#dce1e7; }
#riderPickupWorkflow .pw-event-dot.is-set { background:#4f7d63; }
#riderPickupWorkflow .pw-event-label { font-size:10px; font-weight:600; color:#546170; }
#riderPickupWorkflow .pw-event-time { color:#85909d; text-align:right; white-space:nowrap; font-size:9px; }
#riderPickupWorkflow .pw-rule { padding:18px 19px; background:#f4faf6; border-color:#d8e9de; }
#riderPickupWorkflow .pw-rule .pw-section-title { color:#3f7655; }
#riderPickupWorkflow .pw-rule p { margin-top:7px; color:#66816f; font-size:10px; line-height:1.8; }
/* Responsive */
@media (max-width:1150px) { .courier-shell #courierPageContent:has(#riderPickupWorkflow) { padding:22px 24px; } }
@media (max-width:950px) { #riderPickupWorkflow .pw-columns { grid-template-columns:minmax(0,1fr); } #riderPickupWorkflow .pw-side { grid-template-columns:repeat(2,minmax(0,1fr)); } #riderPickupWorkflow .pw-rule { grid-column:1/-1; } }
@media (max-width:700px) {
    .courier-shell #courierPageContent:has(#riderPickupWorkflow) { padding:15px 13px; }
    #riderPickupWorkflow > * + * { margin-top:14px; }
    #riderPickupWorkflow .pw-hero { padding:22px 18px; border-radius:16px; }
    #courierPageContent #riderPickupWorkflow .pw-hero-title { font-size:25px; }
    #riderPickupWorkflow .pw-hero-side { flex:1 1 100%; align-items:flex-start; }
    #riderPickupWorkflow .pw-hero-actions { justify-content:flex-start; }
    #riderPickupWorkflow .pw-panel { border-radius:15px; }
    #riderPickupWorkflow .pw-panel-heading { padding:16px; }
    #riderPickupWorkflow .pw-progress-wrap { padding:15px; }
    #riderPickupWorkflow .pw-side { grid-template-columns:minmax(0,1fr); }
    #riderPickupWorkflow .pw-action-body { padding:17px; }
    #riderPickupWorkflow .pw-action-title { font-size:18px; }
    #riderPickupWorkflow .pw-instruction { margin-top:18px; padding:16px; }
}
@media (max-width:380px) {
    #riderPickupWorkflow .pw-hero-actions { width:100%; }
    #riderPickupWorkflow .pw-hero-actions .pw-button { flex:1 1 auto; }
    #riderPickupWorkflow .pw-action-kind { white-space:normal; }
    #riderPickupWorkflow .pw-event { grid-template-columns:11px minmax(0,1fr); }
    #riderPickupWorkflow .pw-event-time { grid-column:2; text-align:left; }
}
@media (prefers-reduced-motion:reduce) { #riderPickupWorkflow .pw-button { transition:none; } }

/* Assignment route map: saved coordinates only; no fictitious straight-line journey. */
#riderPickupWorkflow .pw-route-map-stage {position:relative;min-height:350px;background:#e8eff2;}
#riderPickupWorkflow .pw-route-map-canvas {height:350px;width:100%;z-index:1;}
#riderPickupWorkflow .pw-map-placeholder {padding:60px 20px;text-align:center;color:#758296;}
#riderPickupWorkflow .pw-route-map-bottom {padding:14px 19px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;border-top:1px solid #edf0f3;}
#riderPickupWorkflow .pw-map-help {max-width:660px;color:#778496;font-size:10px;line-height:1.7;}
#riderPickupWorkflow .pw-map-actions {display:flex;flex-wrap:wrap;gap:8px;}
#riderPickupWorkflow .pw-map-pin {height:17px;width:17px;background:#397bf2;border:3px solid #fff;border-radius:50%;box-shadow:0 0 0 2px #397bf2,0 2px 8px #1b304440;}
#riderPickupWorkflow .pw-map-pin.is-destination {background:#21a779;box-shadow:0 0 0 2px #21a779,0 2px 8px #1b304440;}
#riderPickupWorkflow .pw-route-map-stage .leaflet-container {font-family:Poppins,system-ui,sans-serif;}
#riderPickupWorkflow .pw-route-map-stage .leaflet-control-attribution {font-size:9px;}
@media(max-width:700px){#riderPickupWorkflow .pw-route-map-stage,#riderPickupWorkflow .pw-route-map-canvas{min-height:275px;height:275px;}#riderPickupWorkflow .pw-route-map-bottom{padding:13px;}}
@endverbatim
</style>
@endpush

@section('content')
@php
    // Original read-only workflow data and status mapping — unchanged.
    $isLogisticsPickup = (bool) $order->logisticsParcel;
    $status = (string) $order->status;
    $sellerName = $order->pickup_name ?: $order->seller?->store_name ?: 'Seller';
    $pickupAddress = $order->pickup_address ?: 'Pickup address not available';
    $originHubStop = $order->deliveryRouteStops->firstWhere('stop_key', 'origin_facility');
    $destinationLabel = $isLogisticsPickup
        ? ($originHubStop?->name ?: 'Assigned Origin Hub')
        : ($order->buyer_name ?: 'Buyer');
    $destinationDetail = $isLogisticsPickup
        ? ($originHubStop?->address_snapshot ?: 'Hand the parcel to SARI Logistics for origin-hub intake.')
        : ($order->buyer_address ?: 'Buyer address not available');

    $statusIndex = match ($status) {
        'courier_accepted' => 1,
        'heading_pickup' => 2,
        'arrived_pickup' => 3,
        'in_transit' => 4,
        'arrived_buyer' => 5,
        'delivered' => 6,
        default => 0,
    };

    $steps = $isLogisticsPickup
        ? [
            ['Assigned', 1],
            ['Heading to Seller', 2],
            ['At Seller', 3],
            ['To Origin Hub', 4],
        ]
        : [
            ['Assigned', 1],
            ['Heading to Seller', 2],
            ['At Seller', 3],
            ['In Transit', 4],
            ['At Buyer', 5],
            ['Delivered', 6],
        ];

    // This Rider is authorized to see only stops on the current leg.
    $visibleStopKeys = $isLogisticsPickup
        ? ['seller', 'origin_facility']
        : ['seller', 'buyer'];
    $nextStopKey = in_array($status, ['courier_accepted', 'heading_pickup'], true)
        ? 'seller'
        : ($isLogisticsPickup ? 'origin_facility' : 'buyer');
    $mapStops = $order->deliveryRouteStops
        ->whereIn('stop_key', $visibleStopKeys)
        ->filter(function ($stop) {
            if ($stop->latitude === null || $stop->longitude === null) return false;
            $lat = (float) $stop->latitude;
            $lng = (float) $stop->longitude;
            return is_finite($lat) && is_finite($lng) && abs($lat) <= 90 && abs($lng) <= 180
                && ! ($lat === 0.0 && $lng === 0.0);
        })
        ->map(fn ($stop) => [
            'key' => (string) $stop->stop_key,
            'lat' => (float) $stop->latitude,
            'lng' => (float) $stop->longitude,
            'name' => (string) ($stop->name ?: ($stop->stop_key === 'seller' ? 'Seller Pickup' : 'Destination')),
        ])->values()->all();

    $workflowRecord = [
        ['Assigned', $order->accepted_at],
        ['Started Pickup', $order->pickup_started_at],
        ['Arrived at Seller', $order->arrived_pickup_at],
        ['Picked Up', $order->picked_up_at],
    ];
    if (! $isLogisticsPickup) {
        $workflowRecord[] = ['Arrived at Buyer', $order->arrived_buyer_at];
        $workflowRecord[] = ['Delivered', $order->delivered_at];
    }
@endphp

<div id="riderPickupWorkflow" class="courier-workspace">
    {{-- Feedback from the existing backend --}}
    @if(session('success'))
        <div class="pw-alert pw-alert-ok" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="pw-alert pw-alert-error" role="alert">{{ $errors->first() }}</div>
    @endif

    {{-- Dark overview hero, matching the Rider Dashboard --}}
    <section class="pw-hero" aria-labelledby="pickupWorkflowTitle">
        <div class="pw-hero-copy">
            <nav class="pw-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('courier.requests') }}">Pickup Assignments</a>
                <span aria-hidden="true">/</span>
                <strong aria-current="page">{{ $order->order_number }}</strong>
            </nav>
            <p class="pw-eyebrow">Rider Operations / Dedicated Job</p>
            <h2 id="pickupWorkflowTitle" class="pw-hero-title">
                {{ $isLogisticsPickup ? 'Seller Pickup' : 'Direct Delivery' }} <span>Workflow</span>
            </h2>
            <p class="pw-hero-description">
                @if($isLogisticsPickup)
                    Collect the parcel from the Seller and hand it over to the assigned origin hub.
                    The destination last-mile Rider handles final Buyer delivery.
                @else
                    Follow your assigned route from Seller pickup to Buyer handoff.
                    Confirm each stage only when the corresponding step is complete.
                @endif
            </p>
        </div>
        <div class="pw-hero-side">
            <div>
                <p class="pw-order-label">Assigned order</p>
                <p class="pw-order-number">{{ $order->order_number }}</p>
            </div>
            <span class="pw-status-badge {{ $status === 'delivered' ? 'pw-completed' : '' }}">{{ $order->statusLabel() }}</span>
            <div class="pw-hero-actions">
                <a class="pw-button" href="{{ route('courier.dashboard') }}">Dashboard</a>
                <a class="pw-button pw-button-gold" href="{{ route('courier.requests') }}">Pickup Queue <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    {{-- Stepper, derived from the same existing statusIndex and steps --}}
    <section class="pw-panel" aria-labelledby="workflowProgressHeading">
        <header class="pw-panel-heading">
            <div>
                <h3 id="workflowProgressHeading" class="pw-section-title">Workflow Progress</h3>
                <p class="pw-section-subtitle">Current status and the next stages in your assignment</p>
            </div>
            <span class="pw-heading-pill">{{ count($steps) }} Stages</span>
        </header>
        <div class="pw-progress-wrap" role="region" aria-label="Scroll horizontally to view all workflow stages" tabindex="0">
            <ol class="pw-progress {{ count($steps) === 4 ? 'pw-four' : 'pw-six' }}">
                @foreach($steps as [$label, $step])
                    @php
                        $done = $statusIndex > $step;
                        $current = $statusIndex === $step;
                    @endphp
                    <li class="pw-step {{ $done ? 'is-done' : ($current ? 'is-current' : '') }}"
                        @if($current) aria-current="step" @endif>
                        <span class="pw-step-number" aria-hidden="true">{{ $done ? '✓' : $step }}</span>
                        <p class="pw-step-name">{{ $label }}</p>
                        <p class="pw-step-detail">{{ $done ? 'Completed' : ($current ? 'Current stage' : 'Upcoming') }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Coordinates come from this order's saved route stops; road directions are external. --}}
    <section class="pw-panel" aria-labelledby="riderLegMapHeading">
        <header class="pw-panel-heading">
            <div>
                <h3 class="pw-section-title" id="riderLegMapHeading">Your Assigned Destination Map</h3>
                <p class="pw-section-subtitle">
                    @if($isLogisticsPickup)
                        Seller pickup → Origin hub · Buyer address is handled by the last-mile rider
                    @else
                        Seller pickup → Buyer handoff
                    @endif
                </p>
            </div>
            <span class="pw-heading-pill">Saved locations</span>
        </header>
        <div class="pw-route-map-stage">
            <div id="sariPickupLegMap" class="pw-route-map-canvas" role="region" aria-label="Street map of saved locations on your assignment"></div>
        </div>
        <div class="pw-route-map-bottom">
            <p id="sariPickupMapNote" class="pw-map-help" role="status">Map will display saved Seller and destination pins. No travel time or route is estimated by SARI.</p>
            <div class="pw-map-actions">
                <button type="button" class="pw-button" id="sariPickupMapFit">Show all stops</button>
                <button type="button" class="pw-button pw-button-blue" id="sariPickupNavigate">Open road directions ↗</button>
            </div>
        </div>
    </section>

    <section class="pw-columns" aria-label="Pickup and delivery workspace">
        {{-- Workflow POST actions are identical to the supplied Blade. --}}
        <article class="pw-panel" aria-labelledby="currentActionTitle">
            <header class="pw-panel-heading">
                <div>
                    <h3 class="pw-section-title">Current Action</h3>
                    <p class="pw-section-subtitle">Complete the action for your current stage</p>
                </div>
                <span class="pw-action-kind">{{ $isLogisticsPickup ? 'Operational Pickup' : 'Direct Delivery' }}</span>
            </header>
            <div class="pw-action-body">
                <div class="pw-action-top">
                    <div class="pw-action-intro">
                        <span class="pw-action-symbol" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg>
                        </span>
                        <div>
                            <p class="pw-overline">Next step</p>
                            <h4 id="currentActionTitle" class="pw-action-title">
                                @if($status === 'courier_accepted')
                                    Start the Seller pickup
                                @elseif($status === 'heading_pickup')
                                    Arrive at the Seller
                                @elseif($status === 'arrived_pickup')
                                    Confirm parcel handoff
                                @elseif($status === 'in_transit' && $isLogisticsPickup)
                                    Deliver parcel to origin hub
                                @elseif($status === 'in_transit')
                                    Continue to Buyer
                                @elseif($status === 'arrived_buyer')
                                    Complete Buyer handoff
                                @else
                                    Review current job status
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>

                @if($status === 'courier_accepted')
                    <div class="pw-instruction pw-instruction-gold">
                        <p class="pw-instruction-label">Pickup assignment accepted</p>
                        <p>Proceed to the Seller pickup location. Use the button below only when you are ready to begin traveling to the Seller.</p>
                    </div>
                    <form method="POST" action="{{ route('courier.orders.proceed-pickup', $order) }}" class="pw-action-form">
                        @csrf
                        <button type="submit" class="pw-button pw-button-gold pw-button-full">Proceed to Seller Pickup <span aria-hidden="true">→</span></button>
                    </form>
                @elseif($status === 'heading_pickup')
                    <div class="pw-instruction">
                        <p class="pw-instruction-label">Heading to the Seller</p>
                        <p>Confirm arrival only after you physically reach the Seller pickup point.</p>
                    </div>
                    <form method="POST" action="{{ route('courier.orders.arrived-pickup', $order) }}" class="pw-action-form">
                        @csrf
                        <button type="submit" class="pw-button pw-button-blue pw-button-full">I Arrived at the Seller <span aria-hidden="true">✓</span></button>
                    </form>
                @elseif($status === 'arrived_pickup')
                    <div class="pw-instruction">
                        <p class="pw-instruction-label">Verify the parcel before pickup</p>
                        <p>Check the parcel and Seller handoff before confirming that it is now in your custody.</p>
                    </div>
                    @if($isLogisticsPickup)
                        <div class="pw-instruction pw-instruction-gold" style="margin-top:12px;">
                            <p class="pw-instruction-label">Parcel QR scan required</p>
                            <p>Ask the Seller to show the QR printed for this order. Scan it only at the physical handoff. A verified scan records custody and moves the parcel to origin-hub transit.</p>
                        </div>
                        <a href="{{ route('courier.parcel-scanner') }}" class="pw-button pw-button-blue pw-button-full pw-action-form">Scan Seller Parcel QR ↗</a>
                    @else
                        <form method="POST" action="{{ route('courier.orders.confirm-pickup', $order) }}" class="pw-action-form">
                            @csrf
                            <button type="submit" class="pw-button pw-button-blue pw-button-full">Confirm Item Pickup <span aria-hidden="true">→</span></button>
                        </form>
                    @endif
                @elseif($status === 'in_transit' && $isLogisticsPickup)
                    <div class="pw-instruction pw-instruction-green">
                        <p class="pw-instruction-label">Seller pickup completed</p>
                        <p>Bring this parcel to the assigned origin hub. There is no Buyer-delivery action on this page because the destination last-mile Rider owns final delivery.</p>
                        <div class="pw-handoff">
                            <p class="pw-handoff-label">Next handoff</p>
                            <strong>SARI Logistics · Origin Hub Intake</strong>
                        </div>
                        <p class="pw-footnote">This pickup job remains active until the authorized Logistics staff scans the parcel QR and records its physical intake at the origin hub.</p>
                    </div>
                    <a href="{{ route('courier.requests') }}" class="pw-button pw-button-full" style="margin-top:18px;">Back to Pickup Assignments</a>
                @elseif($status === 'in_transit')
                    <div class="pw-instruction">
                        <p class="pw-instruction-label">Direct delivery in transit</p>
                        <p>Continue to the Buyer. Confirm arrival only when you physically reach the Buyer; the final QR scan will be available after this step.</p>
                    </div>
                    <form method="POST" action="{{ route('courier.orders.arrived-buyer', $order) }}" class="pw-action-form">
                        @csrf
                        <button type="submit" class="pw-button pw-button-blue pw-button-full">Confirm Arrived at Buyer <span aria-hidden="true">→</span></button>
                    </form>
                @elseif($status === 'arrived_buyer' && ! $isLogisticsPickup)
                    <div class="pw-instruction pw-instruction-green">
                        <p class="pw-instruction-label">Buyer location reached</p>
                        <p>Arrival is recorded. Physically hand the parcel to the Buyer, then scan the matching SARI parcel QR. The scan verifies the order and records the delivery; a manual completion button is not allowed.</p>
                    </div>
                    <a href="{{ route('courier.parcel-scanner') }}" class="pw-button pw-button-green pw-button-full pw-action-form">Scan Parcel QR to Complete Delivery <span aria-hidden="true">↗</span></a>
                @else
                    <div class="pw-instruction">
                        <p class="pw-instruction-label">No action available for this state</p>
                        <p>Return to your assignment queue or dashboard to review current work.</p>
                    </div>
                @endif
            </div>
        </article>

        <aside class="pw-side" aria-label="Job information and activity">
            <article class="pw-panel" aria-labelledby="jobDetailsHeading">
                <header class="pw-panel-heading">
                    <div>
                        <h3 id="jobDetailsHeading" class="pw-section-title">Job Details</h3>
                        <p class="pw-section-subtitle">{{ $order->order_number }}</p>
                    </div>
                    <span class="pw-heading-pill">{{ $isLogisticsPickup ? 'Pickup Leg' : 'Direct' }}</span>
                </header>
                <div class="pw-details">
                    <div class="pw-detail-row">
                        <p class="pw-field-label">Assigned route</p>
                        <div class="pw-route-track">
                            <div class="pw-route-marks" aria-hidden="true"><span class="pw-route-dot"></span><span class="pw-route-line"></span><span class="pw-route-dot pw-end"></span></div>
                            <div class="pw-route-content">
                                <div class="pw-route-item"><p class="pw-field-label">Seller</p><p class="pw-field-value">{{ $sellerName }}</p><p class="pw-field-value pw-normal">{{ $pickupAddress }}</p></div>
                                <div class="pw-route-item"><p class="pw-field-label">Next Destination</p><p class="pw-field-value">{{ $destinationLabel }}</p><p class="pw-field-value pw-normal">{{ $destinationDetail }}</p></div>
                            </div>
                        </div>
                    </div>
                    @if(! $isLogisticsPickup)
                        <div class="pw-detail-row">
                            <p class="pw-field-label">Delivery Fee</p>
                            <p class="pw-field-value">₱{{ number_format($order->courierDeliveryFee(), 2) }}</p>
                        </div>
                    @endif
                    <div class="pw-detail-row">
                        <p class="pw-field-label">Current Stage</p>
                        <p class="pw-field-value">{{ $order->statusLabel() }}</p>
                    </div>
                </div>
            </article>

            <article class="pw-panel" aria-labelledby="recordHeading">
                <header class="pw-panel-heading">
                    <div>
                        <h3 id="recordHeading" class="pw-section-title">Workflow Record</h3>
                        <p class="pw-section-subtitle">Job milestones recorded by the system</p>
                    </div>
                </header>
                <div class="pw-timeline">
                    <ol class="pw-events">
                        @foreach($workflowRecord as [$label, $time])
                            <li class="pw-event">
                                <span class="pw-event-dot {{ $time ? 'is-set' : '' }}" aria-hidden="true"></span>
                                <span class="pw-event-label">{{ $label }}</span>
                                <time class="pw-event-time">{{ $time ? $time->format('M d · h:i A') : 'Pending' }}</time>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </article>

            @if($isLogisticsPickup)
                <article class="pw-panel pw-rule" aria-labelledby="pickupRuleHeading">
                    <h3 id="pickupRuleHeading" class="pw-section-title">Logistics Pickup Rule</h3>
                    <p>This Rider performs the Seller pickup and origin-hub handoff only. Delivery-fee earnings belong to the final last-mile Rider after successful Buyer delivery.</p>
                </article>
            @endif
        </aside>
    </section>
</div>
@endsection


@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<script>
(() => {
    const stops = @json($mapStops);
    const targetKey = @json($nextStopKey);
    const el = document.getElementById('sariPickupLegMap');
    const note = document.getElementById('sariPickupMapNote');
    const fitButton = document.getElementById('sariPickupMapFit');
    const navButton = document.getElementById('sariPickupNavigate');
    const valid = stops.filter(p => Number.isFinite(Number(p.lat)) && Number.isFinite(Number(p.lng))
        && Math.abs(Number(p.lat)) <= 90 && Math.abs(Number(p.lng)) <= 180
        && !(Number(p.lat) === 0 && Number(p.lng) === 0));
    const destination = valid.find(p => p.key === targetKey);
    if (!destination && navButton) { navButton.disabled = true; navButton.title = 'No saved coordinates for this stage'; }
    navButton?.addEventListener('click', () => {
        if (!destination) return;
        const ll = `${Number(destination.lat)},${Number(destination.lng)}`;
        // External map provider calculates real roads; SARI does not guess the route/ETA.
        window.open(`https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(ll)}&travelmode=driving`, '_blank', 'noopener,noreferrer');
    });
    if (!el || typeof L === 'undefined') {
        if (note) note.textContent = 'OpenStreetMap could not load; check your network connection.';
        return;
    }
    const map = L.map(el, {scrollWheelZoom:false});
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom:19, attribution:'&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a> contributors'
    }).addTo(map);
    const points=[];
    valid.forEach(p => {
        const latlng=[Number(p.lat),Number(p.lng)];
        const pin=L.divIcon({className:'',html:`<span class="pw-map-pin ${p.key === targetKey?'is-destination':''}"></span>`,iconSize:[18,18],iconAnchor:[9,9]});
        const content=document.createElement('div');
        const title=document.createElement('strong'); title.textContent=p.key === targetKey?'Current destination':'Saved stop';
        const detail=document.createElement('div'); detail.textContent=String(p.name||'Saved location');
        content.append(title,detail);
        L.marker(latlng,{icon:pin}).bindPopup(content).addTo(map);
        points.push(latlng);
    });
    function fit(){ if(points.length>1) map.fitBounds(points,{padding:[37,37],maxZoom:15});
        else if(points.length) map.setView(points[0],15);
        else map.setView([12.8797,121.7740],6); }
    fit();
    fitButton?.addEventListener('click',fit);
    if(note) note.textContent = points.length
        ? `${points.length} saved location pin(s). Open road directions for actual navigation. No ETA is calculated by SARI.`
        : 'No saved coordinates on this assignment yet. Verify the Seller/hub address before departure.';
    setTimeout(()=>map.invalidateSize(),200);
})();
</script>
@endpush
