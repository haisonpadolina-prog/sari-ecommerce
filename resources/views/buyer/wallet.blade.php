@extends('layouts.buyer')

@section('title', 'My Wallet — SARI')
@section('page-title', 'My Wallet')

@push('styles')
<style>
    .sari-wallet { --wallet-ink:var(--sari-dark-e23e64dd1e, #2F3742); --wallet-muted:var(--sari-dark-a63aa16d7c, #8793A1); --wallet-line:var(--sari-dark-d8c2716225, #E5E7EB); max-width:1520px; margin:0 auto; padding:28px 32px 40px; color:var(--wallet-ink); font-family:'Poppins',ui-sans-serif,system-ui,sans-serif; }
    .sari-wallet *, .sari-wallet *::before, .sari-wallet *::after { box-sizing:border-box; }
    .sari-wallet h1,.sari-wallet h2,.sari-wallet p { margin:0; }
    .sari-wallet .sw-eyebrow { margin-bottom:6px; color:var(--sari-dark-685eb4fc40, #A87316); font-size:10.5px; font-weight:800; line-height:1.4; letter-spacing:.14em; text-transform:uppercase; }
    .sari-wallet h1 { font-size:clamp(27px,2.3vw,31px); font-weight:760; line-height:1.08; letter-spacing:-.04em; color:var(--sari-dark-e23e64dd1e, #2E343B); }
    .sari-wallet .sw-subtitle { margin-top:6px; color:var(--wallet-muted); font-size:11.5px; line-height:1.5; }
    .sari-wallet .sw-note { display:flex; align-items:flex-start; gap:11px; margin-top:20px; padding:14px 16px; border:1px solid var(--wallet-line); border-radius:12px; background:var(--sari-dark-950baec030, #fff); }
    .sari-wallet .sw-note svg { width:19px; height:19px; flex:0 0 19px; margin-top:2px; color:var(--sari-dark-e23e64dd1e, #8e6a25); }
    .sari-wallet .sw-note p { color:var(--sari-dark-a63aa16d7c, #687587); font-size:12px; line-height:1.75; }
    .sari-wallet .sw-note strong { color:var(--sari-dark-e23e64dd1e, #344256); font-weight:600; }
    .sari-wallet .sw-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin:20px 0; }
    .sari-wallet .sw-stat { min-width:0; min-height:110px; padding:14px 16px; border:1px solid var(--wallet-line); border-radius:18px; background:var(--sari-dark-950baec030, #fff); box-shadow:0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)),0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055)); }
    .sari-wallet .sw-stat-top { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .sari-wallet .sw-stat-label { color:var(--sari-dark-a63aa16d7c, #637081); font-size:13px; font-weight:650; line-height:1.4; }
    .sari-wallet .sw-stat svg { width:27px; height:27px; flex:0 0 27px; color:var(--sari-dark-a63aa16d7c, #8190a5); }
    .sari-wallet .sw-stat.sw-stat-cod svg { color:var(--sari-dark-19b301f69a, #ad760e); }
    .sari-wallet .sw-amount { margin-top:7px; color:var(--sari-dark-e23e64dd1e, #1F2937); font-size:clamp(25px,1.9vw,30px); font-weight:650; letter-spacing:-.04em; line-height:1.12; overflow-wrap:anywhere; font-variant-numeric:tabular-nums; }
    .sari-wallet .sw-stat small { display:block; margin-top:6px; color:var(--sari-dark-a63aa16d7c, #8995A3); font-size:11.5px; line-height:1.5; }
    .sari-wallet .sw-panels { display:grid; grid-template-columns:minmax(0,1fr); gap:20px; align-items:start; }
    .sari-wallet .sw-panel { min-width:0; overflow:hidden; background:var(--sari-dark-950baec030, #fff); border:1px solid var(--wallet-line); border-radius:18px; box-shadow:0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)),0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055)); }
    .sari-wallet .sw-panel-head { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; padding:21px 23px; border-bottom:1px solid var(--wallet-line); }
    .sari-wallet h2 { font-size:18px; font-weight:780; letter-spacing:-.025em; line-height:1.4; }
    .sari-wallet .sw-panel-head p { margin-top:4px; color:var(--sari-dark-a63aa16d7c, #8B96A4); font-size:11.5px; line-height:1.5; }
    .sari-wallet .sw-counter { flex-shrink:0; padding:0; border:0; color:var(--sari-dark-a63aa16d7c, #7B8794); font-size:12px; font-weight:600; white-space:nowrap; }
    .sari-wallet .sw-table-wrap { overflow-x:auto; }
    .sari-wallet table { width:100%; border-collapse:collapse; text-align:left; }
    .sari-wallet th { height:48px; padding:13px 16px; background:var(--sari-dark-950baec030, #F8FAFC); color:var(--sari-dark-a63aa16d7c, #8591A0); font-size:11px; font-weight:750; letter-spacing:.075em; text-transform:uppercase; white-space:nowrap; }
    .sari-wallet td { padding:17px 16px; border-top:1px solid var(--sari-dark-d8c2716225, #EDF0F3); font-size:12px; vertical-align:middle; }
    .sari-wallet .sw-primary { display:block; color:var(--sari-dark-e23e64dd1e, #293440); font-size:12.5px; font-weight:750; line-height:1.5; overflow-wrap:anywhere; }
    .sari-wallet .sw-secondary { display:block; margin-top:4px; color:var(--wallet-muted); font-size:11px; line-height:1.5; }
    .sari-wallet .sw-order-link { text-decoration:none; border-radius:3px; }
    .sari-wallet .sw-order-link:hover { color:var(--sari-dark-af18afcaa4, #a56c09); text-decoration:underline; text-underline-offset:3px; }
    .sari-wallet a:focus-visible { outline:2px solid #c9870e; outline-offset:4px; }
    .sari-wallet .sw-money { text-align:right; white-space:nowrap; font-size:12.5px; font-weight:750; font-variant-numeric:tabular-nums; }
    .sari-wallet .sw-badge { display:inline-flex; min-height:27px; align-items:center; padding:5px 10px; border:1px solid var(--sari-dark-d8c2716225, #e2e8f0); border-radius:999px; background:var(--sari-dark-950baec030, #f8fafc); color:var(--sari-dark-a63aa16d7c, #64748b); font-size:10.5px; font-weight:700; line-height:1.3; white-space:nowrap; }
    .sari-wallet .sw-badge.is-paid { color:var(--sari-dark-e23e64dd1e, #22734d); border-color:var(--sari-dark-d8c2716225, #cce8da); background:var(--sari-dark-4d21eb669b, #f1faf5); }
    .sari-wallet .sw-badge.is-unpaid { color:var(--sari-dark-19b301f69a, #98680d); border-color:var(--sari-dark-d8c2716225, #efdfb6); background:var(--sari-dark-950baec030, #fffbef); }
    .sari-wallet .sw-money.is-credit { color:var(--sari-dark-e23e64dd1e, #28764f); }
    .sari-wallet .sw-money.is-debit { color:var(--sari-dark-fb2588b58d, #bb4c4c); }
    .sari-wallet .sw-empty { padding:72px 20px; text-align:center; }
    .sari-wallet .sw-empty svg { display:block; width:58px; height:58px; margin:0 auto 13px; color:var(--sari-dark-a63aa16d7c, #9aa6b6); }
    .sari-wallet .sw-empty strong { display:block; font-size:22px; font-weight:700; color:var(--sari-dark-a63aa16d7c, #59636E); }
    .sari-wallet .sw-empty p { max-width:480px; margin:8px auto 0; font-size:12px; color:var(--wallet-muted); line-height:1.75; }
    .sari-wallet .sw-footer { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; min-height:64px; padding:13px 20px; border-top:1px solid var(--wallet-line); font-size:11px; color:var(--wallet-muted); }
    .sari-wallet .sw-pagination { overflow-x:auto; }
    .sari-wallet .sw-pagination nav { margin:0; }
    @media (hover:hover) { .sari-wallet tbody tr:hover { background:var(--sari-dark-950baec030, #fbfcfe); } }
    .sari-wallet > header { padding-bottom:14px; border-bottom:1px solid var(--sari-dark-d8c2716225, #E9EDF1); }
    .sari-wallet .sw-page-links { display:flex; align-items:center; gap:6px; }
    .sari-wallet .sw-page-links a,.sari-wallet .sw-page-links span {
        display:grid; place-items:center; width:34px; min-width:34px; height:34px;
        border:1px solid var(--sari-dark-d8c2716225, #E5E7EB); border-radius:10px; background:var(--sari-dark-950baec030, #fff); color:var(--sari-dark-a63aa16d7c, #7B8794);
        font-size:11.5px; font-weight:700; text-decoration:none;
    }
    .sari-wallet .sw-page-links [aria-current="page"] { background:#C88912; border-color:#C88912; color:#fff; box-shadow:0 5px 12px var(--sari-dark-59ed29fe9d, rgba(200,137,18,.14)); }
    .sari-wallet .sw-page-links [aria-disabled="true"] { opacity:.42; }
    .sari-wallet .sw-page-links a:hover { border-color:#C88912; color:var(--sari-dark-af18afcaa4, #A56D0A); background:var(--sari-dark-950baec030, #F8FAFC); }
    .sari-wallet .sw-page-links svg { width:12px; height:12px; }
    @media (max-width:1180px) { .sari-wallet { padding:24px 24px 36px; } .sari-wallet .sw-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:767px) { .sari-wallet { padding:20px 16px 32px; } .sari-wallet h1 { font-size:25px; } .sari-wallet .sw-eyebrow { font-size:9.5px; } .sari-wallet .sw-stat-label { font-size:12.5px; } .sari-wallet .sw-stat small { font-size:11px; } .sari-wallet .sw-empty { padding:56px 16px; } .sari-wallet .sw-empty svg { width:40px; height:40px; } .sari-wallet .sw-empty strong { font-size:20px; } }
    @media (max-width:540px) { .sari-wallet .sw-stats { grid-template-columns:1fr; gap:11px; } .sari-wallet .sw-stat { min-height:100px; } .sari-wallet .sw-panel-head { flex-direction:column; align-items:flex-start; padding:17px 16px; gap:6px; } .sari-wallet .sw-footer { flex-direction:column; align-items:flex-start; } .sari-wallet .sw-page-links { align-self:flex-end; } .sari-wallet .sw-table-wrap table { min-width:440px; } }
    @media (prefers-reduced-motion:reduce) { .sari-wallet * { transition-duration:.01ms !important; animation-duration:.01ms !important; } }
</style>
@endpush

@section('content')
@include('components.buyer.header')

@php
    $summaryCards = [
        ['label' => 'Available balance', 'amount' => $wallet->available_balance, 'description' => 'Internal ledger only', 'icon' => 'M3 6h16v14H3z M3 6V4h14v2 M15 11h6v5h-6z M17 13.5h1', 'tone' => ''],
        ['label' => 'Reserved balance', 'amount' => $wallet->reserved_balance, 'description' => 'Not spendable at checkout', 'icon' => 'M6 10h12v11H6z M8 10V7a4 4 0 0 1 8 0v3 M12 14v3', 'tone' => ''],
        ['label' => 'Total COD paid', 'amount' => $codCollected, 'description' => 'Delivered and paid orders', 'icon' => 'M3 6h18v12H3z M3 10c3 0 4-1 4-4 M17 6c0 3 1 4 4 4 M3 14c3 0 4 1 4 4 M17 18c0-3 1-4 4-4 M14 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0', 'tone' => 'sw-stat-cod'],
        ['label' => 'Lifetime wallet credits', 'amount' => $wallet->lifetime_credits, 'description' => 'No automatic Buyer COD earnings', 'icon' => 'M12 3v12 M7 8l5-5 5 5 M4 14v6h16v-6', 'tone' => ''],
    ];
@endphp

<main class="sari-wallet">
    <header>
        <p class="sw-eyebrow">SARI Finance</p>
        <h1>My Wallet &amp; COD History</h1>
        <p class="sw-subtitle">Your internal balances, cash payments, and accounting activity.</p>
    </header>

    <div class="sw-note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01" stroke-linecap="round"/></svg>
        <p>This is an internal record, not a bank or reloadable e-wallet. SARI accepts <strong>Cash on Delivery only</strong> for purchases. Buyers pay the delivery Rider in cash; shopping does not use this wallet.</p>
    </div>

    <section class="sw-stats" aria-label="Wallet and COD summary">
        @foreach ($summaryCards as $card)
            <article class="sw-stat {{ $card['tone'] }}">
                <div class="sw-stat-top">
                    <p class="sw-stat-label">{{ $card['label'] }}</p>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $card['icon'] }}"/></svg>
                </div>
                <p class="sw-amount">₱{{ number_format((float) $card['amount'], 2) }}</p>
                <small>{{ $card['description'] }}</small>
            </article>
        @endforeach
    </section>

    <div class="sw-panels">
        <section class="sw-panel" aria-labelledby="sw-cod-title">
            <header class="sw-panel-head">
                <div><h2 id="sw-cod-title">COD Purchase History</h2><p>Cash payments for your orders, separate from wallet activity.</p></div>
                <span class="sw-counter">{{ number_format($codOrders->total()) }} orders</span>
            </header>
            @if ($codOrders->count())
                <div class="sw-table-wrap" role="region" aria-label="COD orders" tabindex="0">
                    <table>
                        <thead><tr><th scope="col">Order / Date</th><th scope="col">Payment</th><th scope="col" class="sw-money">Total</th></tr></thead>
                        <tbody>
                            @foreach ($codOrders as $order)
                                @php $isPaid = strtolower((string) $order->payment_status) === 'paid'; @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('buyer.orders.show', $order) }}" class="sw-primary sw-order-link">{{ $order->order_number }}</a>
                                        <span class="sw-secondary">{{ $order->created_at?->format('M d, Y') }} · {{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                    </td>
                                    <td><span class="sw-badge {{ $isPaid ? 'is-paid' : 'is-unpaid' }}">{{ $isPaid ? 'Paid' : 'Unpaid' }}</span></td>
                                    <td class="sw-money">₱{{ number_format((float) $order->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="sw-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path d="M9 8h6M9 12h6"/></svg>
                    <strong>No COD orders yet</strong><p>Your cash-on-delivery orders will appear here.</p>
                </div>
            @endif
            @if ($codOrders->count())
                <footer class="sw-footer">
                    <p>Showing {{ $codOrders->firstItem() }}–{{ $codOrders->lastItem() }} of {{ number_format($codOrders->total()) }} orders</p>
                    @if ($codOrders->hasPages())
                        @php
                            $pageStart = max(1, min($codOrders->currentPage() - 2, $codOrders->lastPage() - 4));
                            $pageEnd = min($codOrders->lastPage(), $pageStart + 4);
                        @endphp
                        <nav class="sw-page-links" aria-label="COD order pagination">
                            @if ($codOrders->onFirstPage())
                                <span aria-disabled="true" aria-label="Previous page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></span>
                            @else
                                <a href="{{ $codOrders->previousPageUrl() }}" rel="prev" aria-label="Previous page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></a>
                            @endif
                            @foreach ($codOrders->getUrlRange($pageStart, $pageEnd) as $page => $url)
                                @if ($page === $codOrders->currentPage())
                                    <span aria-current="page" aria-label="Page {{ $page }}">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                                @endif
                            @endforeach
                            @if ($codOrders->hasMorePages())
                                <a href="{{ $codOrders->nextPageUrl() }}" rel="next" aria-label="Next page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></a>
                            @else
                                <span aria-disabled="true" aria-label="Next page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></span>
                            @endif
                        </nav>
                    @endif
                </footer>
            @endif
        </section>

        <section class="sw-panel" aria-labelledby="sw-activity-title">
            <header class="sw-panel-head">
                <div><h2 id="sw-activity-title">Wallet Activity</h2><p>Read-only entries. COD purchases do not credit or debit this wallet.</p></div>
                <span class="sw-counter">Read only</span>
            </header>
            @if ($walletEntries->isNotEmpty())
                <div class="sw-table-wrap" role="region" aria-label="Wallet accounting entries" tabindex="0">
                    <table>
                        <thead><tr><th scope="col">Activity / Date</th><th scope="col" class="sw-money">Amount</th></tr></thead>
                        <tbody>
                            @foreach ($walletEntries as $entry)
                                @php $delta = (float) $entry->available_delta; @endphp
                                <tr>
                                    <td><span class="sw-primary">{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</span><span class="sw-secondary">{{ $entry->occurred_at?->format('M d, Y · h:i A') }}</span></td>
                                    <td class="sw-money {{ $delta > 0 ? 'is-credit' : ($delta < 0 ? 'is-debit' : '') }}">{{ $delta >= 0 ? '+' : '−' }}₱{{ number_format(abs($delta), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <footer class="sw-footer">Latest {{ $walletEntries->count() }} entries</footer>
            @else
                <div class="sw-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M3 7h17v14H3zM3 7V4h14v3M15 12h6v5h-6z"/><path d="M17 14.5h1" stroke-linecap="round"/></svg>
                    <strong>No wallet movements</strong><p>This is expected for normal COD Buyers. Cash purchases remain in COD Purchase History.</p>
                </div>
            @endif
        </section>
    </div>
</main>
@endsection
