<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Platform\AdminUserActivity;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Services\Seller\SellerAccountStatusService;
use App\Services\Accounts\AccountEmailBanService;
use App\Models\Registration\RegistrationApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AdminUsersController extends Controller
{
    public function __construct(
        private readonly SellerAccountStatusService $sellerStatusService,
        private readonly AccountEmailBanService $emailBanService
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $activity = Schema::hasTable('admin_user_activities')
            ? AdminUserActivity::query()
                ->latest('created_at')
                ->limit(300)
                ->get()
                ->groupBy(fn (AdminUserActivity $row) => strtolower($row->user_role) . ':' . $row->user_id)
            : collect();

        $lastSignIns = Schema::hasTable('admin_user_activities')
            ? AdminUserActivity::query()
                ->where('action', 'login')
                ->select('user_role', 'user_id')
                ->selectRaw('MAX(created_at) AS last_sign_in_at')
                ->groupBy('user_role', 'user_id')
                ->get()
                ->keyBy(fn (AdminUserActivity $row) => strtolower($row->user_role) . ':' . $row->user_id)
            : collect();

        $accounts = collect()
            ->concat($this->mapAccounts(BuyerAccount::query()->with('registrationApplication')->latest()->get(), 'Buyer', $activity, $lastSignIns))
            ->concat($this->mapAccounts(SellerAccount::query()->with('registrationApplication')->latest()->get(), 'Seller', $activity, $lastSignIns))
            ->concat($this->mapAccounts(CourierAccount::query()->with(['registrationApplication', 'logistics'])->latest()->get(), 'Rider', $activity, $lastSignIns))
            ->concat($this->mapAccounts(LogisticsAccount::query()->with('registrationApplication')->latest()->get(), 'Logistics', $activity, $lastSignIns))
            ->sortByDesc('created_at')
            ->values();

        $stats = [
            'total' => $accounts->count(),
            'buyers' => $accounts->where('role', 'Buyer')->count(),
            'sellers' => $accounts->where('role', 'Seller')->count(),
            'riders' => $accounts->where('role', 'Rider')->count(),
            'logistics' => $accounts->where('role', 'Logistics')->count(),
            'active' => $accounts->where('status', 'active')->count(),
            'suspended' => $accounts->where('status', '!=', 'active')->count(),
        ];

        return view('admin.users', compact('accounts', 'stats'));
    }

    public function photo(Request $request, string $role, int $id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->guard($request);
        $account = $this->resolveAccount($role, $id);
        $path = $account->profile_image_path;
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        abort_unless(filled($path) && $disk->exists($path), 404, 'Profile photo not found.');

        return response()->file($disk->path($path), [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function document(
        Request $request,
        string $role,
        int $id,
        string $document
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $this->guard($request);

        $normalizedRole = $this->normalizeRole($role);
        $account = $this->resolveAccount($normalizedRole, $id);
        $path = $this->accountDocumentPath($account, $normalizedRole, $document);
        $disk = Storage::disk('local');

        abort_unless(
            filled($path) && $disk->exists($path),
            404,
            'Account document not found.'
        );

        return response()->file($disk->path($path), [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function update(Request $request, string $role, int $id): RedirectResponse
    {
        $this->guard($request);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'contact_no' => ['nullable', 'string', 'max:30'],
            'store_name' => [Rule::requiredIf($normalizedRole === 'seller'), 'nullable', 'string', 'max:150'],
        ]);

        $before = $this->editableSnapshot($account, $normalizedRole);
        $updates = [];

        foreach (['first_name', 'last_name', 'contact_no'] as $field) {
            if (array_key_exists($field, $validated) && Schema::hasColumn($account->getTable(), $field)) {
                $updates[$field] = $validated[$field];
            }
        }

        if ($normalizedRole === 'seller'
            && array_key_exists('store_name', $validated)
            && Schema::hasColumn($account->getTable(), 'store_name')) {
            $updates['store_name'] = $validated['store_name'];
        }

        if ($updates !== []) {
            $account->forceFill($updates)->save();
        }

        $after = $this->editableSnapshot($account->fresh(), $normalizedRole);

        $this->recordActivity(
            $request,
            $normalizedRole,
            $account->id,
            'profile_updated',
            'Admin updated account profile information.',
            ['before' => $before, 'after' => $after]
        );

        return back()->with('success', 'Account information updated successfully.');
    }

    public function suspend(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);
        $currentStatus = strtolower((string) ($account->account_status ?? 'active'));

        if ($currentStatus === 'banned') {
            throw ValidationException::withMessages([
                'account' => 'Unban the account before suspending access.',
            ]);
        }

        if ($normalizedRole === 'seller') {
            /** @var SellerAccount $account */
            $account = $this->sellerStatusService->deactivate(
                $account,
                $validated['reason']
            );
        } elseif (Schema::hasColumn($account->getTable(), 'account_status')) {
            $account->forceFill(['account_status' => 'deactivated'])->save();
            $account->refresh();
        }

        $this->recordActivity(
            $request,
            $normalizedRole,
            $account->id,
            'suspended',
            $validated['reason'],
            ['status' => 'deactivated']
        );

        return $this->respond(
            $request,
            'User access suspended successfully.',
            $account,
            $normalizedRole
        );
    }

    public function restore(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);

        if ($normalizedRole === 'seller') {
            /** @var SellerAccount $account */
            $account = $this->sellerStatusService->restore($account);
        } elseif (Schema::hasColumn($account->getTable(), 'account_status')) {
            $account->forceFill(['account_status' => 'active'])->save();
            $account->refresh();
        }

        $this->recordActivity(
            $request,
            $normalizedRole,
            $account->id,
            'restored',
            'Admin restored account access.',
            ['status' => 'active']
        );

        return $this->respond(
            $request,
            'User access restored successfully.',
            $account,
            $normalizedRole
        );
    }

    public function block(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);
        $status = strtolower((string) ($account->account_status ?? 'active'));

        if ($status === 'banned') {
            throw ValidationException::withMessages([
                'account' => 'A permanently banned account cannot be blocked.',
            ]);
        }

        if ($normalizedRole === 'seller') {
            /** @var SellerAccount $account */
            $account = $this->sellerStatusService->block($account, $validated['reason']);
        } elseif (Schema::hasColumn($account->getTable(), 'account_status')) {
            $account->forceFill(['account_status' => 'blocked'])->save();
            $account->refresh();
        }

        $this->recordActivity(
            $request,
            $normalizedRole,
            (int) $account->getKey(),
            'blocked',
            $validated['reason'],
            ['status' => 'blocked']
        );

        return $this->respond(
            $request,
            'Account blocked successfully. Sign-in and active sessions are no longer allowed.',
            $account,
            $normalizedRole
        );
    }

    public function unblock(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);
        $status = strtolower((string) ($account->account_status ?? 'active'));

        if ($status !== 'blocked') {
            throw ValidationException::withMessages([
                'account' => 'Only blocked accounts can be unblocked.',
            ]);
        }

        if ($normalizedRole === 'seller') {
            /** @var SellerAccount $account */
            $account = $this->sellerStatusService->unblock($account);
        } elseif (Schema::hasColumn($account->getTable(), 'account_status')) {
            $account->forceFill(['account_status' => 'active'])->save();
            $account->refresh();
        }

        $this->recordActivity(
            $request,
            $normalizedRole,
            (int) $account->getKey(),
            'unblocked',
            'Admin restored access to a blocked account.',
            ['status' => 'active']
        );

        return $this->respond(
            $request,
            'Account unblocked successfully.',
            $account,
            $normalizedRole
        );
    }

    public function ban(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);
        $email = strtolower(trim((string) ($account->email ?? '')));

        if (strtolower((string) ($account->account_status ?? 'active')) === 'banned') {
            throw ValidationException::withMessages([
                'account' => 'This account is already banned.',
            ]);
        }

        if ($normalizedRole === 'seller') {
            /** @var SellerAccount $account */
            $account = $this->sellerStatusService->ban(
                $account,
                $validated['reason']
            );
        } elseif (Schema::hasColumn($account->getTable(), 'account_status')) {
            $account->forceFill(['account_status' => 'banned'])->save();
            $account->refresh();
        }

        $this->emailBanService->ban(
            $email,
            $normalizedRole,
            (int) $account->id,
            $request->session()->get('admin_account_id'),
            $validated['reason']
        );

        $this->recordActivity(
            $request,
            $normalizedRole,
            $account->id,
            'banned',
            $validated['reason'],
            ['status' => 'banned']
        );

        return $this->respond(
            $request,
            'Account banned successfully.',
            $account,
            $normalizedRole
        );
    }

    public function unban(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);
        $email = strtolower(trim((string) ($account->email ?? '')));

        if (strtolower((string) ($account->account_status ?? 'active')) !== 'banned') {
            throw ValidationException::withMessages([
                'account' => 'Only banned accounts can have their ban cancelled.',
            ]);
        }

        DB::transaction(function () use ($request, $account, $normalizedRole, $email): void {
            $this->emailBanService->unban($email);
            $this->recordActivity(
                $request,
                $normalizedRole,
                (int) $account->getKey(),
                'ban_cancelled',
                'Admin cancelled the ban and released the email for a fresh registration.',
                ['released_email' => $email]
            );
            $this->releaseAccountForReregistration($account, $normalizedRole);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ban cancelled. The email can now register again.',
            ]);
        }

        return back()->with('success', 'Ban cancelled. The email can now register again.');
    }

    public function destroy(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $normalizedRole = $this->normalizeRole($role);
        $account = $this->resolveAccount($role, $id);
        $originalEmail = strtolower(trim((string) ($account->email ?? '')));
        DB::transaction(function () use ($request, $account, $normalizedRole, $originalEmail): void {
            // Delete means the email is intentionally reusable.
            $this->emailBanService->unban($originalEmail);
            $this->recordActivity(
                $request,
                $normalizedRole,
                (int) $account->getKey(),
                'deleted',
                'Admin deleted the account and released its email for future registration.',
                ['released_email' => $originalEmail]
            );
            $this->releaseAccountForReregistration($account, $normalizedRole);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Account deleted. The email can now be used for a new registration.',
            ]);
        }

        return back()->with('success', 'Account deleted. The email can now be used for a new registration.');
    }

    public function note(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $validated = $request->validate([
            'note' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);

        $this->recordActivity(
            $request,
            $normalizedRole,
            $account->id,
            'admin_note',
            $validated['note']
        );

        return $this->respond(
            $request,
            'Admin note added to the account timeline.',
            $account,
            $normalizedRole
        );
    }

    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_admin'),
            403,
            'Administrator session required.'
        );
    }

    private function resolveAccount(string $role, int $id): Model
    {
        return match ($this->normalizeRole($role)) {
            'buyer' => BuyerAccount::query()->findOrFail($id),
            'seller' => SellerAccount::query()->findOrFail($id),
            'rider' => CourierAccount::query()->findOrFail($id),
            'logistics' => LogisticsAccount::query()->findOrFail($id),
            'social_buyer' => SocialAccount::query()->findOrFail($id),
            default => abort(404),
        };
    }

    private function normalizeRole(string $role): string
    {
        return match (strtolower(trim($role))) {
            'buyer' => 'buyer',
            'seller' => 'seller',
            'rider', 'courier' => 'rider',
            'logistics' => 'logistics',
            'social_buyer', 'social-buyer' => 'social_buyer',
            default => abort(404),
        };
    }

    private function mapAccounts(Collection $accounts, string $role, Collection $activity, Collection $lastSignIns): Collection
    {
        return $accounts->map(function ($account) use ($role, $activity, $lastSignIns): array {
            $personalName = trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? ''));
            $name = $personalName ?: ($account->store_name ?? $account->business_name ?? '');

            $status = strtolower((string) ($account->account_status ?? ($account->registration_status ?? 'active')));
            $roleKey = strtolower($role);
            $key = $roleKey . ':' . $account->id;
            $timeline = collect($activity->get($key, collect()))
                ->take(8)
                ->map(fn (AdminUserActivity $event) => [
                    'action' => $event->action,
                    'description' => $event->description,
                    'created_at' => $event->created_at,
                ])
                ->values();

            if ($timeline->isEmpty() && $account->created_at) {
                $timeline->push([
                    'action' => 'account_created',
                    'description' => 'Account created on the SARI platform.',
                    'created_at' => $account->created_at,
                ]);
            }

            $lastLogin = $lastSignIns->get($key);
            $lastSignInAt = $lastLogin && $lastLogin->last_sign_in_at
                ? \Illuminate\Support\Carbon::parse($lastLogin->last_sign_in_at)
                : null;
            $reference = strtoupper($roleKey) . '-' . str_pad((string) $account->id, 3, '0', STR_PAD_LEFT);

            return [
                'id' => $account->id,
                'role' => $role,
                'role_key' => $roleKey,
                'account_reference' => $reference,
                'profile_photo_url' => filled($account->profile_image_path)
                    ? route('admin.users.photo', [$roleKey, $account->id])
                    : null,
                'last_sign_in_at' => $lastSignInAt,
                'name' => $name ?: $role . ' Account',
                'first_name' => $account->first_name ?? '',
                'last_name' => $account->last_name ?? '',
                'store_name' => $account->store_name ?? '',
                'email' => $account->email ?? '—',
                'contact_no' => $account->contact_no ?? '',
                'status' => $status ?: 'active',
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at,
                'registration_application_id' => $account->registration_application_id ?? null,
                'profile' => $this->accountProfilePayload(
                    $account,
                    $roleKey,
                    $reference,
                    $lastSignInAt
                ),
                'activity' => $timeline,
            ];
        });
    }

    private function accountProfilePayload(
        Model $account,
        string $role,
        string $reference,
        ?\Illuminate\Support\Carbon $lastSignInAt
    ): array {
        /** @var RegistrationApplication|null $registration */
        $registration = $account->relationLoaded('registrationApplication')
            ? $account->getRelation('registrationApplication')
            : null;

        $value = function (string $attribute) use ($account, $registration): mixed {
            $accountValue = $account->getAttribute($attribute);

            if ($accountValue !== null && $accountValue !== '') {
                return $accountValue;
            }

            return $registration?->getAttribute($attribute);
        };

        $personal = [
            $this->profileField('First name', $value('first_name')),
            $this->profileField('Middle initial', $value('middle_initial')),
            $this->profileField('Last name', $value('last_name')),
            $this->profileField('Sex', $value('sex')),
            $this->profileField('Birthday', $this->formatProfileDate($value('birthday'))),
            $this->profileField('Age', $value('age')),
            $this->profileField('Email address', $value('email')),
            $this->profileField('Contact number', $value('contact_no')),
        ];

        $address = [
            $this->profileField('Region', $value('region_name')),
            $this->profileField('Region code', $value('region_code')),
            $this->profileField('Province', $value('province_name')),
            $this->profileField('Province code', $value('province_code')),
            $this->profileField('Municipality / City', $value('municipality_name')),
            $this->profileField('Municipality code', $value('municipality_code')),
            $this->profileField('Barangay', $value('barangay_name')),
            $this->profileField('Barangay code', $value('barangay_code')),
            $this->profileField('Street address', $value('street_address')),
            $this->profileField('Postal code', $value('postal_code')),
        ];

        $roleDetails = match ($role) {
            'seller' => [
                $this->profileField('Store / business name', $account->store_name ?: $registration?->business_name),
                $this->profileField('Seller type', $this->humanizeProfileValue($value('seller_type'))),
                $this->profileField('Line of business', $value('line_of_business')),
                $this->profileField('TIN', $value('tin')),
                $this->profileField('SEC registration no.', $value('sec_registration_no')),
                $this->profileField('Store description', $account->store_description),
                $this->profileField('Store phone', $account->store_phone),
                $this->profileField('Store public email', $account->store_public_email),
                $this->profileField('Store status', $this->humanizeProfileValue($account->store_status)),
                $this->profileField('Pickup address', $account->pickup_address),
                $this->profileField('Pickup instructions', $account->pickup_instructions),
                $this->profileField('Warning count', $account->warning_count),
                $this->profileField('Suspension reason', $account->suspension_reason),
                $this->profileField('Suspended at', $this->formatProfileDateTime($account->suspended_at)),
                $this->profileField('Suspended until', $this->formatProfileDateTime($account->suspended_until)),
            ],
            'logistics' => [
                $this->profileField('Business name', $account->business_name ?: $registration?->business_name),
                $this->profileField('Position', $value('position')),
                $this->profileField('Service areas', $value('service_areas')),
                $this->profileField('TIN', $value('tin')),
                $this->profileField('SEC registration no.', $value('sec_registration_no')),
            ],
            'rider' => [
                $this->profileField('Logistics provider', $account->logistics?->displayName()),
                $this->profileField('Logistics account ID', $account->logistics_account_id),
                $this->profileField('Vehicle type', $value('vehicle_type')),
                $this->profileField('Vehicle model', $account->vehicle_model),
                $this->profileField('Plate number', $value('plate_number')),
                $this->profileField('Driver license number', $value('license_number')),
                $this->profileField('License expiry date', $this->formatProfileDate($value('license_expiry_date'))),
                $this->profileField('Availability status', $this->humanizeProfileValue($account->availability_status)),
                $this->profileField('Rating', $account->rating),
                $this->profileField('Activated at', $this->formatProfileDateTime($account->activated_at)),
            ],
            default => [],
        };

        $registrationFields = [
            $this->profileField('Account reference', $reference),
            $this->profileField('Role', ucfirst(str_replace('_', ' ', $role))),
            $this->profileField('Account status', $this->humanizeProfileValue($account->account_status ?? $account->registration_status ?? 'active')),
            $this->profileField('Registration application ID', $registration?->id ?? $account->registration_application_id),
            $this->profileField('Registration status', $this->humanizeProfileValue($registration?->status ?? $account->registration_status)),
            $this->profileField('Application submitted', $this->formatProfileDateTime($registration?->created_at)),
            $this->profileField('Reviewed at', $this->formatProfileDateTime($registration?->reviewed_at)),
            $this->profileField('Approved at', $this->formatProfileDateTime($account->approved_at ?? $registration?->approved_at)),
            $this->profileField('Rejected at', $this->formatProfileDateTime($registration?->rejected_at)),
            $this->profileField('Review note', $registration?->admin_note),
            $this->profileField('Last sign-in', $this->formatProfileDateTime($lastSignInAt)),
            $this->profileField('Account created', $this->formatProfileDateTime($account->created_at)),
            $this->profileField('Last updated', $this->formatProfileDateTime($account->updated_at)),
        ];

        return [
            'sections' => [
                ['title' => 'Personal & Contact Information', 'fields' => $personal],
                ['title' => 'Address Information', 'fields' => $address],
                ...($roleDetails !== [] ? [['title' => $this->roleSectionTitle($role), 'fields' => $roleDetails]] : []),
                ['title' => 'Registration & Account Record', 'fields' => $registrationFields],
            ],
            'documents' => $this->accountDocumentLinks($account, $role),
        ];
    }

    private function profileField(string $label, mixed $value): array
    {
        $display = is_string($value) ? trim($value) : $value;

        if ($display === null || $display === '') {
            $display = '—';
        } elseif (is_bool($display)) {
            $display = $display ? 'Yes' : 'No';
        } elseif (is_array($display)) {
            $display = implode(', ', array_map('strval', $display));
        }

        return [
            'label' => $label,
            'value' => (string) $display,
        ];
    }

    private function roleSectionTitle(string $role): string
    {
        return match ($role) {
            'seller' => 'Seller & Store Information',
            'logistics' => 'Logistics Business Information',
            'rider' => 'Rider & Vehicle Information',
            default => 'Role Information',
        };
    }

    private function humanizeProfileValue(mixed $value): mixed
    {
        if (!is_string($value) || trim($value) === '') {
            return $value;
        }

        return ucwords(str_replace(['_', '-'], ' ', trim($value)));
    }

    private function formatProfileDate(mixed $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format('M d, Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function formatProfileDateTime(mixed $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)
                ->timezone('Asia/Manila')
                ->format('M d, Y h:i A') . ' PHT';
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function accountDocumentLinks(Model $account, string $role): array
    {
        $definitions = $this->accountDocumentDefinitions($role);
        $documents = [];

        foreach ($definitions as $key => $definition) {
            if (!filled($this->accountDocumentPath($account, $role, $key))) {
                continue;
            }

            $documents[] = [
                'key' => $key,
                'label' => $definition['label'],
                'url' => route('admin.users.document', [$role, $account->getKey(), $key]),
            ];
        }

        return $documents;
    }

    private function accountDocumentDefinitions(string $role): array
    {
        return match ($role) {
            'buyer' => [
                'id' => ['label' => 'Valid ID', 'account_columns' => ['id_path'], 'registration_column' => 'id_path'],
            ],
            'seller' => [
                'id' => ['label' => 'Valid ID', 'account_columns' => ['id_path'], 'registration_column' => 'id_path'],
                'permit' => ['label' => 'Business Permit', 'account_columns' => ['business_permit_path'], 'registration_column' => 'business_permit_path'],
                'bir' => ['label' => 'BIR Document', 'account_columns' => ['bir_path'], 'registration_column' => 'bir_path'],
                'sec' => ['label' => 'SEC Document', 'account_columns' => ['sec_path'], 'registration_column' => 'sec_path'],
            ],
            'logistics' => [
                'id' => ['label' => 'Valid ID', 'account_columns' => ['id_path'], 'registration_column' => 'id_path'],
                'permit' => ['label' => 'Business Permit', 'account_columns' => ['business_permit_path'], 'registration_column' => 'business_permit_path'],
                'bir' => ['label' => 'BIR Document', 'account_columns' => ['bir_path'], 'registration_column' => 'bir_path'],
                'sec' => ['label' => 'SEC Document', 'account_columns' => ['sec_path'], 'registration_column' => 'sec_path'],
            ],
            'rider' => [
                'license' => ['label' => "Driver's License", 'account_columns' => ['driver_license_path', 'id_path'], 'registration_column' => 'id_path'],
                'orcr' => ['label' => 'OR/CR', 'account_columns' => ['orcr_path'], 'registration_column' => 'orcr_path'],
            ],
            default => [],
        };
    }

    private function accountDocumentPath(Model $account, string $role, string $document): ?string
    {
        $definition = $this->accountDocumentDefinitions($role)[$document] ?? null;
        abort_unless($definition, 404);

        foreach ($definition['account_columns'] as $column) {
            $path = $account->getAttribute($column);
            if (filled($path)) {
                return (string) $path;
            }
        }

        /** @var RegistrationApplication|null $registration */
        $registration = $account->relationLoaded('registrationApplication')
            ? $account->getRelation('registrationApplication')
            : null;

        if (!$registration && filled($account->registration_application_id ?? null)) {
            $registration = RegistrationApplication::query()->find($account->registration_application_id);
        }

        $path = $registration?->getAttribute($definition['registration_column']);

        return filled($path) ? (string) $path : null;
    }

    private function releaseAccountForReregistration(Model $account, string $role): void
    {
        $originalEmail = strtolower(trim((string) ($account->email ?? '')));

        $registrationApplicationId = Schema::hasColumn($account->getTable(), 'registration_application_id')
            ? $account->registration_application_id
            : null;

        /*
         * Release every active registration application that can still reserve
         * this email. Older/test accounts may not have registration_application_id
         * populated, so the email fallback is required.
         *
         * AccountRegistrationPolicy treats unverified, pending and approved
         * registration applications as "email in use", while rejected records
         * no longer block a fresh registration.
         */
        if ($registrationApplicationId || $originalEmail !== '') {
            $releasedRegistrationPassword = Hash::make(Str::random(64));

            RegistrationApplication::query()
                ->where(function ($query) use ($registrationApplicationId, $originalEmail): void {
                    if ($registrationApplicationId) {
                        $query->whereKey($registrationApplicationId);
                    }

                    if ($originalEmail !== '') {
                        if ($registrationApplicationId) {
                            $query->orWhereRaw('LOWER(email) = ?', [$originalEmail]);
                        } else {
                            $query->whereRaw('LOWER(email) = ?', [$originalEmail]);
                        }
                    }
                })
                ->whereIn('status', ['unverified', 'pending', 'approved'])
                ->update([
                    'status' => 'rejected',
                    'password' => $releasedRegistrationPassword,
                    'admin_note' => 'Previous account was deleted or released by an administrator. This email may be used for a new registration.',
                    'reviewed_at' => now(),
                    'approved_at' => null,
                    'rejected_at' => now(),
                ]);
        }

        /*
         * The account itself may be soft-deleted. Replace its email first so a
         * database UNIQUE index cannot keep the original email reserved.
         */
        $updates = [
            'email' => 'released+' . $role . '+' . $account->getKey() . '+' . now()->timestamp . '@deleted.sari.local',
        ];

        if (Schema::hasColumn($account->getTable(), 'password')) {
            $updates['password'] = Hash::make(Str::random(64));
        }

        // Unique identifiers on soft-deleted Seller/Logistics rows must be
        // released too, otherwise a fresh registration can still fail at the
        // database UNIQUE constraint even though the account is deleted.
        foreach (['tin', 'sec_registration_no', 'activation_token_hash'] as $column) {
            if (Schema::hasColumn($account->getTable(), $column)) {
                $updates[$column] = null;
            }
        }

        if (Schema::hasColumn($account->getTable(), 'registration_application_id')) {
            $updates['registration_application_id'] = null;
        }

        if (Schema::hasColumn($account->getTable(), 'account_status')) {
            $updates['account_status'] = 'deleted';
        }

        $account->forceFill($updates)->save();
        $account->delete();
    }

    private function editableSnapshot(Model $account, string $role): array
    {
        $snapshot = [];

        foreach (['first_name', 'last_name', 'contact_no'] as $field) {
            if (Schema::hasColumn($account->getTable(), $field)) {
                $snapshot[$field] = $account->{$field};
            }
        }

        if ($role === 'seller' && Schema::hasColumn($account->getTable(), 'store_name')) {
            $snapshot['store_name'] = $account->store_name;
        }

        return $snapshot;
    }

    private function respond(
        Request $request,
        string $message,
        Model $account,
        string $role
    ): RedirectResponse|JsonResponse {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'account' => [
                    'id' => (int) $account->getKey(),
                    'role' => $role,
                    'status' => strtolower(
                        (string) ($account->account_status ?? 'active')
                    ),
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    private function recordActivity(
        Request $request,
        string $role,
        int $userId,
        string $action,
        ?string $description = null,
        ?array $metadata = null
    ): void {
        if (!Schema::hasTable('admin_user_activities')) {
            return;
        }

        AdminUserActivity::query()->create([
            'admin_account_id' => $request->session()->get('admin_account_id'),
            'user_role' => $role,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
