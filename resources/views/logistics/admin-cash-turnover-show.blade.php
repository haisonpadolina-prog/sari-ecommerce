@extends('layouts.logistics')

@section('title', 'Admin Cash Turnover Slip — SARI Logistics')
@section('page-title', 'Turnover Slip / Receipt')

@push('styles')
<style>@media print { #logisticsSidebar, #logisticsHeader, header, nav, .print\:hidden { display: none !important; } #logisticsContent { width:100%!important; margin:0!important; padding:0!important; } }
/* Signatures belong to the printed Logistics slip only. */
@media screen {
    #sariAdminTurnoverReceipt .sari-turnover-signatures { display:none!important; }
}
@media print {
    #sariAdminTurnoverReceipt .sari-turnover-signatures { display:block!important; break-inside:avoid; background:#fff!important; color:#111!important; }
    #sariAdminTurnoverReceipt .sari-turnover-signatures > .grid { display:grid; grid-template-columns:1fr 1fr; gap:32px; margin-top:48px; }
}
</style>
@endpush

@push('styles')
<style>

/* Seller Orders sizing, scoped to this Logistics page. */
@media screen {
#logisticsContent #sariAdminTurnoverReceipt {max-width:1520px!important;width:100%;margin-inline:auto;font-family:'Poppins',sans-serif;color:var(--sari-dark-e23e64dd1e,#253244);font-size:12px;}
#logisticsContent #sariAdminTurnoverReceipt, #logisticsContent #sariAdminTurnoverReceipt * {box-sizing:border-box;}
#logisticsContent #sariAdminTurnoverReceipt > :is(section,form), #logisticsContent #sariAdminTurnoverReceipt > div > section {border-radius:18px;border-color:var(--sari-dark-d8c2716225,#e5e8ed);background:var(--sari-dark-950baec030,#fff);box-shadow:0 5px 18px rgba(31,41,55,.035);}
#logisticsContent #sariAdminTurnoverReceipt :is(section,form).p-5, #logisticsContent #sariAdminTurnoverReceipt section.p-6 {padding:22px;}
#logisticsContent #sariAdminTurnoverReceipt h2 {font-size:clamp(27px,2.3vw,31px)!important;line-height:1.18!important;font-weight:760;letter-spacing:-.04em;overflow-wrap:anywhere;}
#logisticsContent #sariAdminTurnoverReceipt h3 {font-size:16px!important;line-height:1.4;font-weight:700;letter-spacing:-.02em;}
#logisticsContent #sariAdminTurnoverReceipt p, #logisticsContent #sariAdminTurnoverReceipt label, #logisticsContent #sariAdminTurnoverReceipt dl {font-size:11.5px;line-height:1.8;}
#logisticsContent #sariAdminTurnoverReceipt p.uppercase {font-size:10.5px;font-weight:800;letter-spacing:.12em;}
#logisticsContent #sariAdminTurnoverReceipt .cash-page-header {padding:0 0 8px!important;border:0!important;border-radius:0!important;box-shadow:none!important;background:transparent!important;}
#logisticsContent #sariAdminTurnoverReceipt .cash-page-header > p:first-child {font-size:10.5px!important;font-weight:800;letter-spacing:.14em;}
#logisticsContent #sariAdminTurnoverReceipt .cash-nav {display:inline-flex;align-items:center;gap:8px;min-height:40px;padding:0;border:0;background:transparent;box-shadow:none;color:var(--sari-dark-a63aa16d7c,#59636e);font-size:12px;font-weight:750;text-decoration:none;}
#logisticsContent #sariAdminTurnoverReceipt .cash-nav::after {content:'›';font-size:25px;line-height:1;font-weight:800;}
#logisticsContent #sariAdminTurnoverReceipt .cash-nav:hover {color:#c88912;}
#logisticsContent #sariAdminTurnoverReceipt .cash-stats {gap:14px!important;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important;}
#logisticsContent #sariAdminTurnoverReceipt .cash-stats article {min-height:110px;padding:14px 16px;border:1px solid var(--sari-dark-d8c2716225,#e5e8ed);border-radius:18px;background:var(--sari-dark-950baec030,#fff);}
#logisticsContent #sariAdminTurnoverReceipt .cash-stats article p:first-child {font-size:13px;font-weight:600;text-transform:none;letter-spacing:0;}
#logisticsContent #sariAdminTurnoverReceipt .cash-stats article p:last-child {font-size:clamp(22px,2vw,28px);font-weight:650;letter-spacing:-.035em;overflow-wrap:anywhere;}
#logisticsContent #sariAdminTurnoverReceipt .overflow-x-auto {max-width:100%;overflow-x:auto;}
#logisticsContent #sariAdminTurnoverReceipt table {width:100%;min-width:900px;border-collapse:collapse;text-align:left;}
#logisticsContent #sariAdminTurnoverReceipt thead {background:var(--sari-dark-4d21eb669b,#f8f9fb);}
#logisticsContent #sariAdminTurnoverReceipt th {padding:15px 16px!important;font-size:11px!important;font-weight:700;letter-spacing:.065em;text-transform:uppercase;color:var(--sari-dark-a63aa16d7c,#788392);}
#logisticsContent #sariAdminTurnoverReceipt td {padding:17px 16px!important;font-size:12px;line-height:1.55;border-bottom:1px solid var(--sari-dark-d8c2716225,#edf0f3);}
#logisticsContent #sariAdminTurnoverReceipt tbody tr:hover {background:var(--sari-dark-4d21eb669b,#fafbfc);}
#logisticsContent #sariAdminTurnoverReceipt :is(input:not([type=checkbox]),textarea) {min-height:48px;border-radius:14px;padding:12px 14px;font-size:11.5px;color:var(--sari-dark-e23e64dd1e,#253244);background:var(--sari-dark-950baec030,#fff);border-color:var(--sari-dark-d8c2716225,#dfe4eb);}
#logisticsContent #sariAdminTurnoverReceipt textarea {min-height:100px;resize:vertical;}
#logisticsContent #sariAdminTurnoverReceipt input[type=checkbox] {width:17px;height:17px;accent-color:#c88912;}
#logisticsContent #sariAdminTurnoverReceipt button {min-height:48px;border-radius:14px;font-size:12px;font-weight:700;}
#logisticsContent #sariAdminTurnoverReceipt button[type=submit], #logisticsContent #sariAdminTurnoverReceipt form > button {background:#c88912;color:#fff;padding:12px 20px;}
#logisticsContent #sariAdminTurnoverReceipt button[type=submit]:hover, #logisticsContent #sariAdminTurnoverReceipt form > button:hover {background:#b47b0e;}
#logisticsContent #sariAdminTurnoverReceipt :is(button,a,input,textarea):focus-visible {outline:2px solid #c88912;outline-offset:3px;}
#logisticsContent #sariAdminTurnoverReceipt .cash-pagination {min-height:64px;padding:13px 0;border-top:1px solid var(--sari-dark-d8c2716225,#edf0f3);}
#logisticsContent #sariAdminTurnoverReceipt .cash-pagination nav a, #logisticsContent #sariAdminTurnoverReceipt .cash-pagination nav span[aria-current] > span {min-width:34px;min-height:34px;border-radius:10px;font-size:11px;}
#logisticsContent #sariAdminTurnoverReceipt .cash-pagination nav span[aria-current] > span {background:#c88912;color:white;}
@media(max-width:640px){#logisticsContent #sariAdminTurnoverReceipt :is(section,form).p-5,#logisticsContent #sariAdminTurnoverReceipt section.p-6{padding:16px;}#logisticsContent #sariAdminTurnoverReceipt h2{font-size:25px!important;}#logisticsContent #sariAdminTurnoverReceipt .cash-stats{grid-template-columns:1fr;}#logisticsContent #sariAdminTurnoverReceipt .cash-stats article{min-height:100px;}}
}

</style>
@endpush

@push('styles')
<style>

/* Formal A4 paper layout; normal workspace UI is unchanged. */
#sariAdminTurnoverReceipt .turnover-paper-heading {display:none;}
@page {size:A4 portrait;margin:16mm 15mm;}
@media print {
 html,body {background:#fff!important;color:#161616!important;min-width:0!important;height:auto!important;}
 #logisticsContent,#sariAdminTurnoverReceipt {width:100%!important;max-width:none!important;margin:0!important;padding:0!important;}
 #sariAdminTurnoverReceipt,#sariAdminTurnoverReceipt * {font-family:Arial,Helvetica,sans-serif!important;color:#161616!important;box-shadow:none!important;text-shadow:none!important;box-sizing:border-box;}
 #sariAdminTurnoverReceipt {font-size:10pt!important;line-height:1.45!important;}
 #sariAdminTurnoverReceipt .turnover-paper-heading {display:flex!important;justify-content:space-between;align-items:end;padding:0 0 5mm;border-bottom:2pt solid #202020;margin-bottom:6mm;}
 #sariAdminTurnoverReceipt .turnover-paper-heading strong {font-size:24pt;letter-spacing:2pt;}
 #sariAdminTurnoverReceipt .turnover-paper-heading span {font-size:9pt;letter-spacing:1pt;text-transform:uppercase;}
 #sariAdminTurnoverReceipt > .space-y-5 {display:flex!important;flex-direction:column;gap:0!important;}
 #sariAdminTurnoverReceipt > .space-y-5 > section {border:0!important;border-radius:0!important;background:#fff!important;padding:0!important;margin:0 0 7mm!important;min-height:0!important;}
 #sariAdminTurnoverReceipt .turnover-overview {order:0;}
 #sariAdminTurnoverReceipt .turnover-batches {order:1;}
 #sariAdminTurnoverReceipt .turnover-count-history {order:2;}
 #sariAdminTurnoverReceipt .sari-turnover-signatures {order:3;break-inside:avoid;}
 #sariAdminTurnoverReceipt .turnover-history-empty {display:none!important;}
 #sariAdminTurnoverReceipt .print\:hidden {display:none!important;}
 #sariAdminTurnoverReceipt p {font-size:9pt!important;line-height:1.5!important;margin-top:2mm!important;}
 #sariAdminTurnoverReceipt h2 {font-size:18pt!important;letter-spacing:0!important;line-height:1.25!important;margin:2mm 0!important;}
 #sariAdminTurnoverReceipt h3 {font-size:10pt!important;letter-spacing:.6pt!important;text-transform:uppercase;margin:0 0 3mm!important;padding-bottom:2mm;border-bottom:.6pt solid #aaa;break-after:avoid;}
 #sariAdminTurnoverReceipt .turnover-overview > div:first-child {display:block!important;}
 #sariAdminTurnoverReceipt .turnover-overview > div:first-child > div:first-child > p:first-child {font-size:9pt!important;letter-spacing:1pt!important;text-transform:uppercase;}
 #sariAdminTurnoverReceipt .turnover-totals {display:grid!important;grid-template-columns:1fr 1fr 1fr!important;gap:5mm!important;border-top:.6pt solid #aaa;border-bottom:.6pt solid #aaa;padding:4mm 0!important;margin:5mm 0!important;break-inside:avoid;}
 #sariAdminTurnoverReceipt .turnover-totals > div {background:transparent!important;border:0!important;border-radius:0!important;padding:0!important;}
 #sariAdminTurnoverReceipt .turnover-totals p:first-child {font-size:7.5pt!important;text-transform:uppercase;letter-spacing:.4pt;}
 #sariAdminTurnoverReceipt .turnover-totals p:last-child {font-size:14pt!important;font-weight:700!important;margin-top:1.5mm!important;}
 #sariAdminTurnoverReceipt dl {display:grid!important;grid-template-columns:1fr 1fr!important;gap:4mm 10mm!important;margin:0!important;font-size:9pt!important;}
 #sariAdminTurnoverReceipt dt {font-size:8pt!important;color:#555!important;margin-bottom:1mm;}
 #sariAdminTurnoverReceipt dd {margin:0!important;overflow-wrap:anywhere;}
 #sariAdminTurnoverReceipt .turnover-overview > p {border:0!important;border-left:2pt solid #888!important;border-radius:0!important;background:transparent!important;padding:2mm 3mm!important;font-size:8pt!important;margin-top:5mm!important;font-weight:400!important;}
 #sariAdminTurnoverReceipt .overflow-x-auto {overflow:visible!important;margin-top:0!important;}
 #sariAdminTurnoverReceipt table {width:100%!important;min-width:0!important;table-layout:fixed;border-collapse:collapse!important;font-size:8.5pt!important;}
 #sariAdminTurnoverReceipt thead {display:table-header-group;}
 #sariAdminTurnoverReceipt th {font-size:7pt!important;letter-spacing:.3pt;text-transform:uppercase;text-align:left;background:#f2f2f2!important;border-bottom:1pt solid #555!important;padding:3mm 2mm!important;}
 #sariAdminTurnoverReceipt td {font-size:8.5pt!important;padding:3mm 2mm!important;border-bottom:.5pt solid #ccc!important;overflow-wrap:anywhere;vertical-align:top;}
 #sariAdminTurnoverReceipt tr {break-inside:avoid;}
 #sariAdminTurnoverReceipt th:last-child,#sariAdminTurnoverReceipt td:last-child {text-align:right!important;width:22%;}
 #sariAdminTurnoverReceipt .sari-turnover-signatures > p {font-size:8pt!important;color:#555!important;}
 #sariAdminTurnoverReceipt .sari-turnover-signatures > .grid {display:grid!important;grid-template-columns:1fr 1fr!important;gap:12mm!important;margin-top:16mm!important;}
 #sariAdminTurnoverReceipt .sari-turnover-signatures > .grid > div {font-size:8pt!important;border-top:.6pt solid #555!important;padding-top:2mm!important;}
 #sariAdminTurnoverReceipt .turnover-count-history > p {display:none!important;}
 #sariAdminTurnoverReceipt .turnover-count-history .space-y-3 > div {border:0!important;border-bottom:.5pt solid #ccc!important;border-radius:0!important;padding:2mm 0!important;margin:0!important;break-inside:avoid;}
}

</style>
@endpush

@push('styles')
<style>

/* Branded stationery: original assets, black wordmark on white paper. */
@media print {
 #sariAdminTurnoverReceipt .turnover-paper-heading {align-items:center!important;gap:8mm;padding-bottom:5mm!important;margin-bottom:6mm!important;border-bottom:1.2pt solid #222!important;break-inside:avoid;}
 #sariAdminTurnoverReceipt .turnover-brand-lockup {display:flex!important;align-items:center;gap:4mm;flex:0 0 auto;}
 #sariAdminTurnoverReceipt .turnover-brand-lockup img {display:block!important;object-fit:contain!important;max-width:none!important;opacity:1!important;visibility:visible!important;print-color-adjust:exact;-webkit-print-color-adjust:exact;}
 #sariAdminTurnoverReceipt .turnover-brand-emblem {width:15mm!important;height:15mm!important;}
 #sariAdminTurnoverReceipt .turnover-brand-wordmark {width:43mm!important;height:auto!important;max-height:17mm!important;filter:brightness(0)!important;}
 #sariAdminTurnoverReceipt .turnover-document-label {text-align:right;display:flex;flex-direction:column;gap:1.5mm;}
 #sariAdminTurnoverReceipt .turnover-document-label span {font-size:7pt!important;letter-spacing:1.2pt!important;color:#555!important;}
 #sariAdminTurnoverReceipt .turnover-document-label b {font-size:11pt!important;font-weight:700;line-height:1.35;}
 #sariAdminTurnoverReceipt .turnover-document-label small {font-size:8pt!important;color:#555!important;}
 #sariAdminTurnoverReceipt .turnover-overview h2 {font-size:16pt!important;letter-spacing:.2pt!important;}
 #sariAdminTurnoverReceipt .turnover-overview > div:first-child > div:first-child > p:first-child {font-size:7.5pt!important;letter-spacing:.8pt!important;color:#555!important;}
}

</style>
@endpush

@push('styles')
<style>

/* Clean receipt workspace. Scoped screen styles preserve printed stationery. */
@media screen {
#logisticsContent #sariAdminTurnoverReceipt {--receipt-surface:var(--sari-dark-950baec030,#fff);--receipt-soft:var(--sari-dark-4d21eb669b,#f7f9fc);--receipt-line:var(--sari-dark-d8c2716225,#e5eaf0);--receipt-ink:var(--sari-dark-e23e64dd1e,#253244);--receipt-muted:var(--sari-dark-a63aa16d7c,#748093);}
#logisticsContent #sariAdminTurnoverReceipt > .space-y-5 {display:flex;flex-direction:column;gap:20px;}
#logisticsContent #sariAdminTurnoverReceipt > .space-y-5 > section {margin:0!important;border:1px solid var(--receipt-line)!important;border-radius:20px!important;background:var(--receipt-surface)!important;padding:24px!important;box-shadow:0 4px 8px rgba(25,42,65,.025),0 12px 32px rgba(25,42,65,.065)!important;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-overview > div:first-child {gap:20px;align-items:flex-start;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-overview > div:first-child > div:first-child {flex:1 1 420px;min-width:0;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-overview > div:first-child > div:first-child > p:first-child {color:var(--receipt-muted)!important;font-size:10.5px!important;letter-spacing:.12em;font-weight:750;}
#logisticsContent #sariAdminTurnoverReceipt h2 {margin:8px 0!important;color:var(--receipt-ink)!important;font-size:clamp(25px,2.3vw,31px)!important;}
#logisticsContent #sariAdminTurnoverReceipt h3 {color:var(--receipt-ink)!important;margin:0;font-size:16px!important;font-weight:700;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-overview > div:first-child > div:first-child > p:last-child {color:var(--receipt-muted)!important;max-width:650px;font-size:11.5px;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-overview .print\:hidden {display:flex;flex-wrap:wrap;align-items:center;gap:16px;}
#logisticsContent #sariAdminTurnoverReceipt button[onclick="window.print()"] {display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:12px 18px;border:1px solid var(--receipt-line)!important;border-radius:13px;background:var(--receipt-soft)!important;color:var(--receipt-ink)!important;font-size:11.5px;font-weight:700;box-shadow:0 2px 5px rgba(25,42,65,.04);}
#logisticsContent #sariAdminTurnoverReceipt button[onclick="window.print()"]:hover {border-color:#a9b6c8!important;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-overview .print\:hidden a {display:inline-flex;align-items:center;gap:8px;min-height:46px;padding:0!important;border:0!important;border-radius:0!important;background:transparent!important;color:var(--receipt-muted)!important;font-size:11.5px;font-weight:750;text-decoration:none;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-overview .print\:hidden a::after {content:'›';font-size:24px;line-height:1;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-totals {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin:24px 0!important;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-totals > div {min-height:106px;padding:17px 18px!important;border:1px solid var(--receipt-line)!important;border-radius:15px!important;background:var(--receipt-surface)!important;box-shadow:0 3px 12px rgba(25,42,65,.045);}
#logisticsContent #sariAdminTurnoverReceipt .turnover-totals p:first-child {margin:0;color:var(--receipt-muted)!important;font-size:11px;letter-spacing:.025em;text-transform:none;font-weight:600;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-totals p:last-child {margin:10px 0 0!important;font-size:clamp(19px,1.6vw,25px)!important;font-weight:650;line-height:1.3;letter-spacing:-.025em;color:var(--receipt-ink)!important;overflow-wrap:anywhere;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-totals > div:last-child p:last-child {display:inline-flex;font-size:11px!important;letter-spacing:.07em;background:var(--receipt-soft);border:1px solid var(--receipt-line);padding:7px 10px;border-radius:8px;font-weight:750;}
#logisticsContent #sariAdminTurnoverReceipt dl {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 28px;margin:0!important;}
#logisticsContent #sariAdminTurnoverReceipt dl > div {min-width:0;padding:15px 0;border-bottom:1px solid var(--receipt-line);}
#logisticsContent #sariAdminTurnoverReceipt dt {font-size:10.5px;font-weight:600;color:var(--receipt-muted)!important;}
#logisticsContent #sariAdminTurnoverReceipt dd {margin:5px 0 0;font-size:12px;font-weight:500;color:var(--receipt-ink)!important;overflow-wrap:anywhere;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-overview > p {margin:20px 0 0!important;border:1px solid var(--receipt-line)!important;border-left:3px solid #8b9bb0!important;border-radius:12px!important;padding:12px 15px!important;background:var(--receipt-soft)!important;color:var(--receipt-muted)!important;font-size:11px!important;font-weight:500!important;line-height:1.75;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-batches .overflow-x-auto {margin-top:18px;border:1px solid var(--receipt-line);border-radius:13px;}
#logisticsContent #sariAdminTurnoverReceipt table {min-width:760px;}
#logisticsContent #sariAdminTurnoverReceipt thead {background:var(--receipt-soft)!important;}
#logisticsContent #sariAdminTurnoverReceipt th {color:var(--receipt-muted)!important;border-color:var(--receipt-line)!important;font-size:10px!important;padding:14px 16px!important;}
#logisticsContent #sariAdminTurnoverReceipt td {color:var(--receipt-ink)!important;border-color:var(--receipt-line)!important;padding:18px 16px!important;font-size:11.5px;}
#logisticsContent #sariAdminTurnoverReceipt td:first-child,#logisticsContent #sariAdminTurnoverReceipt td:last-child {font-weight:650;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-count-history > p {color:var(--receipt-muted)!important;margin-top:6px;font-size:11px;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-count-history .space-y-3 > p {padding:24px!important;border:1px dashed var(--receipt-line)!important;background:var(--receipt-soft)!important;border-radius:13px;text-align:center;color:var(--receipt-muted)!important;}
#logisticsContent #sariAdminTurnoverReceipt .turnover-count-history .space-y-3 > div {background:var(--receipt-surface)!important;border-color:var(--receipt-line)!important;border-radius:13px;}
@media(max-width:700px){
 #logisticsContent #sariAdminTurnoverReceipt > .space-y-5 > section {padding:18px!important;border-radius:16px!important;}
 #logisticsContent #sariAdminTurnoverReceipt .turnover-totals {grid-template-columns:1fr;gap:10px;}
 #logisticsContent #sariAdminTurnoverReceipt .turnover-totals > div {min-height:90px;}
 #logisticsContent #sariAdminTurnoverReceipt dl {grid-template-columns:1fr;}
}
}

</style>
@endpush

@section('content')
<div id="sariAdminTurnoverReceipt" class="sari-logistics-ui mx-auto max-w-[1400px]">
    <div class="turnover-paper-heading">
        <div class="turnover-brand-lockup">
            <img class="turnover-brand-emblem" src="{{ asset('images/sari-main-logo.png') }}" alt="SARI emblem" width="64" height="64" loading="eager">
            <img class="turnover-brand-wordmark" src="{{ asset('images/sari-logo.png') }}" alt="SARI — Elevated Everyday" width="180" height="60" loading="eager">
        </div>
        <div class="turnover-document-label"><span>Finance &amp; Treasury</span><b>Cash Turnover Slip / Receipt</b><small>Physical COD Collections</small></div>
    </div>
    @include('finance.admin-cash-turnover-detail', ['isAdminView' => false])
</div>
@endsection
