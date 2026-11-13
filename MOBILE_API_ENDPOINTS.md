# SARI Mobile Backend: API routes to use from Flutter

Base URL: `https://preview.sariph.store/api/v1` **once deployed to that server**. This ZIP does not deploy automatically.

## Public authentication / catalog

| Method | URL | Body / action |
|---|---|---|
| POST | `/auth/google` | `{ "id_token":"<google-id-token>", "device_name":"Android phone" }` -> 30-day Sanctum bearer token and role-scoped user. Requires valid Google Web client ID audience. |
| GET | `/products?seller_id=123&page=1` | Seller-scoped buyer-visible inventory, using existing paging. |

## Shared mobile routes (Authorization: Bearer TOKEN)

| Method | URL | Body / action |
|---|---|---|
| POST | `/devices` | `{ "device_id":"<stable-uuid-per-install>", "platform":"android", "fcm_token":"<token>", "push_enabled":true }` Register/update this device's FCM token. |
| DELETE | `/devices` | `{ "device_id":"<stable-uuid-per-install>" }` Unregister on logout. |
| GET | `/updates` | `{role,unread_notifications,unread_messages,latest_notification_id,latest_message_id}` (under `data`) for foreground polling. |

## Buyer (Buyer bearer token only)

| Method | URL | Body / action |
|---|---|---|
| POST | `/buyer/ai-assistant/message` | `{ "message":"Where is my order?", "history":[], "page_context":{"path":"/buyer/orders"} }` -> `{success:true,data:{reply,products,provider,...}}` |
| GET | `/buyer/recent-activity?per_page=10&archived=0` | Own activity, paginated, with read state. |
| GET | `/buyer/recent-activity/summary` | `data.unread_count` |
| POST | `/buyer/recent-activity/read-all` | Mark all as read. |
| POST | `/buyer/recent-activity/{event}/read` | Mark owned activity as read. |
| POST | `/buyer/recent-activity/{event}/archive` | Archive owned activity. |
| PATCH | `/buyer/recent-activity/{event}/restore` | Restore owned archived activity. |
| DELETE | `/buyer/recent-activity/{event}` | Hide activity for this Buyer only. |
| GET | `/buyer/orders/{order}/rider-location` | Authorized active order's current GPS, or `data.location: null`. |
| GET | `/buyer/messages/unread` | Unread Seller messages. |
| POST | `/buyer/messages/orders/{order}/confirm-address` | Confirm delivery address using existing web service. |
| POST | `/buyer/notifications/{event}/archive` | Archive own notification. |
| PATCH | `/buyer/notifications/{event}/restore` | Restore archived notification. |
| DELETE | `/buyer/notifications/{event}` | Hide own notification; no shared order event deletion. |

## Rider (Rider bearer token only)

| Method | URL | Body / action |
|---|---|---|
| POST | `/rider/location` | `{ "assignment_type":"order", "assignment_id":123, "latitude":14.1, "longitude":121.1, "accuracy_m":12, "recorded_at":"2026-11-12T09:20:00Z" }` Must be a **recent, assigned, active** order; alternatively `assignment_type:last_mile`. Max 12 submissions/min. |
| DELETE | `/rider/location` | `{ "assignment_type":"order", "assignment_id":123 }` Stop sharing for a job. |

## Logistics (Logistics manager bearer token only)

| Method | URL | Body / action |
|---|---|---|
| GET | `/logistics/riders/{rider}/location` | Position of the manager's **own** active Rider assignment or `data.location:null`. |
| GET | `/logistics/notifications/summary` | Own unread notifications. |
| POST | `/logistics/notifications/read-all` | Mark manager's own mailbox read. |

## Common JSON examples

Successful device registration:
```json
{"success":true,"message":"Push device registered."}
```

No available authorized Rider GPS:
```json
{"success":true,"data":{"location":null}}
```

Request validation failures use Laravel's standard `422` JSON validation response; missing/non-owned protected resources return `404` or `403`; missing or expired bearer tokens return `401`/`403` per existing middleware.

**All URLs remain unavailable on the deployed server until you merge and deploy this patch.**

## Corrected notification behavior

- Rider `/updates` synchronizes its delivery inbox before returning counts.
- Logistics `/updates` returns actual provider-scoped unread Rider message counts and latest message IDs.
- Re-registering an existing owner/device preserves its initial push cutoff and receipt history. Omitting `push_enabled` preserves the existing preference; send `true` explicitly to enable it again.
- The dispatcher scans all device batches, filters already-delivered events before per-type limits, and checks token ownership/abilities, expiration, account status and email bans. Linked Rider providers must also be accessible.
- Pending push eligibility is bounded to 30 days to match receipt cleanup. Delivery remains at least once, with one active dispatcher recommended.
