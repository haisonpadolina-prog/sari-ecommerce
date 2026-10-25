# SARI COD Remittance, Earnings, Wallets, and Payouts

## Purpose

This document defines the finance flow implemented for the SARI marketplace after parcel delivery. It separates four different concepts that must not be treated as the same thing:

1. **Cash custody** — who is physically holding COD cash.
2. **Earning allocation** — how much each SARI participant has earned internally.
3. **Wallet availability** — how much has passed reconciliation and may be requested for withdrawal.
4. **External payout** — an actual GCash, Maya, bank, InstaPay/PESONet, or cash transfer outside the Laravel application.

The SARI database is the accounting/audit source for items 1–3. It must never claim that item 4 occurred until an external transfer has actually been completed and its reference is recorded.

## Implemented money flow

### 1. Buyer pays COD to the final-mile Rider

When a COD order is delivered and the delivery proof succeeds, SARI records the order as delivered and paid. The Rider is temporarily the **cash custodian** of the full Buyer payment.

Example:

- Merchandise subtotal: PHP 1,000.00
- Shipping charge/value: PHP 100.00
- Buyer COD collected: PHP 1,100.00

The Rider must remit the full PHP 1,100.00. The Rider does **not** deduct personal earnings from the cash before remittance.

### 2. SARI recognizes internal obligations but keeps COD wallets locked

For a delivered COD order, SARI snapshots the obligations but uses `pending_remittance` for the shipping-side earnings. Seller/platform obligations also remain unreleased to wallets until physical cash reaches the Main Branch and is centrally reconciled.

Default SARI business policy used by this implementation:

- Platform commission: **10% of eligible merchandise subtotal**. The project's existing commission-rate ledger remains authoritative and can be changed by authorized Admin controls.
- Pickup Rider: **25% of gross shipping revenue** for Seller → origin hub work.
- Final-mile Rider: **45% of gross shipping revenue** for destination hub → Buyer work.
- Logistics: **remaining shipping revenue** after Rider shares, currently 30% with the default percentages.
- Seller: merchandise amount minus platform commission, any verified withholding amount, and Seller-funded free-shipping discount.

The Rider percentages are application business rules, not statutory rates. They are read from `platform_settings` keys `shipping_pickup_rider_percent` and `shipping_last_mile_rider_percent`; their combined value cannot exceed 100%.

### 3. Rider prepares a physical COD cash-remittance batch

The Rider COD Remittance page only offers orders that are:

- delivered,
- COD,
- paid,
- assigned to that Rider as final-mile delivery (or a supported direct-delivery order), and
- not already attached to a prior remittance batch.

**Rider COD remittance is physical cash only.** The Rider does not choose GCash, Maya, bank transfer, or any other digital remittance method. SARI computes the full expected cash directly from the selected COD parcels, so the Rider cannot reduce the batch by subtracting personal earnings.

The Rider selects the active Logistics hub where the cash will be physically turned over. SARI creates a remittance batch and a time-limited signed QR. The Rider brings both the full cash amount and the QR to the selected hub.

A prepared batch is not yet a release of earnings. The QR identifies the batch only; scanning it does **not** verify that money was received.

### 4. Selected branch physically counts Rider cash

The Logistics Manager operates under a selected branch. The Rider presents the signed Rider-remittance QR at the exact branch where the cash is physically turned over. Logistics scans the QR, physically counts the money, and enters the cash actually counted.

If the count is short or over, the Rider batch becomes `discrepancy`; Rider custody remains unresolved and no central wallet release occurs.

If the count is exact:

- the Rider batch becomes `verified`;
- Rider physical cash custody is cleared;
- the selected branch becomes the physical cash custodian;
- if that selected facility is **not** the Main Branch, wallets remain locked;
- if the selected facility **is** the Main Branch, central custody is already satisfied and the batch can be centrally reconciled immediately.

### 5. Regular branch remits aggregated cash to the Main Branch

Every Logistics provider has exactly one active **Main Branch** used as the central COD treasury. A regular branch groups one or more already-verified Rider cash batches into one physical Branch Treasury remittance batch.

The regular branch does not manually edit the required amount. SARI computes it from the verified Rider cash batches included in the transfer. The system creates one signed **Branch Remittance QR** for the entire cash turnover.

The physical process is:

`Rider -> Branch -> Main Branch`

The sending branch presents the Branch Remittance QR together with the full physical cash. The Main Branch scans the QR and physically counts the money. Scanning identifies the batch only; it does not prove that money was received.

If the Main Branch cash count is short or over, the Branch Treasury batch becomes `discrepancy` and the affected wallets remain locked. If the count exactly matches the system-calculated amount, the batch becomes `verified`, the included Rider batches are marked centrally reconciled, and SARI may release the corresponding wallets.

Historical Rider batches that were already verified under the older single-stage design are marked as already centrally reconciled during migration so they cannot re-enter the new Branch -> Main queue.

### 6. Wallet release after central reconciliation

For a centrally reconciled cash batch, SARI credits wallets using idempotency keys so retrying the release cannot duplicate the money.

For the PHP 1,100.00 example with PHP 100 shipping:

| Participant | Internal allocation |
| --- | ---: |
| Seller | PHP 900.00 |
| SARI Platform | PHP 100.00 |
| Pickup Rider | PHP 25.00 |
| Final-mile Rider | PHP 45.00 |
| Logistics | PHP 30.00 |
| **Total** | **PHP 1,100.00** |

This equality is a core accounting invariant: the system must not create or lose money in the allocation.

## Wallet model

Each wallet stores:

- `available_balance` — may be requested for payout;
- `reserved_balance` — already committed to pending/approved payout requests;
- `lifetime_credits` — total amount released into the wallet;
- `lifetime_payouts` — amount confirmed as externally paid.

Every wallet movement creates a `finance_wallet_entries` row with a unique idempotency key and post-transaction balances.

Wallet owners currently are:

- `platform:1` — SARI platform treasury;
- `seller:{seller_account_id}`;
- `logistics:{logistics_account_id}`;
- `courier:{courier_account_id}`.

## Important separation: COD remittance vs. earnings payout

These are intentionally different flows:

- **Rider COD remittance:** Buyer cash physically held by the final-mile Rider -> full physical cash turnover at the selected Logistics branch. No GCash/Maya/bank option is allowed.
- **Branch Treasury remittance:** verified Rider cash held by a regular branch -> full physical cash turnover to the provider's Main Branch. This is also physical cash only and uses a separate Branch Remittance QR.
- **Earnings payout/withdrawal:** money already released to a Seller/Rider/Logistics/Platform wallet -> may later be paid through an approved payout method such as GCash, Maya, bank transfer, or manual cash release.

A payout method must never be presented as a Rider COD-remittance method.

## Withdrawal and conversion to real money

### Implemented now

Seller, Logistics Manager, Rider, and authorized Platform Admin can create a withdrawal request by choosing:

- GCash,
- Maya,
- bank transfer, or
- manual cash release.

On request:

`available_balance -> reserved_balance`

This prevents the same money from being requested twice.

Admin then follows this state machine:

`pending -> approved -> paid`

or:

`pending/approved -> rejected`

A rejection returns reserved money to available balance. `paid` requires a real external transfer/cash reference; then the reserved amount is removed and `lifetime_payouts` increases.

Destination details are encrypted at rest using Laravel's encrypted cast; the user-facing queue stores a masked destination label separately.

### Not faked by this implementation

The Laravel application does **not** automatically send real money to GCash, Maya, or a bank in this phase. Doing that safely requires a contracted/activated payout provider, production credentials, provider-side beneficiary rules, webhook verification, failure/reversal handling, and reconciliation.

The current workflow is therefore provider-ready but deliberately manual at the final external-transfer step. An Admin makes the real transfer using an approved business payout channel and records the provider/reference number before marking the request paid.

## Tax and legal note

SARI's 10% platform commission is a marketplace business rule and is **not** the Philippine withholding-tax rate.

BIR Revenue Regulations No. 5-2025 states a **0.5% creditable withholding tax** on covered gross remittances by electronic marketplace operators and digital financial services providers to sellers/merchants. This project intentionally keeps seller withholding at `not_evaluated` / PHP 0.00 until SARI has enough verified seller tax profile, applicability, declaration/threshold/exemption information, and actual remittance operations to apply the rule correctly. Do not silently equate the 10% SARI commission with BIR withholding.

## External references used for the design

### Philippine tax and e-commerce context

1. **Bureau of Internal Revenue — Revenue Regulations No. 5-2025**  
   https://bir-cdn.bir.gov.ph/BIR/pdf/RR%205-2025.pdf  
   Used for the current 0.5% creditable withholding-tax reference on covered e-marketplace/DFSP gross remittances. The application does not automatically apply this tax yet.

2. **Department of Trade and Industry — Implementing Rules and Regulations of the Internet Transactions Act of 2023 (RA 11967)**  
   https://www.dti.gov.ph/sdm_downloads/implementing-rules-regulations-of-the-internet-transactions-act-of-2023  
   Used as Philippine e-commerce regulatory context, particularly the need for a trustworthy environment for online merchants and consumers.

### Philippine payout rails

3. **Bangko Sentral ng Pilipinas — National Retail Payment System (NRPS)**  
   https://www.bsp.gov.ph/Pages/PAYMENTS%20AND%20SETTLEMENTS/National%20Retail%20Payment%20System/National-Retail-Payment-System.aspx?ID=1541  
   Used as the official framework reference for safe, efficient, reliable electronic retail payments in the Philippines.

4. **Bangko Sentral ng Pilipinas — PESONet and InstaPay information**  
   https://www.bsp.gov.ph/Pages/PAYMENTS%20AND%20SETTLEMENTS/National%20Retail%20Payment%20System/Empowering-Every-Juan-and-Maria.aspx?ID=1537  
   Used to distinguish PESONet batch EFT from InstaPay real-time low-value EFT.

### Possible future API integrations (not enabled by this patch)

5. **PayMongo — Payment Splitting**  
   https://developers.paymongo.com/docs/seeds-payment-splitting  
   PayMongo documents split-payment support for marketplaces/service providers and states that merchant relationship activation/configuration is required. This is a future option for automated distribution; it is not called by the current code.

6. **Xendit — Philippines Payout Coverage**  
   https://docs.xendit.co/docs/payouts-coverage-philippines  
   Xendit's current Philippines documentation lists PHP payout rails including local InstaPay and PESONet. This is a future option for automated payouts; it is not called by the current code.

## Security and integrity controls implemented

- Wallet ownership comes from authenticated server-side session identity, not submitted user IDs.
- Logistics treasury/reconciliation routes are Manager-only; Hub Operators are denied.
- Every state-changing form remains protected by Laravel web/CSRF middleware.
- Wallet releases use database transactions and unique idempotency keys.
- A remittance order can belong to only one remittance batch.
- Payout reservation checks available balance under row lock.
- A payout cannot be marked paid unless it is approved and has an external reference.
- Full payout destination details are encrypted in the database.
- Payout queue exposes a masked destination label for normal display.

## Important production recommendations

Before automatic production payouts are activated:

1. Complete legal/tax review for SARI's actual operator structure and seller tax profiles.
2. Contract and activate a regulated payout/payment provider suitable for the business.
3. Store provider secrets only in environment/secret-management facilities, never in source control.
4. Verify provider webhooks cryptographically and make callbacks idempotent.
5. Add provider payout statuses such as `processing`, `failed`, `reversed`, and `returned` instead of assuming an API request means money arrived.
6. Use maker-checker approval for treasury payouts in a multi-admin production deployment.
7. Reconcile provider settlement reports against `finance_wallet_entries` and `finance_payout_requests` daily.

## Main implementation files

- `app/Services/Finance/FinancialFlowService.php`
- `app/Services/Finance/ShippingEarningService.php`
- `app/Services/Finance/CodRemittanceService.php`
- `app/Services/Finance/BranchTreasuryService.php`
- `app/Services/Finance/FinanceReleaseService.php`
- `app/Services/Finance/WalletService.php`
- `app/Services/Finance/PayoutService.php`
- `app/Models/Finance/FinanceWallet.php`
- `app/Models/Finance/FinanceWalletEntry.php`
- `app/Models/Finance/FinancePayoutRequest.php`
- `app/Models/Finance/CodRemittanceBatch.php`
- `app/Models/Finance/BranchRemittanceBatch.php`
- `app/Models/Finance/BranchRemittanceItem.php`
- `app/Models/Finance/CodRemittanceItem.php`
- `app/Models/Finance/LogisticsEarning.php`
- `routes/finance.php`
- `tests/Feature/Finance/RemittanceWalletPayoutFlowTest.php`
