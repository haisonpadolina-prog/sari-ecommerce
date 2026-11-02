@extends('layouts.logistics')

@section('title', 'Branch Treasury — SARI Logistics')
@section('page-title', 'Branch Treasury')

@push('styles')
<style>
/* SARI Logistics — Branch Treasury | Scoped UI only. No shared stylesheet required. */
#sariBranchTreasury { --bt-gold:#d9950b; --bt-ink:#213044; --bt-muted:#7b899a; --bt-line:#e3e8ee; --bt-shadow:0 9px 28px rgba(31,41,55,.07); width:100%; max-width:1600px; margin-inline:auto; color:var(--bt-ink); font-size:13px; }
#sariBranchTreasury, #sariBranchTreasury * { box-sizing:border-box; }
#sariBranchTreasury [hidden], #sariBranchTreasury .hidden { display:none !important; }
#sariBranchTreasury :is(button,input,select,a):focus-visible { outline:2px solid #6c839a; outline-offset:3px; }
#sariBranchTreasury button { cursor:pointer; }
#sariBranchTreasury .bt-header { position:relative; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:16px; margin:0 0 21px; padding:4px 0 16px; border:0; background:transparent; box-shadow:none; }
#sariBranchTreasury .bt-eyebrow { margin:0 0 6px; font-size:11px; font-weight:750; letter-spacing:.12em; text-transform:uppercase; color:#ad7519; }
#sariBranchTreasury .bt-page-title { margin:0; font-size:clamp(25px,2.2vw,32px); font-weight:760; letter-spacing:-.045em; line-height:1.2; color:#1f2d3d; }
#sariBranchTreasury .bt-header-tools { display:flex; align-items:center; gap:10px; margin-left:auto; }
#sariBranchTreasury .bt-branch-role { display:inline-flex; align-items:center; padding:9px 12px; border:1px solid #e1e6ec; border-radius:10px; background:#f6f8fa; color:#526275; font-size:11px; font-weight:650; white-space:nowrap; }
#sariBranchTreasury .bt-help-anchor { position:relative; }
#sariBranchTreasury .bt-help-trigger { display:grid; place-items:center; width:39px; height:39px; padding:0; background:#fff; border:1px solid #dae1e9; border-radius:11px; color:#536578; transition:background .16s ease,border-color .16s ease; }
#sariBranchTreasury .bt-help-trigger svg { width:20px; height:20px; }
#sariBranchTreasury .bt-help-trigger:hover, #sariBranchTreasury .bt-help-trigger[aria-expanded=true] { background:#f2f5f8; border-color:#bcc8d5; }
#sariBranchTreasury .bt-help-panel { position:absolute; top:calc(100% + 13px); right:0; width:min(420px,calc(100vw - 34px)); z-index:100; overflow:visible; padding:0; border:1px solid #e0e6ed; border-radius:15px; background:#fff; box-shadow:0 20px 56px rgba(30,42,57,.17); opacity:0; visibility:hidden; transform:translateY(-10px) scale(.985); transform-origin:top right; pointer-events:none; transition:opacity .2s ease,transform .25s cubic-bezier(.2,.8,.2,1),visibility 0s linear .25s; }
#sariBranchTreasury .bt-help-panel.is-open { opacity:1; visibility:visible; transform:translateY(0) scale(1); pointer-events:auto; transition:opacity .2s ease,transform .25s cubic-bezier(.2,.8,.2,1),visibility 0s; }
#sariBranchTreasury .bt-help-panel::before { content:''; position:absolute; top:-5px; right:14px; width:9px; height:9px; transform:rotate(45deg); background:#fff; border-top:1px solid #e0e6ed; border-left:1px solid #e0e6ed; }
#sariBranchTreasury .bt-help-top { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:18px 19px 14px; border-bottom:1px solid #edf0f3; }
#sariBranchTreasury .bt-help-title { margin:0; font-size:16px; line-height:1.35; font-weight:750; color:#253244; }
#sariBranchTreasury .bt-help-close { display:grid;place-items:center; flex:0 0 32px; width:32px; height:32px; padding:0; border-radius:9px; border:1px solid #e2e7ec; background:#fff; color:#5c6a7a; }
#sariBranchTreasury .bt-help-close:hover { background:#f3f5f7; }
#sariBranchTreasury .bt-help-copy { margin:0; padding:16px 19px 19px; color:#5b697a; font-size:13px; line-height:1.7; overflow-wrap:anywhere; }
#sariBranchTreasury .bt-help-copy p { margin:0 0 10px; }
#sariBranchTreasury .bt-help-copy p:last-child { margin-bottom:0; }
#sariBranchTreasury .bt-notice { margin:0 0 16px; border-radius:11px; padding:12px 15px; font-size:12px; }
#sariBranchTreasury .bt-notice-success { background:#eaf7ee; color:#247d4a; border:1px solid #cfead8; }
#sariBranchTreasury .bt-notice-error { background:#fff0ef; color:#ad3a32; border:1px solid #f4d0cb; }
#sariBranchTreasury .bt-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:13px; margin-bottom:26px; }
#sariBranchTreasury .bt-stat { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; min-width:0; min-height:107px; padding:17px 18px; border:1px solid #e6e9ee; border-radius:17px; background:#fff; box-shadow:var(--bt-shadow); }
#sariBranchTreasury .bt-stat-label { margin:0; font-size:12px; font-weight:650; color:#657387; line-height:1.45; }
#sariBranchTreasury .bt-stat-value { margin:9px 0 0; font-weight:780; font-size:clamp(17px,1.55vw,24px); line-height:1.22; letter-spacing:-.035em; color:#1f2d3d; overflow-wrap:anywhere; }
#sariBranchTreasury .bt-stat-icon { flex:0 0 auto; display:block; padding:0; margin:1px 0 0; border:0; border-radius:0; background:transparent !important; box-shadow:none !important; }
#sariBranchTreasury .bt-stat-icon svg { display:block; width:30px; height:30px; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
#sariBranchTreasury .bt-tone-gold { color:#c18a25; } #sariBranchTreasury .bt-tone-blue { color:#4e86b5; } #sariBranchTreasury .bt-tone-green { color:#42946c; } #sariBranchTreasury .bt-tone-rose { color:#bd7180; }
#sariBranchTreasury .bt-scanner { display:flex; justify-content:flex-end; align-items:center; padding:0; margin:0 0 24px; min-height:42px; background:transparent; border:0; box-shadow:none; border-radius:0; }
#sariBranchTreasury .bt-scanner:has(#branchTreasuryScannerWrap:not(.hidden)) { display:block; }
#sariBranchTreasury .bt-scanner-actions { display:flex; justify-content:flex-end; }
#sariBranchTreasury .bt-button { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:40px; padding:10px 15px; border:1px solid #d7dfe8; border-radius:10px; font-size:12px; line-height:1.3; font-weight:700; text-decoration:none; white-space:normal; text-align:center; transition:background .16s ease, border-color .16s ease; }
#sariBranchTreasury .bt-button-charcoal { background:#222b36; color:#fff; border-color:#222b36; } #sariBranchTreasury .bt-button-charcoal:hover { background:#334155; color:#fff; }
#sariBranchTreasury .bt-button-gold { background:#d9950b; border-color:#d9950b; color:#fff; } #sariBranchTreasury .bt-button-gold:hover { background:#c88908; border-color:#c88908; color:#fff; }
#sariBranchTreasury .bt-button-plain { background:#fff; color:#526274; } #sariBranchTreasury .bt-button-plain:hover { background:#f3f6f9; }
#sariBranchTreasury .bt-button:disabled { cursor:not-allowed; opacity:.48; }
#sariBranchTreasury #branchTreasuryScannerWrap { max-width:700px; margin:16px auto 0; }
#sariBranchTreasury #branchTreasuryQrReader { border:1px solid #2d3641; overflow:hidden; border-radius:13px; background:#111821; }
#sariBranchTreasury .bt-scanner-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:12px; }
#sariBranchTreasury .bt-scanner-status { font-size:12px; color:#718096; line-height:1.5; }
#sariBranchTreasury .bt-card { min-width:0; overflow:hidden; margin-top:18px; border:1px solid #e4e8ee; border-radius:17px; background:#fff; box-shadow:var(--bt-shadow); }
#sariBranchTreasury .bt-card-head { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:17px 19px; border-bottom:1px solid #edf0f3; }
#sariBranchTreasury .bt-section-title { margin:0; color:#263246; font-size:18px; letter-spacing:-.02em; line-height:1.35; font-weight:740; }
/* Compact Treasury History heading only; protects against shared layout type overrides. */
#logisticsContent #sariBranchTreasury #btHistoryHeading.bt-section-title {
    font-size: 16px !important;
    line-height: 1.35 !important;
    font-weight: 700 !important;
    letter-spacing: -.018em !important;
}
@media (max-width: 600px) {
    #logisticsContent #sariBranchTreasury #btHistoryHeading.bt-section-title {
        font-size: 15px !important;
    }
}

#sariBranchTreasury .bt-count { flex:0 0 auto; border-radius:9px; border:1px solid #e3e8ee; padding:8px 11px; background:#f7f9fb; color:#627084; font-weight:750; font-size:10px; }
#sariBranchTreasury .bt-card-description { padding:0 19px 14px; color:#8090a1; font-size:12px; }
#sariBranchTreasury .bt-form { padding:18px 19px 20px; }
#sariBranchTreasury .bt-form-footer { display:flex; align-items:end; gap:14px; justify-content:space-between; flex-wrap:wrap; margin-top:16px; }
#sariBranchTreasury .bt-form-footer > label { flex:1 1 280px; }
#sariBranchTreasury .bt-field-label { display:block; margin:0 0 7px; color:#647488; font-size:11px; font-weight:700; }
#sariBranchTreasury .bt-input, #sariBranchTreasury .bt-select { display:block; width:100%; min-height:42px; padding:10px 12px; background:#fff; border:1px solid #dce3eb; border-radius:10px; font-size:12px; color:#334155; }
#sariBranchTreasury .bt-input:focus, #sariBranchTreasury .bt-select:focus { border-color:#9fb0c0; outline:2px solid rgba(100,116,139,.12); }
#sariBranchTreasury .bt-table-wrap { width:100%; overflow-x:auto; }
#sariBranchTreasury .bt-table { width:100%; border-collapse:collapse; table-layout:auto; font-size:12px; text-align:left; }
#sariBranchTreasury .bt-table thead { background:#f7f9fb; }
#sariBranchTreasury .bt-table th { padding:12px 15px; color:#6e7b8d; font-size:10px; font-weight:750; line-height:1.35; text-transform:uppercase; letter-spacing:.055em; white-space:nowrap; }
#sariBranchTreasury .bt-table td { padding:14px 15px; border-top:1px solid #edf0f3; color:#526073; line-height:1.45; vertical-align:middle; overflow-wrap:anywhere; }
#sariBranchTreasury .bt-table tbody tr { transition:background-color .13s ease; } #sariBranchTreasury .bt-table tbody tr:hover { background:#f3f5f7; }
#sariBranchTreasury .bt-primary-text { display:block; font-weight:740; color:#253346; font-size:12px; }
#sariBranchTreasury .bt-secondary-text { display:block; margin-top:3px; color:#8793a3; font-size:11px; line-height:1.4; }
#sariBranchTreasury .bt-money { font-weight:760; color:#253346; font-size:13px; white-space:nowrap; }
#sariBranchTreasury .bt-status { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:11px; line-height:1.2; font-weight:750; white-space:nowrap; }
#sariBranchTreasury .bt-status::before { content:''; flex:0 0 7px; height:7px; width:7px; border-radius:50%; background:currentColor; opacity:.8; }
#sariBranchTreasury .bt-status-verified { background:#eaf7ef; color:#24844f; } #sariBranchTreasury .bt-status-discrepancy { background:#fff0ec; color:#b35c48; } #sariBranchTreasury .bt-status-submitted { background:#fff6e8; color:#a97820; }
#sariBranchTreasury .bt-status-default { background:#f2f4f6; color:#617186; }
#sariBranchTreasury .bt-filter-bar { display:grid; grid-template-columns:minmax(0,1fr) 180px auto auto; gap:11px; align-items:center; margin:22px 0 13px; }
#sariBranchTreasury .bt-search-wrap { position:relative; min-width:0; }
#sariBranchTreasury .bt-search-wrap svg { position:absolute; width:18px; height:18px; top:12px; left:13px; pointer-events:none; fill:none; stroke:#8b9aab; stroke-width:1.8; stroke-linecap:round; }
#sariBranchTreasury .bt-search-wrap .bt-input { padding-left:40px; }
#sariBranchTreasury .bt-history-footer { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; border-top:1px solid #edf0f3; padding:14px 18px; color:#8190a0; font-size:12px; }
#sariBranchTreasury .bt-empty { display:flex; align-items:center; flex-direction:column; justify-content:center; gap:10px; min-height:240px; padding:38px 20px 40px; text-align:center; }
#sariBranchTreasury .bt-empty-icon { display:grid; place-items:center; width:58px; height:58px; margin:0 auto 1px; color:#bac3cc; background:transparent; border:0; }
#sariBranchTreasury .bt-empty-icon svg { width:58px; height:58px; stroke:currentColor; stroke-width:1.55; fill:none; stroke-linecap:round; stroke-linejoin:round; }
#sariBranchTreasury .bt-empty h3 { margin:0; font-size:22px; font-weight:750; letter-spacing:-.03em; color:#9aa8b8; }
#sariBranchTreasury .bt-empty p { margin:0; font-size:12px; color:#9aa8b8; }
#sariBranchTreasury .bt-pagination { display:flex; justify-content:center; padding:14px 0 6px; } #sariBranchTreasury .bt-pagination nav { max-width:100%; }
#sariBranchTreasury .bt-pagination :is(a,span)[aria-current] { color:#fff; background:#d9950b; border-radius:8px; }
#sariBranchTreasury .bt-qr-overlay { position:fixed; inset:0; z-index:99999; display:grid; place-items:center; padding:18px; background:rgba(25,34,46,.55); backdrop-filter:blur(3px); }
#sariBranchTreasury .bt-qr-dialog { width:min(100%,430px); max-height:min(90vh,700px); overflow-y:auto; padding:23px 22px; border:1px solid #e2e7ed; border-radius:18px; background:#fff; box-shadow:0 28px 64px rgba(20,25,37,.23); text-align:center; }
#sariBranchTreasury .bt-qr-top { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px; }
#sariBranchTreasury .bt-qr-top strong { color:#293649; font-size:17px; } #sariBranchTreasury .bt-qr-top button { background:#f4f6f8; border:1px solid #e4e8ed; border-radius:9px; height:33px; width:33px; display:grid;place-items:center; }
#sariBranchTreasury .bt-qr-box { display:grid; place-items:center; margin:20px auto 12px; width:220px; height:220px; background:#fff; padding:8px; border:1px solid #e6eaf0; border-radius:10px; }
#sariBranchTreasury .bt-qr-help { font-size:12px; line-height:1.6; color:#79889a; margin-top:6px; }
@media(max-width:1150px){ #sariBranchTreasury .bt-stats {grid-template-columns:repeat(2,minmax(0,1fr));} }
@media(max-width:850px){ #sariBranchTreasury .bt-filter-bar { grid-template-columns:minmax(0,1fr) minmax(140px,.45fr); } #sariBranchTreasury .bt-filter-bar .bt-button { width:100%; } #sariBranchTreasury .bt-history-table thead { display:none; } #sariBranchTreasury .bt-history-table,#sariBranchTreasury .bt-history-table tbody {display:block;} #sariBranchTreasury .bt-history-table tbody tr {display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); padding:11px 15px; border-top:1px solid #edf0f3;} #sariBranchTreasury .bt-history-table tbody td {display:block; padding:8px 6px; border-top:0;} #sariBranchTreasury .bt-history-table tbody td::before {content:attr(data-label); display:block; margin-bottom:4px; font-size:10px; color:#8793a3; font-weight:700; text-transform:uppercase;} #sariBranchTreasury .bt-history-table tbody td:last-child {grid-column:1 / -1;} }
@media(max-width:600px){ #sariBranchTreasury .bt-header {gap:12px;} #sariBranchTreasury .bt-header-tools {width:100%; justify-content:space-between;} #sariBranchTreasury .bt-page-title {font-size:26px;} #sariBranchTreasury .bt-stats {gap:9px;margin-bottom:22px;} #sariBranchTreasury .bt-stat {min-height:110px; padding:13px 12px; border-radius:14px;} #sariBranchTreasury .bt-stat-icon svg {width:25px;height:25px;} #sariBranchTreasury .bt-stat-label {font-size:11px;} #sariBranchTreasury .bt-stat-value {font-size:16px;letter-spacing:-.045em;} #sariBranchTreasury .bt-scanner {margin-bottom:23px;} #sariBranchTreasury .bt-scanner-actions .bt-button {width:100%;} #sariBranchTreasury .bt-scanner-actions {width:100%;} #sariBranchTreasury .bt-filter-bar {grid-template-columns:minmax(0,1fr) minmax(0,1fr) minmax(82px,.8fr);} #sariBranchTreasury .bt-filter-bar .bt-button {width:100%;padding:9px 6px;font-size:11px;} #sariBranchTreasury .bt-search-wrap {grid-column:1 / -1;} #sariBranchTreasury .bt-section-title {font-size:16px;} #sariBranchTreasury .bt-history-table tbody tr {grid-template-columns:1fr 1fr;} #sariBranchTreasury .bt-history-table tbody td {min-width:0;} #sariBranchTreasury .bt-history-table tbody td:nth-child(1), #sariBranchTreasury .bt-history-table tbody td:nth-child(2) {grid-column:1 / -1;} #sariBranchTreasury .bt-scanner-footer {flex-wrap:wrap;} #sariBranchTreasury .bt-scanner-footer .bt-button {width:100%;} #sariBranchTreasury .bt-help-panel { width:min(420px,calc(100vw - 28px)); } #sariBranchTreasury .bt-empty { min-height:230px; } #sariBranchTreasury .bt-empty h3 { font-size:20px; } }
@media(prefers-reduced-motion:reduce){#sariBranchTreasury .bt-help-panel,#sariBranchTreasury .bt-help-trigger,#sariBranchTreasury .bt-button {transition:none!important;} }

/* V2: Prevent generic shared Logistics section/card styles from creating
   a continuous white backing behind the KPI cards and camera button.
   The four individual .bt-stat cards intentionally retain their styling. */
#logisticsContent #sariBranchTreasury.sari-logistics-treasury > .bt-stats {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 13px !important;
    width: 100% !important;
    min-height: 0 !important;
    margin: 0 0 26px !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    background-image: none !important;
    box-shadow: none !important;
    outline: none !important;
}
#logisticsContent #sariBranchTreasury.sari-logistics-treasury > .bt-scanner {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    width: 100% !important;
    min-height: 42px !important;
    margin: 0 0 24px !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    background-image: none !important;
    box-shadow: none !important;
    outline: none !important;
}
/* When camera opens, the actual scanner still expands below the button. */
#logisticsContent #sariBranchTreasury.sari-logistics-treasury > .bt-scanner:has(#branchTreasuryScannerWrap:not(.hidden)) {
    display: block !important;
}
@media (max-width: 1150px) {
    #logisticsContent #sariBranchTreasury.sari-logistics-treasury > .bt-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}
@media (max-width: 600px) {
    #logisticsContent #sariBranchTreasury.sari-logistics-treasury > .bt-stats {
        gap: 9px !important;
        margin-bottom: 22px !important;
    }
    #logisticsContent #sariBranchTreasury.sari-logistics-treasury > .bt-scanner {
        margin-bottom: 23px !important;
    }
}
</style>
@endpush

@section('content')
<div id="sariBranchTreasury" class="sari-logistics-ui sari-logistics-treasury">
    @if(session('success'))
        <div class="bt-notice bt-notice-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bt-notice bt-notice-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <header class="bt-header">
        <div>
            <p class="bt-eyebrow">Physical Cash Custody</p>
            <h1 class="bt-page-title">{{ $facility->is_main_branch ? 'Main Branch Central Treasury' : 'Branch Remittance' }}</h1>
        </div>
        <div class="bt-header-tools">
            <span class="bt-branch-role">{{ $facility->is_main_branch ? 'Central Treasury' : 'Branch Custodian' }}</span>
            <div class="bt-help-anchor" id="btHelpAnchor">
                <button id="btHelpTrigger" class="bt-help-trigger" type="button" aria-label="Treasury instructions" title="How treasury works" aria-controls="btHelpPanel" aria-expanded="false">
                    <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.6 2.6 0 1 1 4.7 1.6c-1.3 1.4-2.2 1.7-2.2 3.1M12 17.5h.01"/></svg>
                </button>
                <aside id="btHelpPanel" class="bt-help-panel" role="region" aria-labelledby="btHelpHeading" aria-hidden="true" inert>
                    <div class="bt-help-top">
                        <div><p class="bt-eyebrow">Treasury Guide</p><h3 class="bt-help-title" id="btHelpHeading">Physical Cash Custody</h3></div>
                        <button id="btHelpClose" class="bt-help-close" type="button" aria-label="Close treasury guidance">×</button>
                    </div>
                    <div class="bt-help-copy">
                        <p>Operating Branch: <strong>{{ $facility->name }}</strong>.</p>
                        @if($facility->is_main_branch)
                            <p>This is the Main Branch. Scan incoming branch treasury QR codes, count the physical cash, and centrally reconcile exact batches before stakeholder wallets are released.</p>
                            <p>Scan only when the branch cash physically arrives. The QR opens the batch; it does not confirm receipt until the Main Branch counts the money.</p>
                        @else
                            <p>Verified Rider COD cash is now branch custody. Aggregate eligible Rider cash batches and physically send the full amount to <strong>{{ $mainBranch?->name ?: 'the Main Branch' }}</strong>. Wallets remain pending until the Main Branch verifies the full cash batch.</p>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </header>

    <div class="bt-stats" role="group" aria-label="Treasury overview">
        @foreach([
            [$facility->is_main_branch ? 'Incoming branch cash' : 'Cash ready for Main Branch', $facility->is_main_branch ? $stats['submitted'] : $stats['branch_cash_ready'], 'gold'],
            ['In transfer / awaiting receipt', $stats['submitted'], 'blue'],
            ['Centrally verified', $stats['verified'], 'green'],
            ['Open discrepancy', $stats['discrepancy'], 'rose'],
        ] as [$label,$value,$tone])
            <article class="bt-stat">
                <div style="min-width:0"><p class="bt-stat-label">{{ $label }}</p><p class="bt-stat-value">₱{{ number_format((float)$value, 2) }}</p></div>
                <span class="bt-stat-icon bt-tone-{{ $tone }}" aria-hidden="true">
                    @switch($tone)
                        @case('gold')
                            <svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M7 7V4h10v3M3 12h18M12 14h.01"/></svg>
                            @break
                        @case('blue')
                            <svg viewBox="0 0 24 24"><path d="M3 8h12v10H3zM15 11h3l3 3v4h-6"/><circle cx="7" cy="18" r="1.3"/><circle cx="18" cy="18" r="1.3"/></svg>
                            @break
                        @case('green')
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                            @break
                        @default
                            <svg viewBox="0 0 24 24"><path d="M12 3 2 21h20L12 3Z"/><path d="M12 9v5M12 18h.01"/></svg>
                    @endswitch
                </span>
            </article>
        @endforeach
    </div>

    @if(!$facility->is_main_branch)
        <section class="bt-card" aria-labelledby="btPrepareHeading">
            <div class="bt-card-head">
                <h2 class="bt-section-title" id="btPrepareHeading">Prepare Main Branch Remittance</h2>
                <span class="bt-count">{{ $eligibleRiderBatches->count() }} ELIGIBLE</span>
            </div>
            <form method="POST" action="{{ route('logistics.branch-treasury.store') }}" class="bt-form">
                @csrf
                <div class="bt-table-wrap">
                    <table class="bt-table">
                        <thead><tr><th scope="col">Select</th><th scope="col">Rider Batch</th><th scope="col">Rider</th><th scope="col">Parcels</th><th scope="col" style="text-align:right">Cash at Branch</th></tr></thead>
                        <tbody>
                            @forelse($eligibleRiderBatches as $riderBatch)
                                <tr>
                                    <td><input aria-label="Select batch {{ $riderBatch->batch_code }}" type="checkbox" name="rider_batch_ids[]" value="{{ $riderBatch->id }}" style="width:17px;height:17px;accent-color:#d9950b"></td>
                                    <td><strong class="bt-primary-text">{{ $riderBatch->batch_code }}</strong></td>
                                    <td>{{ trim(($riderBatch->courier?->first_name ?? '').' '.($riderBatch->courier?->last_name ?? '')) ?: 'Rider' }}</td>
                                    <td>{{ $riderBatch->items->count() }}</td>
                                    <td style="text-align:right"><span class="bt-money">₱{{ number_format((float)($riderBatch->confirmed_amount ?? $riderBatch->expected_amount),2) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5"><div class="bt-empty"><span class="bt-empty-icon" aria-hidden="true"><svg viewBox="0 0 64 64"><rect x="13" y="19" width="38" height="31" rx="6"/><path d="M13 28h38M22 19v-7h20v7M30 38h4"/></svg></span><h3>No verified Rider cash yet</h3><p>Eligible cash batches will appear here after Rider cash verification.</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bt-form-footer">
                    <label><span class="bt-field-label">Turnover Note (optional)</span><input name="branch_note" maxlength="1000" value="{{ old('branch_note') }}" class="bt-input" placeholder="Sealed bag, shift, or transport note"></label>
                    <button type="submit" {{ $eligibleRiderBatches->isEmpty() ? 'disabled' : '' }} class="bt-button bt-button-gold">Prepare Remittance</button>
                </div>
            </form>
        </section>
    @else
        <div class="bt-scanner" role="region" aria-label="Central Treasury QR scanner">
            <div class="bt-scanner-actions"><button id="startBranchTreasuryScanner" type="button" class="bt-button bt-button-charcoal">Open Camera Scanner</button></div>
            <div id="branchTreasuryScannerWrap" class="hidden">
                <div id="branchTreasuryQrReader"></div>
                <div class="bt-scanner-footer">
                    <p class="bt-scanner-status" id="branchTreasuryScannerStatus" aria-live="polite">Point the camera at the sending branch's SARI treasury QR.</p>
                    <button id="stopBranchTreasuryScanner" type="button" class="bt-button bt-button-plain">Close Camera</button>
                </div>
            </div>
        </div>
    @endif

    @if($batches->isNotEmpty())
    <div class="bt-filter-bar" aria-label="Filter treasury history">
        <label class="bt-search-wrap">
            <span class="sr-only">Search visible treasury batches</span>
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
            <input id="btSearch" type="search" class="bt-input" autocomplete="off" placeholder="Search batch, route, or cash amount...">
        </label>
        <label><span class="sr-only">Filter status</span><select id="btStatus" class="bt-select"><option value="all">All Status</option><option value="submitted">Submitted</option><option value="discrepancy">Discrepancy</option><option value="verified">Verified</option></select></label>
        <button id="btApply" type="button" class="bt-button bt-button-gold">Apply Filter</button>
        <button id="btReset" type="button" class="bt-button bt-button-plain">Reset</button>
    </div>

    @endif

    <section class="bt-card" aria-labelledby="btHistoryHeading">
        <div class="bt-card-head">
            <h2 id="btHistoryHeading" class="bt-section-title">{{ $facility->is_main_branch ? 'Incoming Branch Remittances' : 'Remittances Sent to Main Branch' }}</h2>
            <span class="bt-count">{{ $batches->count() }} BATCHES</span>
        </div>
        @if($batches->isNotEmpty())
            <div class="bt-table-wrap">
                <table class="bt-table bt-history-table">
                    <thead><tr><th scope="col">Batch</th><th scope="col">Route</th><th scope="col">Rider Batches</th><th scope="col">Expected Cash</th><th scope="col">Status</th><th scope="col" style="text-align:right">Action</th></tr></thead>
                    <tbody id="btHistoryBody">
                        @foreach($batches as $batch)
                            <tr data-bt-history-row data-state="{{ $batch->status }}" data-search="{{ $batch->batch_code }} {{ $batch->origin_name_snapshot }} {{ $batch->destination_name_snapshot }} {{ number_format((float)$batch->expected_amount,2) }}">
                                <td data-label="Batch"><span class="bt-primary-text">{{ $batch->batch_code }}</span><span class="bt-secondary-text">{{ $batch->created_at?->timezone('Asia/Manila')->format('M d, Y') ?: 'Date pending' }}</span></td>
                                <td data-label="Route"><span class="bt-primary-text">{{ $batch->origin_name_snapshot }} &gt; {{ $batch->destination_name_snapshot }}</span></td>
                                <td data-label="Rider Batches">{{ $batch->items->count() }}</td>
                                <td data-label="Expected Cash"><span class="bt-money">₱{{ number_format((float)$batch->expected_amount,2) }}</span></td>
                                <td data-label="Status"><span class="bt-status bt-status-{{ in_array($batch->status,['verified','discrepancy','submitted'],true) ? $batch->status : 'default' }}">{{ ucfirst(str_replace('_',' ',$batch->status)) }}</span></td>
                                <td data-label="Action" style="text-align:right">
                                    @if(!$facility->is_main_branch && in_array($batch->status,['submitted','discrepancy'],true))
                                        <div data-branch-qr-card>
                                            <button type="button" data-show-branch-qr class="bt-button bt-button-gold">Show QR</button>
                                            <div data-branch-qr-panel class="bt-qr-overlay hidden" role="dialog" aria-modal="true" aria-label="Branch remittance QR for {{ $batch->batch_code }}" tabindex="-1">
                                                <div class="bt-qr-dialog">
                                                    <div class="bt-qr-top"><strong>Branch Remittance QR</strong><button type="button" data-close-branch-qr aria-label="Close QR dialog">×</button></div>
                                                    <p class="bt-qr-help">Show this QR at the Main Branch when the physical cash arrives.</p>
                                                    <div class="bt-qr-box" data-branch-qr data-payload="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('logistics.branch-treasury.show', now()->addHours(24), ['batch' => $batch->id], false) }}"></div>
                                                    <strong class="bt-primary-text">{{ $batch->batch_code }}</strong>
                                                    <p class="bt-qr-help">₱{{ number_format((float)$batch->expected_amount,2) }} · {{ $batch->items->count() }} Rider batch(es)</p>
                                                    <p class="bt-qr-help">{{ $batch->origin_name_snapshot }} &gt; {{ $batch->destination_name_snapshot }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($facility->is_main_branch && in_array($batch->status,['submitted','discrepancy'],true))
                                        <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('logistics.branch-treasury.show', now()->addHours(24), ['batch' => $batch->id], false) }}" class="bt-button bt-button-gold">Count &amp; Reconcile</a>
                                    @else
                                        <span class="bt-secondary-text">₱{{ number_format((float)$batch->confirmed_amount,2) }} confirmed <span style="display:block">{{ $batch->verified_at?->format('M d, Y · h:i A') }}</span></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="btNoMatches" class="bt-empty" hidden>
                <span class="bt-empty-icon" aria-hidden="true"><svg viewBox="0 0 64 64"><circle cx="28" cy="28" r="16"/><path d="m40 40 15 15M21 28h14"/></svg></span>
                <h3>No matching remittances</h3><p>Try another batch code, route, or status.</p>
            </div>
            <footer class="bt-history-footer"><span id="btCount" aria-live="polite">Showing {{ $batches->count() }} of {{ $batches->count() }} batches on this page</span><span>Physical cash only</span></footer>
        @else
            <div class="bt-empty">
                <span class="bt-empty-icon" aria-hidden="true"><svg viewBox="0 0 64 64"><rect x="10" y="23" width="44" height="29" rx="5"/><path d="M14 23v-9h36v9M10 32h44M28 41h8"/></svg></span>
                <h3>No branch treasury remittances yet</h3><p>Incoming branch cash batches will appear here once prepared.</p>
            </div>
        @endif
    </section>
    <div class="bt-pagination">{{ $batches->links() }}</div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@if($facility->is_main_branch)
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-branch-qr-card]').forEach((card) => {
        const button = card.querySelector('[data-show-branch-qr]');
        const panel = card.querySelector('[data-branch-qr-panel]');
        const target = card.querySelector('[data-branch-qr]');
        let rendered = false;

        button?.addEventListener('click', () => {
            const opening = panel?.classList.contains('hidden');
            panel?.classList.toggle('hidden');
            button.textContent = opening ? 'Hide Branch Remittance QR' : 'Show Branch Remittance QR';

            if (opening && !rendered && target && typeof QRCode !== 'undefined') {
                new QRCode(target, {
                    text: new URL(target.dataset.payload, window.location.origin).toString(),
                    width: 204,
                    height: 204,
                    colorDark: '#171717',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M,
                });
                rendered = true;
            }
        });
    });

    @if($facility->is_main_branch)
    const startButton = document.getElementById('startBranchTreasuryScanner');
    const stopButton = document.getElementById('stopBranchTreasuryScanner');
    const wrap = document.getElementById('branchTreasuryScannerWrap');
    const status = document.getElementById('branchTreasuryScannerStatus');
    let scanner = null;
    let scanning = false;

    const stopScanner = async () => {
        if (scanner && scanning) {
            try { await scanner.stop(); } catch (_) {}
        }
        scanning = false;
        wrap?.classList.add('hidden');
    };

    startButton?.addEventListener('click', async () => {
        wrap?.classList.remove('hidden');
        if (typeof Html5Qrcode === 'undefined') {
            status.textContent = 'Camera scanner could not load. Reload and try again.';
            return;
        }

        status.textContent = 'Requesting camera permission…';
        try {
            scanner = scanner || new Html5Qrcode('branchTreasuryQrReader');
            await scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                async (decodedText) => {
                    try {
                        const target = new URL(decodedText, window.location.origin);
                        const valid = target.origin === window.location.origin
                            && /^\/logistics\/branch-treasury\/\d+$/.test(target.pathname)
                            && target.searchParams.has('signature')
                            && target.searchParams.has('expires');

                        if (!valid) {
                            status.textContent = 'Invalid SARI Branch Treasury QR.';
                            return;
                        }

                        status.textContent = 'Branch cash batch recognized. Opening count screen…';
                        await stopScanner();
                        window.location.assign(target.toString());
                    } catch (_) {
                        status.textContent = 'This QR is not a valid SARI Branch Treasury code.';
                    }
                },
                () => {}
            );
            scanning = true;
            status.textContent = 'Camera ready. Scan the sending branch treasury QR.';
        } catch (_) {
            scanning = false;
            status.textContent = 'Camera unavailable or permission denied. Open an incoming batch from the list instead.';
        }
    });

    stopButton?.addEventListener('click', stopScanner);
    @endif
});
</script>
@endpush

@push('scripts')
<script>
// Page-only interactions: treasury instructions, current-page filters and QR close controls.
document.addEventListener('DOMContentLoaded', () => {
    const anchor = document.getElementById('btHelpAnchor');
    const trigger = document.getElementById('btHelpTrigger');
    const panel = document.getElementById('btHelpPanel');
    const close = document.getElementById('btHelpClose');
    const setHelp = (open, restoreFocus = false) => {
        if (!trigger || !panel) return;
        panel.classList.toggle('is-open', open);
        panel.inert = !open;
        panel.setAttribute('aria-hidden', String(!open));
        trigger.setAttribute('aria-expanded', String(open));
        if (restoreFocus) trigger.focus({ preventScroll: true });
    };
    trigger?.addEventListener('click', () => setHelp(trigger.getAttribute('aria-expanded') !== 'true'));
    close?.addEventListener('click', () => setHelp(false, true));
    document.addEventListener('pointerdown', event => {
        if (anchor && trigger?.getAttribute('aria-expanded') === 'true' && !anchor.contains(event.target)) setHelp(false);
    });

    const rows = [...document.querySelectorAll('#sariBranchTreasury [data-bt-history-row]')];
    const search = document.getElementById('btSearch');
    const status = document.getElementById('btStatus');
    const noMatches = document.getElementById('btNoMatches');
    const count = document.getElementById('btCount');
    const normalize = value => String(value || '').toLocaleLowerCase().trim();
    const apply = () => {
        const q = normalize(search?.value);
        const selected = status?.value || 'all';
        let visible = 0;
        rows.forEach(row => {
            const match = (selected === 'all' || row.dataset.state === selected)
                && normalize(row.dataset.search).includes(q);
            row.hidden = !match;
            if (match) visible++;
        });
        if (noMatches) noMatches.hidden = visible > 0 || rows.length === 0;
        if (count) count.textContent = `Showing ${visible} of ${rows.length} batches on this page`;
    };
    document.getElementById('btApply')?.addEventListener('click', apply);
    search?.addEventListener('keydown', event => {
        if (event.key === 'Enter') { event.preventDefault(); apply(); }
    });
    document.getElementById('btReset')?.addEventListener('click', () => {
        if (search) search.value = '';
        if (status) status.value = 'all';
        apply();
    });

    const qrCards = [...document.querySelectorAll('#sariBranchTreasury [data-branch-qr-card]')];
    const closeQr = (card, focusBack = false) => {
        const qrPanel = card.querySelector('[data-branch-qr-panel]');
        const qrButton = card.querySelector('[data-show-branch-qr]');
        if (!qrPanel || qrPanel.classList.contains('hidden')) return;
        qrPanel.classList.add('hidden');
        if (qrButton) qrButton.textContent = 'Show QR';
        if (focusBack) qrButton?.focus({ preventScroll: true });
    };
    qrCards.forEach(card => {
        const qrPanel = card.querySelector('[data-branch-qr-panel]');
        card.querySelector('[data-close-branch-qr]')?.addEventListener('click', () => closeQr(card,true));
        qrPanel?.addEventListener('click', event => {
            if (event.target === qrPanel) closeQr(card,true);
        });
    });
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        if (trigger?.getAttribute('aria-expanded') === 'true') setHelp(false,true);
        const open = qrCards.find(card => !card.querySelector('[data-branch-qr-panel]')?.classList.contains('hidden'));
        if (open) closeQr(open,true);
    });
});
</script>
@endpush
