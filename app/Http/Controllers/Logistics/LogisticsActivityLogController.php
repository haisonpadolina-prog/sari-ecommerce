<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Logistics\LogisticsActivityLog;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class LogisticsActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Resolve Authenticated Logistics Account
        |--------------------------------------------------------------------------
        */

        $provider = CurrentLogisticsAccount::resolve($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);

        $hub = $isHubOperator
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        /*
        |--------------------------------------------------------------------------
        | Validate Filters
        |--------------------------------------------------------------------------
        */

        $filters = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:80',
            ],

            'module' => [
                'nullable',
                'string',
                'max:45',
            ],

            'facility' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'from' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        |
        | All records must belong to the authenticated
        | Logistics provider.
        |
        | Hub Operators are restricted to their assigned
        | facility using server-side identity.
        |
        */

        $base = LogisticsActivityLog::query()
            ->where(
                'logistics_account_id',
                $provider->id
            );

        if ($hub) {
            $base->where(
                'logistics_facility_id',
                $hub->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Activity Statistics
        |--------------------------------------------------------------------------
        */

        $totalCount = (clone $base)->count();

        $localToday = Carbon::now('Asia/Manila');

        $todayCount = (clone $base)
            ->whereBetween('created_at', [
                $localToday->copy()
                    ->startOfDay()
                    ->utc(),

                $localToday->copy()
                    ->endOfDay()
                    ->utc(),
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Available Module Filters
        |--------------------------------------------------------------------------
        */

        $modules = (clone $base)
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        /*
        |--------------------------------------------------------------------------
        | Facility Filters
        |--------------------------------------------------------------------------
        |
        | Only Main Logistics can select between hubs.
        |
        */

        $facilities = $isHubOperator
            ? collect()
            : LogisticsFacility::query()
                ->where(
                    'logistics_account_id',
                    $provider->id
                )
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                ]);

        /*
        |--------------------------------------------------------------------------
        | Activity History Query
        |--------------------------------------------------------------------------
        */

        $logs = clone $base;

        $keyword = trim(
            (string) ($filters['q'] ?? '')
        );

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($keyword !== '') {
            $escaped = addcslashes(
                $keyword,
                '%_\\'
            );

            $logs->where(function ($query) use ($escaped): void {
                $query
                    ->where(
                        'actor_name',
                        'like',
                        '%' . $escaped . '%'
                    )
                    ->orWhere(
                        'description',
                        'like',
                        '%' . $escaped . '%'
                    )
                    ->orWhere(
                        'facility_name',
                        'like',
                        '%' . $escaped . '%'
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Module Filter
        |--------------------------------------------------------------------------
        */

        if (filled($filters['module'] ?? null)) {
            $logs->where(
                'module',
                $filters['module']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Facility Filter
        |--------------------------------------------------------------------------
        |
        | Ignore browser-supplied facility filters for Hub Operators.
        | Main Logistics may only filter facilities belonging
        | to its own provider account.
        |
        */

        if (
            ! $isHubOperator &&
            filled($filters['facility'] ?? null)
        ) {
            $facilityId = (int) $filters['facility'];

            abort_unless(
                $facilities->contains('id', $facilityId),
                404
            );

            $logs->where(
                'logistics_facility_id',
                $facilityId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From Filter
        |--------------------------------------------------------------------------
        */

        if (filled($filters['from'] ?? null)) {
            $logs->where(
                'created_at',
                '>=',
                Carbon::parse(
                    $filters['from'],
                    'Asia/Manila'
                )
                    ->startOfDay()
                    ->utc()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To Filter
        |--------------------------------------------------------------------------
        */

        if (filled($filters['to'] ?? null)) {
            $logs->where(
                'created_at',
                '<=',
                Carbon::parse(
                    $filters['to'],
                    'Asia/Manila'
                )
                    ->endOfDay()
                    ->utc()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Order & Pagination
        |--------------------------------------------------------------------------
        |
        | Latest activities appear first.
        | Preserve filters when switching pages.
        |
        */

        $logs = $logs
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Render Activity Logs
        |--------------------------------------------------------------------------
        */

        return view(
            'logistics.activity-logs',
            compact(
                'logs',
                'provider',
                'hub',
                'isHubOperator',
                'totalCount',
                'todayCount',
                'modules',
                'facilities',
                'filters',
                'keyword'
            )
        );
    }
}
