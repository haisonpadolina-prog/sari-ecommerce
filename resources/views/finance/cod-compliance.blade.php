@extends('layouts.logistics')
@section('title', 'COD Custody Monitoring — SARI Logistics')
@section('page-title', 'COD Custody Monitoring')

@push('styles')
<style>
.sari-cod-monitor{font-family:Poppins,system-ui,sans-serif;color:#344054;max-width:1500px;margin:0 auto}
.sari-cod-monitor *{box-sizing:border-box}
.sari-cod-monitor .sc-card{border:1px solid #e5e7eb;border-radius:17px;background:white;box-shadow:0 4px 18px rgba(15,23,42,.04)}
.sari-cod-monitor .sc-incident{border:1px solid #f3b5b5;border-radius:15px;background:#fff;padding:18px;margin-top:15px}
.sari-cod-monitor .sc-incident.is-restricted{border-color:#e78a8a}
.sari-cod-monitor .sc-label{font-size:10px;text-transform:uppercase;letter-spacing:.09em;color:#8993a3;font-weight:750}
.sari-cod-monitor .sc-action{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.sari-cod-monitor .sc-action select,.sari-cod-monitor .sc-action input,.sari-cod-monitor .sc-action textarea{border:1px solid #d9dee5;border-radius:10px;padding:10px;font:inherit;font-size:11px;background:#fff;min-width:0}
.sari-cod-monitor .sc-action textarea{flex:1 1 260px;min-height:45px;resize:vertical}
.sari-cod-monitor .sc-action button,.sari-cod-monitor .sc-call{display:inline-flex;align-items:center;justify-content:center;border:1px solid #c88912;background:#c88912;border-radius:10px;color:#fff;text-decoration:none;padding:10px 15px;font-size:11px;font-weight:700;cursor:pointer}
.sari-cod-monitor .sc-call{background:#fff;color:#936716}
.sari-cod-monitor .sc-action button:hover{background:#a9770e}
.sari-cod-monitor .sc-action button:disabled{opacity:.5;cursor:not-allowed}
.sari-cod-monitor summary{cursor:pointer;font-size:11px;color:#7e5a20;font-weight:650}
.sari-cod-monitor .sc-num{font-variant-numeric:tabular-nums}
@media(max-width:700px){.sari-cod-monitor .sc-incident{padding:13px}.sari-cod-monitor .sc-action>*{width:100%}}
</style>
@endpush

@section('content')
<div class="sari-cod-monitor space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div><p class="sc-label" style="color:#a8761e">CASH CONTROL · LOGISTICS MANAGER</p><h1 class="mt-1 text-[26px] font-bold text-slate-800">COD Remittance Monitoring</h1><p class="mt-1 text-[12px] text-slate-500">Cash due by 8:00 PM PHT · review and document before restrictions</p></div>
        <a href="{{ route('logistics.remittances.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[11px] font-semibold text-slate-700">Cash verification →</a>
    </div>
    @if(session('success'))<p class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-[12px] text-emerald-800" role="status">{{ session('success') }}</p>@endif
    @if($errors->any())<p class="rounded-xl border border-red-300 bg-red-50 p-3 text-[12px] text-red-800" role="alert">{{ $errors->first() }}</p>@endif
    <section class="sc-card p-5 {{ $totalCents > 0 ? '!border-red-300 !bg-red-50' : '' }}" role="status">
        <p class="sc-label">Cash overdue · not physically verified</p>
        <p class="mt-2 text-[30px] font-bold sc-num {{ $totalCents > 0 ? 'text-red-700' : 'text-slate-800' }}">₱{{ number_format($totalCents / 100, 2) }}</p>
        <p class="mt-1 text-[12px] text-slate-600">{{ $alerts->filter(fn ($a) => $a['overdue_cents'] > 0)->count() }} Rider(s) overdue. A prepared batch is still outstanding until Logistics verifies the full physical cash amount.</p>
    </section>
    <section class="sc-card p-5">
        <div class="flex flex-wrap justify-between gap-3"><div><h2 class="text-[16px] font-bold">Riders requiring review</h2><p class="mt-1 text-[11px] text-slate-500">Overdue, restricted, or referred for account suspension review</p></div><span class="text-[12px] font-semibold">{{ $alerts->count() }} cases</span></div>
        @forelse($alerts as $entry)
        @php
            $currentCase = $entry['case'];
            $tel = preg_replace('/[^0-9+]/', '', $entry['phone']);
            $canRestrict = $entry['is_review_eligible'];
        @endphp
        <article class="sc-incident {{ $entry['has_restriction'] ? 'is-restricted' : '' }}">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 class="text-[15px] font-bold text-slate-800">{{ $entry['name'] }}</h3>
                    <p class="mt-1 text-[11px] text-slate-500">Rider #{{ $entry['rider_id'] }} · {{ $entry['overdue_count'] }} overdue COD item(s)/batch(es)</p>
                    @if($entry['has_restriction'])<span class="mt-2 inline-block rounded-full bg-red-100 px-3 py-1 text-[10px] font-bold text-red-700">New COD assignments restricted</span>@endif
                    @if($currentCase?->suspension_review_at)<span class="mt-2 inline-block rounded-full bg-amber-100 px-3 py-1 text-[10px] font-semibold text-amber-800">Suspension review requested — not suspended</span>@endif
                </div>
                <div class="text-right"><p class="sc-label">Overdue amount</p><p class="text-[22px] font-bold text-red-700 sc-num">₱{{ number_format($entry['overdue_cents']/100,2) }}</p><p class="mt-1 text-[11px] text-slate-500">{{ number_format($entry['max_overdue_hours'],1) }} h longest overdue</p></div>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                @if($tel !== '')<a class="sc-call" href="tel:{{ $tel }}">Call {{ $entry['phone'] }}</a>@else<span class="text-slate-500">No published contact number</span>@endif
                <span class="text-slate-500">Unbatched cash: ₱{{ number_format($entry['unbatched_cents']/100,2) }}</span>
                <span class="text-slate-500">Total unverified: ₱{{ number_format($entry['all_cents']/100,2) }}</span>
            </div>
            <form method="POST" action="{{ route('logistics.cod-compliance.action', ['rider'=>$entry['rider_id']]) }}" class="sc-action mt-4" data-cod-action>
                @csrf
                <label class="sr-only" for="action-{{ $entry['rider_id'] }}">Action</label>
                <select name="action" id="action-{{ $entry['rider_id'] }}" required>
                    <option value="call_logged">Log phone call</option>
                    <option value="warning">Issue formal warning</option>
                    <option value="extend">Document hub closure / extend deadline</option>
                    @if($canRestrict && !$entry['has_restriction'])<option value="restrict">Restrict NEW COD assignments (after 24h)</option>@endif
                    @if($entry['has_restriction'])<option value="release">Release COD assignment restriction</option><option value="review_suspension">Request full suspension review</option>@endif
                    @if($entry['all_cents'] === 0)<option value="resolve">Close resolved case</option>@endif
                </select>
                <label class="sr-only" for="note-{{ $entry['rider_id'] }}">Documented reason, contact outcome, or Rider explanation</label>
                <textarea name="note" id="note-{{ $entry['rider_id'] }}" placeholder="Required: reason / call result / Rider explanation (8+ characters)" minlength="8" maxlength="1500" required></textarea>
                <label class="hidden" data-cod-extension-label>Extend until (PHT) <input name="extend_until" type="datetime-local" disabled></label>
                <button type="submit">Save action</button>
            </form>
            <details class="mt-4"><summary>View amounts and incident history</summary>
                <div class="mt-2 overflow-x-auto"><table class="w-full text-left text-[11px]"><thead><tr class="bg-slate-50"><th class="p-2">Reference</th><th class="p-2">Source</th><th class="p-2">Amount</th><th class="p-2">Due (PHT)</th></tr></thead><tbody>
                @foreach($entry['entries'] as $line)
                <tr class="border-b border-slate-100"><td class="p-2">{{ $line['reference'] }}</td><td class="p-2">{{ ucfirst($line['source']) }}</td><td class="p-2">₱{{ number_format($line['amount_cents']/100,2) }}</td><td class="p-2">{{ $line['effective_due_at']->format('M d, Y h:i A') }}</td></tr>
                @endforeach
                </tbody></table></div>
                <div class="mt-3 space-y-1">
                    @foreach(($currentCase ? ($events[$currentCase->id] ?? collect()) : collect()) as $event)
                        <p class="rounded-lg bg-slate-50 p-2 text-[10px] text-slate-700"><strong>{{ str_replace('_',' ',ucfirst($event->action)) }}</strong> · {{ \Carbon\Carbon::parse($event->created_at)->setTimezone('Asia/Manila')->format('M d h:i A') }} · {{ $event->note }}</p>
                    @endforeach
                </div>
            </details>
        </article>
        @empty
        <p class="mt-5 rounded-xl bg-slate-50 p-6 text-center text-[12px] text-slate-600">No overdue COD cases at this time.</p>
        @endforelse
    </section>
    <p class="text-[11px] leading-6 text-slate-500">Cash ownership is cleared only after exact Logistics verification. Staff actions are logged and provider-scoped. Suspension review does not deactivate a Rider account; separate HR/legal procedure is required. Cutoff is configurable in config/cod_policy.php. Page reflects data at load time; refresh for updates.</p>
</div>
@endsection
@push('scripts')
<script>
document.querySelectorAll('[data-cod-action]').forEach(function(form){
 const choice=form.querySelector('select[name="action"]');
 const wrap=form.querySelector('[data-cod-extension-label]');
 const input=wrap.querySelector('input');
 const update=()=>{const active=choice.value==='extend';wrap.classList.toggle('hidden',!active);input.disabled=!active;input.required=active;};
 choice.addEventListener('change',update);update();
 form.addEventListener('submit',function(e){if(['restrict','review_suspension'].includes(choice.value) && !confirm('Confirm this documented COD compliance action?'))e.preventDefault();});
});
</script>
@endpush
