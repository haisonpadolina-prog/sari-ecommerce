@extends('layouts.logistics')

@section('title', 'Cash Turnover to SARI Admin — SARI Logistics')
@section('page-title', 'SARI Admin Cash Turnover')

@section('content')
<div class="sari-logistics-ui mx-auto max-w-[1600px] space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[11px] font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] text-rose-800">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a06b0b]">Logistics Main Branch / Physical Cash</p>
        <h2 class="mt-2 text-xl font-bold text-[#211d17]">Turn Over COD Collections to SARI Admin</h2>
        <p class="mt-2 max-w-4xl text-[11px] leading-6 text-[#7e756a]">
            Main Branch: <strong>{{ $facility->name }}</strong>. Select verified, centrally reconciled Rider COD batches and submit their <strong>full physical cash amount</strong> to SARI Admin. Do not deduct Rider earnings or Logistics commissions. Stakeholder wallet earnings remain pending until an authorized SARI Admin counts and confirms the exact cash amount.
        </p>
        <a href="{{ route('logistics.branch-treasury.index') }}" class="mt-3 inline-block text-[11px] font-semibold text-[#a06b0b] underline">Back to Branch Treasury</a>
    </section>

    <section class="grid gap-3 sm:grid-cols-3">
        @foreach([
            ['Ready for SARI Admin', $stats['ready']],
            ['Awaiting Admin Receipt', $stats['pending']],
            ['Confirmed by Admin', $stats['received']],
        ] as [$label, $amount])
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
                <p class="text-[9px] uppercase tracking-[.1em] text-[#8a8175]">{{ $label }}</p>
                <p class="mt-2 text-xl font-bold text-[#211d17]">₱{{ number_format((float) $amount, 2) }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white shadow-sm">
        <div class="border-b border-[#eee8df] p-5">
            <h3 class="text-[14px] font-bold">Prepare Cash Turnover</h3>
            <p class="mt-2 text-[11px] text-[#7e756a]">Only batches verified at your Main Branch or transferred here through verified Branch Treasury can be selected. Each batch can be submitted once.</p>
        </div>
        <form method="POST" action="{{ route('logistics.admin-turnovers.store') }}" class="p-5">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-[11px]">
                    <thead><tr class="border-b border-[#ece5da] text-[9px] uppercase tracking-[.08em] text-[#8a8175]">
                        <th class="px-3 py-3">Include</th><th class="px-3 py-3">Rider batch</th><th class="px-3 py-3">Source hub</th><th class="px-3 py-3">Central reconciliation</th><th class="px-3 py-3 text-right">Verified amount</th>
                    </tr></thead>
                    <tbody>
                    @forelse($eligibleBatches as $batch)
                        <tr class="border-b border-[#f2ede6]">
                            <td class="px-3 py-3"><input type="checkbox" name="rider_batch_ids[]" value="{{ $batch->id }}" @checked(in_array((string) $batch->id, array_map('strval', old('rider_batch_ids', [])))) aria-label="Include {{ $batch->batch_code }}" class="h-4 w-4 accent-[#d9930a]"></td>
                            <td class="px-3 py-3 font-semibold">{{ $batch->batch_code }}</td>
                            <td class="px-3 py-3">{{ $batch->facility?->name ?? 'Hub' }}</td>
                            <td class="px-3 py-3">{{ $batch->central_reconciled_at?->format('M d, Y h:i A') ?? '—' }}</td>
                            <td class="px-3 py-3 text-right font-bold">₱{{ number_format((float) $batch->confirmed_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-9 text-center text-[#8a8175]">No eligible cash batches. Verify the Rider remittance and Main Branch custody first.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <label class="mt-4 block text-[11px] font-semibold text-[#5f574d]">Physical handover note (optional)
                <textarea name="logistics_note" rows="2" maxlength="1000" class="mt-2 block w-full rounded-xl border border-[#ddd4c6] p-3 text-[11px]" placeholder="Envelope seal / Logistics representative / other handover details">{{ old('logistics_note') }}</textarea>
            </label>
            <p class="mt-3 text-[10px] text-[#817769]">The system computes the exact total from the selected verified batches. This form does not confirm physical receipt by Admin.</p>
            <button type="submit" @disabled($eligibleBatches->isEmpty()) class="mt-4 rounded-xl bg-[#d9930a] px-5 py-3 text-[11px] font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40">Prepare Turnover Slip</button>
        </form>
    </section>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <h3 class="text-[14px] font-bold">Turnover History</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[650px] text-left text-[11px]">
                <thead><tr class="border-b border-[#ece5da] text-[9px] uppercase text-[#8a8175]"><th class="py-3">Reference</th><th class="py-3">Prepared</th><th class="py-3 text-right">Expected</th><th class="py-3">Status</th><th class="py-3">Admin confirmation</th><th class="py-3 text-right">Details</th></tr></thead>
                <tbody>
                @forelse($batches as $turnover)
                    <tr class="border-b border-[#f2ede6]">
                        <td class="py-3 font-semibold">{{ $turnover->turnover_code }}</td>
                        <td class="py-3">{{ $turnover->submitted_at?->format('M d, Y h:i A') }}</td>
                        <td class="py-3 text-right font-bold">₱{{ number_format((float) $turnover->expected_amount, 2) }}</td>
                        <td class="py-3"><span class="rounded-lg px-2 py-1 font-bold {{ $turnover->status === 'received' ? 'bg-emerald-50 text-emerald-700' : ($turnover->status === 'discrepancy' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ strtoupper($turnover->status) }}</span></td>
                        <td class="py-3">{{ $turnover->receivedByAdmin?->name ?? 'Pending Admin count' }}</td>
                        <td class="py-3 text-right"><a class="font-semibold text-[#a06b0b] underline" href="{{ route('logistics.admin-turnovers.show', $turnover) }}">Slip / Receipt</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-[#8a8175]">No Admin turnovers prepared yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $batches->links() }}</div>
    </section>
</div>
@endsection
