# SARI Branch, Hub-to-Hub, and Central Treasury Flow

Last reviewed: 2026-10-24

## Operating branch model

One Logistics provider owns multiple physical sorting facilities. After a Logistics Manager logs in, the Manager selects the physical **Operating Branch** for that session. Operational pages and parcel QR actions are scoped to that selected facility. The selected branch does not override parcel routing: parcel origin and destination hubs remain system-routed from the order's delivery-route snapshot.

One active facility is designated as the **Main Branch**. The Main Branch is both a normal operational hub and the central physical COD treasury.

## Parcel custody: same parcel QR across hubs

```text
Seller
  -> Pickup Rider scans parcel QR
  -> Origin Branch scans same QR for intake
  -> Parcel Sorting
  -> system-routed Hub Transfer manifest
  -> Origin Branch scans same QR for outbound loading
  -> Hub-to-Hub linehaul
  -> Destination Branch scans same QR for receipt
  -> Last-Mile assignment
  -> Destination Branch scans same QR for ready-for-delivery handoff
  -> Final-Mile Rider scans same QR for custody / out-for-delivery
  -> Final delivery scan + delivery proof
```

The physical parcel keeps one signed parcel identity. The authenticated role, selected branch, current parcel state, and transfer manifest determine what each scan is allowed to do.

## Cash custody: separate QR hierarchy

Parcel QR and cash-remittance QR codes are deliberately different.

```text
PARCEL QR
= physical parcel identity

RIDER REMITTANCE QR
= one Rider -> Branch COD cash batch

BRANCH REMITTANCE QR
= one Branch -> Main Branch aggregated cash batch
```

### Stage 1 — Buyer -> Rider

The final-mile Rider receives the full COD payment. The Rider is a temporary cash custodian and does not deduct Rider earnings from the cash.

### Stage 2 — Rider -> selected branch

The Rider prepares one physical COD remittance batch containing eligible delivered parcels. The selected branch scans the Rider Remittance QR and physically counts the cash.

- Exact regular-branch count: Rider liability is cleared; cash becomes branch custody; wallets remain pending.
- Exact Main-Branch count: cash is already in central custody and can be centrally reconciled immediately.
- Short/over: discrepancy; no central wallet release.

### Stage 3 — regular branch -> Main Branch

A regular branch groups one or more verified Rider cash batches into one Branch Treasury remittance batch. SARI calculates the exact amount and creates one signed Branch Remittance QR.

The Main Branch scans the Branch Remittance QR and counts the physical cash.

- Exact count: central reconciliation is complete and affected wallets are released idempotently.
- Short/over: discrepancy; affected wallets remain locked.

## Main Branch governance

- Exactly one active Main Branch is intended per Logistics provider.
- The first active facility is promoted automatically when a provider has no Main Branch.
- A Manager can explicitly set another active facility as Main Branch.
- The current Main Branch cannot be deactivated before another Main Branch is selected.
- The Main Branch cannot be switched while open physical Branch Treasury batches are still awaiting or disputing receipt at the current Main Branch.

## Money ownership after central reconciliation

Example: PHP 1,000 merchandise + PHP 100 shipping = PHP 1,100 COD.

```text
Seller            PHP 900
Platform/Admin    PHP 100
Pickup Rider      PHP  25
Final-Mile Rider  PHP  45
Logistics         PHP  30
                  -------
Total             PHP 1,100
```

The 10% platform commission and 25%/45% Rider shipping shares are SARI business rules, not statutory tax rates. Actual payout to GCash/Maya/bank/manual cash remains a separate wallet-withdrawal process after central reconciliation.

## External references used by the broader finance design

The custody chain above is a SARI operational/accounting design. External references are used for Philippine e-commerce, tax, and future payout context rather than as a claim that these sources prescribe SARI's exact internal branch workflow.

1. Bureau of Internal Revenue — Revenue Regulations No. 5-2025  
   https://bir-cdn.bir.gov.ph/BIR/pdf/RR%205-2025.pdf
2. Department of Trade and Industry — IRR of the Internet Transactions Act of 2023  
   https://www.dti.gov.ph/sdm_downloads/implementing-rules-regulations-of-the-internet-transactions-act-of-2023
3. Bangko Sentral ng Pilipinas — National Retail Payment System  
   https://www.bsp.gov.ph/Pages/PAYMENTS%20AND%20SETTLEMENTS/National%20Retail%20Payment%20System/National-Retail-Payment-System.aspx?ID=1541
4. BSP — PESONet and InstaPay information  
   https://www.bsp.gov.ph/Pages/PAYMENTS%20AND%20SETTLEMENTS/National%20Retail%20Payment%20System/Empowering-Every-Juan-and-Maria.aspx?ID=1537
5. PayMongo — Payment Splitting (future integration reference)  
   https://developers.paymongo.com/docs/seeds-payment-splitting
6. Xendit — Philippines payout coverage (future integration reference)  
   https://docs.xendit.co/docs/payouts-coverage-philippines
