# SARI Financial Flow — Thesis Defense Reference

Last reviewed: 2026-09-11

This document separates what SARI **actually records in its database** from what a real payment provider, bank, e-wallet, marketplace, or tax authority would do outside the application.

## 1. Source-backed real-world principles

### Philippine e-marketplace role

Republic Act No. 11967 (Internet Transactions Act of 2023) defines an e-marketplace as a digital platform that connects online consumers and merchants and may process payments, facilitate shipment/logistics, provide post-purchase support, and retain oversight over the transaction.

Official references:
- DTI: https://ecommerce.dti.gov.ph/internet-transactions-act-of-2023/
- Lawphil: https://lawphil.net/statutes/repacts/ra2023/ra_11967_2023.html

### Marketplace fees are commercial fees, not a government-fixed percentage

Shopee Philippines' current Terms of Service show a real marketplace pattern: seller fees apply to successful transactions; its commission fee is a percentage of a defined settlement price and the applicable rate can vary by product category. Shopee also deducts fees and applicable withholding before remitting the balance to the seller.

Industry reference:
- Shopee Philippines Terms of Service: https://help.shopee.ph/portal/4/article/77272

SARI therefore keeps its platform commission **configurable**. The application's commission rate is a SARI business rule, not a BIR- or BSP-prescribed rate.

### Payment holding / seller release is distinct from delivery

Shopee's Terms describe buyer money being held and later released to the seller after receipt/guarantee conditions, less applicable seller fees and withholding. SARI uses the same accounting distinction even though the thesis build does not operate an external escrow account:

- order delivered + paid = finance records become eligible;
- seller settlement `payable` = internal obligation recognized;
- seller settlement `paid` = should only be used when a real payout/remittance is confirmed.

SARI does **not** claim that a bank transfer happened just because an order is delivered.

### Philippine withholding tax is separate from platform commission

BIR Revenue Regulations No. 5-2025 prescribe a 0.5% creditable withholding tax on covered gross remittances by e-marketplace operators and digital financial services providers to sellers/merchants.

Official reference:
- BIR RR No. 5-2025: https://bir-cdn.bir.gov.ph/BIR/pdf/RR%205-2025.pdf

BIR RMC No. 8-2024 explains seller registration/COR requirements, threshold/declaration rules, Form 2307 obligations, and states that when an e-marketplace accepts or collects payment for goods and later remits it to the seller, the covered withholding is deducted before remittance. It also states that the last facility controlling the payment before final remittance is responsible for withholding under the rule.

Official reference:
- BIR RMC No. 8-2024: https://bir-cdn.bir.gov.ph/BIR/pdf/RMC%20No.%208-2024.pdf

Because the current SARI project does not yet contain a verified seller tax profile, sworn-declaration status, exemption status, or a real seller-remittance provider, SARI **does not auto-deduct withholding tax yet**. The settlement ledger keeps explicit withholding fields and marks them `not_evaluated` instead of inventing a tax deduction.

### Actual payment movement should use a regulated / appropriate payment provider

BSP states that Republic Act No. 11127 (National Payment Systems Act) provides the legal/regulatory framework for payment systems and empowers BSP to supervise and regulate payment systems.

Official reference:
- BSP Payments and Settlements: https://www.bsp.gov.ph/SitePages/PaymentsAndSettlements/PaymentsAndSettlements.aspx

For marketplace payment architecture, real providers expose split-payment capabilities. For example:
- PayMongo Split Payments: https://developers.paymongo.com/docs/seeds-payment-splitting
- Xendit Split Payments: https://docs.xendit.co/docs/split-payments

These are architectural references only. The current SARI thesis build is COD-first and does not claim live PayMongo/Xendit fund movement unless a real provider integration and webhook verification are added later.

## 2. SARI implemented finance flow

```text
BUYER PLACES ORDER
        |
        v
MarketplaceOrder
status = new
payment_status = pending
        |
        v
SELLER PREPARES ORDER
        |
        v
SARI LOGISTICS / RIDER FLOW
courier_accepted -> pickup -> in_transit -> arrived_buyer
        |
        v
RIDER COMPLETES DELIVERY
status = delivered
delivered_at = timestamp
COD payment_status = paid
        |
        v
FinancialFlowService
        |
        +--> PaymentTransaction
        |    Records the confirmed internal payment event.
        |    COD record is an internal collection confirmation, not a bank API receipt.
        |
        +--> OrderCommission
        |    eligible_amount = merchandise subtotal
        |    delivery fee excluded
        |    applied rate snapshotted permanently
        |
        +--> SellerSettlement
        |    merchandise amount
        |    - platform commission
        |    - withholding tax IF eventually verified/applicable
        |    = seller net payable
        |    status = payable, not automatically paid
        |
        +--> RiderEarning
             delivery fee amount
             status = available
```

## 3. Example transaction

Assume:

```text
Merchandise subtotal       PHP 1,000.00
Delivery fee               PHP    80.00
Buyer total                PHP 1,080.00
SARI commission rate       10.00%
```

The internal finance ledgers become:

```text
Payment collection         PHP 1,080.00
Platform commission        PHP   100.00
Seller gross merchandise   PHP 1,000.00
Withholding tax            PHP     0.00  (not evaluated in current build)
Seller net payable         PHP   900.00
Rider earning              PHP    80.00
```

The delivery fee is not included in the merchandise commission basis.

If SARI changes its rate to 12% tomorrow, the old order remains 10% because `order_commissions.rate_percent` is a transaction snapshot.

## 4. Rider payout flow

```text
Delivered order
   -> RiderEarning = available
   -> Rider requests payout
   -> exact available RiderEarning rows are linked to payout request
   -> those rows become reserved
   -> Admin approves request
   -> request remains approved until actual payout confirmation
   -> Admin marks paid
   -> linked RiderEarning rows become paid
```

If Admin rejects a payout request, the exact linked earnings return to `available`; the earned delivery fees are not deleted.

This prevents a rider from requesting the same delivery fee twice while a request is pending/approved.

## 5. Important accounting/status distinction

Never answer the panel as if these are the same:

- `delivered` = fulfillment status;
- `paid` on the order = buyer payment status;
- `earned` commission = SARI fee recognized in the internal ledger;
- `payable` seller settlement = SARI owes/recognizes the seller amount internally;
- `paid` payout/settlement = external remittance is confirmed.

## 6. What SARI can truthfully claim today

SARI can demonstrate:

- real database order values;
- real order status transitions;
- commission recognized only on delivered + paid orders;
- immutable historical commission rates;
- merchandise and delivery fee separation;
- seller payable calculation from transaction snapshots;
- rider earnings tied one-to-one to delivered orders;
- rider payout reservation so the same earning is not double-requested;
- audit trail for commission-rate changes and commission adjustments;
- CSV/report calculations sourced from finance ledgers rather than a hard-coded 10%.

## 7. What SARI must NOT claim yet

Until additional modules/integrations exist, do not say that SARI currently:

- holds buyer money in a regulated escrow/guarantee account;
- automatically transfers money to seller bank/e-wallet accounts;
- automatically transfers rider payouts through a bank/e-wallet API;
- automatically performs BIR withholding for every seller;
- automatically issues BIR Form 2307;
- verifies an external payment-provider transaction/reference for COD;
- processes real refunds/chargebacks through an external gateway.

Those can be presented as production extensions, not as already-working thesis features.

## 8. Suggested thesis-panel answers

**Q: Why is the commission rate 10%?**

A: "The percentage is a configurable SARI commercial policy, not a government-mandated rate. Real marketplaces also define seller fees commercially and may vary them by category/program. Our database snapshots the rate used by every completed transaction so changing the policy does not rewrite historical revenue."

**Q: When does SARI earn commission?**

A: "The system recognizes commission only when an order is both delivered and paid. For COD, delivery completion also confirms the buyer payment status."

**Q: Does the courier receive part of the seller commission?**

A: "No. Rider earnings are tracked from the delivery-fee component in a separate ledger. Platform commission is calculated from merchandise subtotal."

**Q: What happens when the commission rate changes?**

A: "The new rate applies prospectively. Each completed order stores its applied rate and amount, so historical orders remain unchanged."

**Q: Is 0.5% BIR withholding the SARI commission?**

A: "No. Platform commission is SARI's commercial fee. Creditable withholding tax is a statutory tax mechanism. We store them as separate fields and do not auto-apply withholding until seller tax applicability and actual remittance are verified."

**Q: Are payouts real bank transfers?**

A: "The thesis system currently maintains an internal payable/payout ledger. A record is not presented as an external transfer unless a payment provider or explicit payout confirmation supports it. Production deployment would integrate a regulated payment provider and reconcile provider transaction IDs/webhooks."

**Q: Why not simply recompute old orders using the current rate?**

A: "That would change historical financial results. The system instead stores an immutable per-order commission snapshot, which is auditable and reproducible."
