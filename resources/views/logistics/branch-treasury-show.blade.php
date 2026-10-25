@extends('layouts.logistics')

@section('title', 'Central Cash Reconciliation — SARI Logistics')
@section('page-title', 'Central Cash Reconciliation')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <a href="{{ route('logistics.branch-treasury.index') }}" class="inline-flex text-[9px] font-semibold text-[#8b650e]">← Back to Branch Treasury</a>

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
                <strong>Count the physical branch cash before submitting.</strong> The QR only identifies the batch. Only an exact Main Branch count centrally reconciles the affected orders and releases stakeholder wallets.
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
            <p class="mt-1 text-[9px]">Verified {{ $batch->verified_at?->format('M d, Y · h:i A') }}. Affected wallet releases were processed idempotently.</p>
        </section>
    @endif
</div>
@endsection
