@extends('layouts.logistics')

@section('title', 'Cash Turnover to SARI Admin — SARI Logistics')
@section('page-title', 'SARI Admin Cash Turnover')

@push('styles')
<style>

/* Seller Orders sizing, scoped to this Logistics page. */
@media screen {
#logisticsContent #sariAdminTurnovers {max-width:1520px!important;width:100%;margin-inline:auto;font-family:'Poppins',sans-serif;color:var(--sari-dark-e23e64dd1e,#253244);font-size:12px;}
#logisticsContent #sariAdminTurnovers, #logisticsContent #sariAdminTurnovers * {box-sizing:border-box;}
#logisticsContent #sariAdminTurnovers > :is(section,form), #logisticsContent #sariAdminTurnovers > div > section {border-radius:18px;border-color:var(--sari-dark-d8c2716225,#e5e8ed);background:var(--sari-dark-950baec030,#fff);box-shadow:0 5px 18px rgba(31,41,55,.035);}
#logisticsContent #sariAdminTurnovers :is(section,form).p-5, #logisticsContent #sariAdminTurnovers section.p-6 {padding:22px;}
#logisticsContent #sariAdminTurnovers h2 {font-size:clamp(27px,2.3vw,31px)!important;line-height:1.18!important;font-weight:760;letter-spacing:-.04em;overflow-wrap:anywhere;}
#logisticsContent #sariAdminTurnovers h3 {font-size:16px!important;line-height:1.4;font-weight:700;letter-spacing:-.02em;}
#logisticsContent #sariAdminTurnovers p, #logisticsContent #sariAdminTurnovers label, #logisticsContent #sariAdminTurnovers dl {font-size:11.5px;line-height:1.8;}
#logisticsContent #sariAdminTurnovers p.uppercase {font-size:10.5px;font-weight:800;letter-spacing:.12em;}
#logisticsContent #sariAdminTurnovers .cash-page-header {padding:0 0 8px!important;border:0!important;border-radius:0!important;box-shadow:none!important;background:transparent!important;}
#logisticsContent #sariAdminTurnovers .cash-page-header > p:first-child {font-size:10.5px!important;font-weight:800;letter-spacing:.14em;}
#logisticsContent #sariAdminTurnovers .cash-nav {display:inline-flex;align-items:center;gap:8px;min-height:40px;padding:0;border:0;background:transparent;box-shadow:none;color:var(--sari-dark-a63aa16d7c,#59636e);font-size:12px;font-weight:750;text-decoration:none;}
#logisticsContent #sariAdminTurnovers .cash-nav::after {content:'›';font-size:25px;line-height:1;font-weight:800;}
#logisticsContent #sariAdminTurnovers .cash-nav:hover {color:#c88912;}
#logisticsContent #sariAdminTurnovers .cash-stats {gap:14px!important;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important;}
#logisticsContent #sariAdminTurnovers .cash-stats article {min-height:110px;padding:14px 16px;border:1px solid var(--sari-dark-d8c2716225,#e5e8ed);border-radius:18px;background:var(--sari-dark-950baec030,#fff);}
#logisticsContent #sariAdminTurnovers .cash-stats article p:first-child {font-size:13px;font-weight:600;text-transform:none;letter-spacing:0;}
#logisticsContent #sariAdminTurnovers .cash-stats article p:last-child {font-size:clamp(22px,2vw,28px);font-weight:650;letter-spacing:-.035em;overflow-wrap:anywhere;}
#logisticsContent #sariAdminTurnovers .overflow-x-auto {max-width:100%;overflow-x:auto;}
#logisticsContent #sariAdminTurnovers table {width:100%;min-width:900px;border-collapse:collapse;text-align:left;}
#logisticsContent #sariAdminTurnovers thead {background:var(--sari-dark-4d21eb669b,#f8f9fb);}
#logisticsContent #sariAdminTurnovers th {padding:15px 16px!important;font-size:11px!important;font-weight:700;letter-spacing:.065em;text-transform:uppercase;color:var(--sari-dark-a63aa16d7c,#788392);}
#logisticsContent #sariAdminTurnovers td {padding:17px 16px!important;font-size:12px;line-height:1.55;border-bottom:1px solid var(--sari-dark-d8c2716225,#edf0f3);}
#logisticsContent #sariAdminTurnovers tbody tr:hover {background:var(--sari-dark-4d21eb669b,#fafbfc);}
#logisticsContent #sariAdminTurnovers :is(input:not([type=checkbox]),textarea) {min-height:48px;border-radius:14px;padding:12px 14px;font-size:11.5px;color:var(--sari-dark-e23e64dd1e,#253244);background:var(--sari-dark-950baec030,#fff);border-color:var(--sari-dark-d8c2716225,#dfe4eb);}
#logisticsContent #sariAdminTurnovers textarea {min-height:100px;resize:vertical;}
#logisticsContent #sariAdminTurnovers input[type=checkbox] {width:17px;height:17px;accent-color:#c88912;}
#logisticsContent #sariAdminTurnovers button {min-height:48px;border-radius:14px;font-size:12px;font-weight:700;}
#logisticsContent #sariAdminTurnovers button[type=submit], #logisticsContent #sariAdminTurnovers form > button {background:#c88912;color:#fff;padding:12px 20px;}
#logisticsContent #sariAdminTurnovers button[type=submit]:hover, #logisticsContent #sariAdminTurnovers form > button:hover {background:#b47b0e;}
#logisticsContent #sariAdminTurnovers :is(button,a,input,textarea):focus-visible {outline:2px solid #c88912;outline-offset:3px;}
#logisticsContent #sariAdminTurnovers .cash-pagination {min-height:64px;padding:13px 0;border-top:1px solid var(--sari-dark-d8c2716225,#edf0f3);}
#logisticsContent #sariAdminTurnovers .cash-pagination nav a, #logisticsContent #sariAdminTurnovers .cash-pagination nav span[aria-current] > span {min-width:34px;min-height:34px;border-radius:10px;font-size:11px;}
#logisticsContent #sariAdminTurnovers .cash-pagination nav span[aria-current] > span {background:#c88912;color:white;}
@media(max-width:640px){#logisticsContent #sariAdminTurnovers :is(section,form).p-5,#logisticsContent #sariAdminTurnovers section.p-6{padding:16px;}#logisticsContent #sariAdminTurnovers h2{font-size:25px!important;}#logisticsContent #sariAdminTurnovers .cash-stats{grid-template-columns:1fr;}#logisticsContent #sariAdminTurnovers .cash-stats article{min-height:100px;}}
}


/* Queue card shadows and right-aligned header navigation. */
@media screen {
#logisticsContent #sariAdminTurnovers > section:not(.cash-page-header):not(.cash-stats),
#logisticsContent #sariAdminTurnovers .cash-stats > article {
    border:1px solid var(--sari-dark-d8c2716225,#e5eaf0)!important;
    background:var(--sari-dark-950baec030,#fff)!important;
    box-shadow:0 4px 8px rgba(25,42,65,.025),0 12px 32px rgba(25,42,65,.065)!important;
}
#logisticsContent #sariAdminTurnovers .cash-page-header {
    display:grid!important;grid-template-columns:minmax(0,1fr) auto;
    align-items:start;column-gap:24px;row-gap:0;
}
#logisticsContent #sariAdminTurnovers .cash-page-header > p {grid-column:1;grid-row:1;margin:0;}
#logisticsContent #sariAdminTurnovers .cash-page-header > h2 {grid-column:1;grid-row:2;min-width:0;}
#logisticsContent #sariAdminTurnovers .cash-page-header > .cash-nav {
    grid-column:2;grid-row:1 / 3;justify-self:end;align-self:start;
    margin:0!important;white-space:nowrap;min-height:40px;
}
@media(max-width:640px){
 #logisticsContent #sariAdminTurnovers .cash-page-header {column-gap:12px;}
 #logisticsContent #sariAdminTurnovers .cash-page-header > p {grid-column:1 / -1;}
 #logisticsContent #sariAdminTurnovers .cash-page-header > h2 {grid-column:1 / -1;grid-row:3;margin-top:8px;}
 #logisticsContent #sariAdminTurnovers .cash-page-header > .cash-nav {grid-column:1 / -1;grid-row:2;min-height:34px;margin-top:4px!important;font-size:11px;}
}
}

</style>
@endpush

@section('content')
<div id="sariAdminTurnovers" class="sari-logistics-ui mx-auto max-w-[1600px] space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[11px] font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] text-rose-800">{{ $errors->first() }}</div>
    @endif

    <section class="cash-page-header rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a06b0b]">Logistics Main Branch / Physical Cash</p>
        <h2 class="mt-2 text-xl font-bold text-[#211d17]">SARI Admin Cash Turnover</h2>
        <a href="{{ route('logistics.branch-treasury.index') }}" class="cash-nav mt-3">Back to Branch Treasury</a>
    </section>

    <section class="cash-stats grid gap-3 sm:grid-cols-3">
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
            <p class="mt-2 text-[11px] text-[#7e756a]">Select verified batches for turnover.</p>
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
            <p class="mt-3 text-[10px] text-[#817769]">Total is calculated from selected batches. Admin receipt is confirmed separately.</p>
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
        <div class="cash-pagination mt-4">{{ $batches->links() }}</div>
    </section>
</div>
@endsection
