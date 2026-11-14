@extends('layouts.logistics')

@section('title', 'COD Cash Remittance — SARI Logistics')
@section('page-title', 'COD Cash Remittance')

@push('styles')
<style>
/* COD Treasury — compact help, minimal camera action, Buyer Cart-style empty state. */
#sariCodRemittances > .cod-page-heading {
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
    flex-wrap: wrap;
    gap: 18px;
    width: 100%;
    padding: 2px 0 12px !important;
    margin: 0 !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    overflow: visible !important;
    position: relative !important;
    z-index: 12;
}
#sariCodRemittances > .cod-page-heading::before,
#sariCodRemittances > .cod-page-heading::after { display: none !important; }
#sariCodRemittances .cod-heading-content { min-width: 0; flex: 1 1 370px; }
#sariCodRemittances .cod-heading-eyebrow {
    display: block;
    margin: 0 0 5px;
    color: #ab771b;
    font-size: 10px;
    line-height: 1.4;
    font-weight: 750;
    text-transform: uppercase;
    letter-spacing: .13em;
}
#sariCodRemittances .cod-heading-title {
    margin: 0;
    color: #222a35;
    font-size: clamp(25px, 2.1vw, 31px);
    font-weight: 750;
    letter-spacing: -.042em;
    line-height: 1.22;
}
#sariCodRemittances .cod-heading-tools {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex: 0 0 auto;
    gap: 10px;
    min-width: 0;
}
#sariCodRemittances .cod-branch-label {
    display: inline-flex;
    align-items: center;
    min-height: 36px;
    padding: 0 12px;
    border: 1px solid #e0e4e9;
    border-radius: 10px;
    background: #f8fafb;
    color: #657487;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .035em;
    text-transform: uppercase;
    white-space: nowrap;
}
#sariCodRemittances .cod-help-trigger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 40px;
    width: 40px;
    height: 40px;
    padding: 0;
    border: 1px solid #dfe4e9;
    border-radius: 12px;
    background: #fff;
    color: #647487;
    box-shadow: 0 2px 7px rgba(15,23,42,.035);
    cursor: pointer;
    transition: background .15s ease, border-color .15s ease;
}
#sariCodRemittances .cod-help-trigger:hover { background: #f3f5f7; border-color: #b9c2ce; }
#sariCodRemittances .cod-help-trigger:focus-visible,
#sariCodRemittances .cod-help-close:focus-visible {
    outline: 3px solid rgba(100,116,139,.32);
    outline-offset: 2px;
}
#sariCodRemittances .cod-help-trigger svg { display:block; width:21px; height:21px; }
/* Dropdown is anchored to the help icon instead of blocking the page. */
#sariCodRemittances .cod-help-anchor { position: relative; flex: 0 0 auto; }
#sariCodRemittances .cod-help-trigger[aria-expanded="true"] {
    background: #f2f5f8;
    color: #253244;
    border-color: #cbd5df;
}
#sariCodRemittances .cod-help-panel {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    z-index: 100;
    box-sizing: border-box;
    width: min(440px, calc(100vw - 36px));
    max-height: min(430px, calc(100dvh - 130px));
    padding: 0;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: none;
    border: 1px solid #e3e8ed;
    border-radius: 16px;
    background: #fff;
    color: #263243;
    box-shadow: 0 18px 50px rgba(19, 31, 46, .15), 0 3px 12px rgba(19, 31, 46, .05);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: translate3d(0, -10px, 0) scale(.98);
    transform-origin: top right;
    transition: opacity .24s ease, transform .3s cubic-bezier(.22, 1, .36, 1), visibility 0s linear .3s;
}
#sariCodRemittances .cod-help-panel::-webkit-scrollbar { display: none; }
#sariCodRemittances .cod-help-panel.is-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: translate3d(0, 0, 0) scale(1);
    transition: opacity .22s ease, transform .3s cubic-bezier(.22, 1, .36, 1), visibility 0s;
}
#sariCodRemittances .cod-help-panel::before {
    content: '';
    position: absolute;
    width: 9px;
    height: 9px;
    top: -5px;
    right: 15px;
    background: #fff;
    border-top: 1px solid #e3e8ed;
    border-left: 1px solid #e3e8ed;
    transform: rotate(45deg);
}
#sariCodRemittances .cod-help-panel-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 19px 20px 14px;
    border-bottom: 1px solid #eef0f2;
}
#sariCodRemittances .cod-help-panel-tag {
    margin: 0 0 5px;
    color: #a87318;
    font-size: 10px;
    line-height: 1.4;
    font-weight: 750;
    letter-spacing: .1em;
    text-transform: uppercase;
}
#sariCodRemittances .cod-help-panel-title {
    margin: 0;
    color: #263243;
    font-size: 17px;
    line-height: 1.4;
    font-weight: 750;
    letter-spacing: -.02em;
}
#sariCodRemittances .cod-help-close {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid #e2e7ec;
    border-radius: 9px;
    background: #fff;
    color: #64748b;
    cursor: pointer;
}
#sariCodRemittances .cod-help-close:hover { background: #f3f5f7; }
#sariCodRemittances .cod-help-copy {
    margin: 0;
    padding: 16px 20px 20px;
    color: #596579;
    font-size: 13px;
    line-height: 1.8;
    overflow-wrap: anywhere;
}
/* Keep only the camera viewport boxed; scanner heading is plain page content. */
#logisticsContent #sariCodRemittances.sari-logistics-remittances > .cod-scanner-section {
    display: block !important;
    padding: 4px 0 6px !important;
    /* Keep the camera action clearly separated from the KPI summary cards. */
    margin: 26px 0 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    overflow: visible !important;
}
#logisticsContent #sariCodRemittances.sari-logistics-remittances > .cod-scanner-section::before,
#logisticsContent #sariCodRemittances.sari-logistics-remittances > .cod-scanner-section::after { display: none !important; }
/* The scanner opens on demand; only its action button is visible at rest. */
#sariCodRemittances .cod-scanner-actions {
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
}
#sariCodRemittances #startRemittanceScanner {
    min-height: 42px;
    padding: 11px 16px;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid #222b36;
    border-radius: 11px;
    background: #222b36;
    color: #fff;
    box-shadow: 0 4px 11px rgba(21,30,41,.1);
    transition: background .16s ease, transform .16s ease;
}
#sariCodRemittances #startRemittanceScanner:hover { background: #334155; }
#sariCodRemittances #remittanceScannerWrap { margin-top: 20px; }
#sariCodRemittances #remittanceScannerStatus { font-size: 12px; line-height: 1.6; }
#sariCodRemittances #stopRemittanceScanner { font-size: 11px; }
/* Explicit responsive KPI grid: avoids inherited grid overrides. */
#sariCodRemittances .cod-kpi-stats {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 12px !important;
    min-width: 0;
}
#sariCodRemittances .cod-kpi-stats > article { min-width: 0; }
#sariCodRemittances .cod-kpi-stats > article p:last-child { overflow-wrap: anywhere; }
/* Don't wrap the empty card in another shared-stylesheet card. */
#logisticsContent #sariCodRemittances.sari-logistics-remittances > .cod-remittance-list {
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    overflow: visible !important;
}
@media (max-width: 900px) {
    #sariCodRemittances .cod-kpi-stats { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
}
@media (max-width: 520px) {
    #sariCodRemittances .cod-kpi-stats > article { padding: 13px !important; }
    #sariCodRemittances .cod-kpi-stats > article p:last-child { font-size: 17px !important; }
}
/* Buyer Cart-inspired empty state — light gray standalone icon, no icon tile. */
#logisticsContent #sariCodRemittances.sari-logistics-remittances .cod-empty-state {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0 !important;
    min-height: 300px;
    padding: 55px 22px !important;
    border: 1px solid #e5e9ed !important;
    border-radius: 18px !important;
    background: #fff !important;
    box-shadow: 0 9px 27px rgba(18, 31, 46, .05) !important;
    text-align: center !important;
}
#sariCodRemittances .cod-empty-icon {
    display: block;
    width: 58px;
    height: 58px;
    margin: 0 auto 18px;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    color: #b3bdc8;
}
#sariCodRemittances .cod-empty-icon svg {
    display: block;
    width: 58px;
    height: 58px;
    fill: none;
    stroke: currentColor;
}
#sariCodRemittances .cod-empty-title {
    margin: 0;
    color: #334155;
    font-size: 22px;
    line-height: 1.35;
    letter-spacing: -.025em;
    font-weight: 750;
}
#sariCodRemittances .cod-empty-description {
    max-width: 410px;
    margin: 8px 0 0;
    color: #94a3b8;
    font-size: 12px;
    line-height: 1.65;
}
@media (max-width: 640px) {
    #sariCodRemittances > .cod-page-heading { gap: 12px; }
    #sariCodRemittances .cod-heading-content { flex-basis: calc(100% - 54px); }
    #sariCodRemittances .cod-heading-tools { flex: 1 1 100%; justify-content: space-between; }
    #sariCodRemittances .cod-heading-title { font-size: 25px; }
    #sariCodRemittances .cod-help-panel { width: min(400px, calc(100vw - 28px)); }
    #sariCodRemittances .cod-help-panel-head { padding: 16px 17px 13px; }
    #sariCodRemittances .cod-help-copy { padding: 15px 17px 18px; font-size: 12px; }
    #logisticsContent #sariCodRemittances.sari-logistics-remittances > .cod-scanner-section {
        margin-top: 22px !important;
    }
    #sariCodRemittances #startRemittanceScanner { width: 100%; }
    #sariCodRemittances .cod-empty-title { font-size: 20px; }
    #sariCodRemittances .cod-empty-icon,
    #sariCodRemittances .cod-empty-icon svg { width: 54px; height: 54px; }
}
@media (prefers-reduced-motion: reduce) {
    #sariCodRemittances .cod-help-trigger,
    #sariCodRemittances .cod-help-panel,
    #sariCodRemittances #startRemittanceScanner { transition: none !important; }
}
</style>
@endpush

@section('content')
<div id="sariCodRemittances" class="sari-logistics-ui sari-logistics-remittances mx-auto max-w-[1600px] space-y-5">
    {{-- SARI_COD_CUSTODY_POLICY_V1 --}}
    @include('finance.partials.cod-overdue-banner')
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    {{-- Plain page heading; instructions are available through the help icon. --}}
    <header class="cod-page-heading">
        <div class="cod-heading-content">
            <p class="cod-heading-eyebrow">Physical Treasury Control</p>
            <h2 class="cod-heading-title">Rider COD Cash Reconciliation</h2>
        </div>
        <div class="cod-heading-tools">
            <span class="cod-branch-label">{{ $facility->is_main_branch ? 'Main Branch intake' : 'Branch cash intake' }}</span>
            <div class="cod-help-anchor" id="codHelpAnchor">
                <button
                    type="button"
                    id="codHelpTrigger"
                    class="cod-help-trigger"
                    aria-label="Show COD cash reconciliation guidance"
                    aria-controls="codHelpPanel"
                    aria-expanded="false"
                    title="How COD reconciliation works"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="9.2" />
                        <path d="M9.5 9a2.65 2.65 0 0 1 5.2.65c0 1.6-2.7 2.1-2.7 3.8" />
                        <path d="M12 17.3h.01" />
                    </svg>
                </button>
                <aside
                    id="codHelpPanel"
                    class="cod-help-panel"
                    role="region"
                    aria-labelledby="codHelpTitle"
                    aria-hidden="true"
                    inert
                >
                    <div class="cod-help-panel-head">
                        <div>
                            <p class="cod-help-panel-tag">Physical Treasury Control</p>
                            <h3 class="cod-help-panel-title" id="codHelpTitle">How COD cash reconciliation works</h3>
                        </div>
                        <button type="button" id="codHelpClose" class="cod-help-close" aria-label="Close COD guidance">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5 5 19" /></svg>
                        </button>
                    </div>
                    <p class="cod-help-copy">COD remittance is physical cash only. This page is scoped to the selected operating branch: {{ $facility->name }}. Scan the Rider's batch QR when the Rider arrives, physically count the cash, and confirm the counted amount. Regular branches hold the verified cash for Main Branch remittance; wallets release only after SARI Admin confirms physical receipt of centrally reconciled cash.</p>
                </aside>
            </div>
        </div>
    </header>

    <section class="cod-kpi-stats grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Awaiting cash turnover', $stats['submitted']],
            ['Verified physical cash', $stats['verified']],
            ['Open discrepancy', $stats['discrepancy']],
            ['Open batches', $stats['pending_count']],
        ] as [$label,$value])
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
                <p class="text-[8px] uppercase tracking-[.1em] text-[#8a8175]">{{ $label }}</p>
                <p class="mt-2 text-[20px] font-bold text-[#211d17]">{{ $label === 'Open batches' ? number_format($value) : '₱'.number_format((float)$value,2) }}</p>
            </article>
        @endforeach
    </section>

    {{-- Scanner button remains available without a permanent heading or description. --}}
    <section class="cod-scanner-section" aria-label="Rider COD remittance camera scanner">
        <div class="cod-scanner-actions">
            <button id="startRemittanceScanner" type="button" class="rounded-xl bg-[#211d17] px-4 py-3 text-[9px] font-semibold text-white">Open Camera Scanner</button>
        </div>
        <div id="remittanceScannerWrap" class="mt-4 hidden">
            <div id="remittanceQrReader" class="mx-auto max-w-xl overflow-hidden rounded-2xl border border-[#ded6ca] bg-black"></div>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                <p id="remittanceScannerStatus" class="text-[9px] text-[#817769]">Point the camera at the Rider's SARI cash-remittance QR.</p>
                <button id="stopRemittanceScanner" type="button" class="rounded-xl border border-[#d8d1c6] bg-white px-4 py-2 text-[9px] font-semibold text-[#211d17]">Close Camera</button>
            </div>
        </div>
    </section>

    <section class="cod-remittance-list space-y-3">
        @forelse($batches as $batch)
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-[8px] uppercase tracking-[.1em] text-[#8a8175]">{{ $batch->batch_code }}</p>
                        <h3 class="mt-1 text-[13px] font-bold">{{ trim(($batch->courier?->first_name ?? '').' '.($batch->courier?->last_name ?? '')) ?: 'Rider' }}</h3>
                        <p class="mt-1 text-[9px] text-[#817769]">
                            {{ $batch->items->count() }} parcel(s) · PHYSICAL CASH<br>
                            Hub: {{ $batch->hub_name_snapshot ?: $batch->facility?->name ?: 'Logistics Hub' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-[8px] uppercase tracking-[.08em] text-[#817769]">Expected physical cash</span>
                        <strong class="mt-1 block text-[16px]">₱{{ number_format((float)$batch->expected_amount,2) }}</strong>
                        <span class="mt-1 block text-[8px] font-semibold uppercase {{ $batch->status === 'verified' ? 'text-emerald-600' : ($batch->status === 'discrepancy' ? 'text-rose-600' : 'text-[#817769]') }}">{{ $batch->status }}</span>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($batch->items as $item)
                        <span class="rounded-lg bg-[#f7f4ee] px-3 py-2 text-[8px] font-semibold">{{ $item->order?->order_number }} · ₱{{ number_format((float)$item->expected_amount,2) }}</span>
                    @endforeach
                </div>

                @if(in_array($batch->status,['submitted','discrepancy'],true))
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-[#eee8de] pt-4">
                        <div class="text-[8px] leading-4 text-[#817769]">
                            @if($batch->status === 'discrepancy')
                                Previous count difference: <strong class="text-rose-600">{{ (float)$batch->difference_amount < 0 ? 'SHORT' : 'OVER' }} ₱{{ number_format(abs((float)$batch->difference_amount),2) }}</strong>
                            @else
                                Waiting for the Rider to physically turn over the full COD cash.
                            @endif
                        </div>
                        <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('logistics.remittances.show', now()->addHours(24), ['batch' => $batch->id], false) }}" class="rounded-xl bg-[#d9930a] px-4 py-3 text-[9px] font-semibold text-white">Count & Verify Cash</a>
                    </div>
                @else
                    <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-[9px] font-semibold text-emerald-700">
                        Physical cash verified {{ $batch->verified_at?->format('M d, Y · h:i A') }} · ₱{{ number_format((float)$batch->confirmed_amount,2) }} confirmed · {{ $batch->central_reconciled_at ? 'centrally reconciled' : 'held by branch pending Main Branch remittance' }}
                    </div>
                @endif
            </article>
        @empty
            <div class="cod-empty-state" role="status">
                <span class="cod-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 64 64" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="8" y="17" width="48" height="37" rx="7"></rect>
                        <path d="M8 26h48M17 17v-6h30v6"></path>
                        <rect x="37" y="32" width="19" height="13" rx="4"></rect>
                        <circle cx="43.5" cy="38.5" r="1.2" fill="currentColor" stroke="none"></circle>
                    </svg>
                </span>
                <h3 class="cod-empty-title">No COD cash remittances prepared yet.</h3>
                <p class="cod-empty-description">Rider cash-remittance batches will appear here when submitted for verification.</p>
            </div>
        @endforelse
    </section>

    {{ $batches->links() }}
</div>
@endsection

@push('scripts')
<script>
// Accessible, lightweight dropdown; does not affect QR/cash controls.
document.addEventListener('DOMContentLoaded', () => {
    const anchor = document.getElementById('codHelpAnchor');
    const trigger = document.getElementById('codHelpTrigger');
    const panel = document.getElementById('codHelpPanel');
    const close = document.getElementById('codHelpClose');
    if (!anchor || !trigger || !panel || !close) return;

    const setOpen = (open, restoreFocus = false) => {
        panel.classList.toggle('is-open', open);
        trigger.setAttribute('aria-expanded', String(open));
        panel.setAttribute('aria-hidden', String(!open));
        panel.inert = !open;
        if (restoreFocus) trigger.focus({ preventScroll: true });
    };

    trigger.addEventListener('click', () => {
        setOpen(trigger.getAttribute('aria-expanded') !== 'true');
    });
    close.addEventListener('click', () => setOpen(false, true));
    document.addEventListener('pointerdown', event => {
        if (trigger.getAttribute('aria-expanded') === 'true' && !anchor.contains(event.target)) {
            setOpen(false);
        }
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
            event.preventDefault();
            setOpen(false, true);
        }
    });
});
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const startButton = document.getElementById('startRemittanceScanner');
    const stopButton = document.getElementById('stopRemittanceScanner');
    const wrap = document.getElementById('remittanceScannerWrap');
    const status = document.getElementById('remittanceScannerStatus');
    let scanner = null;
    let scanning = false;

    const stopScanner = async () => {
        if (scanner && scanning) {
            try { await scanner.stop(); } catch (_) {}
        }
        scanning = false;
        wrap?.classList.add('hidden');
    };

    const handleDecoded = async (decodedText) => {
        try {
            const target = new URL(decodedText, window.location.origin);
            const isSameOrigin = target.origin === window.location.origin;
            const isRemittance = /^\/logistics\/cod-remittance\/\d+$/.test(target.pathname);
            const hasSignature = target.searchParams.has('signature') && target.searchParams.has('expires');

            if (!isSameOrigin || !isRemittance || !hasSignature) {
                status.textContent = 'Invalid SARI remittance QR. Ask the Rider to open the current COD Remittance batch QR.';
                return;
            }

            status.textContent = 'Remittance QR recognized. Opening cash-count screen…';
            await stopScanner();
            window.location.assign(target.toString());
        } catch (_) {
            status.textContent = 'This QR is not a valid SARI COD remittance code.';
        }
    };

    startButton?.addEventListener('click', async () => {
        if (typeof Html5Qrcode === 'undefined') {
            status.textContent = 'Camera scanner could not load. Reload the page and try again.';
            wrap?.classList.remove('hidden');
            return;
        }

        wrap?.classList.remove('hidden');
        status.textContent = 'Requesting camera permission…';

        try {
            scanner = scanner || new Html5Qrcode('remittanceQrReader');
            await scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                handleDecoded,
                () => {}
            );
            scanning = true;
            status.textContent = 'Camera ready. Scan the Rider\'s cash-remittance QR.';
        } catch (error) {
            scanning = false;
            status.textContent = 'Camera unavailable or permission was denied. You can still open the batch from the list below.';
        }
    });

    stopButton?.addEventListener('click', stopScanner);
});
</script>
@endpush
