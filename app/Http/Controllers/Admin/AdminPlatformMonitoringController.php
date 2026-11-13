<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Services\Admin\AdminPlatformMonitoringService;
use App\Services\Admin\AdminSecurityCenterService;
use App\Support\Pdf\MonitoringRosterPdf;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlatformMonitoringController extends Controller
{
    public function __construct(
        private readonly AdminPlatformMonitoringService $monitoring,
        private readonly AdminSecurityCenterService $security
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $section = strtolower((string) $request->query('section', 'sellers'));
        if (!in_array($section, ['sellers', 'logistics', 'buyers', 'sla'], true)) {
            $section = 'sellers';
        }

        $search = trim((string) $request->query('q', ''));
        $status = strtolower((string) $request->query('status', 'all'));
        if (!in_array($status, ['all', 'active', 'deactivated', 'blocked', 'banned'], true)) {
            $status = 'all';
        }

        $slaRows = $this->security->deliverySlaOrders(
            $section === 'sla' ? $search : ''
        );
        $slaSummary = $this->security->deliverySlaSummary($slaRows);

        $records = match ($section) {
            'logistics' => $this->monitoring->logisticsDirectory($search, $status),
            'buyers' => $this->monitoring->buyerDirectory($search, $status),
            'sla' => $slaRows,
            default => $this->monitoring->sellerDirectory($search, $status),
        };

        return view('admin.monitoring.index', [
            'section' => $section,
            'search' => $search,
            'status' => $status,
            'records' => $records,
            'overview' => $this->monitoring->overview(),
            'slaSummary' => $slaSummary,
        ]);
    }

    /** Download all matching records in a real PDF; never export Riders as an Admin-managed directory. */
    public function exportPdf(Request $request): Response
    {
        $this->guard($request);

        $section = strtolower((string) $request->query('section', 'sellers'));
        abort_unless(in_array($section, ['sellers', 'logistics', 'buyers', 'sla'], true), 404);

        $search = trim((string) $request->query('q', ''));
        $status = strtolower((string) $request->query('status', 'all'));
        if (!in_array($status, ['all', 'active', 'deactivated', 'blocked', 'banned'], true)) {
            $status = 'all';
        }

        $rows = [];
        if ($section === 'sla') {
            foreach ($this->security->deliverySlaOrders($search) as $record) {
                $rows[] = [
                    (string) $record['order_number'],
                    (string) $record['status_label'],
                    (string) $record['seller_name'],
                    (string) $record['logistics_name'],
                    number_format((float) $record['elapsed_hours'], 1) . ' hours',
                    $record['is_breached'] ? strtoupper((string) $record['severity']) . ' BREACH' : 'On track',
                ];
            }
        } else {
            // Iterate using bounded pages, not one unbounded relationship query.
            $page = 1;
            do {
                $records = match ($section) {
                    'sellers' => $this->monitoring->sellerDirectory($search, $status, 200, $page),
                    'logistics' => $this->monitoring->logisticsDirectory($search, $status, 200, $page),
                    default => $this->monitoring->buyerDirectory($search, $status, 200, $page),
                };
                foreach ($records as $record) {
                    $location = collect([$record->municipality_name, $record->province_name])->filter()->implode(', ') ?: '-';
                    if ($section === 'sellers') {
                        $rows[] = [
                            (string) ($record->store_name ?: 'Unnamed Store'),
                            (string) $record->email,
                            $location,
                            (string) $record->products_count . ' products',
                            (string) $record->marketplace_orders_count . ' orders',
                            (string) ($record->account_status ?: 'active'),
                        ];
                    } elseif ($section === 'logistics') {
                        $rows[] = [
                            (string) ($record->business_name ?: $record->displayName()),
                            (string) $record->email,
                            $location,
                            (string) $record->facilities_count . ' hubs',
                            (string) $record->active_deliveries_count . ' active',
                            (string) ($record->account_status ?: 'active'),
                        ];
                    } else {
                        $rows[] = [
                            (string) $record->display_name,
                            (string) ($record->email ?: '-'),
                            $location,
                            (string) $record->orders_count . ' orders',
                            (string) $record->delivered_orders_count . ' delivered',
                            (string) ($record->account_status ?: 'active'),
                        ];
                    }
                }
                $page++;
            } while ($records->hasMorePages());
        }

        $generated = now('Asia/Manila')->format('M d, Y h:i A');
        $pdf = MonitoringRosterPdf::render($rows, $section, $status, $search, $generated);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="sari-platform-monitoring-' . $section . '-' . now('Asia/Manila')->format('Ymd-His') . '.pdf"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function seller(Request $request, SellerAccount $seller): View
    {
        $this->guard($request);

        return view('admin.monitoring.detail', $this->monitoring->sellerDetail($seller));
    }

    public function logistics(Request $request, LogisticsAccount $logistics): View
    {
        $this->guard($request);

        return view('admin.monitoring.detail', $this->monitoring->logisticsDetail($logistics));
    }

    public function buyer(Request $request, string $role, int $id): View
    {
        $this->guard($request);
        abort_unless(in_array($role, ['buyer', 'social_buyer'], true), 404);

        return view('admin.monitoring.detail', $this->monitoring->buyerDetail($role, $id));
    }

    public function rider(Request $request, CourierAccount $rider): View
    {
        $this->guard($request);

        return view('admin.monitoring.detail', $this->monitoring->riderDetail($rider));
    }

    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_admin'),
            403,
            'Administrator session required.'
        );
    }
}
