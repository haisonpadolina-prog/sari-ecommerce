@extends('layouts.admin')
@php
    $status = ($kind === 'seller' && $account->suspended_until && now()->lt($account->suspended_until) && strtolower((string)($account->account_status ?? 'active')) === 'active')
        ? 'suspended'
        : strtolower((string) ($account->account_status ?? 'active'));
    $statusText = match ($status) {
        'deactivated' => 'Suspended',
        'suspended' => 'Timed suspension',
        'blocked' => 'Blocked',
        'banned' => 'Banned',
        'pending_activation' => 'Pending activation',
        default => ucfirst(str_replace('_', ' ', $status ?: 'active')),
    };
    $displayName = match ($kind) {
        'seller' => $account->store_name ?: trim(($account->first_name ?? '').' '.($account->last_name ?? '')),
        'logistics' => $account->displayName(),
        'rider' => trim(($account->first_name ?? '').' '.($account->last_name ?? '')),
        default => $isSocialBuyer ?? false
            ? ($account->name ?: 'SARI Buyer')
            : (trim(($account->first_name ?? '').' '.($account->last_name ?? '')) ?: 'SARI Buyer'),
    };
    $backSection = match ($kind) {
        'seller' => 'sellers',
        'logistics' => 'logistics',
        'rider' => 'riders',
        default => 'buyers',
    };
    $location = collect([
        $account->street_address ?? null,
        $account->barangay_name ?? null,
        $account->municipality_name ?? null,
        $account->province_name ?? null,
    ])->filter()->implode(', ');
@endphp
@section('title', $displayName.' — Platform Monitoring')
@section('page-title', 'Platform Monitoring')
@if($kind === 'logistics')
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
    @endpush
@endif
@section('content')
<div class="pmd-page mx-auto w-full max-w-[1500px] pb-8">
    <style>
        .pmd-page{--d-gold:#D59617;--d-gold-strong:#B97805;--d-line:#DFE3E8;--d-muted:#697481;--d-ink:#202124;font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;color:var(--d-ink)}
        .pmd-top{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;padding-bottom:15px;border-bottom:1px solid var(--d-line)}
        .pmd-eyebrow{margin:0;color:#717B87;font-size:8px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}
        .pmd-title{margin:5px 0 0;font-size:clamp(28px,2.1vw,36px);font-weight:700;line-height:1.06;letter-spacing:-.04em}
        .pmd-copy{max-width:780px;margin:7px 0 0;color:var(--d-muted);font-size:9.8px;line-height:1.6}
        .pmd-back{display:inline-flex;height:38px;align-items:center;border:1px solid #D6DCE2;border-radius:8px;background:#fff;padding:0 12px;color:#56606C;font-size:8.5px;font-weight:750;text-decoration:none;white-space:nowrap}
        .pmd-identity{display:flex;align-items:center;gap:12px;margin-top:14px;border:1px solid var(--d-line);border-radius:8px;background:#fff;padding:13px 14px;box-shadow:0 1px 3px rgba(15,23,42,.025),0 8px 20px rgba(15,23,42,.05)}
        .pmd-avatar{display:grid;width:42px;height:42px;flex:0 0 42px;place-items:center;border:1px solid #E2E6EA;border-radius:50%;background:#F7F8FA;color:#58636F;font-size:9px;font-weight:800}
        .pmd-id-name{font-size:12px;font-weight:750;color:#2A3139}
        .pmd-id-sub{margin-top:2px;color:#77818C;font-size:8px}
        .pmd-status{display:inline-flex;min-height:24px;align-items:center;margin-left:auto;border:1px solid #DCE7DF;border-radius:999px;background:#F5F9F6;padding:0 9px;color:#567362;font-size:7px;font-weight:750}
        .pmd-status.deactivated,.pmd-status.suspended{border-color:#E8DDC9;background:#FFF9EF;color:#8B6B32}.pmd-status.blocked{border-color:#EAD7CB;background:#FFF6F1;color:#9A5E42}.pmd-status.banned{border-color:#E7C9C9;background:#FFF3F3;color:#A24F4F}
        .pmd-metrics{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px;margin-top:14px}
        .pmd-metric{min-height:70px;border:1px solid var(--d-line);border-radius:8px;background:#fff;padding:12px 13px}
        .pmd-metric span{display:block;color:#74808D;font-size:7.2px;font-weight:750;text-transform:uppercase;letter-spacing:.07em}.pmd-metric strong{display:block;margin-top:7px;color:#2D333B;font-size:18px;font-weight:700;line-height:1}
        .pmd-grid{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(310px,.72fr);gap:14px;margin-top:14px;align-items:start}
        .pmd-stack{display:grid;gap:14px}
        .pmd-panel{overflow:hidden;border:1px solid var(--d-line);border-radius:8px;background:#fff;box-shadow:0 3px 7px rgba(15,23,42,.04),0 14px 32px rgba(15,23,42,.06)}
        .pmd-panel-head{display:flex;min-height:56px;align-items:center;justify-content:space-between;gap:10px;padding:11px 14px;border-bottom:1px solid #ECEFF2}.pmd-panel-head h2{margin:0;color:#303844;font-size:11px;font-weight:750}.pmd-panel-head p{margin:3px 0 0;color:#74808D;font-size:8px;line-height:1.5}
        .pmd-body{padding:14px}
        .pmd-kv{display:grid;grid-template-columns:150px minmax(0,1fr);gap:8px 14px}.pmd-kv dt{color:#7D8792;font-size:7.8px}.pmd-kv dd{margin:0;color:#38424D;font-size:8.5px;font-weight:600;word-break:break-word}
        .pmd-table-wrap{overflow:auto}.pmd-table{width:100%;min-width:760px;border-collapse:collapse}.pmd-table th{padding:9px 11px;border-bottom:1px solid #ECEFF2;background:#FAFBFC;color:#717B87;font-size:7px;font-weight:800;letter-spacing:.05em;text-align:left;text-transform:uppercase;white-space:nowrap}.pmd-table td{padding:10px 11px;border-bottom:1px solid #F0F2F4;color:#46505D;font-size:8px;line-height:1.5;vertical-align:middle}.pmd-table tr:last-child td{border-bottom:0}.pmd-name{color:#29323B;font-size:8.6px;font-weight:750}.pmd-sub{margin-top:2px;color:#7D8792;font-size:7px}
        .pmd-product{display:flex;align-items:center;gap:9px}.pmd-product img{width:38px;height:38px;border-radius:7px;object-fit:cover;border:1px solid #E5E7EB;background:#F7F8FA}.pmd-img-fallback{display:grid;width:38px;height:38px;place-items:center;border:1px solid #E5E7EB;border-radius:7px;background:#F7F8FA;color:#8A949F;font-size:7px}
        .pmd-map{display:inline-flex;height:29px;align-items:center;border:1px solid #D6DCE2;border-radius:7px;background:#fff;padding:0 9px;color:#56606C;font-size:7px;font-weight:750;text-decoration:none}.pmd-map:hover{border-color:#D9BE83;background:#FFFBF2;color:#8F6418}
        .pmd-admin{border-color:#E9DDBF;background:#FFFCF5}.pmd-admin .pmd-panel-head{border-bottom-color:#F0E7D4}.pmd-reason{width:100%;min-height:76px;resize:vertical;border:1px solid #D6DCE2;border-radius:8px;background:#fff;padding:9px 10px;color:#303844;font:500 8.5px/1.5 'Poppins',sans-serif;outline:none}.pmd-reason:focus{border-color:var(--d-gold);box-shadow:0 0 0 3px rgba(213,150,23,.08)}
        .pmd-actions{display:flex;flex-wrap:wrap;gap:7px;margin-top:9px}.pmd-action{display:inline-flex;min-height:36px;align-items:center;justify-content:center;border-radius:8px;padding:0 11px;font-size:7.7px;font-weight:750;cursor:pointer}.pmd-action.suspend{border:1px solid #E4CFA1;background:#FFF8E9;color:#8F671E}.pmd-action.block{border:1px solid #E5CABB;background:#FFF5F0;color:#985A3D}.pmd-action.ban{border:1px solid #E1BDBD;background:#FFF1F1;color:#A34E4E}.pmd-action.restore{border:1px solid #CDE0D3;background:#F2F9F4;color:#4F795D}.pmd-warning{margin-top:8px;color:#8C6B35;font-size:7.2px;line-height:1.55}
        .pmd-activity{display:grid;gap:9px}.pmd-event{padding-left:10px;border-left:2px solid #E6D7B7}.pmd-event strong{display:block;color:#3C4650;font-size:8px}.pmd-event span{display:block;margin-top:2px;color:#7D8792;font-size:7px;line-height:1.5}
        .pmd-empty{padding:22px 14px;color:#7D8792;font-size:8px;text-align:center}.pmd-pagination{padding:10px 14px;border-top:1px solid #ECEFF2;background:#FAFBFC}
        .pmd-pill{display:inline-flex;min-height:21px;align-items:center;border:1px solid #E1E5EA;border-radius:999px;background:#F8F9FA;padding:0 7px;color:#65707B;font-size:6.5px;font-weight:700;white-space:nowrap}
        .pmd-ops-map{height:390px;border:1px solid #E1E5EA;border-radius:8px;background:#F5F7F8;overflow:hidden}.pmd-map-layout{display:grid;grid-template-columns:minmax(0,1fr) 245px;gap:12px}.pmd-map-side{display:grid;align-content:start;gap:8px}.pmd-map-stat{border:1px solid #E4E8EC;border-radius:8px;background:#FAFBFC;padding:9px 10px}.pmd-map-stat strong{display:block;color:#35404A;font-size:8px}.pmd-map-stat span{display:block;margin-top:3px;color:#7B8691;font-size:6.8px;line-height:1.5}.pmd-map-legend{display:flex;flex-wrap:wrap;gap:8px;margin-top:9px}.pmd-map-key{display:inline-flex;align-items:center;gap:5px;color:#707B86;font-size:6.7px}.pmd-map-dot{width:8px;height:8px;border-radius:999px}.pmd-map-dot.hub{background:#D59617}.pmd-map-dot.route{background:#4F7EA8}.pmd-map-dot.done{background:#5B9270}.pmd-map-note{margin-top:8px;color:#7F8993;font-size:6.7px;line-height:1.55}.pmd-ops-map .leaflet-popup-content{font-family:'Poppins',sans-serif;font-size:9px;line-height:1.5}.pmd-ops-map .leaflet-control-attribution{font-size:7px}
        @media(max-width:1050px){.pmd-grid{grid-template-columns:1fr}.pmd-metrics{grid-template-columns:repeat(3,minmax(0,1fr))}.pmd-map-layout{grid-template-columns:1fr}}
        @media(max-width:639px){.pmd-top{align-items:stretch;flex-direction:column}.pmd-back{width:100%;justify-content:center}.pmd-identity{align-items:flex-start;flex-wrap:wrap}.pmd-status{margin-left:0}.pmd-metrics{grid-template-columns:1fr 1fr}.pmd-kv{grid-template-columns:1fr}.pmd-kv dd{margin-bottom:5px}}
    
/* Shared monitoring visual language. */
.pmd-page { --d-line:#E4E7EC; --d-muted:#667085; --d-ink:#101828; }
.pmd-page .pmd-top {align-items:center;}
.pmd-page .pmd-identity,.pmd-page .pmd-panel {border-radius:12px;background:#FFFFFF;border-color:var(--d-line);box-shadow:0 2px 6px rgba(16,24,40,.04);}
.pmd-page .pmd-grid,.pmd-page .pmd-stack {min-width:0;}
.pmd-page .pmd-metrics {gap:12px;}
.pmd-page .pmd-metric {position:relative;min-width:0;min-height:86px;padding:15px 16px;border:1px solid var(--d-line);border-radius:12px;background:#fff;box-shadow:0 2px 6px rgba(16,24,40,.04);}
.pmd-page .pmd-metric > span:first-child {padding-right:34px;color:#667085;font-size:10px;font-weight:600;line-height:1.35;text-transform:none;letter-spacing:normal;}
.pmd-page .pmd-metric strong {margin-top:5px;color:#101828;font-size:24px;line-height:1.15;letter-spacing:-.035em;overflow-wrap:anywhere;}
.pmd-page .pmd-metric > .pmd-metric-icon {position:absolute;top:14px;right:16px;display:inline-flex;width:25px;height:25px;padding:0;border:0;border-radius:0;background:transparent;box-shadow:none;pointer-events:none;}
.pmd-page .pmd-metric-icon svg {display:block;width:25px;height:25px;}
.pmd-page .pmd-panel-head {padding:14px 16px;background:#fff;border-bottom-color:#ECEFF2;}
.pmd-page .pmd-panel-head h2 {font-size:12px;font-weight:600;color:#252A32;}
.pmd-page .pmd-panel-head p {font-size:9px;line-height:1.6;color:#667085;}
.pmd-page .pmd-body {padding:16px;}
.pmd-page .pmd-kv {grid-template-columns:140px minmax(0,1fr);gap:10px 14px;}
.pmd-page .pmd-kv dt {font-size:10px;color:#667085;line-height:1.5;}
.pmd-page .pmd-kv dd {font-size:10px;font-weight:500;line-height:1.5;color:#344054;}
.pmd-page .pmd-table th {font-size:9px;font-weight:600;padding:11px 14px;background:#F8FAFC;}
.pmd-page .pmd-table td {font-size:10px;padding:12px 14px;}
.pmd-page .pmd-table tbody tr:hover td {background:#F3F4F6;}
.pmd-page .pmd-name {font-size:10px;font-weight:600;}
.pmd-page .pmd-sub {font-size:9px;}
.pmd-page .pmd-status,.pmd-page .pmd-pill {font-size:9px;min-height:25px;padding-inline:9px;}
.pmd-page .pmd-back {height:36px;border-radius:8px;font-size:10px;font-weight:600;}
.pmd-page .pmd-back:hover {background:#F3F4F6;border-color:#CBD2DB;color:#252A32;}
.pmd-page .pmd-map {min-height:34px;height:auto;justify-content:center;padding:8px 12px;border:1px solid var(--d-gold);border-radius:8px;background:var(--d-gold);color:#fff;font-size:10px;font-weight:600;line-height:1.4;}
.pmd-page .pmd-map:hover {background:var(--d-gold-strong);border-color:var(--d-gold-strong);color:#fff;}
.pmd-page .pmd-admin {background:#fff;border-color:var(--d-line);}
.pmd-page .pmd-admin .pmd-panel-head {border-bottom-color:#ECEFF2;}
.pmd-page .pmd-reason {font-size:11px;line-height:1.6;min-height:90px;box-sizing:border-box;}
.pmd-page .pmd-actions {gap:8px;}
.pmd-page .pmd-action {min-height:36px;padding:8px 12px;font-size:10px;font-weight:600;line-height:1.4;}
.pmd-page .pmd-action.suspend,.pmd-page .pmd-action.block,.pmd-page .pmd-action.ban {background:#DC2626;border:1px solid #DC2626;color:#fff;}
.pmd-page .pmd-action.suspend:hover,.pmd-page .pmd-action.block:hover,.pmd-page .pmd-action.ban:hover {background:#B91C1C;border-color:#B91C1C;}
.pmd-page .pmd-action.restore {background:var(--d-gold);border:1px solid var(--d-gold);color:#fff;}
.pmd-page .pmd-action.restore:hover {background:var(--d-gold-strong);border-color:var(--d-gold-strong);}
.pmd-page .pmd-warning {color:#667085;font-size:9px;line-height:1.6;}
.pmd-page .pmd-activity {gap:0;}
.pmd-page .pmd-event {padding:12px 0;border-left:0;border-bottom:1px solid #ECEFF2;}
.pmd-page .pmd-event:first-child {padding-top:0;}
.pmd-page .pmd-event:last-child {border-bottom:0;padding-bottom:0;}
.pmd-page .pmd-event strong {font-size:10px;font-weight:600;}
.pmd-page .pmd-event span {font-size:9px;line-height:1.6;}
.pmd-page :is(.pmd-back,.pmd-map,.pmd-action):focus-visible {outline:2px solid #475467;outline-offset:3px;}
@media(max-width:639px) {.pmd-page .pmd-kv {grid-template-columns:1fr;gap:4px;} .pmd-page .pmd-kv dd {margin-bottom:8px;} .pmd-page .pmd-panel-head {flex-wrap:wrap;} }

.pmd-page .pmd-event[hidden] { display:none !important; }
.pmd-page .pmd-activity-toggle {display:flex;align-items:center;justify-content:center;width:100%;min-height:34px;margin-top:14px;padding:8px 12px;border:1px solid #E4E7EC;border-radius:8px;background:#fff;color:#344054;font:600 10px/1.4 'Poppins',sans-serif;cursor:pointer;}
.pmd-page .pmd-activity-toggle:hover {background:#F3F4F6;border-color:#CBD2DB;}
.pmd-page .pmd-activity-toggle:focus-visible {outline:2px solid #475467;outline-offset:3px;}

/* Only the store/account identity loses its panel treatment. */
.pmd-page .pmd-identity {
    padding: 12px 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}
.pmd-page [data-collapsible-records] tr[hidden] {display:none !important;}
.pmd-page .pmd-records-toggle {width:calc(100% - 32px);margin:12px 16px 16px;}
</style>
    <div class="pmd-top">
        <div>
            <p class="pmd-eyebrow">{{ ucfirst($kind) }} Monitoring</p>
            <h1 class="pmd-title">{{ $displayName }}</h1>
            <p class="pmd-copy">
                @if($kind === 'seller') Store profile, product inventory, order activity, and account controls.
                @elseif($kind === 'logistics') Provider profile, sorting hubs, linked Riders, and assigned delivery flow.
                @elseif($kind === 'rider') Registered service area, assigned deliveries, current product load, and delivery history.
                @else Buyer profile, successful and cancelled orders, purchase activity, and account controls.
                @endif
            </p>
        </div>
        <a class="pmd-back" href="{{ route('admin.monitoring', ['section' => $backSection]) }}">← Back to directory</a>
    </div>
    @if(session('success'))
        <div class="mt-3 rounded-[8px] border border-[#d7e7dc] bg-[#f5faf6] px-3 py-2 text-[9px] font-semibold text-[#51715d]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mt-3 rounded-[8px] border border-[#ead2cc] bg-[#fff6f3] px-3 py-2 text-[9px] text-[#9a5a49]">{{ $errors->first() }}</div>
    @endif
    <div class="pmd-identity">
        <div class="pmd-avatar">{{ collect(preg_split('/\s+/', trim($displayName)))->filter()->map(fn($part) => strtoupper(substr($part,0,1)))->take(2)->implode('') ?: 'SA' }}</div>
        <div class="min-w-0">
            <div class="pmd-id-name">{{ $displayName }}</div>
            <div class="pmd-id-sub">{{ $account->email ?? 'No email' }} @if($location) · {{ $location }} @endif</div>
        </div>
        <span class="pmd-status {{ $status }}">{{ $statusText }}</span>
    </div>
    @if($kind === 'seller')
        <div class="pmd-metrics">
            <div class="pmd-metric"><span>Total products</span><strong>{{ number_format($stats['products']) }}</strong><span class="pmd-metric-icon" style="color:#AC7A16" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v10l9 4 9-4V8M12 13v9"/></svg></span></div>
            <div class="pmd-metric"><span>Live products</span><strong>{{ number_format($stats['live_products']) }}</strong><span class="pmd-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span></div>
            <div class="pmd-metric"><span>Total orders</span><strong>{{ number_format($stats['orders']) }}</strong><span class="pmd-metric-icon" style="color:#AC7A16" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v10l9 4 9-4V8M12 13v9"/></svg></span></div>
            <div class="pmd-metric"><span>Delivered</span><strong>{{ number_format($stats['delivered']) }}</strong><span class="pmd-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span></div>
            <div class="pmd-metric"><span>Cancelled</span><strong>{{ number_format($stats['cancelled']) }}</strong><span class="pmd-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg></span></div>
        </div>
    @elseif($kind === 'logistics')
        <div class="pmd-metrics">
            <div class="pmd-metric"><span>Hubs</span><strong>{{ number_format($stats['facilities']) }}</strong><span class="pmd-metric-icon" style="color:#7C3AED" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v10l9 4 9-4V8M12 13v9"/></svg></span></div>
            <div class="pmd-metric"><span>Active hubs</span><strong>{{ number_format($stats['active_facilities']) }}</strong><span class="pmd-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span></div>
            <div class="pmd-metric"><span>Riders</span><strong>{{ number_format($stats['riders']) }}</strong><span class="pmd-metric-icon" style="color:#0F766E" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3"/><path d="M5 21a7 7 0 0 1 14 0"/></svg></span></div>
            <div class="pmd-metric"><span>Active deliveries</span><strong>{{ number_format($stats['active_deliveries']) }}</strong><span class="pmd-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span></div>
            <div class="pmd-metric"><span>Delivered</span><strong>{{ number_format($stats['delivered']) }}</strong><span class="pmd-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span></div>
        </div>
    @elseif($kind === 'rider')
        <div class="pmd-metrics">
            <div class="pmd-metric"><span>Assigned jobs</span><strong>{{ number_format($stats['assigned']) }}</strong><span class="pmd-metric-icon" style="color:#B7791F" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span></div>
            <div class="pmd-metric"><span>Active jobs</span><strong>{{ number_format($stats['active']) }}</strong><span class="pmd-metric-icon" style="color:#B7791F" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span></div>
            <div class="pmd-metric"><span>Products in load</span><strong>{{ number_format($currentLoad->sum('quantity')) }}</strong><span class="pmd-metric-icon" style="color:#AC7A16" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v10l9 4 9-4V8M12 13v9"/></svg></span></div>
            <div class="pmd-metric"><span>Delivered</span><strong>{{ number_format($stats['delivered']) }}</strong><span class="pmd-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span></div>
            <div class="pmd-metric"><span>Cancelled</span><strong>{{ number_format($stats['cancelled']) }}</strong><span class="pmd-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg></span></div>
        </div>
    @else
        <div class="pmd-metrics">
            <div class="pmd-metric"><span>Total orders</span><strong>{{ number_format($stats['orders']) }}</strong><span class="pmd-metric-icon" style="color:#AC7A16" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v10l9 4 9-4V8M12 13v9"/></svg></span></div>
            <div class="pmd-metric"><span>Successful</span><strong>{{ number_format($stats['delivered']) }}</strong><span class="pmd-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span></div>
            <div class="pmd-metric"><span>Cancelled</span><strong>{{ number_format($stats['cancelled']) }}</strong><span class="pmd-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg></span></div>
            <div class="pmd-metric"><span>In progress</span><strong>{{ number_format($stats['in_progress']) }}</strong><span class="pmd-metric-icon" style="color:#B7791F" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span></div>
            <div class="pmd-metric"><span>Successful spend</span><strong>₱{{ number_format($stats['successful_spend'], 2) }}</strong><span class="pmd-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span></div>
        </div>
    @endif
    <div class="pmd-grid">
        <div class="pmd-stack">
            @if($kind === 'seller')
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Store profile</h2><p>Public store and registered seller information.</p></div></div>
                    <div class="pmd-body">
                        <dl class="pmd-kv">
                            <dt>Store name</dt><dd>{{ $account->store_name ?: '—' }}</dd>
                            <dt>Owner</dt><dd>{{ trim(($account->first_name ?? '').' '.($account->last_name ?? '')) ?: '—' }}</dd>
                            <dt>Seller type</dt><dd>{{ $account->seller_type ?: '—' }}</dd>
                            <dt>Line of business</dt><dd>{{ $account->line_of_business ?: '—' }}</dd>
                            <dt>Store public email</dt><dd>{{ $account->store_public_email ?: $account->email }}</dd>
                            <dt>Store phone</dt><dd>{{ $account->store_phone ?: $account->contact_no ?: '—' }}</dd>
                            <dt>Pickup address</dt><dd>{{ $account->pickup_address ?: $location ?: '—' }}</dd>
                            <dt>Description</dt><dd>{{ $account->store_description ?: 'No store description.' }}</dd>
                        </dl>
                    </div>
                </section>
                <section class="pmd-panel" data-collapsible-records="products">
                    <div class="pmd-panel-head"><div><h2>Products</h2><p>Current Seller product catalog and moderation state.</p></div><span class="pmd-pill">{{ number_format($products->total()) }} items</span></div>
                    <div class="pmd-table-wrap">
                        @if($products->count())
                            <table class="pmd-table"><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Discount</th><th>Moderation</th></tr></thead><tbody>
                            @foreach($products as $product)
                                @php $imageUrl = filled($product->image_path) ? \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) : null; @endphp
                                <tr>
                                    <td><div class="pmd-product">@if($imageUrl)<img src="{{ $imageUrl }}" alt="">@else<div class="pmd-img-fallback">IMG</div>@endif<div><div class="pmd-name">{{ $product->name }}</div><div class="pmd-sub">{{ $product->sku ?: 'No SKU' }}</div></div></div></td>
                                    <td>{{ $product->category ?: '—' }}</td><td>₱{{ number_format((float) $product->price,2) }}</td><td>{{ number_format((int) $product->stock) }}</td><td>{{ number_format((float) $product->discount,1) }}%</td><td><span class="pmd-pill">{{ ucfirst(str_replace('_',' ',(string) $product->moderation_status)) }}</span></td>
                                </tr>
                            @endforeach
                            </tbody></table>
                        @else<div class="pmd-empty">This store has no products.</div>@endif
                    </div>
                    @if($products->hasPages())<div class="pmd-pagination">{{ $products->links() }}</div>@endif
                </section>
                <section class="pmd-panel" data-collapsible-records="store-orders">
                    <div class="pmd-panel-head"><div><h2>Recent store orders</h2><p>Latest orders attached to this Seller account.</p></div></div>
                    @include('admin.monitoring.partials.orders-table', ['ordersList' => $orders])
                </section>
            @elseif($kind === 'logistics')
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Provider profile</h2><p>Business registration, representative, and service coverage.</p></div></div>
                    <div class="pmd-body"><dl class="pmd-kv">
                        <dt>Business name</dt><dd>{{ $account->business_name ?: $account->displayName() }}</dd>
                        <dt>Representative</dt><dd>{{ trim(($account->first_name ?? '').' '.($account->last_name ?? '')) ?: '—' }}{{ $account->position ? ' · '.$account->position : '' }}</dd>
                        <dt>Email</dt><dd>{{ $account->email }}</dd><dt>Contact</dt><dd>{{ $account->contact_no ?: '—' }}</dd>
                        <dt>Registered address</dt><dd>{{ $location ?: '—' }}</dd><dt>Service areas</dt><dd>{{ $account->service_areas ?: 'No service areas recorded.' }}</dd>
                        <dt>TIN</dt><dd>{{ $account->tin ?: '—' }}</dd><dt>SEC registration</dt><dd>{{ $account->sec_registration_no ?: '—' }}</dd>
                    </dl></div>
                </section>
                @php
                    $opsMapPoints = collect();
                    foreach($facilities as $facility) {
                        if($facility->latitude !== null && $facility->longitude !== null) {
                            $opsMapPoints->push([
                                'type' => 'hub', 'name' => $facility->name, 'address' => $facility->address,
                                'lat' => (float)$facility->latitude, 'lng' => (float)$facility->longitude,
                                'status' => $facility->is_active ? 'Active hub' : 'Inactive hub', 'order' => null,
                            ]);
                        }
                    }
                    foreach($orders as $deliveryOrder) {
                        foreach(($deliveryOrder->deliveryRouteStops ?? collect()) as $stop) {
                            if($stop->latitude !== null && $stop->longitude !== null) {
                                $opsMapPoints->push([
                                    'type' => $stop->reached_at ? 'done' : 'route',
                                    'name' => $stop->name ?: ucfirst(str_replace('_',' ',(string)$stop->type)),
                                    'address' => $stop->address_snapshot,
                                    'lat' => (float)$stop->latitude, 'lng' => (float)$stop->longitude,
                                    'status' => $deliveryOrder->statusLabel(), 'order' => $deliveryOrder->order_number,
                                ]);
                            }
                        }
                    }
                    $assignedRiders = $riders->filter(fn($rider) => (int)$rider->active_deliveries_count > 0);
                @endphp
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Logistics operations map</h2><p>Registered hubs plus route-stop coordinates for current delivery jobs. Rider locations are not represented as live GPS.</p></div><span class="pmd-pill">{{ number_format($orders->count()) }} active jobs</span></div>
                    <div class="pmd-body">
                        <div class="pmd-map-layout">
                            <div>
                                <div id="adminLogisticsOperationsMap" class="pmd-ops-map" data-map-points='@json($opsMapPoints->values())'></div>
                                <div class="pmd-map-legend"><span class="pmd-map-key"><i class="pmd-map-dot hub"></i>Hub / facility</span><span class="pmd-map-key"><i class="pmd-map-dot route"></i>Pending/current route stop</span><span class="pmd-map-key"><i class="pmd-map-dot done"></i>Reached route stop</span></div>
                                <div class="pmd-map-note">This map uses coordinates already stored by SARI. It does not claim real-time Rider GPS tracking.</div>
                            </div>
                            <div class="pmd-map-side">
                                <div class="pmd-map-stat"><strong>{{ number_format($facilities->where('is_active',true)->count()) }} active hubs</strong><span>{{ $account->service_areas ?: 'No service areas recorded.' }}</span></div>
                                <div class="pmd-map-stat"><strong>{{ number_format($assignedRiders->count()) }} Riders on active jobs</strong><span>@if($assignedRiders->count()){{ $assignedRiders->take(5)->map(fn($r) => trim($r->first_name.' '.$r->last_name).' ('.$r->active_deliveries_count.')')->implode(' · ') }}@elseNo Rider currently has an active delivery.@endif</span></div>
                                <div class="pmd-map-stat"><strong>{{ number_format($orders->count()) }} current delivery assignments</strong><span>Destination/status pins appear only where the order route contains stored latitude/longitude.</span></div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Hub &amp; sorting facility locations</h2><p>Coordinates come from the Logistics facility records used by SARI delivery routing.</p></div><span class="pmd-pill">{{ number_format($facilities->count()) }} hubs</span></div>
                    <div class="pmd-table-wrap">
                        @if($facilities->count())
                            <table class="pmd-table"><thead><tr><th>Hub</th><th>Address</th><th>Coordinates</th><th>Routes</th><th>Status</th><th></th></tr></thead><tbody>
                            @foreach($facilities as $facility)
                                <tr><td><div class="pmd-name">{{ $facility->name }}</div><div class="pmd-sub">{{ $facility->code }}</div></td><td>{{ $facility->address }}</td><td>{{ number_format((float)$facility->latitude,6) }}, {{ number_format((float)$facility->longitude,6) }}</td><td>{{ number_format((int)$facility->route_stops_count) }}</td><td><span class="pmd-pill">{{ $facility->is_active ? 'Active' : 'Inactive' }}</span></td><td><a class="pmd-map" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($facility->latitude.','.$facility->longitude) }}">Open map</a></td></tr>
                            @endforeach
                            </tbody></table>
                        @else<div class="pmd-empty">No registered hub or sorting facility yet.</div>@endif
                    </div>
                </section>
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Provider personnel</h2><p>The current system stores the provider representative and registered Rider accounts; no separate general-staff account type exists.</p></div></div>
                    <div class="pmd-table-wrap">
                        @if($riders->count())
                            <table class="pmd-table"><thead><tr><th>Rider</th><th>Area</th><th>Vehicle</th><th>Active deliveries</th><th>Delivered</th><th>Status</th><th></th></tr></thead><tbody>
                            @foreach($riders as $rider)
                                <tr><td><div class="pmd-name">{{ trim($rider->first_name.' '.$rider->last_name) }}</div><div class="pmd-sub">{{ $rider->email }}</div></td><td>{{ collect([$rider->barangay_name,$rider->municipality_name,$rider->province_name])->filter()->implode(', ') ?: '—' }}</td><td>{{ $rider->vehicle_type ?: '—' }} · {{ $rider->plate_number ?: '—' }}</td><td>{{ number_format((int)$rider->active_deliveries_count) }}</td><td>{{ number_format((int)$rider->delivered_orders_count) }}</td><td><span class="pmd-pill">{{ ucfirst(str_replace('_',' ',(string)$rider->account_status)) }}</span></td><td><a class="pmd-map" href="{{ route('admin.monitoring.riders.show',$rider) }}">View rider</a></td></tr>
                            @endforeach
                            </tbody></table>
                        @else<div class="pmd-empty">No Rider accounts are linked to this Logistics provider.</div>@endif
                    </div>
                </section>
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Current delivery flow</h2><p>Active Marketplace orders assigned to this Logistics provider.</p></div></div>
                    @include('admin.monitoring.partials.orders-table', ['ordersList' => $orders])
                </section>
            @elseif($kind === 'buyer')
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Buyer profile</h2><p>Account identity and registered delivery area.</p></div></div>
                    <div class="pmd-body"><dl class="pmd-kv">
                        <dt>Account type</dt><dd>{{ ($isSocialBuyer ?? false) ? 'Social Buyer' : 'Registered Buyer' }}</dd>
                        <dt>Name</dt><dd>{{ $displayName }}</dd><dt>Email</dt><dd>{{ $account->email ?: '—' }}</dd>
                        @if(!($isSocialBuyer ?? false))<dt>Contact</dt><dd>{{ $account->contact_no ?: '—' }}</dd><dt>Registered address</dt><dd>{{ $location ?: '—' }}</dd>@endif
                        @if($isSocialBuyer ?? false)<dt>Provider</dt><dd>{{ ucfirst((string)($account->provider ?: 'social')) }}</dd><dt>Last social login</dt><dd>{{ $account->last_login_at?->format('M d, Y · h:i A') ?? '—' }}</dd>@endif
                    </dl></div>
                </section>
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Buyer order history</h2><p>Successful, cancelled, and in-progress orders from the Marketplace order ledger.</p></div><span class="pmd-pill">{{ number_format($orders->total()) }} orders</span></div>
                    @include('admin.monitoring.partials.orders-table', ['ordersList' => $orders])
                    @if($orders->hasPages())<div class="pmd-pagination">{{ $orders->links() }}</div>@endif
                </section>
            @else
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Rider profile &amp; registered area</h2><p>This is the Rider's registered account area, not live GPS telemetry.</p></div></div>
                    <div class="pmd-body"><dl class="pmd-kv">
                        <dt>Rider</dt><dd>{{ $displayName }}</dd><dt>Email</dt><dd>{{ $account->email }}</dd><dt>Contact</dt><dd>{{ $account->contact_no ?: '—' }}</dd>
                        <dt>Registered area</dt><dd>{{ $location ?: '—' }}</dd><dt>Logistics provider</dt><dd>{{ $account->logistics?->displayName() ?? 'Not linked' }}</dd>
                        <dt>Vehicle</dt><dd>{{ $account->vehicle_type ?: '—' }} · {{ $account->vehicle_model ?: '—' }}</dd><dt>Plate number</dt><dd>{{ $account->plate_number ?: '—' }}</dd>
                        <dt>Availability</dt><dd>{{ ucfirst(str_replace('_',' ',(string)($account->availability_status ?: 'offline'))) }}</dd><dt>Rating</dt><dd>{{ $account->rating !== null ? number_format((float)$account->rating,2) : '—' }}</dd>
                    </dl></div>
                </section>
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Products currently assigned to Rider</h2><p>Aggregated from products inside the Rider's active Marketplace delivery jobs.</p></div><span class="pmd-pill">{{ number_format($currentLoad->sum('quantity')) }} units</span></div>
                    <div class="pmd-table-wrap">
                        @if($currentLoad->count())
                            <table class="pmd-table"><thead><tr><th>Product</th><th>Variant</th><th>Quantity</th><th>Orders</th></tr></thead><tbody>
                            @foreach($currentLoad as $load)<tr><td><div class="pmd-name">{{ $load['name'] }}</div></td><td>{{ $load['variant_label'] ?: 'Standard' }}</td><td>{{ number_format($load['quantity']) }}</td><td>{{ implode(', ', $load['orders']) }}</td></tr>@endforeach
                            </tbody></table>
                        @else<div class="pmd-empty">This Rider has no active product load right now.</div>@endif
                    </div>
                </section>
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Active delivery jobs</h2><p>Current pickup and delivery assignments.</p></div></div>
                    @include('admin.monitoring.partials.orders-table', ['ordersList' => $activeOrders])
                </section>
                <section class="pmd-panel">
                    <div class="pmd-panel-head"><div><h2>Recent Rider delivery history</h2><p>Latest assigned delivery records, including completed and cancelled jobs.</p></div></div>
                    @include('admin.monitoring.partials.orders-table', ['ordersList' => $orders])
                </section>
            @endif
        </div>
        <aside class="pmd-stack">
            <section class="pmd-panel pmd-admin">
                <div class="pmd-panel-head"><div><h2>Administrative controls</h2><p>Account restrictions take effect server-side and are written to the Admin audit trail.</p></div></div>
                <div class="pmd-body">
                    @if($status === 'banned')
                        <div class="pmd-warning">This account is permanently banned. The monitoring workspace does not expose ban reversal because the existing general User Management unban flow releases the email for re-registration rather than restoring the original account.</div>
                    @elseif($status === 'suspended' && $kind === 'seller')
                        <form method="POST" action="{{ route('admin.compliance.sellers.unsuspend', $account) }}" data-confirm="Lift this Seller's timed suspension?">@csrf<button class="pmd-action restore" type="submit">Lift timed suspension</button></form>
                        <form method="POST" action="{{ route('admin.users.block', ['role'=>$roleKey,'id'=>$account->id]) }}" class="mt-3" data-confirm="Block this account indefinitely?">@csrf<textarea class="pmd-reason" name="reason" minlength="5" placeholder="Reason for blocking"></textarea><div class="pmd-actions"><button class="pmd-action block" type="submit">Block account</button></div></form>
                    @elseif($status === 'blocked')
                        <form method="POST" action="{{ route('admin.users.unblock', ['role'=>$roleKey,'id'=>$account->id]) }}" data-confirm="Unblock this account and restore sign-in access?">@csrf<button class="pmd-action restore" type="submit">Unblock account</button></form>
                        <form method="POST" action="{{ route('admin.users.ban', ['role'=>$roleKey,'id'=>$account->id]) }}" class="mt-3" data-confirm="Permanently ban this account?">
                            @csrf<textarea class="pmd-reason" name="reason" minlength="5" placeholder="Reason required for permanent ban"></textarea><div class="pmd-actions"><button class="pmd-action ban" type="submit">Ban permanently</button></div>
                        </form>
                    @elseif($status === 'deactivated')
                        <form method="POST" action="{{ route('admin.users.restore', ['role'=>$roleKey,'id'=>$account->id]) }}" data-confirm="Restore this suspended account?">@csrf<button class="pmd-action restore" type="submit">Restore account</button></form>
                        <form method="POST" action="{{ route('admin.users.block', ['role'=>$roleKey,'id'=>$account->id]) }}" class="mt-3" data-confirm="Block this account indefinitely?">@csrf<textarea class="pmd-reason" name="reason" minlength="5" placeholder="Reason for blocking"></textarea><div class="pmd-actions"><button class="pmd-action block" type="submit">Block account</button></div></form>
                    @else
                        <form method="POST" action="{{ route('admin.users.suspend', ['role'=>$roleKey,'id'=>$account->id]) }}" data-account-control>
                            @csrf
                            <textarea class="pmd-reason" name="reason" minlength="5" placeholder="Enter the administrative reason (minimum 5 characters)"></textarea>
                            <div class="pmd-actions">
                                <button class="pmd-action suspend" type="submit" formaction="{{ route('admin.users.suspend', ['role'=>$roleKey,'id'=>$account->id]) }}" data-confirm-action="Suspend this account?">Suspend</button>
                                <button class="pmd-action block" type="submit" formaction="{{ route('admin.users.block', ['role'=>$roleKey,'id'=>$account->id]) }}" data-confirm-action="Block this account indefinitely?">Block</button>
                                <button class="pmd-action ban" type="submit" formaction="{{ route('admin.users.ban', ['role'=>$roleKey,'id'=>$account->id]) }}" data-confirm-action="Permanently ban this account and email?">Ban</button>
                            </div>
                        </form>
                    @endif
                    <div class="pmd-warning">Suspend disables account access using the existing deactivated state. Block is an indefinite reversible lock. Ban is permanent and also uses SARI's email-ban protection.</div>
                </div>
            </section>
            <section class="pmd-panel">
                <div class="pmd-panel-head"><div><h2>Recent account activity</h2><p>User and Admin audit events when available.</p></div></div>
                <div class="pmd-body">
                    @if($activity->count())
                        <div class="pmd-activity" id="monitorAccountActivity">
                            @foreach($activity as $event)
                                <div class="pmd-event" @if($loop->index >= 5) data-extra-activity hidden @endif><strong>{{ ucwords(str_replace('_',' ',(string)$event->action)) }}</strong><span>{{ $event->description ?: 'Activity recorded.' }}</span><span>{{ $event->created_at?->format('M d, Y · h:i A') }}</span></div>
                            @endforeach
                        </div>
                    @if($activity->count() > 5)
                        <button type="button" class="pmd-activity-toggle" id="monitorActivityToggle" aria-expanded="false" aria-controls="monitorAccountActivity" data-total="{{ $activity->count() }}">See more ({{ $activity->count() - 5 }})</button>
                    @endif
                    @else<div class="pmd-empty">No centralized activity events recorded for this account yet.</div>@endif
                </div>
            </section>
        </aside>
    </div>
</div>
<script>
(() => {
    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'Continue?')) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-account-control]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const submitter = event.submitter;
            const reason = form.querySelector('[name="reason"]');
            if (!reason || reason.value.trim().length < 5) {
                event.preventDefault();
                reason?.focus();
                return;
            }
            if (submitter?.dataset.confirmAction && !window.confirm(submitter.dataset.confirmAction)) {
                event.preventDefault();
            }
        });
    });
})();
</script>
@if($kind === 'logistics')
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
        (() => {
            const node = document.getElementById('adminLogisticsOperationsMap');
            if (!node || !window.L || node.dataset.mapReady === '1') return;
            node.dataset.mapReady = '1';
            let points = [];
            try { points = JSON.parse(node.dataset.mapPoints || '[]'); } catch (_) {}
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#039;'}[char]));
            const map = L.map(node, {scrollWheelZoom:false});
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19, attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
            const bounds = [];
            points.forEach((point) => {
                if (!Number.isFinite(Number(point.lat)) || !Number.isFinite(Number(point.lng))) return;
                const latlng = [Number(point.lat), Number(point.lng)];
                bounds.push(latlng);
                const color = point.type === 'hub' ? '#D59617' : (point.type === 'done' ? '#5B9270' : '#4F7EA8');
                const marker = L.circleMarker(latlng, {radius:7, color:'#fff', weight:2, fillColor:color, fillOpacity:1}).addTo(map);
                const orderLine = point.order ? `<br><strong>${escapeHtml(point.order)}</strong>` : '';
                marker.bindPopup(`<strong>${escapeHtml(point.name || 'Location')}</strong>${orderLine}<br>${escapeHtml(point.status || '')}<br>${escapeHtml(point.address || '')}`);
            });
            if (bounds.length) map.fitBounds(bounds, {padding:[28,28], maxZoom:14}); else map.setView([12.8797,121.7740], 5);
            setTimeout(() => map.invalidateSize(), 80);
        })();
        </script>
    @endpush
@endif

<script>
(() => {
    const button = document.getElementById('monitorActivityToggle');
    const list = document.getElementById('monitorAccountActivity');
    if (!button || !list) return;
    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') !== 'true';
        list.querySelectorAll('[data-extra-activity]').forEach(item => { item.hidden = !expanded; });
        button.setAttribute('aria-expanded', String(expanded));
        button.textContent = expanded ? 'Show less' : 'See more (' + (Number(button.dataset.total) - 5) + ')';
        if (!expanded) button.focus({ preventScroll: true });
    });
})();
</script>


<script>
(() => {
    document.querySelectorAll('[data-collapsible-records]').forEach(panel => {
        const table = panel.querySelector('.pmd-table');
        if (!table || !table.tBodies.length) return;
        const rows = Array.from(table.tBodies[0].rows);
        if (rows.length <= 5) return;
        const extra = rows.slice(5);
        extra.forEach(row => { row.hidden = true; });
        const key = panel.dataset.collapsibleRecords;
        table.id = 'monitor-records-' + key;
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'pmd-activity-toggle pmd-records-toggle';
        button.setAttribute('aria-expanded', 'false');
        button.setAttribute('aria-controls', table.id);
        button.textContent = 'See more (' + extra.length + ')';
        const pagination = panel.querySelector('.pmd-pagination');
        panel.insertBefore(button, pagination || null);
        button.addEventListener('click', () => {
            const expanded = button.getAttribute('aria-expanded') !== 'true';
            extra.forEach(row => { row.hidden = !expanded; });
            button.setAttribute('aria-expanded', String(expanded));
            button.textContent = expanded ? 'Show less' : 'See more (' + extra.length + ')';
            if (!expanded) button.focus({ preventScroll: true });
        });
    });
})();
</script>

@endsection
