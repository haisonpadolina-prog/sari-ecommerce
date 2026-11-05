@extends('layouts.admin')

@section('title', 'Logistics Cash Turnovers — SARI Admin')
@section('page-title', 'Cash Turnovers')

@section('content')
<div class="mx-auto max-w-[1600px] space-y-5">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[11px] font-semibold text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] text-rose-800">{{ $errors->first() }}</div>@endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a06b0b]">SARI Central Treasury</p>
        <h2 class="mt-2 text-xl font-bold">Receive Physical COD Cash from Logistics</h2>
        <p class="mt-2 max-w-4xl text-[11px] leading-6 text-[#7e756a]">Only authorized SARI Admins may count and confirm physical Logistics cash turnover. <strong>Do not click Receive until the actual full cash has arrived and been counted.</strong> A shortage or overage must be logged and the unaccepted envelope returned to Logistics; no partial receipt or wallet release occurs.</p>
        <a href="{{ route('admin.finance.index') }}" class="mt-3 inline-block text-[11px] font-semibold text-[#a06b0b] underline">Finance Treasury</a>
    </section>

    <section class="grid gap-3 sm:grid-cols-3">
        @foreach([
            ['Awaiting physical receipt', $stats['awaiting']],
            ['Confirmed cash received', $stats['received']],
            ['Open discrepancy cases', $stats['discrepancies']],
        ] as [$label, $amount])
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
                <p class="text-[9px] uppercase tracking-[.1em] text-[#8a8175]">{{ $label }}</p>
                <p class="mt-2 text-xl font-bold">{{ $label === 'Open discrepancy cases' ? number_format((int) $amount) : '₱'.number_format((float) $amount, 2) }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <h3 class="text-[14px] font-bold">Logistics Turnover Queue</h3>
        <p class="mt-1 text-[10px] text-[#7e756a]">Review a turnover code, count the cash, and record the actual amount. Open discrepancies are displayed first.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[750px] text-left text-[11px]">
                <thead><tr class="border-b border-[#ece5da] text-[9px] uppercase text-[#8a8175]"><th class="py-3">Turnover</th><th class="py-3">Logistics company</th><th class="py-3">Main Branch</th><th class="py-3 text-right">Expected cash</th><th class="py-3">Status</th><th class="py-3">Submitted</th><th class="py-3 text-right">Action</th></tr></thead>
                <tbody>
                @forelse($batches as $turnover)
                    <tr class="border-b border-[#f2ede6]">
                        <td class="py-3 font-semibold">{{ $turnover->turnover_code }}</td>
                        <td class="py-3">{{ $turnover->logistics?->displayName() ?? 'Logistics #'.$turnover->logistics_account_id }}</td>
                        <td class="py-3">{{ $turnover->mainFacility?->name ?? '—' }}</td>
                        <td class="py-3 text-right font-bold">₱{{ number_format((float) $turnover->expected_amount, 2) }}</td>
                        <td class="py-3"><span class="rounded-lg px-2 py-1 font-bold {{ $turnover->status === 'received' ? 'bg-emerald-50 text-emerald-700' : ($turnover->status === 'discrepancy' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ strtoupper($turnover->status) }}</span></td>
                        <td class="py-3">{{ $turnover->submitted_at?->format('M d, Y h:i A') }}</td>
                        <td class="py-3 text-right"><a href="{{ route('admin.finance.cash-turnovers.show', $turnover) }}" class="font-semibold text-[#a06b0b] underline">{{ $turnover->status === 'received' ? 'View receipt' : 'Count & verify' }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-8 text-center text-[#8a8175]">No Logistics cash turnovers submitted yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $batches->links() }}</div>
    </section>
</div>
@endsection
