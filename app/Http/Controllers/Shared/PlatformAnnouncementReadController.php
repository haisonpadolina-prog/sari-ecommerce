<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Platform\PlatformAnnouncement;
use App\Services\Platform\PlatformAnnouncementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlatformAnnouncementReadController extends Controller
{
    public function __construct(
        private readonly PlatformAnnouncementService $announcements
    ) {
    }

    public function __invoke(
        Request $request,
        PlatformAnnouncement $announcement
    ): JsonResponse {
        [$audienceRole, $identityType, $identityId] = $this->identity($request);

        $this->announcements->markRead(
            $announcement,
            $audienceRole,
            $identityType,
            $identityId
        );

        return response()->json(['ok' => true]);
    }

    private function identity(Request $request): array
    {
        if ($request->session()->get('is_buyer')) {
            $registeredId = (int) $request->session()->get('buyer_account_id', 0);
            $socialId = (int) $request->session()->get('buyer_social_account_id', 0);

            abort_if($registeredId <= 0 && $socialId <= 0, 403);

            return [
                'buyer',
                $registeredId > 0 ? 'buyer' : 'social_buyer',
                $registeredId > 0 ? $registeredId : $socialId,
            ];
        }

        if ($request->session()->get('is_seller')) {
            return [
                'seller',
                'seller',
                (int) $request->session()->get('seller_account_id', 0),
            ];
        }

        if ($request->session()->get('is_logistics')) {
            return [
                'logistics',
                'logistics',
                (int) $request->session()->get('logistics_account_id', 0),
            ];
        }

        if ($request->session()->get('is_courier')) {
            return [
                'rider',
                'rider',
                (int) $request->session()->get('courier_account_id', 0),
            ];
        }

        abort(403, 'Authenticated marketplace account required.');
    }
}
