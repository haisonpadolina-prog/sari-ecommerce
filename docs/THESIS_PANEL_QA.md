# SARI Finance — Thesis Panel Q&A

Last reviewed: 2026-09-11

Use these answers only for functionality that is actually installed and tested in the current SARI build. Do not claim external bank/e-wallet transfers, automatic BIR tax filing, or live payment-provider webhooks unless those integrations are added and demonstrated.

## Q1. Why does SARI charge a platform commission?

**Answer:** SARI treats platform commission as a commercial marketplace fee charged to the seller for a successfully completed marketplace transaction. The percentage is a configurable SARI business policy; it is not a percentage mandated by BIR, BSP, or DTI. Real marketplaces use their own seller-fee structures. Shopee Philippines' current Terms of Service, for example, separately describes transaction, processing, commission, shipping-related, and other seller fees, and its commission rate can vary by product category.

Reference:
- Shopee Philippines Terms of Service, Section 23 Seller Fees: https://help.shopee.ph/portal/4/article/77272

## Q2. Why is the SARI commission rate currently 10%?

**Answer:** The 10% is the project's configurable business-policy assumption, not an asserted Philippine industry standard. The defensible part of the system is that the rate is configurable, has an effective timeline, and is snapshotted per completed order. If SARI changes from 10% to 12%, old completed orders keep their original 10% record while new eligible transactions use the later rate.

## Q3. When is commission recognized?

**Answer:** In the current SARI COD workflow, commission is recorded only when the order is both `delivered` and `paid`. This prevents a newly placed, cancelled, unpaid, or still-in-transit order from being treated as earned platform revenue.

The design follows the marketplace principle that seller/platform fees are tied to successful/completed transactions rather than merely to order creation. Shopee's terms describe seller fees on successful transactions and deduction/remittance after successful completion.

Reference:
- https://help.shopee.ph/portal/4/article/77272

## Q4. What amount is used as the SARI commission basis?

**Answer:** The current SARI policy uses the delivered order's merchandise subtotal. Delivery fee is recorded separately for the rider/logistics ledger and is excluded from the platform commission basis. This is a SARI policy choice that keeps the merchandise marketplace fee separate from the delivery service fee.

The exact applied basis and rate are stored in `order_commissions`, so the calculation can be reproduced later.

## Q5. Does the rider receive part of the SARI commission?

**Answer:** No. In the implemented accounting model, the platform commission and rider earning are separate flows. SARI commission is based on merchandise. Rider earning is based on the order's delivery fee and is stored in `rider_earnings`.

## Q6. How does the seller amount work?

**Answer:** After a delivered-and-paid order is recognized, SARI creates an internal seller payable:

```text
Seller merchandise amount
- SARI platform commission
- verified/applicable withholding tax, if implemented
= Seller net payable
```

`payable` means the system recognizes an amount owed to the seller. It does **not** by itself prove that a bank or e-wallet transfer occurred.

## Q7. Is BIR withholding tax the same as SARI commission?

**Answer:** No. They are different.

- Platform commission is SARI's commercial fee.
- Creditable withholding tax is a statutory tax mechanism.

BIR Revenue Regulations No. 5-2025 prescribes one-half percent (0.5%) creditable withholding on covered gross remittances by e-marketplace operators and digital financial services providers to sellers/merchants. BIR RMC No. 8-2024 also discusses registration, the PHP 500,000 threshold/declaration rules, monitoring, deduction before remittance, and Form 2307.

References:
- BIR RR No. 5-2025: https://bir-cdn.bir.gov.ph/BIR/pdf/RR%205-2025.pdf
- BIR RMC No. 8-2024: https://bir-cdn.bir.gov.ph/BIR/pdf/RMC%20No.%208-2024.pdf

## Q8. Why does SARI not automatically deduct 0.5% withholding right now?

**Answer:** Because the current application does not yet have enough verified tax facts to determine the correct treatment for every seller. It does not yet maintain a complete verified seller tax profile, sworn-declaration status, exemption/lower-rate documentation, Form 2307 workflow, or real remittance-provider record. Automatically subtracting tax anyway would create fake financial data. The database therefore stores withholding fields but marks the current status as `not_evaluated` until the tax-compliance module is implemented.

## Q9. Is SARI already transferring real money to sellers and riders?

**Answer:** No, not from the current thesis build. SARI currently records auditable internal payment, commission, seller-payable, rider-earning, and rider-payout statuses. Actual electronic fund movement would require a real provider integration and provider confirmation/webhook or another verifiable remittance process.

For production architecture, payment providers such as PayMongo and Xendit document split-payment functionality for marketplaces and multiple recipients/accounts.

References:
- PayMongo Split Payments: https://developers.paymongo.com/docs/seeds-payment-splitting
- Xendit xenPlatform / Split Payments: https://docs.xendit.co/docs/xenplatform-overview and https://docs.xendit.co/docs/split-payments

## Q10. Why not build a custom wallet and say SARI moves the funds itself?

**Answer:** Because payment systems are regulated. The BSP states that the National Payment Systems Act provides the legal and regulatory framework for payment systems and empowers BSP to supervise and regulate them. A thesis application should distinguish an internal accounting ledger from an actual regulated payment service.

Reference:
- BSP Payments and Settlements: https://www.bsp.gov.ph/SitePages/PaymentsAndSettlements/PaymentsAndSettlements.aspx

## Q11. Is SARI legally an e-marketplace type of system?

**Answer:** The SARI architecture fits the e-marketplace concept described in Republic Act No. 11967 because it connects consumers and online merchants and coordinates marketplace functions including order fulfillment/logistics. RA 11967 describes an e-marketplace as a digital platform connecting consumers with merchants that may facilitate shipment/logistics, post-purchase support, and transaction oversight.

Reference:
- Republic Act No. 11967: https://lawphil.net/statutes/repacts/ra2023/ra_11967_2023.html

## Q12. How do you prevent duplicate commission or rider earnings?

**Answer:** The finance tables enforce one core commission, seller settlement, and rider earning per marketplace order, while service methods are idempotent. Re-running the completed-order finance flow returns/uses the existing ledger records instead of creating a second earning for the same order.

## Q13. How do rider payout requests avoid double counting?

**Answer:** A payout request links the exact `rider_earnings` rows included in that request. Those rows move from `available` to `reserved`. If the request is rejected, they return to `available`; if the payout is marked paid, they become `paid`. This is stronger than subtracting a free-form payout amount from a running total because every peso can be traced to specific delivered orders.

## Q14. What is the difference among delivered, paid, earned, payable, and paid payout?

**Answer:**

```text
delivered            = fulfillment completed
order payment paid   = buyer payment recorded as completed
commission earned    = SARI platform fee recognized internally
seller payable       = amount owed to seller recognized internally
rider available      = delivery earning recognized internally
payout paid          = payout was explicitly confirmed as paid in the SARI ledger
```

These states are intentionally separate so the system does not confuse operational completion with actual settlement.

## Q15. What happens if the panel asks for proof of one transaction?

**Answer:** Pick one delivered-and-paid order and show the linked records:

```text
marketplace_orders
    -> payment_transactions
    -> order_commissions
    -> seller_settlements
    -> rider_earnings
```

Then reproduce the amounts from the database. Example only:

```text
Merchandise      PHP 1,000.00
Delivery fee     PHP    80.00
Buyer total      PHP 1,080.00
Rate             10.00%
Commission       PHP   100.00
Seller payable   PHP   900.00 before any verified tax deduction
Rider earning    PHP    80.00
```

The demo should use the project's actual order record, not these example numbers.
