@extends('layouts.logistics')

@section('title', 'Parcel QR Scanner — SARI Logistics')
@section('page-title', 'Parcel QR Scanner')

@push('styles')
<style>
/* Logistics-only presentation. The reusable scanner and its scan API are not modified. */
#logisticsContent .sari-logistics-scanner { --qr-ink:var(--sari-dark-e23e64dd1e, #252e38); --qr-muted:var(--sari-dark-a63aa16d7c, #8793a1); --qr-border:var(--sari-dark-d8c2716225, #e4e9ef); --qr-gold:#d9950b; --qr-surface:var(--sari-dark-950baec030, #fff); }
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] {
  width:100%; max-width:1190px; margin-inline:auto;
  display:flex; flex-direction:column; gap:18px;
  font-family:Poppins,ui-sans-serif,sans-serif;
}
/* Minimal page heading: leave room for the actual scanner. */
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child {
  padding:4px 2px 1px !important; border:0 !important; border-radius:0 !important;
  background:transparent !important; box-shadow:none !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child > div { align-items:center; gap:16px; }
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child span {
  border:0 !important; border-radius:0; background:transparent !important;
  padding:0 !important; color:var(--sari-dark-af18afcaa4, #af7714) !important; font-size:10px !important;
  font-weight:800; letter-spacing:.13em;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child h2 {
  margin-top:6px; font-size:clamp(26px,2.3vw,31px) !important;
  font-weight:760 !important; line-height:1.12 !important;
  letter-spacing:-.035em !important; color:var(--qr-ink) !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child p {
  margin-top:7px; max-width:720px; font-size:11.5px !important; line-height:1.65 !important;
  color:var(--sari-dark-a63aa16d7c, #7a8591) !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child a {
  min-height:39px; flex-shrink:0; border:1px solid var(--sari-dark-d8c2716225, #e0e5eb) !important; border-radius:11px;
  padding-inline:14px; background:var(--sari-dark-950baec030, white) !important; color:var(--sari-dark-a63aa16d7c, #535f6e) !important;
  font-size:11px; box-shadow:none !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child a:hover {background:var(--sari-dark-950baec030, #f7f9fb) !important; border-color:var(--sari-dark-d8c2716225, #ccd5df) !important;}
/* Logistics-only heading is plain page content: no card, separator, or outline. */
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section.qr-compact-header,
.sari-logistics-scanner [data-sari-qr-scanner] > section.qr-compact-header {
  display:block;
  width:100%;
  min-height:0;
  margin:0 !important;
  padding:0 !important;
  border:0 !important;
  border-radius:0 !important;
  outline:0 !important;
  background:transparent !important;
  background-image:none !important;
  box-shadow:none !important;
  filter:none !important;
  backdrop-filter:none !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section.qr-compact-header::before,
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section.qr-compact-header::after,
.sari-logistics-scanner [data-sari-qr-scanner] > section.qr-compact-header::before,
.sari-logistics-scanner [data-sari-qr-scanner] > section.qr-compact-header::after {
  display:none !important;
  content:none !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-heading { gap:14px; }
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-eyebrow {
  display:block; color:var(--sari-dark-685eb4fc40, #a87416) !important; background:none !important;
  border:0 !important; padding:0 !important;
  font-size:10px !important; font-weight:750 !important; letter-spacing:.13em !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-title {
  margin-top:6px !important;
  color:var(--sari-dark-e23e64dd1e, #25303a) !important; font-size:clamp(25px,2.3vw,30px) !important;
  line-height:1.13 !important; font-weight:760 !important; letter-spacing:-.035em !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-summary {
  max-width:550px !important; margin-top:6px !important;
  font-size:12px !important; line-height:1.5 !important; color:var(--sari-dark-a63aa16d7c, #7a8794) !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back {
  display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; gap:4px;
  padding:5px 0 !important; min-height:32px !important; border:0 !important;
  background:transparent !important; border-radius:0 !important; box-shadow:none !important;
  color:var(--sari-dark-a63aa16d7c, #8a6530) !important; font-size:11px !important; font-weight:700 !important;
  transition:color .12s ease !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back:hover {color:var(--sari-dark-e23e64dd1e, #65502c) !important; background:transparent !important;}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back:focus-visible {outline:2px solid var(--sari-dark-d8c2716225, #a5b0bd); outline-offset:4px; border-radius:4px !important;}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back svg {width:15px; height:15px; flex-shrink:0;}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back span {
  color:inherit !important; text-transform:none !important; letter-spacing:0 !important;
  font-size:11px !important; font-weight:700 !important;
}
@media(max-width:700px){
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back {align-self:flex-start !important;}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-title {font-size:25px !important;}
}

/*
 * buyer-parity.css is linked AFTER @stack('styles') in layouts.logistics.
 * Its scanner rule has higher specificity than the old no-container rule.
 * Match its full selector and add the dedicated header class to reliably win
 * even when that shared CSS loads last. Scope to Logistics only.
 */
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section.qr-compact-header:first-child {
  display: block !important;
  width: 100% !important;
  min-height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  border: 0 !important;
  border-radius: 0 !important;
  background: transparent !important;
  background-image: none !important;
  box-shadow: none !important;
  outline: 0 !important;
  filter: none !important;
  backdrop-filter: none !important;
  overflow: visible !important;
}
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section.qr-compact-header:first-child::before,
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section.qr-compact-header:first-child::after {
  content: none !important;
  display: none !important;
}

/* Camera is the dominant workspace. */
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) {
  display:grid; grid-template-columns:minmax(0,1.48fr) minmax(292px,.82fr);
  align-items:start; gap:18px;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child {
  min-width:0; overflow:hidden; border:1px solid var(--sari-dark-d8c2716225, #303a44) !important;
  border-radius:21px !important; background:var(--sari-dark-f3318ab620, #151c24) !important;
  box-shadow:0 4px 14px var(--sari-dark-7c935c8886, rgba(15,23,42,.07)),0 18px 38px var(--sari-dark-71f44bdb16, rgba(15,23,42,.11)) !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child {
  padding:19px 21px; border-color:var(--sari-dark-adc0ef4882, rgba(255,255,255,.09)) !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child p:first-child {
  color:#f5f7f9 !important; font-size:14px !important; font-weight:700 !important;
  letter-spacing:-.02em !important; text-transform:none !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child p:nth-child(2) {
  color:var(--sari-dark-a63aa16d7c, #98a6b5) !important; font-size:10.5px !important; margin-top:4px;
}
#logisticsContent .sari-logistics-scanner #sariQrCameraBadge {
  padding:7px 11px; font-size:10px; font-weight:650; color:#c9d4df;
  border:1px solid var(--sari-dark-70334c7f17, rgba(255,255,255,.13)); border-radius:999px;
}
#logisticsContent .sari-logistics-scanner #sariQrCameraBadge.text-emerald-300 {color:#92e7b5;}
#logisticsContent .sari-logistics-scanner #sariQrCameraBadge.text-rose-300 {color:#ffaaa1;}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:nth-child(2),
#logisticsContent .sari-logistics-scanner #sariQrReader {
  min-height:382px !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:nth-child(2) { background:var(--sari-dark-f3318ab620, #10161d) !important; }
#logisticsContent .sari-logistics-scanner #sariQrReader { background:var(--sari-dark-f3318ab620, #10161d); }
#logisticsContent .sari-logistics-scanner #sariQrReader video {object-fit:cover;}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder { padding:18px 22px; background:var(--sari-dark-f3318ab620, #10161d); }
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder > div {width:100%; max-width:310px; margin-inline:auto;}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder > div > div:first-child {
  width:70px; height:70px; border:1px solid var(--sari-dark-d8c2716225, #3c4855); border-radius:18px;
  background:var(--sari-dark-fbb35de0d1, #202b37); color:#e8edf2;
}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder > div > div:first-child svg {width:35px; height:35px;}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder p:nth-child(2) {
  margin-top:18px; color:#f5f7f9; font-size:16px; font-weight:650;
}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder p:nth-child(3) {
  margin-top:8px; font-size:11.5px; line-height:1.6; color:var(--sari-dark-a63aa16d7c, #91a0af);
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:last-child {
  padding:17px 20px; gap:10px; border-color:var(--sari-dark-6da278ba55, rgba(255,255,255,.08)) !important;
}
#logisticsContent .sari-logistics-scanner #sariQrStartButton,
#logisticsContent .sari-logistics-scanner #sariQrStopButton,
#logisticsContent .sari-logistics-scanner #sariQrScanAnotherButton {
  min-height:44px; padding:0 17px; border-radius:11px; font-size:11.5px; font-weight:700;
  box-shadow:none; transition:background-color .14s ease,border-color .14s ease;
}
#logisticsContent .sari-logistics-scanner #sariQrStartButton { background:var(--qr-gold) !important; color:#fff !important; }
#logisticsContent .sari-logistics-scanner #sariQrStartButton:hover { background:#c18306 !important; }
#logisticsContent .sari-logistics-scanner #sariQrStopButton { background:var(--sari-dark-fbb35de0d1, #26313d) !important; border:1px solid var(--sari-dark-d8c2716225, #414e5c) !important; color:#e0e8f0 !important; }
#logisticsContent .sari-logistics-scanner #sariQrStopButton:disabled { opacity:.45; cursor:not-allowed; }
#logisticsContent .sari-logistics-scanner #sariQrScanAnotherButton { color:#ffdc92 !important; }
/* White, elevated status card; response colors still come from the original JS. */
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) {min-width:0; display:flex; flex-direction:column; gap:13px;}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section {
  margin:0 !important; border:1px solid var(--qr-border) !important;
  border-radius:19px !important; background:var(--sari-dark-950baec030, #fff) !important;
  box-shadow:0 2px 7px var(--sari-dark-b13200ac05, rgba(15,23,42,.03)),0 13px 29px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055)) !important;
  padding:20px !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section > p:first-child {
  font-size:12px !important; letter-spacing:0 !important; color:var(--sari-dark-e23e64dd1e, #303b46) !important;
  font-weight:700 !important; text-transform:none !important;
}
#logisticsContent .sari-logistics-scanner #sariQrResult {
  margin-top:14px; border-radius:13px !important; padding:17px !important;
  border:1px solid var(--sari-dark-d8c2716225, #e7ebf0); background:var(--sari-dark-950baec030, #f8fafb);
}
#logisticsContent .sari-logistics-scanner #sariQrResult.bg-emerald-50 {background:var(--sari-dark-4d21eb669b, #effaf4) !important; border-color:var(--sari-dark-d8c2716225, #bfe9cf) !important;}
#logisticsContent .sari-logistics-scanner #sariQrResult.bg-rose-50 {background:var(--sari-dark-950baec030, #fff3f2) !important; border-color:var(--sari-dark-d8c2716225, #f2cac5) !important;}
#logisticsContent .sari-logistics-scanner #sariQrResultTitle {font-size:13px !important; font-weight:750;}
#logisticsContent .sari-logistics-scanner #sariQrResultMessage {font-size:11px !important; line-height:1.65 !important;}
#logisticsContent .sari-logistics-scanner #sariQrOrderBlock {border-color:var(--sari-dark-d8c2716225, #e1e6eb) !important;}
#logisticsContent .sari-logistics-scanner #sariQrOrderNumber {font-size:14px !important; overflow-wrap:anywhere;}
#logisticsContent .sari-logistics-scanner #sariQrStage {font-size:11px !important;}
#logisticsContent .sari-logistics-scanner #sariQrNextLink {border-radius:11px; font-size:11px; min-height:41px;}
/* Secondary manual option - collapsed until requested. */
#logisticsContent .sari-logistics-scanner .qr-manual-toggle {
  width:100%; display:flex; justify-content:space-between; align-items:center;
  gap:12px; min-height:46px; border:1px solid var(--qr-border);
  background:var(--sari-dark-950baec030, #fff); color:var(--sari-dark-e23e64dd1e, #465361); border-radius:13px;
  padding:0 15px; font-size:11px; font-weight:700;
  text-align:left; box-shadow:0 4px 11px var(--sari-dark-e1384eb7fc, rgba(15,23,42,.025));
}
#logisticsContent .sari-logistics-scanner .qr-manual-toggle:hover {background:var(--sari-dark-950baec030, #f8fafb); border-color:var(--sari-dark-d8c2716225, #d5dce4);}
#logisticsContent .sari-logistics-scanner .qr-manual-toggle svg {width:16px; height:16px; color:var(--sari-dark-a63aa16d7c, #86919d); transition:transform .15s ease;}
#logisticsContent .sari-logistics-scanner .qr-manual-toggle[aria-expanded="true"] svg {transform:rotate(180deg);}
#logisticsContent .sari-logistics-scanner #sariQrFallbackPanel[hidden] {display:none !important;}
#logisticsContent .sari-logistics-scanner #sariQrFallbackPanel > p:nth-child(2) {font-size:10.5px !important; line-height:1.6; color:var(--sari-dark-a63aa16d7c, #7e8996);}
#logisticsContent .sari-logistics-scanner #sariQrManualPayload {
  width:100%; font-size:11px; border:1px solid var(--sari-dark-d8c2716225, #dce2e9) !important; border-radius:12px;
  background:var(--sari-dark-950baec030, #fff) !important; color:var(--sari-dark-e23e64dd1e, #2e3944) !important;
}
#logisticsContent .sari-logistics-scanner #sariQrManualPayload:focus {outline:none; border-color:var(--sari-dark-d8c2716225, #b1bdc9) !important; box-shadow:0 0 0 3px var(--sari-dark-8eadf403a5, rgba(96,112,128,.1)) !important;}
#logisticsContent .sari-logistics-scanner #sariQrManualForm button {
  min-height:41px; border:1px solid var(--sari-dark-d8c2716225, #dce2e9) !important; border-radius:11px;
  background:var(--sari-dark-950baec030, #f5f7fa) !important; color:var(--sari-dark-e23e64dd1e, #394653) !important; font-size:11px;
}
#logisticsContent .sari-logistics-scanner #sariQrManualForm button:hover {background:var(--sari-dark-4d21eb669b, #eaf0f5) !important;}
/* Replaces the redundant yellow camera warning block with a quiet support note. */
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:last-child {
  border:0 !important; border-radius:0 !important; background:transparent !important;
  box-shadow:none !important; padding:6px 3px !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:last-child > p:first-child {
  color:var(--sari-dark-a63aa16d7c, #7e8996) !important; font-size:10px !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:last-child > p:last-child {
  color:var(--sari-dark-a63aa16d7c, #9ba5af) !important; font-size:10px !important; line-height:1.6;
}
@media (max-width:1150px) {
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) {grid-template-columns:minmax(0,1fr);}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) {display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); align-items:start;}
  #logisticsContent .sari-logistics-scanner .qr-manual-toggle {grid-column:2; grid-row:1;}
  #logisticsContent .sari-logistics-scanner #sariQrFallbackPanel {grid-column:2;}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:last-child {grid-column:1 / -1;}
}
@media (max-width:700px) {
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] {gap:13px;}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child > div {align-items:flex-start; flex-direction:column;}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child a {min-height:36px;}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) {gap:13px;}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) {display:flex; flex-direction:column; align-items:stretch;}
  #logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder > div > div:first-child {width:60px; height:60px;}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:nth-child(2),
  #logisticsContent .sari-logistics-scanner #sariQrReader {min-height:315px !important;}
  #logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:last-child {padding:13px;}
  #logisticsContent .sari-logistics-scanner #sariQrStartButton,#logisticsContent .sari-logistics-scanner #sariQrStopButton {flex:1;}
}
@media(prefers-reduced-motion:reduce){#logisticsContent .sari-logistics-scanner * {transition:none !important;}}
/* Seller Orders typography and controls; scoped to the Logistics scanner only. */
#logisticsContent .sari-logistics-ui.sari-logistics-scanner { --qr-gold:#C88912; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] { max-width:1520px!important; gap:20px!important; padding-bottom:28px; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > .qr-compact-header .qr-compact-eyebrow { font-size:10.5px!important; font-weight:800!important; letter-spacing:.14em!important; line-height:1.4!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > .qr-compact-header .qr-compact-title { font-size:clamp(27px,2.3vw,31px)!important; font-weight:760!important; line-height:1.08!important; letter-spacing:-.04em!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > .qr-compact-header .qr-compact-summary { font-size:11.5px!important; line-height:1.5!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section:nth-of-type(2) { gap:20px!important; grid-template-columns:minmax(0,1.45fr) minmax(310px,.85fr); }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child { border-radius:18px!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child { padding:21px 23px!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child p:first-child { font-size:18px!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child p:nth-child(2) { font-size:11.5px!important; line-height:1.5!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner :is(#sariQrStartButton,#sariQrStopButton,#sariQrScanAnotherButton,.qr-manual-toggle,#sariQrManualForm button,#sariQrNextLink) { min-height:48px!important; border-radius:14px!important; padding:0 18px!important; font-size:12px!important; font-weight:700!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner #sariQrStartButton { background:#C88912!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner #sariQrStartButton:hover { background:#B47B0E!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:not(:last-child) { border-radius:18px!important; padding:21px 23px!important; box-shadow:0 3px 10px rgba(15,23,42,.035),0 12px 30px rgba(15,23,42,.055)!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:first-child > p:first-child { font-size:18px!important; font-weight:780!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner #sariQrResult { border-radius:14px!important; padding:16px!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner #sariQrResultTitle { font-size:14px!important; line-height:1.4!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner #sariQrResultMessage { font-size:11.5px!important; line-height:1.65!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner #sariQrManualPayload { min-height:120px; border-radius:14px!important; padding:14px!important; font-size:11.5px!important; }
#logisticsContent .sari-logistics-ui.sari-logistics-scanner :is(button,a,textarea):focus-visible { outline:2px solid #C88912!important; outline-offset:3px!important; }
/* Native notification dialog: terminal scan results only; status stays available inline. */
#sariQrNotificationModal { box-sizing:border-box; position:fixed; inset:0; width:min(520px,calc(100vw - 32px)); max-height:calc(100dvh - 32px); margin:auto; padding:0; overflow-y:auto; border:1px solid var(--sari-dark-d8c2716225,#E5E7EB); border-radius:20px; background:var(--sari-dark-950baec030,#FFF); color:var(--sari-dark-e23e64dd1e,#2F3742); font-family:Poppins,ui-sans-serif,system-ui,sans-serif; box-shadow:0 24px 80px rgba(15,23,42,.22); }
#sariQrNotificationModal::backdrop { background:rgba(15,23,42,.48); backdrop-filter:blur(4px); }
#sariQrNotificationModal .qr-note-head { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:18px 22px; border-bottom:1px solid var(--sari-dark-d8c2716225,#E9EDF1); }
#sariQrNotificationModal .qr-note-kicker { margin:0; font-size:10px; line-height:1.4; letter-spacing:.12em; font-weight:800; color:var(--sari-dark-a63aa16d7c,#8793A1); text-transform:uppercase; }
#sariQrNotificationModal .qr-note-close { display:grid; place-items:center; width:34px; height:34px; flex:0 0 34px; padding:0; border:1px solid var(--sari-dark-d8c2716225,#DCE2E8); border-radius:10px; background:var(--sari-dark-950baec030,#FFF); color:var(--sari-dark-e23e64dd1e,#59636E); cursor:pointer; }
#sariQrNotificationModal .qr-note-close svg { width:17px; height:17px; }
#sariQrNotificationModal .qr-note-body { padding:24px 22px; }
#sariQrNotificationModal .qr-note-icon { display:grid; place-items:center; width:48px; height:48px; margin-bottom:16px; border-radius:14px; background:var(--sari-dark-4d21eb669b,#ECFDF3); color:var(--sari-dark-e23e64dd1e,#166534); }
#sariQrNotificationModal[data-tone="error"] .qr-note-icon { background:var(--sari-dark-950baec030,#FEF2F2); color:var(--sari-dark-5300031fd1,#B91C1C); }
#sariQrNotificationModal .qr-note-icon svg { width:25px; height:25px; }
#sariQrNotificationModal h2 { margin:0; font-size:22px; font-weight:750; line-height:1.25; letter-spacing:-.025em; overflow-wrap:anywhere; }
#sariQrNotificationModal .qr-note-message { margin:10px 0 0; font-size:12px; line-height:1.7; color:var(--sari-dark-a63aa16d7c,#667383); overflow-wrap:anywhere; white-space:pre-line; }
#sariQrNotificationModal .qr-note-order { margin-top:20px; padding:14px 16px; border:1px solid var(--sari-dark-d8c2716225,#E5E7EB); border-radius:14px; background:var(--sari-dark-4d21eb669b,#F8FAFC); }
#sariQrNotificationModal .qr-note-order small { font-size:10px; color:var(--sari-dark-a63aa16d7c,#8793A1); }
#sariQrNotificationModal .qr-note-order strong { display:block; margin-top:4px; font-size:13px; overflow-wrap:anywhere; }
#sariQrNotificationModal .qr-note-order p { margin:4px 0 0; font-size:11.5px; color:var(--sari-dark-a63aa16d7c,#667383); }
#sariQrNotificationModal .qr-note-actions { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:10px; padding:16px 22px; border-top:1px solid var(--sari-dark-d8c2716225,#E9EDF1); }
#sariQrNotificationModal .qr-note-actions :is(button,a) { box-sizing:border-box; display:inline-flex; align-items:center; justify-content:center; min-height:44px; padding:0 16px; border:1px solid var(--sari-dark-d8c2716225,#DCE2E8); border-radius:12px; background:var(--sari-dark-950baec030,#FFF); color:var(--sari-dark-e23e64dd1e,#59636E); font:700 11.5px/1.4 Poppins,sans-serif; text-decoration:none; cursor:pointer; }
#sariQrNotificationModal .qr-note-actions .is-primary { background:#C88912; border-color:#C88912; color:#FFF; }
#sariQrNotificationModal .qr-note-actions .is-primary:hover { background:#B47B0E; border-color:#B47B0E; }
#sariQrNotificationModal :is(button,a):focus-visible { outline:2px solid #C88912; outline-offset:3px; }
#sariQrNotificationModal [hidden] { display:none!important; }
@media(max-width:1150px) { #logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > section:nth-of-type(2) { grid-template-columns:minmax(0,1fr)!important; } }
@media(max-width:767px) { #logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > .qr-compact-header .qr-compact-title { font-size:25px!important; } #logisticsContent .sari-logistics-ui.sari-logistics-scanner > [data-sari-qr-scanner] > .qr-compact-header .qr-compact-eyebrow { font-size:9.5px!important; } }
@media(max-width:540px) { #sariQrNotificationModal .qr-note-head { padding:16px; } #sariQrNotificationModal .qr-note-body { padding:20px 16px; } #sariQrNotificationModal .qr-note-actions { padding:16px; } #sariQrNotificationModal .qr-note-actions :is(button,a) { flex:1 1 auto; } #sariQrNotificationModal h2 { font-size:20px; } }
@media(prefers-reduced-motion:reduce) { #sariQrNotificationModal *, #logisticsContent .sari-logistics-scanner * { animation:none!important; transition:none!important; } }

</style>
@endpush

@section('content')
<div class="sari-logistics-ui sari-logistics-scanner">
    @include('shared.parcel-qr-scanner', [
        'scanEndpoint' => route('logistics.parcel-scanner.scan'),
        'backUrl' => route('logistics.dashboard'),
        'backLabel' => 'Logistics Dashboard',
        'roleLabel' => 'Logistics',
        'compactHeader' => true,
        'description' => 'Scan the printed parcel QR to record an authorized Logistics checkpoint.',
    ])
    <dialog id="sariQrNotificationModal" aria-labelledby="sariQrNotificationTitle" aria-describedby="sariQrNotificationMessage">
        <header class="qr-note-head">
            <p class="qr-note-kicker">Parcel scan notification</p>
            <button type="button" class="qr-note-close" data-qr-note-close aria-label="Close scan notification" autofocus>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </header>
        <div class="qr-note-body">
            <span class="qr-note-icon" aria-hidden="true">
                <svg data-qr-note-success viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 12 4 4L19 6"/></svg>
                <svg data-qr-note-error viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" hidden><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 16v1"/></svg>
            </span>
            <h2 id="sariQrNotificationTitle" data-qr-note-title>Parcel scan update</h2>
            <p id="sariQrNotificationMessage" class="qr-note-message" data-qr-note-message></p>
            <div class="qr-note-order" data-qr-note-order hidden>
                <small>Order / checkpoint</small>
                <strong data-qr-note-number></strong>
                <p data-qr-note-stage></p>
            </div>
        </div>
        <footer class="qr-note-actions">
            <button type="button" data-qr-note-close>Close</button>
            <button type="button" data-qr-note-again hidden>Scan another parcel</button>
            <a class="is-primary" data-qr-note-next hidden></a>
        </footer>
    </dialog>
</div>
@endsection

@push('scripts')
<script>
// Visual enhancements only. All scan handling remains in shared.parcel-qr-scanner.
document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('.sari-logistics-scanner [data-sari-qr-scanner]');
    if (!root) return;

    const icon = root.querySelector('#sariQrCameraPlaceholder > div > div:first-child');
    if (icon) {
        icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M4 16v3a1 1 0 0 0 1 1h3M20 16v3a1 1 0 0 1-1 1h-3"/><path d="M8 8h2v2H8zM14 8h2v2h-2zM8 14h2v2H8zM13 13h1m2 0h1m-4 3h3"/></svg>';
    }

    const panel = root.querySelector('section:nth-of-type(2) > div:nth-child(2) > section:nth-of-type(2)');
    if (!panel) return;
    panel.id = 'sariQrFallbackPanel';
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'qr-manual-toggle';
    toggle.setAttribute('aria-controls', panel.id);
    toggle.setAttribute('aria-expanded', 'false');
    toggle.innerHTML = '<span>Use manual QR code instead</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
    panel.parentElement.insertBefore(toggle, panel);
    panel.hidden = true;
    toggle.addEventListener('click', () => {
        const open = panel.hidden;
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        if (open) panel.querySelector('#sariQrManualPayload')?.focus();
    });
});
</script>
<script>
(function () {
    function initScannerNotification() {
        const root = document.querySelector('.sari-logistics-scanner [data-sari-qr-scanner]');
        const dialog = document.getElementById('sariQrNotificationModal');
        if (!root || !dialog || typeof dialog.showModal !== 'function') return;
        const result = root.querySelector('#sariQrResult');
        if (!result) return;
        window.__SARI_QR_NOTE_OBSERVER__?.disconnect();
        window.__SARI_QR_NOTE_ABORT__?.abort();
        const controller = new AbortController();
        window.__SARI_QR_NOTE_ABORT__ = controller;
        const { signal } = controller;
        const title = dialog.querySelector('[data-qr-note-title]');
        const message = dialog.querySelector('[data-qr-note-message]');
        const order = dialog.querySelector('[data-qr-note-order]');
        const number = dialog.querySelector('[data-qr-note-number]');
        const stage = dialog.querySelector('[data-qr-note-stage]');
        const next = dialog.querySelector('[data-qr-note-next]');
        const again = dialog.querySelector('[data-qr-note-again]');
        const successIcon = dialog.querySelector('[data-qr-note-success]');
        const errorIcon = dialog.querySelector('[data-qr-note-error]');
        const scanAnother = root.querySelector('#sariQrScanAnotherButton');

        function closeNotification() { if (dialog.open) dialog.close(); }
        function syncNotification() {
            const tone = result.classList.contains('bg-rose-50') ? 'error'
                : result.classList.contains('bg-emerald-50') ? 'success' : null;
            if (!tone) return;
            dialog.dataset.tone = tone;
            title.textContent = root.querySelector('#sariQrResultTitle')?.textContent || 'Parcel scan update';
            message.textContent = root.querySelector('#sariQrResultMessage')?.textContent || '';
            successIcon.hidden = tone !== 'success';
            errorIcon.hidden = tone !== 'error';
            const originalOrder = root.querySelector('#sariQrOrderBlock');
            order.hidden = !originalOrder || originalOrder.classList.contains('hidden');
            number.textContent = order.hidden ? '' : root.querySelector('#sariQrOrderNumber')?.textContent || '';
            stage.textContent = order.hidden ? '' : root.querySelector('#sariQrStage')?.textContent || '';
            const originalNext = root.querySelector('#sariQrNextLink');
            next.hidden = true;
            next.removeAttribute('href');
            // Only mirror the existing server-provided, same-origin next action.
            if (originalNext && !originalNext.classList.contains('hidden')) {
                try {
                    const url = new URL(originalNext.href, window.location.href);
                    if (url.origin === window.location.origin && ['http:', 'https:'].includes(url.protocol)) {
                        next.href = url.href;
                        next.textContent = originalNext.textContent;
                        next.hidden = false;
                    }
                } catch (error) { /* Keep a malformed next action hidden. */ }
            }
            again.hidden = !scanAnother || scanAnother.classList.contains('hidden') || scanAnother.disabled;
            if (!dialog.open) dialog.showModal();
        }
        dialog.querySelectorAll('[data-qr-note-close]').forEach(button => button.addEventListener('click', closeNotification, { signal }));
        dialog.addEventListener('click', event => {
            if (event.target !== dialog) return;
            const box = dialog.getBoundingClientRect();
            if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) closeNotification();
        }, { signal });
        again.addEventListener('click', () => {
            closeNotification();
            if (scanAnother && !scanAnother.disabled && !scanAnother.classList.contains('hidden')) scanAnother.click();
        }, { signal });
        const observer = new MutationObserver(syncNotification);
        observer.observe(result, { subtree:true, childList:true, characterData:true, attributes:true, attributeFilter:['class','href'] });
        window.__SARI_QR_NOTE_OBSERVER__ = observer;
        syncNotification();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initScannerNotification, { once:true });
    else initScannerNotification();
})();
</script>
@endpush
