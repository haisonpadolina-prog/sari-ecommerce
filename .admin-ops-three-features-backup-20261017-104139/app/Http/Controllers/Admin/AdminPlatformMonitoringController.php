<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Services\Admin\AdminPlatformMonitoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlatformMonitoringController extends Controller
{
    public function __construct(
        private readonly AdminPlatformMonitoringService $monitoring
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $section = strtolower((string) $request->query('section', 'sellers'));
        if (!in_array($section, ['sellers', 'logistics', 'buyers', 'riders'], true)) {
            $section = 'sellers';
        }

        $search = trim((string) $request->query('q', ''));
        $status = strtolower((string) $request->query('status', 'all'));
        if (!in_array($status, ['all', 'active', 'deactivated', 'blocked', 'banned'], true)) {
            $status = 'all';
        }

        $records = match ($section) {
            'logistics' => $this->monitoring->logisticsDirectory($search, $status),
            'buyers' => $this->monitoring->buyerDirectory($search, $status),
            'riders' => $this->monitoring->riderDirectory($search, $status),
            default => $this->monitoring->sellerDirectory($search, $status),
        };

        return view('admin.monitoring.index', [
            'section' => $section,
            'search' => $search,
            'status' => $status,
            'records' => $records,
            'overview' => $this->monitoring->overview(),
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
