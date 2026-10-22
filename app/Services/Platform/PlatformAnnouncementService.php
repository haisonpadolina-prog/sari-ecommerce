<?php

namespace App\Services\Platform;

use App\Models\Platform\PlatformAnnouncement;
use App\Models\Platform\PlatformAnnouncementRead;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PlatformAnnouncementService
{
    public const AUDIENCES = ['buyer', 'seller', 'logistics', 'rider'];

    public function activeForCurrentSession(Request $request, int $limit = 5): Collection
    {
        $context = $this->currentIdentity($request);

        if (!$context) {
            return collect();
        }

        return $this->activeFor(
            $context['audience_role'],
            $context['identity_type'],
            $context['identity_id'],
            $limit
        );
    }

    public function currentIdentity(Request $request): ?array
    {
        if ($request->session()->get('is_buyer')) {
            $registeredId = (int) $request->session()->get('buyer_account_id', 0);
            $socialId = (int) $request->session()->get('buyer_social_account_id', 0);
            $identityId = $registeredId > 0 ? $registeredId : $socialId;

            if ($identityId > 0) {
                return [
                    'audience_role' => 'buyer',
                    'identity_type' => $registeredId > 0 ? 'buyer' : 'social_buyer',
                    'identity_id' => $identityId,
                ];
            }
        }

        if ($request->session()->get('is_seller')) {
            $identityId = (int) $request->session()->get('seller_account_id', 0);

            if ($identityId > 0) {
                return [
                    'audience_role' => 'seller',
                    'identity_type' => 'seller',
                    'identity_id' => $identityId,
                ];
            }
        }

        if ($request->session()->get('is_logistics')) {
            $identityId = (int) $request->session()->get('logistics_account_id', 0);

            if ($identityId > 0) {
                return [
                    'audience_role' => 'logistics',
                    'identity_type' => 'logistics',
                    'identity_id' => $identityId,
                ];
            }
        }

        if ($request->session()->get('is_courier')) {
            $identityId = (int) $request->session()->get('courier_account_id', 0);

            if ($identityId > 0) {
                return [
                    'audience_role' => 'rider',
                    'identity_type' => 'rider',
                    'identity_id' => $identityId,
                ];
            }
        }

        return null;
    }

    public function activeFor(
        string $audienceRole,
        string $identityType,
        int $identityId,
        int $limit = 5
    ): Collection {
        if (
            !in_array($audienceRole, self::AUDIENCES, true)
            || $identityId <= 0
        ) {
            return collect();
        }

        return PlatformAnnouncement::query()
            ->live()
            ->forAudience($audienceRole)
            ->whereDoesntHave(
                'reads',
                fn ($query) => $query
                    ->where('identity_type', $identityType)
                    ->where('identity_id', $identityId)
            )
            ->latest('published_at')
            ->latest('id')
            ->limit(max(1, min($limit, 10)))
            ->get()
            ->each(
                fn (PlatformAnnouncement $announcement) =>
                    $announcement->setAttribute('viewer_read_at', null)
            );
    }

    public function unreadCount(
        string $audienceRole,
        string $identityType,
        int $identityId
    ): int {
        return $this->activeFor(
            $audienceRole,
            $identityType,
            $identityId,
            10
        )->count();
    }

    public function markRead(
        PlatformAnnouncement $announcement,
        string $audienceRole,
        string $identityType,
        int $identityId
    ): void {
        abort_unless(
            in_array($audienceRole, self::AUDIENCES, true)
                && $identityId > 0
                && in_array($audienceRole, (array) $announcement->audiences, true)
                && $announcement->status === 'published'
                && (!$announcement->starts_at || $announcement->starts_at->lte(now()))
                && (!$announcement->ends_at || $announcement->ends_at->gt(now())),
            403,
            'This announcement is not available to the current account.'
        );

        PlatformAnnouncementRead::query()->updateOrCreate(
            [
                'platform_announcement_id' => $announcement->id,
                'identity_type' => $identityType,
                'identity_id' => $identityId,
            ],
            [
                'read_at' => now(),
            ]
        );
    }
}
