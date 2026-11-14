@extends('layouts.logistics')

@section('title', ($isHubOperator ? 'Hub Activity' : 'Activity Logs') . ' | SARI Logistics')
@section('page-title', $isHubOperator ? 'Hub Activity' : 'Activity Logs')

@push('styles')
<style>
/* Buyer Recent Activity-inspired UI. Scoped so shared Logistics/Buyer styles cannot add outer cards. */
#lgActivityBuyerParity { --act-gold:#d9950b; --act-ink:#25303d; --act-muted:#828c99; --act-line:#e7eaee; width:100%; max-width:1560px; margin:0 auto; color:var(--act-ink); font-family:Poppins,ui-sans-serif,system-ui,sans-serif; }
#lgActivityBuyerParity *,#lgActivityBuyerParity *::before,#lgActivityBuyerParity *::after { box-sizing:border-box; }
#lgActivityBuyerParity .act-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:18px; padding:2px 0 15px; background:transparent!important; border:0!important; border-bottom:1px solid #e7e9ed!important; border-radius:0!important; box-shadow:none!important; }
#lgActivityBuyerParity .act-eyebrow { margin:0; font-size:10px; font-weight:800; line-height:1.3; letter-spacing:.14em; color:#aa740f; text-transform:uppercase; }
#lgActivityBuyerParity .act-header h1 { margin:5px 0 0; font-size:clamp(26px,2.2vw,31px); font-weight:760; letter-spacing:-.045em; line-height:1.15; color:#27303a; }
#lgActivityBuyerParity .act-header h1 em { color:#c68b16; font-style:normal; }
#lgActivityBuyerParity .act-identity { display:flex; align-items:center; gap:10px; min-width:0; padding:0; background:transparent!important; border:0!important; box-shadow:none!important; }
#lgActivityBuyerParity .act-identity-info { display:grid; gap:2px; min-width:0; }
#lgActivityBuyerParity .act-identity-info small { color:#8993a0; font-size:10px; font-weight:600; }
#lgActivityBuyerParity .act-identity-info strong { color:#344051; font-size:12px; font-weight:700; max-width:210px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; }
#lgActivityBuyerParity .act-avatar { display:grid; place-items:center; flex:0 0 46px; width:46px; height:46px; overflow:hidden; border:2px solid #e5c78d; border-radius:50%; color:#a0781e; background:#f4f0e7; font-size:14px; font-weight:800; box-shadow:0 2px 8px rgba(30,39,48,.06); }
#lgActivityBuyerParity .act-avatar img { display:block; width:100%; height:100%; object-fit:cover; border-radius:50%; }
#lgActivityBuyerParity .act-avatar-hub { background:#eff2f5; color:#788595; border-color:#dce2e9; box-shadow:none; }
#lgActivityBuyerParity .act-summary { display:grid!important; grid-template-columns:repeat(3,minmax(0,1fr))!important; gap:14px; margin:0 0 18px; padding:0!important; background:transparent!important; border:0!important; box-shadow:none!important; }
#lgActivityBuyerParity .act-stat { min-width:0; min-height:110px; display:flex; align-items:flex-start; justify-content:space-between; gap:10px; padding:17px 19px; border:1px solid #e6e9ee; border-radius:18px; background:#fff; box-shadow:0 6px 20px rgba(24,35,48,.06); }
#lgActivityBuyerParity .act-stat p { margin:0; color:#687586; font-size:12px; font-weight:650; line-height:1.5; }
#lgActivityBuyerParity .act-stat strong { display:block; margin:4px 0 1px; color:#27303a; font-size:27px; font-weight:800; letter-spacing:-.05em; line-height:1.15; }
#lgActivityBuyerParity .act-stat small { color:#97a1af; font-size:10px; }
#lgActivityBuyerParity .act-stat svg { width:27px; height:27px; flex:0 0 27px; fill:none; stroke-width:1.7; stroke:currentColor; }
#lgActivityBuyerParity .act-stat:nth-child(1) svg { color:#bb871e; }
#lgActivityBuyerParity .act-stat:nth-child(2) svg { color:#4c8a6c; }
#lgActivityBuyerParity .act-stat:nth-child(3) svg { color:#6486b4; }
#lgActivityBuyerParity .act-filters { display:grid; grid-template-columns:minmax(210px,2.1fr) minmax(135px,1fr) minmax(135px,1.1fr) minmax(135px,.95fr) minmax(135px,.95fr) auto auto; align-items:end; gap:10px; margin:0 0 14px; padding:0!important; background:transparent!important; border:0!important; box-shadow:none!important; }
#lgActivityBuyerParity .act-field { display:flex; flex-direction:column; gap:5px; min-width:0; }
#lgActivityBuyerParity .act-field label { color:#697586; font-size:10px; font-weight:700; }
#lgActivityBuyerParity .act-field :is(input,select) { display:block; width:100%; height:43px; min-height:43px; padding:0 12px; border:1px solid #dce1e7; border-radius:11px; outline:none; background:#fff; color:#3a4553; font-size:11px; box-shadow:none; }
#lgActivityBuyerParity .act-field input:focus,#lgActivityBuyerParity .act-field select:focus { border-color:#bf9a54; box-shadow:0 0 0 3px rgba(217,149,11,.07); }
#lgActivityBuyerParity .act-btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:43px; height:43px; padding:0 17px; border:1px solid #dce1e7; border-radius:11px; background:#fff; color:#4d5b6d; font-size:11px; font-weight:750; text-decoration:none; white-space:nowrap; cursor:pointer; }
#lgActivityBuyerParity .act-btn-primary { background:#d9950b; color:#fff; border-color:#d9950b; box-shadow:0 4px 13px rgba(217,149,11,.13); }
#lgActivityBuyerParity .act-btn-primary:hover { background:#c58708; color:#fff; }
#lgActivityBuyerParity .act-btn:not(.act-btn-primary):hover { background:#f4f5f7; color:#27303a; }
#lgActivityBuyerParity .act-shell { overflow:hidden; border:1px solid #e5e8ed; border-radius:20px; background:#fff; box-shadow:0 5px 11px rgba(15,23,42,.045),0 16px 36px rgba(15,23,42,.07); }
#lgActivityBuyerParity .act-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; min-height:62px; padding:16px 21px; border-bottom:1px solid #e7eaee; background:#fff; }
#lgActivityBuyerParity .act-toolbar h2 { margin:0; color:#29323e; font-size:16px; font-weight:800; }
#lgActivityBuyerParity .act-toolbar span { font-size:11px; color:#85919e; font-weight:650; }
#lgActivityBuyerParity .act-entry { display:grid; grid-template-columns:48px minmax(0,1fr) minmax(110px,auto); gap:14px; align-items:center; min-height:99px; padding:17px 21px; background:#fff; border-bottom:1px solid #eaedf0; transition:background-color .15s ease; }
#lgActivityBuyerParity .act-entry:last-child { border-bottom:0; }
#lgActivityBuyerParity .act-entry:hover { background:#f5f6f7; }
#lgActivityBuyerParity .act-entry-main { min-width:0; }
#lgActivityBuyerParity .act-entry-title { margin:0; color:#202932; font-size:14px; font-weight:760; line-height:1.42; overflow-wrap:anywhere; }
#lgActivityBuyerParity .act-entry-meta { display:flex; gap:5px 8px; flex-wrap:wrap; align-items:center; margin-top:6px; color:#7e8997; font-size:11px; line-height:1.45; }
#lgActivityBuyerParity .act-entry-meta b { color:#475463; font-weight:700; }
#lgActivityBuyerParity .act-meta-divider { color:#b6bec8; }
#lgActivityBuyerParity .act-entry-scope { margin-top:6px; color:#8995a2; font-size:10.5px; line-height:1.5; }
#lgActivityBuyerParity .act-entry-side { display:flex; flex-direction:column; gap:8px; align-items:flex-end; justify-content:center; text-align:right; min-width:0; }
#lgActivityBuyerParity .act-tag { display:inline-flex; align-items:center; justify-content:center; min-height:26px; padding:4px 10px; border-radius:8px; background:#f1f3f6; color:#5f6d7e; font-size:10px; font-weight:700; max-width:100%; overflow-wrap:anywhere; }
#lgActivityBuyerParity .act-reference { color:#7f8995; font-size:10px; font-weight:600; overflow-wrap:anywhere; }
#lgActivityBuyerParity .act-empty { min-height:330px; padding:58px 20px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:11px; text-align:center; }
#lgActivityBuyerParity .act-empty svg { width:58px; height:58px; stroke:#bbc3cd; stroke-width:1.4; fill:none; }
#lgActivityBuyerParity .act-empty h3 { margin:0; font-size:22px; font-weight:760; color:#909aa7; }
#lgActivityBuyerParity .act-empty p { margin:0; max-width:420px; font-size:12px; color:#9da8b6; line-height:1.7; }
#lgActivityBuyerParity .act-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; padding:13px 20px; border-top:1px solid #e9edf0; color:#8a96a3; font-size:11px; }
#lgActivityBuyerParity .act-pages { display:flex; align-items:center; flex-wrap:wrap; gap:5px; }
#lgActivityBuyerParity .act-page { display:inline-flex; align-items:center; justify-content:center; min-width:37px; height:37px; padding:0 10px; background:#f2f4f7; border:1px solid transparent; border-radius:11px; color:#788393; font-size:12px; font-weight:750; text-decoration:none; }
#lgActivityBuyerParity .act-page[aria-current='page'] { background:#cc921f; color:#fff; box-shadow:0 5px 13px rgba(217,149,11,.15); }
#lgActivityBuyerParity a.act-page:hover { background:#e9edf1; color:#28313d; }
#lgActivityBuyerParity .act-footnote { color:#a1aab4; font-size:10px; }
#lgActivityBuyerParity :is(a,button,input,select):focus-visible { outline:2px solid #c18e2a; outline-offset:2px; }
@media (max-width:1350px){#lgActivityBuyerParity .act-filters{grid-template-columns:repeat(6,minmax(0,1fr));}#lgActivityBuyerParity .act-filters .act-search{grid-column:span 3;}#lgActivityBuyerParity .act-filters .act-module{grid-column:span 1;}#lgActivityBuyerParity .act-filters .act-facility{grid-column:span 2;}#lgActivityBuyerParity .act-filters .act-date{grid-column:span 2;}#lgActivityBuyerParity .act-filters .act-btn{grid-column:span 1;}}
@media (max-width:820px){#lgActivityBuyerParity .act-summary{grid-template-columns:repeat(3,minmax(0,1fr))!important;}#lgActivityBuyerParity .act-filters{grid-template-columns:repeat(4,minmax(0,1fr));}#lgActivityBuyerParity .act-filters .act-search{grid-column:span 4;}#lgActivityBuyerParity .act-filters .act-module,#lgActivityBuyerParity .act-filters .act-facility,#lgActivityBuyerParity .act-filters .act-date{grid-column:span 2;}#lgActivityBuyerParity .act-filters .act-btn{grid-column:span 2;}}
@media (max-width:540px){#lgActivityBuyerParity .act-header{align-items:flex-start;}#lgActivityBuyerParity .act-header h1{font-size:27px;}#lgActivityBuyerParity .act-summary{gap:10px;grid-template-columns:repeat(2,minmax(0,1fr))!important;}#lgActivityBuyerParity .act-stat:last-child{grid-column:1/-1;}#lgActivityBuyerParity .act-stat{min-height:104px;padding:14px;}#lgActivityBuyerParity .act-stat strong{font-size:24px;}#lgActivityBuyerParity .act-stat svg{width:23px;height:23px;flex-basis:23px;}#lgActivityBuyerParity .act-filters{grid-template-columns:repeat(2,minmax(0,1fr));}#lgActivityBuyerParity .act-filters .act-search{grid-column:span 2;}#lgActivityBuyerParity .act-filters .act-module,#lgActivityBuyerParity .act-filters .act-facility,#lgActivityBuyerParity .act-filters .act-date{grid-column:span 1;}#lgActivityBuyerParity .act-filters .act-btn{grid-column:span 1;}#lgActivityBuyerParity .act-entry{grid-template-columns:42px minmax(0,1fr);align-items:start;gap:11px;padding:15px;}#lgActivityBuyerParity .act-avatar{width:42px;height:42px;flex-basis:42px;}#lgActivityBuyerParity .act-entry-side{grid-column:2;flex-direction:row;align-items:center;justify-content:flex-start;text-align:left;flex-wrap:wrap;}#lgActivityBuyerParity .act-toolbar{padding:16px;}#lgActivityBuyerParity .act-footer{justify-content:center;text-align:center;}#lgActivityBuyerParity .act-pages{order:2;justify-content:center;width:100%;}}
@media (prefers-reduced-motion:reduce){#lgActivityBuyerParity *{transition:none!important;animation:none!important;}}

/* Activity filters V2 — gray/neutral focus, polished custom dropdowns, no cream/yellow selection. */
#lgActivityBuyerParity .act-filters { position:relative; z-index:5; overflow:visible!important; }
#lgActivityBuyerParity .act-field { gap:6px; }
#lgActivityBuyerParity .act-field label { color:#64748b; font-size:11px; font-weight:700; letter-spacing:.005em; }
#lgActivityBuyerParity .act-field :is(input,select) {
    width:100%; height:44px; min-height:44px; padding:0 13px;
    border:1px solid #d8dfe7!important; border-radius:11px; outline:0!important;
    background:#fff; color:#344054; font-size:12px; font-weight:500;
    box-shadow:none!important;
    transition:border-color .16s ease,box-shadow .16s ease,background-color .16s ease;
}
#lgActivityBuyerParity .act-field input::placeholder { color:#9ba6b4; opacity:1; }
#lgActivityBuyerParity .act-field :is(input,select):hover { border-color:#c2cbd6!important; background:#fff; }
#lgActivityBuyerParity .act-field :is(input,select):focus,
#lgActivityBuyerParity .act-field :is(input,select):focus-visible {
    border-color:#9aa8b9!important;
    outline:0!important;
    box-shadow:0 0 0 3px rgba(148,163,184,.15)!important;
}
#lgActivityBuyerParity .act-field select { appearance:none; -webkit-appearance:none; -moz-appearance:none; padding-right:40px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='m6 9 6 6 6-6' stroke='%2364748b' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-position:right 13px center; background-repeat:no-repeat; background-size:15px;
}
#lgActivityBuyerParity .act-search-wrap { position:relative; min-width:0; }
#lgActivityBuyerParity .act-search-wrap > svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); width:18px; height:18px; fill:none; stroke:#9aa6b6; stroke-width:1.8; stroke-linecap:round; pointer-events:none; }
#lgActivityBuyerParity .act-search-wrap input { padding-left:42px; }
#lgActivityBuyerParity .act-btn { height:44px; min-height:44px; border-radius:11px; font-size:12px; }
#lgActivityBuyerParity :is(a,button,input,select):focus-visible { outline:2px solid #94a3b8!important; outline-offset:2px; }
#lgActivityBuyerParity .act-field :is(input,select):focus-visible { outline:0!important; }
/* Enhanced select is installed only when JS runs; native selects work without JS. */
#lgActivityBuyerParity .act-field select[hidden] { display:none!important; }
#lgActivityBuyerParity .act-select-shell { position:relative; min-width:0; }
#lgActivityBuyerParity .act-select-shell.is-open { z-index:35; }
#lgActivityBuyerParity .act-select-trigger {
    display:flex!important; align-items:center; justify-content:space-between; gap:9px;
    width:100%; min-width:0; height:44px; padding:0 13px;
    border:1px solid #d8dfe7!important; border-radius:11px;
    background:#fff!important; color:#344054!important;
    font:500 12px Poppins,ui-sans-serif,system-ui,sans-serif;
    text-align:left; box-shadow:none!important; cursor:pointer;
    transition:border-color .16s ease,box-shadow .16s ease;
}
#lgActivityBuyerParity .act-select-trigger:hover { border-color:#c2cbd6!important; }
#lgActivityBuyerParity .act-select-trigger:focus-visible,
#lgActivityBuyerParity .act-select-shell.is-open .act-select-trigger {
    outline:0!important; border-color:#9aa8b9!important;
    box-shadow:0 0 0 3px rgba(148,163,184,.15)!important;
}
#lgActivityBuyerParity .act-select-text { flex:1; min-width:0; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; }
#lgActivityBuyerParity .act-select-chevron { flex:0 0 16px; width:16px; height:16px; fill:none; stroke:#8794a5; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; transition:transform .16s ease; }
#lgActivityBuyerParity .act-select-shell.is-open .act-select-chevron { transform:rotate(180deg); }
#lgActivityBuyerParity .act-select-menu {
    position:absolute; z-index:100; top:calc(100% + 7px); left:0; right:0;
    min-width:100%; max-height:min(300px,48vh); overflow:auto;
    padding:6px; background:#fff; border:1px solid #dde3eb; border-radius:13px;
    box-shadow:0 13px 30px rgba(24,39,56,.13),0 3px 9px rgba(24,39,56,.055);
    overscroll-behavior:contain; scrollbar-color:#cbd3de transparent;
}
#lgActivityBuyerParity .act-select-menu[hidden] { display:none!important; }
#lgActivityBuyerParity .act-select-shell.is-up .act-select-menu { top:auto; bottom:calc(100% + 7px); }
#lgActivityBuyerParity .act-select-option {
    display:flex; justify-content:space-between; align-items:center; gap:10px;
    width:100%; min-height:36px; padding:8px 10px;
    border:0!important; border-radius:8px; background:transparent!important;
    color:#475569!important; text-align:left; font:500 12px Poppins,ui-sans-serif,system-ui,sans-serif;
    cursor:pointer; box-shadow:none!important;
}
#lgActivityBuyerParity .act-select-option:hover,
#lgActivityBuyerParity .act-select-option.is-highlighted { background:#f0f3f6!important; color:#263242!important; }
#lgActivityBuyerParity .act-select-option[aria-selected="true"] { background:#f2f4f7!important; color:#334155!important; font-weight:700; }
#lgActivityBuyerParity .act-select-option[aria-selected="true"].is-highlighted { background:#e8edf3!important; }
#lgActivityBuyerParity .act-select-option:focus { outline:0!important; }
#lgActivityBuyerParity .act-select-check { width:15px; height:15px; flex:0 0 15px; stroke:#64748b; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
#lgActivityBuyerParity .act-select-option .act-select-check { visibility:hidden; }
#lgActivityBuyerParity .act-select-option[aria-selected="true"] .act-select-check { visibility:visible; }
@media (prefers-reduced-motion:reduce) { #lgActivityBuyerParity .act-select-chevron { transition:none; } }
</style>
@endpush

@section('content')
@php
    // Current profile image is only displayed for log entries made by this manager.
    // A Hub Operator is a distinct identity and has no saved avatar in the existing model.
    $providerImage = $provider->profile_image_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($provider->profile_image_path)
        : null;
    $providerDisplayName = $provider->displayName();
    $providerInitial = mb_strtoupper(mb_substr(trim((string) ($provider->first_name ?: $providerDisplayName)), 0, 1));
@endphp
<div id="lgActivityBuyerParity" class="sari-logistics-ui" aria-label="Logistics activity history">
    <header class="act-header">
        <div>
            <p class="act-eyebrow">{{ $isHubOperator ? 'YOUR ASSIGNED HUB' : 'YOUR LOGISTICS ACCOUNT' }}</p>
            <h1>{{ $isHubOperator ? 'Hub' : 'Recent' }} <em>Activity</em></h1>
        </div>
        @unless($isHubOperator)
            <div class="act-identity" aria-label="Logistics account profile">
                <span class="act-avatar" role="img" aria-label="{{ $providerDisplayName }} profile photo">
                    @if($providerImage)
                        <img src="{{ $providerImage }}" alt="" width="46" height="46" loading="lazy">
                    @else
                        {{ $providerInitial }}
                    @endif
                </span>
                <div class="act-identity-info">
                    <small>Logistics Account</small>
                    <strong>{{ $providerDisplayName }}</strong>
                </div>
            </div>
        @else
            <div class="act-identity">
                <span class="act-avatar act-avatar-hub" aria-hidden="true">H</span>
                <div class="act-identity-info">
                    <small>Assigned facility · Read-only</small>
                    <strong>{{ $hub?->name ?: 'Hub Activity' }}</strong>
                </div>
            </div>
        @endunless
    </header>

    <div class="act-summary" aria-label="Activity overview">
        <div class="act-stat">
            <div><p>All-time activities</p><strong>{{ number_format($totalCount) }}</strong><small>{{ $isHubOperator ? 'Assigned hub' : 'Logistics network' }}</small></div>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v15H4zM8 9h8M8 13h8M8 17h5"/></svg>
        </div>
        <div class="act-stat">
            <div><p>Recorded today</p><strong>{{ number_format($todayCount) }}</strong><small>{{ now('Asia/Manila')->format('F j, Y') }}</small></div>
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l4 2"/></svg>
        </div>
        <div class="act-stat">
            <div><p>Matching activities</p><strong>{{ number_format($logs->total()) }}</strong><small>Current search and filters</small></div>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v3l-7 7v6l-3-2v-4L4 7z"/></svg>
        </div>
    </div>

    {{-- Keep the existing server-validated GET filters, query strings and hub permissions. --}}
    <form action="{{ route('logistics.activity-logs') }}" method="GET" class="act-filters" aria-label="Filter activity logs">
        <div class="act-field act-search">
            <label for="actSearch">Search activity</label>
            <div class="act-search-wrap">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m16.5 16.5 4 4"></path></svg>
                <input id="actSearch" type="search" name="q" maxlength="80" value="{{ $keyword }}" placeholder="Search operator, activity, or hub…">
            </div>
        </div>
        <div class="act-field act-module"><label for="actModule">Module</label><select id="actModule" name="module"><option value="">All modules</option>@foreach($modules as $module)<option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $module }}</option>@endforeach</select></div>
        @unless($isHubOperator)
            <div class="act-field act-facility"><label for="actFacility">Facility</label><select id="actFacility" name="facility"><option value="">All hubs</option>@foreach($facilities as $facility)<option value="{{ $facility->id }}" @selected((string) ($filters['facility'] ?? '') === (string) $facility->id)>{{ $facility->name }}</option>@endforeach</select></div>
        @endunless
        <div class="act-field act-date"><label for="actFrom">From</label><input id="actFrom" type="date" name="from" value="{{ $filters['from'] ?? '' }}"></div>
        <div class="act-field act-date"><label for="actTo">To</label><input id="actTo" type="date" name="to" value="{{ $filters['to'] ?? '' }}"></div>
        <button type="submit" class="act-btn act-btn-primary">Apply Filter</button>
        <a class="act-btn" href="{{ route('logistics.activity-logs') }}">Reset</a>
    </form>

    <section class="act-shell" aria-labelledby="actHistoryHeading">
        <header class="act-toolbar"><h2 id="actHistoryHeading">{{ $isHubOperator ? 'Hub Activity History' : 'Logistics Activity History' }}</h2><span>{{ number_format($logs->total()) }} {{ $logs->total() === 1 ? 'update' : 'updates' }} · Read-only</span></header>
        @forelse($logs as $log)
            @php
                $isManagerActor = $log->actor_type === 'manager' && (int) $log->actor_id === (int) $provider->id;
                $actorName = trim((string) ($log->actor_name ?: 'Logistics Operator'));
                $actorInitial = mb_strtoupper(mb_substr($actorName, 0, 1));
                $actorRole = $log->actor_type === 'hub_operator' ? 'Hub Operator' : 'Main Logistics';
                $actionLabel = ucwords(str_replace('_', ' ', (string) $log->action));
            @endphp
            <article class="act-entry">
                <span class="act-avatar {{ $isManagerActor ? '' : 'act-avatar-hub' }}" role="img" aria-label="{{ $actorName }} profile avatar">
                    @if($isManagerActor && $providerImage)
                        <img src="{{ $providerImage }}" alt="" width="46" height="46" loading="lazy">
                    @else
                        {{ $actorInitial ?: 'L' }}
                    @endif
                </span>
                <div class="act-entry-main">
                    <h3 class="act-entry-title">{{ $log->description }}</h3>
                    <div class="act-entry-meta">
                        <b>{{ $actorName }}</b><span class="act-meta-divider">·</span>
                        <span>{{ $actorRole }}</span><span class="act-meta-divider">·</span>
                        <span title="{{ $log->created_at?->copy()->timezone('Asia/Manila')->format('M d, Y · h:i A') }}">{{ $log->created_at?->diffForHumans() }}</span>
                    </div>
                    <div class="act-entry-scope">{{ $log->facility_name ?: 'Network-wide' }} · {{ $log->created_at?->copy()->timezone('Asia/Manila')->format('M d, Y · h:i A') }}</div>
                </div>
                <div class="act-entry-side">
                    <span class="act-tag">{{ $log->module }} · {{ $actionLabel }}</span>
                    @if($log->subject_type && $log->subject_id)
                        <span class="act-reference">{{ ucfirst($log->subject_type) }} #{{ $log->subject_id }}</span>
                    @endif
                </div>
            </article>
        @empty
            <div class="act-empty">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 4v5h5M3.5 9A9 9 0 0 1 12 3M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h3>No activity found</h3>
                <p>Completed Logistics actions will appear here. Try clearing your filters if you're looking for older activity.</p>
            </div>
        @endforelse
        <footer class="act-footer">
            <span>Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }} activities</span>
            @if($logs->hasPages())
                @php
                    $current = $logs->currentPage();
                    $last = $logs->lastPage();
                    $start = max(1, min($current - 2, $last - 4));
                    $end = min($last, max(5, $current + 2));
                @endphp
                <nav class="act-pages" aria-label="Activity pages">
                    @if($logs->onFirstPage())<span class="act-page" aria-disabled="true">&lt;</span>@else<a class="act-page" href="{{ $logs->previousPageUrl() }}" aria-label="Previous page">&lt;</a>@endif
                    @for($page = $start; $page <= $end; $page++)
                        @if($page === $current)<span class="act-page" aria-current="page">{{ $page }}</span>@else<a class="act-page" href="{{ $logs->url($page) }}">{{ $page }}</a>@endif
                    @endfor
                    @if($logs->hasMorePages())<a class="act-page" href="{{ $logs->nextPageUrl() }}" aria-label="Next page">&gt;</a>@else<span class="act-page" aria-disabled="true">&gt;</span>@endif
                </nav>
            @endif
            <small class="act-footnote">Audit entries cannot be edited or deleted.</small>
        </footer>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    'use strict';

    function initActivityDropdowns() {
        const root = document.getElementById('lgActivityBuyerParity');
        if (!root || root.dataset.filtersEnhanced === 'true') return;
        root.dataset.filtersEnhanced = 'true';

        const controls = [];
        let opened = null;

        function closeMenu(control, restoreFocus = false) {
            if (!control) return;
            control.shell.classList.remove('is-open', 'is-up');
            control.menu.hidden = true;
            control.trigger.setAttribute('aria-expanded', 'false');
            if (opened === control) opened = null;
            if (restoreFocus) control.trigger.focus();
        }

        function setHighlight(control, index) {
            const { items, menu } = control;
            if (!items.length) return;
            control.highlightedIndex = Math.max(0, Math.min(index, items.length - 1));
            items.forEach((button, i) => {
                button.classList.toggle('is-highlighted', i === control.highlightedIndex);
                button.tabIndex = i === control.highlightedIndex ? 0 : -1;
            });
            items[control.highlightedIndex].scrollIntoView({ block: 'nearest' });
            menu.setAttribute('aria-activedescendant', items[control.highlightedIndex].id);
        }

        function openMenu(control) {
            if (opened && opened !== control) closeMenu(opened);
            const selected = Math.max(0, control.items.findIndex(button => button.dataset.value === control.select.value));
            control.menu.hidden = false;
            control.shell.classList.add('is-open');
            control.trigger.setAttribute('aria-expanded', 'true');
            opened = control;
            const bounds = control.trigger.getBoundingClientRect();
            const roomBelow = window.innerHeight - bounds.bottom;
            const roomAbove = bounds.top;
            control.shell.classList.toggle('is-up', roomBelow < Math.min(315, control.menu.scrollHeight + 12) && roomAbove > roomBelow);
            setHighlight(control, selected);
        }

        function choose(control, index) {
            const selected = control.items[index];
            if (!selected) return;
            control.select.value = selected.dataset.value;
            control.select.dispatchEvent(new Event('change', { bubbles: true }));
            control.trigger.querySelector('.act-select-text').textContent = selected.dataset.label;
            control.items.forEach(button => button.setAttribute('aria-selected', String(button === selected)));
            closeMenu(control, true);
        }

        root.querySelectorAll('.act-module select, .act-facility select').forEach((select, dropdownIndex) => {
            const field = select.closest('.act-field');
            if (!field) return;
            const label = field.querySelector('label');
            const shell = document.createElement('div');
            shell.className = 'act-select-shell';
            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'act-select-trigger';
            const menu = document.createElement('div');
            menu.className = 'act-select-menu';
            menu.hidden = true;
            menu.id = `act-options-${dropdownIndex}`;
            menu.setAttribute('role', 'listbox');
            menu.setAttribute('aria-label', label?.textContent.trim() || 'Filter options');
            trigger.setAttribute('aria-haspopup', 'listbox');
            trigger.setAttribute('aria-controls', menu.id);
            trigger.setAttribute('aria-expanded', 'false');
            trigger.setAttribute('aria-label', label?.textContent.trim() || 'Choose filter');
            const text = document.createElement('span');
            text.className = 'act-select-text';
            text.textContent = select.selectedOptions[0]?.textContent.trim() || '';
            trigger.appendChild(text);
            trigger.insertAdjacentHTML('beforeend', '<svg class="act-select-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>');
            const items = Array.from(select.options).map((option, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'act-select-option';
                button.id = `act-option-${dropdownIndex}-${index}`;
                button.dataset.value = option.value;
                button.dataset.label = option.textContent.trim();
                button.setAttribute('role', 'option');
                button.setAttribute('aria-selected', String(option.selected));
                button.tabIndex = -1;
                const itemLabel = document.createElement('span');
                itemLabel.textContent = option.textContent.trim();
                button.appendChild(itemLabel);
                const check = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                check.setAttribute('class', 'act-select-check');
                check.setAttribute('viewBox', '0 0 24 24');
                check.setAttribute('aria-hidden', 'true');
                check.innerHTML = '<path d="m5 12 5 5L19 7"/>';
                button.appendChild(check);
                button.addEventListener('click', () => choose(control, index));
                button.addEventListener('mouseenter', () => setHighlight(control, index));
                menu.appendChild(button);
                return button;
            });
            const control = { select, shell, trigger, menu, items, highlightedIndex: 0 };
            shell.append(trigger, menu);
            select.insertAdjacentElement('afterend', shell);
            // Keep the original named select in the GET form, including its current value.
            // If JavaScript is disabled, it remains a normal native select.
            select.hidden = true;
            select.tabIndex = -1;
            select.setAttribute('aria-hidden', 'true');
            trigger.addEventListener('click', () => opened === control ? closeMenu(control) : openMenu(control));
            shell.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    if (opened === control) { event.preventDefault(); closeMenu(control, true); }
                    return;
                }
                if (event.key === 'Tab') { if (opened === control) closeMenu(control); return; }
                if (['ArrowDown','ArrowUp','Home','End','Enter',' '].includes(event.key)) {
                    event.preventDefault();
                    if (opened !== control) {
                        openMenu(control);
                        if (event.key === 'Enter' || event.key === ' ') return;
                    }
                    if (event.key === 'ArrowDown') setHighlight(control, control.highlightedIndex + 1);
                    if (event.key === 'ArrowUp') setHighlight(control, control.highlightedIndex - 1);
                    if (event.key === 'Home') setHighlight(control, 0);
                    if (event.key === 'End') setHighlight(control, control.items.length - 1);
                    if (event.key === 'Enter' || event.key === ' ') choose(control, control.highlightedIndex);
                }
            });
            controls.push(control);
        });

        document.addEventListener('pointerdown', event => {
            if (opened && !opened.shell.contains(event.target)) closeMenu(opened);
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && opened) closeMenu(opened, true);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initActivityDropdowns, { once: true });
    } else {
        initActivityDropdowns();
    }
})();
</script>
@endpush
