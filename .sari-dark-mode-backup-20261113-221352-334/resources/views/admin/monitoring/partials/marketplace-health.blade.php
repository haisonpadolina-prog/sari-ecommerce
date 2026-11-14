@php
    $healthScore = max(0, min(100, (int) ($liveHealth['score'] ?? 100)));
    $healthLabel = match (true) {
        $healthScore >= 85 => 'Healthy',
        $healthScore >= 70 => 'Watch',
        default => 'At Risk',
    };
@endphp


<style>
    /* Marketplace Health partial: final graph-specific authority */
    #adminMarketplaceHealth {
        padding: 15px 16px !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 18px !important;
        background: #FFFFFF !important;
        background-image: none !important;
        box-shadow: 0 6px 18px rgba(17,24,39,.05) !important;
    }

    #adminMarketplaceHealth .sari-health-header {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 12px !important;
        padding-bottom: 13px !important;
        border-bottom: 1px solid #E5E7EB !important;
    }

    #adminMarketplaceHealth .sari-health-title {
        font-size: 17px !important;
        line-height: 1.28 !important;
        font-weight: 700 !important;
        color: #1F1B17 !important;
    }

    #adminMarketplaceHealth .sari-health-subtitle {
        margin-top: 4px !important;
        font-size: 10px !important;
        line-height: 1.5 !important;
        color: #7B8490 !important;
    }

    #adminMarketplaceHealth .sari-health-actions {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex: 0 0 auto !important;
    }

    #adminMarketplaceHealth .sari-health-stable,
    #adminMarketplaceHealth .sari-health-action {
        display: inline-flex !important;
        min-height: 34px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border-radius: 999px !important;
        padding: 0 12px !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    #adminMarketplaceHealth .sari-health-stable {
        border: 1px solid #E5E7EB !important;
        background: #FFFFFF !important;
        color: #8A6214 !important;
    }

    #adminMarketplaceHealth .sari-health-action {
        border: 1px solid #D9C293 !important;
        background: #FFFFFF !important;
        color: #8A6214 !important;
    }

    #adminMarketplaceHealth .sari-health-main--graph-only {
        display: block !important;
        margin-top: 13px !important;
    }

    #adminMarketplaceHealth .sari-health-graph-panel--compact {
        padding: 14px !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 18px !important;
        background: #FFFFFF !important;
        background-image: none !important;
        box-shadow: none !important;
    }

    #adminMarketplaceHealth .sari-health-graph-head--compact {
        display: flex !important;
        align-items: flex-end !important;
        justify-content: space-between !important;
        gap: 14px !important;
        margin-bottom: 8px !important;
    }

    #adminMarketplaceHealth .sari-health-graph-kicker {
        font-size: 8px !important;
        line-height: 1.25 !important;
        font-weight: 800 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        color: #9B721E !important;
    }

    #adminMarketplaceHealth .sari-health-graph-score-line {
        display: flex !important;
        align-items: flex-end !important;
        gap: 10px !important;
        margin-top: 4px !important;
        flex-wrap: wrap !important;
    }

    #adminMarketplaceHealth .sari-health-graph-score-line strong {
        font-size: 30px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: -.05em !important;
        color: #202124 !important;
    }

    #adminMarketplaceHealth .sari-health-graph-score-line span {
        margin-bottom: 3px !important;
        font-size: 10px !important;
        color: #7B8490 !important;
    }

    #adminMarketplaceHealth .sari-health-graph-score-line em {
        display: inline-flex !important;
        min-height: 26px !important;
        margin-bottom: 3px !important;
        align-items: center !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 999px !important;
        background: #FFFFFF !important;
        padding: 0 10px !important;
        font-size: 9px !important;
        font-style: normal !important;
        font-weight: 700 !important;
        color: #8A6214 !important;
    }

    #adminMarketplaceHealth .sari-health-graph-description {
        max-width: 560px !important;
        margin-top: 3px !important;
        font-size: 8.8px !important;
        line-height: 1.55 !important;
        color: #7B8490 !important;
    }

    #adminMarketplaceHealth .sari-health-graph-side {
        display: flex !important;
        min-width: 112px !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        gap: 2px !important;
    }

    #adminMarketplaceHealth .sari-health-graph-side-label {
        font-size: 8px !important;
        font-weight: 700 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase !important;
        color: #9B9388 !important;
    }

    #adminMarketplaceHealth .sari-health-graph-side strong {
        font-size: 16px !important;
        line-height: 1.1 !important;
        font-weight: 700 !important;
        color: #8A6214 !important;
    }

    #adminMarketplaceHealth .sari-health-graph-canvas--white {
        overflow: hidden !important;
        padding: 10px 12px 8px !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 18px !important;
        background: #FFFFFF !important;
        background-image: none !important;
        box-shadow: none !important;
    }

    #adminMarketplaceHealth .sari-health-graph-svg {
        display: block !important;
        width: 100% !important;
        height: auto !important;
    }

    #adminMarketplaceHealth .sari-health-graph-grid-line {
        stroke: #E8ECF1 !important;
        stroke-width: 1 !important;
    }

    #adminMarketplaceHealth .sari-health-profile-area {
        stroke: none !important;
    }

    #adminMarketplaceHealth .sari-health-profile-area.is-order { fill: url(#mhAreaOrder) !important; }
    #adminMarketplaceHealth .sari-health-profile-area.is-fulfillment { fill: url(#mhAreaFulfillment) !important; }
    #adminMarketplaceHealth .sari-health-profile-area.is-seller { fill: url(#mhAreaSeller) !important; }
    #adminMarketplaceHealth .sari-health-profile-area.is-approval { fill: url(#mhAreaApproval) !important; }

    #adminMarketplaceHealth .sari-health-profile-line {
        fill: none !important;
        stroke-linecap: round !important;
        stroke-linejoin: round !important;
        opacity: .98 !important;
    }

    #adminMarketplaceHealth .sari-health-profile-line.is-order {
        stroke: #A96F10 !important;
        stroke-width: 4.2 !important;
    }

    #adminMarketplaceHealth .sari-health-profile-line.is-fulfillment {
        stroke: #D59617 !important;
        stroke-width: 3.9 !important;
    }

    #adminMarketplaceHealth .sari-health-profile-line.is-seller {
        stroke: #E2B24B !important;
        stroke-width: 3.6 !important;
    }

    #adminMarketplaceHealth .sari-health-profile-line.is-approval {
        stroke: #C79B45 !important;
        stroke-width: 3.2 !important;
    }

    #adminMarketplaceHealth .sari-health-graph-legend,
    #adminMarketplaceHealth .sari-health-metrics,
    #adminMarketplaceHealth .sari-health-summary {
        display: none !important;
    }

    @media (max-width: 767px) {
        #adminMarketplaceHealth {
            padding: 14px !important;
        }

        #adminMarketplaceHealth .sari-health-header,
        #adminMarketplaceHealth .sari-health-graph-head--compact {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        #adminMarketplaceHealth .sari-health-actions {
            width: 100% !important;
            justify-content: space-between !important;
        }

        #adminMarketplaceHealth .sari-health-graph-side {
            align-items: flex-start !important;
        }
    }
</style>

<section id="adminMarketplaceHealth" class="sari-health-card p-5 sm:p-6">
            <div class="sari-health-header">
                <div class="sari-health-heading">
                    <div class="min-w-0">
                        <h4 class="sari-health-title">Marketplace Health</h4>
                        <p class="sari-health-subtitle">Operational health across the full marketplace</p>
                    </div>
                </div>

                <div class="sari-health-actions">
                    <span class="sari-health-stable">
                        <span class="h-2 w-2 rounded-full bg-[#3d9666]"></span>
                        Stable
                    </span>
                    <a href="{{ $liveUrls['reports'] ?? '#' }}" class="sari-health-action">
                        View Details
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="m9 6 6 6-6 6"></path>
                        </svg>
                    </a>
                </div>
            </div>

            @php
                $healthMetrics = [
                    [
                        'key' => 'order_success',
                        'label' => 'Order Success Rate',
                        'value' => number_format((float) ($liveHealth['order_success'] ?? 0), 1) . '%',
                        'width' => max(0, min(100, (float) ($liveHealth['order_success'] ?? 0))) . '%',
                        'note' => 'Delivered vs terminal orders',
                        'icon' => 'cart',
                    ],
                    [
                        'key' => 'fulfillment',
                        'label' => 'Fulfillment Health',
                        'value' => number_format((float) ($liveHealth['fulfillment'] ?? 0), 1) . '%',
                        'width' => max(0, min(100, (float) ($liveHealth['fulfillment'] ?? 0))) . '%',
                        'note' => 'Current delivery flow',
                        'icon' => 'box',
                    ],
                    [
                        'key' => 'seller_active',
                        'label' => 'Seller Active Rate',
                        'value' => number_format((float) ($liveHealth['seller_active'] ?? 0), 1) . '%',
                        'width' => max(0, min(100, (float) ($liveHealth['seller_active'] ?? 0))) . '%',
                        'note' => 'Active seller accounts',
                        'icon' => 'users',
                    ],
                    [
                        'key' => 'registration_approval',
                        'label' => 'Registration Approval',
                        'value' => number_format((float) ($liveHealth['registration_approval'] ?? 0), 1) . '%',
                        'width' => max(0, min(100, (float) ($liveHealth['registration_approval'] ?? 0))) . '%',
                        'note' => 'Reviewed applications',
                        'icon' => 'file',
                    ],
                ];
            @endphp

            @php
                $chartMetrics = collect($healthMetrics)->map(function ($metric) use ($liveHealth) {
                    $shortLabel = match ($metric['key']) {
                        'order_success' => 'Order Success',
                        'fulfillment' => 'Fulfillment',
                        'seller_active' => 'Seller Active',
                        'registration_approval' => 'Approval',
                        default => $metric['label'],
                    };

                    return [
                        'key' => $metric['key'],
                        'label' => $shortLabel,
                        'raw' => max(0, min(100, (float) ($liveHealth[$metric['key']] ?? 0))),
                        'display' => $metric['value'],
                    ];
                })->values();

                $chartWidth = 760;
                $chartHeight = 246;
                $chartPadLeft = 38;
                $chartPadRight = 24;
                $chartPadTop = 18;
                $chartPadBottom = 16;
                $plotWidth = $chartWidth - $chartPadLeft - $chartPadRight;
                $plotHeight = $chartHeight - $chartPadTop - $chartPadBottom;
                $chartBaseline = $chartPadTop + $plotHeight;
                $chartGridValues = [20, 40, 60, 80];

                $seriesStyles = [
                    ['class' => 'is-order', 'peakA' => 78, 'peakB' => 34, 'peakC' => 66],
                    ['class' => 'is-fulfillment', 'peakA' => 56, 'peakB' => 82, 'peakC' => 44],
                    ['class' => 'is-seller', 'peakA' => 24, 'peakB' => 64, 'peakC' => 18],
                    ['class' => 'is-approval', 'peakA' => 12, 'peakB' => 20, 'peakC' => 10],
                ];

                $profileXs = [
                    $chartPadLeft,
                    round($chartPadLeft + ($plotWidth * .16), 1),
                    round($chartPadLeft + ($plotWidth * .32), 1),
                    round($chartPadLeft + ($plotWidth * .50), 1),
                    round($chartPadLeft + ($plotWidth * .70), 1),
                    round($chartPadLeft + ($plotWidth * .86), 1),
                    $chartWidth - $chartPadRight,
                ];

                $chartSeries = $chartMetrics->values()->map(function ($metric, $index) use (
                    $seriesStyles,
                    $profileXs,
                    $chartBaseline,
                    $plotHeight,
                    $chartPadTop
                ) {
                    $style = $seriesStyles[$index] ?? $seriesStyles[0];

                    $values = [
                        6,
                        $style['peakA'],
                        max(8, $style['peakA'] - 18),
                        $style['peakB'],
                        max(10, $style['peakC']),
                        max(8, $style['peakC'] - 10),
                        6,
                    ];

                    $points = collect($profileXs)->map(function ($x, $pointIndex) use ($values, $chartBaseline, $plotHeight, $chartPadTop) {
                        $value = $values[$pointIndex] ?? 6;
                        $y = $chartBaseline - (($value / 100) * $plotHeight);
                        $y = max($chartPadTop + 5, min($chartBaseline - 4, $y));

                        return ['x' => round($x, 1), 'y' => round($y, 1)];
                    })->values()->all();

                    $linePath = '';
                    if (count($points) > 0) {
                        $linePath = 'M ' . $points[0]['x'] . ' ' . $points[0]['y'];

                        for ($i = 1; $i < count($points); $i++) {
                            $previous = $points[$i - 1];
                            $current = $points[$i];
                            $midX = round(($previous['x'] + $current['x']) / 2, 1);

                            $linePath .= ' C '
                                . $midX . ' ' . $previous['y'] . ', '
                                . $midX . ' ' . $current['y'] . ', '
                                . $current['x'] . ' ' . $current['y'];
                        }
                    }

                    $areaPath = $linePath !== ''
                        ? $linePath
                            . ' L ' . $profileXs[count($profileXs) - 1] . ' ' . $chartBaseline
                            . ' L ' . $profileXs[0] . ' ' . $chartBaseline
                            . ' Z'
                        : '';

                    return array_merge($metric, [
                        'class' => $style['class'],
                        'line_path' => $linePath,
                        'area_path' => $areaPath,
                    ]);
                })->all();
            @endphp

            <div class="sari-health-main sari-health-main--graph-only">
                <div class="sari-health-graph-panel sari-health-graph-panel--compact sari-health-graph-panel--white">
                    <div class="sari-health-graph-head sari-health-graph-head--compact">
                        <div class="sari-health-graph-copy">
                            <p class="sari-health-graph-kicker">Health score</p>
                            <div class="sari-health-graph-score-line">
                                <strong>{{ $healthScore }}</strong>
                                <span>out of 100</span>
                                <em>{{ $healthLabel }}</em>
                            </div>
                            <p class="sari-health-graph-description">
                                {{ $healthScore >= 85
                                    ? 'Your marketplace is performing well across key operational areas.'
                                    : ($healthScore >= 70
                                        ? 'Marketplace performance is stable, with a few areas worth monitoring.'
                                        : 'Marketplace health needs attention across multiple operational areas.') }}
                            </p>
                        </div>

                        <div class="sari-health-graph-side">
                            <span class="sari-health-graph-side-label">Health profile</span>
                            <strong>{{ $healthLabel }}</strong>
                        </div>
                    </div>

                    <div class="sari-health-graph-canvas sari-health-graph-canvas--compact sari-health-graph-canvas--white">
                        <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="sari-health-graph-svg" role="img" aria-label="Current marketplace health mountain profile">
                            <defs>
                                <linearGradient id="mhAreaOrder" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#A96F10" stop-opacity="0.28"></stop>
                                    <stop offset="100%" stop-color="#A96F10" stop-opacity="0"></stop>
                                </linearGradient>
                                <linearGradient id="mhAreaFulfillment" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#D59617" stop-opacity="0.22"></stop>
                                    <stop offset="100%" stop-color="#D59617" stop-opacity="0"></stop>
                                </linearGradient>
                                <linearGradient id="mhAreaSeller" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#E2B24B" stop-opacity="0.20"></stop>
                                    <stop offset="100%" stop-color="#E2B24B" stop-opacity="0"></stop>
                                </linearGradient>
                                <linearGradient id="mhAreaApproval" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#C79B45" stop-opacity="0.16"></stop>
                                    <stop offset="100%" stop-color="#C79B45" stop-opacity="0"></stop>
                                </linearGradient>
                            </defs>

                            @foreach ($chartGridValues as $gridValue)
                                @php
                                    $gridY = $chartBaseline - (($gridValue / 100) * $plotHeight);
                                @endphp
                                <line
                                    x1="{{ $chartPadLeft }}"
                                    y1="{{ $gridY }}"
                                    x2="{{ $chartWidth - $chartPadRight }}"
                                    y2="{{ $gridY }}"
                                    class="sari-health-graph-grid-line"
                                ></line>
                            @endforeach

                            @foreach ($chartSeries as $series)
                                <path
                                    d="{{ $series['area_path'] }}"
                                    class="sari-health-profile-area {{ $series['class'] }}"
                                ></path>
                            @endforeach

                            @foreach ($chartSeries as $series)
                                <path
                                    d="{{ $series['line_path'] }}"
                                    class="sari-health-profile-line {{ $series['class'] }}"
                                ></path>
                            @endforeach
                        </svg>
                    </div>
                </div>
            </div>
        </section>
