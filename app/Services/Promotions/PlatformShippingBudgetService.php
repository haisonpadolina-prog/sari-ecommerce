<?php

namespace App\Services\Promotions;

use App\Models\Catalog\SellerProduct;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\PlatformSetting;
use App\Models\Promotions\PlatformCampaign;
use App\Models\Promotions\PlatformShippingRedemption;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlatformShippingBudgetService
{
    /** Convert validated PHP money text to centavos without floating-point accounting. */
    public static function cents(string|int|float|null $value): int
    {
        $value = trim((string) ($value ?? '0'));
        if (!preg_match('/^\d{1,9}(?:\.\d{1,2})?$/D', $value)) {
            throw ValidationException::withMessages(['shipping_budget' => 'Use a positive peso amount with at most two decimal places.']);
        }
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }

    public function totals(PlatformCampaign $campaign): array
    {
        $reserved = (int) $campaign->shipping_reserved_cents;
        $used = (int) $campaign->shipping_used_cents;
        return ['allocated' => (int) $campaign->shipping_budget_cents, 'reserved' => $reserved,
            'used' => $used, 'available' => max(0, (int) $campaign->shipping_budget_cents - $reserved - $used)];
    }

    /** Preview only. All final checks are repeated under a campaign lock at checkout. */
    public function quote(string $code, array $lines, array $identity, string $address, int $feeCents, bool $lock = false, ?string $checkoutReference = null): array
    {
        $enabled = filter_var(PlatformSetting::valueOf('platform_campaigns_enabled', true), FILTER_VALIDATE_BOOLEAN);
        if (!$enabled) $this->fail('SARI campaigns are currently disabled.');
        $query = PlatformCampaign::query()->where('shipping_code', strtoupper(trim($code)));
        if ($lock) $query->lockForUpdate();
        $campaign = $query->first();
        if (!$campaign || !$campaign->shipping_enabled || !$campaign->isLive()) $this->fail('This shipping voucher is unavailable, paused, or outside its campaign dates.');
        $campaign->load([
            'products' => fn ($query) => $lock ? $query->lockForUpdate() : $query,
            'sellers' => fn ($query) => $lock ? $query->lockForUpdate() : $query,
            'categories' => fn ($query) => $lock ? $query->lockForUpdate() : $query,
        ]);
        $buyerColumn = !empty($identity['buyer_account_id']) ? 'buyer_account_id' : 'buyer_social_account_id';
        $buyerId = (int) ($identity[$buyerColumn] ?? 0);
        if ($buyerId <= 0) $this->fail('Sign in to a Buyer account to use a shipping voucher.');
        $uses = PlatformShippingRedemption::query()->where('platform_campaign_id', $campaign->id)
            ->where($buyerColumn, $buyerId)->whereIn('state', ['reserved', 'used'])
            ->when($checkoutReference, fn ($query) => $query->where('checkout_reference', '<>', $checkoutReference))
            ->when($lock, fn ($query) => $query->lockForUpdate())
            ->get(['checkout_reference'])->pluck('checkout_reference')->unique()->count();
        if ($uses >= (int) $campaign->shipping_per_buyer_limit) $this->fail('You reached the per-Buyer order limit for this shipping voucher.');
        $areas = $campaign->shipping_areas ?? [];
        // Match complete comma-separated address components, never arbitrary substrings.
        $addressParts = array_map(fn ($part) => mb_strtolower(trim($part)), explode(',', $address));
        if ($areas && !collect($areas)->contains(fn ($area) => in_array(mb_strtolower(trim($area)), $addressParts, true))) {
            $this->fail('This delivery address is outside the shipping voucher areas.');
        }
        $eligibleCents = 0;
        foreach ($lines as $line) {
            $product = $line['product'] ?? null;
            if ($product instanceof SellerProduct && app(PlatformCampaignService::class)->appliesToProduct($campaign, $product)) {
                $eligibleCents += self::cents(number_format(max(0, (float) $line['buyer_line_total']), 2, '.', ''));
            }
        }
        if ($eligibleCents <= 0 || $eligibleCents < (int) $campaign->shipping_minimum_cents) $this->fail('The eligible product subtotal does not meet this shipping voucher minimum.');
        $amount = min(max(0, $feeCents), (int) $campaign->shipping_cap_cents);
        if ($amount <= 0) $this->fail('There is no remaining shipping fee to discount.');
        if ($amount > $this->totals($campaign)['available']) $this->fail('This shipping voucher has insufficient remaining budget.');
        return ['campaign' => $campaign, 'amount_cents' => $amount];
    }

    /** Caller creates the order inside its checkout transaction; no independent commits. */
    public function applyToOrder(string $code, MarketplaceOrder $order, array $lines): void
    {
        if (trim($code) === '') return;
        if (!$order->exists || $order->status !== 'new') $this->fail('Shipping vouchers can only be reserved for a new checkout order.');
        if (DB::transactionLevel() < 1) throw new \LogicException('Shipping reservations require a checkout transaction.');
        if (PlatformShippingRedemption::query()->where('marketplace_order_id', $order->id)->exists()) return;
        $quote = $this->quote($code, $lines, [
            'buyer_account_id' => $order->buyer_account_id,
            'buyer_social_account_id' => $order->buyer_social_account_id,
        ], (string) $order->buyer_address, self::cents(number_format((float) $order->delivery_fee, 2, '.', '')), true, $order->checkout_reference);
        $campaign = $quote['campaign'];
        $amount = $quote['amount_cents'];
        PlatformShippingRedemption::create([
            'platform_campaign_id' => $campaign->id, 'marketplace_order_id' => $order->id,
            'checkout_reference' => $order->checkout_reference,
            'buyer_account_id' => $order->buyer_account_id, 'buyer_social_account_id' => $order->buyer_social_account_id,
            'amount_cents' => $amount, 'state' => 'reserved',
            'policy_snapshot' => ['code' => $campaign->shipping_code, 'funder' => 'platform',
                'campaign_name' => $campaign->name, 'cap_cents' => $campaign->shipping_cap_cents,
                'minimum_cents' => $campaign->shipping_minimum_cents, 'areas' => $campaign->shipping_areas,
                'cancellation_policy' => 'Release before delivery; delivered subsidy remains used.'],
        ]);
        $campaign->forceFill(['shipping_reserved_cents' => (int) $campaign->shipping_reserved_cents + $amount])->save();
        $discount = $amount / 100;
        $snapshot = $order->platform_campaign_snapshot ?? [];
        $snapshot['shipping'] = ['campaign_id' => $campaign->id, 'code' => $campaign->shipping_code, 'amount' => $discount, 'funder' => 'platform'];
        $order->forceFill([
            'platform_shipping_discount_amount' => $discount,
            'shipping_discount_amount' => round((float) $order->shipping_discount_amount + $discount, 2),
            'shipping_discount_funder' => (float) $order->shipping_discount_amount > 0 ? 'shared' : 'platform',
            'delivery_fee' => round((float) $order->delivery_fee - $discount, 2),
            'total' => round((float) $order->total - $discount, 2),
            'platform_campaign_snapshot' => $snapshot,
        ])->save();
    }

    /** Idempotent lifecycle: released reservations cannot be revived by reopening an order. */
    public function syncOrder(MarketplaceOrder $order): void
    {
        if ((float) $order->platform_shipping_discount_amount <= 0) return;
        DB::transaction(function () use ($order): void {
            $lockedOrder = MarketplaceOrder::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $record = PlatformShippingRedemption::query()->where('marketplace_order_id', $order->id)->first();
            if (!$record) return;
            $campaign = PlatformCampaign::query()->whereKey($record->platform_campaign_id)->lockForUpdate()->firstOrFail();
            $record = PlatformShippingRedemption::query()->whereKey($record->id)->lockForUpdate()->firstOrFail();
            if ($record->state !== 'reserved') return;
            if (!in_array($lockedOrder->status, ['delivered', 'cancelled'], true)) return;
            $amount = (int) $record->amount_cents;
            $campaign->forceFill([
                'shipping_reserved_cents' => max(0, (int) $campaign->shipping_reserved_cents - $amount),
                'shipping_used_cents' => (int) $campaign->shipping_used_cents + ($lockedOrder->status === 'delivered' ? $amount : 0),
            ])->save();
            if ($lockedOrder->status === 'delivered') $record->update(['state' => 'used', 'used_at' => now()]);
            else $record->update(['state' => 'released', 'released_at' => now()]);
        }, 3);
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['shipping_code' => $message]);
    }
}
