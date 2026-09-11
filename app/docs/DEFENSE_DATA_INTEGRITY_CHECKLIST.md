# SARI Thesis Defense — Finance Data Integrity Checklist

Run this checklist before presenting finance numbers to a panel.

## Must be true before the demo

- All finance migrations are `Ran` in `php artisan migrate:status`.
- `commission_rates` contains the intended active SARI policy rate.
- Legacy commission backfill was reviewed with `--dry-run` before writing rows.
- Finance backfill was reviewed with `--dry-run` before writing rows.
- No delivered order is silently treated as paid if the database says otherwise.
- Commission KPI totals are derived from `order_commissions`, not recomputed from the current rate.
- Seller revenue/report totals are derived from `seller_settlements` using `eligible_at`.
- Rider earnings are derived from `rider_earnings`, not a manually typed balance.
- The same rider earning cannot be included twice in the same payout request.
- Delivery fees are not mixed into merchandise GMV/commission calculations unless a future SARI policy explicitly changes the rule.
- BIR withholding remains `not_evaluated` until seller tax applicability and remittance facts are actually implemented/verified.
- Do not describe `payable`, `approved`, or an internal payment row as a bank/e-wallet transfer.

## Development-data warning

The uploaded project still contains legacy/development conveniences in other parts of the application (for example, temporary/demo login behavior). Before the final defense or production-like demonstration, identify and disable demo-only credentials/data that could create or present synthetic accounts. This finance patch deliberately does not invent missing payment, courier, tax, or remittance facts during backfill.

## Recommended reconciliation query checks

Use Tinker or your DB client to verify counts/totals for the actual demo data:

```php
App\Models\OrderCommission::count();
App\Models\OrderCommission::sum('net_commission');
App\Models\SellerSettlement::sum('merchandise_amount');
App\Models\SellerSettlement::sum('seller_net_amount');
App\Models\RiderEarning::sum('delivery_fee_amount');
App\Models\PaymentTransaction::where('status', 'paid')->sum('amount');
```

Those totals answer different questions. They are not expected to all be equal because merchandise, delivery, commission, and seller payable represent different financial components.
