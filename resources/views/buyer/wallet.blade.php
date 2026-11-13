@extends('layouts.buyer')

@section('title', 'My Wallet — SARI')
@section('page-title', 'My Wallet')

@push('styles')
<style>
    .sari-wallet { --wallet-ink:#253041; --wallet-muted:#718096; --wallet-line:#e6ebf1; max-width:1380px; margin:0 auto; padding:28px 28px 40px; color:var(--wallet-ink); font-family:'Poppins',ui-sans-serif,system-ui,sans-serif; }
    .sari-wallet *, .sari-wallet *::before, .sari-wallet *::after { box-sizing:border-box; }
    .sari-wallet h1,.sari-wallet h2,.sari-wallet p { margin:0; }
    .sari-wallet .sw-eyebrow { margin-bottom:7px; color:#a56c09; font-size:11px; font-weight:700; letter-spacing:.13em; text-transform:uppercase; }
    .sari-wallet h1 { font-size:clamp(24px,2.1vw,30px); font-weight:750; line-height:1.25; letter-spacing:-.035em; }
    .sari-wallet .sw-subtitle { margin-top:9px; color:var(--wallet-muted); font-size:13px; line-height:1.7; }
    .sari-wallet .sw-note { display:flex; align-items:flex-start; gap:11px; margin-top:20px; padding:14px 16px; border:1px solid var(--wallet-line); border-radius:12px; background:#fff; }
    .sari-wallet .sw-note svg { width:19px; height:19px; flex:0 0 19px; margin-top:2px; color:#8e6a25; }
    .sari-wallet .sw-note p { color:#687587; font-size:12px; line-height:1.75; }
    .sari-wallet .sw-note strong { color:#344256; font-weight:600; }
    .sari-wallet .sw-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; margin:20px 0; }
    .sari-wallet .sw-stat { min-width:0; padding:20px; border:1px solid var(--wallet-line); border-radius:16px; background:#fff; box-shadow:0 5px 18px rgba(15,23,42,.035); }
    .sari-wallet .sw-stat-top { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .sari-wallet .sw-stat-label { color:#667387; font-size:12px; font-weight:600; line-height:1.5; }
    .sari-wallet .sw-stat svg { width:22px; height:22px; flex:0 0 22px; color:#8190a5; }
    .sari-wallet .sw-stat.sw-stat-cod svg { color:#ad760e; }
    .sari-wallet .sw-amount { margin-top:13px; color:#253041; font-size:clamp(21px,2vw,28px); font-weight:750; letter-spacing:-.04em; line-height:1.3; overflow-wrap:anywhere; font-variant-numeric:tabular-nums; }
    .sari-wallet .sw-stat small { display:block; margin-top:7px; color:var(--wallet-muted); font-size:11px; line-height:1.6; }
    .sari-wallet .sw-panels { display:grid; grid-template-columns:minmax(0,1.15fr) minmax(0,1fr); gap:20px; align-items:start; }
    .sari-wallet .sw-panel { min-width:0; overflow:hidden; background:#fff; border:1px solid var(--wallet-line); border-radius:16px; box-shadow:0 7px 22px rgba(15,23,42,.04); }
    .sari-wallet .sw-panel-head { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; padding:20px; border-bottom:1px solid var(--wallet-line); }
    .sari-wallet h2 { font-size:16px; font-weight:700; letter-spacing:-.025em; line-height:1.4; }
    .sari-wallet .sw-panel-head p { margin-top:5px; color:var(--wallet-muted); font-size:11px; line-height:1.7; }
    .sari-wallet .sw-counter { flex-shrink:0; padding:4px 9px; border:1px solid var(--wallet-line); border-radius:7px; color:#64748b; font-size:10px; font-weight:600; white-space:nowrap; }
    .sari-wallet .sw-table-wrap { overflow-x:auto; }
    .sari-wallet table { width:100%; border-collapse:collapse; text-align:left; }
    .sari-wallet th { padding:12px 20px; background:#f8fafc; color:#7b8797; font-size:10px; font-weight:650; letter-spacing:.06em; text-transform:uppercase; white-space:nowrap; }
    .sari-wallet td { padding:16px 20px; border-top:1px solid #edf1f5; font-size:12px; vertical-align:middle; }
    .sari-wallet .sw-primary { display:block; color:#344256; font-size:12px; font-weight:600; line-height:1.5; overflow-wrap:anywhere; }
    .sari-wallet .sw-secondary { display:block; margin-top:4px; color:var(--wallet-muted); font-size:10px; line-height:1.65; }
    .sari-wallet .sw-order-link { text-decoration:none; border-radius:3px; }
    .sari-wallet .sw-order-link:hover { color:#a56c09; text-decoration:underline; text-underline-offset:3px; }
    .sari-wallet a:focus-visible { outline:2px solid #c9870e; outline-offset:4px; }
    .sari-wallet .sw-money { text-align:right; white-space:nowrap; font-weight:650; font-variant-numeric:tabular-nums; }
    .sari-wallet .sw-badge { display:inline-flex; padding:3px 8px; border:1px solid #e2e8f0; border-radius:6px; background:#f8fafc; color:#64748b; font-size:10px; font-weight:600; line-height:1.5; white-space:nowrap; }
    .sari-wallet .sw-badge.is-paid { color:#22734d; border-color:#cce8da; background:#f1faf5; }
    .sari-wallet .sw-badge.is-unpaid { color:#98680d; border-color:#efdfb6; background:#fffbef; }
    .sari-wallet .sw-money.is-credit { color:#28764f; }
    .sari-wallet .sw-money.is-debit { color:#bb4c4c; }
    .sari-wallet .sw-empty { padding:38px 24px; text-align:center; }
    .sari-wallet .sw-empty svg { display:block; width:38px; height:38px; margin:0 auto 13px; color:#9aa6b6; }
    .sari-wallet .sw-empty strong { display:block; font-size:13px; font-weight:600; color:#4b586a; }
    .sari-wallet .sw-empty p { max-width:280px; margin:6px auto 0; font-size:11px; color:var(--wallet-muted); line-height:1.75; }
    .sari-wallet .sw-footer { padding:15px 20px; border-top:1px solid var(--wallet-line); font-size:11px; color:var(--wallet-muted); }
    .sari-wallet .sw-pagination { overflow-x:auto; }
    .sari-wallet .sw-pagination nav { margin:0; }
    @media (hover:hover) { .sari-wallet tbody tr:hover { background:#fbfcfe; } }
    @media (max-width:1100px) { .sari-wallet .sw-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } .sari-wallet .sw-panels { grid-template-columns:1fr; } }
    @media (max-width:600px) { .sari-wallet { padding:20px 16px 30px; } .sari-wallet .sw-stats { gap:10px; } .sari-wallet .sw-stat { padding:15px 12px; border-radius:13px; } .sari-wallet .sw-stat-label { font-size:11px; } .sari-wallet .sw-stat svg { width:18px; height:18px; flex-basis:18px; } .sari-wallet .sw-amount { font-size:20px; } .sari-wallet .sw-panel-head { padding:16px; } .sari-wallet th,.sari-wallet td { padding-left:16px; padding-right:16px; } .sari-wallet .sw-counter { font-size:9px; } }
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
            @if ($codOrders->hasPages())
                <footer class="sw-footer sw-pagination">{{ $codOrders->links() }}</footer>
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
