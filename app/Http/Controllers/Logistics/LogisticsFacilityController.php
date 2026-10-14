<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsFacility;
use App\Services\Delivery\DeliveryRouteService;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LogisticsFacilityController extends Controller
{
    public function __construct(
        private readonly DeliveryRouteService $deliveryRoute,
    ) {
    }

    public function index(Request $request): View
    {
        return $this->page($request);
    }

    public function edit(
        Request $request,
        LogisticsFacility $facility
    ): View {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $this->assertOwned($facility, $logistics->id);

        return $this->page($request, $facility);
    }

    public function store(Request $request): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $validated = $request->validate(
            $this->rules($logistics->id)
        );

        LogisticsFacility::query()->create([
            'logistics_account_id' => $logistics->id,
            'code' => $this->uniqueCode(
                $logistics->id,
                $validated['name']
            ),
            'name' => $validated['name'],
            'address' => $validated['address'],
            'province_name' => $validated['province_name'],
            'municipality_name' => $validated['municipality_name'],
            'barangay_name' => $validated['barangay_name'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_active' => $request->boolean('is_active'),
        ]);

        /*
         * A newly completed network may make older unowned orders routable.
         */
        $this->deliveryRoute->assignUnownedOrders(30);

        return redirect()
            ->route('logistics.facilities.index')
            ->with(
                'success',
                'Sorting facility added. New eligible orders can now use it.'
            );
    }

    public function update(
        Request $request,
        LogisticsFacility $facility
    ): RedirectResponse {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $this->assertOwned($facility, $logistics->id);

        $validated = $request->validate(
            $this->rules(
                $logistics->id,
                $facility
            )
        );

        $facility->fill([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'province_name' => $validated['province_name'],
            'municipality_name' => $validated['municipality_name'],
            'barangay_name' => $validated['barangay_name'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_active' => $request->boolean('is_active'),
        ])->save();

        return redirect()
            ->route('logistics.facilities.index')
            ->with(
                'success',
                'Sorting facility updated. Existing order route snapshots were preserved.'
            );
    }

    public function toggle(
        Request $request,
        LogisticsFacility $facility
    ): RedirectResponse {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $this->assertOwned($facility, $logistics->id);

        $facility->forceFill([
            'is_active' => !$facility->is_active,
        ])->save();

        return back()->with(
            'success',
            $facility->is_active
                ? 'Sorting facility activated.'
                : 'Sorting facility deactivated for new route selection.'
        );
    }

    public function destroy(
        Request $request,
        LogisticsFacility $facility
    ): RedirectResponse {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $this->assertOwned($facility, $logistics->id);

        if ($facility->routeStops()->exists()) {
            throw ValidationException::withMessages([
                'facility' =>
                    'This facility already belongs to an order route. Deactivate it instead so historical tracking remains intact.',
            ]);
        }

        $facility->delete();

        return redirect()
            ->route('logistics.facilities.index')
            ->with('success', 'Unused sorting facility deleted.');
    }

    private function page(
        Request $request,
        ?LogisticsFacility $editing = null
    ): View {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $facilities = LogisticsFacility::query()
            ->forLogistics($logistics->id)
            ->withCount('routeStops')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $activeCount = $facilities
            ->where('is_active', true)
            ->count();

        $stats = [
            'total' => $facilities->count(),
            'active' => $activeCount,
            'used' => $facilities
                ->filter(
                    fn (LogisticsFacility $facility): bool =>
                        $facility->route_stops_count > 0
                )
                ->count(),
            'routing_ready' => $activeCount >= 2,
        ];

        return view(
            'logistics.facilities',
            compact(
                'logistics',
                'facilities',
                'editing',
                'stats'
            )
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(
        int $logisticsAccountId,
        ?LogisticsFacility $facility = null
    ): array {
        return [
            'name' => [
                'required',
                'string',
                'max:140',
                Rule::unique('logistics_facilities', 'name')
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'logistics_account_id',
                                $logisticsAccountId
                            )
                    )
                    ->ignore($facility?->id),
            ],
            'address' => ['required', 'string', 'max:1200'],
            'province_name' => ['required', 'string', 'max:120'],
            'municipality_name' => ['required', 'string', 'max:120'],
            'barangay_name' => ['nullable', 'string', 'max:120'],
            'latitude' => ['required', 'numeric', 'between:4,22.5'],
            'longitude' => ['required', 'numeric', 'between:116,127.5'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function assertOwned(
        LogisticsFacility $facility,
        int $logisticsAccountId
    ): void {
        abort_unless(
            (int) $facility->logistics_account_id
                === $logisticsAccountId,
            404
        );
    }

    private function uniqueCode(
        int $logisticsAccountId,
        string $name
    ): string {
        $base = Str::upper(
            Str::slug(
                'L' . $logisticsAccountId . '-' . $name,
                '-'
            )
        );

        $base = substr($base ?: 'SARI-FACILITY', 0, 52);
        $candidate = $base;
        $suffix = 2;

        while (
            LogisticsFacility::query()
                ->where('code', $candidate)
                ->exists()
        ) {
            $candidate = substr($base, 0, 52)
                . '-'
                . $suffix;

            $suffix++;
        }

        return $candidate;
    }
}
