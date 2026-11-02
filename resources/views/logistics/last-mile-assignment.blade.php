@extends('layouts.logistics')

@section('title', 'Last Mile Assignment — SARI Logistics')
@section('page-title', 'Last Mile Assignment')

@push('styles')
<style>
/* SARI Logistics — Last Mile. Self-contained, scoped UI; no legacy CSS required. */
#lgLastMile, #lgLastMile * { box-sizing: border-box; }
#lgLastMile {
    --lm-gold:#d9950b; --lm-gold-hover:#c5880a; --lm-ink:#202b3b;
    --lm-muted:#78869b; --lm-border:#e4eaf0; --lm-surface:#fff;
    --lm-shadow:0 11px 26px rgba(29,43,60,.065),0 2px 7px rgba(29,43,60,.025);
    width:100%;max-width:1620px;margin:0 auto;color:var(--lm-ink);font-size:13px;
}
#lgLastMile [hidden] { display:none !important; }
#lgLastMile button,#lgLastMile select,#lgLastMile input {font:inherit;}
#lgLastMile button {cursor:pointer;}
#lgLastMile a {text-decoration:none;}
#lgLastMile :is(button,input,select,a):focus-visible {outline:2px solid #9baabb;outline-offset:3px;}
#lgLastMile :is(input,select):focus {border-color:#9baabb;box-shadow:0 0 0 3px rgba(133,151,173,.12);outline:none;}
#lgLastMile .lm2-page-header {display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin:0 0 22px;padding:4px 0 17px;border:0;border-bottom:1px solid #e2e7ec;border-radius:0;background:transparent;box-shadow:none;}
#lgLastMile .lm2-eyebrow {margin:0 0 6px;color:#aa7317;letter-spacing:.13em;font-size:10px;font-weight:750;text-transform:uppercase;}
#lgLastMile .lm2-page-title {margin:0;color:#1e2c40;font-size:clamp(25px,2.4vw,32px);font-weight:800;letter-spacing:-.045em;line-height:1.17;}
#lgLastMile .lm2-page-title span {color:var(--lm-gold);}
#lgLastMile .lm2-header-actions {display:flex;align-items:center;flex-wrap:wrap;gap:9px;}
#lgLastMile .lm2-btn {display:inline-flex;justify-content:center;align-items:center;gap:6px;min-height:40px;padding:10px 15px;border:1px solid #dce4ed;border-radius:11px;background:#fff;color:#526176;font-weight:700;font-size:12px;line-height:1.3;transition:background .16s,border-color .16s,box-shadow .16s;}
#lgLastMile .lm2-btn:hover {background:#f3f6f9;border-color:#cbd6e2;}
#lgLastMile .lm2-btn-gold {background:var(--lm-gold)!important;color:white!important;border-color:var(--lm-gold)!important;box-shadow:0 5px 13px rgba(217,149,11,.14);}
#lgLastMile .lm2-btn-gold:hover {background:var(--lm-gold-hover)!important;border-color:var(--lm-gold-hover)!important;}
#lgLastMile .lm2-btn:disabled {opacity:.5;cursor:not-allowed;box-shadow:none;}
#lgLastMile .lm2-alert {padding:14px 16px;margin:0 0 14px;border-radius:12px;border:1px solid #d9e5e0;background:#f3fbf6;color:#276448;}
#lgLastMile .lm2-alert-error {background:#fff4f3;border-color:#f3d8d5;color:#ab514c;}
#lgLastMile .lm2-alert ul {padding-left:18px;margin:6px 0 0;}
#lgLastMile .lm2-stats {display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr))!important;gap:14px!important;margin:0 0 22px!important;padding:0!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;}
#lgLastMile .lm2-stat {display:flex!important;justify-content:space-between;align-items:flex-start;min-width:0;gap:10px;padding:17px 18px;min-height:116px;border:1px solid var(--lm-border);border-radius:18px;background:#fff;box-shadow:var(--lm-shadow);}
#lgLastMile .lm2-stat-label {margin:0 0 6px;color:#52647b;font-size:12px;font-weight:650;line-height:1.45;}
#lgLastMile .lm2-stat-value {display:block;color:#19283b;font-size:28px;font-weight:800;line-height:1.08;letter-spacing:-.04em;}
#lgLastMile .lm2-stat-note {display:block;margin-top:6px;color:#8b99aa;font-size:11px;line-height:1.45;}
#lgLastMile .lm2-stat-icon {display:inline-flex;align-items:center;justify-content:center;flex:0 0 32px;width:32px;height:32px;padding:0;border:0;border-radius:0;background:transparent!important;box-shadow:none!important;}
#lgLastMile .lm2-stat-icon svg {display:block;width:31px;height:31px;stroke:currentColor;}
#lgLastMile .lm2-stat-icon.gold {color:#c58a19;} #lgLastMile .lm2-stat-icon.purple {color:#8875b5;} #lgLastMile .lm2-stat-icon.blue {color:#4b86b5;} #lgLastMile .lm2-stat-icon.green {color:#3e916e;}
#lgLastMile .lm2-tabs {display:flex;align-items:center;gap:26px;flex-wrap:wrap;margin:0 0 16px;padding:2px 0 13px;border-bottom:1px solid #dde5ed;background:transparent!important;box-shadow:none!important;}
#lgLastMile .lm2-tab {display:inline-flex;align-items:center;gap:7px;margin:0;padding:6px 0;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:#7b8a9e!important;font-size:14px;font-weight:700;line-height:1.4;white-space:nowrap;}
#lgLastMile .lm2-tab:hover {color:#37475c!important;}
#lgLastMile .lm2-tab[aria-selected="true"] {color:#c48612!important;}
#lgLastMile .lm2-tab-count {font-weight:800;color:inherit;}
#lgLastMile .lm2-pane {min-width:0;}
#lgLastMile .lm2-filterbar {display:grid!important;grid-template-columns:minmax(160px,1fr) minmax(170px,210px) auto auto;align-items:center;gap:12px!important;margin:0 0 16px;padding:0!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;}
#lgLastMile .lm2-search-field {display:flex;align-items:center;gap:10px;min-width:0;height:46px;padding:0 14px;border:1px solid #dbe4ed;border-radius:12px;background:#fff;color:#8492a6;}
#lgLastMile .lm2-search-field svg {width:17px;height:17px;flex-shrink:0;}
#lgLastMile .lm2-search-input {width:100%;min-width:0;height:100%;margin:0;padding:0;border:0!important;background:transparent!important;box-shadow:none!important;color:#263448;font-size:12px;outline:0!important;}
#lgLastMile .lm2-search-field:focus-within {border-color:#9caabd;box-shadow:0 0 0 3px rgba(133,151,173,.10);}
#lgLastMile .lm2-select {display:block;width:100%;height:46px;padding:0 13px;border:1px solid #dbe4ed;border-radius:12px;background:white;color:#394a60;font-size:12px;}
#lgLastMile .lm2-filterbar .lm2-btn {min-height:46px;white-space:nowrap;}
#lgLastMile .lm2-card {min-width:0;overflow:hidden;border:1px solid var(--lm-border);border-radius:19px;background:#fff;box-shadow:var(--lm-shadow);}
#lgLastMile .lm2-card-head {display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:17px 21px;border-bottom:1px solid #e9eef3;}
#lgLastMile .lm2-card-title {margin:0;color:#1f2d42;font-size:16px;font-weight:750;letter-spacing:-.02em;}
#lgLastMile .lm2-count {display:inline-flex;align-items:center;min-height:29px;padding:6px 10px;border:1px solid #e2e8f0;border-radius:9px;color:#6c7d93;background:#f8fafc;font-size:10px;font-weight:750;white-space:nowrap;}
#lgLastMile .lm2-table-wrap {overflow-x:auto;max-width:100%;}
#lgLastMile .lm2-table {width:100%;border-collapse:collapse;table-layout:fixed;text-align:left;}
#lgLastMile .lm2-table th {padding:13px 16px;border-bottom:1px solid #e2e8ee;background:#f7f9fb;color:#6e7d91;font-size:10px;font-weight:800;letter-spacing:.065em;text-transform:uppercase;}
#lgLastMile .lm2-table td {padding:16px;border-bottom:1px solid #edf0f3;vertical-align:middle;font-size:12px;line-height:1.5;overflow-wrap:anywhere;}
#lgLastMile .lm2-table tbody tr:last-child td {border-bottom:0;}
#lgLastMile .lm2-table tbody tr:hover {background:#f2f4f6!important;box-shadow:none!important;}
#lgLastMile .lm2-strong {font-size:12px;color:#24334a;font-weight:750;}
#lgLastMile .lm2-sub {font-size:11px;margin-top:3px;color:#8493a7;line-height:1.5;overflow-wrap:anywhere;}
#lgLastMile .lm2-status {display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border:1px solid #e0e7ed;border-radius:999px;background:#f7f9fb;color:#64768b;font-size:10px;font-weight:700;white-space:nowrap;}
#lgLastMile .lm2-status.success {border-color:#cbe8d7;background:#eef9f2;color:#287a53;}
#lgLastMile .lm2-status.warn {border-color:#f0dfbd;background:#fff9ee;color:#a2751d;}
#lgLastMile .lm2-status.info {border-color:#d4e4f5;background:#f0f7fe;color:#4079b4;}
#lgLastMile .lm2-status.danger {border-color:#efd9d5;background:#fdf3f2;color:#a95550;}
#lgLastMile .lm2-row-actions {display:flex;align-items:center;justify-content:flex-end;gap:12px;}
#lgLastMile .lm2-eye {display:inline-flex;align-items:center;justify-content:center;width:31px;height:34px;padding:0;border:0;background:transparent;color:#687b92;}
#lgLastMile .lm2-eye:hover {color:#25364b;} #lgLastMile .lm2-eye svg {width:19px;height:19px;}
#lgLastMile .lm2-btn-small {min-height:34px;padding:7px 12px;font-size:11px;border-radius:9px;white-space:nowrap;}
#lgLastMile .lm2-mobile-list {display:none;}
#lgLastMile .lm2-mobile-card {padding:16px;border-bottom:1px solid #edf0f3;}
#lgLastMile .lm2-mobile-card:last-child {border-bottom:0;}
#lgLastMile .lm2-mobile-top {display:flex;align-items:flex-start;justify-content:space-between;gap:10px;}
#lgLastMile .lm2-mobile-details {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px;margin:13px 0;}
#lgLastMile .lm2-mobile-actions {display:flex;justify-content:flex-end;align-items:center;gap:13px;}
#lgLastMile .lm2-mobile-label {font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#8b99ab;margin-bottom:3px;}
#lgLastMile .lm2-empty {display:flex;flex-direction:column;justify-content:center;align-items:center;gap:9px;min-height:260px;padding:40px 22px;text-align:center;background:white;}
#lgLastMile .lm2-empty-icon {width:58px;height:58px;color:#b6c0cb;stroke:currentColor;background:transparent!important;border:0!important;border-radius:0!important;}
#lgLastMile .lm2-empty-title {margin:3px 0 0;color:#9cabbc;font-size:21px;font-weight:750;line-height:1.35;}
#lgLastMile .lm2-empty-desc {max-width:480px;color:#9ba9ba;font-size:12px;line-height:1.65;}
#lgLastMile .lm2-empty .lm2-btn {margin-top:8px;}
#lgLastMile .lm2-table-footer {display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:11px;padding:13px 18px;border-top:1px solid #e9eef3;color:#8493a7;font-size:11px;}
#lgLastMile .lm2-rows-control {display:flex;align-items:center;gap:10px;}
#lgLastMile .lm2-rows-control select {min-width:63px;min-height:34px;padding:5px 10px;border:1px solid #dfe6ee;border-radius:10px;background:#fff;color:#45556a;}
#lgLastMile .lm2-pager {display:flex;align-items:center;justify-content:center;gap:7px;margin:16px 0 22px;}
#lgLastMile .lm2-page-btn {display:inline-flex;justify-content:center;align-items:center;width:35px;height:35px;border:0;border-radius:10px;background:transparent;color:#7d90a6;font-size:12px;font-weight:750;}
#lgLastMile .lm2-page-btn:hover:not(:disabled) {background:#eef2f6;}
#lgLastMile .lm2-page-btn.is-active {background:#d9950b;color:white;box-shadow:0 5px 12px rgba(217,149,11,.16);}
#lgLastMile .lm2-page-btn:disabled {opacity:.35;cursor:default;}
#lgLastMile .lm2-modal {position:fixed;inset:0;z-index:1200;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(18,28,41,.52);backdrop-filter:blur(3px);}
#lgLastMile .lm2-dialog {width:min(600px,100%);max-height:min(760px,calc(100dvh - 35px));overflow:auto;border:1px solid #e4eaf0;border-radius:21px;background:#fff;box-shadow:0 25px 80px rgba(16,27,41,.26);overscroll-behavior:contain;}
#lgLastMile .lm2-dialog-head {display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:22px 24px 17px;border-bottom:1px solid #e9eef3;}
#lgLastMile .lm2-dialog-title {font-size:22px;letter-spacing:-.03em;margin:0;color:#203047;font-weight:800;line-height:1.2;}
#lgLastMile .lm2-dialog-subtitle {margin:6px 0 0;color:#8291a4;font-size:12px;overflow-wrap:anywhere;}
#lgLastMile .lm2-close {display:inline-grid;place-items:center;flex:0 0 35px;width:35px;height:35px;padding:0;border:1px solid #e2e8ee;border-radius:10px;background:white;color:#627489;font-size:21px;}
#lgLastMile .lm2-close:hover {background:#f3f5f8;}
#lgLastMile .lm2-dialog-body {display:grid;gap:12px;padding:20px 24px 24px;}
#lgLastMile .lm2-info {padding:13px 15px;border:1px solid #e8ecf2;border-radius:12px;background:#f8fafc;}
#lgLastMile .lm2-info-label {display:block;margin-bottom:5px;color:#8997aa;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.055em;}
#lgLastMile .lm2-info-value {color:#29394e;font-size:13px;font-weight:650;overflow-wrap:anywhere;line-height:1.55;}
#lgLastMile .lm2-info-desc {margin-top:5px;color:#8796a7;font-size:11px;line-height:1.6;overflow-wrap:anywhere;}
#lgLastMile .lm2-info-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
#lgLastMile .lm2-dialog-section {margin:10px 0 3px;color:#34455a;font-size:13px;font-weight:800;}
#lgLastMile .lm2-parcel-list {display:grid;gap:8px;max-height:220px;overflow:auto;}
#lgLastMile .lm2-parcel {padding:12px 14px;border:1px solid #e8edf2;border-radius:10px;background:#fff;}
#lgLastMile .lm2-form-label {display:block;margin:5px 0 7px;color:#53647c;font-size:12px;font-weight:700;}
#lgLastMile .lm2-form-select {display:block;width:100%;min-height:46px;padding:10px 12px;border:1px solid #dce4ed;border-radius:11px;background:#fff;color:#30415a;font-size:13px;}
#lgLastMile .lm2-dialog-actions {display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:8px;padding-top:16px;border-top:1px solid #e8edf2;}
#lgLastMile .lm2-modal-warning {padding:12px;border:1px solid #f0dfce;border-radius:11px;color:#9b672c;background:#fff9f1;font-size:12px;line-height:1.6;}
#lgLastMile .lm2-rider-preview {display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #deebe2;border-radius:12px;background:#f4faf6;}
#lgLastMile .lm2-rider-preview-icon {display:grid;place-items:center;width:35px;height:35px;flex:0 0 35px;border-radius:10px;background:#e3f1e9;color:#36805b;font-weight:800;}
#lgLastMile .lm2-rider-preview strong {font-size:12px;color:#28513b;} #lgLastMile .lm2-rider-preview p {font-size:11px;color:#668976;margin:3px 0 0;}
body.lm2-modal-open {overflow:hidden;}
@media(max-width:1060px) {#lgLastMile .lm2-stats {grid-template-columns:repeat(2,minmax(0,1fr))!important;}}
@media(max-width:850px) {#lgLastMile .lm2-filterbar {grid-template-columns:minmax(0,1fr) minmax(145px,190px)!important;} #lgLastMile .lm2-filterbar .lm2-btn{width:100%;} #lgLastMile .lm2-table-wrap{display:none;} #lgLastMile .lm2-mobile-list{display:block;}}
@media(max-width:560px) {#lgLastMile .lm2-page-header{gap:15px;} #lgLastMile .lm2-page-title{font-size:25px;} #lgLastMile .lm2-header-actions{width:100%;} #lgLastMile .lm2-header-actions .lm2-btn{flex:1;} #lgLastMile .lm2-stats{gap:10px!important;} #lgLastMile .lm2-stat{padding:13px;min-height:111px;border-radius:14px;} #lgLastMile .lm2-stat-label{font-size:11px;} #lgLastMile .lm2-stat-value{font-size:24px;} #lgLastMile .lm2-stat-icon svg{width:27px;height:27px;} #lgLastMile .lm2-filterbar{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:9px!important;} #lgLastMile .lm2-search-field{grid-column:1/-1;} #lgLastMile .lm2-filterbar>.lm2-select{grid-column:1/-1;} #lgLastMile .lm2-tab{font-size:12px;} #lgLastMile .lm2-tabs{gap:17px;} #lgLastMile .lm2-card-head{padding:15px 16px;} #lgLastMile .lm2-empty-title{font-size:20px;} #lgLastMile .lm2-table-footer{padding:12px 15px;} #lgLastMile .lm2-dialog{border-radius:17px;} #lgLastMile .lm2-dialog-head{padding:17px;} #lgLastMile .lm2-dialog-body{padding:16px;} #lgLastMile .lm2-info-grid{grid-template-columns:1fr;} #lgLastMile .lm2-dialog-actions .lm2-btn{flex:1;} }
@media(prefers-reduced-motion:reduce){#lgLastMile *{scroll-behavior:auto!important;transition:none!important;animation:none!important;}}
</style>
@endpush

@section('content')
@php
    // SARI-MULTIPARCEL-DISPATCH-UI-V2: strict service-area matching + unstarted same-area batch top-up.
    $readyParcelCount = $deliveryGroups->sum(fn ($group) => $group->count());
    $deliveryAreaCount = $deliveryGroups->count();
    $availableRiderCount = $riders->reject(fn ($rider) => $busyRiderIds->contains((int) $rider->id))->count();
    $activeAssignmentCount = $assignments->whereIn('status', ['assigned', 'out_for_delivery'])->count();
    $assignmentCount = $assignments->count();
    $areas = collect();
    foreach ($deliveryGroups as $groupKey => $group) {
        $first = $group->first();
        if (!$first) { continue; }
        $sameAreaBatchKey = (int) $first->destination_facility_id.'|'.\App\Support\LogisticsServiceArea::barangay($first->barangay_name);
        $eligibleRiders = $riders->filter(function ($rider) use ($first, $busyRiderIds, $mergeableRiderAreaKeys, $sameAreaBatchKey) {
            return \App\Support\LogisticsServiceArea::matchesRider($rider, $first)
                && (! $busyRiderIds->contains((int) $rider->id)
                    || $mergeableRiderAreaKeys->get((int) $rider->id) === $sameAreaBatchKey);
        })->values();
        $allAreaRiders = $riders->filter(fn ($rider) => \App\Support\LogisticsServiceArea::matchesRider($rider, $first));
        $busyAreaRiderCount = $allAreaRiders->filter(fn ($rider) => $busyRiderIds->contains((int) $rider->id))->count();
        $searchValues = [$first->barangay_name, $first->destination_facility_name, $first->destination_municipality];
        foreach ($group as $parcel) {
            array_push($searchValues, $parcel->order_number, $parcel->buyer_name, $parcel->buyer_address, $parcel->pickup_rider_name);
        }
        $areas->push([
            'key' => md5((string) $groupKey), 'first' => $first, 'parcels' => $group,
            'riders' => $eligibleRiders, 'busy_rider_count' => $busyAreaRiderCount,
            'state' => $eligibleRiders->isNotEmpty() ? 'serviceable' : 'unserved',
            'search' => implode(' ', array_filter($searchValues)),
        ]);
    }
@endphp
<div id="lgLastMile" class="sari-logistics-ui" data-lastmile-workspace data-default-tab="{{ $areas->isEmpty() && $assignmentCount > 0 ? 'history' : 'ready' }}">
    <header class="lm2-page-header">
        <div>
            <p class="lm2-eyebrow">Destination Hub Dispatch</p>
            <h1 class="lm2-page-title">Last Mile <span>Assignment</span></h1>
        </div>
        <div class="lm2-header-actions">
            <a class="lm2-btn" href="{{ route('logistics.hub-transfers') }}"><span aria-hidden="true">&lt;</span> Hub Transfers</a>
            <a class="lm2-btn lm2-btn-gold" href="{{ route('logistics.delivery-monitoring') }}">Delivery Monitoring <span aria-hidden="true">&gt;</span></a>
        </div>
    </header>

    @if(session('success'))
        <div class="lm2-alert" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="lm2-alert lm2-alert-error" role="alert"><strong>Could not assign Last-Mile Rider</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="lm2-stats" aria-label="Last-mile overview">
        @foreach([
            ['Ready Parcels', $readyParcelCount, 'At destination hubs', 'gold'],
            ['Delivery Areas', $deliveryAreaCount, 'Barangay dispatch groups', 'purple'],
            ['Available Riders', $availableRiderCount, 'Online without active batch', 'blue'],
            ['Active Batches', $activeAssignmentCount, 'Assigned or on delivery', 'green'],
        ] as [$label, $value, $note, $tone])
            <div class="lm2-stat">
                <div><p class="lm2-stat-label">{{ $label }}</p><strong class="lm2-stat-value">{{ number_format($value) }}</strong><span class="lm2-stat-note">{{ $note }}</span></div>
                <span class="lm2-stat-icon {{ $tone }}" aria-hidden="true">
                    @switch($tone)
                        @case('gold')<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h7M7 17h5"/></svg>@break
                        @case('blue')<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="7" r="3"/><circle cx="17" cy="8" r="2.5"/><path d="M2 21v-2a6 6 0 0 1 12 0v2M15 15a5 5 0 0 1 7 5"/></svg>@break
                        @case('green')<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>@break
                        @default<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="8" height="7" rx="1"/><rect x="13" y="4" width="8" height="7" rx="1"/><rect x="8" y="13" width="8" height="7" rx="1"/></svg>
                    @endswitch
                </span>
            </div>
        @endforeach
    </div>

    <div class="lm2-tabs" role="tablist" aria-label="Last mile workspaces">
        <button type="button" class="lm2-tab" id="lmTabReady" data-lm-tab="ready" role="tab" aria-controls="lmPaneReady" aria-selected="true">Dispatch Queue <span class="lm2-tab-count">{{ $areas->count() }}</span></button>
        <button type="button" class="lm2-tab" id="lmTabHistory" data-lm-tab="history" role="tab" aria-controls="lmPaneHistory" aria-selected="false">Last-Mile Assignments <span class="lm2-tab-count">{{ $assignmentCount }}</span></button>
    </div>

    <div class="lm2-pane" id="lmPaneReady" data-lm-pane="ready" role="tabpanel" aria-labelledby="lmTabReady">
        @if($areas->isNotEmpty())
        <div class="lm2-filterbar" data-lm-controls="ready">
            <label class="lm2-search-field">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                <input class="lm2-search-input" type="search" data-lm-search="ready" placeholder="Search barangay, hub, order, Buyer..." autocomplete="off" aria-label="Search dispatch queue">
            </label>
            <select class="lm2-select" data-lm-select="ready" aria-label="Filter Rider availability"><option value="all">All Areas</option><option value="serviceable">Rider Available</option><option value="unserved">Needs Rider</option></select>
            <button class="lm2-btn lm2-btn-gold" type="button" data-lm-apply="ready">Apply Filter</button>
            <button class="lm2-btn" type="button" data-lm-reset="ready">Reset</button>
        </div>
        @endif
        <div class="lm2-card">
            <div class="lm2-card-head"><h2 class="lm2-card-title">Barangay Dispatch Queue</h2><span class="lm2-count">{{ $areas->count() }} {{ $areas->count() === 1 ? 'AREA' : 'AREAS' }}</span></div>
            @if($areas->isNotEmpty())
            <div class="lm2-table-wrap">
                <table class="lm2-table"><thead><tr><th style="width:29%">Delivery Area</th><th style="width:28%">Destination Hub</th><th style="width:12%">Parcels</th><th style="width:17%">Rider Availability</th><th style="width:14%;text-align:right">Action</th></tr></thead>
                <tbody>
                    @foreach($areas as $area)
                        <tr data-lm-item="ready" data-lm-key="{{ $area['key'] }}" data-lm-state="{{ $area['state'] }}" data-lm-search-text="{{ $area['search'] }}">
                            <td><div class="lm2-strong">Barangay {{ $area['first']->barangay_name }}</div><div class="lm2-sub">{{ $area['first']->destination_municipality }}</div></td>
                            <td><div class="lm2-strong">{{ $area['first']->destination_facility_name }}</div><div class="lm2-sub">Destination hub</div></td>
                            <td><span class="lm2-strong">{{ $area['parcels']->count() }}</span><div class="lm2-sub">parcel(s)</div></td>
                            <td>@if($area['riders']->isNotEmpty())<span class="lm2-status success">{{ $area['riders']->count() }} available</span>@else<span class="lm2-status danger">Needs Rider</span>@endif</td>
                            <td><div class="lm2-row-actions"><button type="button" class="lm2-eye" data-lm-open="lmDetail{{ $area['key'] }}" aria-label="View delivery area details" title="View details"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button><button type="button" class="lm2-btn lm2-btn-small {{ $area['riders']->isNotEmpty() ? 'lm2-btn-gold' : '' }}" data-lm-open="lmAssign{{ $area['key'] }}" @disabled($area['riders']->isEmpty())>Assign</button></div></td>
                        </tr>
                    @endforeach
                </tbody></table>
            </div>
            <div class="lm2-mobile-list">
                @foreach($areas as $area)
                    <div class="lm2-mobile-card" data-lm-mirror="ready" data-lm-key="{{ $area['key'] }}">
                        <div class="lm2-mobile-top"><div><div class="lm2-strong">Barangay {{ $area['first']->barangay_name }}</div><div class="lm2-sub">{{ $area['first']->destination_municipality }}</div></div>@if($area['riders']->isNotEmpty())<span class="lm2-status success">Rider Available</span>@else<span class="lm2-status danger">Needs Rider</span>@endif</div>
                        <div class="lm2-mobile-details"><div><div class="lm2-mobile-label">Hub</div><div class="lm2-strong">{{ $area['first']->destination_facility_name }}</div></div><div><div class="lm2-mobile-label">Parcels</div><div class="lm2-strong">{{ $area['parcels']->count() }}</div></div></div>
                        <div class="lm2-mobile-actions"><button type="button" class="lm2-eye" data-lm-open="lmDetail{{ $area['key'] }}" aria-label="View delivery area details"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button><button type="button" class="lm2-btn lm2-btn-small {{ $area['riders']->isNotEmpty() ? 'lm2-btn-gold' : '' }}" data-lm-open="lmAssign{{ $area['key'] }}" @disabled($area['riders']->isEmpty())>Assign Batch</button></div>
                    </div>
                @endforeach
            </div>
            <div class="lm2-empty" data-lm-no-match="ready" hidden>
                <svg class="lm2-empty-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                <h3 class="lm2-empty-title">No matching delivery areas</h3><p class="lm2-empty-desc">Try another barangay, order, hub or Rider availability filter.</p>
            </div>
            <div class="lm2-table-footer"><span data-lm-count="ready">Showing 1–{{ min(10, $areas->count()) }} of {{ $areas->count() }} areas</span><label class="lm2-rows-control">Rows per page <select data-lm-size="ready" aria-label="Dispatch queue rows per page"><option value="10">10</option><option value="20">20</option><option value="50">50</option></select></label></div>
            @else
            <div class="lm2-empty">
                <svg class="lm2-empty-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h16v12H4zM4 8l3-4h10l3 4M9 13h6M12 10v6"/></svg>
                <h3 class="lm2-empty-title">Last-Mile Dispatch Queue Is Clear</h3><p class="lm2-empty-desc">Parcels appear here after a hub transfer reaches the destination facility.</p>
                <a href="{{ route('logistics.hub-transfers') }}" class="lm2-btn lm2-btn-gold">View Hub Transfers</a>
            </div>
            @endif
        </div>
        <div class="lm2-pager" data-lm-pager="ready" aria-label="Dispatch queue pagination" hidden></div>
    </div>

    <div class="lm2-pane" id="lmPaneHistory" data-lm-pane="history" role="tabpanel" aria-labelledby="lmTabHistory" hidden>
        @if($assignmentCount > 0)
        <div class="lm2-filterbar" data-lm-controls="history">
            <label class="lm2-search-field"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg><input class="lm2-search-input" type="search" data-lm-search="history" placeholder="Search assignment, Rider, area..." autocomplete="off" aria-label="Search last mile assignments"></label>
            <select class="lm2-select" data-lm-select="history" aria-label="Filter assignment status"><option value="all">All Status</option><option value="assigned">Assigned</option><option value="out_for_delivery">Out for Delivery</option><option value="completed">Completed</option></select>
            <button type="button" class="lm2-btn lm2-btn-gold" data-lm-apply="history">Apply Filter</button>
            <button type="button" class="lm2-btn" data-lm-reset="history">Reset</button>
        </div>
        @endif
        <div class="lm2-card">
            <div class="lm2-card-head"><h2 class="lm2-card-title">Last-Mile Assignments</h2><span class="lm2-count">{{ $assignmentCount }} {{ $assignmentCount === 1 ? 'BATCH' : 'BATCHES' }}</span></div>
            @if($assignmentCount > 0)
            <div class="lm2-table-wrap"><table class="lm2-table"><thead><tr><th style="width:23%">Assignment</th><th style="width:27%">Delivery Area</th><th style="width:23%">Rider</th><th style="width:10%">Parcels</th><th style="width:12%">Status</th><th style="width:5%;text-align:right">View</th></tr></thead><tbody>
            @foreach($assignments as $assignment)
                @php
                    $assignmentSearch = implode(' ', array_filter([$assignment->assignment_code, $assignment->facility_name, $assignment->delivery_area, $assignment->first_name, $assignment->last_name, $assignment->vehicle_type, $assignment->status]));
                    $assignmentStatusLabel = ucwords(str_replace('_', ' ', $assignment->status));
                    $statusTone = $assignment->status === 'completed' ? 'success' : ($assignment->status === 'out_for_delivery' ? 'info' : 'warn');
                @endphp
                <tr data-lm-item="history" data-lm-key="{{ $assignment->id }}" data-lm-state="{{ $assignment->status }}" data-lm-search-text="{{ $assignmentSearch }}">
                    <td><div class="lm2-strong">{{ $assignment->assignment_code }}</div><div class="lm2-sub">Last-mile batch</div></td>
                    <td><div class="lm2-strong">Barangay {{ $assignment->delivery_area }}</div><div class="lm2-sub">{{ $assignment->facility_name }}</div></td>
                    <td><div class="lm2-strong">{{ trim($assignment->first_name.' '.$assignment->last_name) }}</div><div class="lm2-sub">{{ $assignment->vehicle_type ?: 'Vehicle not recorded' }}</div></td>
                    <td><span class="lm2-strong">{{ $assignment->order_count }}</span></td>
                    <td><span class="lm2-status {{ $statusTone }}">{{ $assignmentStatusLabel }}</span></td>
                    <td><div class="lm2-row-actions"><button type="button" class="lm2-eye" data-lm-open="lmHistory{{ $assignment->id }}" aria-label="View assignment details" title="View details"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div></td>
                </tr>
            @endforeach
            </tbody></table></div>
            <div class="lm2-mobile-list">
            @foreach($assignments as $assignment)
                @php $statusTone = $assignment->status === 'completed' ? 'success' : ($assignment->status === 'out_for_delivery' ? 'info' : 'warn'); @endphp
                <div class="lm2-mobile-card" data-lm-mirror="history" data-lm-key="{{ $assignment->id }}"><div class="lm2-mobile-top"><div><div class="lm2-strong">{{ $assignment->assignment_code }}</div><div class="lm2-sub">{{ $assignment->facility_name }}</div></div><span class="lm2-status {{ $statusTone }}">{{ ucwords(str_replace('_', ' ', $assignment->status)) }}</span></div><div class="lm2-mobile-details"><div><div class="lm2-mobile-label">Delivery Area</div><div class="lm2-strong">{{ $assignment->delivery_area }}</div></div><div><div class="lm2-mobile-label">Rider</div><div class="lm2-strong">{{ trim($assignment->first_name.' '.$assignment->last_name) }}</div></div><div><div class="lm2-mobile-label">Parcels</div><div class="lm2-strong">{{ $assignment->order_count }}</div></div><div><div class="lm2-mobile-label">Vehicle</div><div class="lm2-strong">{{ $assignment->vehicle_type ?: '—' }}</div></div></div><div class="lm2-mobile-actions"><button type="button" class="lm2-eye" data-lm-open="lmHistory{{ $assignment->id }}" aria-label="View assignment details"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div>
            @endforeach
            </div>
            <div class="lm2-empty" data-lm-no-match="history" hidden><svg class="lm2-empty-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg><h3 class="lm2-empty-title">No matching assignments</h3><p class="lm2-empty-desc">Try another assignment code, Rider, hub or status.</p></div>
            <div class="lm2-table-footer"><span data-lm-count="history">Showing 1–{{ min(10, $assignmentCount) }} of {{ $assignmentCount }} assignments</span><label class="lm2-rows-control">Rows per page <select data-lm-size="history" aria-label="Assignment rows per page"><option value="10">10</option><option value="20">20</option><option value="50">50</option></select></label></div>
            @else
            <div class="lm2-empty"><svg class="lm2-empty-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 10h8M8 14h5"/></svg><h3 class="lm2-empty-title">No Last-Mile Assignments Yet</h3><p class="lm2-empty-desc">Assigned Rider batches will appear here when local delivery begins.</p></div>
            @endif
        </div>
        <div class="lm2-pager" data-lm-pager="history" aria-label="Last mile assignment pagination" hidden></div>
    </div>

    {{-- Delivery-area details and assignment forms, rendered separately from compact rows. --}}
    @foreach($areas as $area)
        <div id="lmDetail{{ $area['key'] }}" class="lm2-modal" role="dialog" aria-modal="true" aria-label="Delivery area details" hidden>
            <div class="lm2-dialog" role="document"><div class="lm2-dialog-head"><div><p class="lm2-eyebrow">Delivery Area Details</p><h3 class="lm2-dialog-title">Barangay {{ $area['first']->barangay_name }}</h3><p class="lm2-dialog-subtitle">{{ $area['first']->destination_municipality }}</p></div><button type="button" class="lm2-close" data-lm-close aria-label="Close details">×</button></div>
                <div class="lm2-dialog-body"><div class="lm2-info"><span class="lm2-info-label">Destination Hub</span><div class="lm2-info-value">{{ $area['first']->destination_facility_name }}</div><div class="lm2-info-desc">{{ $area['first']->destination_municipality }}</div></div><div class="lm2-info-grid"><div class="lm2-info"><span class="lm2-info-label">Parcels</span><div class="lm2-info-value">{{ $area['parcels']->count() }}</div></div><div class="lm2-info"><span class="lm2-info-label">Available Local Riders</span><div class="lm2-info-value">{{ $area['riders']->count() }}</div>@if($area['busy_rider_count'] > 0)<div class="lm2-info-desc">{{ $area['busy_rider_count'] }} busy Rider(s)</div>@endif</div></div>
                    <h4 class="lm2-dialog-section">Parcels for Delivery</h4><div class="lm2-parcel-list">@foreach($area['parcels'] as $parcel)<div class="lm2-parcel"><div class="lm2-strong">{{ $parcel->order_number }}</div><div class="lm2-sub">{{ $parcel->buyer_name }} · {{ $parcel->buyer_address }}</div><div class="lm2-sub">Pickup Rider: {{ $parcel->pickup_rider_name ?: 'Not recorded' }}{{ $parcel->pickup_rider_email ? ' · '.$parcel->pickup_rider_email : '' }}</div></div>@endforeach</div>
                    <div class="lm2-dialog-actions"><button type="button" class="lm2-btn" data-lm-close>Close</button>@if($area['riders']->isNotEmpty())<button type="button" class="lm2-btn lm2-btn-gold" data-lm-switch="lmAssign{{ $area['key'] }}">Assign Rider</button>@endif</div>
                </div>
            </div>
        </div>
        <div id="lmAssign{{ $area['key'] }}" class="lm2-modal" role="dialog" aria-modal="true" aria-label="Assign local Rider" hidden>
            <div class="lm2-dialog" role="document"><div class="lm2-dialog-head"><div><p class="lm2-eyebrow">Confirm Last-Mile Dispatch</p><h3 class="lm2-dialog-title">Assign Local Rider</h3><p class="lm2-dialog-subtitle">Barangay {{ $area['first']->barangay_name }} · {{ $area['parcels']->count() }} parcel(s)</p></div><button type="button" class="lm2-close" data-lm-close aria-label="Close assignment">×</button></div>
                <form method="POST" action="{{ route('logistics.last-mile-assignment.batch-assign') }}" data-lastmile-form data-area-name="{{ $area['first']->barangay_name }}">
                    @csrf
                    @foreach($area['parcels'] as $parcel)<input type="hidden" name="order_ids[]" value="{{ $parcel->order_id }}">@endforeach
                    <div class="lm2-dialog-body"><div class="lm2-info"><span class="lm2-info-label">Destination Hub</span><div class="lm2-info-value">{{ $area['first']->destination_facility_name }}</div><div class="lm2-info-desc">{{ $area['first']->destination_municipality }}</div></div>
                        <div class="lm2-info-grid"><div class="lm2-info"><span class="lm2-info-label">Local Delivery Area</span><div class="lm2-info-value">{{ $area['first']->barangay_name }}</div></div><div class="lm2-info"><span class="lm2-info-label">Delivery Batch</span><div class="lm2-info-value">{{ $area['parcels']->count() }} parcel(s)</div></div></div>
                        <div><label class="lm2-form-label" for="rider{{ $area['key'] }}">Local Rider *</label><select id="rider{{ $area['key'] }}" class="lm2-form-select" name="rider_id" required data-lastmile-rider @disabled($area['riders']->isEmpty())><option value="">Select local Rider / add to unstarted batch</option>@foreach($area['riders'] as $rider)@php $riderName = trim($rider->first_name.' '.$rider->last_name); @endphp<option value="{{ $rider->id }}" data-rider-name="{{ $riderName }}" data-rider-area="{{ $rider->barangay_name }}, {{ $rider->municipality_name }}" data-rider-vehicle="{{ $rider->vehicle_type }}">{{ $riderName }}{{ $rider->vehicle_type ? ' — '.$rider->vehicle_type : '' }}</option>@endforeach</select></div>
                        <div class="lm2-rider-preview" data-rider-preview hidden><span class="lm2-rider-preview-icon" data-rider-initial>R</span><div><strong data-rider-preview-name></strong><p data-rider-preview-area></p><p data-rider-preview-vehicle></p></div></div>
                        @if($area['riders']->isEmpty())<div class="lm2-modal-warning">No eligible online Rider currently serves {{ $area['first']->barangay_name }}. @if($area['busy_rider_count'] > 0){{ $area['busy_rider_count'] }} local Rider(s) currently handling another or already-started batch.@endif</div>@else<div class="lm2-modal-warning">Confirm the parcel count. A Rider with an unstarted batch for this same hub and barangay will receive these parcels in that existing batch. Otherwise a new batch is created.</div>@endif
                        <div class="lm2-dialog-actions"><button type="button" class="lm2-btn" data-lm-close>Cancel</button><button type="submit" class="lm2-btn lm2-btn-gold" @disabled($area['riders']->isEmpty())>Confirm Assignment</button></div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- History details modal: assignment operations remain with their existing backend. --}}
    @foreach($assignments as $assignment)
        @php $statusTone = $assignment->status === 'completed' ? 'success' : ($assignment->status === 'out_for_delivery' ? 'info' : 'warn'); @endphp
        <div id="lmHistory{{ $assignment->id }}" class="lm2-modal" role="dialog" aria-modal="true" aria-label="Last mile assignment details" hidden>
            <div class="lm2-dialog" role="document"><div class="lm2-dialog-head"><div><p class="lm2-eyebrow">Assignment Details</p><h3 class="lm2-dialog-title">{{ $assignment->assignment_code }}</h3><p class="lm2-dialog-subtitle">{{ $assignment->facility_name }}</p></div><button type="button" class="lm2-close" data-lm-close aria-label="Close details">×</button></div>
                <div class="lm2-dialog-body"><div class="lm2-info"><span class="lm2-info-label">Status</span><span class="lm2-status {{ $statusTone }}">{{ ucwords(str_replace('_', ' ', $assignment->status)) }}</span></div><div class="lm2-info-grid"><div class="lm2-info"><span class="lm2-info-label">Delivery Area</span><div class="lm2-info-value">Barangay {{ $assignment->delivery_area }}</div></div><div class="lm2-info"><span class="lm2-info-label">Batch Load</span><div class="lm2-info-value">{{ $assignment->order_count }} parcel(s)</div></div></div><div class="lm2-info"><span class="lm2-info-label">Assigned Local Rider</span><div class="lm2-info-value">{{ trim($assignment->first_name.' '.$assignment->last_name) }}</div><div class="lm2-info-desc">{{ $assignment->vehicle_type ?: 'Vehicle not recorded' }}</div></div><div class="lm2-dialog-actions"><button type="button" class="lm2-btn" data-lm-close>Close</button><a class="lm2-btn lm2-btn-gold" href="{{ route('logistics.delivery-monitoring') }}">Delivery Monitoring &gt;</a></div></div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
(() => {
    'use strict';
    const init = () => {
        const root = document.querySelector('#lgLastMile[data-lastmile-workspace]');
        if (!root || root.dataset.initialized === 'true') return;
        root.dataset.initialized = 'true';
        const $ = (selector, parent = root) => parent.querySelector(selector);
        const $$ = (selector, parent = root) => Array.from(parent.querySelectorAll(selector));
        const normalize = v => String(v ?? '').toLocaleLowerCase().trim();
        const tabs = $$('[data-lm-tab]');
        function setTab(tabName, focus = false) {
            tabs.forEach(tab => { const selected = tab.dataset.lmTab === tabName; tab.setAttribute('aria-selected', String(selected)); tab.tabIndex = selected ? 0 : -1; if (focus && selected) tab.focus(); });
            $$('[data-lm-pane]').forEach(pane => { pane.hidden = pane.dataset.lmPane !== tabName; });
        }
        tabs.forEach(tab => tab.addEventListener('click', () => setTab(tab.dataset.lmTab)));
        setTab(root.dataset.defaultTab || 'ready');
        const pageState = {};
        for (const kind of ['ready','history']) {
            const rows = $$(`[data-lm-item="${kind}"]`);
            const mirrors = $$(`[data-lm-mirror="${kind}"]`);
            const search = $(`[data-lm-search="${kind}"]`);
            const select = $(`[data-lm-select="${kind}"]`);
            const size = $(`[data-lm-size="${kind}"]`);
            const count = $(`[data-lm-count="${kind}"]`);
            const pager = $(`[data-lm-pager="${kind}"]`);
            const noMatch = $(`[data-lm-no-match="${kind}"]`);
            const state = pageState[kind] = { page:1, keyword:'', filter:'all' };
            const perPage = () => Number(size?.value || 10);
            const render = () => {
                const matched = rows.filter(row => (state.filter === 'all' || row.dataset.lmState === state.filter) && normalize(row.dataset.lmSearchText).includes(state.keyword));
                const totalPages = Math.max(1, Math.ceil(matched.length/perPage()));
                state.page = Math.max(1, Math.min(totalPages,state.page));
                const start = (state.page-1)*perPage();
                const visible = new Set(matched.slice(start,start+perPage()).map(row => row.dataset.lmKey));
                rows.forEach(row => { row.hidden = !visible.has(row.dataset.lmKey); });
                mirrors.forEach(card => { card.hidden = !visible.has(card.dataset.lmKey); });
                if (noMatch) noMatch.hidden = matched.length !== 0;
                if (count) count.textContent = matched.length ? `Showing ${start+1}–${Math.min(start+perPage(),matched.length)} of ${matched.length} ${kind === 'ready' ? 'areas' : 'assignments'}` : `Showing 0 of 0 ${kind === 'ready' ? 'areas' : 'assignments'}`;
                if (pager) {
                    pager.replaceChildren(); pager.hidden = totalPages <= 1 || matched.length === 0;
                    if (!pager.hidden) {
                        const add = (label, page, disabled, active = false) => {
                            const btn = document.createElement('button'); btn.type='button'; btn.className='lm2-page-btn'+(active?' is-active':''); btn.textContent=label; btn.disabled=disabled; btn.setAttribute('aria-label',label === '<'?'Previous page':label === '>'?'Next page':`Page ${label}`); if (active) btn.setAttribute('aria-current','page'); btn.addEventListener('click',() => {state.page=page;render();}); pager.append(btn);
                        };
                        add('<',state.page-1,state.page===1);
                        const from = Math.max(1,Math.min(state.page-2,totalPages-4));
                        for(let p=from;p<=Math.min(totalPages,from+4);p++) add(String(p),p,false,p===state.page);
                        add('>',state.page+1,state.page===totalPages);
                    }
                }
            };
            $(`[data-lm-apply="${kind}"]`)?.addEventListener('click',() => {state.keyword=normalize(search?.value);state.filter=select?.value||'all';state.page=1;render();});
            $(`[data-lm-reset="${kind}"]`)?.addEventListener('click',() => {if(search) search.value='';if(select)select.value='all';state.keyword='';state.filter='all';state.page=1;render();});
            search?.addEventListener('keydown',event => {if(event.key==='Enter'){event.preventDefault();$(`[data-lm-apply="${kind}"]`)?.click();}});
            size?.addEventListener('change',()=>{state.page=1;render();});
            render();
        }
        let activeModal = null;
        let priorFocus = null;
        const closeModal = (restore = true) => {
            if (!activeModal) return;
            activeModal.hidden=true; activeModal=null;document.body.classList.remove('lm2-modal-open');
            if(restore && priorFocus?.isConnected) priorFocus.focus({preventScroll:true});
        };
        const openModal = (id) => {
            const modal = document.getElementById(id);
            if(!modal || !root.contains(modal))return;
            if(activeModal) closeModal(false);
            priorFocus=document.activeElement; activeModal=modal; modal.hidden=false;
            document.body.classList.add('lm2-modal-open');
            modal.querySelector('[data-lm-close]')?.focus({preventScroll:true});
        };
        root.addEventListener('click',event => {
            const opener = event.target.closest('[data-lm-open]');
            if(opener && root.contains(opener)) {openModal(opener.dataset.lmOpen);return;}
            const swap = event.target.closest('[data-lm-switch]');
            if(swap && root.contains(swap)){openModal(swap.dataset.lmSwitch);return;}
            if(activeModal && (event.target === activeModal || event.target.closest('[data-lm-close]')))closeModal();
        });
        document.addEventListener('keydown',event => {
            if(!activeModal)return;
            if(event.key==='Escape'){event.preventDefault();closeModal();return;}
            if(event.key==='Tab'){
                const focusables = $$('button:not(:disabled),select:not(:disabled),a[href],input:not([type="hidden"]):not(:disabled)',activeModal).filter(el=>!el.hidden);
                if(!focusables.length)return;
                const first=focusables[0],last=focusables[focusables.length-1];
                if(event.shiftKey && document.activeElement===first){event.preventDefault();last.focus();}
                else if(!event.shiftKey && document.activeElement===last){event.preventDefault();first.focus();}
            }
        });
        $$('[data-lastmile-form]').forEach(form => {
            const select = $('[data-lastmile-rider]',form);
            const preview = $('[data-rider-preview]',form);
            const render = () => {
                const opt=select?.selectedOptions?.[0];
                if(!opt?.value){if(preview)preview.hidden=true;return;}
                $('[data-rider-preview-name]',preview).textContent=opt.dataset.riderName||opt.textContent.trim();
                $('[data-rider-preview-area]',preview).textContent=opt.dataset.riderArea||'Local delivery area';
                $('[data-rider-preview-vehicle]',preview).textContent=opt.dataset.riderVehicle||'Vehicle not recorded';
                $('[data-rider-initial]',preview).textContent=(opt.dataset.riderName||'R').charAt(0).toUpperCase();
                preview.hidden=false;
            };
            select?.addEventListener('change',render);render();
            form.addEventListener('submit',event=>{
                if(!select?.value){event.preventDefault();select?.focus();return;}
                const btn=$('button[type="submit"]',form);
                if(btn){btn.disabled=true;btn.textContent='Assigning Rider…';}
            });
        });
    };
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
</script>
@endpush
