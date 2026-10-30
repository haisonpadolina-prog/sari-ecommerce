@extends('layouts.logistics')

@section('title', 'COD Cash Remittance — SARI Logistics')
@section('page-title', 'COD Cash Remittance')

@section('content')
<div class="sari-logistics-ui sari-logistics-remittances mx-auto max-w-[1600px] space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="max-w-4xl">
                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a06b0b]">Physical Treasury Control</p>
                <h2 class="mt-2 text-xl font-bold text-[#211d17]">Rider COD Cash Reconciliation</h2>
                <p class="mt-2 text-[10px] leading-5 text-[#7e756a]">
                    COD remittance is physical cash only. This page is scoped to the selected operating branch: {{ $facility->name }}. Scan the Rider's batch QR when the Rider arrives, physically count the cash, and confirm the counted amount. Regular branches hold the verified cash for Main Branch remittance; only central reconciliation releases wallets.
                </p>
            </div>
            <span class="rounded-full border border-[#ead39d] bg-[#fff8e8] px-4 py-2 text-[8px] font-bold uppercase tracking-[.1em] text-[#94640d]">{{ $facility->is_main_branch ? 'Main Branch intake' : 'Branch cash intake' }}</span>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
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

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a06b0b]">Hub Camera Scanner</p>
                <h3 class="mt-1 text-[13px] font-bold text-[#211d17]">Scan Rider Remittance QR</h3>
                <p class="mt-1 text-[8px] text-[#817769]">Use the camera only when the Rider is physically present with the cash. Scanning opens the batch; it does not release any money.</p>
            </div>
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

    <section class="space-y-3">
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
            <div class="rounded-2xl border border-dashed border-[#d8d2c8] bg-white p-10 text-center text-[10px] text-[#817769]">No COD cash remittances prepared yet.</div>
        @endforelse
    </section>

    {{ $batches->links() }}
</div>
@endsection

@push('scripts')
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
