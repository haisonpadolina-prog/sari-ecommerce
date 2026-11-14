@extends('layouts.admin')
@section('title', 'Security Center — SARI Admin')
@section('page-title', 'Security Center')
@section('content')
<div class="sari-security mx-auto w-full max-w-[1500px] pb-8">
    <style>
        .sari-security{--gold:#D59617;--line:#DFE3E8;--ink:#202124;--muted:#6E7985;font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;color:var(--ink)}
        .sec-head{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;padding-bottom:15px;border-bottom:1px solid var(--line)}
        .sec-eye{margin:0;color:#717B87;font-size:8px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}
        .sec-title{margin:5px 0 0;font-size:clamp(29px,2.2vw,37px);font-weight:700;line-height:1.06;letter-spacing:-.045em}.sec-title span{color:var(--gold)}
        .sec-copy{max-width:830px;margin:7px 0 0;color:var(--muted);font-size:9.5px;line-height:1.6}
        .sec-live{display:inline-flex;min-height:34px;align-items:center;gap:7px;border:1px solid #d8e6dc;border-radius:999px;background:#f5faf6;padding:0 11px;color:#4f765d;font-size:7.5px;font-weight:800;white-space:nowrap}.sec-live i{width:6px;height:6px;border-radius:999px;background:#55a16b;box-shadow:0 0 0 4px rgba(85,161,107,.1)}
        .sec-flash{margin-top:12px;border:1px solid #d7e7dc;border-radius:8px;background:#f5faf6;padding:9px 11px;color:#51715d;font-size:8.5px;font-weight:650}
        .sec-metrics{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:9px;margin-top:14px}.sec-metric{border:1px solid var(--line);border-radius:8px;background:#fff;padding:12px 13px;box-shadow:0 1px 3px rgba(15,23,42,.025)}.sec-metric span{display:block;color:#77828e;font-size:6.7px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.sec-metric strong{display:block;margin-top:7px;font-size:19px;line-height:1;font-weight:750;color:#2a323b}.sec-metric.alert strong{color:#b64242}
        .sec-grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(360px,.85fr);gap:14px;margin-top:14px;align-items:start}.sec-stack{display:grid;gap:14px}
        .sec-panel{overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 3px 7px rgba(15,23,42,.035),0 14px 32px rgba(15,23,42,.055)}
        .sec-panel-head{display:flex;min-height:58px;align-items:center;justify-content:space-between;gap:12px;padding:11px 14px;border-bottom:1px solid #eceff2}.sec-panel-head h2{margin:0;font-size:11px;font-weight:750;color:#303844}.sec-panel-head p{margin:3px 0 0;font-size:7.8px;line-height:1.5;color:#7d8792}.sec-pill{display:inline-flex;min-height:23px;align-items:center;border:1px solid #e0e4e8;border-radius:999px;background:#f8f9fa;padding:0 8px;color:#68737e;font-size:6.6px;font-weight:800;white-space:nowrap}
        .sec-risk-list{display:grid;gap:8px;padding:12px}.sec-risk{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:10px;align-items:start;border:1px solid #eceff2;border-radius:8px;padding:10px;background:#fcfcfd}.sec-risk.critical{border-color:#efc1c1;background:#fff7f7}.sec-risk.high{border-color:#ecd3c1;background:#fff9f5}.sec-risk.medium{border-color:#eadfbe;background:#fffcf3}.sec-score{display:grid;width:42px;height:42px;place-items:center;border-radius:50%;background:#f1f3f5;color:#58636e;font-size:9px;font-weight:800}.critical .sec-score{background:#ffe7e7;color:#a83f3f}.high .sec-score{background:#fff0e6;color:#995a32}.medium .sec-score{background:#fff5d9;color:#8a6a23}.sec-risk strong{display:block;font-size:8.5px;color:#343e48}.sec-risk span{display:block;margin-top:3px;font-size:7px;line-height:1.5;color:#7a8590}.sec-risk-meta{margin-top:5px!important;color:#9a8870!important}.sec-risk-link{display:inline-flex;height:29px;align-items:center;border:1px solid #d7dde3;border-radius:7px;background:#fff;padding:0 9px;color:#56616c;font-size:6.8px;font-weight:800;text-decoration:none;white-space:nowrap}.sec-sev{margin-bottom:5px;border-radius:999px;padding:4px 7px;font-size:5.8px;font-weight:800;text-align:center;text-transform:uppercase}.sec-sev.critical{background:#ffe7e7;color:#a73d3d}.sec-sev.high{background:#fff0e5;color:#9c5a31}.sec-sev.medium{background:#fff5d9;color:#8a6822}
        .sec-table-wrap{overflow:auto}.sec-table{width:100%;min-width:760px;border-collapse:collapse}.sec-table th{padding:9px 11px;border-bottom:1px solid #eceff2;background:#fafbfc;color:#717b87;font-size:6.5px;font-weight:800;letter-spacing:.06em;text-align:left;text-transform:uppercase;white-space:nowrap}.sec-table td{padding:10px 11px;border-bottom:1px solid #f0f2f4;color:#46505d;font-size:7.5px;line-height:1.45;vertical-align:middle}.sec-table tr:last-child td{border-bottom:0}.sec-name{font-size:8.2px;font-weight:750;color:#303943}.sec-sub{margin-top:2px;font-size:6.5px;color:#7d8792}.sec-btn{display:inline-flex;min-height:30px;align-items:center;border:1px solid #e1c4c4;border-radius:7px;background:#fff5f5;padding:0 9px;color:#a14d4d;font-size:6.7px;font-weight:800;cursor:pointer}.sec-current{color:#4e7b5e;font-size:6.8px;font-weight:800}
        .sec-audit{display:grid}.sec-event{display:grid;grid-template-columns:120px minmax(0,1fr);gap:12px;padding:11px 13px;border-bottom:1px solid #f0f2f4}.sec-event:last-child{border-bottom:0}.sec-time{font-size:6.8px;line-height:1.5;color:#84909b}.sec-event strong{display:block;font-size:8px;color:#37414b}.sec-event p{margin:3px 0 0;font-size:6.9px;line-height:1.5;color:#7a8590}.sec-diff{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-top:7px}.sec-diff div{border:1px solid #e6eaee;border-radius:7px;background:#fafbfc;padding:7px}.sec-diff b{display:block;margin-bottom:4px;font-size:5.8px;letter-spacing:.05em;text-transform:uppercase;color:#7b8691}.sec-diff pre{margin:0;white-space:pre-wrap;word-break:break-word;font:600 6px/1.45 'Poppins',sans-serif;color:#53606b}.sec-empty{padding:24px 14px;text-align:center;color:#7d8792;font-size:7.5px}
        @media(max-width:1180px){.sec-metrics{grid-template-columns:repeat(3,minmax(0,1fr))}.sec-grid{grid-template-columns:1fr}}@media(max-width:640px){.sec-head{align-items:stretch;flex-direction:column}.sec-metrics{grid-template-columns:1fr 1fr}.sec-event{grid-template-columns:1fr}.sec-diff{grid-template-columns:1fr}}
    
/* Match the compact monitoring screens. */
.sari-security {--line:#E4E7EC;--ink:#101828;--muted:#667085;}
.sari-security .sec-head {align-items:center;}
.sari-security .sec-metrics {gap:12px;}
.sari-security .sec-metric {position:relative;min-width:0;min-height:86px;padding:15px 16px;border-radius:12px;box-shadow:0 2px 6px rgba(16,24,40,.04);}
.sari-security .sec-metric > span:first-child {padding-right:34px;color:#667085;font-size:10px;font-weight:600;line-height:1.35;text-transform:none;letter-spacing:normal;}
.sari-security .sec-metric strong {margin-top:5px;font-size:24px;line-height:1.15;letter-spacing:-.035em;}
.sari-security .sec-metric > .sec-metric-icon {position:absolute;top:14px;right:16px;display:inline-flex;width:25px;height:25px;padding:0;border:0;background:transparent;border-radius:0;box-shadow:none;pointer-events:none;}
.sari-security .sec-metric-icon svg {display:block;width:25px;height:25px;}
.sari-security .sec-grid,.sari-security .sec-stack {min-width:0;}
.sari-security .sec-panel {border-radius:12px;box-shadow:0 2px 6px rgba(16,24,40,.04);}
.sari-security .sec-panel-head {padding:14px 16px;}
.sari-security .sec-panel-head h2 {font-size:12px;font-weight:600;color:#252A32;}
.sari-security .sec-panel-head p {font-size:9px;line-height:1.6;color:#667085;}
.sari-security .sec-copy {font-size:10px;}
.sari-security .sec-live,.sari-security .sec-pill {font-size:9px;font-weight:600;}
.sari-security .sec-risk {background:#fff;border-color:#E4E7EC;padding:12px;border-radius:10px;}
.sari-security .sec-risk:hover {background:#F3F4F6;}
.sari-security .sec-risk strong {font-size:11px;font-weight:600;}
.sari-security .sec-risk span {font-size:9px;line-height:1.6;}
.sari-security .sec-risk-meta {color:#667085!important;}
.sari-security .sec-sev {font-size:8px;}
.sari-security .sec-risk-link {justify-content:center;min-height:34px;height:auto;padding:8px 12px;border:1px solid var(--gold);border-radius:8px;background:var(--gold);color:#fff;font-size:10px;font-weight:600;}
.sari-security .sec-risk-link:hover {background:#B97805;border-color:#B97805;color:#fff;}
.sari-security .sec-table th {padding:11px 14px;font-size:9px;font-weight:600;background:#F8FAFC;}
.sari-security .sec-table td {padding:12px 14px;font-size:10px;}
.sari-security .sec-table tbody tr:hover td {background:#F3F4F6;}
.sari-security .sec-name {font-size:10px;font-weight:600;}
.sari-security .sec-sub {font-size:9px;}
.sari-security .sec-btn {min-height:34px;justify-content:center;padding:8px 12px;border:1px solid #DC2626;border-radius:8px;background:#DC2626;color:#fff;font-size:10px;font-weight:600;white-space:nowrap;}
.sari-security .sec-btn:hover {background:#B91C1C;border-color:#B91C1C;}
.sari-security .sec-current {font-size:9px;}
.sari-security .sec-event {padding:14px 16px;grid-template-columns:110px minmax(0,1fr);}
.sari-security .sec-time {font-size:9px;line-height:1.6;}
.sari-security .sec-event strong {font-size:10px;font-weight:600;}
.sari-security .sec-event p {font-size:9px;line-height:1.6;}
.sari-security .sec-diff b {font-size:8px;}
.sari-security .sec-diff pre {font-size:9px;line-height:1.6;}
.sari-security .sec-empty {font-size:10px;line-height:1.6;}
.sari-security .sec-panel > div[style] {font-size:10px!important;color:#667085!important;line-height:1.7!important;padding:16px!important;}
.sari-security :is(.sec-risk-link,.sec-btn):focus-visible {outline:2px solid #475467;outline-offset:3px;}
@media(max-width:640px) {.sari-security .sec-panel-head {flex-wrap:wrap;} .sari-security .sec-event {grid-template-columns:1fr;gap:6px;} .sari-security .sec-risk {grid-template-columns:auto minmax(0,1fr);} .sari-security .sec-risk > div:last-child {grid-column:2;justify-self:start;} }

.sari-security > #active-sessions {margin-top:14px;}
.sari-security #active-sessions .sec-table {min-width:0;width:100%;}
.sari-security #active-sessions .sec-table td {overflow-wrap:anywhere;}
.sari-security #active-sessions .sec-table th {white-space:normal;}
.sari-security .sec-event[hidden] {display:none!important;}
.sari-security .sec-time {overflow-wrap:anywhere;}
.sari-security .sec-audit-toggle {display:flex;justify-content:center;align-items:center;min-height:34px;width:calc(100% - 32px);margin:12px 16px 16px;padding:8px 12px;border:1px solid #E4E7EC;border-radius:8px;background:#fff;color:#344054;font:600 10px/1.4 'Poppins',sans-serif;cursor:pointer;}
.sari-security .sec-audit-toggle:hover {background:#F3F4F6;border-color:#CBD2DB;}
.sari-security .sec-audit-toggle:focus-visible {outline:2px solid #475467;outline-offset:3px;}
@media(max-width:640px) {
    .sari-security #active-sessions .sec-table thead {display:none;}
    .sari-security #active-sessions .sec-table tbody {display:block;}
    .sari-security #active-sessions .sec-table tr {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));padding:12px;border-bottom:1px solid #ECEFF2;}
    .sari-security #active-sessions .sec-table td {display:block;min-width:0;padding:8px;border:0;}
    .sari-security #active-sessions .sec-table td::before {display:block;margin-bottom:4px;color:#667085;font-size:9px;font-weight:600;}
    .sari-security #active-sessions td:nth-child(1)::before {content:'Account';}
    .sari-security #active-sessions td:nth-child(2)::before {content:'Role';}
    .sari-security #active-sessions td:nth-child(3)::before {content:'Browser';}
    .sari-security #active-sessions td:nth-child(4)::before {content:'IP';}
    .sari-security #active-sessions td:nth-child(5)::before {content:'Last seen';}
    .sari-security #active-sessions td:nth-child(6)::before {content:'Session';}
    .sari-security #active-sessions td:last-child {grid-column:1/-1;}
}

        /* ========================================================
           SARI SECURITY CENTER — PURPOSE-FIRST WORKSPACE
           Scoped visual redesign; security routes/actions unchanged.
           ======================================================== */
        .sari-security {
            --sec-navy:#101828;--sec-sub:#475467;--sec-muted:#667085;
            --sec-stroke:#E5E7EB;--sec-cloud:#F9FAFB;
            max-width:1520px!important;padding-bottom:28px!important;
        }
        .sari-security .sec-head {align-items:center!important;min-height:80px;gap:18px;padding-bottom:18px!important;}
        .sari-security .sec-eye {font-size:10px!important;letter-spacing:.13em!important;color:#B97805!important;}
        .sari-security .sec-title {margin-top:5px!important;font-size:clamp(27px,2.15vw,32px)!important;line-height:1.1!important;}
        .sari-security .sec-copy {max-width:720px!important;margin-top:7px!important;font-size:11px!important;line-height:1.6!important;}
        .sari-security .sec-live {min-height:34px;padding:0 12px;gap:8px;font-size:10px!important;border-color:#D7E9DE;background:#F6FBF8;}
        .sari-security .sec-live i {width:7px;height:7px;}
        .sari-security .sec-flash {border-radius:10px;font-size:11px;padding:12px 14px;}
        .sari-security .sec-focus-toolbar {
            display:flex;justify-content:space-between;align-items:center;gap:20px;
            margin:18px 0 14px;padding:16px 18px;border:1px solid var(--sec-stroke);
            border-radius:16px;background:#fff;box-shadow:0 2px 8px rgba(16,24,40,.035);
        }
        .sari-security .sec-focus-intro {min-width:0;}
        .sari-security .sec-focus-eyebrow {display:block;color:#B97805;font-size:9px;font-weight:700;letter-spacing:.12em;}
        .sari-security .sec-focus-intro strong {display:block;margin-top:4px;font-size:17px;font-weight:700;line-height:1.3;color:var(--sec-navy);}
        .sari-security .sec-focus-intro p {margin:5px 0 0;max-width:700px;color:var(--sec-muted);font-size:11px;line-height:1.55;}
        .sari-security .sec-focus-field {width:300px;flex:0 0 300px;}
        .sari-security .sec-focus-field label {display:block;margin-bottom:7px;color:var(--sec-sub);font-size:10px;font-weight:600;}
        .sari-security .sec-focus-select-wrap {position:relative;}
        .sari-security .sec-focus-select-wrap select {
            width:100%;height:48px;appearance:none;-webkit-appearance:none;
            border:1px solid #D5DAE1;border-radius:12px;padding:0 43px 0 15px;
            background:#fff;color:#344054;font:600 12px 'Poppins',ui-sans-serif,system-ui,sans-serif;
            cursor:pointer;box-shadow:none;outline:0;
        }
        .sari-security .sec-focus-select-wrap select:hover {border-color:#B7C0CC;background:#fff;}
        .sari-security .sec-focus-select-wrap select:focus-visible {border-color:#C88E16;box-shadow:0 0 0 3px rgba(213,150,23,.10);}
        .sari-security .sec-focus-select-wrap svg {position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#667085;pointer-events:none;}
        .sari-security .sec-workspace[hidden] {display:none!important;}
        .sari-security .sec-workspace {min-width:0;}
        .sari-security .sec-metrics {grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:0;}
        .sari-security .sec-metric {min-height:110px!important;border-radius:16px!important;padding:20px!important;box-shadow:0 3px 10px rgba(16,24,40,.04)!important;}
        .sari-security .sec-metric > span:first-child {font-size:11px!important;}
        .sari-security .sec-metric strong {margin-top:12px!important;font-size:30px!important;}
        .sari-security .sec-metric > .sec-metric-icon {top:18px!important;right:18px!important;}
        .sari-security .sec-metric > .sec-metric-icon svg {width:23px!important;height:23px!important;}
        .sari-security .sec-quick-nav {margin-top:16px;border:1px solid var(--sec-stroke);border-radius:16px;background:#fff;overflow:hidden;}
        .sari-security .sec-quick-head {display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:8px;padding:17px 20px;border-bottom:1px solid #ECEFF2;}
        .sari-security .sec-quick-head strong {color:var(--sec-navy);font-size:15px;font-weight:700;}
        .sari-security .sec-quick-head span {color:var(--sec-muted);font-size:10px;}
        .sari-security .sec-quick-list {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));padding:7px 10px;column-gap:8px;}
        .sari-security .sec-quick-link {display:flex;min-height:54px;align-items:center;gap:10px;width:100%;border:0;border-bottom:1px solid #F2F4F7;border-radius:8px;padding:10px 11px;background:#fff;text-align:left;cursor:pointer;}
        .sari-security .sec-quick-link:hover {background:#F8FAFC;}
        .sari-security .sec-quick-link:focus-visible {outline:2px solid #B97805;outline-offset:-2px;}
        .sari-security .sec-quick-label {flex:1;min-width:0;color:#344054;font-size:12px;font-weight:600;}
        .sari-security .sec-quick-meta {color:#8A94A2;font-size:10px;white-space:nowrap;}
        .sari-security .sec-quick-link svg {flex:0 0 auto;color:#98A2B3;}
        .sari-security .sec-panel {overflow:hidden;border-color:var(--sec-stroke);border-radius:16px!important;box-shadow:0 2px 9px rgba(16,24,40,.04)!important;}
        .sari-security .sec-panel-head {min-height:86px;padding:19px 22px!important;gap:16px;}
        .sari-security .sec-panel-head h2 {font-size:18px!important;font-weight:700!important;line-height:1.3;color:var(--sec-navy)!important;}
        .sari-security .sec-panel-head p {max-width:950px;margin-top:5px!important;font-size:11px!important;line-height:1.6!important;color:var(--sec-muted)!important;}
        .sari-security .sec-pill {min-height:0;border:0!important;border-radius:0!important;background:transparent!important;padding:0!important;color:#8A94A2!important;font-size:11px!important;font-weight:600!important;}
        .sari-security .sec-risk-list {gap:10px;padding:16px 18px;}
        .sari-security .sec-risk {padding:15px;border-radius:12px;gap:13px;}
        .sari-security .sec-risk strong {font-size:12px!important;line-height:1.45;}
        .sari-security .sec-risk span {font-size:10.5px!important;line-height:1.6;}
        .sari-security .sec-risk-link {min-height:34px!important;font-size:10px!important;}
        .sari-security .sec-sev {font-size:9px!important;}
        .sari-security .sec-score {width:46px;height:46px;font-size:12px;}
        .sari-security .sec-table-wrap {overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none;}
        .sari-security .sec-table-wrap::-webkit-scrollbar {display:none;}
        .sari-security #active-sessions .sec-table {min-width:950px;}
        .sari-security .sec-table th {height:48px;padding:12px 16px!important;font-size:11px!important;letter-spacing:.055em;}
        .sari-security .sec-table td {padding:16px!important;font-size:11px!important;line-height:1.55;}
        .sari-security .sec-name {font-size:12px!important;font-weight:700!important;}
        .sari-security .sec-sub {font-size:10px!important;}
        .sari-security .sec-btn {min-height:36px!important;font-size:10px!important;}
        .sari-security .sec-event {grid-template-columns:170px minmax(0,1fr);gap:20px;padding:18px 22px;}
        .sari-security .sec-time {font-size:10px!important;}
        .sari-security .sec-event strong {font-size:12px!important;font-weight:700!important;}
        .sari-security .sec-event p {font-size:11px!important;}
        .sari-security .sec-diff {gap:12px;margin-top:12px;}
        .sari-security .sec-diff div {padding:12px;}
        .sari-security .sec-diff b {font-size:10px!important;}
        .sari-security .sec-diff pre {font-size:10px!important;white-space:pre-wrap;overflow-wrap:anywhere;}
        .sari-security .sec-audit-toggle {height:40px;font-size:11px;}
        .sari-security .sec-alert-copy {padding:22px!important;max-width:1000px;color:#475467;font-size:12px;line-height:1.85;}
        .sari-security .sec-empty {padding:60px 20px!important;color:#98A2B3;text-align:center;}
        .sari-security .sec-empty-icon {display:block;width:70px;height:70px;margin:0 auto 12px;color:#98A2B3;}
        .sari-security .sec-empty strong {display:block;color:#667085;font-size:21px;font-weight:700;line-height:1.35;}
        .sari-security .sec-empty p {margin:7px auto 0;max-width:560px;color:#98A2B3;font-size:13px;line-height:1.6;}
        @media (max-width:1000px) {
            .sari-security .sec-metrics {grid-template-columns:repeat(2,minmax(0,1fr));}
            .sari-security .sec-focus-field {width:260px;flex-basis:260px;}
        }
        @media (max-width:700px) {
            .sari-security .sec-head {align-items:flex-start!important;}
            .sari-security .sec-live {align-self:flex-start;}
            .sari-security .sec-focus-toolbar {flex-direction:column;align-items:stretch;padding:15px;gap:16px;}
            .sari-security .sec-focus-field {width:100%;flex:0 0 auto;}
            .sari-security .sec-quick-list {grid-template-columns:1fr;}
            .sari-security .sec-panel-head {padding:16px!important;}
            .sari-security .sec-panel-head h2 {font-size:16px!important;}
            .sari-security .sec-event {grid-template-columns:1fr;gap:8px;}
            .sari-security .sec-risk {grid-template-columns:auto minmax(0,1fr);}
            .sari-security .sec-risk > div:last-child {grid-column:2;justify-self:start;}
            .sari-security .sec-metric {min-height:102px!important;padding:15px!important;}
            .sari-security .sec-metric strong {font-size:25px!important;}
        }
        @media (max-width:420px) {.sari-security .sec-metrics {grid-template-columns:1fr;}}

        /* === LEAN SECURITY WORKSPACE / NEUTRAL SECTION FILTER === */
        .sari-security .sec-head {min-height:0!important;padding-bottom:15px!important;}
        .sari-security .sec-focus-toolbar {
            position:relative;z-index:20;display:flex!important;justify-content:flex-end!important;align-items:flex-end!important;
            margin:16px 0 16px!important;padding:0!important;gap:0!important;
            border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;
        }
        .sari-security .sec-focus-field {position:relative;width:300px;flex:0 1 300px;min-width:0;}
        .sari-security .sec-focus-field label {
            display:block!important;margin:0 0 7px!important;color:#667085!important;
            font-size:11px!important;font-weight:600!important;letter-spacing:0!important;
        }
        .sari-security .sec-focus-select-wrap {position:relative;isolation:isolate;}
        .sari-security .sec-focus-select-wrap select,
        .sari-security .sec-focus-select-wrap .sec-focus-trigger {
            width:100%;height:48px;border:1px solid #D8DEE6!important;border-radius:11px!important;
            background:#fff!important;color:#344054!important;
            font:600 12px/1.35 'Poppins',ui-sans-serif,system-ui,sans-serif!important;
            padding:0 42px 0 14px!important;box-shadow:none!important;
            transition:border-color .15s ease,background-color .15s ease,box-shadow .15s ease;
        }
        .sari-security .sec-focus-select-wrap select:hover,
        .sari-security .sec-focus-select-wrap .sec-focus-trigger:hover {border-color:#B8C1CB!important;background:#fff!important;}
        .sari-security .sec-focus-select-wrap select:focus-visible,
        .sari-security .sec-focus-select-wrap .sec-focus-trigger:focus-visible,
        .sari-security .sec-focus-select-wrap.is-open .sec-focus-trigger {
            outline:none!important;border-color:#9CA9B8!important;
            box-shadow:0 0 0 3px rgba(148,163,184,.16)!important;background:#fff!important;
        }
        .sari-security .sec-focus-native-chevron {position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#667085;pointer-events:none;}
        .sari-security .sec-focus-select-wrap.is-enhanced select {
            position:absolute!important;width:1px!important;height:1px!important;opacity:0!important;
            padding:0!important;margin:-1px!important;overflow:hidden!important;clip-path:inset(50%)!important;
            pointer-events:none!important;
        }
        .sari-security .sec-focus-select-wrap.is-enhanced .sec-focus-native-chevron {display:none!important;}
        .sari-security .sec-focus-trigger {display:flex;align-items:center;justify-content:space-between;gap:12px;text-align:left;cursor:pointer;}
        .sari-security .sec-focus-trigger[hidden], .sari-security .sec-focus-menu[hidden] {display:none!important;}
        .sari-security .sec-focus-trigger > span {flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
        .sari-security .sec-focus-trigger svg {width:15px;height:15px;flex:0 0 auto;color:#667085;transition:transform .16s ease;}
        .sari-security .sec-focus-select-wrap.is-open .sec-focus-trigger svg {transform:rotate(180deg);}
        .sari-security .sec-focus-menu {
            position:absolute;z-index:100;top:calc(100% + 7px);left:0;right:0;min-width:260px;
            border:1px solid #E2E7ED;border-radius:12px;background:#fff;padding:6px;
            box-shadow:0 14px 32px rgba(15,23,42,.12),0 3px 10px rgba(15,23,42,.04);
        }
        .sari-security .sec-focus-option {
            width:100%;min-height:41px;display:flex;justify-content:space-between;align-items:center;gap:9px;
            border:0;border-radius:8px;background:#fff;padding:8px 11px;color:#475467;
            font:500 11px/1.5 'Poppins',ui-sans-serif,system-ui,sans-serif;
            text-align:left;cursor:pointer;transition:background-color .12s ease,color .12s ease;
        }
        .sari-security .sec-focus-option:hover,
        .sari-security .sec-focus-option:focus-visible {outline:none!important;background:#F3F5F7!important;color:#1F2937!important;}
        .sari-security .sec-focus-option[aria-selected="true"] {background:#F7F8FA!important;color:#263445!important;font-weight:650!important;}
        .sari-security .sec-focus-option svg {width:15px;height:15px;color:#7C8796;opacity:0;flex:0 0 auto;}
        .sari-security .sec-focus-option[aria-selected="true"] svg {opacity:1;}
        /* Override the legacy broad "select-wrap svg" rule for inline menu icons. */
        .sari-security .sec-focus-trigger svg, .sari-security .sec-focus-option svg {
            position:static!important;right:auto!important;top:auto!important;transform:none;
        }
        .sari-security .sec-focus-select-wrap.is-open .sec-focus-trigger svg {transform:rotate(180deg)!important;}
        /* Per-workspace client-side page navigation: 10 loaded records per page. */
        .sari-security .sec-risk[hidden], .sari-security .sec-event[hidden],
        .sari-security .sec-table tbody tr[hidden], .sari-security .sec-pagination[hidden] {display:none!important;}
        .sari-security .sec-pagination {
            min-height:64px;display:flex;align-items:center;justify-content:space-between;gap:14px;
            padding:13px 20px;border-top:1px solid #ECEFF2;background:#fff;
        }
        .sari-security .sec-page-summary {color:#7C8796;font-size:11px;line-height:1.5;font-weight:500;white-space:nowrap;}
        .sari-security .sec-page-controls {display:flex;align-items:center;justify-content:flex-end;flex-wrap:wrap;gap:6px;}
        .sari-security .sec-page-btn {
            display:inline-flex;min-width:34px;height:34px;align-items:center;justify-content:center;
            padding:0 9px;border:1px solid #DEE3E9;border-radius:9px;background:#fff;
            color:#4B5563;font:600 11px/1.3 'Poppins',ui-sans-serif,system-ui,sans-serif;
            cursor:pointer;transition:background-color .12s,border-color .12s;
        }
        .sari-security .sec-page-btn:hover:not(:disabled) {background:#F3F5F7;border-color:#BAC3CE;}
        .sari-security .sec-page-btn:focus-visible {outline:2px solid #8795A7;outline-offset:2px;}
        .sari-security .sec-page-btn[aria-current="page"] {background:#263445!important;border-color:#263445!important;color:#fff!important;cursor:default;}
        .sari-security .sec-page-btn:disabled {color:#B3BBC6;border-color:#EEF0F3;cursor:not-allowed;}
        .sari-security .sec-page-ellipsis {color:#98A2B3;padding:0 2px;font-size:12px;}
        @media(max-width:700px) {
            .sari-security .sec-focus-toolbar {justify-content:stretch!important;margin:13px 0 14px!important;}
            .sari-security .sec-focus-field {width:100%!important;flex:1 1 auto!important;}
            .sari-security .sec-focus-menu {min-width:0;}
            .sari-security .sec-pagination {flex-direction:column;align-items:flex-start;padding:12px 14px;}
            .sari-security .sec-page-controls {justify-content:flex-start;}
        }
</style>
    <div class="sec-head">
        <div>
            <p class="sec-eye">Trust &amp; Safety</p>
            <h1 class="sec-title">Security <span>Center</span></h1>
        </div>
        <span class="sec-live"><i></i>15-second critical alert watch</span>
    </div>
    @if(session('success'))<div class="sec-flash">{{ session('success') }}</div>@endif

    {{-- Minimal unboxed section filter; native select is retained as a no-JS fallback. --}}
    <div class="sec-focus-toolbar" aria-label="Security section filter">
        <div class="sec-focus-field">
            <label for="securityFocusSelect">Security section</label>
            <div class="sec-focus-select-wrap" data-sec-custom-select>
                <select id="securityFocusSelect" aria-controls="security-view-overview security-view-risks security-view-sessions security-view-signins security-view-audit security-view-alerts">
                    <option value="overview">Security overview</option>
                    <option value="risks">Risk &amp; fraud flags</option>
                    <option value="sessions">Active account sessions</option>
                    <option value="signins">Failed sign-in watch</option>
                    <option value="audit">Audit timeline</option>
                    <option value="alerts">Alert behavior</option>
                </select>
                <svg class="sec-focus-native-chevron" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
                <button type="button" class="sec-focus-trigger" data-sec-focus-trigger aria-haspopup="listbox" aria-expanded="false" aria-controls="securityFocusMenu" hidden>
                    <span data-sec-focus-label>Security overview</span>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
                </button>
                <div class="sec-focus-menu" id="securityFocusMenu" role="listbox" aria-label="Security section" data-sec-focus-menu hidden>
                    <button type="button" role="option" class="sec-focus-option" data-sec-option="overview" aria-selected="true"><span>Security overview</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></button>
                    <button type="button" role="option" class="sec-focus-option" data-sec-option="risks" aria-selected="false"><span>Risk &amp; fraud flags</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></button>
                    <button type="button" role="option" class="sec-focus-option" data-sec-option="sessions" aria-selected="false"><span>Active account sessions</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></button>
                    <button type="button" role="option" class="sec-focus-option" data-sec-option="signins" aria-selected="false"><span>Failed sign-in watch</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></button>
                    <button type="button" role="option" class="sec-focus-option" data-sec-option="audit" aria-selected="false"><span>Audit timeline</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></button>
                    <button type="button" role="option" class="sec-focus-option" data-sec-option="alerts" aria-selected="false"><span>Alert behavior</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></button>
                </div>
            </div>
        </div>
    </div>
    <section class="sec-workspace" id="security-view-overview" data-sec-view="overview">
    <div class="sec-metrics">
        <div class="sec-metric alert"><span>Critical alerts</span><strong>{{ number_format((int)($metrics['critical'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 10 18H2L12 3ZM12 9v5M12 17h.01"/></svg></span></div>
        <div class="sec-metric"><span>High-risk signals</span><strong>{{ number_format((int)($metrics['high_risk'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#B7791F" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 5 8 9 8 9s8-4 8-9V6l-8-3ZM12 8v5M12 16h.01"/></svg></span></div>
        <div class="sec-metric"><span>Failed logins · 24h</span><strong>{{ number_format((int)($metrics['failed_logins_24h'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#AC7A16" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg></span></div>
        <div class="sec-metric"><span>Blocked accounts</span><strong>{{ number_format((int)($metrics['blocked'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#7C3AED" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="7" r="3"/><path d="M3 21a7 7 0 0 1 12-5M16 16l6 6M22 16l-6 6"/></svg></span></div>
        <div class="sec-metric"><span>Suspended / banned</span><strong>{{ number_format((int)($metrics['suspended'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m6 6 12 12"/></svg></span></div>
        <div class="sec-metric"><span>Active sessions</span><strong>{{ number_format((int)($metrics['active_sessions'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg></span></div>
    </div>

        <div class="sec-quick-nav" aria-label="Open a focused workspace">
            <div class="sec-quick-head"><strong>Explore a security area</strong><span>Select a section to view only its details</span></div>
            <div class="sec-quick-list">
                <button type="button" class="sec-quick-link" data-sec-open="risks"><span class="sec-quick-label">Risk & fraud</span><span class="sec-quick-meta">{{ number_format($risks->count()) }} flags</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
                <button type="button" class="sec-quick-link" data-sec-open="sessions"><span class="sec-quick-label">Active sessions</span><span class="sec-quick-meta">{{ number_format($sessions->count()) }} sessions</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
                <button type="button" class="sec-quick-link" data-sec-open="signins"><span class="sec-quick-label">Sign-in watch</span><span class="sec-quick-meta">{{ number_format($failedLogins->count()) }} attempts</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
                <button type="button" class="sec-quick-link" data-sec-open="audit"><span class="sec-quick-label">Audit trail</span><span class="sec-quick-meta">{{ number_format($audit->count()) }} events</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
                <button type="button" class="sec-quick-link" data-sec-open="alerts"><span class="sec-quick-label">Alert behavior</span><span class="sec-quick-meta">Alert rules & acknowledgements</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
            </div>
        </div>

    </section>
    <section class="sec-workspace" id="security-view-risks" data-sec-view="risks" hidden>
<section class="sec-panel" id="risk-signals">
                <div class="sec-panel-head"><div><h2>Smart risk &amp; fraud flags</h2><p>Failed-login bursts, cancellation patterns, repeated returns/refunds, Seller cancellation rate, and stalled order operations.</p></div><span class="sec-pill">{{ number_format($risks->count()) }} active</span></div>
                <div class="sec-risk-list">
                    @forelse($risks as $risk)
                        <article class="sec-risk {{ $risk['severity'] }}">
                            <div class="sec-score">{{ number_format((int)$risk['score']) }}</div>
                            <div><strong>{{ $risk['title'] }}</strong><span>{{ $risk['message'] }}</span><span class="sec-risk-meta">{{ $risk['category'] }} · automated indicator, Admin review required</span></div>
                            <div><div class="sec-sev {{ $risk['severity'] }}">{{ $risk['severity'] }}</div>@if($risk['url'])<a class="sec-risk-link" href="{{ $risk['url'] }}">Review</a>@endif</div>
                        </article>
                    @empty
                        <div class="sec-empty"><svg class="sec-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/><path d="M8.5 11h5"/></svg><strong>No active risk flags</strong><p>No active risk rules are currently above their review threshold.</p></div>
                    @endforelse
                </div>
                <nav class="sec-pagination" data-sec-pager="risks" aria-label="Risk signal pages" hidden>
                    <span class="sec-page-summary" data-sec-page-summary aria-live="polite"></span>
                    <div class="sec-page-controls" data-sec-page-controls></div>
                </nav>
            </section>
    </section>
    <section class="sec-workspace" id="security-view-sessions" data-sec-view="sessions" hidden>
<section class="sec-panel" id="active-sessions">
                <div class="sec-panel-head"><div><h2>Active account sessions</h2><p>Read directly from Laravel's database session store. Force Logout deletes the selected server session immediately.</p></div><span class="sec-pill">{{ number_format($sessions->count()) }} sessions</span></div>
                <div class="sec-table-wrap">
                    @if($sessions->count())
                        <table class="sec-table"><thead><tr><th>Account</th><th>Role</th><th>Browser</th><th>IP</th><th>Last seen</th><th>Session</th><th></th></tr></thead><tbody>
                        @foreach($sessions as $session)
                            <tr><td><div class="sec-name">{{ $session['name'] }}</div><div class="sec-sub">ID {{ $session['user_id'] }}</div></td><td>{{ $session['role_label'] }}</td><td>{{ $session['browser'] }}</td><td>{{ $session['ip'] }}</td><td>{{ $session['last_seen_at']->diffForHumans() }}</td><td>…{{ $session['session_suffix'] }}</td><td>
                                @if($session['current'])<span class="sec-current">Current session</span>@else
                                    <form method="POST" action="{{ route('admin.security.sessions.force-logout', $session['session_handle']) }}" onsubmit="return confirm('Force logout this active session?');">@csrf<button class="sec-btn" type="submit">Force Logout</button></form>
                                @endif
                            </td></tr>
                        @endforeach
                        </tbody></table>
                    @else<div class="sec-empty"><svg class="sec-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/><path d="M8.5 11h5"/></svg><strong>No active sessions found</strong><p>No parseable active SARI database sessions were found.</p></div>@endif
                </div>
                <nav class="sec-pagination" data-sec-pager="sessions" aria-label="Active session pages" hidden>
                    <span class="sec-page-summary" data-sec-page-summary aria-live="polite"></span>
                    <div class="sec-page-controls" data-sec-page-controls></div>
                </nav>
            </section>
    </section>
    <section class="sec-workspace" id="security-view-signins" data-sec-view="signins" hidden>
<section class="sec-panel" id="failed-logins">
                <div class="sec-panel-head"><div><h2>Failed sign-in watch</h2><p>Unsuccessful password sign-in submissions recorded by the security middleware.</p></div><span class="sec-pill">Latest {{ number_format($failedLogins->count()) }}</span></div>
                <div class="sec-table-wrap">
                    @if($failedLogins->count())
                        <table class="sec-table" style="min-width:620px"><thead><tr><th>Identity</th><th>IP</th><th>Browser</th><th>Time</th></tr></thead><tbody>
                        @foreach($failedLogins as $login)<tr><td><div class="sec-name">{{ $login['name'] }}</div><div class="sec-sub">{{ $login['email_masked'] }}</div></td><td>{{ $login['ip'] }}</td><td>{{ $login['browser'] }}</td><td>{{ $login['created_at']?->format('M d · h:i A') }}</td></tr>@endforeach
                        </tbody></table>
                    @else<div class="sec-empty"><svg class="sec-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/><path d="M8.5 11h5"/></svg><strong>No failed sign-ins</strong><p>No failed sign-in attempts have been recorded.</p></div>@endif
                </div>
                <nav class="sec-pagination" data-sec-pager="signins" aria-label="Failed sign-in pages" hidden>
                    <span class="sec-page-summary" data-sec-page-summary aria-live="polite"></span>
                    <div class="sec-page-controls" data-sec-page-controls></div>
                </nav>
            </section>
    </section>
    <section class="sec-workspace" id="security-view-audit" data-sec-view="audit" hidden>
<section class="sec-panel" id="audit-timeline">
                <div class="sec-panel-head"><div><h2>Audit timeline</h2><p>Reuses the existing centralized Admin activity table. State snapshots appear when the action records before/after values.</p></div><span class="sec-pill">Latest {{ number_format($audit->count()) }}</span></div>
                <div class="sec-audit">
                    @forelse($audit as $event)
                        <article class="sec-event"><div class="sec-time">{{ $event['created_at']?->format('M d, Y') }}<br>{{ $event['created_at']?->format('h:i:s A') }}<br>{{ $event['ip'] }}</div><div><strong>{{ $event['admin'] }} · {{ $event['action_label'] }}</strong><p>{{ $event['description'] }} · Subject: {{ $event['subject'] }}</p>
                            @if($event['before'] || $event['after'])<div class="sec-diff"><div><b>Before</b><pre>{{ json_encode($event['before'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></div><div><b>After</b><pre>{{ json_encode($event['after'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></div></div>@endif
                        </div></article>
                    @empty<div class="sec-empty"><svg class="sec-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.4-3.4"/><path d="M8.5 11h5"/></svg><strong>No audit records yet</strong><p>No administrator actions have been recorded yet.</p></div>@endforelse
                </div>
<nav class="sec-pagination" data-sec-pager="audit" aria-label="Admin audit trail pages" hidden>
                    <span class="sec-page-summary" data-sec-page-summary aria-live="polite"></span>
                    <div class="sec-page-controls" data-sec-page-controls></div>
                </nav>
            </section>
    </section>
    <section class="sec-workspace" id="security-view-alerts" data-sec-view="alerts" hidden>
<section class="sec-panel" id="security-alert-behavior">
                <div class="sec-panel-head"><div><h2>Alert behavior</h2><p>Critical alerts are checked throughout the Admin UI, not only on this page.</p></div></div>
                <div class="sec-alert-copy">A new unacknowledged critical alert triggers a red on-screen warning and an audible three-tone alarm. Browsers can block audio before the first user interaction; when that happens, the visual alert remains visible and sound starts after the Admin interacts with the page. Acknowledgement suppresses the same fingerprint for 12 hours while keeping it visible in this Security Center.</div>
            </section>
    </section>
</div>

<script>
(() => {
    const root = document.querySelector('.sari-security');
    const select = root?.querySelector('#securityFocusSelect');
    if (!root || !select) return;

    const views = Array.from(root.querySelectorAll('[data-sec-view]'));
    const keys = new Set(['overview','risks','sessions','signins','audit','alerts']);
    const anchorToView = {
        'risk-signals': 'risks', 'active-sessions': 'sessions',
        'failed-logins': 'signins', 'audit-timeline': 'audit',
        'security-alert-behavior': 'alerts'
    };
    const viewToAnchor = {
        overview:'security-view-overview', risks:'risk-signals',
        sessions:'active-sessions', signins:'failed-logins',
        audit:'audit-timeline', alerts:'security-alert-behavior'
    };

    const focusRoot = root.querySelector('[data-sec-custom-select]');
    const focusTrigger = focusRoot?.querySelector('[data-sec-focus-trigger]');
    const focusLabel = focusRoot?.querySelector('[data-sec-focus-label]');
    const focusMenu = focusRoot?.querySelector('[data-sec-focus-menu]');
    const focusOptions = Array.from(focusRoot?.querySelectorAll('[data-sec-option]') || []);

    function updateFocusLabel() {
        const nativeOption = Array.from(select.options).find(o => o.value === select.value);
        if (focusLabel) focusLabel.textContent = nativeOption?.textContent?.trim() || 'Security overview';
        focusOptions.forEach(option => {
            option.setAttribute('aria-selected', String(option.dataset.secOption === select.value));
        });
    }

    function closeFocus(returnFocus = false) {
        if (focusRoot) focusRoot.classList.remove('is-open');
        if (focusMenu) focusMenu.hidden = true;
        if (focusTrigger) focusTrigger.setAttribute('aria-expanded','false');
        if (returnFocus) focusTrigger?.focus({preventScroll:true});
    }

    function openFocus() {
        if (!focusMenu || !focusTrigger) return;
        focusMenu.hidden = false;
        focusRoot.classList.add('is-open');
        focusTrigger.setAttribute('aria-expanded','true');
        (focusOptions.find(o => o.dataset.secOption === select.value) || focusOptions[0])?.focus({preventScroll:true});
    }

    function openView(key, updateHash = false) {
        if (!keys.has(key)) key = 'overview';
        views.forEach(view => { view.hidden = view.dataset.secView !== key; });
        select.value = key;
        updateFocusLabel();
        closeFocus();
        if (updateHash && window.history?.replaceState) {
            try {
                const path = window.location.pathname + window.location.search;
                window.history.replaceState(window.history.state, '', path + '#' + encodeURIComponent(viewToAnchor[key]));
            } catch (_) { /* Non-HTTP test/document origins do not permit history updates. */ }
        }
    }

    function currentHashView() {
        let name = '';
        try { name = decodeURIComponent(window.location.hash.replace(/^#/,'')); }
        catch (_) { return 'overview'; }
        if (name.startsWith('security-view-')) {
            const key = name.slice('security-view-'.length);
            if (keys.has(key)) return key;
        }
        return anchorToView[name] || 'overview';
    }

    select.addEventListener('change', () => openView(select.value,true));

    if (focusRoot && focusTrigger && focusMenu && focusOptions.length) {
        // Progressive enhancement: keep the native select when JS is blocked.
        focusRoot.classList.add('is-enhanced');
        select.tabIndex = -1;
        select.setAttribute('aria-hidden','true');
        focusTrigger.hidden = false;
        updateFocusLabel();
        focusTrigger.addEventListener('click', () => {
            if (focusRoot.classList.contains('is-open')) closeFocus();
            else openFocus();
        });
        focusTrigger.addEventListener('keydown', event => {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();openFocus();
            }
        });
        focusOptions.forEach((option, index) => {
            option.addEventListener('click', () => {
                select.value = option.dataset.secOption;
                select.dispatchEvent(new Event('change',{bubbles:true}));
                closeFocus(true);
            });
            option.addEventListener('keydown', event => {
                let target = null;
                if (event.key === 'ArrowDown') target = (index + 1) % focusOptions.length;
                if (event.key === 'ArrowUp') target = (index - 1 + focusOptions.length) % focusOptions.length;
                if (event.key === 'Home') target = 0;
                if (event.key === 'End') target = focusOptions.length-1;
                if (target !== null) {event.preventDefault();focusOptions[target].focus();}
                if (event.key === 'Escape') {event.preventDefault();closeFocus(true);}
                if (event.key === 'Tab') closeFocus();
            });
        });
        document.addEventListener('pointerdown', event => {
            if (!focusRoot.contains(event.target)) closeFocus();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && focusRoot.classList.contains('is-open')) closeFocus(true);
        });
    }

    root.querySelectorAll('[data-sec-open]').forEach(button => {
        button.addEventListener('click', () => {
            openView(button.dataset.secOpen,true);
            (focusTrigger && !focusTrigger.hidden ? focusTrigger : select).focus({preventScroll:true});
        });
    });
    window.addEventListener('hashchange', () => openView(currentHashView()));

    // Pagination is scoped to each already-loaded collection. It does not
    // mutate audit records, server-backed session actions, or security policy.
    const pageSize = 10;
    const lists = {
        risks: '#risk-signals .sec-risk-list > .sec-risk',
        sessions: '#active-sessions .sec-table tbody > tr',
        signins: '#failed-logins .sec-table tbody > tr',
        audit: '#audit-timeline .sec-audit > .sec-event'
    };

    Object.entries(lists).forEach(([key, selector]) => {
        const rows = Array.from(root.querySelectorAll(selector));
        const nav = root.querySelector('[data-sec-pager="'+ key +'"]');
        const summary = nav?.querySelector('[data-sec-page-summary]');
        const controls = nav?.querySelector('[data-sec-page-controls]');
        if (!nav || !summary || !controls) return;
        const count = rows.length;
        const pages = Math.max(1, Math.ceil(count/pageSize));
        let currentPage = 1;

        function pageButton(label, page, disabled = false, active = false) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'sec-page-btn';
            btn.textContent = label;
            btn.disabled = disabled;
            if (active) btn.setAttribute('aria-current','page');
            if (!active && !disabled) btn.addEventListener('click', () => {
                currentPage = page;
                render();
            });
            return btn;
        }
        function ellipsis() {
            const span = document.createElement('span');
            span.className = 'sec-page-ellipsis';
            span.textContent = '…';
            span.setAttribute('aria-hidden','true');
            return span;
        }
        function render() {
            const first = (currentPage - 1)*pageSize;
            rows.forEach((row,i) => {row.hidden = i < first || i >= first + pageSize;});
            nav.hidden = count <= pageSize;
            if (nav.hidden) return;
            summary.textContent = `Showing ${first + 1}–${Math.min(first+pageSize,count)} of ${count} records`;
            controls.replaceChildren();
            controls.appendChild(pageButton('‹',currentPage-1,currentPage===1));
            const from = Math.max(1,Math.min(currentPage-2,pages-4));
            const to = Math.min(pages,Math.max(5,currentPage+2));
            if (from>1) {
                controls.appendChild(pageButton('1',1));
                if (from>2) controls.appendChild(ellipsis());
            }
            for (let p=from;p<=to;p++) controls.appendChild(pageButton(String(p),p,false,p===currentPage));
            if (to<pages) {
                if (to<pages-1) controls.appendChild(ellipsis());
                controls.appendChild(pageButton(String(pages),pages));
            }
            controls.appendChild(pageButton('›',currentPage+1,currentPage===pages));
        }
        render();
    });
    openView(currentHashView());
})();
</script>

@endsection
