@php($isAdminView = (bool) ($isAdminView ?? false))
<div class="space-y-5">
    @if(session('success'))<div class="print:hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[11px] text-emerald-800">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="print:hidden rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] text-rose-800">{{ session('warning') }}</div>@endif
    @if($errors->any())<div class="print:hidden rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11px] text-rose-800">{{ $errors->first() }}</div>@endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap justify-between gap-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a06b0b]">SARI · Physical COD Cash Turnover</p>
                <h2 class="mt-2 text-xl font-bold text-[#211d17]">{{ $turnover->turnover_code }}</h2>
                <p class="mt-2 text-[11px] leading-6 text-[#7e756a]">{{ $turnover->status === 'received' ? 'Receipt of full physical cash CONFIRMED by SARI Admin.' : 'Logistics submission ONLY — this is not proof that SARI Admin received cash.' }}</p>
            </div>
            <div class="flex items-start gap-2 print:hidden">
                <button type="button" onclick="window.print()" class="rounded-xl border border-[#ddd4c6] px-4 py-2 text-[11px] font-semibold">Print slip / receipt</button>
                <a href="{{ $isAdminView ? route('admin.finance.cash-turnovers.index') : route('logistics.admin-turnovers.index') }}" class="rounded-xl bg-[#211d17] px-4 py-2 text-[11px] font-semibold text-white">Back to queue</a>
            </div>
        </div>
        <div class="mt-5 grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl bg-[#f7f4ee] p-4"><p class="text-[9px] uppercase text-[#8a8175]">Expected physical cash</p><p class="mt-2 text-xl font-bold">₱{{ number_format((float) $turnover->expected_amount, 2) }}</p></div>
            <div class="rounded-xl bg-[#f7f4ee] p-4"><p class="text-[9px] uppercase text-[#8a8175]">Admin counted</p><p class="mt-2 text-xl font-bold">{{ $turnover->confirmed_amount === null ? 'Not yet counted' : '₱'.number_format((float) $turnover->confirmed_amount, 2) }}</p></div>
            <div class="rounded-xl p-4 {{ $turnover->status === 'received' ? 'bg-emerald-50' : ($turnover->status === 'discrepancy' ? 'bg-rose-50' : 'bg-amber-50') }}"><p class="text-[9px] uppercase text-[#8a8175]">Turnover state</p><p class="mt-2 text-lg font-bold">{{ strtoupper($turnover->status) }}</p></div>
        </div>
        <dl class="mt-5 grid gap-3 text-[11px] sm:grid-cols-2">
            <div><dt class="font-semibold text-[#817769]">Sender</dt><dd>{{ $turnover->logistics?->displayName() ?? 'Logistics #'.$turnover->logistics_account_id }}</dd></div>
            <div><dt class="font-semibold text-[#817769]">Cash originating branch</dt><dd>{{ $turnover->mainFacility?->name ?? '—' }}</dd></div>
            <div><dt class="font-semibold text-[#817769]">Submitted on</dt><dd>{{ $turnover->submitted_at?->format('M d, Y h:i A') }}</dd></div>
            <div><dt class="font-semibold text-[#817769]">Admin confirmed on</dt><dd>{{ $turnover->received_at?->format('M d, Y h:i A') ?? 'Awaiting actual cash receipt' }}</dd></div>
            <div><dt class="font-semibold text-[#817769]">Admin receiver</dt><dd>{{ $turnover->receivedByAdmin?->name ?? 'None yet' }}</dd></div>
            <div><dt class="font-semibold text-[#817769]">Logistics note</dt><dd class="whitespace-pre-wrap">{{ $turnover->logistics_note ?: '—' }}</dd></div>
        </dl>
        @if($turnover->status === 'received')
            <p class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-[11px] font-semibold text-emerald-800">SARI Admin cash receipt recorded. Associated Seller, platform, Logistics and Rider earnings are released to internal wallets (not automatic external transfers).</p>
        @else
            <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-[11px] font-semibold text-amber-800">The Logistics provider is responsible for physical custody until SARI Admin verifies full receipt. No new wallet release is permitted.</p>
        @endif
    </section>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <h3 class="text-[13px] font-bold">Physical Handover Signatures</h3>
        <p class="mt-2 text-[10px] text-[#817769]">For printed physical evidence only. The system's Admin receipt is recorded separately after an independent cash count.</p>
        <div class="mt-8 grid gap-8 sm:grid-cols-2">
            <div class="border-t border-[#a6a09a] pt-2 text-[10px]">Logistics Manager / Authorized Cash Carrier · Signature &amp; Date</div>
            <div class="border-t border-[#a6a09a] pt-2 text-[10px]">SARI Treasury Admin · Signature &amp; Date</div>
        </div>
    </section>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <h3 class="text-[14px] font-bold">Included Verified Rider COD Batches</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[500px] text-left text-[11px]">
                <thead><tr class="border-b border-[#eee8df] text-[9px] uppercase text-[#8a8175]"><th class="py-3">Batch code</th><th class="py-3">Rider</th><th class="py-3">Main Branch reconciled</th><th class="py-3 text-right">Verified cash</th></tr></thead>
                <tbody>
                @foreach($turnover->items as $item)
                    @php($batch = $item->codRemittanceBatch)
                    <tr class="border-b border-[#f2ede6]"><td class="py-3 font-semibold">{{ $batch?->batch_code ?? 'Missing batch' }}</td><td class="py-3">{{ $batch?->courier?->first_name }} {{ $batch?->courier?->last_name }}</td><td class="py-3">{{ $batch?->central_reconciled_at?->format('M d, Y h:i A') ?? '—' }}</td><td class="py-3 text-right font-semibold">₱{{ number_format((float) $item->expected_amount, 2) }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @if($isAdminView && in_array($turnover->status, ['submitted', 'discrepancy'], true))
        <section class="print:hidden rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <h3 class="text-[14px] font-bold">SARI Admin: Physical Cash Count & Receipt</h3>
            <p class="mt-2 text-[11px] leading-6 text-[#7e756a]">Physically count the entire cash envelope. Enter the exact amount actually received. If short or over, log the discrepancy and return the entire envelope to Logistics (no partial receipt). Recount only after the cash issue is resolved. This action releases earnings ONLY on an exact match.</p>
            <form method="POST" action="{{ route('admin.finance.cash-turnovers.receive', $turnover) }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_2fr_auto] sm:items-end">
                @csrf
                <label class="text-[11px] font-semibold">Actual counted cash (PHP)
                    <input type="number" name="confirmed_amount" min="0" max="999999999.99" step="0.01" required value="{{ old('confirmed_amount') }}" class="mt-2 block w-full rounded-xl border border-[#ddd4c6] px-3 py-3 text-[11px]" placeholder="0.00">
                </label>
                <label class="text-[11px] font-semibold">Count notes / discrepancy reason
                    <input type="text" name="admin_note" maxlength="1000" value="{{ old('admin_note') }}" class="mt-2 block w-full rounded-xl border border-[#ddd4c6] px-3 py-3 text-[11px]" placeholder="Required when shortage or excess is recorded">
                </label>
                <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-3 text-[11px] font-semibold text-white" onclick="return confirm('Have you physically counted the cash? This will release wallet earnings ONLY if the count exactly matches the expected amount.')">Record Physical Cash Count</button>
            </form>
        </section>
    @endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <h3 class="text-[14px] font-bold">Immutable Admin Count History</h3>
        <p class="mt-1 text-[10px] text-[#7e756a]">Previous shortages, overages and successful counts remain recorded for accountability.</p>
        <div class="mt-4 space-y-3">
            @forelse($turnover->counts->sortBy('counted_at') as $count)
                <div class="flex flex-wrap justify-between gap-3 rounded-xl border border-[#eee8df] p-4 text-[11px]">
                    <div><p class="font-semibold">{{ $count->admin?->name ?? 'Admin #'.$count->admin_account_id }} · {{ $count->counted_at?->format('M d, Y h:i A') }}</p><p class="mt-1 text-[#7e756a]">{{ $count->note ?: 'No comment' }}</p></div>
                    <div class="text-right"><p class="font-bold">₱{{ number_format((float) $count->confirmed_amount, 2) }}</p><p class="mt-1 {{ $count->result === 'received' ? 'text-emerald-700' : 'text-rose-700' }}">{{ strtoupper($count->result) }} · Difference ₱{{ number_format((float) $count->difference_amount, 2) }}</p></div>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-[#ddd4c6] p-5 text-[11px] text-[#817769]">No Admin physical cash count has been recorded yet.</p>
            @endforelse
        </div>
    </section>
</div>
