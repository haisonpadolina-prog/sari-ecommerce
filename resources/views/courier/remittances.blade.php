@extends('layouts.courier')

@section('title', 'COD Cash Remittance')
@section('header-title', 'COD Cash Remittance')
@section('header-subtitle', 'Physical Hub Turnover')

@section('content')
<div class="mx-auto max-w-[1500px] space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-2xl border border-[#e7dfd2] bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="max-w-3xl">
                <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#a06b0b]">COD Cash Custody</p>
                <h2 class="mt-2 text-[18px] font-bold text-[#211d17]">Bring the full Buyer cash to your Logistics hub</h2>
                <p class="mt-2 text-[9px] leading-5 text-[#817769]">
                    COD remittance is <strong>physical cash only</strong>. Do not deduct your Rider earning from the cash.
                    SARI releases your earning separately to your Rider wallet only after Logistics counts and verifies the full batch.
                </p>
            </div>
            <div class="rounded-2xl bg-[#211d17] px-5 py-4 text-white">
                <p class="text-[7px] uppercase tracking-[.12em] text-white/60">Cash currently in custody</p>
                <p class="mt-1 text-[22px] font-bold">₱{{ number_format($expectedTotal, 2) }}</p>
                <p class="mt-1 text-[8px] text-white/60">{{ $eligibleOrders->count() }} delivered COD parcel(s)</p>
            </div>
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-3">
        <article class="rounded-2xl border border-[#e7dfd2] bg-white p-4 shadow-sm">
            <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#8d8375]">1 · Prepare</p>
            <p class="mt-2 text-[11px] font-bold text-[#211d17]">Select delivered COD parcels</p>
            <p class="mt-1 text-[8px] leading-4 text-[#817769]">SARI computes the exact cash total. The Rider cannot edit the required amount.</p>
        </article>
        <article class="rounded-2xl border border-[#e7dfd2] bg-white p-4 shadow-sm">
            <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#8d8375]">2 · Turn over</p>
            <p class="mt-2 text-[11px] font-bold text-[#211d17]">Bring cash + batch QR to the hub</p>
            <p class="mt-1 text-[8px] leading-4 text-[#817769]">Present the QR to the Logistics Manager when physically handing over the money.</p>
        </article>
        <article class="rounded-2xl border border-[#e7dfd2] bg-white p-4 shadow-sm">
            <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#8d8375]">3 · Reconcile</p>
            <p class="mt-2 text-[11px] font-bold text-[#211d17]">Logistics counts the cash</p>
            <p class="mt-1 text-[8px] leading-4 text-[#817769]">Exact cash releases wallets. A shortage or overage remains a discrepancy and releases nothing.</p>
        </article>
    </section>

    <form method="POST" action="{{ route('courier.remittances.store') }}" class="rounded-2xl border border-[#e7dfd2] bg-white p-5 shadow-sm">
        @csrf
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-[13px] font-bold text-[#211d17]">Prepare Physical Cash Batch</h3>
                <p class="mt-1 text-[8px] text-[#817769]">Only delivered, paid COD parcels assigned to you and not previously remitted appear here.</p>
            </div>
            <span class="rounded-full border border-[#efd79f] bg-[#fff8e8] px-3 py-2 text-[8px] font-bold uppercase tracking-[.08em] text-[#9a680b]">Cash only</span>
        </div>

        <div class="mt-4 overflow-x-auto rounded-xl border border-[#eee6da]">
            <table class="min-w-full text-left text-[9px]">
                <thead class="bg-[#faf8f4] text-[#776e62]">
                    <tr><th class="p-3">Select</th><th class="p-3">Order</th><th class="p-3">Buyer</th><th class="p-3">COD Collected</th><th class="p-3">Delivered</th></tr>
                </thead>
                <tbody class="divide-y divide-[#eee6da]">
                @forelse($eligibleOrders as $order)
                    <tr>
                        <td class="p-3"><input type="checkbox" name="order_ids[]" value="{{ $order->id }}" checked></td>
                        <td class="p-3 font-semibold text-[#211d17]">{{ $order->order_number }}</td>
                        <td class="p-3">{{ $order->buyer_name ?: 'Buyer' }}</td>
                        <td class="p-3 font-semibold">₱{{ number_format((float) $order->total, 2) }}</td>
                        <td class="p-3">{{ $order->delivered_at?->format('M d, Y · h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-[#918677]">No COD cash is waiting for physical remittance.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-[1fr_1fr]">
            <div class="rounded-xl border border-[#e8e0d4] bg-[#faf8f4] p-4">
                <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#817769]">System-calculated cash to hand over</p>
                <p class="mt-2 text-[24px] font-bold text-[#211d17]">₱{{ number_format($expectedTotal, 2) }}</p>
                <p class="mt-1 text-[8px] leading-4 text-[#817769]">This amount is computed from the selected COD parcels. Your Rider earnings are not deducted here.</p>
            </div>

            <label class="block">
                <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Physical turnover hub</span>
                <select name="logistics_facility_id" required class="mt-2 w-full rounded-xl border border-[#ddd4c6] bg-white px-3 py-3 text-[10px]">
                    <option value="">Choose the hub where you will bring the cash</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->id }}" @selected(old('logistics_facility_id') == $facility->id)>
                            {{ $facility->name }}{{ $facility->municipality_name ? ' · '.$facility->municipality_name : '' }}
                        </option>
                    @endforeach
                </select>
                @if($facilities->isEmpty())
                    <span class="mt-2 block text-[8px] font-semibold text-rose-600">No active Logistics hub is configured for your provider. Ask Logistics to configure one before remitting cash.</span>
                @endif
            </label>
        </div>

        <label class="mt-4 block">
            <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Rider note (optional)</span>
            <input name="rider_note" maxlength="1000" value="{{ old('rider_note') }}" class="mt-2 w-full rounded-xl border border-[#ddd4c6] px-3 py-3 text-[10px]" placeholder="Optional note about the physical turnover">
        </label>

        <button @disabled($eligibleOrders->isEmpty() || $facilities->isEmpty()) class="mt-4 rounded-xl bg-[#d9930a] px-5 py-3 text-[9px] font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40">
            Prepare Physical Cash Remittance
        </button>
    </form>

    <section class="rounded-2xl border border-[#e7dfd2] bg-white p-5 shadow-sm">
        <div>
            <h3 class="text-[13px] font-bold text-[#211d17]">Recent Cash Remittance Batches</h3>
            <p class="mt-1 text-[8px] text-[#817769]">For open batches, present the QR to Logistics together with the physical cash.</p>
        </div>

        <div class="mt-4 space-y-3">
            @forelse($batches as $batch)
                @php
                    $isOpen = in_array($batch->status, ['submitted', 'discrepancy'], true);
                    $scanUrl = $isOpen
                        ? \Illuminate\Support\Facades\URL::temporarySignedRoute(
                            'logistics.remittances.show',
                            now()->addHours(24),
                            ['batch' => $batch->id],
                            false
                        )
                        : null;
                @endphp
                <article class="rounded-xl border border-[#eee6da] p-4">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <strong class="text-[11px] text-[#211d17]">{{ $batch->batch_code }}</strong>
                                <span class="rounded-full bg-[#f6f1e7] px-2.5 py-1 text-[7px] font-bold uppercase tracking-[.08em] text-[#876a31]">Physical cash</span>
                            </div>
                            <div class="mt-2 text-[8px] leading-4 text-[#817769]">
                                {{ $batch->items_count }} parcel(s)<br>
                                Hub: {{ $batch->hub_name_snapshot ?: $batch->facility?->name ?: 'Logistics Hub' }}
                                @if($batch->hub_address_snapshot)
                                    <br>{{ $batch->hub_address_snapshot }}
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <strong class="text-[15px] text-[#211d17]">₱{{ number_format((float) $batch->expected_amount, 2) }}</strong>
                            <div class="mt-1 text-[8px] font-semibold uppercase tracking-[.08em] {{ $batch->status === 'verified' ? 'text-emerald-600' : ($batch->status === 'discrepancy' ? 'text-rose-600' : 'text-[#817769]') }}">
                                {{ strtoupper($batch->status) }}
                            </div>
                        </div>
                    </div>

                    @if($isOpen && $scanUrl)
                        <div class="mt-4 border-t border-[#eee6da] pt-4">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#d9930a] px-4 py-3 text-[9px] font-bold text-white shadow-sm transition hover:bg-[#c98306] focus:outline-none focus:ring-2 focus:ring-[#d9930a]/30"
                                data-remittance-qr-toggle="{{ $batch->id }}"
                                aria-expanded="false"
                                aria-controls="remittanceQrPanel{{ $batch->id }}"
                            >
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                    <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                                    <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                                    <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                                    <path d="M14 14h3v3h-3zM18 18h3v3h-3zM18 14h3M14 18v3"></path>
                                </svg>
                                <span data-remittance-qr-label>Show Remittance QR</span>
                            </button>

                            <div
                                id="remittanceQrPanel{{ $batch->id }}"
                                class="mt-4 hidden rounded-2xl border-2 border-[#d9930a] bg-[#fffdf8] p-4"
                                data-remittance-qr-panel="{{ $batch->id }}"
                            >
                                <div class="grid gap-5 sm:grid-cols-[190px_1fr] sm:items-center">
                                    <div class="mx-auto">
                                        <div
                                            id="remittanceQr{{ $batch->id }}"
                                            class="grid h-[180px] w-[180px] place-items-center rounded-2xl border border-[#ded5c7] bg-white p-3 shadow-sm"
                                            data-qr-url="{{ $scanUrl }}"
                                            data-qr-rendered="false"
                                            aria-label="Remittance QR for {{ $batch->batch_code }}"
                                        ></div>
                                    </div>

                                    <div>
                                        <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#a06b0b]">Show this QR at the hub</p>
                                        <h4 class="mt-2 text-[16px] font-bold text-[#211d17]">{{ $batch->batch_code }}</h4>

                                        <div class="mt-3 grid gap-2 text-[9px] text-[#655d52] sm:grid-cols-2">
                                            <div class="rounded-xl border border-[#ece3d6] bg-white p-3">
                                                <span class="block text-[7px] font-bold uppercase tracking-[.08em] text-[#938879]">Physical cash</span>
                                                <strong class="mt-1 block text-[16px] text-[#211d17]">₱{{ number_format((float) $batch->expected_amount, 2) }}</strong>
                                            </div>
                                            <div class="rounded-xl border border-[#ece3d6] bg-white p-3">
                                                <span class="block text-[7px] font-bold uppercase tracking-[.08em] text-[#938879]">Parcels in batch</span>
                                                <strong class="mt-1 block text-[16px] text-[#211d17]">{{ $batch->items_count }}</strong>
                                            </div>
                                        </div>

                                        <p class="mt-3 text-[9px] font-semibold text-[#211d17]">{{ $batch->hub_name_snapshot ?: $batch->facility?->name ?: 'Logistics Hub' }}</p>
                                        @if($batch->hub_address_snapshot)
                                            <p class="mt-1 text-[8px] leading-4 text-[#817769]">{{ $batch->hub_address_snapshot }}</p>
                                        @endif

                                        <p class="mt-3 text-[8px] leading-4 text-[#817769]">
                                            Present this QR together with the full physical COD cash. Logistics scans this batch once, then physically counts the money before verification.
                                        </p>

                                        @if($batch->status === 'discrepancy')
                                            <p class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-[8px] font-semibold text-rose-600">
                                                Previous cash count had a discrepancy of ₱{{ number_format(abs((float) $batch->difference_amount), 2) }}. Resolve the exact cash before re-verification.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(!$isOpen)
                        <div class="mt-4 border-t border-[#eee6da] pt-3">
                            <p class="text-[8px] font-semibold text-emerald-600">This remittance batch is already closed. No QR is required.</p>
                        </div>
                    @endif
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-[#e5dccf] p-6 text-center text-[9px] text-[#918677]">No remittance batches yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const renderQr = (node) => {
        if (!node || node.dataset.qrRendered === 'true') {
            return true;
        }

        if (typeof QRCode === 'undefined') {
            node.innerHTML = '<p style="padding:12px;text-align:center;font-size:11px;color:#b42318;line-height:1.45">QR library could not load. Check the internet connection, then reload this page.</p>';
            return false;
        }

        node.innerHTML = '';
        new QRCode(node, {
            text: node.dataset.qrUrl,
            width: 156,
            height: 156,
            colorDark: '#151515',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M,
        });
        node.dataset.qrRendered = 'true';
        return true;
    };

    document.querySelectorAll('[data-remittance-qr-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const batchId = button.dataset.remittanceQrToggle;
            const panel = document.querySelector(`[data-remittance-qr-panel="${batchId}"]`);
            const label = button.querySelector('[data-remittance-qr-label]');

            if (!panel) return;

            const opening = panel.classList.contains('hidden');
            panel.classList.toggle('hidden', !opening);
            button.setAttribute('aria-expanded', opening ? 'true' : 'false');

            if (label) {
                label.textContent = opening ? 'Hide Remittance QR' : 'Show Remittance QR';
            }

            if (opening) {
                renderQr(panel.querySelector('[data-qr-url]'));
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
});
</script>
@endpush
