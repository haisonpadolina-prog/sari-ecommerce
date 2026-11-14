@extends('layouts.logistics')

@section('title', 'Finance & Payouts — SARI Logistics')
@section('page-title', 'Finance & Payouts')

@push('styles')
<style>
/* Consistent Logistics UI proportions; presentation only. */

#logisticsContent #lgFinancePayouts {max-width:1520px;font-family:'Poppins',sans-serif;--fp-surface:var(--sari-dark-950baec030,#fff);--fp-soft:var(--sari-dark-4d21eb669b,#f7f9fc);--fp-line:var(--sari-dark-d8c2716225,#e5eaf0);--fp-muted:var(--sari-dark-a63aa16d7c,#748093);--fp-ink:var(--sari-dark-e23e64dd1e,#253244);color:var(--fp-ink);}
#logisticsContent #lgFinancePayouts > section:not(.grid),#logisticsContent #lgFinancePayouts > div.rounded-2xl,#logisticsContent #lgFinancePayouts > section.grid > :is(div,article) {background:var(--fp-surface)!important;border:1px solid var(--fp-line)!important;border-radius:18px;box-shadow:0 4px 8px rgba(25,42,65,.025),0 12px 32px rgba(25,42,65,.065)!important;}
#logisticsContent #lgFinancePayouts > section.grid {padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important;gap:14px;}
#logisticsContent #lgFinancePayouts > section:first-of-type {background:transparent!important;border:0!important;padding:0 0 8px!important;box-shadow:none!important;}
#logisticsContent #lgFinancePayouts h2 {font-size:clamp(27px,2.3vw,31px)!important;font-weight:760;line-height:1.15;letter-spacing:-.04em;}
#logisticsContent #lgFinancePayouts h3 {font-size:16px;font-weight:700;color:var(--fp-ink)!important;}
#logisticsContent #lgFinancePayouts p {font-size:11.5px;line-height:1.8;color:var(--fp-muted);}
#logisticsContent #lgFinancePayouts article {min-height:110px;padding:14px 16px;}
#logisticsContent #lgFinancePayouts article p:first-child {font-size:12px;font-weight:600;text-transform:none;letter-spacing:0;}
#logisticsContent #lgFinancePayouts article p:last-child {font-size:clamp(20px,1.7vw,27px);font-weight:650;color:var(--fp-ink);overflow-wrap:anywhere;}
#logisticsContent #lgFinancePayouts :is(input,select) {height:48px;border-radius:14px;border-color:var(--fp-line);background:var(--fp-surface);color:var(--fp-ink);padding:12px 14px;font-size:11.5px;}
#logisticsContent #lgFinancePayouts label span {font-size:10.5px;color:var(--fp-muted);}
#logisticsContent #lgFinancePayouts button[type=submit] {min-height:48px;border-radius:14px;font-size:12px;background:#c88912;padding:12px 20px;}
#logisticsContent #lgFinancePayouts .rounded-xl {border-color:var(--fp-line);}
#logisticsContent #lgFinancePayouts [class*="bg-[#f7f4ee]"],#logisticsContent #lgFinancePayouts [class*="bg-[#fffaf0]"] {background:var(--fp-soft)!important;border:1px solid var(--fp-line);}
#logisticsContent #lgFinancePayouts .space-y-2 > div {font-size:11.5px;padding:16px;border-color:var(--fp-line);}
#logisticsContent #lgFinancePayouts .space-y-2 strong {font-weight:650;font-size:12px;}
#logisticsContent #lgFinancePayouts > a {background:transparent!important;border:0!important;border-radius:0!important;color:var(--fp-muted)!important;font-size:12px;font-weight:750;padding:0;min-height:40px;align-items:center;}
#logisticsContent #lgFinancePayouts :is(input,select,button,a):focus-visible {outline:2px solid #c88912;outline-offset:3px;}
@media(max-width:640px){#logisticsContent #lgFinancePayouts h2{font-size:25px!important;}#logisticsContent #lgFinancePayouts article{min-height:100px;}}

</style>
@endpush

@section('content')
<div id="lgFinancePayouts" class="sari-logistics-ui sari-logistics-finance mx-auto max-w-[1600px] space-y-5">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>@endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a06b0b]">Shipping Economics</p>
        <h2 class="mt-2 text-xl font-bold text-[#211d17]">{{ $logistics->displayName() }}</h2>
        <p class="mt-2 max-w-4xl text-[10px] leading-5 text-[#7e756a]">Gross shipping revenue minus pickup Rider and final-mile Rider shares becomes the Logistics margin. COD earnings enter the internal wallet only after Main Branch reconciliation and independent physical cash receipt by SARI Admin. The wallet is not an automatic bank or GCash transfer.</p>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['Gross shipping', data_get($stats, 'gross_shipping', data_get($stats, 'gross', 0))],
            ['Pickup Rider cost', data_get($stats, 'pickup_rider_cost', data_get($stats, 'pickup_cost', 0))],
            ['Last-mile Rider cost', data_get($stats, 'last_mile_rider_cost', data_get($stats, 'last_mile_cost', 0))],
            ['Net Logistics', data_get($stats, 'net_logistics', data_get($stats, 'net', 0))],
            ['Awaiting COD remittance', data_get($stats, 'pending_remittance', data_get($stats, 'pending', 0))],
        ] as [$label,$value])
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm"><p class="text-[8px] uppercase tracking-[.1em] text-[#8a8175]">{{ $label }}</p><p class="mt-2 text-[18px] font-bold text-[#211d17]">₱{{ number_format((float)$value,2) }}</p></article>
        @endforeach
    </section>

    @include('finance.payout-form', ['payoutAction'=>route('logistics.finance.payout'),'wallet'=>$wallet,'payoutTitle'=>'Withdraw Logistics Earnings'])

    <section class="grid gap-5 xl:grid-cols-2">
        <div class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <h3 class="text-[13px] font-bold">Recent Shipping Earnings</h3>
            <div class="mt-4 space-y-2">
                @forelse($earnings as $earning)
                    <div class="grid grid-cols-[1fr_auto] gap-3 rounded-xl border border-[#eee6da] px-4 py-3 text-[9px]"><div><strong>{{ $earning->order?->order_number ?: 'Order' }}</strong><div class="mt-1 text-[#817769]">Shipping ₱{{ number_format((float)$earning->gross_shipping_revenue,2) }} · Riders ₱{{ number_format((float)$earning->pickup_rider_cost + (float)$earning->last_mile_rider_cost,2) }}</div></div><div class="text-right"><strong>₱{{ number_format((float)$earning->net_logistics_amount,2) }}</strong><div class="mt-1 text-[#817769]">{{ strtoupper(str_replace('_',' ',$earning->status)) }}</div></div></div>
                @empty<p class="text-[9px] text-[#817769]">No Logistics earnings yet.</p>@endforelse
            </div>
            <div class="mt-4">{{ $earnings->links() }}</div>
        </div>

        <div class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <h3 class="text-[13px] font-bold">Withdrawal Requests</h3>
            <div class="mt-4 space-y-2">
                @forelse($payoutRequests as $payout)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-[#eee6da] px-4 py-3 text-[9px]"><div><strong>{{ $payout->request_code }}</strong><div class="mt-1 text-[#817769]">{{ $payout->destination_label }}</div></div><div class="text-right"><strong>₱{{ number_format((float)$payout->amount,2) }}</strong><div class="mt-1 text-[#817769]">{{ strtoupper($payout->status) }}</div></div></div>
                @empty<p class="text-[9px] text-[#817769]">No withdrawal requests yet.</p>@endforelse
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-[13px] font-bold">Logistics Wallet Ledger</h3>
            <p class="text-[10px] text-[#817769]">Available ₱{{ number_format((float)$wallet->available_balance,2) }} · Reserved ₱{{ number_format((float)$wallet->reserved_balance,2) }} · Paid ₱{{ number_format((float)$wallet->lifetime_payouts,2) }}</p>
        </div>
        <p class="mt-2 text-[10px] leading-5 text-[#817769]">Credits mean COD cash was reconciled at Main Branch and the full physical turnover was confirmed received by SARI Admin; reserve/payout entries track requested and externally confirmed withdrawals. These are internal accounting movements, not automatic transfers.</p>
        <div class="mt-4 space-y-2">
            @forelse($walletEntries as $entry)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#eee6da] px-4 py-3 text-[10px]">
                    <div><strong>{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</strong><div class="mt-1 text-[#817769]">{{ $entry->occurred_at?->format('M d, Y · h:i A') }} · {{ $entry->description }}</div></div>
                    <div class="text-right"><strong>₱{{ number_format((float)$entry->amount,2) }}</strong><div class="mt-1 text-[#817769]">Available after ₱{{ number_format((float)$entry->available_balance_after,2) }}</div></div>
                </div>
            @empty
                <p class="rounded-xl bg-[#fffaf0] p-4 text-[10px] text-[#817769]">No wallet movements yet. Earnings stay pending until Main Branch COD reconciliation and SARI Admin physical cash receipt.</p>
            @endforelse
        </div>
    </section>

    <a href="{{ route('logistics.remittances.index') }}" class="inline-flex rounded-xl border border-[#d9c8a6] bg-[#fff9ef] px-4 py-3 text-[9px] font-semibold text-[#9b6708]">Open COD Remittance Reconciliation →</a>
</div>
@endsection
