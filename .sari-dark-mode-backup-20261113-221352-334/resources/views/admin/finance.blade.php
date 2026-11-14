@extends('layouts.admin')

@section('title', 'Finance Treasury — SARI Admin')
@section('page-title', 'Finance Treasury')

@section('content')
<style>
    /* ============================================================
       SARI ADMIN — FINANCE TREASURY
       UI parity with Buyer "Shop Products" catalog.
       VISUAL ONLY: all Blade logic, routes, forms, field names,
       tokens, partials and pagination are unchanged.
       ============================================================ */

    .treasury-page {
        --sh-gold: #c88912;
        --sh-gold-dark: #a96f0e;
        --sh-gold-text: #89580c;
        --sh-ink: #20252b;
        --sh-text: #2e343b;
        --sh-body: #29323b;
        --sh-muted: #707b86;
        --sh-soft: #818995;
        --sh-border: #e5e7eb;
        --sh-control-border: #dfe3e8;
        --sh-divider: #eceef1;
        --sh-row-divider: #eef0f2;
        --sh-shadow:
            0 2px 5px rgba(15, 23, 42, .045),
            0 10px 24px rgba(15, 23, 42, .075),
            0 24px 54px rgba(15, 23, 42, .095),
            inset 0 1px 0 rgba(255, 255, 255, .92);

        width: 100%;
        max-width: 1440px;
        margin-inline: auto;
        margin-top: 0;
        padding: 0 0 34px;
        color: var(--sh-text);
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .treasury-page *,
    .treasury-page *::before,
    .treasury-page *::after { box-sizing: border-box; }

    .treasury-page button,
    .treasury-page input,
    .treasury-page a {
        font-family: inherit;
        transition: color .16s ease, background-color .16s ease, border-color .16s ease, box-shadow .16s ease;
    }

    .treasury-stack > * + * { margin-top: 18px; }

    /* ---------------- Flash messages ---------------- */
    .ft-alert {
        border-radius: 14px;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.5;
    }
    .ft-alert-success { border: 1px solid #d5e6da; background: #f4faf6; color: #36805a; }
    .ft-alert-error { border: 1px solid #f1d3d3; background: #fff5f5; color: #b04545; }

    /* ---------------- Open page header ---------------- */
    .ft-hero { padding: 0 2px 12px; }

    .ft-eyebrow {
        margin: 0;
        color: #a87316;
        font-size: 10.5px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .ft-title {
        margin: 5px 0 0;
        color: var(--sh-text);
        font-size: clamp(29px, 2.35vw, 34px);
        font-weight: 760;
        line-height: 1.04;
        letter-spacing: -.035em;
    }
    .ft-title span { color: var(--sh-gold); }

    .ft-lead {
        max-width: 860px;
        margin: 8px 0 0;
        color: #7d8792;
        font-size: 11.5px;
        font-weight: 450;
        line-height: 1.6;
    }

    /* Platform balance (charcoal, same as active category chip) */
    .ft-balance {
        min-width: 260px;
        border: 1px solid #2f2f2f;
        border-radius: 17px;
        background: linear-gradient(180deg, #363636 0%, #292929 100%);
        padding: 18px 22px;
        color: #fff;
        box-shadow: 0 8px 18px rgba(24, 24, 24, .17), inset 0 1px 0 rgba(255, 255, 255, .06);
    }
    .ft-balance-label {
        margin: 0;
        color: rgba(255, 255, 255, .65);
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .ft-balance-value {
        margin: 6px 0 0;
        font-size: 28px;
        font-weight: 760;
        line-height: 1.05;
        letter-spacing: -.035em;
    }
    .ft-balance-sub { margin: 6px 0 0; color: rgba(255, 255, 255, .65); font-size: 11px; font-weight: 500; }

    /* ---------------- Floating card surface ---------------- */
    .ft-card {
        border: 1px solid var(--sh-border);
        border-radius: 17px;
        background: #fff;
        padding: 20px;
        box-shadow: var(--sh-shadow);
    }

    .ft-card-accent { background: #fffaf1; border-color: #eadfc9; }

    .ft-h3 {
        margin: 0;
        color: var(--sh-ink);
        font-size: 14px;
        font-weight: 750;
        line-height: 1.35;
        letter-spacing: -.015em;
    }

    .ft-muted {
        margin: 4px 0 0;
        color: var(--sh-muted);
        font-size: 11px;
        font-weight: 500;
        line-height: 1.6;
    }

    .ft-kicker {
        margin: 0;
        color: #a87316;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    /* ---------------- KPI cards ---------------- */
    .ft-kpi { padding: 16px 18px; min-height: 96px; }
    .ft-kpi-label {
        margin: 0;
        color: var(--sh-soft);
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .ft-kpi-value {
        margin: 8px 0 0;
        color: var(--sh-ink);
        font-size: 22px;
        font-weight: 760;
        line-height: 1.1;
        letter-spacing: -.03em;
    }

    /* ---------------- Buttons ---------------- */
    .ft-btn {
        display: inline-flex;
        height: 42px;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 0 16px;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        line-height: 1;
        white-space: nowrap;
        cursor: pointer;
        text-decoration: none;
    }
    .ft-btn-dark {
        border-color: #2f2f2f;
        background: linear-gradient(180deg, #363636 0%, #292929 100%);
        box-shadow: 0 8px 18px rgba(24, 24, 24, .17), inset 0 1px 0 rgba(255, 255, 255, .06);
    }
    .ft-btn-dark:hover { background: linear-gradient(180deg, #333 0%, #262626 100%); }
    .ft-btn-gold {
        border-color: #d79a28;
        background: var(--sh-gold);
        box-shadow: 0 5px 12px rgba(200, 137, 18, .14);
    }
    .ft-btn-gold:hover { border-color: var(--sh-gold-dark); background: var(--sh-gold-dark); }
    .ft-btn-green { border-color: #2f9e63; background: #2f9e63; }
    .ft-btn-green:hover { border-color: #268650; background: #268650; }
    .ft-btn-red { border-color: #dc4a4a; background: #dc4a4a; }
    .ft-btn-red:hover { border-color: #c03b3b; background: #c03b3b; }
    .ft-btn:focus-visible { outline: 2px solid rgba(47, 47, 47, .28); outline-offset: 2px; }

    /* ---------------- Inputs ---------------- */
    .ft-label {
        display: block;
        color: #4b5560;
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .045em;
        text-transform: uppercase;
    }

    .ft-input {
        width: 100%;
        min-width: 0;
        height: 42px;
        border: 1px solid var(--sh-control-border);
        border-radius: 10px;
        background: #fff;
        padding: 0 12px;
        color: var(--sh-body);
        font-size: 12px;
        font-weight: 550;
        box-shadow: none;
    }
    .ft-label .ft-input { margin-top: 6px; text-transform: none; letter-spacing: 0; }
    .ft-input::placeholder { color: #79838e; font-size: 11.5px; font-weight: 450; opacity: 1; }
    .ft-input:hover { border-color: #cfd5dc; background: #fbfcfd; }
    .ft-input:focus {
        outline: none;
        border-color: var(--sh-gold);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(200, 137, 18, .08);
    }

    /* ---------------- Tinted pills / tiles ---------------- */
    .ft-pill {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        border-radius: 9px;
        padding: 0 12px;
        font-size: 11px;
        font-weight: 700;
    }
    .ft-pill-amber { background: #fff8e9; color: #a87019; border: 1px solid #efd9b0; }
    .ft-pill-blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #d6e4fb; }
    .ft-pill-green { background: #eef8f1; color: #36805a; border: 1px solid #d6e9dc; }

    .ft-tile {
        border-radius: 12px;
        padding: 12px 16px;
        text-align: center;
        font-size: 10px;
        font-weight: 750;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .ft-tile strong {
        display: block;
        margin-top: 4px;
        font-size: 17px;
        font-weight: 760;
        letter-spacing: -.02em;
        text-transform: none;
    }
    .ft-tile-blue { background: #eff6ff; color: #1d4ed8; }
    .ft-tile-violet { background: #f5f3ff; color: #6d28d9; }
    .ft-tile-green { background: #eef8f1; color: #36805a; }
    .ft-tile-neutral { background: #f8f9fa; color: var(--sh-muted); border: 1px solid var(--sh-row-divider); }
    .ft-tile-neutral strong { color: var(--sh-ink); }

    /* ---------------- Payout rows (flat inside floating card) ---------------- */
    .ft-payout {
        border: 1px solid var(--sh-row-divider);
        border-radius: 14px;
        background: #fff;
        padding: 16px;
    }
    .ft-payout-code {
        margin: 0;
        color: var(--sh-muted);
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .ft-payout-name { margin: 4px 0 0; color: var(--sh-body); font-size: 13px; font-weight: 750; }
    .ft-payout-meta { margin: 4px 0 0; color: var(--sh-muted); font-size: 11px; font-weight: 500; line-height: 1.5; }
    .ft-payout-amount { margin: 0; color: var(--sh-ink); font-size: 20px; font-weight: 760; letter-spacing: -.03em; }
    .ft-payout-status { margin: 4px 0 0; font-size: 10.5px; font-weight: 800; letter-spacing: .06em; }

    .ft-transfer {
        margin-top: 10px;
        border: 1px solid var(--sh-row-divider);
        border-radius: 10px;
        background: #fafbfc;
        padding: 9px 12px;
        color: #3f4852;
        font-size: 11px;
        font-weight: 500;
        line-height: 1.5;
    }
    .ft-transfer span { font-weight: 750; color: var(--sh-ink); }

    .ft-note { margin-top: 12px; border-radius: 10px; padding: 9px 12px; font-size: 11px; font-weight: 500; }
    .ft-note-green { background: #eef8f1; color: #36805a; border: 1px solid #d6e9dc; }
    .ft-note-red { background: #fff5f5; color: #b04545; border: 1px solid #f1d3d3; }

    .ft-empty {
        border: 1px dashed #d9dde2;
        border-radius: 14px;
        padding: 36px 16px;
        color: var(--sh-muted);
        font-size: 11.5px;
        font-weight: 600;
        text-align: center;
    }

    /* ---------------- Ledger rows ---------------- */
    .ft-ledger-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        border: 1px solid var(--sh-row-divider);
        border-radius: 12px;
        padding: 11px 14px;
        font-size: 11px;
        color: var(--sh-body);
    }
    .ft-ledger-row strong { font-weight: 750; font-size: 11.5px; }
    .ft-ledger-row .ft-ledger-desc { margin-top: 3px; color: var(--sh-muted); font-weight: 500; }
    .ft-ledger-amt { font-size: 12px; font-weight: 750; white-space: nowrap; text-align: right; }

    /* ---------------- Pagination (Laravel links) ---------------- */
    .treasury-page nav[role="navigation"] { font-size: 11px; }
    .treasury-page nav[role="navigation"] a,
    .treasury-page nav[role="navigation"] span[aria-current] span,
    .treasury-page nav[role="navigation"] span[aria-disabled] span {
        border-radius: 8px;
        font-size: 11px;
    }

    /* ---------------- Responsive ---------------- */
    @media (max-width: 1023px) {
        .ft-balance { width: 100%; min-width: 0; }
    }

    @media (max-width: 767px) {
        .ft-eyebrow { font-size: 9.5px; }
        .ft-title { font-size: 29px; }
        .ft-lead { font-size: 10.5px; }
        .ft-card { border-radius: 14px; padding: 16px; }
        .treasury-stack > * + * { margin-top: 14px; }
        .ft-btn { width: 100%; }
        .ft-payout form.flex { flex-direction: column; }
        .ft-payout form.flex .ft-btn { width: 100%; }
    }
</style>

<div class="treasury-page">
<div class="treasury-stack">
    @if(session('success'))<div class="ft-alert ft-alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="ft-alert ft-alert-error">{{ $errors->first() }}</div>@endif

    <section class="ft-hero">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div class="min-w-0">
                <p class="ft-eyebrow">Platform Treasury</p>
                <h2 class="ft-title">COD, Earnings & <span>Payout Control</span></h2>
            </div>
            <div class="ft-balance">
                <p class="ft-balance-label">Platform available</p>
                <p class="ft-balance-value">₱{{ number_format((float)$platformWallet->available_balance,2) }}</p>
                <p class="ft-balance-sub">Reserved ₱{{ number_format((float)$platformWallet->reserved_balance,2) }}</p>
            </div>
        </div>
    </section>

    <section class="grid gap-[18px] sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['Commission earned',$commissionStats['earned']],
            ['All wallet available',$walletStats['available']],
            ['All wallet reserved',$walletStats['reserved']],
            ['COD awaiting verification',$remittanceStats['awaiting']],
            ['Lifetime payouts',$walletStats['lifetime_payouts']],
        ] as [$label,$value])
            <article class="ft-card ft-kpi"><p class="ft-kpi-label">{{ $label }}</p><p class="ft-kpi-value">₱{{ number_format((float)$value,2) }}</p></article>
        @endforeach
    </section>

    <section class="ft-card">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="ft-kicker">Shipping Earnings Policy</p>
                <h3 class="ft-h3 mt-1">Pickup + Last-Mile split</h3>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="ft-tile ft-tile-blue"><span class="block">Pickup Rider</span><strong>{{ number_format($shippingPolicy['pickup_percent'],2) }}%</strong></div>
                <div class="ft-tile ft-tile-violet"><span class="block">Final Mile</span><strong>{{ number_format($shippingPolicy['last_mile_percent'],2) }}%</strong></div>
                <div class="ft-tile ft-tile-green"><span class="block">Logistics</span><strong>{{ number_format($shippingPolicy['logistics_percent'],2) }}%</strong></div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.finance.shipping-policy') }}" class="mt-4 grid gap-3 md:grid-cols-[1fr_1fr_auto]">
            @csrf
            <label class="ft-label">Pickup Rider %
                <input type="number" name="pickup_percent" min="0" max="100" step="0.01" value="{{ old('pickup_percent',$shippingPolicy['pickup_percent']) }}" required class="ft-input">
            </label>
            <label class="ft-label">Final-Mile Rider %
                <input type="number" name="last_mile_percent" min="0" max="100" step="0.01" value="{{ old('last_mile_percent',$shippingPolicy['last_mile_percent']) }}" required class="ft-input">
            </label>
            <button type="submit" class="ft-btn ft-btn-gold self-end">Save Policy</button>
        </form>
    </section>

    @if($admin->canManagePlatformSettings())
        @include('finance.payout-form', [
            'payoutAction'=>route('admin.finance.platform-payout'),
            'wallet'=>$platformWallet,
            'payoutTitle'=>'Platform Treasury Withdrawal',
            'payoutHelp'=>''
        ])
    @endif

    <section class="ft-card">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div><h3 class="ft-h3">Payout Queue</h3><p class="ft-muted">Seller, Logistics, Rider, and platform withdrawal requests.</p></div>
            <div class="flex flex-wrap gap-2"><span class="ft-pill ft-pill-amber">Pending ₱{{ number_format($payoutStats['pending'],2) }}</span><span class="ft-pill ft-pill-blue">Approved ₱{{ number_format($payoutStats['approved'],2) }}</span><span class="ft-pill ft-pill-green">Paid ₱{{ number_format($payoutStats['paid'],2) }}</span></div>
        </div>

        <div class="mt-4 space-y-3">
            @forelse($payoutRequests as $payout)
                <article class="ft-payout">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="ft-payout-code">{{ $payout->request_code }} · {{ strtoupper($payout->owner_type) }}</p>
                            <h4 class="ft-payout-name">{{ $payout->owner_label }}</h4>
                            <p class="ft-payout-meta">{{ strtoupper(str_replace('_',' ',$payout->payout_method)) }} · {{ $payout->destination_label }} · {{ $payout->requested_at?->format('M d, Y · h:i A') }}</p>
                            @php($destination = (array) ($payout->destination_details ?? []))
                            <div class="ft-transfer">
                                <span>Transfer to:</span>
                                {{ $destination['account_name'] ?? '—' }} · {{ $destination['account_reference'] ?? '—' }}
                            </div>
                        </div>
                        <div class="text-right"><p class="ft-payout-amount">₱{{ number_format((float)$payout->amount,2) }}</p><p class="ft-payout-status {{ $payout->status === 'paid' ? 'text-emerald-700' : ($payout->status === 'rejected' ? 'text-rose-700' : 'text-amber-700') }}">{{ strtoupper($payout->status) }}</p></div>
                    </div>

                    @if($payout->status === 'pending')
                        <div class="mt-4 grid gap-2 md:grid-cols-2">
                            <form method="POST" action="{{ route('admin.finance.payouts.approve',$payout) }}" class="flex gap-2">@csrf<input name="admin_note" maxlength="1000" placeholder="Optional approval note" class="ft-input flex-1"><button class="ft-btn ft-btn-gold">Approve</button></form>
                            <form method="POST" action="{{ route('admin.finance.payouts.reject',$payout) }}" class="flex gap-2">@csrf<input name="admin_note" required minlength="3" maxlength="1000" placeholder="Required rejection reason" class="ft-input flex-1"><button class="ft-btn ft-btn-red">Reject</button></form>
                        </div>
                    @elseif($payout->status === 'approved')
                        <div class="mt-4 grid gap-2 md:grid-cols-2">
                            <form method="POST" action="{{ route('admin.finance.payouts.paid',$payout) }}" class="flex gap-2">@csrf<input name="external_reference" required minlength="3" maxlength="191" placeholder="Real bank/e-wallet/cash reference" class="ft-input flex-1"><button class="ft-btn ft-btn-green">Confirm Paid</button></form>
                            <form method="POST" action="{{ route('admin.finance.payouts.reject',$payout) }}" class="flex gap-2">@csrf<input name="admin_note" required minlength="3" maxlength="1000" placeholder="Cancel reason" class="ft-input flex-1"><button class="ft-btn ft-btn-red">Reject / Return Funds</button></form>
                        </div>
                    @elseif($payout->status === 'paid')
                        <p class="ft-note ft-note-green">External reference: <strong>{{ $payout->external_reference }}</strong></p>
                    @elseif($payout->status === 'rejected')
                        <p class="ft-note ft-note-red">{{ $payout->admin_note }}</p>
                    @endif
                </article>
            @empty
                <p class="ft-empty">No payout requests yet.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $payoutRequests->links() }}</div>
    </section>

    <section class="grid gap-[18px] xl:grid-cols-2">
        <div class="ft-card"><h3 class="ft-h3">COD Reconciliation Snapshot</h3><div class="mt-4 grid grid-cols-3 gap-3"><div class="ft-tile ft-tile-neutral"><span>Awaiting</span><strong>₱{{ number_format($remittanceStats['awaiting'],2) }}</strong></div><div class="ft-tile ft-tile-green"><span>Verified</span><strong>₱{{ number_format($remittanceStats['verified'],2) }}</strong></div><div class="ft-tile" style="background:#fff5f5;color:#b04545;"><span>Discrepancies</span><strong>{{ number_format($remittanceStats['discrepancy_count']) }}</strong></div></div></div>
        <div class="ft-card"><h3 class="ft-h3">Platform Wallet Ledger</h3><div class="mt-4 space-y-2">@forelse($platformEntries as $entry)<div class="ft-ledger-row"><div><strong>{{ strtoupper(str_replace('_',' ',$entry->category)) }}</strong><div class="ft-ledger-desc">{{ $entry->description }}</div></div><div class="ft-ledger-amt">{{ $entry->available_delta >= 0 ? '+' : '' }}₱{{ number_format((float)$entry->available_delta,2) }}</div></div>@empty<p class="ft-muted">No platform wallet entries yet.</p>@endforelse</div></div>
    </section>
</div>
</div>
@endsection