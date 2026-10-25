@extends('layouts.admin')

@section('title', 'Finance Treasury — SARI Admin')
@section('page-title', 'Finance Treasury')

@section('content')
<div class="mx-auto max-w-[1700px] space-y-5">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-semibold text-emerald-700">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] font-semibold text-rose-700">{{ $errors->first() }}</div>@endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a06b0b]">Platform Treasury</p>
                <h2 class="mt-2 text-xl font-bold text-[#211d17]">COD, Earnings & Payout Control</h2>
                <p class="mt-2 max-w-4xl text-[10px] leading-5 text-[#7e756a]">Every peso is tracked from Buyer collection to Rider custody, Logistics remittance verification, wallet release, reservation, and confirmed external payout. Marking a payout paid requires a real external reference.</p>
            </div>
            <div class="rounded-2xl bg-[#211d17] px-6 py-5 text-white">
                <p class="text-[8px] uppercase tracking-[.12em] text-white/60">Platform available</p>
                <p class="mt-1 text-[25px] font-bold">₱{{ number_format((float)$platformWallet->available_balance,2) }}</p>
                <p class="mt-1 text-[8px] text-white/60">Reserved ₱{{ number_format((float)$platformWallet->reserved_balance,2) }}</p>
            </div>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['Commission earned',$commissionStats['earned']],
            ['All wallet available',$walletStats['available']],
            ['All wallet reserved',$walletStats['reserved']],
            ['COD awaiting verification',$remittanceStats['awaiting']],
            ['Lifetime payouts',$walletStats['lifetime_payouts']],
        ] as [$label,$value])
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm"><p class="text-[8px] uppercase tracking-[.1em] text-[#8a8175]">{{ $label }}</p><p class="mt-2 text-[18px] font-bold text-[#211d17]">₱{{ number_format((float)$value,2) }}</p></article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.12em] text-[#a06b0b]">Shipping Earnings Policy</p>
                <h3 class="mt-1 text-[13px] font-bold text-[#211d17]">Pickup + Last-Mile split</h3>
                <p class="mt-1 max-w-3xl text-[9px] leading-5 text-[#817769]">These percentages apply to gross shipping revenue on Logistics-managed delivered parcels. Logistics automatically receives the remainder. Existing completed-order snapshots are not recomputed.</p>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center text-[8px]">
                <div class="rounded-xl bg-blue-50 px-4 py-3 text-blue-700"><span class="block uppercase">Pickup Rider</span><strong class="mt-1 block text-[14px]">{{ number_format($shippingPolicy['pickup_percent'],2) }}%</strong></div>
                <div class="rounded-xl bg-violet-50 px-4 py-3 text-violet-700"><span class="block uppercase">Final Mile</span><strong class="mt-1 block text-[14px]">{{ number_format($shippingPolicy['last_mile_percent'],2) }}%</strong></div>
                <div class="rounded-xl bg-emerald-50 px-4 py-3 text-emerald-700"><span class="block uppercase">Logistics</span><strong class="mt-1 block text-[14px]">{{ number_format($shippingPolicy['logistics_percent'],2) }}%</strong></div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.finance.shipping-policy') }}" class="mt-4 grid gap-3 md:grid-cols-[1fr_1fr_auto]">
            @csrf
            <label class="text-[8px] font-semibold text-[#5f574d]">Pickup Rider %
                <input type="number" name="pickup_percent" min="0" max="100" step="0.01" value="{{ old('pickup_percent',$shippingPolicy['pickup_percent']) }}" required class="mt-1 w-full rounded-xl border border-[#ddd4c6] px-3 py-2 text-[10px]">
            </label>
            <label class="text-[8px] font-semibold text-[#5f574d]">Final-Mile Rider %
                <input type="number" name="last_mile_percent" min="0" max="100" step="0.01" value="{{ old('last_mile_percent',$shippingPolicy['last_mile_percent']) }}" required class="mt-1 w-full rounded-xl border border-[#ddd4c6] px-3 py-2 text-[10px]">
            </label>
            <button type="submit" class="self-end rounded-xl bg-[#211d17] px-5 py-2.5 text-[8px] font-semibold text-white">Save Policy</button>
        </form>
    </section>

    @if($admin->canManagePlatformSettings())
        @include('finance.payout-form', [
            'payoutAction'=>route('admin.finance.platform-payout'),
            'wallet'=>$platformWallet,
            'payoutTitle'=>'Platform Treasury Withdrawal',
            'payoutHelp'=>'Creates an internal treasury withdrawal request. It does not send money automatically; an approved request must later be confirmed with the real bank/e-wallet/cash reference.'
        ])
    @endif

    <section class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div><h3 class="text-[13px] font-bold text-[#211d17]">Payout Queue</h3><p class="mt-1 text-[9px] text-[#817769]">Seller, Logistics, Rider, and platform withdrawal requests.</p></div>
            <div class="flex gap-2 text-[8px]"><span class="rounded-lg bg-amber-50 px-3 py-2 text-amber-700">Pending ₱{{ number_format($payoutStats['pending'],2) }}</span><span class="rounded-lg bg-blue-50 px-3 py-2 text-blue-700">Approved ₱{{ number_format($payoutStats['approved'],2) }}</span><span class="rounded-lg bg-emerald-50 px-3 py-2 text-emerald-700">Paid ₱{{ number_format($payoutStats['paid'],2) }}</span></div>
        </div>

        <div class="mt-4 space-y-3">
            @forelse($payoutRequests as $payout)
                <article class="rounded-2xl border border-[#eee6da] p-4">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-[8px] uppercase tracking-[.1em] text-[#8a8175]">{{ $payout->request_code }} · {{ strtoupper($payout->owner_type) }}</p>
                            <h4 class="mt-1 text-[12px] font-bold">{{ $payout->owner_label }}</h4>
                            <p class="mt-1 text-[8px] text-[#817769]">{{ strtoupper(str_replace('_',' ',$payout->payout_method)) }} · {{ $payout->destination_label }} · {{ $payout->requested_at?->format('M d, Y · h:i A') }}</p>
                            @php($destination = (array) ($payout->destination_details ?? []))
                            <div class="mt-2 rounded-xl border border-[#eee6da] bg-[#faf8f4] px-3 py-2 text-[8px] leading-4 text-[#5f574d]">
                                <span class="font-semibold">Transfer to:</span>
                                {{ $destination['account_name'] ?? '—' }} · {{ $destination['account_reference'] ?? '—' }}
                            </div>
                        </div>
                        <div class="text-right"><p class="text-[18px] font-bold">₱{{ number_format((float)$payout->amount,2) }}</p><p class="mt-1 text-[8px] font-semibold {{ $payout->status === 'paid' ? 'text-emerald-700' : ($payout->status === 'rejected' ? 'text-rose-700' : 'text-amber-700') }}">{{ strtoupper($payout->status) }}</p></div>
                    </div>

                    @if($payout->status === 'pending')
                        <div class="mt-4 grid gap-2 md:grid-cols-2">
                            <form method="POST" action="{{ route('admin.finance.payouts.approve',$payout) }}" class="flex gap-2">@csrf<input name="admin_note" maxlength="1000" placeholder="Optional approval note" class="min-w-0 flex-1 rounded-xl border border-[#ddd4c6] px-3 py-2 text-[9px]"><button class="rounded-xl bg-[#d9930a] px-4 py-2 text-[8px] font-semibold text-white">Approve</button></form>
                            <form method="POST" action="{{ route('admin.finance.payouts.reject',$payout) }}" class="flex gap-2">@csrf<input name="admin_note" required minlength="3" maxlength="1000" placeholder="Required rejection reason" class="min-w-0 flex-1 rounded-xl border border-[#ddd4c6] px-3 py-2 text-[9px]"><button class="rounded-xl bg-rose-600 px-4 py-2 text-[8px] font-semibold text-white">Reject</button></form>
                        </div>
                    @elseif($payout->status === 'approved')
                        <div class="mt-4 grid gap-2 md:grid-cols-2">
                            <form method="POST" action="{{ route('admin.finance.payouts.paid',$payout) }}" class="flex gap-2">@csrf<input name="external_reference" required minlength="3" maxlength="191" placeholder="Real bank/e-wallet/cash reference" class="min-w-0 flex-1 rounded-xl border border-[#ddd4c6] px-3 py-2 text-[9px]"><button class="rounded-xl bg-emerald-600 px-4 py-2 text-[8px] font-semibold text-white">Confirm Paid</button></form>
                            <form method="POST" action="{{ route('admin.finance.payouts.reject',$payout) }}" class="flex gap-2">@csrf<input name="admin_note" required minlength="3" maxlength="1000" placeholder="Cancel reason" class="min-w-0 flex-1 rounded-xl border border-[#ddd4c6] px-3 py-2 text-[9px]"><button class="rounded-xl bg-rose-600 px-4 py-2 text-[8px] font-semibold text-white">Reject / Return Funds</button></form>
                        </div>
                    @elseif($payout->status === 'paid')
                        <p class="mt-3 rounded-xl bg-emerald-50 px-3 py-2 text-[8px] text-emerald-700">External reference: <strong>{{ $payout->external_reference }}</strong></p>
                    @elseif($payout->status === 'rejected')
                        <p class="mt-3 rounded-xl bg-rose-50 px-3 py-2 text-[8px] text-rose-700">{{ $payout->admin_note }}</p>
                    @endif
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-[#d8d2c8] p-8 text-center text-[9px] text-[#817769]">No payout requests yet.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $payoutRequests->links() }}</div>
    </section>

    <section class="grid gap-5 xl:grid-cols-2">
        <div class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm"><h3 class="text-[13px] font-bold">COD Reconciliation Snapshot</h3><div class="mt-4 grid grid-cols-3 gap-3 text-center"><div class="rounded-xl bg-[#f7f4ee] p-4"><p class="text-[7px] uppercase text-[#817769]">Awaiting</p><p class="mt-2 font-bold">₱{{ number_format($remittanceStats['awaiting'],2) }}</p></div><div class="rounded-xl bg-emerald-50 p-4"><p class="text-[7px] uppercase text-emerald-700">Verified</p><p class="mt-2 font-bold">₱{{ number_format($remittanceStats['verified'],2) }}</p></div><div class="rounded-xl bg-rose-50 p-4"><p class="text-[7px] uppercase text-rose-700">Discrepancies</p><p class="mt-2 font-bold">{{ number_format($remittanceStats['discrepancy_count']) }}</p></div></div></div>
        <div class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm"><h3 class="text-[13px] font-bold">Platform Wallet Ledger</h3><div class="mt-4 space-y-2">@forelse($platformEntries as $entry)<div class="flex justify-between gap-3 rounded-xl border border-[#eee6da] px-3 py-3 text-[8px]"><div><strong>{{ strtoupper(str_replace('_',' ',$entry->category)) }}</strong><div class="mt-1 text-[#817769]">{{ $entry->description }}</div></div><div class="text-right font-semibold">{{ $entry->available_delta >= 0 ? '+' : '' }}₱{{ number_format((float)$entry->available_delta,2) }}</div></div>@empty<p class="text-[9px] text-[#817769]">No platform wallet entries yet.</p>@endforelse</div></div>
    </section>
</div>
@endsection
