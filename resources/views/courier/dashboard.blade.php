@extends('layouts.courier')

@section('title', 'Rider Dashboard')
@section('header-title', 'Rider Dashboard')
@section('header-subtitle', 'Operations Overview')

@push('styles')
    <link
        rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/uicons-thin-straight/css/uicons-thin-straight.css"
    >
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/dashboard.css') }}?v=20261029-4"
    >
@endpush

@section('content')
@php
    $hasMarketplaceWork =
        $currentDelivery instanceof \App\Models\Orders\MarketplaceOrder;

    $isLogisticsPickup =
        $hasMarketplaceWork && (bool) $currentDelivery->logisticsParcel;

    $lastMileJobs =
        (int) ($stats['last_mile_waiting'] ?? 0)
        + (int) ($stats['last_mile_active'] ?? 0);

    // Presentation values for the charts. No queries or database writes.
    $overviewPickupWaiting = max(0, (int) ($stats['pickup_waiting'] ?? 0));
    $overviewPickupActive = max(0, (int) ($stats['pickup_active'] ?? 0));
    $overviewPickupTotal = $overviewPickupWaiting + $overviewPickupActive;

    $overviewPickupWaitingPercent = $overviewPickupTotal > 0
        ? ($overviewPickupWaiting / $overviewPickupTotal) * 100
        : 0;

    $overviewPickupActivePercent = $overviewPickupTotal > 0
        ? ($overviewPickupActive / $overviewPickupTotal) * 100
        : 0;

    $overviewLastMileWaiting = max(0, (int) ($stats['last_mile_waiting'] ?? 0));
    $overviewLastMileActive = max(0, (int) ($stats['last_mile_active'] ?? 0));
    $overviewLastMileTotal = $overviewLastMileWaiting + $overviewLastMileActive;

    $overviewLastMileScale = max(
        1,
        $overviewLastMileWaiting,
        $overviewLastMileActive
    );

    $overviewLastMileWaitingWidth =
        ($overviewLastMileWaiting / $overviewLastMileScale) * 100;

    $overviewLastMileActiveWidth =
        ($overviewLastMileActive / $overviewLastMileScale) * 100;
@endphp

<div id="riderDashboard">

    @if(session('success'))
        <div class="rd-alert rd-alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rd-alert rd-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- HERO --}}
    <section class="rd-hero" aria-labelledby="riderWelcomeTitle">
        <div class="rd-hero-layout">
            <div class="rd-hero-copy">
                <h2 id="riderWelcomeTitle" class="rd-hero-title">
                    <span data-rider-greeting>Welcome, Rider!</span>
                    <span class="rd-wave" aria-hidden="true">
                        <i class="fi fi-ts-hand-wave"></i>
                    </span>
                </h2>

                <p class="rd-hero-subtitle">
                    Here’s what’s happening with your deliveries today.
                </p>

                <div class="rd-hero-statuses" aria-label="Rider availability">
                    <span
                        class="rd-status"
                        data-status="{{ strtolower(trim((string) ($rider->availability_status ?? 'offline'))) }}"
                    >
                        <i class="rd-status-dot" aria-hidden="true"></i>
                        <span>
                            Rider {{ ucfirst((string) ($rider->availability_status ?? 'offline')) }}
                        </span>
                    </span>

                    <span class="rd-status-divider" aria-hidden="true"></span>
                    <span class="rd-status-caption">SARI Delivery Partner</span>
                </div>
            </div>

            <div class="rd-hero-divider" aria-hidden="true"></div>

            <div class="rd-hero-meta">
                <div class="rd-meta-item">
                    <div class="rd-meta-heading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <circle cx="12" cy="12" r="8.5"></circle>
                            <path d="M12 7.5v5l3 2"></path>
                        </svg>
                        <p class="rd-meta-label">Time</p>
                    </div>
                    <div class="rd-meta-copy">
                        <p class="rd-meta-value" data-rider-clock>—</p>
                        <p class="rd-meta-note">Asia/Manila</p>
                    </div>
                </div>

                <div class="rd-meta-item">
                    <div class="rd-meta-heading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M7 3v3M17 3v3"></path>
                            <rect x="4" y="5" width="16" height="15" rx="2"></rect>
                            <path d="M4 10h16"></path>
                        </svg>
                        <p class="rd-meta-label">Date</p>
                    </div>
                    <div class="rd-meta-copy">
                        <p class="rd-meta-value" data-rider-day>—</p>
                        <p class="rd-meta-note" data-rider-date>—</p>
                    </div>
                </div>

                <div class="rd-meta-item rd-meta-weather">
                    <div class="rd-meta-heading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M17.5 17.5H8a4 4 0 1 1 .8-7.92A5 5 0 0 1 18 9a3.5 3.5 0 1 1-.5 8.5Z"></path>
                            <path d="M15 7.5a2.5 2.5 0 0 1 2.5-2.5"></path>
                        </svg>
                        <p class="rd-meta-label">Weather</p>
                    </div>
                    <div class="rd-meta-copy">
                        <p class="rd-meta-value">—</p>
                        <p class="rd-meta-note">Weather unavailable</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- KPI CARDS --}}
    <section class="rd-kpis" aria-label="Rider performance summary">
        @foreach([
            [
                'Pickup Queue',
                (int) ($stats['pickup_waiting'] ?? 0),
                'Jobs waiting to start',
                'pickup',
            ],
            [
                'Active Work',
                (int) ($stats['active_deliveries'] ?? 0),
                'Jobs currently moving',
                'active',
            ],
            [
                'Last-Mile Jobs',
                $lastMileJobs,
                'Assigned destination work',
                'lastmile',
            ],
            [
                "Today's Earnings",
                '₱'.number_format((float) ($stats['earnings_today'] ?? 0), 2),
                (int) ($stats['completed_today'] ?? 0).' completed today',
                'earnings',
            ],
        ] as [$label, $value, $note, $icon])
            <article class="rd-kpi rd-kpi-{{ $icon }}">
                <div class="rd-kpi-layout">
                    <div class="rd-kpi-copy">
                        <p class="rd-kpi-label">{{ $label }}</p>
                        <p class="rd-kpi-value">{{ $value }}</p>
                        <p class="rd-kpi-note">{{ $note }}</p>
                    </div>

                    <span class="rd-kpi-icon" aria-hidden="true">
                        @if($icon === 'pickup')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 7h14v12H5zM8 7V5h8v2M8 12h8"></path>
                            </svg>
                        @elseif($icon === 'active')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"></path>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="18" cy="18" r="2"></circle>
                            </svg>
                        @elseif($icon === 'lastmile')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                                <circle cx="12" cy="10" r="2"></circle>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 3v18"></path>
                                <path d="M17 7.5c0-1.8-1.8-3-5-3s-5 1.2-5 3 1.5 2.6 5 3.5 5 1.7 5 3.5-1.8 3-5 3-5-1.2-5-3"></path>
                            </svg>
                        @endif
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    {{-- OVERVIEW GRAPHS --}}
    <section class="rd-overview" aria-label="Current rider workload overview">

        <article class="rd-overview-card">
            <div class="rd-overview-head">
                <div>
                    <h3 class="rd-overview-title">Pickup Overview</h3>
                    <p class="rd-overview-subtitle">
                        Current pickup and direct-delivery orders.
                    </p>
                </div>
                <span class="rd-overview-tag">Current workload</span>
            </div>

            <div class="rd-donut-body">
                <div class="rd-donut">
                    <svg
                        viewBox="0 0 200 200"
                        role="img"
                        aria-label="Pickup and direct orders: {{ $overviewPickupWaiting }} waiting, {{ $overviewPickupActive }} active."
                    >
                        <circle
                            class="rd-donut-track"
                            cx="100" cy="100" r="76"
                        ></circle>

                        @if($overviewPickupTotal > 0)
                            <g transform="rotate(-90 100 100)">
                                <circle
                                    class="rd-donut-segment rd-donut-waiting"
                                    cx="100" cy="100" r="76"
                                    pathLength="100"
                                    stroke-dasharray="{{ $overviewPickupWaitingPercent }} {{ 100 - $overviewPickupWaitingPercent }}"
                                ></circle>
                                <circle
                                    class="rd-donut-segment rd-donut-active"
                                    cx="100" cy="100" r="76"
                                    pathLength="100"
                                    stroke-dasharray="{{ $overviewPickupActivePercent }} {{ 100 - $overviewPickupActivePercent }}"
                                    stroke-dashoffset="{{ -$overviewPickupWaitingPercent }}"
                                ></circle>
                            </g>
                        @endif
                    </svg>

                    <div class="rd-donut-center" aria-hidden="true">
                        <span>Current orders</span>
                        <strong>{{ number_format($overviewPickupTotal) }}</strong>
                    </div>
                </div>

                <div class="rd-donut-legend">
                    <p class="rd-legend-eyebrow">Order breakdown</p>
                    <h4 class="rd-legend-title">Your pickup workload</h4>

                    <div class="rd-legend-row">
                        <span>
                            <i class="rd-legend-dot" aria-hidden="true"></i>
                            Waiting
                        </span>
                        <strong>{{ number_format($overviewPickupWaiting) }}</strong>
                    </div>

                    <div class="rd-legend-row">
                        <span>
                            <i class="rd-legend-dot rd-legend-dot-blue" aria-hidden="true"></i>
                            Active
                        </span>
                        <strong>{{ number_format($overviewPickupActive) }}</strong>
                    </div>

                    <p class="rd-chart-note">
                        @if($overviewPickupTotal === 0)
                            No waiting or active orders right now.
                        @else
                            Includes seller pickups and direct deliveries.
                        @endif
                    </p>
                </div>
            </div>

            <div class="rd-overview-footer">
                <span>Based on current assigned orders.</span>
                <a href="{{ route('courier.requests') }}">View assignments →</a>
            </div>
        </article>

        <article class="rd-overview-card">
            <div class="rd-overview-head">
                <div>
                    <h3 class="rd-overview-title">Last-Mile Overview</h3>
                    <p class="rd-overview-subtitle">
                        Assigned batches awaiting or undergoing delivery.
                    </p>
                </div>
                <span class="rd-overview-tag">Current workload</span>
            </div>

            <div class="rd-overview-body">
                <div class="rd-overview-metrics">
                    <div class="rd-overview-metric">
                        <p>Total batches</p>
                        <strong>{{ number_format($overviewLastMileTotal) }}</strong>
                    </div>
                    <div class="rd-overview-metric">
                        <p>Waiting</p>
                        <strong>{{ number_format($overviewLastMileWaiting) }}</strong>
                    </div>
                    <div class="rd-overview-metric">
                        <p>Out for delivery</p>
                        <strong>{{ number_format($overviewLastMileActive) }}</strong>
                    </div>
                </div>

                <div class="rd-bar-chart">
                    <div class="rd-bar-axis" aria-hidden="true">
                        <span>0</span>
                        <span>{{ number_format($overviewLastMileScale) }} batches</span>
                    </div>

                    <div class="rd-bar-row">
                        <div class="rd-bar-label">
                            <span>Waiting for dispatch</span>
                            <strong>{{ number_format($overviewLastMileWaiting) }}</strong>
                        </div>
                        <div class="rd-bar-track" aria-hidden="true">
                            <span
                                class="rd-bar-fill"
                                style="width: {{ $overviewLastMileWaitingWidth }}%"
                            ></span>
                        </div>
                    </div>

                    <div class="rd-bar-row">
                        <div class="rd-bar-label">
                            <span>Out for delivery</span>
                            <strong>{{ number_format($overviewLastMileActive) }}</strong>
                        </div>
                        <div class="rd-bar-track" aria-hidden="true">
                            <span
                                class="rd-bar-fill rd-bar-fill-blue"
                                style="width: {{ $overviewLastMileActiveWidth }}%"
                            ></span>
                        </div>
                    </div>
                </div>

                <p class="rd-chart-note">
                    @if($overviewLastMileTotal === 0)
                        No waiting or active last-mile batches right now.
                    @else
                        Counts represent delivery batches, not individual parcels.
                    @endif
                </p>
            </div>

            <div class="rd-overview-footer">
                <span>Snapshot of your current last-mile assignments.</span>
                <a href="{{ route('courier.last-mile') }}">View last-mile deliveries →</a>
            </div>
        </article>
    </section>

    {{-- CURRENT ASSIGNMENTS --}}
    <section class="rd-jobs" aria-label="Current assignments">
        <article class="rd-panel">
            <div class="rd-panel-head">
                <div>
                    <h3 class="rd-panel-title">Current Pickup / Direct Job</h3>
                    <p class="rd-panel-description">
                        Your current assignment and next delivery step.
                    </p>
                </div>
                <a href="{{ route('courier.requests') }}" class="rd-button">
                    Pickup Assignments <span aria-hidden="true">→</span>
                </a>
            </div>

            @if($hasMarketplaceWork)
                <div class="rd-panel-body">
                    <div class="rd-job-top">
                        <div class="rd-job-copy">
                            <div class="rd-badges">
                                <h4 class="rd-order-number">
                                    {{ $currentDelivery->order_number }}
                                </h4>
                                <span class="rd-badge rd-badge-blue">
                                    {{ $currentDelivery->statusLabel() }}
                                </span>
                                <span class="rd-badge">
                                    {{ $isLogisticsPickup ? 'Seller Pickup' : 'Direct Delivery' }}
                                </span>
                            </div>

                            <p class="rd-route">
                                {{ $currentDelivery->pickup_name ?: $currentDelivery->seller?->store_name ?: 'Seller' }}
                                →
                                {{ $isLogisticsPickup ? 'Origin Hub' : ($currentDelivery->buyer_name ?: 'Buyer') }}
                            </p>

                            <p class="rd-copy">
                                @if($isLogisticsPickup)
                                    Collect the parcel from the Seller
                                    and hand it over to the assigned origin hub.
                                    Final Buyer delivery belongs to the
                                    destination last-mile Rider.
                                @else
                                    Continue the assigned direct-delivery
                                    workflow until Buyer handoff is complete.
                                @endif
                            </p>
                        </div>

                        <div class="rd-detail">
                            <p class="rd-detail-label">Current Stage</p>
                            <p class="rd-detail-value">
                                {{ $currentDelivery->statusLabel() }}
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('courier.delivery.show', ['order' => $currentDelivery->id]) }}"
                        class="rd-button rd-button-blue rd-button-block"
                    >
                        Continue Current Job →
                    </a>
                </div>
            @else
                <div class="rd-empty">
                    <div class="rd-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 7h16v11H4zM8 7V5h8v2"></path>
                        </svg>
                    </div>
                    <p class="rd-empty-title">
                        No active pickup or direct-delivery job.
                    </p>
                    <p class="rd-copy">
                        Assigned work will appear here after
                        SARI Logistics dispatches it to you.
                    </p>
                </div>
            @endif
        </article>

        <article class="rd-panel">
            <div class="rd-panel-head">
                <div>
                    <h3 class="rd-panel-title">Current Last-Mile Batch</h3>
                    <p class="rd-panel-description">
                        Destination hub → assigned barangay → Buyer.
                    </p>
                </div>
                <a href="{{ route('courier.last-mile') }}" class="rd-button">
                    Last-Mile Deliveries <span aria-hidden="true">→</span>
                </a>
            </div>

            @if($currentLastMile)
                <div class="rd-panel-body">
                    <div class="rd-job-top">
                        <div class="rd-job-copy">
                            <p class="rd-order-number">
                                {{ $currentLastMile->assignment_code }}
                            </p>
                            <h4 class="rd-route">
                                {{ $currentLastMile->facility_name }}
                                →
                                {{ $currentLastMile->delivery_area }}
                            </h4>
                            <p class="rd-copy">
                                {{ $currentLastMile->parcel_count }}
                                {{ $currentLastMile->parcel_count === 1 ? 'parcel' : 'parcels' }}
                            </p>
                        </div>
                        <span class="rd-badge rd-badge-gold">
                            {{ str_replace('_', ' ', $currentLastMile->status) }}
                        </span>
                    </div>

                    <div class="rd-details">
                        <div class="rd-detail">
                            <p class="rd-detail-label">Potential Delivery Fee</p>
                            <p class="rd-detail-value">
                                ₱{{ number_format((float) $currentLastMile->delivery_fee_total, 2) }}
                            </p>
                        </div>
                        <div class="rd-detail">
                            <p class="rd-detail-label">Destination Hub</p>
                            <p class="rd-detail-value">
                                {{ $currentLastMile->facility_name }}
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('courier.last-mile') }}"
                        class="rd-button rd-button-gold rd-button-block"
                    >
                        Continue Last-Mile Workflow →
                    </a>
                </div>
            @else
                <div class="rd-empty">
                    <div class="rd-empty-icon rd-empty-icon-green" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"></path>
                            <circle cx="12" cy="10" r="2"></circle>
                        </svg>
                    </div>
                    <p class="rd-empty-title">No active last-mile batch.</p>
                    <p class="rd-copy">
                        Destination-hub assignments appear here
                        when Logistics dispatches a local batch to you.
                    </p>
                </div>
            @endif
        </article>
    </section>

    <section class="rd-bottom" aria-label="Upcoming work and daily summary">
        <article class="rd-panel">
            <div class="rd-panel-head">
                <div>
                    <h3 class="rd-panel-title">Up Next</h3>
                    <p class="rd-panel-description">
                        Latest assignments waiting for you to start.
                    </p>
                </div>
                <a href="{{ route('courier.requests') }}" class="rd-text-link">
                    View all →
                </a>
            </div>

            <div class="rd-panel-body">
                <div class="rd-queue">
                    @forelse($assignments->take(4) as $order)
                        <a
                            href="{{ route('courier.delivery.show', ['order' => $order->id]) }}"
                            class="rd-queue-item"
                        >
                            <div>
                                <p class="rd-order-number">{{ $order->order_number }}</p>
                                <p class="rd-copy">
                                    {{ $order->pickup_name ?: $order->seller?->store_name ?: 'Seller' }}
                                    →
                                    {{ $order->logisticsParcel ? 'Origin Hub' : ($order->buyer_name ?: 'Buyer') }}
                                </p>
                            </div>
                            <span class="rd-badge rd-badge-gold">OPEN →</span>
                        </a>
                    @empty
                        <div class="rd-empty-queue">
                            No pickup assignments waiting.
                        </div>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="rd-panel">
            <div class="rd-panel-head">
                <div>
                    <h3 class="rd-panel-title">Today at a Glance</h3>
                    <p class="rd-panel-description">
                        Your current operational snapshot.
                    </p>
                </div>
            </div>

            <div class="rd-panel-body">
                <div class="rd-snapshot">
                    <div class="rd-snapshot-row">
                        <span>Completed jobs</span>
                        <strong>{{ (int) ($stats['completed_today'] ?? 0) }}</strong>
                    </div>
                    <div class="rd-snapshot-row">
                        <span>Pickup active</span>
                        <strong>{{ (int) ($stats['pickup_active'] ?? 0) }}</strong>
                    </div>
                    <div class="rd-snapshot-row">
                        <span>Last-mile active</span>
                        <strong>{{ (int) ($stats['last_mile_active'] ?? 0) }}</strong>
                    </div>
                    <div class="rd-snapshot-row rd-snapshot-earnings">
                        <span>Earned today</span>
                        <strong>
                            ₱{{ number_format((float) ($stats['earnings_today'] ?? 0), 2) }}
                        </strong>
                    </div>
                </div>

                <a
                    href="{{ route('courier.earnings') }}"
                    class="rd-button rd-button-purple rd-button-block"
                >
                    Open Earnings
                </a>
            </div>
        </article>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const root = document.getElementById('riderDashboard');
    if (!root) return;

    if (typeof window.__sariRiderClockCleanup === 'function') {
        window.__sariRiderClockCleanup();
    }

    const greeting = root.querySelector('[data-rider-greeting]');
    const clock = root.querySelector('[data-rider-clock]');
    const day = root.querySelector('[data-rider-day]');
    const date = root.querySelector('[data-rider-date]');
    const timeZone = 'Asia/Manila';

    const hourFormatter = new Intl.DateTimeFormat('en-GB', {
        timeZone,
        hour: '2-digit',
        hourCycle: 'h23',
    });

    const timeFormatter = new Intl.DateTimeFormat('en-PH', {
        timeZone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });

    const dayFormatter = new Intl.DateTimeFormat('en-PH', {
        timeZone,
        weekday: 'long',
    });

    const dateFormatter = new Intl.DateTimeFormat('en-PH', {
        timeZone,
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });

    let timer = null;

    function getGreetingByHour(hour) {
        if (hour >= 5 && hour < 12) return 'Good morning';
        if (hour >= 12 && hour < 18) return 'Good afternoon';
        return 'Good evening';
    }

    function renderDateTime() {
        const now = new Date();
        const hourPart = hourFormatter
            .formatToParts(now)
            .find(part => part.type === 'hour');

        const hour = Number(hourPart?.value ?? 0);

        if (greeting) {
            greeting.textContent = `${getGreetingByHour(hour)}, Rider!`;
        }
        if (clock) clock.textContent = timeFormatter.format(now);
        if (day) day.textContent = dayFormatter.format(now);
        if (date) date.textContent = dateFormatter.format(now);
    }

    function stopClock() {
        if (timer !== null) {
            window.clearTimeout(timer);
            timer = null;
        }
    }

    function scheduleClock() {
        stopClock();

        if (!root.isConnected) {
            cleanup();
            return;
        }

        renderDateTime();

        if (document.hidden) return;

        const delay = 60000 - (Date.now() % 60000) + 25;
        timer = window.setTimeout(scheduleClock, delay);
    }

    function handleVisibility() {
        if (document.hidden) stopClock();
        else scheduleClock();
    }

    function cleanup() {
        stopClock();
        document.removeEventListener('visibilitychange', handleVisibility);
        window.removeEventListener('pagehide', stopClock);
        window.removeEventListener('pageshow', scheduleClock);

        if (window.__sariRiderClockCleanup === cleanup) {
            delete window.__sariRiderClockCleanup;
        }
    }

    document.addEventListener('visibilitychange', handleVisibility);
    window.addEventListener('pagehide', stopClock);
    window.addEventListener('pageshow', scheduleClock);

    window.__sariRiderClockCleanup = cleanup;
    scheduleClock();
})();
</script>
@endpush