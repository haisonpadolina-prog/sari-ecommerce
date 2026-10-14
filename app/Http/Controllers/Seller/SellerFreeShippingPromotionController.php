<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Models\Promotions\SellerFreeShippingPromotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellerFreeShippingPromotionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);
        $data = $this->validatedPromotion($request);

        DB::transaction(function () use ($seller, $data): void {
            $lockedSeller = SellerAccount::query()
                ->whereKey($seller->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertNoScheduleOverlap(
                $lockedSeller,
                $data['starts_at'],
                $data['ends_at']
            );

            SellerFreeShippingPromotion::query()->create([
                'seller_account_id' => $lockedSeller->id,
                'minimum_order_amount' => $data['minimum_order_amount'],
                'maximum_shipping_discount' => $data['maximum_shipping_discount'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'status' => SellerFreeShippingPromotion::STATUS_ACTIVE,
            ]);
        });

        return back()->with('success', 'Free Shipping promotion created.');
    }

    public function update(
        Request $request,
        SellerFreeShippingPromotion $promotion
    ): RedirectResponse {
        $seller = $this->seller($request);
        $this->ensureOwnership($seller, $promotion);

        abort_if(
            $promotion->status === SellerFreeShippingPromotion::STATUS_ENDED,
            422,
            'Ended promotions cannot be edited.'
        );

        $data = $this->validatedPromotion($request);

        DB::transaction(function () use ($seller, $promotion, $data): void {
            $lockedSeller = SellerAccount::query()
                ->whereKey($seller->id)
                ->lockForUpdate()
                ->firstOrFail();

            $locked = SellerFreeShippingPromotion::query()
                ->whereKey($promotion->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureOwnership($lockedSeller, $locked);

            if ($locked->status === SellerFreeShippingPromotion::STATUS_ACTIVE) {
                $this->assertNoScheduleOverlap(
                    $lockedSeller,
                    $data['starts_at'],
                    $data['ends_at'],
                    (int) $locked->id
                );
            }

            $locked->forceFill([
                'minimum_order_amount' => $data['minimum_order_amount'],
                'maximum_shipping_discount' => $data['maximum_shipping_discount'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
            ])->save();
        });

        return back()->with('success', 'Free Shipping promotion updated.');
    }

    public function pause(Request $request, SellerFreeShippingPromotion $promotion): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->ensureOwnership($seller, $promotion);

        abort_if(
            $promotion->status === SellerFreeShippingPromotion::STATUS_ENDED,
            422,
            'Ended promotions cannot be paused.'
        );

        if ($promotion->ends_at?->lte(now())) {
            $promotion->forceFill([
                'status' => SellerFreeShippingPromotion::STATUS_ENDED,
                'ended_at' => $promotion->ended_at ?: now(),
            ])->save();

            throw ValidationException::withMessages([
                'promotion' => 'This promotion already expired and cannot be paused.',
            ]);
        }

        $promotion->forceFill([
            'status' => SellerFreeShippingPromotion::STATUS_PAUSED,
            'paused_at' => now(),
        ])->save();

        return back()->with('success', 'Free Shipping promotion paused.');
    }

    public function resume(Request $request, SellerFreeShippingPromotion $promotion): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->ensureOwnership($seller, $promotion);

        abort_if(
            $promotion->status === SellerFreeShippingPromotion::STATUS_ENDED,
            422,
            'Ended promotions cannot be resumed.'
        );

        if ($promotion->ends_at?->lte(now())) {
            $promotion->forceFill([
                'status' => SellerFreeShippingPromotion::STATUS_ENDED,
                'ended_at' => $promotion->ended_at ?: now(),
            ])->save();

            throw ValidationException::withMessages([
                'promotion' => 'This promotion already expired and cannot be resumed.',
            ]);
        }

        DB::transaction(function () use ($seller, $promotion): void {
            $lockedSeller = SellerAccount::query()
                ->whereKey($seller->id)
                ->lockForUpdate()
                ->firstOrFail();

            $locked = SellerFreeShippingPromotion::query()
                ->whereKey($promotion->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureOwnership($lockedSeller, $locked);

            $this->assertNoScheduleOverlap(
                $lockedSeller,
                $locked->starts_at,
                $locked->ends_at,
                (int) $locked->id
            );

            $locked->forceFill([
                'status' => SellerFreeShippingPromotion::STATUS_ACTIVE,
                'paused_at' => null,
            ])->save();
        });

        return back()->with('success', 'Free Shipping promotion resumed.');
    }

    public function end(Request $request, SellerFreeShippingPromotion $promotion): RedirectResponse
    {
        $seller = $this->seller($request);
        $this->ensureOwnership($seller, $promotion);

        $promotion->forceFill([
            'status' => SellerFreeShippingPromotion::STATUS_ENDED,
            'ended_at' => now(),
        ])->save();

        return back()->with('success', 'Free Shipping promotion ended.');
    }

    /** @return array{minimum_order_amount: float, maximum_shipping_discount: float, starts_at: Carbon, ends_at: Carbon} */
    private function validatedPromotion(Request $request): array
    {
        $validated = $request->validate([
            'minimum_order_amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'maximum_shipping_discount' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'starts_at' => ['required', 'date_format:Y-m-d\\TH:i'],
            'ends_at' => ['required', 'date_format:Y-m-d\\TH:i'],
        ]);

        $startsAt = Carbon::createFromFormat(
            'Y-m-d\\TH:i',
            $validated['starts_at'],
            SellerFreeShippingPromotion::TIMEZONE
        )->utc();

        $endsAt = Carbon::createFromFormat(
            'Y-m-d\\TH:i',
            $validated['ends_at'],
            SellerFreeShippingPromotion::TIMEZONE
        )->utc();

        if ($endsAt->lte($startsAt)) {
            throw ValidationException::withMessages([
                'ends_at' => 'Promotion end date and time must be after the start date and time.',
            ]);
        }

        if ($endsAt->lte(now())) {
            throw ValidationException::withMessages([
                'ends_at' => 'Promotion end date and time must still be in the future.',
            ]);
        }

        return [
            'minimum_order_amount' => round((float) $validated['minimum_order_amount'], 2),
            'maximum_shipping_discount' => round((float) $validated['maximum_shipping_discount'], 2),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }

    private function assertNoScheduleOverlap(
        SellerAccount $seller,
        Carbon $startsAt,
        Carbon $endsAt,
        ?int $ignorePromotionId = null
    ): void {
        $overlap = SellerFreeShippingPromotion::query()
            ->where('seller_account_id', $seller->id)
            ->where('status', SellerFreeShippingPromotion::STATUS_ACTIVE)
            ->when($ignorePromotionId, fn ($query) => $query->where('id', '!=', $ignorePromotionId))
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'starts_at' => 'This schedule overlaps another active or scheduled Free Shipping promotion.',
            ]);
        }
    }

    private function ensureOwnership(
        SellerAccount $seller,
        SellerFreeShippingPromotion $promotion
    ): void {
        abort_unless(
            (int) $promotion->seller_account_id === (int) $seller->id,
            404
        );
    }

    private function seller(Request $request): SellerAccount
    {
        $seller = $request->attributes->get('sellerAccount');

        if ($seller instanceof SellerAccount) {
            return $seller;
        }

        return SellerAccount::query()->findOrFail(
            (int) $request->session()->get('seller_account_id')
        );
    }
}
