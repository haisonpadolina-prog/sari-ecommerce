# Delivered files

Full files are included; copy paths from this ZIP into the Laravel project root. Only three existing files were updated; the other application files are additions.

| Path | Purpose |
| --- | --- |
| `config/admin_ai.php` | Feature flags, provider settings and retention thresholds. |
| `database/migrations/2026_11_11_190000_create_admin_ai_tables.php` | Encrypted conversation and expiring proposal storage. |
| `app/Models/Platform/AdminAiThread.php` | Encrypted per-admin conversation context. |
| `app/Models/Platform/AdminAiAction.php` | Encrypted, owned proposal and execution evidence. |
| `app/Http/Middleware/Admin/EnsureAdminAiAccess.php` | Current admin-record guard and private responses. |
| `app/Services/Admin/Ai/AdminAiDataService.php` | Allowlisted database queries, chart aggregates and operational alerts. |
| `app/Services/Admin/Ai/AdminAiActionService.php` | Validated action previews, permissions, confirmation, replay protection and auditing. |
| `app/Services/Admin/Ai/AdminAiAssistantService.php` | Multilingual Gemini function-calling loop and server-owned follow-up context. |
| `app/Http/Controllers/Admin/AdminAiAssistantController.php` | Chat, status, alerts, confirmation and forget endpoints. |
| `app/Console/Commands/AdminAiMaintenance.php` | Retained-history pruning and daily-deduplicated proactive notifications. |
| `app/Jobs/DispatchAdminSellerRealtime.php` | Restores missing existing-controller dependency with after-commit seller events. |
| `routes/admin-ai.php` | Protected, rate-limited feature routes. |
| `routes/web.php` | Existing full file with one new AI routes include. |
| `routes/console.php` | Existing full file with hourly maintenance schedule. |
| `resources/views/layouts/admin.blade.php` | Existing full layout with assistant assets and persisted widget. |
| `resources/views/components/admin/ai-assistant.blade.php` | Accessible chat panel, language selector and quick prompts. |
| `public/css/admin/ai-assistant.css` | Scoped neutral SARI widget styling and responsive rules. |
| `public/js/admin/ai-assistant.js` | Chat interactions, protected product cards, tables, CSV, chart PNG and saved-location maps. |
| `tests/Feature/AdminAiAssistantTest.php` | 50 targeted feature tests. |
| `docs/ADMIN_AI_ASSISTANT.md` | Installation, permissions, data definitions, capabilities and limitations. |
| `docs/VALIDATION.md` | Executed verification and outstanding runtime checks. |

Not included: original `.env` files, production credentials, vendor/node_modules, original uploads/media, `.git`, generated caches, lockfiles, or test-runtime dependencies.

## Gemini / Buyer UI correction

Updated: `config/admin_ai.php`, `AdminAiAssistantService`, `AdminAiAssistantController`, the Admin widget Blade/CSS/JS, Admin layout cache-busting URLs, tests and documentation. Included unchanged: `public/images/sari-seller-ai-assistant.png`, the Buyer robot artwork. The existing Buyer assistant and `.env` are unchanged. No additional migration.

## Language and UI update

Added `public/js/admin/ai-assistant-i18n.js` for local English/Tagalog interface copy and `app/Services/Admin/Ai/AdminAiTranslationService.php` for bounded, tool-free display translation. Updated assistant language instructions, controller/routes, widget Blade/CSS/JS, layout asset versions, tests and documentation. No schema change.

## Rate limiter correction

Added `app/Http/Middleware/Admin/ThrottleAdminAiRequests.php`. Updated `routes/admin-ai.php`, chat JS/localized wait text, asset cache versions, tests and documentation. Removed nested generic throttle counters, preserved per-operation limits and isolated them by validated admin ID. No existing global middleware or unrelated routes were modified.

## Graph correction

Added `public/js/admin/ai-assistant-chart.js`. Updated `AdminAiDataService` date aggregation/zero filling, assistant metric instructions, chart rendering/localization/style, layout asset order/version, tests and documentation. Delivered metrics now use delivered_at and Manila buckets; order count remains based on created_at. No schema or credential change.


### Language modal UI — 2026-11-12
Compact language and Priorities controls replace the dropdown strip. Language modal supports Auto, English, Tagalog and Taglish, explicit save/cancel, Escape/backdrop dismissal, focus return/trap and inert background. Routine status is screen-reader-only; progress and errors remain visible. Existing translations, action confirmation and backend endpoints are preserved. Updated Blade, scoped CSS, main JS, locale catalog and layout asset versions.
Validation: JS syntax and jsdom language/modal tests passed, including cancel/save, Escape, focus trap/return, preference persistence, translation caching and unchanged action payloads; rate-limit and SVG chart regression tests passed. Full browser layout and live Gemini/database requests were not verified for this UI update.


## Admin operations expansion — 2026-11-12

New files:
- app/Services/Admin/Ai/AdminAiOperationsService.php — bounded Action Center, logistics, complaints/returns, stage-specific COD, seller metrics and proposal-history reads.
- app/Services/Admin/Ai/AdminAiPermissionService.php — AI-specific read/action authorization.
- database/migrations/2026_11_12_140000_add_admin_ai_permissions.php — nullable AI role; no automatic privileges.

Updated: AdminAiDataService (authorized dispatch/review queue), AdminAiAssistantService (new declarations/instructions), AdminAiActionService (reviewer authorization/AI-role previews/stale pending checks/failure history), AdminAiAssistantController and routes/admin-ai.php (protected direct read/preview endpoints/capabilities), widget Blade/CSS/JS and locale catalog (Admin tools/checkbox bulk preview/date inputs), Admin layout (asset version), feature tests and documentation. Existing AdminAccount model is unchanged; role mutations use forceFill only after super-admin authorization. Existing global admin page permissions and .env are unchanged.
