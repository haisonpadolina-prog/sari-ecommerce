# SARI Admin AI Assistant

Laravel 12 / Blade / Livewire integration using the existing server-side Gemini configuration. Copy complete files preserving paths. Existing routes/web.php, routes/console.php and resources/views/layouts/admin.blade.php are full files based on the supplied project snapshot; merge their integration additions if your local versions contain newer unrelated edits.

## Install/update

```sh
php artisan migrate
php artisan optimize:clear
```

Keep the existing GEMINI_API_KEY and shared model configuration; optional ADMIN_AI_GEMINI_MODEL overrides the Admin model. No new Composer/npm dependency. Never send your key in chat. ADMIN_AI_ENABLED controls access. ADMIN_AI_ALLOW_ACTIONS=true enables authorized previews/confirmation; the default is false. Optional ADMIN_AI_STALE_ORDER_HOURS defaults to 48; ADMIN_AI_RETENTION_DAYS defaults to 30. ADMIN_AI_ALERTS_ENABLED enables scheduled proactive alerts. Keep queue workers and Laravel scheduler running for queued notifications and maintenance. Hard-refresh after installing.

## Admin tools inside chat

Open the floating Buyer-style robot, then Admin tools. Direct tools and bulk previews do not call Gemini. Conversational requests require valid Gemini configuration. English/Tagalog/Taglish and Auto remain available through the language modal. Explicit selection translates displayed messages while preserving originals and action payloads.

| Tool | Actual behavior |
| --- | --- |
| Action Center | Permission-scoped counts and links for active pending/flagged products, pending seller registrations, open complaints, stale active orders and open returns. Links open existing review/monitoring screens. |
| Bulk Product Review | Active pending/flagged listings with photos and checkbox selection. Select 1–10, preview approval/rejection, then confirm separately. Rejection requires the admin's reason, 5–500 characters. Never automatically approves all pages. |
| Logistics Exceptions | stale=active order updated_at beyond threshold; hub=also received and not sorted; failed=latest explicit order event status failed/delivery_failed/failed_delivery. Does not infer failure from free text or claim live GPS/late SLA. |
| Complaints | Subject, description, status and existing admin note; reporter identifiers withheld. |
| Returns & Refunds | Order reference, buyer/seller IDs, reason/details, seller response, requested/approved amounts and recorded refund status; query id for up to 50 return events. |
| COD Remittances | PHP batch expected, submitted, confirmed, difference and unconfirmed amounts; courier/provider filtering for rider stage. stage=rider/branch/admin reads separate custody stages. Never adds overlapping stages as separate funds. |
| Seller Performance | Required Manila from/to dates; order cohort created in that range, current delivered/cancelled counts, delivered GMV including fees, cancellation rate, average created-to-delivered hours. Chart covers displayed sellers. |
| AI Action History | Proposal details, targets, owner/confirming admin ID, status, timestamps, result. Includes pending, expired, cancelled, executed and failed. Super admin sees all; other admins see their own. |
| AI Permissions | Own effective role/actions; super admin can list up to 100 admin identities and propose exact AI role assignments. |

Date inputs apply to COD and seller performance quick tools; defaults to the last 30 Manila calendar dates. Other filters and COD stage/logistics exception kind can be requested in chat. Lists page at 20 with direct next-page reads; CSV exports only displayed rows. Protected existing product cover images only, no generated promotional images. Original products/accounts/orders/registrations/commissions/journey/analytics/locations/audit/guide tools are retained subject to permissions.

## AI roles (do not change existing admin page permissions)

| Role | AI access |
| --- | --- |
| Existing super_admin | All reads and enabled actions, including assigning AI roles. |
| product_reviewer | Products, seller account lookup, scoped Action Center, own history, permissions/guide; approval/rejection only when actions enabled. |
| support | Operational accounts/orders/journey/locations/exceptions, complaints/returns, scoped Action Center and own history. Read-only. |
| finance | Commissions/analytics, COD, seller performance, returns, scoped Action Center and own history. Read-only. |
| observer | Guide, own permissions/history only. |
| Existing admin with null ai_role | Backward-compatible legacy read access, excluding admin role listing. No actions. Assign a scoped role to narrow access. |

Only super_admin may propose assign_ai_role for explicit non-super-admin IDs; role values are observer/product_reviewer/support/finance. Confirmation required. Super-admin privileges cannot be changed here. Role changes clear stored AI context for the target. No account is automatically promoted. Every read, prepare and confirmation endpoint enforces permission independently; model responses are not authorization.

Examples:

- “Ipakita ang Action Center at pending products.”
- “Preview approval for products #12 and #15.”
- “I-reject ang product #12 dahil mali ang product description.”
- “Show unsorted stale hub parcels.”
- “Check return request #8 and the seller response.”
- “Compare rider COD batches for logistics #2, November 1–12, 2026.”
- “Show Admin-stage COD receipts for the same dates.”
- “Graph seller performance from 2026-11-01 to 2026-11-12.”
- “Show failed AI actions.”
- “List admin AI roles.” Then: “Assign product_reviewer AI role to admin #4.”

## Boundaries and safeguards

General platform complaints have no order/seller/evidence relationship in the supplied schema; the AI cannot manufacture those links or per-seller complaint counts. Return records have no uploaded proof field. No automatic dispute decision, money transfer, refund execution, physical cash receipt confirmation or logistics assignment is added. Return/refund status is an internal workflow/ledger record, not proof that external payment moved. COD batches exclude unbatched collections; unconfirmed amount is a review figure, not a proven loss. Existing admin pages remain the authoritative screens for their workflows.

Product actions reload and lock all exact targets, reject archived/already-reviewed records and stale fingerprints, then execute existing admin controllers transactionally. Realtime jobs run after commit. A failed attempt retains no target writes; safe failure history persists separately. Proposal owner only can confirm/cancel; repeat successful confirmation is idempotent; preview expires after 10 minutes. Existing seller enforcement/message/announcement actions remain super-admin-only. Actions may send the existing workflow's notifications; the preview describes effects.

Tools use ORM/parameter binding, safe text rendering, CSRF, validated sessions, per-operation rate limits and private no-store responses. Conversation/proposal data are encrypted at rest and pruned by the configured maintenance job; this history is not an immutable regulatory audit archive. Historical proposals do not preserve every verbatim chat instruction. No secret values are included in this package.

## Validation limits

See VALIDATION.md. Local integration tests use an isolated SQLite test database and mocked Gemini responses. Live Gemini access, production database behavior, full browser appearance and workload performance remain to be verified in your installation.
