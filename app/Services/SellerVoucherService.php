<?php
namespace App\Services;

use App\Models\SellerVoucher;
use Illuminate\Validation\ValidationException;

class SellerVoucherService
{
    public function resolveForSeller(int $sellerId, ?string $code, float $subtotal): ?array
    {
        $code = strtoupper(trim((string) $code));
        if ($code === '') {
            return null;
        }

        $voucher = SellerVoucher::query()
            ->where('seller_account_id', $sellerId)
            ->whereRaw('UPPER(code) = ?', [$code])
            ->lockForUpdate()
            ->first();

        if (!$voucher) {
            return null;
        }

        if (!$voucher->isUsableAt()) {
            throw ValidationException::withMessages([
                'voucher_code' => "Voucher {$code} is inactive, expired, or fully used.",
            ]);
        }

        if ($subtotal < (float) $voucher->minimum_spend) {
            throw ValidationException::withMessages([
                'voucher_code' => "Voucher {$code} requires a minimum Seller subtotal of ₱" . number_format((float) $voucher->minimum_spend, 2) . '.',
            ]);
        }

        $discount = $voucher->discount_type === 'percentage'
            ? round($subtotal * ((float) $voucher->discount_value / 100), 2)
            : round((float) $voucher->discount_value, 2);

        if ($voucher->maximum_discount !== null) {
            $discount = min($discount, (float) $voucher->maximum_discount);
        }

        $discount = round(min(max(0, $discount), $subtotal), 2);

        return ['voucher' => $voucher, 'discount' => $discount];
    }
}
