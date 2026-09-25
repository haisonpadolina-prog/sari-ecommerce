<?php
/**
 * SARI ERP Order Flow Phase 1 Installer
 *
 * Adds the missing Logistics pickup verification step without changing
 * the existing Buyer -> Seller -> Rider order model or database schema.
 *
 * Run from the Laravel project root:
 *   php install-erp-order-flow-phase1.php
 */

declare(strict_types=1);

$root = __DIR__;

$files = [
    'routes/logistics.php',
    'app/Http/Controllers/LogisticsPickupRequestController.php',
    'app/Http/Controllers/LogisticsDeliveryAssignmentController.php',
    'resources/views/logistics/pickup-requests.blade.php',
];

foreach ($files as $relative) {
    if (!is_file($root . DIRECTORY_SEPARATOR . $relative)) {
        fwrite(STDERR, "Missing required file: {$relative}\n");
        exit(1);
    }
}

function backupFile(string $path): void
{
    $backup = $path . '.phase1.bak';
    if (!is_file($backup)) {
        copy($path, $backup);
    }
}

function replaceOnce(string $contents, string $search, string $replace, string $label): string
{
    if (str_contains($contents, $replace)) {
        echo "[skip] {$label} already installed.\n";
        return $contents;
    }

    if (!str_contains($contents, $search)) {
        fwrite(STDERR, "[error] Could not find patch anchor: {$label}\n");
        exit(1);
    }

    return preg_replace(
        '/' . preg_quote($search, '/') . '/',
        str_replace(['\\', '$'], ['\\\\', '\\$'], $replace),
        $contents,
        1
    ) ?? $contents;
}

/* --------------------------------------------------------------------------
 | 1) ROUTE: Logistics verifies Seller pickup request before Rider assignment
 * -------------------------------------------------------------------------- */
$routePath = $root . '/routes/logistics.php';
backupFile($routePath);
$routes = file_get_contents($routePath);

$routeAnchor = <<<'PHP'
        Route::get('/pickup-requests', [LogisticsPickupRequestController::class, 'index'])->name('pickup-requests');
PHP;

$routeReplacement = <<<'PHP'
        Route::get('/pickup-requests', [LogisticsPickupRequestController::class, 'index'])->name('pickup-requests');
        Route::post('/pickup-requests/{order}/verify', [LogisticsPickupRequestController::class, 'verify'])->name('pickup-requests.verify');
PHP;

$routes = replaceOnce($routes, $routeAnchor, $routeReplacement, 'pickup verification route');
file_put_contents($routePath, $routes);

/* --------------------------------------------------------------------------
 | 2) CONTROLLER: pickup requests can now be verified by Logistics
 * -------------------------------------------------------------------------- */
$pickupControllerPath = $root . '/app/Http/Controllers/LogisticsPickupRequestController.php';
backupFile($pickupControllerPath);

$pickupController = <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Models\LogisticsParcel;
use App\Models\MarketplaceOrder;
use App\Services\MarketplaceOrderWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsPickupRequestController extends Controller
{
    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow
    ) {
    }

    public function index(): View
    {
        $orders = MarketplaceOrder::query()
            ->with(['seller', 'logisticsParcel'])
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->oldest('ready_at')
            ->oldest('id')
            ->get();

        return view('logistics.pickup-requests', compact('orders'));
    }

    public function verify(MarketplaceOrder $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $locked->status !== 'ready_for_pickup'
                || filled($locked->courier_email)
            ) {
                throw ValidationException::withMessages([
                    'order' => 'This pickup request is no longer available for Logistics verification.',
                ]);
            }

            $parcel = LogisticsParcel::query()
                ->where('marketplace_order_id', $locked->id)
                ->lockForUpdate()
                ->first();

            if ($parcel && in_array($parcel->status, ['awaiting_intake', 'received', 'sorted'], true)) {
                throw ValidationException::withMessages([
                    'order' => 'This parcel has already moved beyond pickup verification.',
                ]);
            }

            LogisticsParcel::query()->updateOrCreate(
                ['marketplace_order_id' => $locked->id],
                [
                    'status' => 'pickup_verified',
                    'sorting_zone' => null,
                    'received_at' => null,
                    'sorted_at' => null,
                ]
            );

            $this->workflow->record(
                $locked,
                'both',
                'pickup_verified',
                'Pickup Request Verified',
                'SARI Logistics verified the Seller pickup request for order '
                    . $locked->order_number
                    . '. It is now ready for Rider assignment.'
            );
        }, 3);

        return back()->with('success', 'Pickup request verified. You can now assign an approved Rider.');
    }
}
PHP;

file_put_contents($pickupControllerPath, $pickupController);

/* --------------------------------------------------------------------------
 | 3) DELIVERY ASSIGNMENT: only verified pickup requests can be assigned
 * -------------------------------------------------------------------------- */
$assignmentPath = $root . '/app/Http/Controllers/LogisticsDeliveryAssignmentController.php';
backupFile($assignmentPath);
$assignment = file_get_contents($assignmentPath);

$indexAnchor = <<<'PHP'
        $orders = MarketplaceOrder::query()
            ->with('seller')
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->oldest('ready_at')
            ->oldest('id')
            ->get();
PHP;

$indexReplacement = <<<'PHP'
        $orders = MarketplaceOrder::query()
            ->with(['seller', 'logisticsParcel'])
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->whereHas('logisticsParcel', function ($query): void {
                $query->where('status', 'pickup_verified');
            })
            ->oldest('ready_at')
            ->oldest('id')
            ->get();
PHP;

$assignment = replaceOnce(
    $assignment,
    $indexAnchor,
    $indexReplacement,
    'verified pickup filter for delivery assignment'
);

$assignGuardAnchor = <<<'PHP'
            if (
                $lockedOrder->status !== 'ready_for_pickup'
                || $lockedOrder->courier_email
            ) {
                throw ValidationException::withMessages([
                    'order' => 'This order is no longer available for Logistics assignment.',
                ]);
            }

            $rider = CourierAccount::query()
PHP;

$assignGuardReplacement = <<<'PHP'
            if (
                $lockedOrder->status !== 'ready_for_pickup'
                || $lockedOrder->courier_email
            ) {
                throw ValidationException::withMessages([
                    'order' => 'This order is no longer available for Logistics assignment.',
                ]);
            }

            $verifiedParcel = LogisticsParcel::query()
                ->where('marketplace_order_id', $lockedOrder->id)
                ->where('status', 'pickup_verified')
                ->lockForUpdate()
                ->first();

            if (!$verifiedParcel) {
                throw ValidationException::withMessages([
                    'order' => 'Logistics must verify the Seller pickup request before assigning a Rider.',
                ]);
            }

            $rider = CourierAccount::query()
PHP;

$assignment = replaceOnce(
    $assignment,
    $assignGuardAnchor,
    $assignGuardReplacement,
    'pickup verification guard before Rider assignment'
);

file_put_contents($assignmentPath, $assignment);

/* --------------------------------------------------------------------------
 | 4) PICKUP REQUEST UI: Verify first, then Assign Rider
 * -------------------------------------------------------------------------- */
$viewPath = $root . '/resources/views/logistics/pickup-requests.blade.php';
backupFile($viewPath);
$view = file_get_contents($viewPath);

$successAnchor = <<<'BLADE'
<section class="space-y-4">
BLADE;

$successReplacement = <<<'BLADE'
<section class="space-y-4">
    @if(session('success'))
        <div class="rounded-xl border border-[#d8eee1] bg-[#f3faf6] px-4 py-3 text-[9px] font-semibold text-[#477b59]">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-[#f0dcdc] bg-[#fff6f6] px-4 py-3 text-[9px] font-semibold text-[#a6555b]">
            {{ $errors->first() }}
        </div>
    @endif
BLADE;

$view = replaceOnce($view, $successAnchor, $successReplacement, 'pickup request feedback banners');

$badgeAnchor = <<<'BLADE'
                    <span class="rounded-full bg-[#fff6e5] px-2.5 py-1 text-[7px] font-bold text-[#a96e05]">READY</span>
BLADE;

$badgeReplacement = <<<'BLADE'
                    @php($pickupVerified = ($order->logisticsParcel?->status === 'pickup_verified'))
                    <span class="rounded-full px-2.5 py-1 text-[7px] font-bold {{ $pickupVerified ? 'bg-[#ecf8f1] text-[#287a50]' : 'bg-[#fff6e5] text-[#a96e05]' }}">
                        {{ $pickupVerified ? 'VERIFIED' : 'AWAITING VERIFICATION' }}
                    </span>
BLADE;

$view = replaceOnce($view, $badgeAnchor, $badgeReplacement, 'pickup request verification badge');

$actionAnchor = <<<'BLADE'
                    <a href="{{ route('logistics.delivery-assignment') }}" class="rounded-xl bg-[#d9930a] px-4 py-2.5 text-[8px] font-bold text-white">Assign Rider →</a>
BLADE;

$actionReplacement = <<<'BLADE'
                    @if($pickupVerified)
                        <a href="{{ route('logistics.delivery-assignment') }}" class="rounded-xl bg-[#d9930a] px-4 py-2.5 text-[8px] font-bold text-white">
                            Assign Rider →
                        </a>
                    @else
                        <form method="POST" action="{{ route('logistics.pickup-requests.verify', $order) }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-[#222222] px-4 py-2.5 text-[8px] font-bold text-white transition hover:-translate-y-px hover:shadow-lg">
                                Verify Pickup Request
                            </button>
                        </form>
                    @endif
BLADE;

$view = replaceOnce($view, $actionAnchor, $actionReplacement, 'verify/assign pickup action');
file_put_contents($viewPath, $view);

echo "\nSARI ERP Order Flow Phase 1 installed successfully.\n";
echo "Flow: Buyer checkout -> Seller prepares -> Seller ready -> Logistics verifies -> Logistics assigns Rider.\n";
echo "\nNext commands:\n";
echo "  php artisan optimize:clear\n";
echo "  php artisan route:list --name=logistics.pickup\n";
echo "\nBackups were created with the .phase1.bak suffix.\n";
