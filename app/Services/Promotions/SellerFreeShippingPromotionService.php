<?php

namespace App\Services\Promotions;

use App\Models\Promotions\SellerFreeShippingPromotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SellerFreeShippingPromotionService
{
    public const TIMEZONE = 'Asia/Manila';
    public const FUNDER = 'seller';

    /** @var array<string, array|null> */
    private array $offerCache = [];

    /**
     * Resolve the seller's current or next promotion for Buyer-facing messaging.
     * This method does not lock rows and must not be used as the final checkout authority.
     *
     * @return array<string, mixed>|null
     */
    public function offerForSeller(int $sellerId, float $sellerSubtotal = 0, ?float $originalShippingFee = null): ?array
    {
        if ($sellerId <= 0) {
            return null;
        }

        $cacheKey = implode(':', [
            $sellerId,
            number_format(max(0, $sellerSubtotal), 2, '.', ''),
            $originalShippingFee === null ? 'na' : number_format(max(0, $originalShippingFee), 2, '.', ''),
        ]);

        if (array_key_exists($cacheKey, $this->offerCache)) {
            return $this->offerCache[$cacheKey];
        }

        $now = now();

        $promotion = SellerFreeShippingPromotion::query()
            ->where('seller_account_id', $sellerId)
            ->where('status', SellerFreeShippingPromotion::STATUS_ACTIVE)
            ->where('ends_at', '>', $now)
            ->orderByRaw('CASE WHEN starts_at <= ? THEN 0 ELSE 1 END', [$now])
            ->orderBy('starts_at')
            ->orderByDesc('maximum_shipping_discount')
            ->first();

        return $this->offerCache[$cacheKey] = $promotion
            ? $this->quote($promotion, $sellerSubtotal, $originalShippingFee, $now)
            : null;
    }

    /**
     * Resolve a currently-live promotion for Buyer catalog/product-card messaging.
     *
     * Important: this does NOT decide checkout eligibility. It only answers:
     * "Does this seller currently have a live Free Shipping promotion?"
     *
     * @return array<string, mixed>|null
     */
    public function liveOfferForSeller(int $sellerId): ?array
    {
        if ($sellerId <= 0) {
            return null;
        }

        $cacheKey = 'live:' . $sellerId;

        if (array_key_exists($cacheKey, $this->offerCache)) {
            return $this->offerCache[$cacheKey];
        }

        $now = now();

        $promotion = SellerFreeShippingPromotion::query()
            ->where('seller_account_id', $sellerId)
            ->where('status', SellerFreeShippingPromotion::STATUS_ACTIVE)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->orderByDesc('maximum_shipping_discount')
            ->orderBy('id')
            ->first();

        if (!$promotion) {
            return $this->offerCache[$cacheKey] = null;
        }

        return $this->offerCache[$cacheKey] = $this->liveOfferPayload($promotion);
    }

    /**
     * Prefetch live catalog badges in batches. The original liveOfferForSeller()
     * remains the authority and still works unchanged for a single seller.
     * Only this service's in-request cache is populated; checkout is untouched.
     *
     * @param iterable<int|string> $sellerIds
     */
    public function primeLiveOffersForSellers(iterable $sellerIds): void
    {
        $uncached = [];
        foreach ($sellerIds as $sellerId) {
            $sellerId = (int) $sellerId;
            if ($sellerId > 0 && !array_key_exists('live:' . $sellerId, $this->offerCache)) {
                $uncached[$sellerId] = $sellerId;
            }
        }

        if ($uncached === []) {
            return;
        }

        $now = now();
        foreach (array_chunk(array_values($uncached), 200) as $batch) {
            $promotions = SellerFreeShippingPromotion::query()
                ->whereIn('seller_account_id', $batch)
                ->where('status', SellerFreeShippingPromotion::STATUS_ACTIVE)
                ->where('starts_at', '<=', $now)
                ->where('ends_at', '>', $now)
                ->orderByDesc('maximum_shipping_discount')
                ->orderBy('id')
                ->get()
                ->groupBy('seller_account_id');

            foreach ($batch as $sellerId) {
                $promotion = $promotions->get($sellerId)?->first();
                $this->offerCache['live:' . $sellerId] = $promotion
                    ? $this->liveOfferPayload($promotion)
                    : null;
            }
        }
    }

    /** The same payload for single-seller and bulk lookups. */
    private function liveOfferPayload(SellerFreeShippingPromotion $promotion): array
    {
        return [
            'promotion_id' => (int) $promotion->id,
            'active' => true,
            'minimum_order_amount' => round(max(0, (float) $promotion->minimum_order_amount), 2),
            'maximum_shipping_discount' => round(max(0, (float) $promotion->maximum_shipping_discount), 2),
            'starts_at' => $promotion->starts_at,
            'ends_at' => $promotion->ends_at,
            'starts_at_manila' => $promotion->startsAtManila(),
            'ends_at_manila' => $promotion->endsAtManila(),
            'funder' => self::FUNDER,
        ];
    }

    /**
     * Final backend-authoritative checkout quote. If $lock is true the selected
     * promotion row is locked inside the caller's transaction so a pause/end
     * cannot race the order creation path.
     *
     * @return array<string, mixed>
     */
    public function checkoutQuote(
        int $sellerId,
        float $sellerSubtotal,
        float $originalShippingFee,
        bool $lock = false
    ): array {
        $now = now();

        $query = SellerFreeShippingPromotion::query()
            ->where('seller_account_id', $sellerId)
            ->where('status', SellerFreeShippingPromotion::STATUS_ACTIVE)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->orderByDesc('maximum_shipping_discount')
            ->orderBy('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        $promotion = $query->first();

        if (!$promotion) {
            return $this->emptyQuote($sellerSubtotal, $originalShippingFee, 'No active Free Shipping promotion.');
        }

        return $this->quote($promotion, $sellerSubtotal, $originalShippingFee, $now);
    }

    /** @return array<string, mixed> */
    private function quote(
        SellerFreeShippingPromotion $promotion,
        float $sellerSubtotal,
        ?float $originalShippingFee,
        Carbon $at
    ): array {
        $sellerSubtotal = round(max(0, $sellerSubtotal), 2);
        $originalShippingFee = $originalShippingFee === null
            ? null
            : round(max(0, $originalShippingFee), 2);
        $minimum = round(max(0, (float) $promotion->minimum_order_amount), 2);
        $maximumDiscount = round(max(0, (float) $promotion->maximum_shipping_discount), 2);
        $live = $promotion->isLiveAt($at);
        $eligible = $live && $sellerSubtotal >= $minimum;
        $discount = ($eligible && $originalShippingFee !== null)
            ? round(min($originalShippingFee, $maximumDiscount), 2)
            : 0.0;
        $buyerShippingFee = $originalShippingFee === null
            ? null
            : round(max(0, $originalShippingFee - $discount), 2);

        $reason = null;

        if ($promotion->status === SellerFreeShippingPromotion::STATUS_PAUSED) {
            $reason = 'Promotion is paused.';
        } elseif ($promotion->status === SellerFreeShippingPromotion::STATUS_ENDED || $promotion->ends_at?->lte($at)) {
            $reason = 'Promotion has ended.';
        } elseif ($promotion->starts_at?->gt($at)) {
            $reason = 'Promotion has not started yet.';
        } elseif ($sellerSubtotal < $minimum) {
            $reason = 'Minimum Seller subtotal has not been reached.';
        }

        return [
            'promotion' => $promotion,
            'promotion_id' => (int) $promotion->id,
            'status' => $promotion->displayStatus($at),
            'active' => $live,
            'eligible' => $eligible,
            'minimum_order_amount' => $minimum,
            'maximum_shipping_discount' => $maximumDiscount,
            'seller_subtotal' => $sellerSubtotal,
            'remaining_to_qualify' => round(max(0, $minimum - $sellerSubtotal), 2),
            'original_shipping_fee' => $originalShippingFee,
            'shipping_discount' => $discount,
            'buyer_shipping_fee' => $buyerShippingFee,
            'funder' => self::FUNDER,
            'starts_at' => $promotion->starts_at,
            'ends_at' => $promotion->ends_at,
            'starts_at_manila' => $promotion->startsAtManila(),
            'ends_at_manila' => $promotion->endsAtManila(),
            'reason' => $reason,
        ];
    }

    /** @return array<string, mixed> */
    private function emptyQuote(float $sellerSubtotal, float $originalShippingFee, string $reason): array
    {
        return [
            'promotion' => null,
            'promotion_id' => null,
            'status' => null,
            'active' => false,
            'eligible' => false,
            'minimum_order_amount' => 0.0,
            'maximum_shipping_discount' => 0.0,
            'seller_subtotal' => round(max(0, $sellerSubtotal), 2),
            'remaining_to_qualify' => 0.0,
            'original_shipping_fee' => round(max(0, $originalShippingFee), 2),
            'shipping_discount' => 0.0,
            'buyer_shipping_fee' => round(max(0, $originalShippingFee), 2),
            'funder' => self::FUNDER,
            'starts_at' => null,
            'ends_at' => null,
            'starts_at_manila' => null,
            'ends_at_manila' => null,
            'reason' => $reason,
        ];
    }
}
