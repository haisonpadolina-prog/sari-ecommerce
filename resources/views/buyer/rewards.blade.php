@extends('layouts.buyer')



@section('title', 'Rewards — SARI')

@section('page-title', 'Rewards')



@section('content')



@include('components.buyer.header')



@php

    $points = $points ?? 0;

    $spent = $spent ?? 0;

    $orders = $orders ?? collect();

    $platformVouchers = $platformVouchers ?? [];

    $promotionTimezone = $promotionTimezone ?? 'Asia/Manila';

@endphp



<div class="sari-rewards-page mx-auto w-full max-w-[1500px] px-4 pb-8 pt-4 sm:px-6 sm:pt-5 lg:px-8 xl:px-10">



    {{-- ============================================================

        PAGE HEADER

    ============================================================ --}}

    <section class="sari-rewards-page-header">

        <p class="sari-rewards-eyebrow">SARI Marketplace</p>



        <h1 class="sari-rewards-page-title">Rewards</h1>



        <p class="sari-rewards-page-copy">

            Earn points and view active SARI event vouchers published by Admin. Promotion availability follows Philippine Standard Time.

        </p>

    </section>



    {{-- ============================================================

        REWARDS SUMMARY

    ============================================================ --}}

    <section class="sari-rewards-summary-card">
        <div class="sari-rewards-summary-item">
            <p class="sari-rewards-summary-label">Your Reward Points</p>

            <div class="sari-rewards-summary-value-row">
                <strong class="sari-rewards-summary-value">{{ number_format((int) $points) }}</strong>
                <span class="sari-rewards-summary-unit">points</span>
            </div>

            <p class="sari-rewards-summary-copy">
                Automatically earned from successfully delivered purchases.
            </p>
        </div>

        <div class="sari-rewards-summary-item">
            <p class="sari-rewards-summary-label">Delivered Spend</p>

            <div class="sari-rewards-summary-value-row">
                <strong class="sari-rewards-summary-value">₱{{ number_format((float) $spent, 2) }}</strong>
            </div>

            <p class="sari-rewards-summary-copy">
                Total amount from successfully delivered orders.
            </p>
        </div>
    </section>


    {{-- ============================================================

        ACTIVE SARI EVENT VOUCHERS

    ============================================================ --}}

    <section class="sari-voucher-section">

        <div class="sari-voucher-section-head">

            <div>

                <p class="sari-rewards-eyebrow">Live Buyer Promotions</p>

                <h2>Available SARI Event Vouchers</h2>

                <p>Published vouchers appear here automatically while they are active in Philippine Standard Time.</p>

            </div>
        </div>



        @if(count($platformVouchers))

            <div class="sari-voucher-grid">

                @foreach($platformVouchers as $voucher)

                    <article class="sari-voucher-card">

                        <div class="sari-voucher-card-top">

                            <div>

                                <p class="sari-voucher-campaign">{{ $voucher['campaign_name'] }}</p>

                                <strong class="sari-voucher-code">{{ $voucher['code'] }}</strong>

                            </div>

                            <span class="sari-voucher-value">{{ $voucher['value'] }}</span>

                        </div>

                        <p class="sari-voucher-meta">

                            @if(($voucher['minimum'] ?? 0) > 0)

                                Min. eligible spend ₱{{ number_format((float) $voucher['minimum'], 2) }} ·

                            @endif

                            Valid until {{ $voucher['ends_at_pht'] ?? 'campaign end' }} PHT

                        </p>

                        <a href="{{ route('buyer.products') }}" class="sari-voucher-shop">Shop eligible products</a>

                    </article>

                @endforeach

            </div>

        @else

            <div class="sari-voucher-empty">No active SARI event vouchers right now. New published vouchers will appear here automatically.</div>

        @endif

    </section>



    {{-- ============================================================

        REWARD ACTIVITY

    ============================================================ --}}

    <section class="sari-rewards-activity">

        <div class="sari-rewards-activity-head">

            <div class="flex min-w-0 items-center gap-3">
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





.sari-voucher-section {

    margin-top:14px;

    border:1px solid var(--rewards-border);

    border-radius:14px;

    background:#fff;

    padding:16px;

    box-shadow:0 8px 22px rgba(31,41,55,.045);

}

.sari-voucher-section-head {display:flex;align-items:flex-start;justify-content:space-between;gap:14px;padding-bottom:12px;border-bottom:1px solid #edf0f2}

.sari-voucher-section-head h2 {margin-top:4px;color:#28303a;font-size:14px;font-weight:750}

.sari-voucher-section-head p:last-child {margin-top:4px;color:#7f8a98;font-size:8px;line-height:1.55}

.sari-pht-badge {display:inline-flex;min-height:25px;align-items:center;border:1px solid #ead39d;border-radius:999px;background:#fff8e9;padding:0 9px;color:#94620a;font-size:7px;font-weight:800;white-space:nowrap}

.sari-voucher-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:12px}

.sari-voucher-card {border:1px solid #e8dfce;border-radius:12px;background:#fffdf8;padding:13px}

.sari-voucher-card-top {display:flex;align-items:flex-start;justify-content:space-between;gap:12px}

.sari-voucher-campaign {color:#7b8692;font-size:7.5px;font-weight:600}

.sari-voucher-code {display:block;margin-top:4px;color:#8f6218;font-size:14px;letter-spacing:.04em}

.sari-voucher-value {color:#b97805;font-size:9px;font-weight:800;white-space:nowrap}

.sari-voucher-meta {margin-top:10px;color:#7e766d;font-size:7.5px;line-height:1.55}

.sari-voucher-shop {display:inline-flex;height:31px;align-items:center;justify-content:center;margin-top:10px;border:1px solid #dfc17a;border-radius:8px;background:#fff8e9;padding:0 10px;color:#8f6218;font-size:7.5px;font-weight:750;text-decoration:none}

.sari-voucher-empty {margin-top:12px;border:1px dashed #dfe4e8;border-radius:10px;background:#fafbfc;padding:18px;color:#7f8a98;font-size:8.5px;text-align:center}

@media(max-width:700px){.sari-voucher-section-head{flex-direction:column}.sari-voucher-grid{grid-template-columns:1fr}}



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



/* ============================================================
   REWARDS SUMMARY CARD — CLEAN KPI STYLE
   Replaces the decorative reward hero with a simple summary card.
============================================================ */
.sari-rewards-summary-card {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    overflow:hidden;
    border:1px solid #dfe5ea;
    border-radius:16px;
    background:#ffffff;
    box-shadow:0 8px 24px rgba(31,41,55,.035);
}

.sari-rewards-summary-item {
    position:relative;
    min-width:0;
    min-height:116px;
    padding:18px 22px;
    background:#ffffff;
}

.sari-rewards-summary-item + .sari-rewards-summary-item::before {
    content:"";
    position:absolute;
    left:0;
    top:20px;
    bottom:20px;
    width:1px;
    background:#e3e8ed;
}

.sari-rewards-summary-label {
    margin:0;
    color:#697586;
    font-size:9px;
    font-weight:750;
    line-height:1.2;
    letter-spacing:.07em;
    text-transform:uppercase;
}

.sari-rewards-summary-value-row {
    display:flex;
    align-items:baseline;
    gap:7px;
    margin-top:14px;
}

.sari-rewards-summary-value {
    color:#111827;
    font-size:28px;
    font-weight:800;
    line-height:1;
    letter-spacing:-.04em;
}

.sari-rewards-summary-unit {
    color:#5f6b78;
    font-size:12px;
    font-weight:650;
}

.sari-rewards-summary-copy {
    max-width:420px;
    margin-top:9px;
    color:#8a949f;
    font-size:9.5px;
    font-weight:500;
    line-height:1.5;
}

@media (max-width:680px) {
    .sari-rewards-summary-card {
        grid-template-columns:1fr;
        border-radius:14px;
    }

    .sari-rewards-summary-item {
        min-height:108px;
        padding:16px 18px;
    }

    .sari-rewards-summary-item + .sari-rewards-summary-item::before {
        top:0;
        left:18px;
        right:18px;
        bottom:auto;
        width:auto;
        height:1px;
    }

    .sari-rewards-summary-label {
        font-size:8.5px;
    }

    .sari-rewards-summary-value {
        font-size:25px;
    }

    .sari-rewards-summary-copy {
        font-size:9px;
    }
}


/* ============================================================
   FINAL REWARDS CART-STYLE POLISH
   Header, Reward Activity and empty state aligned with Buyer Cart.
============================================================ */

.sari-rewards-page .sari-rewards-page-header {
    padding: 2px 2px 14px !important;
}

.sari-rewards-page .sari-rewards-page-header .sari-rewards-eyebrow {
    color: #a87316 !important;
    font-size: 10.5px !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
    letter-spacing: .14em !important;
    text-transform: uppercase !important;
}

.sari-rewards-page .sari-rewards-page-title {
    margin-top: 5px !important;
    color: #2e343b !important;
    font-size: clamp(28px, 2.35vw, 33px) !important;
    font-weight: 760 !important;
    line-height: 1.04 !important;
    letter-spacing: -.035em !important;
}

.sari-rewards-page .sari-rewards-page-copy {
    max-width: 760px !important;
    margin-top: 7px !important;
    color: #7d8792 !important;
    font-size: 11.5px !important;
    font-weight: 450 !important;
    line-height: 1.55 !important;
}

.sari-rewards-page .sari-pht-badge {
    display: none !important;
}

.sari-rewards-page .sari-voucher-section-head h2 {
    margin-top: 5px !important;
    color: #28313b !important;
    font-size: 15.5px !important;
    font-weight: 750 !important;
}

.sari-rewards-page .sari-voucher-section-head p:last-child {
    margin-top: 5px !important;
    color: #7f8a98 !important;
    font-size: 10.5px !important;
    line-height: 1.5 !important;
}

.sari-rewards-page .sari-rewards-activity-head {
    align-items: flex-start !important;
    padding: 18px 20px 15px !important;
}

.sari-rewards-page .sari-rewards-activity-head > .flex {
    gap: 0 !important;
}

.sari-rewards-page .sari-rewards-section-icon {
    display: none !important;
}

.sari-rewards-page .sari-rewards-activity-head h2 {
    color: #26303a !important;
    font-size: 16.5px !important;
    font-weight: 750 !important;
    line-height: 1.25 !important;
    letter-spacing: -.015em !important;
}

.sari-rewards-page .sari-rewards-activity-head p {
    margin-top: 4px !important;
    color: #7e8996 !important;
    font-size: 11px !important;
    font-weight: 450 !important;
    line-height: 1.5 !important;
}

.sari-rewards-page .sari-rewards-count {
    min-height: 30px !important;
    padding-inline: 10px !important;
    color: #65717e !important;
    font-size: 9.5px !important;
    font-weight: 650 !important;
}

.sari-rewards-page .sari-rewards-empty {
    min-height: 330px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: #ffffff !important;
    padding: 58px 24px !important;
    box-shadow: none !important;
}

.sari-rewards-page .sari-rewards-empty-icon {
    width: 76px !important;
    height: 76px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #b4bbc3 !important;
}

.sari-rewards-page .sari-rewards-empty-icon svg {
    width: 46px !important;
    height: 46px !important;
    color: currentColor !important;
    stroke-width: 1.45 !important;
}

.sari-rewards-page .sari-rewards-empty h3 {
    margin-top: 16px !important;
    color: #929ba5 !important;
    font-size: 20px !important;
    font-weight: 720 !important;
    line-height: 1.25 !important;
    letter-spacing: -.02em !important;
}

.sari-rewards-page .sari-rewards-empty p {
    max-width: 560px !important;
    margin-top: 7px !important;
    color: #a4acb5 !important;
    font-size: 11.5px !important;
    font-weight: 450 !important;
    line-height: 1.6 !important;
}

.sari-rewards-page .sari-rewards-shop-button {
    min-width: 166px !important;
    height: 46px !important;
    margin-top: 24px !important;
    gap: 0 !important;
    border: 1px solid #c88912 !important;
    border-radius: 11px !important;
    background: #c88912 !important;
    padding: 0 24px !important;
    color: #ffffff !important;
    font-size: 11.5px !important;
    font-weight: 750 !important;
    line-height: 1 !important;
    box-shadow: none !important;
}

.sari-rewards-page .sari-rewards-shop-button:hover,
.sari-rewards-page .sari-rewards-shop-button:focus-visible {
    border-color: #ad760f !important;
    background: #ad760f !important;
    color: #ffffff !important;
    outline: none !important;
}

.sari-rewards-page .sari-rewards-shop-button svg {
    display: none !important;
}

@media (max-width: 680px) {
    .sari-rewards-page .sari-rewards-page-header .sari-rewards-eyebrow {
        font-size: 9.5px !important;
    }

    .sari-rewards-page .sari-rewards-page-title {
        font-size: 29px !important;
    }

    .sari-rewards-page .sari-rewards-page-copy {
        font-size: 10.5px !important;
    }

    .sari-rewards-page .sari-rewards-activity-head {
        padding: 16px !important;
    }

    .sari-rewards-page .sari-rewards-activity-head h2 {
        font-size: 15px !important;
    }

    .sari-rewards-page .sari-rewards-activity-head p {
        font-size: 10px !important;
    }

    .sari-rewards-page .sari-rewards-empty {
        min-height: 285px !important;
        padding: 48px 18px !important;
    }

    .sari-rewards-page .sari-rewards-empty-icon {
        width: 62px !important;
        height: 62px !important;
    }

    .sari-rewards-page .sari-rewards-empty-icon svg {
        width: 38px !important;
        height: 38px !important;
    }

    .sari-rewards-page .sari-rewards-empty h3 {
        font-size: 18px !important;
    }

    .sari-rewards-page .sari-rewards-empty p {
        font-size: 10.5px !important;
    }

    .sari-rewards-page .sari-rewards-shop-button {
        height: 44px !important;
        font-size: 11px !important;
    }
}

</style>



@endsection
