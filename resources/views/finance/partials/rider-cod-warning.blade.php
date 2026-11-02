@php
    $sariRiderCodSummary = app(\App\Services\Finance\CodCustodyMonitor::class)->rider($rider);
@endphp
@php
    $sariCodCase = $sariRiderCodSummary['case'];
    $sariLastWarning = $sariCodCase
        ? \Illuminate\Support\Facades\DB::table('cod_custody_case_events')
            ->where('cod_custody_case_id', $sariCodCase->id)
            ->where('action', 'warning')->latest()->first()
        : null;
@endphp
@if($sariRiderCodSummary['overdue_cents'] > 0 || $sariRiderCodSummary['has_restriction'])
    <section class="rounded-2xl border border-red-300 bg-red-50 p-4" role="alert">
        <strong class="text-[13px] text-red-800">COD cash remittance {{ $sariRiderCodSummary['has_restriction'] ? '— new COD work restricted' : 'is overdue' }}</strong>
        @if($sariLastWarning)
            <p class="mt-2 text-[11px] font-semibold text-red-800">Formal COD warning recorded {{ \Carbon\Carbon::parse($sariLastWarning->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}. Contact your Logistics Manager if you need to explain or dispute the amount.</p>
        @endif
        <p class="mt-2 text-[11px] leading-6 text-red-700">
            ₱{{ number_format($sariRiderCodSummary['overdue_cents'] / 100, 2) }} overdue across {{ $sariRiderCodSummary['overdue_count'] }} item(s)/batch(es).
            Bring the FULL cash to your assigned Logistics hub for physical verification. Preparing a QR batch is not the same as handing over cash.
            Your remittance access stays available even while new COD assignments are restricted.
        </p>
    </section>
@elseif($sariRiderCodSummary['all_cents'] > 0)
    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4" role="status">
        <strong class="text-[12px] text-amber-900">Same-day COD cash reminder</strong>
        <p class="mt-1 text-[11px] text-amber-800">₱{{ number_format($sariRiderCodSummary['all_cents']/100, 2) }} still needs physical cash verification. Standard cutoff: 8:00 PM Philippine time; after-cutoff deliveries are due the next day unless Logistics documents a different hub closure arrangement.</p>
    </section>
@endif
