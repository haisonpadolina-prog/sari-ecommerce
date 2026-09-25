@php
    $liveSales = $liveSales ?? [];

    $formatNumber = static fn ($value): string => number_format((float) ($value ?? 0), 0);
    $formatMoney = static fn ($value): string => '₱' . number_format((float) ($value ?? 0), 2);
    $formatPercent = static function ($value): string {
        $number = (float) ($value ?? 0);
        $prefix = $number > 0 ? '+' : '';
        return $prefix . number_format($number, 1) . '%';
    };
    $formatCompactMoney = static function ($value): string {
        $number = (float) ($value ?? 0);
        $abs = abs($number);
        if ($abs >= 1000000) return '₱' . number_format($number / 1000000, 1) . 'M';
        if ($abs >= 1000) return '₱' . number_format($number / 1000, 1) . 'K';
        return '₱' . number_format($number, 0);
    };

    $salesSeries = array_values($liveSales['series_sales'] ?? $liveSales['weekly_sales'] ?? []);
    $commissionSeries = array_values($liveSales['series_commission'] ?? $liveSales['weekly_commission'] ?? []);
    $chartLabels = array_values($liveSales['series_labels'] ?? []);
    $chartTooltipLabels = array_values($liveSales['series_tooltip_labels'] ?? $chartLabels);

    $seriesCount = max(1, count($salesSeries), count($commissionSeries), count($chartLabels));
    $salesSeries = array_values(array_pad(array_slice($salesSeries, 0, $seriesCount), $seriesCount, 0));
    $commissionSeries = array_values(array_pad(array_slice($commissionSeries, 0, $seriesCount), $seriesCount, 0));

    while (count($chartLabels) < $seriesCount) {
        $chartLabels[] = 'Period ' . (count($chartLabels) + 1);
    }
    while (count($chartTooltipLabels) < $seriesCount) {
        $chartTooltipLabels[] = $chartLabels[count($chartTooltipLabels)] ?? 'Period';
    }

    $chartLeft = 115.0;
    $chartRight = 815.0;
    $chartX = [];
    if ($seriesCount === 1) {
        $chartX[] = ($chartLeft + $chartRight) / 2;
    } else {
        $step = ($chartRight - $chartLeft) / ($seriesCount - 1);
        for ($index = 0; $index < $seriesCount; $index++) {
            $chartX[] = round($chartLeft + ($step * $index), 1);
        }
    }

    $chartHitWidth = max(42.0, min(128.0, ($chartRight - $chartLeft) / max(1, $seriesCount)));
    $rawChartMax = max([1, ...array_map('floatval', $salesSeries), ...array_map('floatval', $commissionSeries)]);
    $magnitude = 10 ** max(0, floor(log10($rawChartMax)));
    $chartMax = ceil($rawChartMax / $magnitude) * $magnitude;
    if ($chartMax <= 0) $chartMax = 1;

    $chartY = static fn ($value): float => round(242 - ((min((float) $value, (float) $chartMax) / $chartMax) * 200), 1);
    $salesPoints = [];
    $commissionPoints = [];
    foreach ($chartX as $index => $x) {
        $salesPoints[] = [$x, $chartY($salesSeries[$index] ?? 0)];
        $commissionPoints[] = [$x, $chartY($commissionSeries[$index] ?? 0)];
    }

    $salesPath = collect($salesPoints)
        ->map(fn ($point, $index) => ($index === 0 ? 'M' : 'L') . $point[0] . ' ' . $point[1])
        ->implode(' ');
    $commissionPath = collect($commissionPoints)
        ->map(fn ($point, $index) => ($index === 0 ? 'M' : 'L') . $point[0] . ' ' . $point[1])
        ->implode(' ');

    $firstChartX = $chartX[0] ?? $chartLeft;
    $lastChartX = $chartX[count($chartX) - 1] ?? $chartRight;
    $areaPath = $salesPath . ' L' . $lastChartX . ' 242 L' . $firstChartX . ' 242 Z';
    $axisValues = [$chartMax, $chartMax * .75, $chartMax * .5, $chartMax * .25, 0];

    $hasSalesChartData = collect($salesSeries)->contains(fn ($value) => (float) $value > 0)
        || collect($commissionSeries)->contains(fn ($value) => (float) $value > 0);
@endphp

<section id="adminSalesOverview" class="sari-sales-clean-card">
    <div class="sari-sales-clean-head">
        <div>
            <h3 class="sari-sales-clean-title">Sales Overview</h3>
            <p class="sari-sales-clean-subtitle">{{ $liveSales['subtitle'] ?? 'Monthly marketplace activity' }}</p>
        </div>

        @php
            $salesPeriodOptions = [
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                'last_3_months' => 'Last 3 Months',
                'this_year' => 'This Year',
            ];
            $activeSalesPeriod = $liveSales['period'] ?? 'this_month';
        @endphp

        <details class="sari-sales-period-menu">
            <summary class="sari-sales-clean-period" aria-label="Change analytics period">
                <span>{{ $liveSales['period_label'] ?? 'This Month' }}</span>
                <svg viewBox="0 0 24 24" class="sari-sales-period-chevron h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="m8 10 4 4 4-4"></path>
                </svg>
            </summary>

            <div class="sari-sales-period-options" role="menu" aria-label="Sales reporting period">
                @foreach ($salesPeriodOptions as $periodKey => $periodLabel)
                    <button
                                type="button"
                                class="sari-sales-period-option {{ $activeSalesPeriod === $periodKey ? 'is-active' : '' }}"
                                role="menuitem"
                                data-sales-period="{{ $periodKey }}"
                                aria-current="{{ $activeSalesPeriod === $periodKey ? 'true' : 'false' }}"
                            >
                        <span>{{ $periodLabel }}</span>
                        @if ($activeSalesPeriod === $periodKey)
                            <span class="sari-sales-period-option-check" aria-hidden="true">✓</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </details>
    </div>

    <div class="sari-sales-clean-metrics">
        <div class="sari-sales-clean-metric">
            <p class="sari-sales-clean-metric-label">Orders</p>
            <p class="sari-sales-clean-metric-value">{{ $formatNumber($liveSales['orders'] ?? $liveSales['month_orders'] ?? 0) }}</p>
            <p class="sari-sales-clean-metric-note {{ (float) ($liveSales['orders_growth'] ?? 0) >= 0 ? 'is-positive' : 'is-negative' }}">
                <span>{{ (float) ($liveSales['orders_growth'] ?? 0) >= 0 ? '▲' : '▼' }} {{ $formatPercent($liveSales['orders_growth'] ?? 0) }}</span>
                <span class="is-muted">{{ $liveSales['comparison_label'] ?? 'vs last month' }}</span>
            </p>
        </div>

        <div class="sari-sales-clean-metric">
            <p class="sari-sales-clean-metric-label">Sales Growth</p>
            <p class="sari-sales-clean-metric-value">{{ $formatPercent($liveSales['growth'] ?? 0) }}</p>
            <p class="sari-sales-clean-metric-note {{ (float) ($liveSales['growth'] ?? 0) >= 0 ? 'is-positive' : 'is-negative' }}">
                <span>{{ (float) ($liveSales['growth'] ?? 0) >= 0 ? '▲' : '▼' }}</span>
                <span class="is-muted">{{ $liveSales['comparison_label'] ?? 'vs last month' }}</span>
            </p>
        </div>

        <div class="sari-sales-clean-metric">
            <p class="sari-sales-clean-metric-label">Commission</p>
            <p class="sari-sales-clean-metric-value">{{ $formatCompactMoney($liveSales['commission'] ?? 0) }}</p>
            <p class="sari-sales-clean-metric-note">
                <span class="is-muted">{{ number_format((float) ($liveSales['commission_rate'] ?? 0), 1) }}% platform rate</span>
            </p>
        </div>
    </div>

    <div class="sari-sales-clean-divider"></div>

    <div class="sari-sales-clean-chart-head">
        <div>
            <h4 class="sari-sales-clean-chart-title">Sales &amp; Commission Trend</h4>
            <p class="sari-sales-clean-chart-copy">{{ $liveSales['chart_copy'] ?? 'Weekly snapshot — each point represents one week' }}</p>
        </div>

        <div class="sari-sales-clean-legend" aria-label="Chart series controls">
            <button type="button" class="sari-sales-clean-legend-item" data-sales-series-toggle="sales" aria-pressed="true">
                <span class="sari-sales-clean-dot is-sales"></span>
                Sales
            </button>
            <button type="button" class="sari-sales-clean-legend-item" data-sales-series-toggle="commission" aria-pressed="true">
                <span class="sari-sales-clean-dot is-commission"></span>
                Commission
            </button>
        </div>
    </div>

    <div class="sari-sales-clean-chart" data-sales-chart>
        <div class="sari-sales-clean-tooltip" data-sales-tooltip aria-hidden="true">
            <p class="sari-sales-clean-tooltip-week" data-tooltip-week>Week</p>
            <div class="sari-sales-clean-tooltip-row">
                <span><i class="is-sales"></i>Sales</span>
                <strong data-tooltip-sales>₱0</strong>
            </div>
            <div class="sari-sales-clean-tooltip-row">
                <span><i class="is-commission"></i>Commission</span>
                <strong data-tooltip-commission>₱0</strong>
            </div>
        </div>

        <svg viewBox="0 0 900 286" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Sales and commission trend for {{ $liveSales['period_label'] ?? 'This Month' }}">
            <defs>
                <linearGradient id="sariSalesCleanArea" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#C79229" stop-opacity=".12"/>
                    <stop offset="100%" stop-color="#C79229" stop-opacity="0"/>
                </linearGradient>
            </defs>

            {{-- Grid --}}
            <line class="sari-sales-clean-grid-line" x1="76" y1="42" x2="858" y2="42"/>
            <line class="sari-sales-clean-grid-line" x1="76" y1="92" x2="858" y2="92"/>
            <line class="sari-sales-clean-grid-line" x1="76" y1="142" x2="858" y2="142"/>
            <line class="sari-sales-clean-grid-line" x1="76" y1="192" x2="858" y2="192"/>
            <line class="sari-sales-clean-base-line" x1="76" y1="242" x2="858" y2="242"/>

            @foreach ($chartX as $x)
                <line class="sari-sales-clean-guide-line" x1="{{ $x }}" y1="42" x2="{{ $x }}" y2="242"/>
            @endforeach

            {{-- Y-axis --}}
            @if ($hasSalesChartData)
                <text class="sari-sales-clean-axis" x="15" y="46">{{ $formatCompactMoney($axisValues[0]) }}</text>
                <text class="sari-sales-clean-axis" x="15" y="96">{{ $formatCompactMoney($axisValues[1]) }}</text>
                <text class="sari-sales-clean-axis" x="15" y="146">{{ $formatCompactMoney($axisValues[2]) }}</text>
                <text class="sari-sales-clean-axis" x="15" y="196">{{ $formatCompactMoney($axisValues[3]) }}</text>
            @endif
            <text class="sari-sales-clean-axis" x="31" y="246">₱0</text>

            {{-- Series --}}
            <path class="sari-sales-clean-area" data-sales-series="sales" d="{{ $areaPath }}" fill="url(#sariSalesCleanArea)"/>
            <path class="sari-sales-clean-line is-commission" data-sales-series="commission" d="{{ $commissionPath }}"/>
            <path class="sari-sales-clean-line is-sales" data-sales-series="sales" d="{{ $salesPath }}"/>

            @foreach ($salesPoints as $index => [$cx,$cy])
                <circle class="sari-sales-clean-point is-sales" data-sales-series="sales" cx="{{ $cx }}" cy="{{ $cy }}" r="4.6"/>
                <circle class="sari-sales-clean-point is-commission" data-sales-series="commission" cx="{{ $commissionPoints[$index][0] }}" cy="{{ $commissionPoints[$index][1] }}" r="3.6"/>
            @endforeach

            {{-- Interactive hit areas --}}
            @foreach ($chartX as $index => $x)
                <line class="sari-sales-clean-hover-guide" data-chart-guide="{{ $index }}" x1="{{ $x }}" y1="42" x2="{{ $x }}" y2="242"/>
                <rect
                    class="sari-sales-clean-hitbox"
                    x="{{ round($x - ($chartHitWidth / 2), 1) }}"
                    y="34"
                    width="{{ round($chartHitWidth, 1) }}"
                    height="216"
                    tabindex="0"
                    role="button"
                    aria-label="{{ $chartTooltipLabels[$index] ?? $chartLabels[$index] ?? 'Period' }}: sales {{ $formatMoney($salesSeries[$index] ?? 0) }}, commission {{ $formatMoney($commissionSeries[$index] ?? 0) }}"
                    data-chart-hit
                    data-guide-index="{{ $index }}"
                    data-week="{{ $chartTooltipLabels[$index] ?? $chartLabels[$index] ?? 'Period' }}"
                    data-sales="{{ number_format((float) ($salesSeries[$index] ?? 0), 2, '.', '') }}"
                    data-commission="{{ number_format((float) ($commissionSeries[$index] ?? 0), 2, '.', '') }}"
                ></rect>
            @endforeach

            {{-- X-axis --}}
            @foreach ($chartX as $index => $x)
                <text class="sari-sales-clean-axis" x="{{ $x }}" y="274" text-anchor="middle">{{ $chartLabels[$index] ?? 'Period ' . ($index + 1) }}</text>
            @endforeach
        </svg>
    </div>

    @if (! $hasSalesChartData)
        <div class="sari-sales-clean-empty">
            <span class="sari-sales-clean-empty-icon">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 19V13M10 19V9M15 19v-4M20 19V6"></path>
                </svg>
            </span>
            <div>
                <p class="sari-sales-clean-empty-title">No sales activity yet this period</p>
                <p class="sari-sales-clean-empty-copy">Sales and commission will appear here once delivered orders are recorded.</p>
            </div>
        </div>
    @endif
</section>
