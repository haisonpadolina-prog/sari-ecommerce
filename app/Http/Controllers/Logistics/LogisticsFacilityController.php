<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\LogisticsHubAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Services\Delivery\DeliveryRouteService;
use App\Services\Logistics\LogisticsActivityLogger;
use App\Support\CurrentLogisticsAccount;
use App\Support\LogisticsServiceArea;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    public function edit(
        Request $request,
        LogisticsFacility $facility
    ): View {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);

        return $this->page($request, $facility);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Municipal Hub + Hub Operator
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->normalizeOperatorInput($request);

        $validated = $request->validate(
            $this->rules(
                $logistics->id,
                null,
                null,
                true
            ),
            $this->validationMessages()
        );

        /*
         * The hub and its operator must be created together.
         * A failed operator creation rolls back the hub creation.
         */

        $facility = DB::transaction(
            function () use (
                $request,
                $validated,
                $logistics
            ): LogisticsFacility {
                $facility = LogisticsFacility::query()->create([
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

                LogisticsHubAccount::query()->create([
                    'logistics_account_id' => $logistics->id,

                    'logistics_facility_id' => $facility->id,

                    'name' => $validated['operator_name'],

                    'email' => $validated['operator_email'],

                    'password' => Hash::make(
                        $validated['operator_password']
                    ),

                    'role' => 'hub_operator',
                    'account_status' => 'active',
                    'last_login_at' => null,
                ]);

                return $facility;
            },
            3
        );

        $this->deliveryRoute->assignUnownedOrders(30);

        /*
         * The middleware records successful creation using the real,
         * newly saved facility ID. Never take this ID from form input.
         */
        $request->attributes->set('activity_changed', true);
        $request->attributes->set('activity_created_facility_id', (int) $facility->id);
        $request->attributes->set('activity_facility_id', (int) $facility->id);

        return redirect()
            ->route('logistics.facilities.index')
            ->with(
                'success',
                $facility->name
                .' and its Hub Operator account were created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Municipal Hub + Hub Operator
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        LogisticsFacility $facility
    ): RedirectResponse {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);

        $operator = $this->operatorForFacility(
            $facility,
            $logistics->id
        );

        $this->normalizeOperatorInput($request);

        $validated = $request->validate(
            $this->rules(
                $logistics->id,
                $facility,
                $operator,
                false
            ),
            $this->validationMessages()
        );

        if ($facility->routeStops()->exists()
            && (LogisticsServiceArea::normalize($validated['province_name']) !== LogisticsServiceArea::normalize($facility->province_name)
                || LogisticsServiceArea::municipality($validated['municipality_name']) !== LogisticsServiceArea::municipality($facility->municipality_name))) {
            throw ValidationException::withMessages([
                'municipality_name' => 'A hub used by order routes cannot move to another municipality or province. Create a new hub to preserve existing delivery assignments.',
            ]);
        }

        /*
         * A successful request is not necessarily an actual change.
         * Report true only when the facility/operator was modified.
         */
        $changed = DB::transaction(
            function () use (
                $request,
                $validated,
                $facility,
                $operator,
                $logistics
            ): bool {
                /*
                 * Keep existing historical order route snapshots intact.
                 */
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

                $facilityChanged = $facility->isDirty();

                if ($facilityChanged) {
                    $facility->save();
                }

                /*
                 * Existing Hub Operator: blank credential fields mean
                 * retain existing values, exactly as before.
                 */
                if ($operator) {
                    $updates = [];

                    if (filled($validated['operator_name'] ?? null)) {
                        $updates['name'] = $validated['operator_name'];
                    }

                    if (filled($validated['operator_email'] ?? null)) {
                        $updates['email'] = $validated['operator_email'];
                    }

                    if (filled($validated['operator_password'] ?? null)) {
                        $updates['password'] = Hash::make(
                            $validated['operator_password']
                        );
                    }

                    $operatorChanged = false;

                    if ($updates !== []) {
                        $operator->forceFill($updates);
                        $operatorChanged = $operator->isDirty();

                        if ($operatorChanged) {
                            $operator->save();
                        }
                    }

                    return $facilityChanged || $operatorChanged;
                }

                /*
                 * Legacy facility without an operator: create its
                 * required operator account inside the transaction.
                 */
                LogisticsHubAccount::query()->create([
                    'logistics_account_id' => $logistics->id,

                    'logistics_facility_id' => $facility->id,

                    'name' => $validated['operator_name'],

                    'email' => $validated['operator_email'],

                    'password' => Hash::make(
                        $validated['operator_password']
                    ),

                    'role' => 'hub_operator',
                    'account_status' => 'active',
                    'last_login_at' => null,
                ]);

                return true;
            },
            3
        );

        /*
         * The middleware skips no-op updates (activity_changed=false).
         * Facility ownership is revalidated by the logger.
         */
        $request->attributes->set('activity_changed', $changed);
        $request->attributes->set('activity_facility_id', (int) $facility->id);

        return redirect()
            ->route('logistics.facilities.index')
            ->with(
                'success',
                'Municipal hub updated successfully. Existing route snapshots were preserved.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate Municipal Hub
    |--------------------------------------------------------------------------
    */

    public function toggle(
        Request $request,
        LogisticsFacility $facility
    ): RedirectResponse {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);

        $facility->forceFill([
            'is_active' => ! $facility->is_active,
        ])->save();

        $request->attributes->set('activity_changed', true);
        $request->attributes->set('activity_facility_id', (int) $facility->id);

        return back()->with(
            'success',
            $facility->is_active
                ? 'Municipal hub activated.'
                : 'Municipal hub deactivated for new route selection. Its Hub Operator cannot operate this hub while inactive.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Unused Municipal Hub
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        LogisticsFacility $facility
    ): RedirectResponse {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $this->assertOwned($facility, $logistics->id);

        /*
         * Never delete a facility used in historical routes.
         */

        if ($facility->routeStops()->exists()) {
            throw ValidationException::withMessages([
                'facility' => 'This hub already belongs to an order route. Deactivate it instead so historical tracking remains intact.',
            ]);
        }

        /*
         * Deletion needs special handling: the generic middleware runs
         * after the controller and cannot look up a deleted facility.
         * Instead, write the audit snapshot INSIDE the delete transaction
         * while the facility still exists. A failed delete rolls it back.
         * The delete route is intentionally absent from middleware EVENTS.
         */
        DB::transaction(
            function () use ($request, $facility, $logistics): void {
                $lockedFacility = LogisticsFacility::query()
                    ->whereKey($facility->id)
                    ->where('logistics_account_id', $logistics->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Recheck after obtaining the facility row lock.
                if ($lockedFacility->routeStops()->exists()) {
                    throw ValidationException::withMessages([
                        'facility' => 'This hub already belongs to an order route. Deactivate it instead so historical tracking remains intact.',
                    ]);
                }

                // Save the facility/actor names before deleting the source rows.
                $this->activityLogger->record(
                    $request,
                    'Sorting Facilities',
                    'deleted',
                    'Unused municipal hub and operator deleted',
                    'facility',
                    (int) $lockedFacility->id,
                    (int) $lockedFacility->id
                );

                LogisticsHubAccount::query()
                    ->where('logistics_account_id', $logistics->id)
                    ->where('logistics_facility_id', $lockedFacility->id)
                    ->delete();

                $lockedFacility->delete();
            },
            3
        );

        return redirect()
            ->route('logistics.facilities.index')
            ->with(
                'success',
                'Unused municipal hub and its Hub Operator account were deleted.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Municipal Hub Page Data
    |--------------------------------------------------------------------------
    */

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

        $hubAccounts = LogisticsHubAccount::query()
            ->where('logistics_account_id', $logistics->id)
            ->whereIn(
                'logistics_facility_id',
                $facilities->pluck('id')->all()
            )
            ->orderBy('name')
            ->get()
            ->keyBy('logistics_facility_id');

        $editingOperator = $editing
            ? $hubAccounts->get($editing->id)
            : null;

        $activeCount = $facilities
            ->where('is_active', true)
            ->count();

        $stats = [
            'total' => $facilities->count(),

            'active' => $activeCount,

            'used' => $facilities
                ->filter(
                    fn (LogisticsFacility $facility): bool => $facility->route_stops_count > 0
                )
                ->count(),

            'operator_accounts' => $hubAccounts->count(),

            'routing_ready' => $activeCount >= 2,
        ];

        return view(
            'logistics.facilities',
            compact(
                'logistics',
                'facilities',
                'editing',
                'editingOperator',
                'hubAccounts',
                'stats'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Hub Operator Input
    |--------------------------------------------------------------------------
    */

    private function normalizeOperatorInput(Request $request): void
    {
        $updates = [];

        foreach (['name', 'address', 'province_name', 'municipality_name', 'barangay_name'] as $field) {
            if ($request->has($field)) {
                $updates[$field] = trim(preg_replace('/\s+/u', ' ', (string) $request->input($field)) ?? '');
            }
        }

        if ($request->has('operator_name')) {
            $name = (string) $request->input('operator_name', '');

            /*
             * Remove leading/trailing whitespace.
             * Collapse repeated whitespace into one space.
             */

            $updates['operator_name'] = trim(
                preg_replace('/\s+/u', ' ', $name) ?? $name
            );
        }

        if ($request->has('operator_email')) {
            $updates['operator_email'] = strtolower(
                trim(
                    (string) $request->input('operator_email', '')
                )
            );
        }

        if (! empty($updates)) {
            $request->merge($updates);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    private function rules(
        int $logisticsAccountId,
        ?LogisticsFacility $facility = null,
        ?LogisticsHubAccount $operator = null,
        bool $creating = false
    ): array {
        /*
         * New hubs and legacy hubs without an operator
         * must provide complete operator credentials.
         */

        $operatorRequired = $creating || $operator === null;

        return [
            /*
            |--------------------------------------------------------------------------
            | Municipal Hub Validation
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:140',

                Rule::unique(
                    'logistics_facilities',
                    'name'
                )
                    ->where(
                        fn ($query) => $query->where(
                            'logistics_account_id',
                            $logisticsAccountId
                        )
                    )
                    ->ignore($facility?->id),
            ],

            'address' => [
                'required',
                'string',
                'max:1200',
            ],

            'province_name' => [
                'required',
                'string',
                'max:120',
            ],

            /*
             * Maintain one hub per municipality per provider.
             */

            'municipality_name' => [
                'required',
                'string',
                'max:120',

                Rule::unique(
                    'logistics_facilities',
                    'municipality_name'
                )
                    ->where(
                        fn ($query) => $query->where(
                            'logistics_account_id',
                            $logisticsAccountId
                        )->where('province_name', request()->input('province_name'))
                    )
                    ->ignore($facility?->id),
            ],

            'barangay_name' => [
                'nullable',
                'string',
                'max:120',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:4,22.5',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:116,127.5',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Hub Operator Name
            |--------------------------------------------------------------------------
            |
            | 2-50 characters
            | Letters, including Filipino ñ
            | Spaces, period, hyphen, apostrophe
            |
            */

            'operator_name' => [
                $operatorRequired ? 'required' : 'nullable',
                'string',
                'min:2',
                'max:50',

                function ($attribute, $value, $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $valid = preg_match(
                        '/\A[\p{L}\p{M} .\'’\-]+\z/u',
                        (string) $value
                    );

                    if ($valid !== 1) {
                        $fail(
                            'Operator name may only contain letters, spaces, hyphens, apostrophes, and periods.'
                        );
                    }

                    if (
                        preg_match('/[\p{L}]/u', (string) $value) !== 1
                    ) {
                        $fail(
                            'Operator name must contain letters.'
                        );
                    }
                },
            ],

            /*
            |--------------------------------------------------------------------------
            | Hub Operator Email
            |--------------------------------------------------------------------------
            |
            | Email is normalized to lowercase.
            | Uniqueness comparison is case-insensitive.
            |
            */

            'operator_email' => [
                $operatorRequired ? 'required' : 'nullable',
                'string',
                'email:rfc',
                'max:255',

                function ($attribute, $value, $fail) use ($operator): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $email = strtolower(trim((string) $value));

                    $query = LogisticsHubAccount::query()
                        ->whereRaw(
                            'LOWER(email) = ?',
                            [$email]
                        );

                    if ($operator) {
                        $query->whereKeyNot($operator->id);
                    }

                    if ($query->exists()) {
                        $fail(
                            'This email address is already registered to another Hub Operator.'
                        );
                    }
                },
            ],

            /*
            |--------------------------------------------------------------------------
            | Hub Operator Password
            |--------------------------------------------------------------------------
            |
            | At least 8 characters
            | At least one uppercase letter
            | At least one lowercase letter
            | At least one number
            | At least one special character
            | Must match password confirmation
            |
            */

            'operator_password' => [
                $operatorRequired ? 'required' : 'nullable',
                'string',
                'min:8',
                'max:255',
                'confirmed',

                function ($attribute, $value, $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $password = (string) $value;

                    if (preg_match('/[A-Z]/', $password) !== 1) {
                        $fail(
                            'Password must contain at least one uppercase letter.'
                        );
                    }

                    if (preg_match('/[a-z]/', $password) !== 1) {
                        $fail(
                            'Password must contain at least one lowercase letter.'
                        );
                    }

                    if (preg_match('/[0-9]/', $password) !== 1) {
                        $fail(
                            'Password must contain at least one number.'
                        );
                    }

                    if (
                        preg_match(
                            '/[^A-Za-z0-9\s]/',
                            $password
                        ) !== 1
                    ) {
                        $fail(
                            'Password must contain at least one special character.'
                        );
                    }
                },
            ],

            'operator_password_confirmation' => [
                $operatorRequired ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Messages
    |--------------------------------------------------------------------------
    */

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

            'operator_name.required' => 'Please enter the Hub Operator name.',

            'operator_name.min' => 'Operator name must contain at least 2 characters.',

            'operator_name.max' => 'Operator name must not exceed 50 characters.',

            'operator_email.required' => 'Please enter the Hub Operator email address.',

            'operator_email.email' => 'Please enter a valid email address.',

            'operator_password.required' => 'Please enter the Hub Operator password.',

            'operator_password.min' => 'Password must contain at least 8 characters.',

            'operator_password.max' => 'Password must not exceed 255 characters.',

            'operator_password.confirmed' => 'The password and confirmation password do not match.',

            'operator_password_confirmation.required' => 'Please confirm the Hub Operator password.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Operator For Facility
    |--------------------------------------------------------------------------
    */

    private function operatorForFacility(
        LogisticsFacility $facility,
        int $logisticsAccountId
    ): ?LogisticsHubAccount {
        return LogisticsHubAccount::query()
            ->where(
                'logistics_account_id',
                $logisticsAccountId
            )
            ->where(
                'logistics_facility_id',
                $facility->id
            )
            ->orderBy('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Facility Ownership
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Unique Facility Code
    |--------------------------------------------------------------------------
    */

    private function uniqueCode(
        int $logisticsAccountId,
        string $name
    ): string {
        $base = Str::upper(
            Str::slug(
                'L'.$logisticsAccountId.'-'.$name,
                '-'
            )
        );

        $base = substr(
            $base ?: 'SARI-FACILITY',
            0,
            52
        );

        $candidate = $base;
        $suffix = 2;

        while (
            LogisticsFacility::query()
                ->where('code', $candidate)
                ->exists()
        ) {
            $candidate = substr($base, 0, 52)
                .'-'
                .$suffix;

            $suffix++;
        }

        return $candidate;
    }
}
