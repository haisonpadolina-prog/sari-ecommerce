<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\RegistrationApplication;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminReportsController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $period = $this->resolvePeriod($request);
        $previousPeriod = $this->previousPeriod($period);

        $stats = $this->statsForPeriod($period);
        $previousStats = $this->statsForPeriod($previousPeriod);
        $comparisons = $this->buildComparisons($stats, $previousStats);

        $statusBreakdown = MarketplaceOrder::query()
            ->whereBetween('created_at', [$period['from'], $period['to']])
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->orderByDesc('aggregate')
            ->pluck('aggregate', 'status')
            ->map(fn ($count) => (int) $count);

        $recent = MarketplaceOrder::query()
            ->whereBetween('created_at', [$period['from'], $period['to']])
            ->latest('created_at')
            ->limit(8)
            ->get();

        $dailySeries = $this->dailyOrderSeries($period);
        $registrationSeries = $this->dailyRegistrationSeries($period);

        return view('admin.reports', compact(
            'stats',
            'previousStats',
            'comparisons',
            'statusBreakdown',
            'recent',
            'dailySeries',
            'registrationSeries',
            'period',
            'previousPeriod',
        ));
    }

    public function export(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $period = $this->resolvePeriod($request);
        $stats = $this->statsForPeriod($period);
        $previousPeriod = $this->previousPeriod($period);
        $previousStats = $this->statsForPeriod($previousPeriod);
        $comparisons = $this->buildComparisons($stats, $previousStats);

        $statusBreakdown = MarketplaceOrder::query()
            ->whereBetween('created_at', [$period['from'], $period['to']])
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->orderByDesc('aggregate')
            ->pluck('aggregate', 'status')
            ->map(fn ($count) => (int) $count);

        $orders = MarketplaceOrder::query()
            ->whereBetween('created_at', [$period['from'], $period['to']])
            ->latest('created_at')
            ->get();

        $applications = RegistrationApplication::query()
            ->whereBetween('created_at', [$period['from'], $period['to']])
            ->latest('created_at')
            ->get();

        /*
         * No third-party PDF package is required.
         *
         * This route renders a professional A4-landscape print view.
         * The view automatically opens the browser's native Print / Save as PDF
         * dialog, which avoids raw CSV/text rendering and the missing DomPDF
         * dependency.
         */
        return view('admin.reports-pdf', [
            'period' => $period,
            'stats' => $stats,
            'comparisons' => $comparisons,
            'statusBreakdown' => $statusBreakdown,
            'orders' => $orders,
            'applications' => $applications,
            'generatedAt' => now(),
        ]);
    }

    private function resolvePeriod(Request $request): array
    {
        $validated = validator($request->only(['from', 'to']), [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ])->validate();

        $today = CarbonImmutable::today();

        $from = !empty($validated['from'])
            ? CarbonImmutable::parse($validated['from'])->startOfDay()
            : $today->startOfMonth();

        $to = !empty($validated['to'])
            ? CarbonImmutable::parse($validated['to'])->endOfDay()
            : $today->endOfDay();

        if ($to->lessThan($from)) {
            throw ValidationException::withMessages([
                'to' => 'The report end date must be on or after the start date.',
            ]);
        }

        $days = $from->startOfDay()->diffInDays($to->startOfDay()) + 1;

        if ($days > 366) {
            throw ValidationException::withMessages([
                'from' => 'Choose a report range of 366 days or less.',
            ]);
        }

        return [
            'from' => $from,
            'to' => $to,
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
            'days' => $days,
            'label' => $from->format('M j, Y').' – '.$to->format('M j, Y'),
        ];
    }

    private function previousPeriod(array $period): array
    {
        $previousTo = $period['from']->subSecond()->endOfDay();
        $previousFrom = $previousTo->startOfDay()->subDays($period['days'] - 1);

        return [
            'from' => $previousFrom,
            'to' => $previousTo,
            'from_date' => $previousFrom->toDateString(),
            'to_date' => $previousTo->toDateString(),
            'days' => $period['days'],
            'label' => $previousFrom->format('M j, Y').' – '.$previousTo->format('M j, Y'),
        ];
    }

    private function statsForPeriod(array $period): array
    {
        $orderBase = MarketplaceOrder::query()
            ->whereBetween('created_at', [$period['from'], $period['to']]);

        $registrationBase = RegistrationApplication::query()
            ->whereBetween('created_at', [$period['from'], $period['to']]);

        return [
            'orders' => (clone $orderBase)->count(),
            'active' => (clone $orderBase)->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'delivered' => (clone $orderBase)->where('status', 'delivered')->count(),
            'cancelled' => (clone $orderBase)->where('status', 'cancelled')->count(),
            'gmv' => (float) (clone $orderBase)->where('status', 'delivered')->sum('total'),
            'delivery_fees' => (float) (clone $orderBase)->where('status', 'delivered')->sum('delivery_fee'),
            'registrations' => (clone $registrationBase)->count(),
            'pending_registrations' => (clone $registrationBase)->where('status', 'pending')->count(),
        ];
    }

    private function buildComparisons(array $current, array $previous): array
    {
        return collect($current)
            ->mapWithKeys(fn ($value, $key) => [
                $key => $this->percentageChange((float) $value, (float) ($previous[$key] ?? 0)),
            ])
            ->all();
    }

    private function percentageChange(float $current, float $previous): ?float
    {
        if (abs($previous) < 0.000001) {
            return abs($current) < 0.000001 ? 0.0 : null;
        }

        return (($current - $previous) / abs($previous)) * 100;
    }

    private function dailyOrderSeries(array $period): array
    {
        $rows = MarketplaceOrder::query()
            ->whereBetween('created_at', [$period['from'], $period['to']])
            ->selectRaw('DATE(created_at) as report_date')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered_count', ['delivered'])
            ->selectRaw('SUM(CASE WHEN status = ? THEN total ELSE 0 END) as gmv', ['delivered'])
            ->selectRaw('SUM(CASE WHEN status = ? THEN delivery_fee ELSE 0 END) as delivery_fees', ['delivered'])
            ->groupBy('report_date')
            ->orderBy('report_date')
            ->get()
            ->keyBy(fn ($row) => (string) $row->report_date);

        return $this->dateKeys($period)
            ->map(function (string $date) use ($rows): array {
                $row = $rows->get($date);

                return [
                    'date' => $date,
                    'orders' => (int) ($row->orders_count ?? 0),
                    'delivered' => (int) ($row->delivered_count ?? 0),
                    'gmv' => (float) ($row->gmv ?? 0),
                    'delivery_fees' => (float) ($row->delivery_fees ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    private function dailyRegistrationSeries(array $period): array
    {
        $rows = RegistrationApplication::query()
            ->whereBetween('created_at', [$period['from'], $period['to']])
            ->selectRaw('DATE(created_at) as report_date')
            ->selectRaw('COUNT(*) as registrations_count')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_count', ['pending'])
            ->groupBy('report_date')
            ->orderBy('report_date')
            ->get()
            ->keyBy(fn ($row) => (string) $row->report_date);

        return $this->dateKeys($period)
            ->map(function (string $date) use ($rows): array {
                $row = $rows->get($date);

                return [
                    'date' => $date,
                    'registrations' => (int) ($row->registrations_count ?? 0),
                    'pending' => (int) ($row->pending_count ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    private function dateKeys(array $period): Collection
    {
        $dates = collect();
        $cursor = $period['from']->startOfDay();
        $end = $period['to']->startOfDay();

        while ($cursor->lessThanOrEqualTo($end)) {
            $dates->push($cursor->toDateString());
            $cursor = $cursor->addDay();
        }

        return $dates;
    }

    private function csvSafe(mixed $value): string
    {
        $text = (string) ($value ?? '');

        if ($text !== '' && preg_match('/^[=+\-@]/', $text) === 1) {
            return "'".$text;
        }

        return $text;
    }
}
