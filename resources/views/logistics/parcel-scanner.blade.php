@extends('layouts.logistics')

@section('title', 'Parcel QR Scanner — SARI Logistics')
@section('page-title', 'Parcel QR Scanner')

@push('styles')
<style>
/* Logistics-only presentation. The reusable scanner and its scan API are not modified. */
#logisticsContent .sari-logistics-scanner { --qr-ink:#252e38; --qr-muted:#8793a1; --qr-border:#e4e9ef; --qr-gold:#d9950b; --qr-surface:#fff; }
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
  padding:0 !important; color:#af7714 !important; font-size:10px !important;
  font-weight:800; letter-spacing:.13em;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child h2 {
  margin-top:6px; font-size:clamp(26px,2.3vw,31px) !important;
  font-weight:760 !important; line-height:1.12 !important;
  letter-spacing:-.035em !important; color:var(--qr-ink) !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child p {
  margin-top:7px; max-width:720px; font-size:11.5px !important; line-height:1.65 !important;
  color:#7a8591 !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child a {
  min-height:39px; flex-shrink:0; border:1px solid #e0e5eb !important; border-radius:11px;
  padding-inline:14px; background:white !important; color:#535f6e !important;
  font-size:11px; box-shadow:none !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:first-child a:hover {background:#f7f9fb !important; border-color:#ccd5df !important;}
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
  display:block; color:#a87416 !important; background:none !important;
  border:0 !important; padding:0 !important;
  font-size:10px !important; font-weight:750 !important; letter-spacing:.13em !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-title {
  margin-top:6px !important;
  color:#25303a !important; font-size:clamp(25px,2.3vw,30px) !important;
  line-height:1.13 !important; font-weight:760 !important; letter-spacing:-.035em !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-summary {
  max-width:550px !important; margin-top:6px !important;
  font-size:12px !important; line-height:1.5 !important; color:#7a8794 !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back {
  display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; gap:4px;
  padding:5px 0 !important; min-height:32px !important; border:0 !important;
  background:transparent !important; border-radius:0 !important; box-shadow:none !important;
  color:#8a6530 !important; font-size:11px !important; font-weight:700 !important;
  transition:color .12s ease !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back:hover {color:#65502c !important; background:transparent !important;}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > .qr-compact-header .qr-compact-back:focus-visible {outline:2px solid #a5b0bd; outline-offset:4px; border-radius:4px !important;}
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
  min-width:0; overflow:hidden; border:1px solid #303a44 !important;
  border-radius:21px !important; background:#151c24 !important;
  box-shadow:0 4px 14px rgba(15,23,42,.07),0 18px 38px rgba(15,23,42,.11) !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child {
  padding:19px 21px; border-color:rgba(255,255,255,.09) !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child p:first-child {
  color:#f5f7f9 !important; font-size:14px !important; font-weight:700 !important;
  letter-spacing:-.02em !important; text-transform:none !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:first-child p:nth-child(2) {
  color:#98a6b5 !important; font-size:10.5px !important; margin-top:4px;
}
#logisticsContent .sari-logistics-scanner #sariQrCameraBadge {
  padding:7px 11px; font-size:10px; font-weight:650; color:#c9d4df;
  border:1px solid rgba(255,255,255,.13); border-radius:999px;
}
#logisticsContent .sari-logistics-scanner #sariQrCameraBadge.text-emerald-300 {color:#92e7b5;}
#logisticsContent .sari-logistics-scanner #sariQrCameraBadge.text-rose-300 {color:#ffaaa1;}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:nth-child(2),
#logisticsContent .sari-logistics-scanner #sariQrReader {
  min-height:382px !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:nth-child(2) { background:#10161d !important; }
#logisticsContent .sari-logistics-scanner #sariQrReader { background:#10161d; }
#logisticsContent .sari-logistics-scanner #sariQrReader video {object-fit:cover;}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder { padding:18px 22px; background:#10161d; }
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder > div {width:100%; max-width:310px; margin-inline:auto;}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder > div > div:first-child {
  width:70px; height:70px; border:1px solid #3c4855; border-radius:18px;
  background:#202b37; color:#e8edf2;
}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder > div > div:first-child svg {width:35px; height:35px;}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder p:nth-child(2) {
  margin-top:18px; color:#f5f7f9; font-size:16px; font-weight:650;
}
#logisticsContent .sari-logistics-scanner #sariQrCameraPlaceholder p:nth-child(3) {
  margin-top:8px; font-size:11.5px; line-height:1.6; color:#91a0af;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:first-child > div:last-child {
  padding:17px 20px; gap:10px; border-color:rgba(255,255,255,.08) !important;
}
#logisticsContent .sari-logistics-scanner #sariQrStartButton,
#logisticsContent .sari-logistics-scanner #sariQrStopButton,
#logisticsContent .sari-logistics-scanner #sariQrScanAnotherButton {
  min-height:44px; padding:0 17px; border-radius:11px; font-size:11.5px; font-weight:700;
  box-shadow:none; transition:background-color .14s ease,border-color .14s ease;
}
#logisticsContent .sari-logistics-scanner #sariQrStartButton { background:var(--qr-gold) !important; color:#fff !important; }
#logisticsContent .sari-logistics-scanner #sariQrStartButton:hover { background:#c18306 !important; }
#logisticsContent .sari-logistics-scanner #sariQrStopButton { background:#26313d !important; border:1px solid #414e5c !important; color:#e0e8f0 !important; }
#logisticsContent .sari-logistics-scanner #sariQrStopButton:disabled { opacity:.45; cursor:not-allowed; }
#logisticsContent .sari-logistics-scanner #sariQrScanAnotherButton { color:#ffdc92 !important; }
/* White, elevated status card; response colors still come from the original JS. */
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) {min-width:0; display:flex; flex-direction:column; gap:13px;}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section {
  margin:0 !important; border:1px solid var(--qr-border) !important;
  border-radius:19px !important; background:#fff !important;
  box-shadow:0 2px 7px rgba(15,23,42,.03),0 13px 29px rgba(15,23,42,.055) !important;
  padding:20px !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section > p:first-child {
  font-size:12px !important; letter-spacing:0 !important; color:#303b46 !important;
  font-weight:700 !important; text-transform:none !important;
}
#logisticsContent .sari-logistics-scanner #sariQrResult {
  margin-top:14px; border-radius:13px !important; padding:17px !important;
  border:1px solid #e7ebf0; background:#f8fafb;
}
#logisticsContent .sari-logistics-scanner #sariQrResult.bg-emerald-50 {background:#effaf4 !important; border-color:#bfe9cf !important;}
#logisticsContent .sari-logistics-scanner #sariQrResult.bg-rose-50 {background:#fff3f2 !important; border-color:#f2cac5 !important;}
#logisticsContent .sari-logistics-scanner #sariQrResultTitle {font-size:13px !important; font-weight:750;}
#logisticsContent .sari-logistics-scanner #sariQrResultMessage {font-size:11px !important; line-height:1.65 !important;}
#logisticsContent .sari-logistics-scanner #sariQrOrderBlock {border-color:#e1e6eb !important;}
#logisticsContent .sari-logistics-scanner #sariQrOrderNumber {font-size:14px !important; overflow-wrap:anywhere;}
#logisticsContent .sari-logistics-scanner #sariQrStage {font-size:11px !important;}
#logisticsContent .sari-logistics-scanner #sariQrNextLink {border-radius:11px; font-size:11px; min-height:41px;}
/* Secondary manual option - collapsed until requested. */
#logisticsContent .sari-logistics-scanner .qr-manual-toggle {
  width:100%; display:flex; justify-content:space-between; align-items:center;
  gap:12px; min-height:46px; border:1px solid var(--qr-border);
  background:#fff; color:#465361; border-radius:13px;
  padding:0 15px; font-size:11px; font-weight:700;
  text-align:left; box-shadow:0 4px 11px rgba(15,23,42,.025);
}
#logisticsContent .sari-logistics-scanner .qr-manual-toggle:hover {background:#f8fafb; border-color:#d5dce4;}
#logisticsContent .sari-logistics-scanner .qr-manual-toggle svg {width:16px; height:16px; color:#86919d; transition:transform .15s ease;}
#logisticsContent .sari-logistics-scanner .qr-manual-toggle[aria-expanded="true"] svg {transform:rotate(180deg);}
#logisticsContent .sari-logistics-scanner #sariQrFallbackPanel[hidden] {display:none !important;}
#logisticsContent .sari-logistics-scanner #sariQrFallbackPanel > p:nth-child(2) {font-size:10.5px !important; line-height:1.6; color:#7e8996;}
#logisticsContent .sari-logistics-scanner #sariQrManualPayload {
  width:100%; font-size:11px; border:1px solid #dce2e9 !important; border-radius:12px;
  background:#fff !important; color:#2e3944 !important;
}
#logisticsContent .sari-logistics-scanner #sariQrManualPayload:focus {outline:none; border-color:#b1bdc9 !important; box-shadow:0 0 0 3px rgba(96,112,128,.1) !important;}
#logisticsContent .sari-logistics-scanner #sariQrManualForm button {
  min-height:41px; border:1px solid #dce2e9 !important; border-radius:11px;
  background:#f5f7fa !important; color:#394653 !important; font-size:11px;
}
#logisticsContent .sari-logistics-scanner #sariQrManualForm button:hover {background:#eaf0f5 !important;}
/* Replaces the redundant yellow camera warning block with a quiet support note. */
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:last-child {
  border:0 !important; border-radius:0 !important; background:transparent !important;
  box-shadow:none !important; padding:6px 3px !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:last-child > p:first-child {
  color:#7e8996 !important; font-size:10px !important;
}
#logisticsContent .sari-logistics-scanner [data-sari-qr-scanner] > section:nth-of-type(2) > div:nth-child(2) > section:last-child > p:last-child {
  color:#9ba5af !important; font-size:10px !important; line-height:1.6;
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
@endpush
