# SARI Financial Flow — Thesis Defense Reference

Last reviewed: 2026-10-24

This document describes the finance behavior currently implemented in SARI. It deliberately separates **order/payment status**, **physical COD cash custody**, **internal earnings**, **wallet availability**, and **actual external payout**. Those are different states and must not be presented as if they are the same.

For detailed implementation and references, also see `docs/SARI_COD_REMITTANCE_EARNINGS_PAYOUTS.md`.

## 1. Source-backed principles used by the design

### Philippine e-commerce context

Republic Act No. 11967 (Internet Transactions Act of 2023) and its DTI Implementing Rules and Regulations provide the Philippine e-commerce regulatory context for online marketplaces and emphasize trust between online merchants and consumers.

Official references:
- DTI ITA IRR: https://www.dti.gov.ph/sdm_downloads/implementing-rules-regulations-of-the-internet-transactions-act-of-2023
- Lawphil RA 11967: https://lawphil.net/statutes/repacts/ra2023/ra_11967_2023.html

### SARI's 10% commission is a business rule, not a tax rate

SARI's platform commission is configurable and is snapshotted per completed order. The current default is 10% of the eligible merchandise subtotal. Shipping is excluded from the commission basis.

This percentage is a SARI commercial policy. It is not a BIR-prescribed marketplace commission rate.

### Philippine withholding is separate from SARI commission

BIR Revenue Regulations No. 5-2025 provides a 0.5% creditable withholding-tax rate on covered gross remittances by electronic marketplace operators and digital financial services providers to sellers/merchants.

Official reference:
- BIR RR No. 5-2025: https://bir-cdn.bir.gov.ph/BIR/pdf/RR%205-2025.pdf

The current SARI build does **not** automatically deduct this withholding. The application does not yet have enough verified seller tax-applicability, declaration, exemption, and real remittance-provider information to apply the rule correctly. The seller settlement therefore keeps withholding fields explicit but currently marks them `not_evaluated` and PHP 0.00.

### External money movement should use appropriate payment rails/providers

The Bangko Sentral ng Pilipinas' National Retail Payment System framework emphasizes safe, efficient, and reliable retail payments. BSP materials also describe InstaPay as a real-time low-value EFT scheme and PESONet as a retail electronic-fund-transfer rail.

Official references:
- BSP NRPS: https://www.bsp.gov.ph/Pages/PAYMENTS%20AND%20SETTLEMENTS/National%20Retail%20Payment%20System/National-Retail-Payment-System.aspx?ID=1541
- BSP PESONet/InstaPay: https://www.bsp.gov.ph/Pages/PAYMENTS%20AND%20SETTLEMENTS/National%20Retail%20Payment%20System/Empowering-Every-Juan-and-Maria.aspx?ID=1537

Provider documentation shows how a production marketplace could later automate distribution/payouts. Examples:
- PayMongo Payment Splitting: https://developers.paymongo.com/docs/seeds-payment-splitting
- Xendit Philippines payout coverage: https://docs.xendit.co/docs/payouts-coverage-philippines
- Xendit Payout API: https://docs.xendit.co/docs/payouts-via-api

These provider links are architectural references only. The current SARI build does not claim an API payout happened unless a real provider integration is added and verified.

## 2. Current SARI COD money flow

```text
BUYER
  |
  | pays full COD amount
  v
FINAL-MILE RIDER
  |
  | temporary physical cash custody
  v
SELECTED LOGISTICS BRANCH
  |
  | scans Rider remittance QR + physically counts full cash
  | exact count clears Rider custody
  |
  | if this is a regular branch
  v
BRANCH TREASURY REMITTANCE
  |
  | one physical cash batch + one Branch Remittance QR
  v
MAIN BRANCH / CENTRAL TREASURY
  |
  | scans batch QR + physically counts cash
  |
  +---- mismatch ----> DISCREPANCY
  |                    wallets remain locked
  |
  +---- exact --------> CENTRAL RECONCILIATION
                         |
                         v
                    WALLET RELEASE
                         |
             +-----------+-----------+-----------+-----------+
             |           |           |           |           |
             v           v           v           v           v
           SELLER     PLATFORM    LOGISTICS   PICKUP      FINAL-MILE
                                             RIDER         RIDER
```

The Rider does **not** subtract personal earnings from COD cash. A regular branch also does not net off company earnings before sending cash to the Main Branch. Physical custody and internal earning ownership stay separate until central reconciliation.

If the Rider remits directly to the Main Branch, the extra Branch -> Main transfer is unnecessary because the cash is already in central custody.

## 3. Financial recognition at delivery

When an order is `delivered` and `payment_status = paid`, `FinancialFlowService` creates or reuses the relevant internal records:

```text
FinancialFlowService
  |
  +--> PaymentTransaction
  |      COD buyer collection snapshot
  |      cash_custody = rider_pending_remittance
  |
  +--> OrderCommission
  |      merchandise-only commission snapshot
  |
  +--> SellerSettlement
  |      seller merchandise net obligation
  |
  +--> RiderEarning (pickup_leg when applicable)
  |
  +--> RiderEarning (last_mile_leg when applicable)
  |
  +--> LogisticsEarning
         gross shipping - Rider shares = Logistics margin
```

Because the current SARI buyer payment flow is COD-only, these obligations are recognized but **wallet release waits for Main Branch central cash reconciliation** before becoming available.

## 4. Default SARI allocation model

For a Logistics-managed order, the current default shipping policy is:

- Pickup Rider: 25% of gross shipping revenue
- Final-mile Rider: 45% of gross shipping revenue
- Logistics: the remainder, currently 30%

The Rider percentages are configurable SARI business rules in `platform_settings`. Their combined value may not exceed 100%.

The platform commission remains independently configurable through the existing commission-rate ledger and currently defaults to 10% of eligible merchandise subtotal.

### Example

Assume:

```text
Merchandise subtotal             PHP 1,000.00
Gross shipping revenue           PHP   100.00
Buyer COD total                  PHP 1,100.00
Platform commission              10%
Pickup Rider shipping share      25%
Final-mile Rider shipping share  45%
```

Allocation after Main Branch central reconciliation:

```text
Seller wallet                    PHP   900.00
Platform wallet                  PHP   100.00
Pickup Rider wallet              PHP    25.00
Final-mile Rider wallet          PHP    45.00
Logistics wallet                 PHP    30.00
                                --------------
Total                            PHP 1,100.00
```

The accounting invariant is that the allocation must reconcile to the amount that funded it. Seller-funded free-shipping discounts are deducted from the Seller settlement so the shipping side can still be funded from the original courier fee.

## 5. Physical cash custody and remittance flow

### Rider -> selected branch

```text
Delivered COD parcel
   -> Buyer payment recorded
   -> Rider has physical cash custody
   -> Rider opens COD Remittance
   -> selects eligible delivered parcels
   -> selects the active Logistics branch for in-person turnover
   -> SARI computes the full expected COD cash automatically
   -> SARI creates a physical Rider cash batch + signed QR
   -> Rider personally brings full cash + QR to the branch
   -> selected branch scans QR and physically counts cash
      -> exact at regular branch: Rider custody cleared, branch custody begins, wallets still locked
      -> exact at Main Branch: centrally reconciled, wallets can release
      -> short/over: discrepancy, Rider custody unresolved, NO wallet release
```

### Regular branch -> Main Branch

```text
Verified Rider cash batches at regular branch
   -> branch selects eligible batches
   -> SARI computes exact aggregate physical cash
   -> creates one Branch Treasury batch + signed Branch Remittance QR
   -> physical cash is carried to Main Branch
   -> Main Branch scans QR and counts cash
      -> exact: central reconciliation + wallet release
      -> short/over: discrepancy + NO wallet release
```

Each Rider order can belong to only one Rider COD remittance batch, and each verified Rider batch can belong to only one Branch Treasury remittance batch. Wallet releases use unique idempotency keys so retrying a verified release does not duplicate credits.

The current SARI policy is **physical cash only** for both Rider -> Branch and Branch -> Main Branch remittance. GCash, Maya, bank transfer, InstaPay, PESONet, and other digital rails are not COD-custody remittance methods. Those channels are relevant only to later earnings payout/withdrawal after wallet release.

## 6. Wallet and withdrawal flow

Wallets exist for:

- SARI platform treasury
- Seller
- Logistics provider
- Rider

Each wallet separates:

- `available_balance` — withdrawable;
- `reserved_balance` — already committed to a payout request;
- `lifetime_credits` — released earnings received by the wallet;
- `lifetime_payouts` — externally confirmed payouts.

Withdrawal state machine:

```text
AVAILABLE BALANCE
      |
      | user requests withdrawal
      v
RESERVED BALANCE
      |
      v
PENDING PAYOUT
      |
      +---- reject ----> reserved returned to available
      |
      v
APPROVED
      |
      | Admin performs real external transfer/cash release
      | and enters its real reference
      v
PAID
```

SARI currently supports payout-request destinations labeled as:

- GCash
- Maya
- bank transfer
- manual cash release

The current implementation is intentionally **manual at the final external-transfer step**. An authorized treasury Admin performs the real transfer outside the Laravel application and records its real reference before marking it paid. This prevents the application from fabricating a successful GCash/bank payout.

## 7. Role earnings

### Seller

Seller earnings come from merchandise, not shipping:

```text
Eligible merchandise
- SARI platform commission
- verified withholding, if eventually applicable
- Seller-funded free-shipping discount
= Seller net settlement
```

After Main Branch central cash reconciliation, the net settlement is credited to the Seller wallet and may be requested for payout.

### SARI Platform / Admin Treasury

The platform earns the snapshotted platform commission, currently defaulting to 10% of eligible merchandise. After Main Branch central cash reconciliation, the platform commission is credited to the platform treasury wallet.

The Admin is the treasury operator; the wallet represents SARI/platform revenue, not an employee salary.

### Logistics

For Logistics-managed delivery:

```text
Gross shipping revenue
- pickup Rider share
- final-mile Rider share
= Logistics net shipping margin
```

The Logistics Manager can request withdrawal from the Logistics wallet. Hub Operators do not receive treasury/remittance permissions.

### Rider

A Rider can earn separately for the physical leg actually performed:

- `pickup_leg` — Seller to origin hub;
- `last_mile_leg` — destination hub to Buyer;
- `direct_delivery` — legacy/direct delivery when Logistics split does not apply.

The same Rider may have two earning rows for the same parcel if that Rider legitimately performed both pickup and last-mile legs. This is intentional and replaces the old one-order/one-Rider-earning model.

## 8. Status meanings that must not be mixed up

- `delivered` — parcel fulfillment state;
- order `paid` — Buyer payment has been collected/confirmed;
- `pending_remittance` — earning obligation exists but COD cash has not yet reconciled;
- remittance `verified` — Logistics confirmed the full expected COD amount;
- wallet `available_balance` — funds may be requested for withdrawal;
- payout `pending` — withdrawal requested and funds reserved;
- payout `approved` — treasury approval given, but real money is not yet claimed sent;
- payout `paid` — actual external transfer/cash release reference was recorded.

## 9. What SARI can truthfully demonstrate now

SARI can demonstrate:

- order-based commission snapshots;
- Seller settlement calculation;
- separate pickup and last-mile Rider earnings;
- Logistics shipping margin;
- COD cash custody and remittance batches;
- discrepancy blocking;
- idempotent wallet release;
- wallet available/reserved balances;
- payout reservation preventing double withdrawal;
- Admin payout approval/rejection;
- externally confirmed payout references;
- encrypted payout destination details;
- role-based access to finance/remittance functions.

## 10. What SARI must not claim yet

Do not claim that the current build automatically:

- sends GCash or Maya money;
- sends bank/PESONet/InstaPay payouts;
- operates a regulated escrow account;
- applies BIR withholding to every Seller;
- generates BIR Form 2307;
- verifies a payout-provider webhook;
- handles provider-side payout failure/reversal automatically.

Those require production agreements/credentials, provider APIs, verified webhooks, reconciliation procedures, and appropriate legal/tax review.

## 11. Suggested thesis-panel answers

**Q: Why does the Rider remit the full COD amount instead of keeping the Rider fee?**

A: "The Rider is treated as a temporary cash custodian, not the owner of the collected amount. Full remittance lets the system reconcile the Buyer collection exactly. Rider compensation is then released separately through the Rider wallet, which makes shortages, overages, and duplicate payouts auditable."

**Q: Why are there two Rider earnings for one parcel?**

A: "A Logistics-managed parcel has two different delivery services: Seller-to-hub pickup and destination-hub-to-Buyer last-mile delivery. SARI records the leg actually performed, so the pickup Rider and final-mile Rider can be compensated independently."

**Q: Why is SARI's commission 10%?**

A: "Ten percent is the current configurable SARI commercial policy, not a government-mandated rate. Each completed order stores the applied commission snapshot so later policy changes do not rewrite historical results."

**Q: Is the 0.5% BIR withholding the same as SARI's 10% commission?**

A: "No. The SARI commission is a commercial platform fee. Creditable withholding is a statutory tax mechanism. The project keeps them separate and does not auto-apply seller withholding until the required tax-applicability information and real remittance process are available."

**Q: Does clicking Approve mean the money was already sent?**

A: "No. Approval only authorizes the payout. SARI marks it paid only after the treasury operator completes the real external transfer or cash release and records the reference."

**Q: How can automated payouts be added later?**

A: "The wallet and payout-request model can be connected to an activated payout provider. Production integration would create provider payouts, verify signed webhooks, track processing/failed/reversed states, and reconcile provider reports before finalizing the internal payout."
