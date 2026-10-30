@extends('layouts.logistics')

@section('title', 'Branch Treasury — SARI Logistics')
@section('page-title', 'Branch Treasury')

@section('content')
<div class="sari-logistics-ui sari-logistics-treasury mx-auto max-w-[1600px] space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div class="max-w-4xl">
                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a06b0b]">Physical Cash Custody</p>
                <h2 class="mt-2 text-xl font-bold text-[#211d17]">
                    {{ $facility->is_main_branch ? 'Main Branch Central Treasury' : 'Branch → Main Branch Remittance' }}
                </h2>
                <p class="mt-2 text-[10px] leading-5 text-[#7e756a]">
                    Operating Branch: <strong>{{ $facility->name }}</strong>.
                    @if($facility->is_main_branch)
                        This is the Main Branch. Scan incoming branch treasury QR codes, count the physical cash, and centrally reconcile exact batches before stakeholder wallets are released.
                    @else
                        Verified Rider COD cash is now branch custody. Aggregate eligible Rider cash batches and physically send the full amount to <strong>{{ $mainBranch?->name ?: 'the Main Branch' }}</strong>. Wallets remain pending until the Main Branch verifies the full cash batch.
                    @endif
                </p>
            </div>
            <div class="rounded-xl border border-[#ead39d] bg-[#fff8e8] px-4 py-3 text-right">
                <span class="block text-[7px] font-bold uppercase tracking-[.1em] text-[#9a6b14]">Treasury Role</span>
                <strong class="mt-1 block text-[11px] text-[#211d17]">{{ $facility->is_main_branch ? 'Central Treasury' : 'Branch Custodian' }}</strong>
            </div>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            [$facility->is_main_branch ? 'Incoming branch cash' : 'Cash ready for Main Branch', $facility->is_main_branch ? $stats['submitted'] : $stats['branch_cash_ready']],
            ['In transfer / awaiting receipt', $stats['submitted']],
            ['Centrally verified', $stats['verified']],
            ['Open discrepancy', $stats['discrepancy']],
        ] as [$label,$value])
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
                <p class="text-[8px] uppercase tracking-[.1em] text-[#8a8175]">{{ $label }}</p>
                <p class="mt-2 text-[20px] font-bold text-[#211d17]">₱{{ number_format((float)$value,2) }}</p>
            </article>
        @endforeach
    </section>

    @if(!$facility->is_main_branch)
        <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a06b0b]">Prepare Physical Treasury Batch</p>
                    <h3 class="mt-1 text-[13px] font-bold text-[#211d17]">Verified Rider cash waiting at this branch</h3>
                    <p class="mt-1 text-[9px] text-[#817769]">Select Rider cash batches already counted at {{ $facility->name }}. SARI computes the exact branch cash total and locks each Rider batch to one Main Branch remittance.</p>
                </div>
                <span class="rounded-full bg-[#f7f4ee] px-3 py-2 text-[8px] font-bold text-[#685f53]">{{ $eligibleRiderBatches->count() }} eligible batch(es)</span>
            </div>

            <form method="POST" action="{{ route('logistics.branch-treasury.store') }}" class="mt-5">
                @csrf
                <div class="overflow-x-auto rounded-xl border border-[#eee8de]">
                    <table class="min-w-full text-left text-[9px]">
                        <thead class="bg-[#faf8f4] text-[#776e62]">
                            <tr><th class="p-3">Select</th><th class="p-3">Rider Batch</th><th class="p-3">Rider</th><th class="p-3">Parcels</th><th class="p-3 text-right">Cash at Branch</th></tr>
                        </thead>
                        <tbody class="divide-y divide-[#eee8de]">
                            @forelse($eligibleRiderBatches as $riderBatch)
                                <tr>
                                    <td class="p-3"><input type="checkbox" name="rider_batch_ids[]" value="{{ $riderBatch->id }}" class="h-4 w-4 accent-[#d9930a]"></td>
                                    <td class="p-3 font-semibold text-[#211d17]">{{ $riderBatch->batch_code }}</td>
                                    <td class="p-3">{{ trim(($riderBatch->courier?->first_name ?? '').' '.($riderBatch->courier?->last_name ?? '')) ?: 'Rider' }}</td>
                                    <td class="p-3">{{ $riderBatch->items->count() }}</td>
                                    <td class="p-3 text-right font-semibold">₱{{ number_format((float)($riderBatch->confirmed_amount ?? $riderBatch->expected_amount),2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-8 text-center text-[#817769]">No verified Rider cash is waiting for Main Branch remittance.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
                    <label>
                        <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Branch turnover note (optional)</span>
                        <input name="branch_note" maxlength="1000" value="{{ old('branch_note') }}" class="mt-2 w-full rounded-xl border border-[#ddd4c6] px-3 py-3 text-[10px]" placeholder="Sealed bag / shift / transport note">
                    </label>
                    <button type="submit" {{ $eligibleRiderBatches->isEmpty() ? 'disabled' : '' }} class="rounded-xl bg-[#d9930a] px-5 py-3 text-[9px] font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40">Prepare Main Branch Remittance</button>
                </div>
            </form>
        </section>
    @else
        <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a06b0b]">Central Treasury Camera</p>
                    <h3 class="mt-1 text-[13px] font-bold text-[#211d17]">Scan Branch Remittance QR</h3>
                    <p class="mt-1 text-[8px] text-[#817769]">Scan only when the branch cash physically arrives. The QR opens the batch; it does not confirm receipt until the Main Branch counts the money.</p>
                </div>
                <button id="startBranchTreasuryScanner" type="button" class="rounded-xl bg-[#211d17] px-4 py-3 text-[9px] font-semibold text-white">Open Camera Scanner</button>
            </div>
            <div id="branchTreasuryScannerWrap" class="mt-4 hidden">
                <div id="branchTreasuryQrReader" class="mx-auto max-w-xl overflow-hidden rounded-2xl border border-[#ded6ca] bg-black"></div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                    <p id="branchTreasuryScannerStatus" class="text-[9px] text-[#817769]">Point the camera at the sending branch's SARI treasury QR.</p>
                    <button id="stopBranchTreasuryScanner" type="button" class="rounded-xl border border-[#d8d1c6] bg-white px-4 py-2 text-[9px] font-semibold text-[#211d17]">Close Camera</button>
                </div>
            </div>
        </section>
    @endif

    <section class="space-y-3">
        <div>
            <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a06b0b]">Treasury History</p>
            <h3 class="mt-1 text-[13px] font-bold text-[#211d17]">{{ $facility->is_main_branch ? 'Incoming branch remittances' : 'Remittances sent to Main Branch' }}</h3>
        </div>

        @forelse($batches as $batch)
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-[8px] uppercase tracking-[.1em] text-[#8a8175]">{{ $batch->batch_code }}</p>
                        <h4 class="mt-1 text-[13px] font-bold text-[#211d17]">{{ $batch->origin_name_snapshot }} → {{ $batch->destination_name_snapshot }}</h4>
                        <p class="mt-1 text-[9px] text-[#817769]">{{ $batch->items->count() }} Rider cash batch(es) · Physical cash only</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[8px] uppercase tracking-[.08em] text-[#817769]">Expected cash</span>
                        <strong class="mt-1 block text-[16px]">₱{{ number_format((float)$batch->expected_amount,2) }}</strong>
                        <span class="mt-1 block text-[8px] font-semibold uppercase {{ $batch->status === 'verified' ? 'text-emerald-600' : ($batch->status === 'discrepancy' ? 'text-rose-600' : 'text-[#817769]') }}">{{ $batch->status }}</span>
                    </div>
                </div>

                @if(!$facility->is_main_branch && in_array($batch->status,['submitted','discrepancy'],true))
                    <div class="mt-4 border-t border-[#eee8de] pt-4" data-branch-qr-card>
                        <button type="button" data-show-branch-qr class="rounded-xl bg-[#d9930a] px-4 py-3 text-[9px] font-semibold text-white">Show Branch Remittance QR</button>
                        <div data-branch-qr-panel class="mt-4 hidden rounded-2xl border border-[#ead39d] bg-[#fffaf0] p-5 text-center">
                            <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#9a6b14]">Show this QR at the Main Branch</p>
                            <div class="mx-auto mt-4 h-[220px] w-[220px] bg-white p-2" data-branch-qr data-payload="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('logistics.branch-treasury.show', now()->addHours(24), ['batch' => $batch->id], false) }}"></div>
                            <p class="mt-4 text-[12px] font-bold text-[#211d17]">{{ $batch->batch_code }}</p>
                            <p class="mt-1 text-[9px] text-[#817769]">₱{{ number_format((float)$batch->expected_amount,2) }} · {{ $batch->items->count() }} Rider batch(es)</p>
                            <p class="mt-1 text-[8px] text-[#817769]">{{ $batch->origin_name_snapshot }} → {{ $batch->destination_name_snapshot }}</p>
                        </div>
                    </div>
                @elseif($facility->is_main_branch && in_array($batch->status,['submitted','discrepancy'],true))
                    <div class="mt-4 flex justify-end border-t border-[#eee8de] pt-4">
                        <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('logistics.branch-treasury.show', now()->addHours(24), ['batch' => $batch->id], false) }}" class="rounded-xl bg-[#d9930a] px-4 py-3 text-[9px] font-semibold text-white">Count & Reconcile Cash</a>
                    </div>
                @else
                    <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-[9px] font-semibold text-emerald-700">Central receipt verified {{ $batch->verified_at?->format('M d, Y · h:i A') }} · ₱{{ number_format((float)$batch->confirmed_amount,2) }} confirmed</div>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-[#d8d2c8] bg-white p-10 text-center text-[10px] text-[#817769]">No branch treasury remittance batches yet.</div>
        @endforelse
    </section>

    {{ $batches->links() }}
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
