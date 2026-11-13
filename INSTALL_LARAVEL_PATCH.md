# SARI — Corrected Consolidated Laravel Mobile Backend Patch (2026-11-12)

This corrected build includes all original mobile additions plus the review fixes described in `REVIEW_FIXES_AND_VERIFICATION.md`. It was compared with `LARAVEL-E-COMMERCE.zip`.

This **single patch supersedes** `SARI_Laravel_Mobile_API_Connections_Patch_2026-11-12.zip`. Apply **only this consolidated ZIP**, not both. The original additions were reconciled against the supplied `LARAVEL-E-COMMERCE.zip`, reusing the existing Laravel/MySQL web source of truth. It **does not** include any Flutter changes, Firebase service-account secrets, or a new AI provider.

## Important: deployment safety

1. **Do not deploy directly to production without a backup or comparing recent Git changes.** Files such as `routes/api.php`, `config/services.php` and `routes/console.php` are full-file replacements from the uploaded repository snapshot. If your working branch changed after that snapshot, merge the relevant additions rather than blindly replacing those files.
2. Back up the database and your modified Laravel files, and use a testing/staging branch.
3. Unzip the contents into the Laravel project **root** (`artisan` and `composer.json` should be in that root), preserving the paths.
4. This patch introduces **one migration containing three new tables**. Run migrations only against the intended environment.
5. The latest Flutter app will still need **separate** Google SDK, Firebase SDK, notifications, GPS and AI UI integration. Laravel cannot make mobile buttons work on its own.

## Install commands (PowerShell)

```powershell
composer require "google/apiclient:^2.0"
php artisan optimize:clear
php artisan migrate:status
php artisan migrate
php artisan route:list --path=api/v1
php artisan test --filter=MobileBackendPatchTest
php artisan test
php artisan schedule:list
```

Requirements: PHP 8.2+, the existing Laravel 12, Sanctum and its existing DB tables; an outbound connection to Google for Google-ID-token verification/FCM.

**Scheduler:** On the hosting environment, configure **one scheduler invocation every minute**, using the absolute PHP/artisan paths:

```cron
* * * * * cd /absolute/path/to/sari-ecommerce && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

The original hourly `admin-ai:maintenance` schedule is preserved. The Laravel scheduler also triggers `sari:mobile-push --limit=100` every minute and `sari:mobile-prune` daily. `--limit` is now the database batch size: ALL eligible device batches are visited on each run, rather than always stopping at the first 100 devices. Event batches select pending events before limiting, draining oldest pending events over successive runs. Run `php artisan sari:mobile-push` manually to inspect whether FCM is configured. Without FCM setup, it reports configuration missing; it does **not** claim a notification was sent.

## Configuration: server `.env` only

```dotenv
# Use Google's Web OAuth client ID configured for the Flutter app's serverClientId.
GOOGLE_CLIENT_ID=your-web-oauth-client-id.apps.googleusercontent.com
GOOGLE_MOBILE_SERVER_CLIENT_ID=your-web-oauth-client-id.apps.googleusercontent.com

# Firebase project ID + absolute path to a private, SERVER-ONLY service-account JSON.
# Do not upload this JSON to Flutter, Git, or this chat.
SARI_FIREBASE_PROJECT_ID=your-firebase-project-id
SARI_FIREBASE_CREDENTIALS_FILE=/absolute/private/location/sari-firebase-service-account.json

# Existing Laravel Buyer AI settings are reused. Configure them in the current
# config/sari.php/.env as your web Buyer AI already does, if AI-provider responses are wanted.
```

**Google:** In Google Cloud, configure the Web OAuth client plus the Android package name/signing SHA and (if needed) iOS bundle ID. Flutter must request an ID token whose audience is `GOOGLE_MOBILE_SERVER_CLIENT_ID`. Only already-registered, active, uniquely matched Buyer/Rider/Logistics accounts are permitted. Existing **Seller Google sign-in stays web-only**. No silent email/role merging or auto-account registration.

**Firebase:** Create a Firebase project, register Android/iOS applications, enable Firebase Cloud Messaging HTTP v1, provision a service account with Firebase messaging permissions, and protect the JSON file outside the web root. iOS additionally needs APNs integration in Firebase. Notification sound/channel handling and user permission requests must be implemented in Flutter; Laravel only sends `sound=default`/`sari_updates` channel attributes.

**Buyer AI:** Reuses existing read-only `BuyerAiAssistantService` and server configuration. It can return local responses if your web AI provider is not configured; this patch doesn't supply or store an external AI API key.

## Added files

```
app/Console/Commands/DispatchMobilePush.php
app/Console/Commands/PruneMobileData.php
app/Http/Controllers/Api/V1/MobileGoogleAuthController.php
app/Http/Controllers/Api/V1/MobileBuyerAddressConfirmationController.php
app/Http/Controllers/Api/V1/MobileDeviceController.php
app/Http/Controllers/Api/V1/MobileUpdatesController.php
app/Http/Controllers/Api/V1/MobileRiderLocationController.php
app/Http/Controllers/Api/V1/MobileBuyerAiController.php
app/Http/Controllers/Api/V1/MobileBuyerRecentActivityController.php
app/Http/Middleware/Api/RecordMobileBuyerActivity.php
app/Services/Accounts/GoogleMobileIdTokenVerifier.php
app/Services/Mobile/MobileFcmSender.php
app/Services/Mobile/MobileRiderTracking.php
app/Services/Mobile/MobileBuyerSessionBridge.php
app/Services/Mobile/MobileBuyerActionLogger.php
app/Services/Mobile/MobilePushAccess.php
tests/Feature/MobileBackendPatchTest.php
database/migrations/2026_11_12_170000_create_mobile_sync_infrastructure.php
```

## Existing files that this ZIP changes

```
routes/api.php
routes/console.php
config/services.php
app/Http/Controllers/Api/V1/MobileAuthController.php
app/Http/Controllers/Api/V1/MobileCatalogController.php
app/Http/Controllers/Api/V1/MobileBuyerMessageController.php
app/Http/Controllers/Api/V1/MobileBuyerNotificationController.php
app/Http/Controllers/Api/V1/MobileLogisticsController.php
app/Http/Controllers/Api/V1/MobileRiderNotificationController.php
```

In particular, `MobileRiderNotificationController::sync` becomes callable by the server-side push scheduler to materialize the rider's web-parity inbox before sending alerts.

## Database additions

- `sari_mobile_devices`: device IDs, owner role/account, encrypted FCM registration token, **linked Sanctum token ID**, delivery preference, registration date. Revoked, expired, mismatched, banned, inactive or deleted-account subscriptions are removed during push dispatch. Rider subscriptions also require an accessible linked Logistics provider. Re-register after resolving account access. Refreshing an existing owner/device preserves its original registration cutoff, accepted delivery receipts and push-disabled preference unless `push_enabled` is explicitly supplied.
- `sari_mobile_push_receipts`: a per-device/per-event deduplication record for accepted FCM messages. This is **at-least-once style delivery**, not a guarantee that a phone displayed the push.
- `sari_mobile_rider_locations`: **only the most recently reported location per active assignment**. Position sharing stops on explicit request and is hidden if it is older than two minutes. No historical route trace is stored by this patch. Stale latest positions are deleted after 24 hours by a daily scheduled cleanup, and old push receipts after 30 days. Push eligibility uses the later of initial device registration and the last 30 days, preventing old notification replay after receipt cleanup; outages/backlogs older than 30 days are not retried.

## Endpoint contracts

Detailed API paths in `MOBILE_API_ENDPOINTS.md`. All authenticated routes use existing Sanctum bearer tokens and `EnsureMobileRole`; GPS only permits active jobs owned by the Rider. Buyer location requires ownership of the order, Logistics location is limited to its own registered Rider. FCM notification payloads deliberately contain generic text rather than personal/order details.

## Notification freshness

- **Foreground refresh:** `GET /api/v1/updates` supplies role-scoped unread counts and change IDs. Flutter will **still need to poll** periodically/on-resume (e.g. every 15–30 s), or later subscribe to appropriate realtime channels. The Laravel endpoint alone does not automatically update Flutter widgets.
- **Push while app is backgrounded:** scheduler scans newer existing database order/notification/message records and requests delivery via Firebase FCM. Cron, internet, Firebase credentials, OS permissions and the app's registered FCM device token are required. The scheduler cadence is **approximately one minute**, not instant WebSocket push.
- **Rider inbox:** the existing courier notifications are synchronized before each rider push check AND before each foreground `/updates` poll, even if FCM is not configured. The app's unread/read state remains in the shared tables.
- **Logistics:** preserves manager-only notification recipient scope. Foreground polling now counts actual unread Rider messages and returns their latest ID; background dispatch also includes provider-scoped Rider message pushes. No new Logistics operations modules are added.

## GPS privacy and limits

- Rider GPS is **opt-in from the future Flutter UI**. Call `POST /rider/location` only during an active assigned route with OS permission and user consent. Stop sending updates when delivery completes, background permission is revoked, or the rider stops sharing. Use `DELETE /rider/location` when stopping voluntarily.
- Server validates token and active assignment ownership, coordinates, accuracy and reading freshness. It cannot independently verify that device-reported coordinates are genuine.
- Buyer and Logistics location reads expose at most the latest authorized, nonstale position. **No guaranteed continuous background tracking** until Android/iOS permissions, battery policy, consent and Flutter implementation are complete.
- Production deployment should review privacy policy, retention/access controls and telemetry rates.

## Verification checklist before mobile work

1. `php -l` on the PHP files, `php artisan route:list --path=api/v1`, `php artisan test`, and `php artisan migrate --pretend` as needed.
2. Register Google ID token for an existing active Buyer and reject invalid/wrong-audience/expired tokens, banned/pending/suspended accounts, and duplicate cross-role emails.
3. Register FCM device using a valid bearer token; log out/revoke it and verify the push scheduler stops sending for that token. Verify that other accounts never receive the first account's notifications.
4. Test Buyer `updates`, `ai-assistant/message`, `recent-activity` (including successful mobile cart action), message unread, address confirmation, and notification archive/read actions.
5. Test Rider GPS on an assigned active order/last-mile route; reject unrelated Rider/order; return `location:null` when sharing is off or position expires; verify buyer can read only its own order.
6. Test Logistics notification summary and Rider location using a manager from the matching Logistics provider, and confirm it cannot read another provider's Rider.
7. Verify FCM on physical Android phone (and iOS if in scope). FCM permission, sounds and channel must be tested on the device, not assumed from an HTTP 200.
8. Perform cross-user order, assignment, delivery and database parity tests with web + Flutter after the Flutter patch.

## Verification status for this corrected build

- Reviewed the original 28 patch files and relevant dependencies in `LARAVEL-E-COMMERCE.zip`; checked controller methods, imports and baseline replacement diffs.
- Ran source/reference checks and isolated SQLite query simulations for device traversal, pending-event backlog, receipt scoping, cutoff preservation and retention behavior. These are NOT Laravel integration tests.
- Added `tests/Feature/MobileBackendPatchTest.php` with dedicated in-memory SQLite tests for batching, receipt filtering, registration, account/token access, polling counts, schedules and retry eligibility. It never uses the application's real database connection.
- PHP is unavailable in this review environment. `php -l`, Artisan boot, migration execution and the supplied PHP test suite were NOT run here. Run the commands above in your Laravel environment before deployment.
- No live Firebase, Google sign-in, GPS, production database or physical-device tests were performed. Successful FCM acceptance does not prove a phone displayed the notification.

## Notes

- `routes/console.php` here is based on the original supplied file. If you added scheduled tasks after that snapshot, **merge this patch's Schedule line** into your existing `routes/console.php` instead of replacing all its content.
- The push command uses bounded database batches but scans every eligible device, synchronously. An event backlog is capped per device/type per run and drains on subsequent runs. A large deployment may take longer than one minute; `withoutOverlapping()` skips overlapping scheduler runs. Run only one dispatcher at a time (including manual invocations); a crash after FCM acceptance but before receipt storage may produce a duplicate.
- For high-volume production, migrate the minute-scanning push approach to durable queued event-driven jobs, optimize indexes, add retry/backoff and monitoring, and avoid an all-device scan.
- The existing Google web login and Reverb web events remain untouched.

## Applying the corrected build

- Use this ZIP alone; it already includes the complete original consolidated patch. Do not apply the old patch afterward.
- `composer require "google/apiclient:^2.0"` is mandatory for Google/FCM. This ZIP deliberately does not overwrite your `composer.json` or lockfile; Composer must resolve and record the dependency in your environment.
- The three-table migration is unchanged from the original patch. If that migration is already applied, do not roll it back or delete its tables; apply the corrected PHP files and clear caches.
- Files marked as replacements in `PATCH_FILE_MANIFEST.txt` are based on the supplied full-project snapshot. Merge later local edits, especially in routes/config, instead of overwriting those later edits.
- Do not regenerate your existing APP_KEY: it protects existing data and the encrypted device tokens.
