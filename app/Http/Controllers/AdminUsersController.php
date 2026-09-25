<?php

namespace App\Http\Controllers;

use App\Models\AdminUserActivity;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\SellerAccount;
use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Services\SellerAccountStatusService;
use Illuminate\Validation\ValidationException;

class AdminUsersController extends Controller
{
    public function __construct(
        private readonly SellerAccountStatusService $sellerStatusService
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

        $accounts = collect()
            ->concat($this->mapAccounts(BuyerAccount::query()->latest()->get(), 'Buyer', $activity))
            ->concat($this->mapAccounts(SellerAccount::query()->latest()->get(), 'Seller', $activity))
            ->concat($this->mapAccounts(CourierAccount::query()->latest()->get(), 'Rider', $activity))
            ->concat($this->mapAccounts(LogisticsAccount::query()->latest()->get(), 'Logistics', $activity))
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

    public function ban(Request $request, string $role, int $id): RedirectResponse|JsonResponse
    {
        $this->guard($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $account = $this->resolveAccount($role, $id);
        $normalizedRole = $this->normalizeRole($role);

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

        if (strtolower((string) ($account->account_status ?? 'active')) !== 'banned') {
            throw ValidationException::withMessages([
                'account' => 'Only banned accounts can be unbanned.',
            ]);
        }

        if ($normalizedRole === 'seller') {
            /** @var SellerAccount $account */
            $account = $this->sellerStatusService->unban($account);
        } elseif (Schema::hasColumn($account->getTable(), 'account_status')) {
            $account->forceFill(['account_status' => 'active'])->save();
            $account->refresh();
        }

        $this->recordActivity(
            $request,
            $normalizedRole,
            $account->id,
            'unbanned',
            'Admin restored a banned account.',
            ['status' => 'active']
        );

        return $this->respond(
            $request,
            'Account unbanned successfully.',
            $account,
            $normalizedRole
        );
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

    private function mapAccounts(Collection $accounts, string $role, Collection $activity): Collection
    {
        return $accounts->map(function ($account) use ($role, $activity): array {
            $name = $role === 'Seller'
                ? ($account->store_name ?: trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? '')))
                : trim(($account->first_name ?? '') . ' ' . ($account->last_name ?? ''));

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

            return [
                'id' => $account->id,
                'role' => $role,
                'role_key' => $roleKey,
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
                'activity' => $timeline,
            ];
        });
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
