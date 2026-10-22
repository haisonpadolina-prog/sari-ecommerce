<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Promotions\PlatformCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminPlatformCampaignController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $campaigns = PlatformCampaign::query()
            ->withCount(['products', 'sellers', 'categories', 'redemptions'])
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $editing = null;
        $editId = (int) $request->query('edit', 0);

        if ($editId > 0) {
            $editing = PlatformCampaign::query()
                ->with(['products:id', 'sellers:id', 'categories'])
                ->find($editId);
        }

        $sellers = SellerAccount::query()
            ->orderBy('store_name')
            ->orderBy('email')
            ->get(['id', 'store_name', 'email']);

        $products = SellerProduct::query()
            ->orderBy('name')
            ->limit(1500)
            ->get(['id', 'seller_account_id', 'name', 'category', 'price']);

        $categories = SellerProduct::query()
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->map(fn ($category) => trim((string) $category))
            ->filter()
            ->unique(fn ($category) => strtolower($category))
            ->values();

        return view('admin.platform-campaigns', compact(
            'campaigns',
            'editing',
            'sellers',
            'products',
            'categories'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);

        $validated = $this->validated($request);

        $campaign = DB::transaction(function () use ($request, $validated): PlatformCampaign {
            $campaign = new PlatformCampaign();
            $this->fillCampaign($campaign, $request, $validated);
            $campaign->admin_account_id = $this->adminId($request);
            $campaign->slug = $this->uniqueSlug((string) $validated['name']);
            $campaign->save();
            $this->syncTargets($campaign, $validated);

            return $campaign;
        });

        return redirect()
            ->route('admin.platform-campaigns', ['edit' => $campaign->id])
            ->with('success', 'Campaign saved. Published campaigns marked Activate immediately are live for Buyers now using Philippine time.');
    }

    public function update(
        Request $request,
        PlatformCampaign $campaign
    ): RedirectResponse {
        abort_unless($request->session()->get('is_admin'), 403);

        $validated = $this->validated($request, $campaign);

        DB::transaction(function () use ($request, $campaign, $validated): void {
            $this->fillCampaign($campaign, $request, $validated);
            $campaign->save();
            $this->syncTargets($campaign, $validated);
        });

        return redirect()
            ->route('admin.platform-campaigns', ['edit' => $campaign->id])
            ->with('success', 'Campaign updated successfully.');
    }

    public function publish(Request $request, PlatformCampaign $campaign): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);

        if (!$campaign->sale_enabled && !$campaign->voucher_enabled) {
            return back()->withErrors([
                'campaign' => 'Enable a Buyer sale discount or voucher before publishing this campaign.',
            ]);
        }

        $campaign->update(['state' => 'published']);

        return back()->with('success', 'Campaign published. Start/end timestamps control when Buyers receive the promotion.');
    }

    public function pause(Request $request, PlatformCampaign $campaign): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);
        $campaign->update(['state' => 'paused']);

        return back()->with('success', 'Campaign paused. Buyer campaign discounts are no longer applied.');
    }

    public function destroy(Request $request, PlatformCampaign $campaign): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);

        if ($campaign->redemptions()->exists()) {
            throw ValidationException::withMessages([
                'campaign' => 'This campaign already has voucher redemptions. Pause it instead so audit history is preserved.',
            ]);
        }

        $campaign->delete();

        return redirect()
            ->route('admin.platform-campaigns')
            ->with('success', 'Campaign deleted.');
    }

    private function validated(Request $request, ?PlatformCampaign $campaign = null): array
    {
        if ($request->filled('voucher_code')) {
            $request->merge([
                'voucher_code' => strtoupper(trim((string) $request->input('voucher_code'))),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:1200'],
            'state' => ['required', Rule::in(['draft', 'published', 'paused'])],
            'target_scope' => ['required', Rule::in(['all', 'sellers', 'products', 'categories'])],
            'seller_ids' => ['nullable', 'array'],
            'seller_ids.*' => ['integer', 'exists:seller_accounts,id'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:seller_products,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', 'max:120'],

            'sale_enabled' => ['required', 'boolean'],
            'sale_discount_type' => ['nullable', Rule::in(['percentage', 'fixed'])],
            'sale_discount_value' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'sale_max_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],

            'voucher_enabled' => ['required', 'boolean'],
            'voucher_code' => [
                'nullable',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('platform_campaigns', 'voucher_code')->ignore($campaign?->id),
            ],
            'voucher_discount_type' => ['nullable', Rule::in(['percentage', 'fixed'])],
            'voucher_discount_value' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'voucher_minimum_spend' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'voucher_maximum_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'voucher_usage_limit' => ['nullable', 'integer', 'min:1', 'max:100000000'],
            'voucher_per_buyer_limit' => ['nullable', 'integer', 'min:1', 'max:1000'],

            'activate_immediately' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['required', 'date'],
        ]);

        $validated['sale_enabled'] = $request->boolean('sale_enabled');
        $validated['voucher_enabled'] = $request->boolean('voucher_enabled');
        $validated['activate_immediately'] = $request->boolean('activate_immediately');

        $errors = [];

        $startUtc = null;

        if ($validated['activate_immediately'] && ($validated['state'] ?? 'draft') === 'published') {
            $startUtc = PlatformCampaign::storageNow();
        } elseif (!empty($validated['starts_at'])) {
            $startUtc = $this->manilaToUtc((string) $validated['starts_at']);
        } else {
            $errors['starts_at'] = 'Choose a Philippine start time or enable Activate immediately.';
        }

        $endUtc = $this->manilaToUtc((string) $validated['ends_at']);

        if ($startUtc && $endUtc->lte($startUtc)) {
            $errors['ends_at'] = 'End time must be later than the campaign start time in Philippine time.';
        }

        if ($endUtc->lte(PlatformCampaign::storageNow())) {
            $errors['ends_at'] = 'End time must be in the future using Philippine time.';
        }

        $validated['_starts_at_utc'] = $startUtc;
        $validated['_ends_at_utc'] = $endUtc;

        if ($validated['sale_enabled']) {
            if (empty($validated['sale_discount_type']) || (float) ($validated['sale_discount_value'] ?? 0) <= 0) {
                $errors['sale_discount_value'] = 'Enter the Buyer sale discount type and a value greater than zero.';
            }

            if (($validated['sale_discount_type'] ?? null) === 'percentage'
                && (float) ($validated['sale_discount_value'] ?? 0) > 100) {
                $errors['sale_discount_value'] = 'Percentage Buyer sale discounts cannot exceed 100%.';
            }
        }

        if ($validated['voucher_enabled']) {
            if (trim((string) ($validated['voucher_code'] ?? '')) === '') {
                $errors['voucher_code'] = 'Enter a voucher code.';
            }

            if (empty($validated['voucher_discount_type']) || (float) ($validated['voucher_discount_value'] ?? 0) <= 0) {
                $errors['voucher_discount_value'] = 'Enter the voucher discount type and a value greater than zero.';
            }

            if (($validated['voucher_discount_type'] ?? null) === 'percentage'
                && (float) ($validated['voucher_discount_value'] ?? 0) > 100) {
                $errors['voucher_discount_value'] = 'Percentage voucher discounts cannot exceed 100%.';
            }
        }

        if (($validated['state'] ?? 'draft') === 'published'
            && !$validated['sale_enabled']
            && !$validated['voucher_enabled']) {
            $errors['campaign'] = 'A published campaign needs a Buyer sale discount, voucher, or both.';
        }

        $scope = (string) $validated['target_scope'];
        $targetCount = match ($scope) {
            'sellers' => count($validated['seller_ids'] ?? []),
            'products' => count($validated['product_ids'] ?? []),
            'categories' => count(array_filter($validated['categories'] ?? [])),
            default => 1,
        };

        if ($targetCount < 1) {
            $errors['target_scope'] = 'Choose at least one target for the selected campaign scope.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
    }

    private function fillCampaign(
        PlatformCampaign $campaign,
        Request $request,
        array $validated
    ): void {
        $campaign->fill([
            'name' => trim((string) $validated['name']),
            'description' => trim((string) ($validated['description'] ?? '')) ?: null,
            'state' => (string) $validated['state'],
            'target_scope' => (string) $validated['target_scope'],
            'sale_enabled' => (bool) $validated['sale_enabled'],
            'sale_discount_type' => $validated['sale_enabled'] ? $validated['sale_discount_type'] : null,
            'sale_discount_value' => $validated['sale_enabled'] ? (float) $validated['sale_discount_value'] : 0,
            'sale_max_discount' => $validated['sale_enabled'] && filled($validated['sale_max_discount'] ?? null)
                ? (float) $validated['sale_max_discount']
                : null,
            'voucher_enabled' => (bool) $validated['voucher_enabled'],
            'voucher_code' => $validated['voucher_enabled']
                ? strtoupper(trim((string) $validated['voucher_code']))
                : null,
            'voucher_discount_type' => $validated['voucher_enabled'] ? $validated['voucher_discount_type'] : null,
            'voucher_discount_value' => $validated['voucher_enabled'] ? (float) $validated['voucher_discount_value'] : 0,
            'voucher_minimum_spend' => $validated['voucher_enabled'] ? (float) ($validated['voucher_minimum_spend'] ?? 0) : 0,
            'voucher_maximum_discount' => $validated['voucher_enabled'] && filled($validated['voucher_maximum_discount'] ?? null)
                ? (float) $validated['voucher_maximum_discount']
                : null,
            'voucher_usage_limit' => $validated['voucher_enabled'] && filled($validated['voucher_usage_limit'] ?? null)
                ? (int) $validated['voucher_usage_limit']
                : null,
            'voucher_per_buyer_limit' => $validated['voucher_enabled']
                ? max(1, (int) ($validated['voucher_per_buyer_limit'] ?? 1))
                : 1,
            // Admin/Buyer promotion time is always Philippine Standard Time.
            // UTC is used only as the database storage representation.
            'starts_at' => $validated['_starts_at_utc'],
            'ends_at' => $validated['_ends_at_utc'],
        ]);
    }

    private function syncTargets(PlatformCampaign $campaign, array $validated): void
    {
        $scope = (string) $validated['target_scope'];

        $campaign->products()->sync(
            $scope === 'products'
                ? collect($validated['product_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->all()
                : []
        );

        $campaign->sellers()->sync(
            $scope === 'sellers'
                ? collect($validated['seller_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->all()
                : []
        );

        $campaign->categories()->delete();

        if ($scope === 'categories') {
            collect($validated['categories'] ?? [])
                ->map(fn ($category) => trim((string) $category))
                ->filter()
                ->unique(fn ($category) => strtolower($category))
                ->each(fn ($category) => $campaign->categories()->create(['category' => $category]));
        }
    }

    private function manilaToUtc(string $value): Carbon
    {
        return Carbon::parse($value, PlatformCampaign::TIMEZONE)->utc();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'campaign';
        $slug = $base;
        $suffix = 2;

        while (PlatformCampaign::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function adminId(Request $request): ?int
    {
        $id = (int) $request->session()->get('admin_account_id', 0);

        return $id > 0 ? $id : null;
    }
}
