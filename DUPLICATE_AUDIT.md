# Duplicate-function audit

Inspected against the latest reconstructed SARI working baseline that already contains:
- Admin Platform Monitoring
- Security Center / risk flags / stuck-order detection
- Platform Settings governance
- Events & Promotions
- Buyer/Seller/Logistics/Rider account flows

## 1. Platform Announcements
No existing Admin-created, role-targeted announcement module was found.
Existing Buyer/Seller/Logistics/Rider notifications are workflow-specific and are not a substitute for Admin announcements.

Implementation: NEW module.
- `platform_announcements`
- `platform_announcement_reads`
- Admin create/edit/publish/pause/delete
- Buyer/Seller/Logistics/Rider targeted display
- per-account dismiss/read persistence
- PHT scheduling

## 2. Maintenance / Feature Controls
Existing functionality already found:
- Checkout Enabled
- Registrations Enabled
- Commerce Maintenance Mode
- Maintenance Message
- governed/scheduled Platform Settings versions and audit history

Implementation: EXTEND the existing Platform Settings page only.
No second maintenance page was created.

New switches:
- Seller Product Posting
- Platform Campaigns

Enforcement:
- Seller Add Product / new product save / new draft save is blocked when Seller Product Posting is disabled.
- Existing Seller product management remains available.
- Buyer-facing Admin campaign sales and platform vouchers stop applying when Platform Campaigns is disabled.
- Existing campaign records remain intact.

## 3. Delivery SLA Monitor
Existing Security Center already contains `stuckOrders()` and operational risk alerts.

Implementation: REUSE that source of truth.
No second SLA detector/service was created.

The existing Security service now exposes one SLA engine with:
- status-specific expected windows
- stage-start timestamps
- elapsed time
- breach / overdue calculation
- warning/high/critical state
- Seller, Logistics and Rider context

That data is shown as a new Delivery SLA tab inside the existing Platform Monitoring page.
Security Center critical/stuck-order alerts continue to consume the same SLA data.
