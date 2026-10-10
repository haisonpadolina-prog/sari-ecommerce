@extends('layouts.buyer')

@section('title', 'Rewards — SARI')
@section('page-title', 'Rewards')

@section('content')

@include('components.buyer.header')

@php
    $points = $points ?? 0;
    $spent = $spent ?? 0;
    $orders = $orders ?? collect();
@endphp

<div class="sari-rewards-page mx-auto w-full max-w-[1500px] px-4 pb-8 pt-4 sm:px-6 sm:pt-5 lg:px-8 xl:px-10">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <section class="sari-rewards-page-header">
        <p class="sari-rewards-eyebrow">SARI Marketplace</p>

        <h1 class="sari-rewards-page-title">Rewards</h1>

        <p class="sari-rewards-page-copy">
            Earn points from successful purchases and track your rewards activity in one place.
        </p>
    </section>

    {{-- ============================================================
        REWARDS OVERVIEW
    ============================================================ --}}
    <section class="sari-rewards-overview">
        <div class="sari-rewards-overview-grid">

            {{-- Reward Points --}}
            <div class="sari-reward-stat">
                <span class="sari-reward-stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.8 1-6.1-4.4-4.3 6.1-.9L12 3Z"></path>
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="sari-reward-stat-label">Your Reward Points</p>

                    <div class="sari-reward-stat-value-row">
                        <strong class="sari-reward-stat-value sari-reward-stat-value--points">
                            {{ number_format((int) $points) }}
                        </strong>
                        <span class="sari-reward-stat-unit">points</span>
                    </div>

                    <p class="sari-reward-stat-copy">
                        Automatically earned from successfully delivered purchases.
                    </p>
                </div>
            </div>

            <div class="sari-rewards-divider" aria-hidden="true"></div>

            {{-- Delivered Spend --}}
            <div class="sari-reward-stat">
                <span class="sari-reward-stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="4" y="6" width="16" height="12" rx="2"></rect>
                        <path d="M4 10h16"></path>
                        <path d="M16 14h.01"></path>
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="sari-reward-stat-label">Delivered Spend</p>

                    <div class="sari-reward-stat-value-row">
                        <strong class="sari-reward-stat-value">
                            ₱{{ number_format((float) $spent, 2) }}
                        </strong>
                    </div>

                    <p class="sari-reward-stat-copy">
                        Total amount from successfully delivered orders.
                    </p>
                </div>
            </div>
        </div>

        {{-- Decorative reward illustration --}}
        <div class="sari-rewards-art" aria-hidden="true">
            <span class="sari-coin sari-coin-one">★</span>
            <span class="sari-coin sari-coin-two">★</span>
            <span class="sari-coin sari-coin-three">★</span>

            <svg class="sari-gift-illustration" viewBox="0 0 260 180" fill="none">
                <path d="M18 152c28-46 67-63 103-46 23 11 41 10 61-1 23-13 43-9 60 11v64H18v-28Z" fill="#FFF8EA"></path>
                <path d="M120 96h82v64h-82z" fill="#FFFDF8" stroke="#E5C37E" stroke-width="2"></path>
                <path d="M113 86h96v24h-96z" rx="8" fill="#FFF9EF" stroke="#E3BB68" stroke-width="2"></path>
                <path d="M156 86h15v74h-15z" fill="#D99818"></path>
                <path d="M113 98h96" stroke="#E9D2A5" stroke-width="2"></path>
                <path d="M160 86c-20-13-24-29-13-35 10-5 21 3 22 24 4-20 15-30 26-25 13 6 8 24-13 36" fill="#FFF3D4" stroke="#D99818" stroke-width="3" stroke-linecap="round"></path>
                <path d="M171 87c8 2 18 7 27 15" stroke="#D99818" stroke-width="3" stroke-linecap="round"></path>
            </svg>
        </div>
    </section>

    {{-- ============================================================
        REWARD ACTIVITY
    ============================================================ --}}
    <section class="sari-rewards-activity">
        <div class="sari-rewards-activity-head">
            <div class="flex min-w-0 items-center gap-3">
                <span class="sari-rewards-section-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M7 4h10"></path>
                        <path d="M8 2h8v4H8z"></path>
                        <path d="M6 5h12v16H6z"></path>
                        <path d="M9 10h6"></path>
                        <path d="M9 14h6"></path>
                    </svg>
                </span>

                <div class="min-w-0">
                    <h2>Reward Activity</h2>
                    <p>View your recent points earned from delivered purchases.</p>
                </div>
            </div>

            @if ($orders->count() > 0)
                <span class="sari-rewards-count">
                    {{ number_format($orders->count()) }}
                    {{ $orders->count() === 1 ? 'activity' : 'activities' }}
                </span>
            @endif
        </div>

        <div class="sari-rewards-activity-body">
            @forelse ($orders as $order)
                @php
                    $orderTotal = (float) ($order->total ?? 0);
                    $rewardRate = (float) config('sari.buyer.reward_points_per_peso', 0.10);
                    $earnedPoints = (int) floor($orderTotal * $rewardRate);
                    $orderNumber = $order->order_number ?? 'Order';
                    $deliveredDate = $order->delivered_at ?? null;
                @endphp

                <article class="sari-reward-row">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="sari-reward-row-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 3v18"></path>
                                <path d="M17 7H9.5a2.5 2.5 0 0 0 0 5H14a2.5 2.5 0 0 1 0 5H6"></path>
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <p class="sari-reward-order-number">{{ $orderNumber }}</p>
                            <p class="sari-reward-order-meta">
                                Delivered
                                @if ($deliveredDate)
                                    {{ $deliveredDate instanceof \Carbon\CarbonInterface
                                        ? $deliveredDate->format('M d, Y')
                                        : \Carbon\Carbon::parse($deliveredDate)->format('M d, Y') }}
                                @else
                                    successfully
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 text-right">
                        <span class="sari-reward-earned">+{{ number_format($earnedPoints) }} pts</span>
                        <p class="sari-reward-delivered-total">
                            ₱{{ number_format($orderTotal, 2) }} delivered
                        </p>
                    </div>
                </article>
            @empty
                <div class="sari-rewards-empty">
                    <span class="sari-rewards-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M4 9h16v11H4z"></path>
                            <path d="M12 9v11"></path>
                            <path d="M4 13h16"></path>
                            <path d="M8 9c-2.5 0-3.5-1.5-3.5-3S6 3.5 7.5 4c1.7.6 3.2 3 4.5 5"></path>
                            <path d="M16 9c2.5 0 3.5-1.5 3.5-3S18 3.5 16.5 4c-1.7.6-3.2 3-4.5 5"></path>
                        </svg>
                    </span>

                    <h3>No reward activity yet</h3>

                    <p>
                        Complete your first successful delivery to start earning SARI reward points.
                    </p>

                    <a href="{{ route('buyer.products') }}" class="sari-rewards-shop-button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path d="M3 4h2l2 11h11l2-8H6"></path>
                            <circle cx="9" cy="19" r="1.4"></circle>
                            <circle cx="18" cy="19" r="1.4"></circle>
                        </svg>
                        Start Shopping
                    </a>
                </div>
            @endforelse
        </div>
    </section>
</div>

<style>
.sari-rewards-page {
    --rewards-gold:#c88912;
    --rewards-gold-dark:#aa710d;
    --rewards-text:#27303a;
    --rewards-muted:#7c8794;
    --rewards-border:#e2e7ec;
    font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    color:var(--rewards-text);
}

.sari-rewards-page-header {
    padding:2px 2px 16px;
}

.sari-rewards-eyebrow {
    color:#a76f13;
    font-size:7px;
    font-weight:800;
    letter-spacing:.15em;
    text-transform:uppercase;
}

.sari-rewards-page-title {
    margin-top:5px;
    color:#202834;
    font-size:clamp(30px,2.6vw,38px);
    font-weight:800;
    line-height:1.03;
    letter-spacing:-.04em;
}

.sari-rewards-page-copy {
    max-width:680px;
    margin-top:7px;
    color:#7f8a98;
    font-size:8.5px;
    line-height:1.6;
}

/* Overview */
.sari-rewards-overview {
    position:relative;
    min-height:210px;
    overflow:hidden;
    border:1px solid var(--rewards-border);
    border-radius:17px;
    background:#fff;
    box-shadow:0 14px 34px rgba(31,41,55,.055),0 3px 8px rgba(31,41,55,.02);
}

.sari-rewards-overview-grid {
    display:grid;
    grid-template-columns:minmax(250px,1fr) 1px minmax(250px,.95fr);
    gap:30px;
    width:min(68%,900px);
    min-height:210px;
    align-items:center;
    padding:28px 34px;
}

.sari-reward-stat {
    display:flex;
    min-width:0;
    align-items:center;
    gap:16px;
}

.sari-reward-stat-icon {
    display:grid;
    width:58px;
    height:58px;
    flex:0 0 58px;
    place-items:center;
    border:1px solid #f0e1c1;
    border-radius:17px;
    background:#fff9ed;
    color:#c3830e;
}

.sari-reward-stat-icon svg {
    width:27px;
    height:27px;
    stroke-width:1.8;
    stroke-linecap:round;
    stroke-linejoin:round;
}

.sari-reward-stat-label {
    color:#657080;
    font-size:8px;
    font-weight:700;
}

.sari-reward-stat-value-row {
    display:flex;
    align-items:baseline;
    gap:8px;
    margin-top:3px;
}

.sari-reward-stat-value {
    color:#bf7d08;
    font-size:30px;
    font-weight:850;
    letter-spacing:-.045em;
    line-height:1;
}

.sari-reward-stat-value--points {
    font-size:38px;
}

.sari-reward-stat-unit {
    color:#202834;
    font-size:18px;
    font-weight:800;
    letter-spacing:-.03em;
}

.sari-reward-stat-copy {
    max-width:330px;
    margin-top:7px;
    color:#8993a0;
    font-size:7.5px;
    line-height:1.55;
}

.sari-rewards-divider {
    width:1px;
    height:90px;
    background:#e7ebef;
}

.sari-rewards-art {
    position:absolute;
    inset:0 0 0 auto;
    width:34%;
    pointer-events:none;
}

.sari-gift-illustration {
    position:absolute;
    right:12px;
    bottom:-6px;
    width:260px;
    height:180px;
}

.sari-coin {
    position:absolute;
    z-index:2;
    display:grid;
    place-items:center;
    border:2px solid #e6ba5d;
    border-radius:999px;
    background:#fff8e9;
    color:#d08a0d;
    font-weight:900;
    box-shadow:0 5px 14px rgba(190,125,9,.12);
}

.sari-coin-one { right:210px; top:48px; width:44px; height:44px; font-size:18px; transform:rotate(-16deg); }
.sari-coin-two { right:130px; top:22px; width:34px; height:34px; font-size:14px; transform:rotate(12deg); }
.sari-coin-three { right:258px; bottom:36px; width:30px; height:30px; font-size:12px; transform:rotate(9deg); }

/* Activity */
.sari-rewards-activity {
    margin-top:16px;
    overflow:hidden;
    border:1px solid var(--rewards-border);
    border-radius:17px;
    background:#fff;
    box-shadow:0 12px 30px rgba(31,41,55,.048),0 2px 7px rgba(31,41,55,.018);
}

.sari-rewards-activity-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:18px;
    padding:16px 20px;
    border-bottom:1px solid #edf0f3;
}

.sari-rewards-section-icon {
    display:grid;
    width:40px;
    height:40px;
    flex:0 0 40px;
    place-items:center;
    border:1px solid #f0e2c5;
    border-radius:11px;
    background:#fff9ed;
    color:#c18412;
}

.sari-rewards-section-icon svg {
    width:18px;
    height:18px;
    stroke-width:1.8;
    stroke-linecap:round;
    stroke-linejoin:round;
}

.sari-rewards-activity-head h2 {
    color:#28313b;
    font-size:15px;
    font-weight:800;
    letter-spacing:-.02em;
}

.sari-rewards-activity-head p {
    margin-top:2px;
    color:#8993a0;
    font-size:7.2px;
}

.sari-rewards-count {
    display:inline-flex;
    min-height:29px;
    align-items:center;
    border:1px solid #e1e6eb;
    border-radius:999px;
    background:#f8f9fa;
    padding:0 10px;
    color:#66717d;
    font-size:7px;
    font-weight:700;
}

.sari-rewards-activity-body {
    padding:14px 20px 18px;
}

.sari-reward-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    border:1px solid #e7ebef;
    border-radius:11px;
    background:#fff;
    padding:12px 13px;
    transition:border-color .15s ease,box-shadow .15s ease,background-color .15s ease;
}

.sari-reward-row + .sari-reward-row {
    margin-top:9px;
}

.sari-reward-row:hover {
    border-color:#d7dde3;
    background:#fcfcfd;
    box-shadow:0 6px 15px rgba(31,41,55,.035);
}

.sari-reward-row-icon {
    display:grid;
    width:34px;
    height:34px;
    flex:0 0 34px;
    place-items:center;
    border:1px solid #eadfc9;
    border-radius:9px;
    background:#fffaf1;
    color:#a97010;
}

.sari-reward-row-icon svg {
    width:15px;
    height:15px;
    stroke-width:1.8;
    stroke-linecap:round;
    stroke-linejoin:round;
}

.sari-reward-order-number {
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    color:#343c45;
    font-size:8.8px;
    font-weight:750;
}

.sari-reward-order-meta,
.sari-reward-delivered-total {
    margin-top:3px;
    color:#89929c;
    font-size:6.7px;
}

.sari-reward-earned {
    display:inline-flex;
    align-items:center;
    border:1px solid #d6e7dc;
    border-radius:999px;
    background:#f5faf7;
    padding:5px 9px;
    color:#4d765d;
    font-size:7.4px;
    font-weight:800;
}

/* Empty state */
.sari-rewards-empty {
    display:flex;
    min-height:250px;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    border:1px dashed #dbe1e6;
    border-radius:13px;
    background:#fafbfc;
    padding:30px 18px;
    text-align:center;
}

.sari-rewards-empty-icon {
    display:grid;
    width:46px;
    height:46px;
    place-items:center;
    border:1px solid #eadfc9;
    border-radius:12px;
    background:#fffaf1;
    color:#be7d0d;
}

.sari-rewards-empty-icon svg {
    width:20px;
    height:20px;
    stroke-width:1.7;
    stroke-linecap:round;
    stroke-linejoin:round;
}

.sari-rewards-empty h3 {
    margin-top:13px;
    color:#303740;
    font-size:10px;
    font-weight:800;
}

.sari-rewards-empty p {
    max-width:520px;
    margin-top:5px;
    color:#8a949f;
    font-size:7.4px;
    line-height:1.55;
}

.sari-rewards-shop-button {
    display:inline-flex;
    height:37px;
    align-items:center;
    justify-content:center;
    gap:7px;
    margin-top:14px;
    border:1px solid #c88912;
    border-radius:9px;
    background:#c88912;
    padding:0 16px;
    color:#fff;
    font-size:7.6px;
    font-weight:800;
    text-decoration:none;
    transition:background-color .15s ease,border-color .15s ease;
}

.sari-rewards-shop-button:hover,
.sari-rewards-shop-button:focus-visible {
    border-color:#aa710d;
    background:#aa710d;
    outline:none;
}

.sari-rewards-shop-button svg {
    width:14px;
    height:14px;
    stroke-width:1.8;
    stroke-linecap:round;
    stroke-linejoin:round;
}

/* Responsive */
@media (max-width:1100px) {
    .sari-rewards-overview-grid {
        width:76%;
        gap:22px;
        padding:24px;
    }

    .sari-rewards-art {
        width:28%;
    }

    .sari-gift-illustration {
        right:-28px;
        opacity:.9;
    }
}

@media (max-width:900px) {
    .sari-rewards-overview-grid {
        width:100%;
        grid-template-columns:1fr 1px 1fr;
        padding-right:24px;
    }

    .sari-rewards-art {
        display:none;
    }
}

@media (max-width:680px) {
    .sari-rewards-page {
        padding-inline:14px !important;
    }

    .sari-rewards-overview-grid {
        grid-template-columns:1fr;
        gap:18px;
        padding:18px;
    }

    .sari-rewards-divider {
        width:100%;
        height:1px;
    }

    .sari-rewards-overview {
        min-height:0;
    }

    .sari-reward-stat-icon {
        width:52px;
        height:52px;
        flex-basis:52px;
        border-radius:14px;
    }

    .sari-reward-stat-value--points {
        font-size:34px;
    }

    .sari-reward-stat-value {
        font-size:27px;
    }

    .sari-rewards-activity-head {
        align-items:flex-start;
        padding:14px;
    }

    .sari-rewards-activity-body {
        padding:12px 14px 15px;
    }

    .sari-reward-row {
        align-items:flex-start;
    }
}

@media (max-width:480px) {
    .sari-rewards-page-title {
        font-size:28px;
    }

    .sari-reward-row {
        flex-direction:column;
    }

    .sari-reward-row > .shrink-0 {
        width:100%;
        text-align:left !important;
    }
}

@media (prefers-reduced-motion:reduce) {
    .sari-reward-row,
    .sari-rewards-shop-button {
        transition:none !important;
    }
}
</style>

@endsection
