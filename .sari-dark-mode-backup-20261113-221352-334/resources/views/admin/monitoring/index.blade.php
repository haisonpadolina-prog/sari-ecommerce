@extends('layouts.admin')
@section('title', 'Platform Monitoring — SARI Admin')
@section('page-title', 'Platform Monitoring')
@section('content')
@php
    $sections = [
        'sellers' => ['label' => 'Stores & Sellers', 'count' => $overview['sellers']],
        'logistics' => ['label' => 'Logistics Providers', 'count' => $overview['logistics']],
        'buyers' => ['label' => 'Buyers', 'count' => $overview['buyers']],
        'sla' => ['label' => 'Delivery SLA', 'count' => $slaSummary['breached']],
    ];
    $statusLabel = static fn ($value) => match (strtolower((string) $value)) {
        'deactivated' => 'Suspended',
        'suspended' => 'Timed suspension',
        'blocked' => 'Blocked',
        'banned' => 'Banned',
        'pending_activation' => 'Pending activation',
        default => ucfirst(str_replace('_', ' ', (string) ($value ?: 'active'))),
    };
@endphp
<div class="pm-page mx-auto w-full max-w-[1500px] pb-8">
    <style>
        .pm-page{--pm-gold:#D59617;--pm-gold-strong:#B97805;--pm-ink:#202124;--pm-muted:#697481;--pm-line:#DFE3E8;--pm-soft:#F7F8FA;font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;color:var(--pm-ink)}
        .pm-header{padding-bottom:15px;border-bottom:1px solid var(--pm-line)}
        .pm-eyebrow{margin:0;color:#717B87;font-size:8px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}
        .pm-title{margin:5px 0 0;font-size:clamp(29px,2.2vw,37px);font-weight:700;line-height:1.06;letter-spacing:-.045em}
        .pm-title span{color:var(--pm-gold)}
        .pm-copy{max-width:820px;margin:7px 0 0;color:var(--pm-muted);font-size:10px;line-height:1.65}
        .pm-metrics{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;margin-top:14px}
        .pm-metric{min-height:72px;border:1px solid var(--pm-line);border-radius:8px;background:#fff;padding:12px 13px;box-shadow:0 1px 3px rgba(15,23,42,.025),0 7px 18px rgba(15,23,42,.045)}
        .pm-metric span{display:block;color:#74808D;font-size:7.5px;font-weight:700;text-transform:uppercase;letter-spacing:.08em}
        .pm-metric strong{display:block;margin-top:7px;color:#2D333B;font-size:20px;line-height:1;font-weight:700}
        .pm-tabs{display:flex;gap:6px;margin-top:14px;overflow:auto;padding-bottom:2px}
        .pm-tab{display:inline-flex;min-height:36px;align-items:center;gap:7px;border:1px solid #DDE2E7;border-radius:8px;background:#fff;padding:0 11px;color:#606A75;font-size:8.8px;font-weight:700;text-decoration:none;white-space:nowrap}
        .pm-tab:hover{border-color:#D9BE83;background:#F3F4F6;color:#8F6418}
        .pm-tab.is-active{border-color:var(--pm-gold);background:var(--pm-gold);color:#fff}
        .pm-tab b{display:inline-flex;min-width:20px;height:20px;align-items:center;justify-content:center;border-radius:999px;background:rgba(255,255,255,.16);padding:0 6px;font-size:7px}
        .pm-filter{display:grid;grid-template-columns:minmax(280px,1fr) 180px auto auto;gap:9px;align-items:end;margin-top:14px}
        .pm-field label{display:block;margin-bottom:6px;color:#4B5563;font-size:8.8px;font-weight:750}
        .pm-control{width:100%;height:42px;border:1px solid #D6DCE2;border-radius:7px;background:#fff;padding:0 11px;color:#29313B;font:500 9.8px 'Poppins',sans-serif;outline:none}
        .pm-control:focus{border-color:var(--pm-gold);box-shadow:0 0 0 3px rgba(213,150,23,.08)}
        .pm-btn{display:inline-flex;height:42px;align-items:center;justify-content:center;border-radius:7px;padding:0 14px;font-size:8.8px;font-weight:750;text-decoration:none;white-space:nowrap}
        .pm-btn.primary{border:1px solid var(--pm-gold);background:var(--pm-gold);color:#fff}
        .pm-btn.primary:hover{background:var(--pm-gold-strong);border-color:var(--pm-gold-strong)}
        .pm-btn.secondary{border:1px solid #D6DCE2;background:#fff;color:#56606C}
        .pm-panel{margin-top:14px;overflow:hidden;border:1px solid var(--pm-line);border-radius:8px;background:#fff;box-shadow:0 3px 7px rgba(15,23,42,.04),0 14px 32px rgba(15,23,42,.075),0 30px 68px rgba(15,23,42,.07)}
        .pm-panel-head{display:flex;min-height:58px;align-items:center;justify-content:space-between;gap:12px;padding:11px 14px;border-bottom:1px solid #ECEFF2}
        .pm-panel-head h2{margin:0;color:#303844;font-size:11px;font-weight:750}
        .pm-panel-head p{margin:3px 0 0;color:#74808D;font-size:8.4px;line-height:1.55}
        .pm-count{display:inline-flex;min-height:22px;align-items:center;border:1px solid #E5E7EB;border-radius:999px;background:#F8F9FA;padding:0 7px;color:#6E675F;font-size:6.8px;font-weight:700}
        .pm-table-wrap{overflow:auto}
        .pm-table{width:100%;min-width:1050px;border-collapse:collapse}
        .pm-table th{padding:10px 12px;border-bottom:1px solid #ECEFF2;background:#FAFBFC;color:#717B87;font-size:7.5px;font-weight:800;letter-spacing:.06em;text-align:left;text-transform:uppercase;white-space:nowrap}
        .pm-table td{padding:12px;border-bottom:1px solid #F0F2F4;background:#fff;color:#46505D;font-size:8.6px;line-height:1.5;vertical-align:middle}
        .pm-table tbody tr:hover td{background:#F3F4F6}
        .pm-table tbody tr:last-child td{border-bottom:0}
        .pm-name{color:#28303A;font-size:9.2px;font-weight:750}
        .pm-sub{margin-top:2px;color:#7D8792;font-size:7.5px}
        .pm-status{display:inline-flex;min-height:23px;align-items:center;border:1px solid #DCE7DF;border-radius:999px;background:#F5F9F6;padding:0 8px;color:#567362;font-size:6.8px;font-weight:750;white-space:nowrap}
        .pm-status.deactivated,.pm-status.suspended{border-color:#E8DDC9;background:#FFF9EF;color:#8B6B32}
        .pm-status.blocked{border-color:#EAD7CB;background:#FFF6F1;color:#9A5E42}
        .pm-status.banned{border-color:#E7C9C9;background:#FFF3F3;color:#A24F4F}
        .pm-sla{display:inline-flex;min-height:23px;align-items:center;border:1px solid #DDE3E8;border-radius:999px;background:#F7F9FA;padding:0 8px;color:#68737E;font-size:6.8px;font-weight:800;white-space:nowrap}
        .pm-sla.warning{border-color:#E9D9AA;background:#FFFAEC;color:#8A6925}
        .pm-sla.high{border-color:#E8CFBC;background:#FFF7F1;color:#965B38}
        .pm-sla.critical{border-color:#E6C1C1;background:#FFF3F3;color:#A24747}
        .pm-sla.normal{border-color:#D8E5DC;background:#F5FAF6;color:#567462}
        .pm-view{display:inline-flex;height:31px;align-items:center;border:1px solid #DEC98E;border-radius:7px;background:#FFF9EC;padding:0 10px;color:#8D641C;font-size:7.5px;font-weight:750;text-decoration:none}
        .pm-pagination{padding:10px 14px;border-top:1px solid #ECEFF2;background:#FAFBFC}
        .pm-pagination nav{font-size:9px}
        .pm-empty{padding:40px 18px;color:#7D8792;font-size:9px;text-align:center}
        @media(max-width:1000px){.pm-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.pm-filter{grid-template-columns:1fr 1fr}.pm-filter .pm-field:first-child{grid-column:1/-1}}
        @media(max-width:639px){.pm-metrics{grid-template-columns:1fr 1fr}.pm-filter{grid-template-columns:1fr}.pm-filter .pm-field:first-child{grid-column:auto}.pm-btn{width:100%}}
    
.pm-page .pm-metrics { gap:12px; }
.pm-page .pm-metric {position:relative;min-height:86px;padding:15px 16px;border:1px solid #E4E7EC;border-radius:12px;background:#fff;box-shadow:0 2px 6px rgba(16,24,40,.04);}
.pm-page .pm-metric > span:first-child {padding-right:34px;color:#667085;font-size:10px;font-weight:600;line-height:1.35;letter-spacing:normal;text-transform:none;}
.pm-page .pm-metric strong {margin-top:5px;color:#101828;font-size:24px;line-height:1;letter-spacing:-.035em;}
.pm-page .pm-metric > .pm-metric-icon {position:absolute;top:14px;right:16px;display:inline-flex;width:25px;height:25px;padding:0;background:transparent;border:0;border-radius:0;box-shadow:none;pointer-events:none;}
.pm-page .pm-metric-icon svg {display:block;width:25px;height:25px;}
.pm-page .pm-view {justify-content:center;min-height:34px;height:auto;padding:8px 12px;border:1px solid var(--pm-gold);border-radius:8px;background:var(--pm-gold);color:#fff;font-size:10px;font-weight:600;line-height:1.4;text-align:center;text-decoration:none;}
.pm-page .pm-view:hover {background:var(--pm-gold-strong);border-color:var(--pm-gold-strong);color:#fff;}
.pm-page .pm-view:focus-visible {outline:2px solid var(--pm-gold-strong);outline-offset:3px;}
@media (min-width:1001px) and (max-width:1279px) {.pm-page .pm-metrics {grid-template-columns:repeat(3,minmax(0,1fr));}}

/* Monitoring section selection uses charcoal. */
.pm-page .pm-tab.is-active,
.pm-page .pm-tab.is-active:hover {
    background: #252A32;
    border-color: #252A32;
    color: #FFFFFF;
}
.pm-page .pm-tab:not(.is-active):hover {
    background: #F3F4F6;
    border-color: #CBD2DB;
    color: #252A32;
}
.pm-page .pm-tab.is-active b {
    background: rgba(255,255,255,.14);
    color: #FFFFFF;
}
.pm-page .pm-tab:focus-visible {
    outline: 2px solid #475467;
    outline-offset: 3px;
}

        /* SARI Seller Orders UI parity — monitoring only (no global overrides). */
        .pm-page{width:100%;max-width:1520px!important;padding-bottom:28px!important}
        .pm-header{position:relative;min-height:92px;padding:0 180px 15px 0!important;border-bottom:1px solid #E9EDF1!important}
        .pm-eyebrow{font-size:10.5px!important;font-weight:800!important;line-height:1.4!important;color:#A87316!important}
        .pm-title{margin-top:6px!important;font-size:clamp(27px,2.3vw,31px)!important;font-weight:760!important;line-height:1.08!important;color:#2E343B!important}
        .pm-title span{color:#C88912!important}
        .pm-copy{max-width:790px!important;margin-top:6px!important;font-size:11.5px!important;line-height:1.5!important;color:#8793A1!important}
        .pm-export{position:absolute;right:0;top:6px;display:inline-flex;align-items:center;justify-content:center;gap:8px;height:42px;padding:0 14px;border:1px solid #DCE2E8;border-radius:11px;background:#fff;color:#475569;font-size:11px;font-weight:700;text-decoration:none;white-space:nowrap}
        .pm-export svg{width:16px;height:16px}
        .pm-export:hover,.pm-export:focus-visible{background:#F8FAFC;border-color:#C88912;color:#94620A}
        .pm-metrics{grid-template-columns:repeat(5,minmax(0,1fr))!important;gap:14px!important;margin-top:18px!important}
        .pm-page .pm-metric{min-height:110px!important;padding:14px 16px!important;border:1px solid #E5E7EB!important;border-radius:18px!important;background:#fff!important;box-shadow:0 3px 10px rgba(15,23,42,.035),0 12px 30px rgba(15,23,42,.055)!important}
        .pm-page .pm-metric > span:first-child{color:#637081!important;font-size:13px!important;letter-spacing:0!important;text-transform:none!important;font-weight:650!important}
        .pm-page .pm-metric strong{margin-top:7px!important;color:#1F2937!important;font-size:clamp(25px,1.9vw,30px)!important;line-height:1.12!important}
        .pm-page .pm-metric > .pm-metric-icon{top:14px!important;right:16px!important;width:27px!important;height:27px!important}
        .pm-page .pm-metric-icon svg{width:27px!important;height:27px!important}
        .pm-tabs{gap:9px!important;margin-top:18px!important;padding-bottom:0!important;scrollbar-width:none}
        .pm-tabs::-webkit-scrollbar{display:none}
        .pm-tab{min-height:43px!important;gap:8px!important;border-radius:12px!important;padding:0 15px!important;font-size:11.5px!important}
        .pm-tab b{min-width:23px!important;height:23px!important;font-size:10px!important}
        .pm-tab:not(.is-active):hover{background:#F8FAFC!important;color:#344054!important;border-color:#CBD5E1!important}
        .pm-filter{grid-template-columns:minmax(0,1fr) minmax(185px,210px) auto auto!important;gap:12px!important;margin-top:20px!important;align-items:end!important}
        .pm-field{min-width:0}
        .pm-field label{margin-bottom:7px!important;color:#4B5563!important;font-size:11px!important;font-weight:700!important}
        .pm-control,.pm-select-trigger,.pm-btn{height:48px!important;min-height:48px!important;border-radius:14px!important;font-size:11.5px!important}
        .pm-control{border:1px solid #DCE2E8!important;padding:0 16px!important;color:#3F4B59!important;background:#fff!important;font-weight:500!important;box-shadow:none!important}
        .pm-control:hover,.pm-select-trigger:hover{background:#F8FAFC!important;border-color:#CBD5E1!important}
        .pm-control:focus{background:#fff!important;border-color:#C88912!important;box-shadow:0 0 0 3px rgba(200,137,18,.09)!important}
        .pm-btn{padding:0 18px!important;font-weight:700!important;box-shadow:none!important}
        .pm-btn.primary{background:#C88912!important;border-color:#C88912!important;font-size:12px!important}
        .pm-btn.primary:hover{background:#B47B0E!important;border-color:#B47B0E!important}
        .pm-btn.secondary{background:#fff!important;border-color:#DCE2E8!important;color:#5F6D7C!important;font-size:12px!important}
        .pm-btn.secondary:hover{background:#F8FAFC!important;border-color:#CBD5E1!important}
        .pm-select{position:relative;width:100%}
        .pm-native-select{display:block}
        .pm-select-trigger{display:none;width:100%;align-items:center;justify-content:space-between;gap:8px;border:1px solid #DCE2E8;background:#fff;padding:0 15px;color:#344054;font:600 11.5px Poppins,system-ui,sans-serif;text-align:left;cursor:pointer}
        .pm-select-trigger svg{width:17px;height:17px;color:#667085}
        .pm-select.is-enhanced .pm-native-select{position:absolute!important;width:1px!important;height:1px!important;min-height:1px!important;overflow:hidden!important;clip-path:inset(50%)!important;opacity:0!important;pointer-events:none!important}
        .pm-select.is-enhanced .pm-select-trigger{display:flex}
        .pm-select-trigger[aria-expanded="true"]{border-color:#A0AAB6!important;box-shadow:0 0 0 3px rgba(148,163,184,.10)!important}
        .pm-select-menu{position:absolute;z-index:90;top:calc(100% + 8px);left:0;right:0;padding:7px;border:1px solid #E2E8F0;border-radius:14px;background:#fff;box-shadow:0 16px 32px rgba(15,23,42,.12),0 2px 6px rgba(15,23,42,.05)}
        .pm-select-menu[hidden]{display:none!important}
        .pm-select-option{display:flex;width:100%;min-height:38px;align-items:center;justify-content:space-between;border:0;border-radius:9px;padding:0 12px;background:#fff;color:#475569;font:600 11.5px Poppins,system-ui,sans-serif;cursor:pointer;text-align:left}
        .pm-select-option:hover,.pm-select-option:focus-visible,.pm-select-option[aria-selected="true"]{outline:none!important;background:#F1F5F9!important;color:#344054!important}
        .pm-select-option[aria-selected="true"]:after{content:'✓';color:#667085;font-size:13px}
        .pm-panel{margin-top:20px!important;border:1px solid #E5E7EB!important;border-radius:18px!important;box-shadow:0 3px 10px rgba(15,23,42,.035),0 12px 30px rgba(15,23,42,.055)!important}
        .pm-panel-head{min-height:78px!important;padding:21px 23px!important;border-color:#E9EDF1!important;background:#fff!important}
        .pm-panel-head h2{font-size:18px!important;font-weight:780!important;color:#2F3742!important}
        .pm-panel-head p{margin-top:4px!important;font-size:11.5px!important;color:#8B96A4!important}
        .pm-count{min-height:28px!important;padding:0 10px!important;border-color:#E5E7EB!important;background:#F8FAFC!important;color:#667085!important;font-size:11px!important}
        .pm-table-wrap{width:100%;overflow-x:auto!important;overflow-y:hidden!important;scrollbar-width:none!important}
        .pm-table-wrap::-webkit-scrollbar{display:none!important}
        .pm-table{width:100%;min-width:1060px!important}
        .pm-table th{height:48px!important;padding:13px 13px!important;background:#F8FAFC!important;color:#8591A0!important;font-size:11px!important;letter-spacing:.065em!important}
        .pm-table td{padding:17px 13px!important;background:#fff!important;color:#475467!important;font-size:11.5px!important;line-height:1.5!important}
        .pm-table tbody tr:hover td{background:#F8FAFC!important}
        .pm-name{color:#293440!important;font-size:12.5px!important;font-weight:750!important;line-height:1.4!important}
        .pm-sub{margin-top:3px!important;color:#8793A1!important;font-size:10.5px!important;line-height:1.45!important}
        .pm-status,.pm-sla{min-height:27px!important;padding:4px 10px!important;font-size:10px!important}
        .pm-view{min-height:34px!important;padding:8px 12px!important;border-radius:10px!important;background:#fff!important;border:1px solid #DCE2E8!important;color:#475569!important;font-size:10.5px!important}
        .pm-view:hover{border-color:#C88912!important;background:#F8FAFC!important;color:#94620A!important}
        .pm-check-cell{width:50px!important;min-width:50px!important;text-align:center!important;padding-left:17px!important;padding-right:7px!important}
        .pm-check-wrap{display:inline-flex;min-width:28px;min-height:32px;align-items:center;justify-content:center;cursor:pointer}
        .pm-check-wrap input[type=checkbox]{appearance:none;-webkit-appearance:none;display:grid;place-items:center;width:18px;height:18px;border:1.5px solid #CBD5E1;border-radius:5px;background:#fff;cursor:pointer}
        .pm-check-wrap input[type=checkbox]:checked,.pm-check-wrap input[type=checkbox]:indeterminate{background:#C88912;border-color:#C88912}
        .pm-check-wrap input[type=checkbox]:checked:after{content:'';width:5px;height:9px;border:solid white;border-width:0 2px 2px 0;transform:rotate(45deg) translateY(-1px)}
        .pm-check-wrap input[type=checkbox]:indeterminate:after{content:'';width:9px;height:2px;background:#fff;border-radius:2px}
        .pm-check-wrap input[type=checkbox]:focus-visible{outline:2px solid #C88912;outline-offset:3px}
        .pm-selection{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 19px;background:#F8FAFC;border-bottom:1px solid #E5E7EB;font-size:11.5px;color:#465362}
        .pm-selection[hidden]{display:none!important}
        .pm-selection small{font-size:10px;color:#8591A0}
        .pm-selection button{min-height:32px;border:1px solid #DCE2E8;border-radius:9px;padding:0 12px;background:#fff;color:#475569;font-weight:650;cursor:pointer}
        .pm-selection button:hover{background:#F1F5F9}
        .pm-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:76px 18px!important;background:#fff!important;color:#8793A1!important}
        .pm-empty svg{width:70px;height:70px;color:#98A2B3}
        .pm-empty strong{margin-top:15px;color:#667085;font-size:22px;font-weight:700;line-height:1.3}
        .pm-empty p{margin:8px 0 0;color:#98A2B3;font-size:13px;line-height:1.6;text-align:center}
        .pm-pagination{display:flex;min-height:64px;align-items:center;justify-content:space-between;gap:15px;padding:13px 20px!important;border-color:#E9EDF1!important;background:#fff!important}
        .pm-pagination-summary{color:#7B8794;font-size:11px;font-weight:500}
        .pm-pages{display:flex;align-items:center;flex-wrap:wrap;gap:6px}
        .pm-page-arrow,.pm-page-number,.pm-page-ellipsis{display:grid;place-items:center;min-width:34px;height:34px;border:1px solid #E5E7EB;border-radius:10px;background:#fff;color:#667085;font-size:11.5px;font-weight:700;text-decoration:none}
        .pm-page-arrow svg{width:15px;height:15px}
        .pm-page-number.is-current{background:#C88912!important;border-color:#C88912!important;color:white!important}
        .pm-page-arrow.is-disabled{opacity:.42}
        .pm-page-ellipsis{border-color:transparent}
        .pm-pages a:hover,.pm-pages a:focus-visible{outline:0;border-color:#C88912;background:#F8FAFC;color:#94620A}
        @media(max-width:1180px){.pm-metrics{grid-template-columns:repeat(2,minmax(0,1fr))!important}}
        @media(max-width:900px){.pm-filter{grid-template-columns:repeat(2,minmax(0,1fr))!important}.pm-filter .pm-field{grid-column:1/-1}.pm-filter .pm-btn{width:100%}}
        @media(max-width:640px){.pm-header{padding-right:0!important;padding-bottom:66px!important}.pm-export{top:auto;left:0;right:auto;bottom:13px}.pm-metrics{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:9px!important}.pm-page .pm-metric{min-height:94px!important;padding:12px!important}.pm-page .pm-metric>span:first-child{font-size:11.5px!important}.pm-page .pm-metric strong{font-size:24px!important}.pm-tabs{overflow-x:auto!important;flex-wrap:nowrap}.pm-filter{grid-template-columns:1fr!important}.pm-filter .pm-field{grid-column:auto!important}.pm-panel-head{align-items:flex-start!important;flex-direction:column!important;gap:8px!important;padding:17px 16px!important}.pm-pagination{align-items:flex-start;flex-direction:column}.pm-pages{align-self:flex-end}.pm-empty strong{font-size:20px}.pm-empty p{font-size:12px}}


        /* Section filter replaces four navigation tabs; same neutral/white controls as Account status. */
        .pm-page .pm-header { min-height:72px!important; }
        .pm-page .pm-filter { grid-template-columns:minmax(210px,1fr) minmax(195px,245px) minmax(180px,210px) auto auto!important; gap:12px!important; }
        .pm-page .pm-section-field .pm-select-trigger [data-pm-label] { min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .pm-page .pm-section-field .pm-select-menu { max-height:340px; overflow-y:auto; scrollbar-width:thin; }
        .pm-page .pm-section-field .pm-select-option { gap:10px; }
        .pm-page .pm-section-field .pm-section-count { margin-left:auto; color:#98A2B3; font-size:10px; font-weight:700; }
        .pm-page .pm-section-field .pm-select-option[aria-selected="true"] .pm-section-count { color:#667085; }
        .pm-page .pm-section-field .pm-select-option[aria-selected="true"]:after { margin-left:0; }
        @media(max-width:1150px) {
            .pm-page .pm-filter { grid-template-columns:repeat(2,minmax(0,1fr))!important; }
            .pm-page .pm-filter .pm-field { grid-column:auto!important; }
            .pm-page .pm-filter .pm-search-field { grid-column:1/-1!important; }
            .pm-page .pm-filter .pm-btn { width:100%!important; }
        }
        @media(max-width:640px) {
            .pm-page .pm-filter { grid-template-columns:minmax(0,1fr)!important; gap:10px!important; }
            .pm-page .pm-filter .pm-search-field { grid-column:auto!important; }
        }

        /* Monitoring record total: plain supporting text, no pill/container. */
        .pm-page .pm-panel-head .pm-record-count {
            display: inline;
            align-self: center;
            min-height: 0;
            margin: 0;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            color: #7B8794;
            font-size: 11.5px;
            font-weight: 500;
            line-height: 1.5;
            white-space: nowrap;
        }
        @media (max-width: 640px) {
            .pm-page .pm-panel-head .pm-record-count {
                align-self: flex-start;
                font-size: 11px;
            }
        }
</style>
    <div class="pm-header">
        <p class="pm-eyebrow">Operations & Account Oversight</p>
        <h1 class="pm-title">Platform <span>Monitoring</span></h1>
        <a class="pm-export" href="{{ route('admin.monitoring.export-pdf', ['section' => $section, 'q' => $search, 'status' => $status]) }}" aria-label="Export filtered monitoring records as PDF">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 3h7l4 4v14H7zM14 3v5h4M9 13h6M9 17h6"/></svg>
            Export PDF
        </a>
    </div>
    @if(session('success'))
        <div class="mt-3 rounded-[8px] border border-[#d7e7dc] bg-[#f5faf6] px-3 py-2 text-[9px] font-semibold text-[#51715d]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mt-3 rounded-[8px] border border-[#ead2cc] bg-[#fff6f3] px-3 py-2 text-[9px] text-[#9a5a49]">{{ $errors->first() }}</div>
    @endif
    <div class="pm-metrics">
        <div class="pm-metric"><span>Stores</span><strong>{{ number_format($overview['sellers']) }}</strong><span class="pm-metric-icon" style="color:#C47522" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18l-2-5H5L3 9ZM5 9v11h14V9M9 20v-6h6v6"/></svg></span></div>
        <div class="pm-metric"><span>Logistics</span><strong>{{ number_format($overview['logistics']) }}</strong><span class="pm-metric-icon" style="color:#7C3AED" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v12H3zM14 10h4l3 3v5h-7"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg></span></div>
        <div class="pm-metric"><span>Buyers</span><strong>{{ number_format($overview['buyers']) }}</strong><span class="pm-metric-icon" style="color:#2563EB" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.5"/><path d="M5 21a7 7 0 0 1 14 0"/></svg></span></div>
        <div class="pm-metric"><span>Active deliveries</span><strong>{{ number_format($overview['active_deliveries']) }}</strong><span class="pm-metric-icon" style="color:#AC7A16" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v10l9 4 9-4V8M12 13v9"/></svg></span></div>
        <div class="pm-metric"><span>SLA breached</span><strong>{{ number_format($slaSummary['breached']) }}</strong><span class="pm-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 10 18H2L12 3ZM12 9v5M12 17h.01"/></svg></span></div>
    </div>
    <form method="GET" action="{{ route('admin.monitoring') }}" class="pm-filter">
        <div class="pm-field pm-search-field">
            <label for="monitorSearch">Search</label>
            <input
                id="monitorSearch"
                class="pm-control"
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="{{ $section === 'sla' ? 'Order, Seller, Logistics, Rider...' : 'Name, email, store, business, plate, location...' }}"
            >
        </div>
        <div class="pm-field pm-section-field">
            <label for="monitorSection">Monitoring section</label>
            <div class="pm-select" data-pm-select>
                <select id="monitorSection" name="section" class="pm-control pm-native-select" data-pm-native>
                    @foreach($sections as $key => $meta)
                        <option value="{{ $key }}" @selected($section === $key)>{{ $meta['label'] }} ({{ number_format($meta['count']) }})</option>
                    @endforeach
                </select>
                <button type="button" class="pm-select-trigger" data-pm-trigger aria-controls="pmSectionOptions" aria-expanded="false" aria-haspopup="listbox" aria-label="Monitoring section filter">
                    <span data-pm-label>{{ $sections[$section]['label'] }} ({{ number_format($sections[$section]['count']) }})</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
                </button>
                <div id="pmSectionOptions" class="pm-select-menu" data-pm-menu role="listbox" aria-label="Monitoring sections" hidden>
                    @foreach($sections as $key => $meta)
                        <button type="button" role="option" class="pm-select-option" data-pm-option="{{ $key }}" aria-selected="{{ $section === $key ? 'true' : 'false' }}">
                            <span>{{ $meta['label'] }}</span><span class="pm-section-count">{{ number_format($meta['count']) }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        @if($section !== 'sla')
            <div class="pm-field">
                <label for="monitorStatus">Account status</label>
                <div class="pm-select" data-pm-select>
                    <select id="monitorStatus" class="pm-control pm-native-select" name="status" data-pm-native>
                        <option value="all" @selected($status === 'all')>All statuses</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="deactivated" @selected($status === 'deactivated')>Suspended</option>
                        <option value="blocked" @selected($status === 'blocked')>Blocked</option>
                        <option value="banned" @selected($status === 'banned')>Banned</option>
                    </select>
                    <button type="button" class="pm-select-trigger" data-pm-trigger aria-expanded="false" aria-haspopup="listbox" aria-label="Account status filter">
                        <span data-pm-label>{{ ['all' => 'All statuses', 'active' => 'Active', 'deactivated' => 'Suspended', 'blocked' => 'Blocked', 'banned' => 'Banned'][$status] ?? 'All statuses' }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
                    </button>
                    <div class="pm-select-menu" data-pm-menu role="listbox" aria-label="Account status options" hidden>
                        @foreach(['all'=>'All statuses', 'active'=>'Active', 'deactivated'=>'Suspended', 'blocked'=>'Blocked', 'banned'=>'Banned'] as $value=>$label)
                            <button type="button" role="option" class="pm-select-option" data-pm-option="{{ $value }}" aria-selected="{{ $status === $value ? 'true' : 'false' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="pm-field">
                <label>SLA summary</label>
                <div class="pm-control" style="display:flex;align-items:center;gap:8px">
                    <span>{{ number_format($slaSummary['critical']) }} critical</span>
                    <span>·</span>
                    <span>{{ number_format($slaSummary['warning']) }} nearing breach</span>
                </div>
            </div>
        @endif
        <button class="pm-btn primary" type="submit">Apply</button>
        <a class="pm-btn secondary" href="{{ route('admin.monitoring', ['section' => $section]) }}">Reset</a>
    </form>
    <section class="pm-panel">
        <div class="pm-panel-head">
            <div>
                <h2>{{ $sections[$section]['label'] }}</h2>
                @if($section === 'sla')
                    <p>One SLA engine is shared with Security Center. Review active delivery stages, thresholds, elapsed time, and breaches here.</p>
                @endif
            </div>
            <span class="pm-record-count">
                {{ number_format($section === 'sla' ? $records->count() : $records->total()) }}
                {{ $section === 'sla' ? 'active orders' : 'records' }}
            </span>
        </div>
        <div class="pm-selection" data-pm-selection hidden aria-live="polite">
            <span><strong data-pm-selected-count>0</strong> selected on this page <small>· Read-only review selection</small></span>
            <button type="button" data-pm-clear>Clear selection</button>
        </div>
        <div class="pm-table-wrap">
            @if($records->count())
                <table class="pm-table">
                    @if($section === 'sla')
                        <thead><tr><th class="pm-check-cell"><label class="pm-check-wrap"><input type="checkbox" data-pm-all aria-label="Select all visible monitoring records"></label></th><th>Order</th><th>Stage</th><th>Elapsed</th><th>SLA</th><th>Seller</th><th>Logistics</th><th>Rider</th><th>Stage started</th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            <tr data-pm-record-row>
                                <td class="pm-check-cell"><label class="pm-check-wrap"><input type="checkbox" data-pm-check aria-label="Select order {{ $record['order_number'] }}"></label></td>
                                <td>
                                    <div class="pm-name">{{ $record['order_number'] }}</div>
                                    <div class="pm-sub">Last update {{ $record['last_updated_at']?->diffForHumans() ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="pm-name">{{ $record['status_label'] }}</div>
                                    <div class="pm-sub">{{ $record['threshold_hours'] }}h expected window</div>
                                </td>
                                <td>
                                    <div class="pm-name">{{ number_format((float)$record['elapsed_hours'],1) }}h</div>
                                    <div class="pm-sub">
                                        @if($record['is_breached'])
                                            {{ number_format((float)$record['overdue_hours'],1) }}h overdue
                                        @else
                                            {{ number_format(max(0,(float)$record['threshold_hours']-(float)$record['elapsed_hours']),1) }}h remaining
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="pm-sla {{ $record['severity'] }}">
                                        {{ $record['is_breached'] ? strtoupper($record['severity']).' BREACH' : ($record['severity']==='warning' ? 'WARNING' : 'ON TRACK') }}
                                    </span>
                                </td>
                                <td>
                                    @if($record['seller_id'])
                                        <a class="pm-view" href="{{ route('admin.monitoring.sellers.show',$record['seller_id']) }}">{{ $record['seller_name'] }}</a>
                                    @else
                                        {{ $record['seller_name'] }}
                                    @endif
                                </td>
                                <td>
                                    @if($record['logistics_id'])
                                        <a class="pm-view" href="{{ route('admin.monitoring.logistics.show',$record['logistics_id']) }}">{{ $record['logistics_name'] }}</a>
                                    @else
                                        {{ $record['logistics_name'] }}
                                    @endif
                                </td>
                                <td><div>{{ $record['rider_name'] }}</div><div class="pm-sub">{{ $record['rider_email'] ?: 'No rider assigned' }}</div></td>
                                <td>{{ $record['stage_started_at']?->timezone('Asia/Manila')->format('M j, h:i A') ?? '—' }} <span class="pm-sub">PHT</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @elseif($section === 'sellers')
                        <thead><tr><th class="pm-check-cell"><label class="pm-check-wrap"><input type="checkbox" data-pm-all aria-label="Select all visible monitoring records"></label></th><th>Store</th><th>Owner</th><th>Location</th><th>Products</th><th>Orders</th><th>Delivered</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            @php $recordStatus = ($record->suspended_until && now()->lt($record->suspended_until) && strtolower((string)($record->account_status ?: 'active')) === 'active') ? 'suspended' : strtolower((string) ($record->account_status ?: 'active')); @endphp
                            <tr data-pm-record-row>
                                <td class="pm-check-cell"><label class="pm-check-wrap"><input type="checkbox" data-pm-check aria-label="Select store {{ $record->store_name }}"></label></td>
                                <td><div class="pm-name">{{ $record->store_name ?: 'Unnamed Store' }}</div><div class="pm-sub">{{ $record->email }}</div></td>
                                <td><div class="pm-name">{{ trim(($record->first_name ?? '').' '.($record->last_name ?? '')) ?: '—' }}</div><div class="pm-sub">{{ $record->contact_no ?: 'No contact' }}</div></td>
                                <td>{{ collect([$record->municipality_name, $record->province_name])->filter()->implode(', ') ?: '—' }}</td>
                                <td>{{ number_format((int) $record->products_count) }} <span class="pm-sub">({{ number_format((int) $record->live_products_count) }} live)</span></td>
                                <td>{{ number_format((int) $record->marketplace_orders_count) }}</td>
                                <td>{{ number_format((int) $record->delivered_orders_count) }}</td>
                                <td><span class="pm-status {{ $recordStatus }}">{{ $statusLabel($recordStatus) }}</span></td>
                                <td><a class="pm-view" href="{{ route('admin.monitoring.sellers.show', $record) }}">View store</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @elseif($section === 'logistics')
                        <thead><tr><th class="pm-check-cell"><label class="pm-check-wrap"><input type="checkbox" data-pm-all aria-label="Select all visible monitoring records"></label></th><th>Provider</th><th>Representative</th><th>Service area</th><th>Hubs</th><th>Riders</th><th>Active deliveries</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            @php $recordStatus = strtolower((string) ($record->account_status ?: 'active')); @endphp
                            <tr data-pm-record-row>
                                <td class="pm-check-cell"><label class="pm-check-wrap"><input type="checkbox" data-pm-check aria-label="Select provider {{ $record->business_name }}"></label></td>
                                <td><div class="pm-name">{{ $record->business_name ?: $record->displayName() }}</div><div class="pm-sub">{{ $record->email }}</div></td>
                                <td>{{ trim(($record->first_name ?? '').' '.($record->last_name ?? '')) ?: '—' }}</td>
                                <td><div>{{ collect([$record->municipality_name, $record->province_name])->filter()->implode(', ') ?: '—' }}</div><div class="pm-sub">{{ \Illuminate\Support\Str::limit((string) $record->service_areas, 55) }}</div></td>
                                <td>{{ number_format((int) $record->facilities_count) }}</td>
                                <td>{{ number_format((int) $record->riders_count) }}</td>
                                <td>{{ number_format((int) $record->active_deliveries_count) }}</td>
                                <td><span class="pm-status {{ $recordStatus }}">{{ $statusLabel($recordStatus) }}</span></td>
                                <td><a class="pm-view" href="{{ route('admin.monitoring.logistics.show', $record) }}">View network</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @elseif($section === 'buyers')
                        <thead><tr><th class="pm-check-cell"><label class="pm-check-wrap"><input type="checkbox" data-pm-all aria-label="Select all visible monitoring records"></label></th><th>Buyer</th><th>Account type</th><th>Location</th><th>Total orders</th><th>Successful</th><th>Cancelled</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            @php $recordStatus = strtolower((string) ($record->account_status ?: 'active')); @endphp
                            <tr data-pm-record-row>
                                <td class="pm-check-cell"><label class="pm-check-wrap"><input type="checkbox" data-pm-check aria-label="Select buyer {{ $record->display_name }}"></label></td>
                                <td><div class="pm-name">{{ $record->display_name }}</div><div class="pm-sub">{{ $record->email ?: 'No email' }}</div></td>
                                <td>{{ $record->role_key === 'social_buyer' ? 'Social Buyer' : 'Registered Buyer' }}</td>
                                <td>{{ collect([$record->municipality_name, $record->province_name])->filter()->implode(', ') ?: '—' }}</td>
                                <td>{{ number_format((int) $record->orders_count) }}</td>
                                <td>{{ number_format((int) $record->delivered_orders_count) }}</td>
                                <td>{{ number_format((int) $record->cancelled_orders_count) }}</td>
                                <td><span class="pm-status {{ $recordStatus }}">{{ $statusLabel($recordStatus) }}</span></td>
                                <td><a class="pm-view" href="{{ route('admin.monitoring.buyers.show', ['role' => $record->role_key, 'id' => $record->id]) }}">View buyer</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                </table>
            @else
                <div class="pm-empty" role="status">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.45" aria-hidden="true"><circle cx="11" cy="11" r="7.5"/><path d="m20.5 20.5-4.2-4.2"/></svg>
                    <strong>No matching monitoring records</strong>
                    <p>Try a different search term or account status filter.</p>
                </div>
            @endif
        </div>
        <div class="pm-pagination">
            <span class="pm-pagination-summary">
                @if($section === 'sla')
                    Showing {{ number_format($records->count()) }} active orders
                @else
                    Showing {{ number_format($records->firstItem() ?? 0) }}–{{ number_format($records->lastItem() ?? 0) }} of {{ number_format($records->total()) }} results
                @endif
            </span>
            @if($section !== 'sla' && $records->hasPages())
                @php
                    $current = $records->currentPage();
                    $last = $records->lastPage();
                    $first = max(1, min($current - 2, $last - 4));
                    $end = min($last, $first + 4);
                @endphp
                <nav class="pm-pages" aria-label="Monitoring record pages">
                    @if($records->onFirstPage())
                        <span class="pm-page-arrow is-disabled" aria-label="Previous page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg></span>
                    @else
                        <a class="pm-page-arrow" href="{{ $records->previousPageUrl() }}" aria-label="Previous page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg></a>
                    @endif
                    @if($first > 1)
                        <a class="pm-page-number" href="{{ $records->url(1) }}">1</a>
                        @if($first > 2)<span class="pm-page-ellipsis">…</span>@endif
                    @endif
                    @for($number = $first; $number <= $end; $number++)
                        @if($number === $current)
                            <span class="pm-page-number is-current" aria-current="page">{{ $number }}</span>
                        @else
                            <a class="pm-page-number" href="{{ $records->url($number) }}">{{ $number }}</a>
                        @endif
                    @endfor
                    @if($end < $last)
                        @if($end < $last - 1)<span class="pm-page-ellipsis">…</span>@endif
                        <a class="pm-page-number" href="{{ $records->url($last) }}">{{ $last }}</a>
                    @endif
                    @if($records->hasMorePages())
                        <a class="pm-page-arrow" href="{{ $records->nextPageUrl() }}" aria-label="Next page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg></a>
                    @else
                        <span class="pm-page-arrow is-disabled" aria-label="Next page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg></span>
                    @endif
                </nav>
            @endif
        </div>
    </section>
</div>
<script>
(function () {
    function initializeMonitoringControls() {
        const page = document.querySelector('.pm-page');
        if (!page || page.dataset.pmInitialized === 'true') return;
        page.dataset.pmInitialized = 'true';

        const selectRoots = Array.from(page.querySelectorAll('[data-pm-select]'));
        const closeAllSelects = (except = null) => {
            selectRoots.forEach(root => {
                if (root === except) return;
                const menu = root.querySelector('[data-pm-menu]');
                const trigger = root.querySelector('[data-pm-trigger]');
                if (menu) menu.hidden = true;
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            });
        };
        selectRoots.forEach(root => {
            const native = root.querySelector('[data-pm-native]');
            const trigger = root.querySelector('[data-pm-trigger]');
            const label = root.querySelector('[data-pm-label]');
            const menu = root.querySelector('[data-pm-menu]');
            const options = Array.from(root.querySelectorAll('[data-pm-option]'));
            if (!native || !trigger || !menu || !label) return;
            root.classList.add('is-enhanced');
            native.tabIndex = -1;
            native.setAttribute('aria-hidden', 'true');
            const close = () => { menu.hidden = true; trigger.setAttribute('aria-expanded', 'false'); };
            const sync = () => {
                label.textContent = native.selectedOptions[0]?.textContent?.trim() || '';
                options.forEach(option => option.setAttribute('aria-selected', String(option.dataset.pmOption === native.value)));
            };
            sync();
            trigger.addEventListener('click', () => {
                const opening = menu.hidden;
                closeAllSelects(root);
                menu.hidden = !opening;
                trigger.setAttribute('aria-expanded', String(opening));
                if (opening) options.find(option => option.dataset.pmOption === native.value)?.focus();
            });
            options.forEach((option, index) => {
                option.addEventListener('click', () => {
                    native.value = option.dataset.pmOption || '';
                    native.dispatchEvent(new Event('change', { bubbles: true }));
                    sync(); close(); trigger.focus();
                });
                option.addEventListener('keydown', event => {
                    if (event.key === 'Escape') { event.preventDefault(); close(); trigger.focus(); return; }
                    const step = event.key === 'ArrowDown' ? 1 : event.key === 'ArrowUp' ? -1 : 0;
                    if (step) { event.preventDefault(); options[(index + step + options.length) % options.length].focus(); }
                    if (event.key === 'Home' || event.key === 'End') {
                        event.preventDefault(); options[event.key === 'Home' ? 0 : options.length - 1].focus();
                    }
                });
            });
            native.addEventListener('change', sync);
        });
        document.addEventListener('pointerdown', event => {
            if (!selectRoots.some(root => root.contains(event.target))) closeAllSelects();
        });
        document.addEventListener('keydown', event => { if (event.key === 'Escape') closeAllSelects(); });

        const all = page.querySelector('[data-pm-all]');
        const checks = Array.from(page.querySelectorAll('[data-pm-check]'));
        const selection = page.querySelector('[data-pm-selection]');
        const counter = page.querySelector('[data-pm-selected-count]');
        const clear = page.querySelector('[data-pm-clear]');
        const refresh = () => {
            const checked = checks.filter(input => input.checked).length;
            if (selection) selection.hidden = checked === 0;
            if (counter) counter.textContent = String(checked);
            if (all) { all.checked = checks.length > 0 && checked === checks.length; all.indeterminate = checked > 0 && checked < checks.length; }
        };
        all?.addEventListener('change', () => { checks.forEach(input => { input.checked = all.checked; }); refresh(); });
        checks.forEach(input => input.addEventListener('change', refresh));
        clear?.addEventListener('click', () => { checks.forEach(input => { input.checked = false; }); refresh(); });
        refresh();
    }
    document.addEventListener('livewire:navigated', initializeMonitoringControls);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeMonitoringControls, { once:true });
    else initializeMonitoringControls();
})();
</script>

@endsection
