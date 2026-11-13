@extends('layouts.admin')

@section('title', 'Logistics Cash Turnovers — SARI Admin')
@section('page-title', 'Cash Turnovers')

@section('content')
<style>
    /* ============================================================
       SARI ADMIN — LOGISTICS CASH TURNOVERS
       UI parity with Buyer "Shop Products" catalog.
       VISUAL ONLY: Blade logic, routes, variables and pagination
       are unchanged.
       ============================================================ */

    .turnover-page {
        --sh-gold: #c88912;
        --sh-gold-dark: #a96f0e;
        --sh-ink: #20252b;
        --sh-text: #2e343b;
        --sh-body: #29323b;
        --sh-muted: #707b86;
        --sh-soft: #818995;
        --sh-border: #e5e7eb;
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
        padding: 0 0 34px;
        color: var(--sh-text);
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .turnover-page *,
    .turnover-page *::before,
    .turnover-page *::after { box-sizing: border-box; }

    .turnover-page a {
        transition: color .16s ease, background-color .16s ease, border-color .16s ease;
    }

    .tt-stack > * + * { margin-top: 18px; }

    /* ---------------- Flash messages ---------------- */
    .tt-alert {
        border-radius: 14px;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.5;
    }
    .tt-alert-success { border: 1px solid #d5e6da; background: #f4faf6; color: #36805a; }
    .tt-alert-error { border: 1px solid #f1d3d3; background: #fff5f5; color: #b04545; }

    /* ---------------- Open page header ---------------- */
    .tt-hero { padding: 0 2px 12px; }

    .tt-eyebrow {
        margin: 0;
        color: #a87316;
        font-size: 10.5px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .tt-title {
        margin: 5px 0 0;
        color: var(--sh-text);
        font-size: clamp(29px, 2.35vw, 34px);
        font-weight: 760;
        line-height: 1.04;
        letter-spacing: -.035em;
    }
    .tt-title span { color: var(--sh-gold); }

    .tt-lead {
        max-width: 860px;
        margin: 8px 0 0;
        color: #7d8792;
        font-size: 11.5px;
        font-weight: 450;
        line-height: 1.6;
    }
    .tt-lead strong { color: var(--sh-body); font-weight: 700; }

    .tt-back {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        color: #a96f0e;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none;
    }
    .tt-back:hover { color: #7d5007; }

    /* ---------------- Floating card surface ---------------- */
    .tt-card {
        border: 1px solid var(--sh-border);
        border-radius: 17px;
        background: #fff;
        box-shadow: var(--sh-shadow);
    }

    .tt-kpi { padding: 16px 18px; min-height: 96px; }
    .tt-kpi-label {
        margin: 0;
        color: var(--sh-soft);
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .tt-kpi-value {
        margin: 8px 0 0;
        color: var(--sh-ink);
        font-size: 24px;
        font-weight: 760;
        line-height: 1.1;
        letter-spacing: -.03em;
    }

    /* ---------------- Queue card ---------------- */
    .tt-queue-head {
        padding: 16px 18px;
        border-bottom: 1px solid var(--sh-divider);
    }

    .tt-h3 {
        margin: 0;
        color: var(--sh-ink);
        font-size: 14px;
        font-weight: 750;
        line-height: 1.35;
        letter-spacing: -.015em;
    }

    .tt-muted {
        margin: 4px 0 0;
        color: var(--sh-muted);
        font-size: 11px;
        font-weight: 500;
        line-height: 1.6;
    }

    .tt-table-wrap {
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: #d9dde2 transparent;
    }

    .tt-table {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;
        text-align: left;
    }

    .tt-table thead tr {
        background: #fafbfc;
        border-bottom: 1px solid var(--sh-divider);
    }

    .tt-table th {
        padding: 0 16px;
        height: 52px;
        color: #4b5560;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .tt-table td {
        padding: 12px 16px;
        color: #3f4852;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.4;
    }

    .tt-table tbody tr {
        border-bottom: 1px solid var(--sh-row-divider);
        transition: background-color .16s ease;
    }
    .tt-table tbody tr:last-child { border-bottom: 0; }
    .tt-table tbody tr:hover { background: #fcfcfd; }

    .tt-code { color: var(--sh-body); font-weight: 700; }
    .tt-amount { color: var(--sh-ink); font-weight: 750; white-space: nowrap; }
    .tt-date { color: var(--sh-muted); font-size: 11px; white-space: nowrap; }

    .tt-status {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 0 10px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .02em;
        white-space: nowrap;
    }
    .tt-status::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
    }
    .tt-status-received { border: 1px solid #d6e9dc; background: #eef8f1; color: #36805a; }
    .tt-status-discrepancy { border: 1px solid #f1d3d3; background: #fff5f5; color: #b04545; }
    .tt-status-pending { border: 1px solid #efd9b0; background: #fff8e9; color: #a87019; }

    .tt-action {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        border: 1px solid #dfe3e8;
        border-radius: 9px;
        background: #fff;
        padding: 0 12px;
        color: #865709;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }
    .tt-action:hover,
    .tt-action:focus-visible {
        outline: none;
        border-color: #eadfc9;
        background: #fffaf1;
        color: var(--sh-gold);
    }

    .tt-empty {
        padding: 44px 16px !important;
        color: var(--sh-muted) !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        text-align: center;
    }

    .tt-queue-foot { padding: 12px 18px; border-top: 1px solid var(--sh-divider); background: #fafbfc; }
    .tt-queue-foot:empty { display: none; }

    .turnover-page nav[role="navigation"] { font-size: 11px; }
    .turnover-page nav[role="navigation"] a,
    .turnover-page nav[role="navigation"] span[aria-current] span,
    .turnover-page nav[role="navigation"] span[aria-disabled] span {
        border-radius: 8px;
        font-size: 11px;
    }

    /* ---------------- Responsive ---------------- */
    @media (max-width: 767px) {
        .tt-eyebrow { font-size: 9.5px; }
        .tt-title { font-size: 29px; }
        .tt-lead { font-size: 10.5px; }
        .tt-stack > * + * { margin-top: 14px; }
        .tt-card { border-radius: 14px; }
    }
</style>

<div class="turnover-page">
<div class="tt-stack">
    @if(session('success'))<div class="tt-alert tt-alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="tt-alert tt-alert-error">{{ $errors->first() }}</div>@endif

    <section class="tt-hero">
        <p class="tt-eyebrow">SARI Central Treasury</p>
        <h2 class="tt-title">Receive Physical COD Cash <span>from Logistics</span></h2>
    </section>

    <section class="grid gap-[18px] sm:grid-cols-3">
        @foreach([
            ['Awaiting physical receipt', $stats['awaiting']],
            ['Confirmed cash received', $stats['received']],
            ['Open discrepancy cases', $stats['discrepancies']],
        ] as [$label, $amount])
            <article class="tt-card tt-kpi">
                <p class="tt-kpi-label">{{ $label }}</p>
                <p class="tt-kpi-value">{{ $label === 'Open discrepancy cases' ? number_format((int) $amount) : '₱'.number_format((float) $amount, 2) }}</p>
            </article>
        @endforeach
    </section>

    <section class="tt-card overflow-hidden">
        <div class="tt-queue-head">
            <h3 class="tt-h3">Logistics Turnover Queue</h3>
            <p class="tt-muted">Review a turnover code, count the cash, and record the actual amount. Open discrepancies are displayed first.</p>
        </div>
        <div class="tt-table-wrap">
            <table class="tt-table">
                <thead><tr><th>Turnover</th><th>Logistics company</th><th>Main Branch</th><th class="text-right">Expected cash</th><th>Status</th><th>Submitted</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                @forelse($batches as $turnover)
                    <tr>
                        <td class="tt-code">{{ $turnover->turnover_code }}</td>
                        <td>{{ $turnover->logistics?->displayName() ?? 'Logistics #'.$turnover->logistics_account_id }}</td>
                        <td>{{ $turnover->mainFacility?->name ?? '—' }}</td>
                        <td class="tt-amount text-right">₱{{ number_format((float) $turnover->expected_amount, 2) }}</td>
                        <td><span class="tt-status {{ $turnover->status === 'received' ? 'tt-status-received' : ($turnover->status === 'discrepancy' ? 'tt-status-discrepancy' : 'tt-status-pending') }}">{{ strtoupper($turnover->status) }}</span></td>
                        <td class="tt-date">{{ $turnover->submitted_at?->format('M d, Y h:i A') }}</td>
                        <td class="text-right"><a href="{{ route('admin.finance.cash-turnovers.show', $turnover) }}" class="tt-action">{{ $turnover->status === 'received' ? 'View receipt' : 'Count & verify' }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="tt-empty">No Logistics cash turnovers submitted yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="tt-queue-foot">{{ $batches->links() }}</div>
    </section>
</div>
</div>
@endsection