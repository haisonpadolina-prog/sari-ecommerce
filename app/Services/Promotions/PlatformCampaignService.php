<?php

namespace App\Services\Promotions;

use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductVariant;
use App\Models\Orders\BuyerCartItem;
use App\Models\Platform\PlatformSetting;
use App\Models\Promotions\PlatformCampaign;
use App\Models\Promotions\PlatformCampaignRedemption;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PlatformCampaignService
{
    private ?Collection $liveCampaigns = null;

    /**
     * Platform events are buyer-only overlays. The Seller's persisted price is
     * never changed here; this method only returns a quoted Buyer price.
     *
     * @return array{seller_price: float, buyer_price: float, discount: float, campaign: ?PlatformCampaign, snapshot: ?array}
     */
    public function saleQuote(
        SellerProduct $product,
        ?SellerProductVariant $variant,
        float $sellerUnitPrice
    ): array {
        $sellerUnitPrice = round(max(0, $sellerUnitPrice), 2);
        $best = null;

        foreach ($this->liveCampaigns() as $campaign) {
            if (!$campaign->sale_enabled || !$this->appliesToProduct($campaign, $product)) {
                continue;
            }

            $discount = $this->discountAmount(
                $sellerUnitPrice,
                (string) $campaign->sale_discount_type,
                (float) $campaign->sale_discount_value,
                $campaign->sale_max_discount !== null ? (float) $campaign->sale_max_discount : null
            );

            if ($discount <= 0) {
                continue;
            }

            if ($best === null || $discount > $best['discount']) {
                $best = [
                    'discount' => $discount,
                    'campaign' => $campaign,
                ];
            }
        }

        if ($best === null) {
            return [
                'seller_price' => $sellerUnitPrice,
                'buyer_price' => $sellerUnitPrice,
                'discount' => 0.0,
                'campaign' => null,
                'snapshot' => null,
            ];
        }

        /** @var PlatformCampaign $campaign */
        $campaign = $best['campaign'];
        $discount = round(min($sellerUnitPrice, (float) $best['discount']), 2);

        return [
            'seller_price' => $sellerUnitPrice,
            'buyer_price' => round(max(0, $sellerUnitPrice - $discount), 2),
            'discount' => $discount,
            'campaign' => $campaign,
            'snapshot' => $this->campaignSnapshot($campaign),
        ];
    }

    public function voucherOfferForProduct(SellerProduct $product): ?array
    {
        $campaign = $this->liveCampaigns()
            ->first(function (PlatformCampaign $campaign) use ($product): bool {
                return $campaign->voucher_enabled
                    && trim((string) $campaign->voucher_code) !== ''
                    && ($campaign->voucher_usage_limit === null
                        || (int) $campaign->voucher_used_count < (int) $campaign->voucher_usage_limit)
                    && $this->appliesToProduct($campaign, $product);
            });

        return $campaign instanceof PlatformCampaign
            ? $this->voucherPayload($campaign)
            : null;
    }

    /**
     * Buyer-facing voucher catalog. This is intentionally independent from the
     * cart so Admin-created live vouchers can appear immediately on the
     * Rewards / Vouchers page.
     *
     * @param array{buyer_account_id: ?int, buyer_social_account_id: ?int}|null $identity
     * @return array<int, array<string, mixed>>
     */
    public function buyerVoucherCatalog(?array $identity = null): array
    {
        return $this->liveCampaigns()
            ->filter(function (PlatformCampaign $campaign) use ($identity): bool {
                if (!$campaign->voucher_enabled || trim((string) $campaign->voucher_code) === '') {
                    return false;
                }

                if ($campaign->voucher_usage_limit !== null
                    && (int) $campaign->voucher_used_count >= (int) $campaign->voucher_usage_limit) {
                    return false;
                }

                if ($identity !== null
                    && $this->buyerRedemptionCount($campaign, $identity) >= max(1, (int) ($campaign->voucher_per_buyer_limit ?: 1))) {
                    return false;
                }

                return true;
            })
            ->map(fn (PlatformCampaign $campaign) => $this->voucherPayload($campaign))
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function voucherOffersForItems(Collection $items, ?array $identity = null): array
    {
        $products = $items
            ->map(fn (BuyerCartItem $item) => $item->product)
            ->filter(fn ($product) => $product instanceof SellerProduct)
            ->values();

        return $this->liveCampaigns()
            ->filter(function (PlatformCampaign $campaign) use ($products, $identity): bool {
                if (!$campaign->voucher_enabled || trim((string) $campaign->voucher_code) === '') {
                    return false;
                }

                if ($campaign->voucher_usage_limit !== null
                    && (int) $campaign->voucher_used_count >= (int) $campaign->voucher_usage_limit) {
                    return false;
                }

                if ($identity !== null && $this->buyerRedemptionCount($campaign, $identity) >= max(1, (int) ($campaign->voucher_per_buyer_limit ?: 1))) {
                    return false;
                }

                return $products->contains(
                    fn (SellerProduct $product) => $this->appliesToProduct($campaign, $product)
                );
            })
            ->map(fn (PlatformCampaign $campaign) => $this->voucherPayload($campaign))
            ->values()
            ->all();
    }

    /**
     * Resolve a SARI platform voucher once per checkout. If the code does not
     * belong to a platform campaign, null is returned so the existing Seller
     * voucher flow can handle it unchanged.
     *
     * Each line must contain: product, buyer_line_total, seller_id.
     *
     * @param Collection<int, array<string, mixed>> $lines
     * @param array{buyer_account_id: ?int, buyer_social_account_id: ?int} $identity
     * @return array{campaign: PlatformCampaign, discount: float, eligible_subtotal: float, allocations: array<int, float>, snapshot: array}|null
     */
    public function previewVoucherForCheckout(
        string $code,
        Collection $lines,
        array $identity
    ): ?array {
        return $this->resolveVoucher($code, $lines, $identity, false);
    }

    public function resolveVoucherForCheckout(
        string $code,
        Collection $lines,
        array $identity
    ): ?array {
        return $this->resolveVoucher($code, $lines, $identity, true);
    }

    /**
     * Shared voucher resolver used for non-mutating checkout preview and final
     * transactional validation. Final checkout optionally locks the campaign.
     *
     * @param Collection<int, array<string, mixed>> $lines
     * @param array{buyer_account_id: ?int, buyer_social_account_id: ?int} $identity
     * @return array{campaign: PlatformCampaign, discount: float, eligible_subtotal: float, allocations: array<int, float>, snapshot: array}|null
     */
    private function resolveVoucher(
        string $code,
        Collection $lines,
        array $identity,
        bool $lock
    ): ?array {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return null;
        }

        if ($this->liveCampaigns()->isEmpty()) {
            return null;
        }

        $query = PlatformCampaign::query()
            ->live()
            ->where('voucher_enabled', true)
            ->whereRaw('UPPER(voucher_code) = ?', [$code]);

        if ($lock) {
            $query->lockForUpdate();
        }

        $campaign = $query->first();

        if (!$campaign) {
            return null;
        }

        $campaign->load(['products:id', 'sellers:id', 'categories:id,platform_campaign_id,category']);

        if ($campaign->voucher_usage_limit !== null
            && (int) $campaign->voucher_used_count >= (int) $campaign->voucher_usage_limit) {
            throw ValidationException::withMessages([
                'voucher_code' => "SARI voucher {$code} has reached its usage limit.",
            ]);
        }

        $this->assertBuyerUsageAvailable($campaign, $identity, $code);

        $eligibleBySeller = [];

        foreach ($lines as $line) {
            $product = $line['product'] ?? null;

            if (!$product instanceof SellerProduct || !$this->appliesToProduct($campaign, $product)) {
                continue;
            }

            $sellerId = (int) ($line['seller_id'] ?? $product->seller_account_id ?? 0);
            $lineTotal = round(max(0, (float) ($line['buyer_line_total'] ?? 0)), 2);

            if ($sellerId <= 0 || $lineTotal <= 0) {
                continue;
            }

            $eligibleBySeller[$sellerId] = round(
                ($eligibleBySeller[$sellerId] ?? 0) + $lineTotal,
                2
            );
        }

        $eligibleSubtotal = round(array_sum($eligibleBySeller), 2);

        if ($eligibleSubtotal <= 0) {
            throw ValidationException::withMessages([
                'voucher_code' => "SARI voucher {$code} does not apply to the selected products.",
            ]);
        }

        $minimum = max(0, (float) $campaign->voucher_minimum_spend);

        if ($eligibleSubtotal < $minimum) {
            throw ValidationException::withMessages([
                'voucher_code' => "SARI voucher {$code} requires at least ₱"
                    . number_format($minimum, 2)
                    . ' of eligible products.',
            ]);
        }

        $discount = $this->discountAmount(
            $eligibleSubtotal,
            (string) $campaign->voucher_discount_type,
            (float) $campaign->voucher_discount_value,
            $campaign->voucher_maximum_discount !== null
                ? (float) $campaign->voucher_maximum_discount
                : null
        );

        if ($discount <= 0) {
            throw ValidationException::withMessages([
                'voucher_code' => "SARI voucher {$code} currently has no discount value.",
            ]);
        }

        return [
            'campaign' => $campaign,
            'discount' => $discount,
            'eligible_subtotal' => $eligibleSubtotal,
            'allocations' => $this->allocateDiscount($discount, $eligibleBySeller),
            'snapshot' => $this->campaignSnapshot($campaign),
        ];
    }

    /**
     * @param array{buyer_account_id: ?int, buyer_social_account_id: ?int} $identity
     * @param array<int, int> $orderIds
     */
    public function recordVoucherRedemption(
        PlatformCampaign $campaign,
        string $checkoutReference,
        array $identity,
        float $discount,
        array $orderIds
    ): void {
        PlatformCampaignRedemption::query()->create([
            'platform_campaign_id' => $campaign->id,
            'checkout_reference' => $checkoutReference,
            'buyer_account_id' => $identity['buyer_account_id'] ?? null,
            'buyer_social_account_id' => $identity['buyer_social_account_id'] ?? null,
            'voucher_code' => strtoupper((string) $campaign->voucher_code),
            'discount_amount' => round($discount, 2),
            'order_ids' => array_values(array_map('intval', $orderIds)),
        ]);

        $campaign->increment('voucher_used_count');
    }

    public function appliesToProduct(PlatformCampaign $campaign, SellerProduct $product): bool
    {
        return match ((string) $campaign->target_scope) {
            'products' => $campaign->products->contains(fn ($target) => (int) $target->id === (int) $product->id),
            'sellers' => $campaign->sellers->contains(fn ($target) => (int) $target->id === (int) $product->seller_account_id),
            'categories' => $campaign->categories->contains(function ($target) use ($product): bool {
                return strtolower(trim((string) $target->category))
                    === strtolower(trim((string) $product->category));
            }),
            default => true,
        };
    }

    public function campaignSnapshot(PlatformCampaign $campaign): array
    {
        return [
            'id' => (int) $campaign->id,
            'name' => (string) $campaign->name,
            'slug' => (string) $campaign->slug,
            'description' => (string) ($campaign->description ?? ''),
            'target_scope' => (string) $campaign->target_scope,
            'sale_enabled' => (bool) $campaign->sale_enabled,
            'sale_discount_type' => $campaign->sale_discount_type,
            'sale_discount_value' => (float) $campaign->sale_discount_value,
            'voucher_enabled' => (bool) $campaign->voucher_enabled,
            'voucher_code' => $campaign->voucher_code,
            'starts_at' => $campaign->starts_at?->toIso8601String(),
            'ends_at' => $campaign->ends_at?->toIso8601String(),
        ];
    }

    /** @return Collection<int, PlatformCampaign> */
    private function liveCampaigns(): Collection
    {
        if ($this->liveCampaigns !== null) {
            return $this->liveCampaigns;
        }

        $enabled = PlatformSetting::valueOf(
            'platform_campaigns_enabled',
            true
        );

        $enabled = is_bool($enabled)
            ? $enabled
            : (filter_var(
                $enabled,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? false);

        if (!$enabled) {
            return $this->liveCampaigns = collect();
        }

        return $this->liveCampaigns = PlatformCampaign::query()
            ->live()
            ->with(['products:id', 'sellers:id', 'categories:id,platform_campaign_id,category'])
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get();
    }

    private function buyerRedemptionCount(PlatformCampaign $campaign, array $identity): int
    {
        $query = PlatformCampaignRedemption::query()
            ->where('platform_campaign_id', $campaign->id);

        $buyerAccountId = (int) ($identity['buyer_account_id'] ?? 0);
        $buyerSocialId = (int) ($identity['buyer_social_account_id'] ?? 0);

        if ($buyerAccountId > 0) {
            return $query->where('buyer_account_id', $buyerAccountId)->count();
        }

        if ($buyerSocialId > 0) {
            return $query->where('buyer_social_account_id', $buyerSocialId)->count();
        }

        return PHP_INT_MAX;
    }

    private function assertBuyerUsageAvailable(
        PlatformCampaign $campaign,
        array $identity,
        string $code
    ): void {
        $limit = max(1, (int) ($campaign->voucher_per_buyer_limit ?: 1));

        $buyerAccountId = (int) ($identity['buyer_account_id'] ?? 0);
        $buyerSocialId = (int) ($identity['buyer_social_account_id'] ?? 0);

        if ($buyerAccountId <= 0 && $buyerSocialId <= 0) {
            throw ValidationException::withMessages([
                'voucher_code' => 'A Buyer account is required to use a SARI event voucher.',
            ]);
        }

        if ($this->buyerRedemptionCount($campaign, $identity) >= $limit) {
            throw ValidationException::withMessages([
                'voucher_code' => "You already reached the per-Buyer limit for SARI voucher {$code}.",
            ]);
        }
    }

    private function discountAmount(
        float $amount,
        string $type,
        float $value,
        ?float $maximum
    ): float {
        $amount = round(max(0, $amount), 2);
        $value = max(0, $value);

        $discount = $type === 'percentage'
            ? round($amount * (min(100, $value) / 100), 2)
            : round($value, 2);

        if ($maximum !== null) {
            $discount = min($discount, max(0, $maximum));
        }

        return round(min($amount, max(0, $discount)), 2);
    }

    /**
     * @param array<int, float> $eligibleBySeller
     * @return array<int, float>
     */
    private function allocateDiscount(float $discount, array $eligibleBySeller): array
    {
        $discountCents = (int) round($discount * 100);
        $eligibleCents = array_map(fn ($value) => (int) round($value * 100), $eligibleBySeller);
        $eligibleTotal = array_sum($eligibleCents);

        if ($discountCents <= 0 || $eligibleTotal <= 0) {
            return [];
        }

        $allocations = [];
        $remaining = $discountCents;
        $sellerIds = array_keys($eligibleCents);
        $lastSellerId = end($sellerIds);

        foreach ($eligibleCents as $sellerId => $sellerCents) {
            if ((int) $sellerId === (int) $lastSellerId) {
                $share = $remaining;
            } else {
                $share = (int) round($discountCents * ($sellerCents / $eligibleTotal));
                $share = min($share, $remaining);
            }

            $allocations[(int) $sellerId] = round($share / 100, 2);
            $remaining -= $share;
        }

        return $allocations;
    }

    private function voucherPayload(PlatformCampaign $campaign): array
    {
        $value = $campaign->voucher_discount_type === 'percentage'
            ? rtrim(rtrim(number_format((float) $campaign->voucher_discount_value, 2, '.', ''), '0'), '.') . '% off'
            : '₱' . number_format((float) $campaign->voucher_discount_value, 2) . ' off';

        return [
            'campaign_id' => (int) $campaign->id,
            'campaign_name' => (string) $campaign->name,
            'code' => strtoupper((string) $campaign->voucher_code),
            'value' => $value,
            'minimum' => (float) $campaign->voucher_minimum_spend,
            'maximum_discount' => $campaign->voucher_maximum_discount !== null
                ? (float) $campaign->voucher_maximum_discount
                : null,
            'target_scope' => (string) $campaign->target_scope,
            'starts_at' => $campaign->starts_at?->toIso8601String(),
            'ends_at' => $campaign->ends_at?->toIso8601String(),
            'starts_at_pht' => $campaign->startsAtManila()?->format('M d, Y h:i A'),
            'ends_at_pht' => $campaign->endsAtManila()?->format('M d, Y h:i A'),
            'timezone' => PlatformCampaign::TIMEZONE,
        ];
    }
}
