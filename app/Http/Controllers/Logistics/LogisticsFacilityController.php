<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\LogisticsHubAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Finance\BranchRemittanceBatch;
use App\Services\Delivery\DeliveryRouteService;
use App\Services\Logistics\LogisticsActivityLogger;
use App\Support\CurrentLogisticsAccount;
use App\Support\LogisticsServiceArea;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LogisticsFacilityController extends Controller
{
    public function __construct(
        private readonly DeliveryRouteService $deliveryRoute,
        private readonly LogisticsActivityLogger $activityLogger
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Municipal Hub Pages
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        return $this->page($request);
    }

    public function edit(Request $request, LogisticsFacility $facility): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);

        return $this->page($request, $facility);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Municipal Hub
    |--------------------------------------------------------------------------
    |
    | A branch is a physical sorting facility under the authenticated Logistics
    | provider. It does not create a second login, email address, or password.
    | The provider selects the operating branch after signing in.
    |
    */

    public function store(Request $request): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->normalizeFacilityInput($request);

        $validated = $request->validate(
            $this->rules($logistics->id),
            $this->validationMessages()
        );

        $facility = DB::transaction(function () use ($request, $validated, $logistics): LogisticsFacility {
            $hasMainBranch = LogisticsFacility::query()
                ->forLogistics($logistics->id)
                ->where('is_main_branch', true)
                ->lockForUpdate()
                ->exists();

            $isActive = $request->boolean('is_active');

            return LogisticsFacility::query()->create([
                'logistics_account_id' => $logistics->id,
                'code' => $this->uniqueCode($logistics->id, $validated['name']),
                'name' => $validated['name'],
                'address' => $validated['address'],
                'province_name' => $validated['province_name'],
                'municipality_name' => $validated['municipality_name'],
                'barangay_name' => $validated['barangay_name'] ?? null,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'is_active' => $isActive,
                // The first active branch becomes the Main Branch automatically.
                'is_main_branch' => $isActive && ! $hasMainBranch,
            ]);
        }, 3);

        $this->deliveryRoute->assignUnownedOrders(30);

        $request->attributes->set('activity_changed', true);
        $request->attributes->set('activity_created_facility_id', (int) $facility->id);
        $request->attributes->set('activity_facility_id', (int) $facility->id);

        return redirect()
            ->route('logistics.facilities.index')
            ->with('success', $facility->name.' was added to your Logistics network successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Update Municipal Hub
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, LogisticsFacility $facility): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);
        $this->normalizeFacilityInput($request);

        $validated = $request->validate(
            $this->rules($logistics->id, $facility),
            $this->validationMessages()
        );

        if ($facility->routeStops()->exists()
            && (LogisticsServiceArea::normalize($validated['province_name']) !== LogisticsServiceArea::normalize($facility->province_name)
                || LogisticsServiceArea::municipality($validated['municipality_name']) !== LogisticsServiceArea::municipality($facility->municipality_name))) {
            throw ValidationException::withMessages([
                'municipality_name' => 'A hub used by order routes cannot move to another municipality or province. Create a new hub to preserve existing delivery assignments.',
            ]);
        }

        $changed = DB::transaction(function () use ($request, $validated, $facility): bool {
            $facility->fill([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'province_name' => $validated['province_name'],
                'municipality_name' => $validated['municipality_name'],
                'barangay_name' => $validated['barangay_name'] ?? null,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'is_active' => $request->boolean('is_active'),
            ]);

            if (! $facility->isDirty()) {
                return false;
            }

            $facility->save();

            return true;
        }, 3);

        $request->attributes->set('activity_changed', $changed);
        $request->attributes->set('activity_facility_id', (int) $facility->id);

        return redirect()
            ->route('logistics.facilities.index')
            ->with('success', 'Municipal hub updated successfully. Existing route snapshots were preserved.');
    }

    /*
    |--------------------------------------------------------------------------
    | Main Branch
    |--------------------------------------------------------------------------
    */

    public function setMain(Request $request, LogisticsFacility $facility): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);

        if (! $facility->is_active) {
            throw ValidationException::withMessages([
                'facility' => 'Only an active sorting facility can become the Main Branch.',
            ]);
        }

        $currentMain = LogisticsFacility::query()
            ->forLogistics($logistics->id)
            ->where('is_main_branch', true)
            ->first();

        if ($currentMain && (int) $currentMain->id !== (int) $facility->id) {
            $openCentralCash = BranchRemittanceBatch::query()
                ->where('logistics_account_id', $logistics->id)
                ->where('destination_facility_id', $currentMain->id)
                ->whereIn('status', ['submitted', 'discrepancy'])
                ->exists();

            if ($openCentralCash) {
                throw ValidationException::withMessages([
                    'facility' => 'Reconcile all open Branch Treasury cash batches at the current Main Branch before changing the Main Branch.',
                ]);
            }
        }

        DB::transaction(function () use ($facility, $logistics): void {
            LogisticsFacility::query()
                ->forLogistics($logistics->id)
                ->lockForUpdate()
                ->update(['is_main_branch' => false]);

            LogisticsFacility::query()
                ->whereKey($facility->id)
                ->where('logistics_account_id', $logistics->id)
                ->update([
                    'is_main_branch' => true,
                    'updated_at' => now(),
                ]);
        }, 3);

        $request->attributes->set('activity_changed', true);
        $request->attributes->set('activity_facility_id', (int) $facility->id);

        return back()->with('success', $facility->name.' is now the Main Branch and central COD treasury.');
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate Municipal Hub
    |--------------------------------------------------------------------------
    */

    public function toggle(Request $request, LogisticsFacility $facility): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);

        if ($facility->is_main_branch && $facility->is_active) {
            throw ValidationException::withMessages([
                'facility' => 'Set another active facility as Main Branch before deactivating the current Main Branch.',
            ]);
        }

        if ($facility->is_active) {
            $hasOpenTreasuryBatch = BranchRemittanceBatch::query()
                ->where('logistics_account_id', $logistics->id)
                ->whereIn('status', ['submitted', 'discrepancy'])
                ->where(function ($query) use ($facility): void {
                    $query->where('origin_facility_id', $facility->id)
                        ->orWhere('destination_facility_id', $facility->id);
                })
                ->exists();

            if ($hasOpenTreasuryBatch) {
                throw ValidationException::withMessages([
                    'facility' => "Reconcile or resolve this branch's open physical treasury batches before deactivating it.",
                ]);
            }
        }

        $facility->forceFill([
            'is_active' => ! $facility->is_active,
        ])->save();

        $request->attributes->set('activity_changed', true);
        $request->attributes->set('activity_facility_id', (int) $facility->id);

        return back()->with(
            'success',
            $facility->is_active
                ? 'Municipal hub activated for new routes.'
                : 'Municipal hub deactivated for new route selection. Historical route records are preserved.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Unused Municipal Hub
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request, LogisticsFacility $facility): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);

        if ($facility->is_main_branch) {
            throw ValidationException::withMessages([
                'facility' => 'Set another active facility as Main Branch before deleting this facility.',
            ]);
        }

        if ($facility->routeStops()->exists()) {
            throw ValidationException::withMessages([
                'facility' => 'This hub already belongs to an order route. Deactivate it instead so historical tracking remains intact.',
            ]);
        }

        DB::transaction(function () use ($request, $facility, $logistics): void {
            $lockedFacility = LogisticsFacility::query()
                ->whereKey($facility->id)
                ->where('logistics_account_id', $logistics->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedFacility->routeStops()->exists()) {
                throw ValidationException::withMessages([
                    'facility' => 'This hub already belongs to an order route. Deactivate it instead so historical tracking remains intact.',
                ]);
            }

            $this->activityLogger->record(
                $request,
                'Sorting Facilities',
                'deleted',
                'Unused municipal hub deleted',
                'facility',
                (int) $lockedFacility->id,
                (int) $lockedFacility->id
            );

            // Compatibility cleanup for Hub Operator accounts created by older SARI builds.
            LogisticsHubAccount::query()
                ->where('logistics_account_id', $logistics->id)
                ->where('logistics_facility_id', $lockedFacility->id)
                ->delete();

            $lockedFacility->delete();
        }, 3);

        return redirect()
            ->route('logistics.facilities.index')
            ->with('success', 'Unused municipal hub deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Municipal Hub Page Data
    |--------------------------------------------------------------------------
    */

    private function page(Request $request, ?LogisticsFacility $editing = null): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $facilities = LogisticsFacility::query()
            ->forLogistics($logistics->id)
            ->withCount('routeStops')
            ->orderByDesc('is_main_branch')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $activeCount = $facilities->where('is_active', true)->count();

        $stats = [
            'total' => $facilities->count(),
            'active' => $activeCount,
            'used' => $facilities
                ->filter(fn (LogisticsFacility $facility): bool => $facility->route_stops_count > 0)
                ->count(),
            'routing_ready' => $activeCount >= 2,
            'main_branch_count' => $facilities->where('is_main_branch', true)->count(),
        ];

        return view('logistics.facilities', compact(
            'logistics',
            'facilities',
            'editing',
            'stats'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Facility Input
    |--------------------------------------------------------------------------
    */

    private function normalizeFacilityInput(Request $request): void
    {
        $updates = [];

        foreach (['name', 'address', 'province_name', 'municipality_name', 'barangay_name'] as $field) {
            if ($request->has($field)) {
                $updates[$field] = trim(preg_replace('/\s+/u', ' ', (string) $request->input($field)) ?? '');
            }
        }

        if ($updates !== []) {
            $request->merge($updates);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    /** @return array<string, mixed> */
    private function rules(int $logisticsAccountId, ?LogisticsFacility $facility = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:140',
                Rule::unique('logistics_facilities', 'name')
                    ->where(fn ($query) => $query->where('logistics_account_id', $logisticsAccountId))
                    ->ignore($facility?->id),
            ],
            'address' => ['required', 'string', 'max:1200'],
            'province_name' => ['required', 'string', 'max:120'],
            'municipality_name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('logistics_facilities', 'municipality_name')
                    ->where(fn ($query) => $query
                        ->where('logistics_account_id', $logisticsAccountId)
                        ->where('province_name', request()->input('province_name')))
                    ->ignore($facility?->id),
            ],
            'barangay_name' => ['nullable', 'string', 'max:120'],
            'latitude' => ['required', 'numeric', 'between:4,22.5'],
            'longitude' => ['required', 'numeric', 'between:116,127.5'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'name.required' => 'Please enter a Municipal Hub name.',
            'name.unique' => 'This hub name is already registered under your Logistics account.',
            'address.required' => 'Please enter the complete physical hub address.',
            'province_name.required' => 'Please select a province from the address directory.',
            'municipality_name.required' => 'Please select a city or municipality from the address directory.',
            'municipality_name.unique' => 'A Municipal Hub already exists in this municipality under your Logistics account.',
            'latitude.required' => 'Please select the physical hub location on the map.',
            'longitude.required' => 'Please select the physical hub location on the map.',
        ];
    }

    private function assertOwned(LogisticsFacility $facility, int $logisticsAccountId): void
    {
        abort_unless((int) $facility->logistics_account_id === $logisticsAccountId, 404);
    }

    private function uniqueCode(int $logisticsAccountId, string $name): string
    {
        $base = Str::upper(Str::slug('L'.$logisticsAccountId.'-'.$name, '-'));
        $base = substr($base ?: 'SARI-FACILITY', 0, 52);

        $candidate = $base;
        $suffix = 2;

        while (LogisticsFacility::query()->where('code', $candidate)->exists()) {
            $candidate = substr($base, 0, 52).'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
