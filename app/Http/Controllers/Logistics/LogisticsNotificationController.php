<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Platform\LogisticsNotification;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class LogisticsNotificationController extends Controller
{
    private const SHARED_DESTINATIONS = [
        'logistics.dashboard',
        'logistics.pickup-requests',
        'logistics.delivery-assignment',
        'logistics.rider-management',
        'logistics.incoming-parcels',
        'logistics.parcel-sorting',
        'logistics.hub-transfers',
        'logistics.last-mile-assignment',
        'logistics.delivery-monitoring',
        'logistics.reports',
    ];

    private const MANAGER_DESTINATIONS = [
        'logistics.rider-applications',
        'logistics.facilities.index',
        'logistics.messages',
        'logistics.account-management',
    ];

    /**
     * Server-filtered notification history.
     *
     * Counts are mailbox-wide, while filters and search are applied
     * before pagination.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Notification Filters
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'filter' => [
                'nullable',
                'in:all,unread',
            ],

            'q' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $activeFilter = $validated['filter'] ?? 'all';

        $search = trim($validated['q'] ?? '');

        /*
        |--------------------------------------------------------------------------
        | Resolve Exact Recipient Mailbox
        |--------------------------------------------------------------------------
        */

        $mailbox = $this->mailbox($request);

        /*
        |--------------------------------------------------------------------------
        | Global Mailbox Counts
        |--------------------------------------------------------------------------
        |
        | These are not limited to the current page or search results.
        |
        */

        $totalCount = (clone $mailbox)->count();

        $unreadCount = (clone $mailbox)
            ->unread()
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Apply Server-Side Status Filter
        |--------------------------------------------------------------------------
        */

        $filtered = clone $mailbox;

        if ($activeFilter === 'unread') {
            $filtered->unread();
        }

        /*
        |--------------------------------------------------------------------------
        | Apply Server-Side Search
        |--------------------------------------------------------------------------
        |
        | Search only within the authenticated recipient's mailbox.
        | User input is passed through SQL parameter bindings.
        |
        */

        if ($search !== '') {
            $term = '%'.mb_strtolower($search).'%';

            $filtered->where(
                function (Builder $query) use ($term): void {
                    $query
                        ->whereRaw(
                            'LOWER(title) LIKE ?',
                            [$term]
                        )
                        ->orWhereRaw(
                            'LOWER(message) LIKE ?',
                            [$term]
                        )
                        ->orWhereRaw(
                            'LOWER(type) LIKE ?',
                            [$term]
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Paginate Filtered Results
        |--------------------------------------------------------------------------
        |
        | withQueryString() preserves filter and q when changing pages.
        |
        */

        $notifications = $filtered
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $filteredCount = (int) $notifications->total();

        /*
        |--------------------------------------------------------------------------
        | Current Recipient Context
        |--------------------------------------------------------------------------
        */

        $isHubOperator = CurrentLogisticsHub::isHubOperator(
            $request
        );

        $hubFacility = $isHubOperator
            ? CurrentLogisticsHub::requireFacility($request)
            : null;

        return view(
            'logistics.notifications',
            compact(
                'notifications',
                'unreadCount',
                'totalCount',
                'filteredCount',
                'activeFilter',
                'search',
                'isHubOperator',
                'hubFacility'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Header Notification Summary
    |--------------------------------------------------------------------------
    |
    | Returns the latest seven persistent notifications and global unread
    | count for this exact recipient.
    |
    */

    public function summary(Request $request): JsonResponse
    {
        $mailbox = $this->mailbox($request);

        $unreadCount = (clone $mailbox)
            ->unread()
            ->count();

        $recent = (clone $mailbox)
            ->orderByDesc('id')
            ->limit(7)
            ->get();

        return $this->noStoreJson([
            'ok' => true,

            'unread_count' => $unreadCount,

            'notifications' => $recent->map(
                fn (LogisticsNotification $item): array => [
                    'id' => (int) $item->id,

                    'type' => $item->type,

                    'title' => $item->title,

                    'message' => $item->message,

                    'is_read' => $item->isRead(),

                    'created_at' =>
                        $item->created_at?->toIso8601String(),

                    'time_ago' =>
                        $item->created_at?->diffForHumans(),

                    'read_url' => route(
                        'logistics.notifications.read',
                        [
                            'notification' => $item->id,
                        ]
                    ),

                    'open_url' => route(
                        'logistics.notifications.open',
                        [
                            'notification' => $item->id,
                        ]
                    ),
                ]
            )->all(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mark Individual Notification as Read
    |--------------------------------------------------------------------------
    |
    | Notification IDs outside the recipient's mailbox return 404.
    |
    */

    public function read(
        Request $request,
        int $notification
    ): RedirectResponse|JsonResponse {
        $mailbox = $this->mailbox($request);

        $item = (clone $mailbox)
            ->whereKey($notification)
            ->firstOrFail();

        $this->markAsRead(
            $mailbox,
            (int) $item->id
        );

        $unreadCount = (clone $mailbox)
            ->unread()
            ->count();

        if ($request->expectsJson()) {
            return $this->noStoreJson([
                'ok' => true,

                'notification_id' => (int) $item->id,

                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with(
            'success',
            'Notification marked as read.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Mark All Notifications as Read
    |--------------------------------------------------------------------------
    |
    | This updates the entire current recipient mailbox.
    | It is not limited to the selected page or search results.
    |
    */

    public function readAll(
        Request $request
    ): RedirectResponse|JsonResponse {
        $mailbox = $this->mailbox($request);

        (clone $mailbox)
            ->unread()
            ->update([
                'read_at' => now(),
            ]);

        /*
         * Recount instead of assuming zero. A newly inserted notification
         * may remain unread if it arrives during the update.
         */

        $unreadCount = (clone $mailbox)
            ->unread()
            ->count();

        if ($request->expectsJson()) {
            return $this->noStoreJson([
                'ok' => true,

                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with(
            'success',
            'Notifications marked as read.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Open Notification
    |--------------------------------------------------------------------------
    |
    | POST only:
    | 1. Confirm ownership.
    | 2. Resolve a whitelisted destination.
    | 3. Mark notification as read.
    | 4. Redirect to the authorized Logistics page.
    |
    */

    public function open(
        Request $request,
        int $notification
    ): RedirectResponse|JsonResponse {
        $mailbox = $this->mailbox($request);

        $item = (clone $mailbox)
            ->whereKey($notification)
            ->firstOrFail();

        $destination = $this->destinationFor(
            $item,
            CurrentLogisticsHub::isHubOperator($request)
        );

        $this->markAsRead(
            $mailbox,
            (int) $item->id
        );

        if ($request->expectsJson()) {
            return $this->noStoreJson([
                'ok' => true,

                'notification_id' => (int) $item->id,

                'unread_count' =>
                    (clone $mailbox)->unread()->count(),

                'redirect_url' => route($destination),
            ]);
        }

        return redirect()->route($destination);
    }

    /*
    |--------------------------------------------------------------------------
    | Exact Recipient Mailbox
    |--------------------------------------------------------------------------
    |
    | Never accept provider ID or recipient ID from request input.
    |
    | @return Builder<LogisticsNotification>
    |
    */

    private function mailbox(Request $request): Builder
    {
        $provider = CurrentLogisticsAccount::resolve(
            $request
        );

        $hubAccountId = null;

        if (CurrentLogisticsHub::isHubOperator($request)) {
            $hubAccount = CurrentLogisticsHub::requireAccount(
                $request
            );

            abort_unless(
                (int) $hubAccount->logistics_account_id
                    === (int) $provider->id,
                403,
                'Invalid Hub Operator provider relationship.'
            );

            $hubAccountId = (int) $hubAccount->id;
        }

        return LogisticsNotification::query()
            ->forRecipient(
                (int) $provider->id,
                $hubAccountId
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Idempotent Read Update
    |--------------------------------------------------------------------------
    |
    | @param Builder<LogisticsNotification> $mailbox
    |
    */

    private function markAsRead(
        Builder $mailbox,
        int $notificationId
    ): void {
        (clone $mailbox)
            ->whereKey($notificationId)
            ->unread()
            ->update([
                'read_at' => now(),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Secure Notification Destination
    |--------------------------------------------------------------------------
    */

    private function destinationFor(
        LogisticsNotification $notification,
        bool $isHubOperator
    ): string {
        $allowed = $isHubOperator
            ? self::SHARED_DESTINATIONS
            : array_merge(
                self::SHARED_DESTINATIONS,
                self::MANAGER_DESTINATIONS
            );

        $name = $notification->action_route;

        if (
            ! is_string($name)
            || ! in_array($name, $allowed, true)
            || ! empty($notification->action_params)
            || ! Route::has($name)
        ) {
            return 'logistics.dashboard';
        }

        return $name;
    }

    /*
    |--------------------------------------------------------------------------
    | Private JSON Responses
    |--------------------------------------------------------------------------
    */

    private function noStoreJson(array $payload): JsonResponse
    {
        return response()
            ->json($payload)
            ->header(
                'Cache-Control',
                'no-store, no-cache, must-revalidate'
            );
    }
}
