@extends('layouts.logistics')

@section('title', 'Central Cash Reconciliation — SARI Logistics')
@section('page-title', 'Central Cash Reconciliation')

@push('styles')
<style>

/* Seller Orders sizing, scoped to this Logistics page. */
@media screen {
#logisticsContent #sariCentralCash {max-width:1520px!important;width:100%;margin-inline:auto;font-family:'Poppins',sans-serif;color:var(--sari-dark-e23e64dd1e,#253244);font-size:12px;}
#logisticsContent #sariCentralCash, #logisticsContent #sariCentralCash * {box-sizing:border-box;}
#logisticsContent #sariCentralCash > :is(section,form), #logisticsContent #sariCentralCash > div > section {border-radius:18px;border-color:var(--sari-dark-d8c2716225,#e5e8ed);background:var(--sari-dark-950baec030,#fff);box-shadow:0 5px 18px rgba(31,41,55,.035);}
#logisticsContent #sariCentralCash :is(section,form).p-5, #logisticsContent #sariCentralCash section.p-6 {padding:22px;}
#logisticsContent #sariCentralCash h2 {font-size:clamp(27px,2.3vw,31px)!important;line-height:1.18!important;font-weight:760;letter-spacing:-.04em;overflow-wrap:anywhere;}
#logisticsContent #sariCentralCash h3 {font-size:16px!important;line-height:1.4;font-weight:700;letter-spacing:-.02em;}
#logisticsContent #sariCentralCash p, #logisticsContent #sariCentralCash label, #logisticsContent #sariCentralCash dl {font-size:11.5px;line-height:1.8;}
#logisticsContent #sariCentralCash p.uppercase {font-size:10.5px;font-weight:800;letter-spacing:.12em;}
#logisticsContent #sariCentralCash .cash-page-header {padding:0 0 8px!important;border:0!important;border-radius:0!important;box-shadow:none!important;background:transparent!important;}
#logisticsContent #sariCentralCash .cash-page-header > p:first-child {font-size:10.5px!important;font-weight:800;letter-spacing:.14em;}
#logisticsContent #sariCentralCash .cash-nav {display:inline-flex;align-items:center;gap:8px;min-height:40px;padding:0;border:0;background:transparent;box-shadow:none;color:var(--sari-dark-a63aa16d7c,#59636e);font-size:12px;font-weight:750;text-decoration:none;}
#logisticsContent #sariCentralCash .cash-nav::after {content:'›';font-size:25px;line-height:1;font-weight:800;}
#logisticsContent #sariCentralCash .cash-nav:hover {color:#c88912;}
#logisticsContent #sariCentralCash .cash-stats {gap:14px!important;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important;}
#logisticsContent #sariCentralCash .cash-stats article {min-height:110px;padding:14px 16px;border:1px solid var(--sari-dark-d8c2716225,#e5e8ed);border-radius:18px;background:var(--sari-dark-950baec030,#fff);}
#logisticsContent #sariCentralCash .cash-stats article p:first-child {font-size:13px;font-weight:600;text-transform:none;letter-spacing:0;}
#logisticsContent #sariCentralCash .cash-stats article p:last-child {font-size:clamp(22px,2vw,28px);font-weight:650;letter-spacing:-.035em;overflow-wrap:anywhere;}
#logisticsContent #sariCentralCash .overflow-x-auto {max-width:100%;overflow-x:auto;}
#logisticsContent #sariCentralCash table {width:100%;min-width:900px;border-collapse:collapse;text-align:left;}
#logisticsContent #sariCentralCash thead {background:var(--sari-dark-4d21eb669b,#f8f9fb);}
#logisticsContent #sariCentralCash th {padding:15px 16px!important;font-size:11px!important;font-weight:700;letter-spacing:.065em;text-transform:uppercase;color:var(--sari-dark-a63aa16d7c,#788392);}
#logisticsContent #sariCentralCash td {padding:17px 16px!important;font-size:12px;line-height:1.55;border-bottom:1px solid var(--sari-dark-d8c2716225,#edf0f3);}
#logisticsContent #sariCentralCash tbody tr:hover {background:var(--sari-dark-4d21eb669b,#fafbfc);}
#logisticsContent #sariCentralCash :is(input:not([type=checkbox]),textarea) {min-height:48px;border-radius:14px;padding:12px 14px;font-size:11.5px;color:var(--sari-dark-e23e64dd1e,#253244);background:var(--sari-dark-950baec030,#fff);border-color:var(--sari-dark-d8c2716225,#dfe4eb);}
#logisticsContent #sariCentralCash textarea {min-height:100px;resize:vertical;}
#logisticsContent #sariCentralCash input[type=checkbox] {width:17px;height:17px;accent-color:#c88912;}
#logisticsContent #sariCentralCash button {min-height:48px;border-radius:14px;font-size:12px;font-weight:700;}
#logisticsContent #sariCentralCash button[type=submit], #logisticsContent #sariCentralCash form > button {background:#c88912;color:#fff;padding:12px 20px;}
#logisticsContent #sariCentralCash button[type=submit]:hover, #logisticsContent #sariCentralCash form > button:hover {background:#b47b0e;}
#logisticsContent #sariCentralCash :is(button,a,input,textarea):focus-visible {outline:2px solid #c88912;outline-offset:3px;}
#logisticsContent #sariCentralCash .cash-pagination {min-height:64px;padding:13px 0;border-top:1px solid var(--sari-dark-d8c2716225,#edf0f3);}
#logisticsContent #sariCentralCash .cash-pagination nav a, #logisticsContent #sariCentralCash .cash-pagination nav span[aria-current] > span {min-width:34px;min-height:34px;border-radius:10px;font-size:11px;}
#logisticsContent #sariCentralCash .cash-pagination nav span[aria-current] > span {background:#c88912;color:white;}
@media(max-width:640px){#logisticsContent #sariCentralCash :is(section,form).p-5,#logisticsContent #sariCentralCash section.p-6{padding:16px;}#logisticsContent #sariCentralCash h2{font-size:25px!important;}#logisticsContent #sariCentralCash .cash-stats{grid-template-columns:1fr;}#logisticsContent #sariCentralCash .cash-stats article{min-height:100px;}}
}

</style>
@endpush

@section('content')
<div id="sariCentralCash" class="sari-logistics-ui sari-logistics-cash-detail mx-auto max-w-5xl space-y-5">
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <a href="{{ route('logistics.branch-treasury.index') }}" class="cash-nav">Branch Treasury</a>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#a06b0b]">Branch → Main Physical Cash Batch</p>
                <h2 class="mt-2 text-[20px] font-bold text-[#211d17]">{{ $batch->batch_code }}</h2>
                <p class="mt-2 text-[9px] leading-5 text-[#817769]">
                    From: <strong>{{ $batch->origin_name_snapshot }}</strong><br>
                    To: <strong>{{ $batch->destination_name_snapshot }}</strong><br>
                    Current operating branch: <strong>{{ $facility->name }}</strong>
                </p>
            </div>
            <div class="rounded-2xl bg-[#211d17] px-5 py-4 text-right text-white">
                <p class="text-[7px] uppercase tracking-[.12em] text-white/60">Expected physical cash</p>
                <p class="mt-1 text-[24px] font-bold">₱{{ number_format((float)$batch->expected_amount,2) }}</p>
                <p class="mt-1 text-[8px] text-white/60">{{ $batch->items->count() }} Rider batch(es)</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <h3 class="text-[13px] font-bold text-[#211d17]">Included Rider cash batches</h3>
        <div class="mt-4 overflow-x-auto rounded-xl border border-[#eee8de]">
            <table class="min-w-full text-left text-[9px]">
                <thead class="bg-[#faf8f4] text-[#776e62]"><tr><th class="p-3">Rider Batch</th><th class="p-3">Rider</th><th class="p-3">Parcels</th><th class="p-3 text-right">Branch Cash</th></tr></thead>
                <tbody class="divide-y divide-[#eee8de]">
                    @foreach($batch->items as $item)
                        @php($riderBatch = $item->codRemittanceBatch)
                        <tr>
                            <td class="p-3 font-semibold text-[#211d17]">{{ $riderBatch?->batch_code }}</td>
                            <td class="p-3">{{ trim(($riderBatch?->courier?->first_name ?? '').' '.($riderBatch?->courier?->last_name ?? '')) ?: 'Rider' }}</td>
                            <td class="p-3">{{ $riderBatch?->items?->count() ?? 0 }}</td>
                            <td class="p-3 text-right font-semibold">₱{{ number_format((float)$item->expected_amount,2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @if(in_array($batch->status,['submitted','discrepancy'],true))
        <form method="POST" action="{{ route('logistics.branch-treasury.verify',$batch) }}" class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            @csrf
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[9px] leading-5 text-amber-800">
                <strong>Count the physical branch cash before submitting.</strong> The QR only identifies the batch. An exact Main Branch count reconciles Logistics custody only; stakeholder wallets remain pending until SARI Admin confirms physical cash receipt.
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label>
                    <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Physical cash actually counted</span>
                    <input name="confirmed_amount" type="number" step="0.01" min="0" required value="{{ old('confirmed_amount', number_format((float)$batch->expected_amount,2,'.','')) }}" class="mt-2 w-full rounded-xl border border-[#ddd4c6] px-3 py-3 text-[12px] font-semibold">
                </label>
                <label>
                    <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Central reconciliation note (optional)</span>
                    <input name="main_branch_note" maxlength="1000" value="{{ old('main_branch_note') }}" class="mt-2 w-full rounded-xl border border-[#ddd4c6] px-3 py-3 text-[10px]" placeholder="Cash bag / count / discrepancy note">
                </label>
            </div>

            @if($batch->status === 'discrepancy')
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[9px] font-semibold text-rose-700">
                    Previous count: ₱{{ number_format((float)$batch->confirmed_amount,2) }} · {{ (float)$batch->difference_amount < 0 ? 'Short' : 'Over' }} ₱{{ number_format(abs((float)$batch->difference_amount),2) }}. No affected wallets were released.
                </div>
            @endif

            <button class="mt-5 rounded-xl bg-[#d9930a] px-5 py-3 text-[9px] font-semibold text-white">Confirm Main Branch Cash Count</button>
        </form>
    @else
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-800">
            <p class="text-[9px] font-bold uppercase tracking-[.1em]">Centrally verified</p>
            <p class="mt-2 text-[18px] font-bold">₱{{ number_format((float)$batch->confirmed_amount,2) }}</p>
            <p class="mt-1 text-[9px]">Verified {{ $batch->verified_at?->format('M d, Y · h:i A') }}. The cash is ready for Logistics to SARI Admin turnover. Wallet earnings are still pending.</p>
        </section>
    @endif
</div>
@endsection
