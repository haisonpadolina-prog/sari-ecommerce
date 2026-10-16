@extends('layouts.logistics')

@section('title', 'Reports — SARI Logistics')
@section('page-title', 'Reports')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/reports.css') }}?v=20261015-1"
    >
@endpush


@section('content')

@php
    $maxTrendCount =
        max(
            1,
            (int) $deliveryTrend
                ->max('count')
        );

    $sevenDayTotal =
        $deliveryTrend
            ->sum('count');

    $sevenDayFees =
        $deliveryTrend
            ->sum('fees');

    $summaryCards = [
        [
            'Waiting Assignment',
            $stats['waiting_assignment'],
            'Seller pickups not yet dispatched',
            'amber',
        ],
        [
            'Active Deliveries',
            $stats['active_deliveries'],
            'Orders currently moving',
            'blue',
        ],
        [
            'Delivered Today',
            $stats['delivered_today'],
            'Completed Buyer handoffs',
            'green',
        ],
        [
            'Delivered This Month',
            $stats['delivered_this_month'],
            'Monthly completed deliveries',
            'green',
        ],
        [
            'Monthly Delivery Fees',
            '₱'.number_format(
                $stats['delivery_fees_this_month'],
                2
            ),
            'Recorded delivered-order fees',
            'gold',
        ],
        [
            'Average Delivery Fee',
            '₱'.number_format(
                $stats['average_delivery_fee'],
                2
            ),
            'Per completed order this month',
            'neutral',
        ],
    ];
@endphp


<div class="reports-page">

    {{-- =====================================================
        HERO
    ====================================================== --}}

    <header class="reports-page-header">

        <div>

            <p class="reports-eyebrow">
                OPERATIONS ANALYTICS
            </p>


            <h1>
                Logistics <span>Reports</span>
            </h1>


            <p class="reports-subtitle">
                Review delivery throughput, parcel movement,
                Last-Mile Rider performance, and completed
                Marketplace deliveries across the Logistics network.
            </p>

        </div>


        <div class="reports-header-actions">

            <a
                href="{{ route(
                    'logistics.delivery-monitoring'
                ) }}"
                class="reports-button"
            >
                ← Delivery Monitoring
            </a>


            <a
                href="{{ route(
                    'logistics.dashboard'
                ) }}"
                class="
                    reports-button
                    reports-button-primary
                "
            >
                Dashboard
            </a>

        </div>

    </header>


    {{-- =====================================================
        MONTH LABEL
    ====================================================== --}}

    <div class="reports-period-bar">

        <div>

            <span class="reports-label">
                Reporting Period
            </span>


            <strong>
                {{ now('Asia/Manila')->format('F Y') }}
            </strong>

        </div>


        <div class="reports-period-meta">

            <span>
                7-day deliveries:
                <strong>
                    {{ $sevenDayTotal }}
                </strong>
            </span>


            <span>
                7-day delivery fees:
                <strong>
                    ₱{{ number_format(
                        $sevenDayFees,
                        2
                    ) }}
                </strong>
            </span>

        </div>

    </div>


    {{-- =====================================================
        SUMMARY
    ====================================================== --}}

    <div class="reports-stats">

        @foreach(
            $summaryCards
            as [$label, $value, $note, $tone]
        )

            <article
                class="
                    reports-stat
                    reports-stat-{{ $tone }}
                "
            >

                <p>
                    {{ $label }}
                </p>


                <strong>
                    {{ $value }}
                </strong>


                <span>
                    {{ $note }}
                </span>

            </article>

        @endforeach

    </div>


    {{-- =====================================================
        ANALYTICS GRID
    ====================================================== --}}

    <div class="reports-analysis-grid">


        {{-- =================================================
            SEVEN DAY TREND
        ================================================== --}}

        <section class="reports-card">

            <div class="reports-card-header">

                <div>

                    <p class="reports-eyebrow">
                        DELIVERY THROUGHPUT
                    </p>


                    <h2>
                        Last 7 days
                    </h2>


                    <p>
                        Completed deliveries by day.
                    </p>

                </div>


                <span class="reports-card-metric">
                    {{ $sevenDayTotal }}
                    delivered
                </span>

            </div>


            <div class="reports-trend">

                @foreach(
                    $deliveryTrend
                    as $day
                )

                    @php
                        $barWidth =
                            $day['count'] > 0
                                ? max(
                                    5,
                                    (
                                        $day['count']
                                        /
                                        $maxTrendCount
                                    )
                                    * 100
                                )
                                : 0;
                    @endphp


                    <div class="reports-trend-row">

                        <div class="reports-trend-day">

                            <strong>
                                {{ $day['label'] }}
                            </strong>


                            <span>
                                {{ $day['short_date'] }}
                            </span>

                        </div>


                        <div class="reports-trend-track">

                            <div
                                class="reports-trend-bar"
                                style="width: {{ $barWidth }}%;"
                            ></div>

                        </div>


                        <div class="reports-trend-value">

                            <strong>
                                {{ $day['count'] }}
                            </strong>


                            <span>
                                ₱{{ number_format(
                                    $day['fees'],
                                    2
                                ) }}
                            </span>

                        </div>

                    </div>

                @endforeach

            </div>

        </section>


        {{-- =================================================
            PIPELINE
        ================================================== --}}

        <section class="reports-card">

            <div class="reports-card-header">

                <div>

                    <p class="reports-eyebrow">
                        CURRENT NETWORK LOAD
                    </p>


                    <h2>
                        Parcel pipeline
                    </h2>


                    <p>
                        Current parcels across Logistics stages.
                    </p>

                </div>


                <span class="reports-card-metric">
                    Live
                </span>

            </div>


            <div class="reports-pipeline">

                @foreach($pipeline as $stage)

                    <div class="reports-pipeline-row">

                        <span
                            class="
                                reports-pipeline-dot
                                is-{{ $stage['tone'] }}
                            "
                        ></span>


                        <div class="reports-pipeline-copy">

                            <strong>
                                {{ $stage['label'] }}
                            </strong>


                            <p>
                                {{ $stage['description'] }}
                            </p>

                        </div>


                        <strong class="reports-pipeline-count">
                            {{ $stage['count'] }}
                        </strong>

                    </div>

                @endforeach

            </div>


            <div class="reports-pipeline-footer">

                <div>

                    <span class="reports-label">
                        Origin Hub Processed
                    </span>

                    <strong>
                        {{ $stats['origin_processed'] }}
                    </strong>

                </div>


                <div>

                    <span class="reports-label">
                        Ready for Linehaul
                    </span>

                    <strong>
                        {{ $stats['ready_linehaul'] }}
                    </strong>

                </div>

            </div>

        </section>

    </div>


    {{-- =====================================================
        RIDERS + RECENT DELIVERIES
    ====================================================== --}}

    <div class="reports-bottom-grid">


        {{-- =================================================
            TOP LAST-MILE RIDERS
        ================================================== --}}

        <section class="reports-card">

            <div class="reports-card-header">

                <div>

                    <p class="reports-eyebrow">
                        LAST-MILE PERFORMANCE
                    </p>


                    <h2>
                        Top local Riders
                    </h2>


                    <p>
                        Completed deliveries for the current month.
                    </p>

                </div>

            </div>


            <div class="reports-rider-list">

                @forelse(
                    $topLastMileRiders
                    as $index => $rider
                )

                    @php
                        $riderName =
                            trim(
                                $rider->first_name
                                .' '
                                .$rider->last_name
                            );
                    @endphp


                    <article class="reports-rider-row">

                        <span class="reports-rider-rank">
                            {{ $index + 1 }}
                        </span>


                        <span
                            class="reports-rider-avatar"
                            aria-hidden="true"
                        >
                            {{
                                mb_strtoupper(
                                    mb_substr(
                                        $riderName,
                                        0,
                                        1
                                    )
                                )
                            }}
                        </span>


                        <div class="reports-rider-copy">

                            <strong>
                                {{ $riderName }}
                            </strong>


                            <p>
                                {{
                                    $rider->barangay_name
                                    ?: 'Area not set'
                                }}

                                @if(
                                    $rider->municipality_name
                                )
                                    ·
                                    {{ $rider->municipality_name }}
                                @endif
                            </p>


                            <span>
                                {{
                                    $rider->vehicle_type
                                    ?: 'Vehicle not recorded'
                                }}
                            </span>

                        </div>


                        <div class="reports-rider-performance">

                            <strong>
                                {{ $rider->delivered_count }}
                            </strong>


                            <span>
                                delivered
                            </span>

                        </div>

                    </article>

                @empty

                    <div class="reports-empty reports-empty-small">

                        <h3>
                            No Rider delivery data yet
                        </h3>


                        <p>
                            Completed Last-Mile deliveries
                            will appear here.
                        </p>

                    </div>

                @endforelse

            </div>

        </section>


        {{-- =================================================
            RECENT COMPLETED DELIVERIES
        ================================================== --}}

        <section class="reports-card reports-deliveries-card">

            <div class="reports-card-header">

                <div>

                    <p class="reports-eyebrow">
                        COMPLETED ORDERS
                    </p>


                    <h2>
                        Recent deliveries
                    </h2>


                    <p>
                        Latest successful Buyer handoffs.
                    </p>

                </div>


                <a
                    href="{{ route(
                        'logistics.delivery-monitoring'
                    ) }}"
                    class="reports-text-link"
                >
                    Delivery Monitoring →
                </a>

            </div>


            <div class="reports-table-scroll">

                <table class="reports-table">

                    <thead>

                        <tr>
                            <th>Order</th>
                            <th>Seller / Buyer</th>
                            <th>Pickup Rider</th>
                            <th>Route</th>
                            <th>Fee</th>
                            <th>Delivered</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse(
                            $recentDelivered
                            as $order
                        )

                            @php
                                $stops =
                                    $order
                                        ->deliveryRouteStops
                                        ->keyBy(
                                            'stop_key'
                                        );

                                $origin =
                                    $stops->get(
                                        'origin_facility'
                                    );

                                $destination =
                                    $stops->get(
                                        'destination_facility'
                                    );

                                $originName =
                                    $origin
                                        ?->facility
                                        ?->name
                                    ?: $origin
                                        ?->name
                                    ?: 'Origin hub';

                                $destinationName =
                                    $destination
                                        ?->facility
                                        ?->name
                                    ?: $destination
                                        ?->name
                                    ?: 'Destination hub';
                            @endphp


                            <tr>

                                <td>

                                    <strong>
                                        {{ $order->order_number }}
                                    </strong>

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            $order
                                                ->seller
                                                ?->store_name
                                            ?: 'SARI Seller'
                                        }}
                                    </strong>


                                    <p>
                                        Buyer:
                                        {{ $order->buyer_name }}
                                    </p>

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            $order->courier_name
                                            ?: 'Pickup Rider'
                                        }}
                                    </strong>


                                    <p>
                                        Pickup snapshot
                                    </p>

                                </td>


                                <td>

                                    <div class="reports-route">

                                        <span>
                                            {{ $originName }}
                                        </span>


                                        <b aria-hidden="true">
                                            →
                                        </b>


                                        <span>
                                            {{ $destinationName }}
                                        </span>

                                    </div>

                                </td>


                                <td>

                                    <strong>
                                        ₱{{ number_format(
                                            (float) $order
                                                ->delivery_fee,
                                            2
                                        ) }}
                                    </strong>

                                </td>


                                <td>

                                    <span class="reports-delivered-badge">
                                        Delivered
                                    </span>


                                    <p>
                                        {{
                                            $order
                                                ->delivered_at
                                                ?->timezone(
                                                    'Asia/Manila'
                                                )
                                                ->format(
                                                    'M d · g:i A'
                                                )
                                        }}
                                    </p>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="reports-table-empty"
                                >
                                    No completed deliveries yet.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</div>

@endsection