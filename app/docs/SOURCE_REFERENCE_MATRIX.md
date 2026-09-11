# SARI Finance Source Reference Matrix

| System design decision | External reference | How SARI applies it |
|---|---|---|
| E-marketplace coordinates seller, buyer, payment/logistics/post-purchase support | RA 11967 / DTI Internet Transactions Act | Order, seller, logistics and financial records are separate but linked. |
| Marketplace commission is a seller/platform commercial fee on successful transactions | Shopee PH Terms of Service, Seller Fees | Commission is configurable and recognized on completed paid orders; SARI does not claim the percentage is government-set. |
| Seller release/remittance is not identical to delivery status | Shopee Guarantee / Seller Fee settlement clauses | `SellerSettlement.status=payable` is an internal liability, not proof of money transfer. |
| CWT is separate from platform fee | BIR RR 5-2025; RMC 8-2024 | Separate withholding fields; auto-withholding disabled until seller tax/remittance conditions are actually known. |
| Platform payment systems are regulated | BSP NPSA / Payments and Settlements | SARI does not pretend its MySQL ledger is a regulated wallet or bank. |
| Marketplace split payment APIs exist | PayMongo Split Payments; Xendit Split Payments | Database stores provider-ready payment/settlement references without claiming an integration that is not configured. |
| Logistics earning is distinct from merchandise settlement | Shopee logistics/fees model + SARI order fields | Rider earning comes from delivery fee; seller settlement comes from merchandise. |

References:
- https://ecommerce.dti.gov.ph/internet-transactions-act-of-2023/
- https://lawphil.net/statutes/repacts/ra2023/ra_11967_2023.html
- https://help.shopee.ph/portal/4/article/77272
- https://bir-cdn.bir.gov.ph/BIR/pdf/RR%205-2025.pdf
- https://bir-cdn.bir.gov.ph/BIR/pdf/RMC%20No.%208-2024.pdf
- https://www.bsp.gov.ph/SitePages/PaymentsAndSettlements/PaymentsAndSettlements.aspx
- https://developers.paymongo.com/docs/seeds-payment-splitting
- https://docs.xendit.co/docs/split-payments
