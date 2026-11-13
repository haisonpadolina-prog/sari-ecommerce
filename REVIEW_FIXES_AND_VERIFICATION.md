# SARI corrected mobile backend patch

Compared against: `LARAVEL-E-COMMERCE.zip` supplied in this conversation.
Original mobile patch: `SARI_Laravel_All_Mobile_API_Backend_2026-11-12.zip`.

This is a complete replacement for the original consolidated mobile patch, not a second partial patch. It includes its original routes/controllers/services/migration plus the corrections below. The original project ZIP was not modified.

## Corrections

| Finding | Corrected implementation |
|---|---|
| Admin AI hourly schedule lost | `routes/console.php` retains `admin-ai:maintenance` alongside both mobile schedules. |
| Only earliest 100 devices processed | `DispatchMobilePush::handle()` uses ID-based chunk traversal through ALL eligible devices. `--limit` controls batch size, not total devices. |
| Pending pushes excluded by newest-event limits | `DispatchMobilePush::pending()` filters device-specific receipt keys in SQL before applying each event limit. `eventsFor()` processes oldest pending events first. The key expression supports MySQL and SQLite. |
| Registration resets pending-event cutoff | `MobileDeviceController::register()` inserts first-registration time once and updates the existing subscription without changing that cutoff or deleting its receipts. Omitted push preference preserves the existing setting. Device token ownership transfer remains transactional. |
| Background push bypasses account-access checks | `MobilePushAccess::allows()` verifies Sanctum token ownership, account role, ability, explicit and global expiration, account status, email ban and soft-deleted account resolution. Linked Rider providers must also be active and unbanned. Inaccessible subscriptions are removed, and access is rechecked before each send. |
| Rider polling returns stale materialized feed | `MobileUpdatesController::index()` calls Rider inbox synchronization before querying notification counts, independently of Firebase configuration. |
| Logistics message counters always zero | `MobileUpdatesController::index()` reads actual incoming Rider messages scoped to the authenticated Logistics account. Dispatcher also includes these provider-scoped messages for background pushes. |
| Receipt pruning could replay old events after preserving registration time | Dispatcher bounds push eligibility to the later of original registration and the last 30 days, matching receipt retention. |

FCM failures do not create delivery receipts. The command returns a failure exit code when deliveries/scans fail; logs omit tokens and credentials. Rider push selection also excludes archived notifications.

## Executed verification

- Read all 28 files in the original mobile patch and relevant baseline dependencies; compared each replaced PHP file with the uploaded full project.
- Rechecked corrected `App` imports and all 143 controller action references in `routes/api.php`; none unresolved in the combined source.
- Confirmed the Admin AI schedule and both mobile schedules exist.
- Executed isolated SQLite relational simulations: traversal across 201 devices including deletion during traversal; a 41-event pending backlog draining over multiple batches; receipt/device and recipient scoping; cutoff/preference preservation; explicit push re-enabling; and old-event exclusion after retention cleanup.
- Confirmed the original mobile migration is byte-for-byte unchanged.

These checks are not execution of Laravel/PHP code.

## Included regression suite

`tests/Feature/MobileBackendPatchTest.php` contains 16 cases:

1. Process all device batches even while deleting revoked subscriptions.
2. Filter receipts before event limits and scope receipts to each device.
3. Preserve registration cutoff, receipts and disabled push preference during refresh.
4. Honor explicit push re-enabling.
5. Remove the old subscription when the same FCM token changes owners.
6. Reject anonymous device registration.
7. Preserve original and new scheduler commands.
8. Permit an active owner with the correct mobile token.
9. Reject banned, inactive and soft-deleted accounts.
10. Reject revoked, expired, wrong-owner and wrong-ability tokens.
11. Enforce global Sanctum expiration.
12. Stop Rider pushes when the linked Logistics provider is inaccessible.
13. Materialize Rider assignment notifications during foreground polling.
14. Scope Logistics message counters and push events to the provider.
15. Prevent replay of events outside the receipt retention window.
16. Keep failed deliveries eligible for retry without recording false success.

The suite creates only a dedicated in-memory SQLite connection and minimal test schemas. It runs the actual mobile migration against that isolated connection. It blocks Laravel HTTP requests to external services. No Firebase credential file is bundled or required for these regression cases.

## Not executed here

The review environment has no PHP executable. PHP syntax lint, Laravel route/application boot, migrations against the real schema, Composer resolution, and the 16 PHP regression cases were NOT executed here. Live Google authentication, Firebase acceptance, background GPS and physical-phone behavior were not tested. The patch contains no Flutter source.

## Apply and verify in the Laravel project root

Back up the current code and database. Merge any local changes made after the supplied project snapshot, especially routes/config; see baseline SHA-256 values in the manifest. Copy this ZIP's contents into the Laravel root, preserving paths.

```powershell
composer require "google/apiclient:^2.0"
php artisan optimize:clear
php artisan migrate:status
php artisan migrate
Get-Content PATCH_FILE_MANIFEST.txt | Where-Object { $_ -like '*.php' } | ForEach-Object { php -l $_; if ($LASTEXITCODE -ne 0) { throw "PHP lint failed: $_" } }
php artisan route:list --path=api/v1
php artisan schedule:list
php artisan test --filter=MobileBackendPatchTest
php artisan test
```

The lint command consumes the plain path lines in the manifest. `composer require` updates your existing Composer manifest/lockfile; this ZIP does not replace them. Install the resulting locked dependencies on the server as part of your normal deployment.

The existing three-table migration is unchanged: if already applied, do not roll it back, delete tables or rerun it manually. Do not regenerate the existing APP_KEY.

Configure Google/Firebase using variable names in `INSTALL_LARAVEL_PATCH.md`, keep server credentials private, and enable the server's existing Laravel cron. Check physical-phone delivery separately after Flutter integration.

## Operational limits

- This remains synchronous minute-based dispatch, not an instant WebSocket delivery system. All device batches are scanned but event batches are bounded per device/type and drain on subsequent runs.
- Scheduler `withoutOverlapping()` prevents overlapping scheduled runs. Run only one dispatcher at a time, including manual commands. A crash after FCM acceptance but before receipt persistence may produce a duplicate; delivery is at least once.
- Pending events older than 30 days are intentionally ineligible. Archived Rider notifications are excluded. Existing in-app records are not removed by push dispatch.
- Database query simulations use SQLite; validate the actual MySQL deployment using the commands and account/device scenarios above. Source/reference checks cannot prove production compatibility or error-free operation.
