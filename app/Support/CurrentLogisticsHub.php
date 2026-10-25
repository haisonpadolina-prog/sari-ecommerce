<?php

namespace App\Support;

use App\Models\Accounts\LogisticsHubAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * Resolves the current Logistics operating facility.
 *
 * Hub Operators are permanently scoped to their assigned facility.
 * Main Logistics Managers keep their own identity but select an operating
 * branch through logistics_operating_facility_id. This keeps branch context
 * separate from Hub Operator authentication/session keys.
 */
final class CurrentLogisticsHub
{
    private const VALID_ORDER_SCOPES = [
        'origin',
        'destination',
        'involved',
    ];

    public static function isHubOperator(Request $request): bool
    {
        return (bool) $request->session()->get(
            'is_logistics_hub_operator',
            false
        );
    }

    public static function account(Request $request): ?LogisticsHubAccount
    {
        if (! self::isHubOperator($request)) {
            return null;
        }

        $hubAccountId = (int) $request->session()->get(
            'logistics_hub_account_id',
            0
        );

        $parentId = (int) $request->session()->get(
            'logistics_account_id',
            0
        );

        $facilityId = (int) $request->session()->get(
            'logistics_facility_id',
            0
        );

        if ($hubAccountId <= 0 || $parentId <= 0 || $facilityId <= 0) {
            return null;
        }

        $account = LogisticsHubAccount::query()
            ->with(['logisticsAccount', 'facility'])
            ->whereKey($hubAccountId)
            ->where('logistics_account_id', $parentId)
            ->where('logistics_facility_id', $facilityId)
            ->where('role', 'hub_operator')
            ->where('account_status', 'active')
            ->first();

        if (! $account) {
            return null;
        }

        if ($account->logisticsAccount?->account_status !== 'active') {
            return null;
        }

        $facility = $account->facility;

        if (
            ! $facility
            || ! $facility->is_active
            || (int) $facility->logistics_account_id !== $parentId
        ) {
            return null;
        }

        return $account;
    }

    /**
     * Hub Operator's permanently assigned facility only.
     */
    public static function facility(Request $request): ?LogisticsFacility
    {
        return self::account($request)?->facility;
    }

    /**
     * Active facility used for operational pages.
     *
     * Hub Operator: assigned facility.
     * Manager: explicitly selected operating branch.
     */
    public static function operatingFacility(Request $request): ?LogisticsFacility
    {
        if (self::isHubOperator($request)) {
            return self::facility($request);
        }

        $role = trim((string) $request->session()->get('logistics_role', ''));

        if ($role !== '' && $role !== 'manager') {
            return null;
        }

        $providerId = (int) $request->session()->get(
            'logistics_account_id',
            0
        );

        $facilityId = (int) $request->session()->get(
            'logistics_operating_facility_id',
            0
        );

        $selectedSessionId = (string) $request->session()->get(
            'logistics_operating_session_id',
            ''
        );

        $currentSessionId = (string) $request->session()->getId();

        if (
            $providerId <= 0
            || $facilityId <= 0
            || $selectedSessionId === ''
            || $currentSessionId === ''
            || ! hash_equals($currentSessionId, $selectedSessionId)
        ) {
            return null;
        }

        return LogisticsFacility::query()
            ->whereKey($facilityId)
            ->where('logistics_account_id', $providerId)
            ->where('is_active', true)
            ->first();
    }

    public static function hasOperatingFacility(Request $request): bool
    {
        return self::operatingFacility($request) !== null;
    }

    public static function facilityId(Request $request): ?int
    {
        return self::facility($request)?->id;
    }

    public static function operatingFacilityId(Request $request): ?int
    {
        return self::operatingFacility($request)?->id;
    }

    public static function accountId(Request $request): ?int
    {
        return self::account($request)?->id;
    }

    public static function requireAccount(Request $request): LogisticsHubAccount
    {
        $account = self::account($request);

        abort_unless(
            $account,
            403,
            'An active, correctly assigned Hub Operator account is required.'
        );

        return $account;
    }

    /**
     * Hub Operator assignment only. Keep for identity-sensitive operations.
     */
    public static function requireFacility(Request $request): LogisticsFacility
    {
        $facility = self::facility($request);

        abort_unless(
            $facility,
            403,
            'An active assigned Logistics Hub is required.'
        );

        return $facility;
    }

    /**
     * Current operational branch for either a Hub Operator or Manager.
     */
    public static function requireOperatingFacility(Request $request): LogisticsFacility
    {
        $facility = self::operatingFacility($request);

        abort_unless(
            $facility,
            403,
            'Select an active Logistics operating branch before continuing.'
        );

        return $facility;
    }

    /**
     * Apply current operating-branch visibility to MarketplaceOrder queries.
     *
     * origin: Seller pickup / origin intake
     * destination: destination intake / last-mile dispatch
     * involved: either origin or destination hub
     *
     * @param Builder<MarketplaceOrder> $query
     * @return Builder<MarketplaceOrder>
     */
    public static function scopeOrders(
        Request $request,
        Builder $query,
        string $visibility = 'involved'
    ): Builder {
        self::validateVisibility($visibility);

        $facility = self::operatingFacility($request);

        if (! $facility) {
            return $query;
        }

        return $query
            ->where('logistics_account_id', $facility->logistics_account_id)
            ->whereHas('deliveryRouteStops', function (Builder $stops) use (
                $facility,
                $visibility
            ): void {
                self::applyStopVisibility(
                    $stops,
                    (int) $facility->id,
                    $visibility
                );
            });
    }

    /**
     * Apply current operating-branch visibility to LogisticsParcel queries.
     *
     * @param Builder<\App\Models\Delivery\LogisticsParcel> $query
     * @return Builder<\App\Models\Delivery\LogisticsParcel>
     */
    public static function scopeParcels(
        Request $request,
        Builder $query,
        string $visibility = 'involved'
    ): Builder {
        self::validateVisibility($visibility);

        $facility = self::operatingFacility($request);

        if (! $facility) {
            return $query;
        }

        return $query->whereHas(
            'order',
            function (Builder $orders) use ($facility, $visibility): void {
                $orders
                    ->where('logistics_account_id', $facility->logistics_account_id)
                    ->whereHas('deliveryRouteStops', function (Builder $stops) use (
                        $facility,
                        $visibility
                    ): void {
                        self::applyStopVisibility(
                            $stops,
                            (int) $facility->id,
                            $visibility
                        );
                    });
            }
        );
    }

    /**
     * Authorize an individual order against the selected/assigned facility.
     * Returns 404 to avoid disclosing unrelated provider/facility records.
     */
    public static function authorizeOrder(
        Request $request,
        MarketplaceOrder $order,
        string $visibility = 'involved'
    ): void {
        self::validateVisibility($visibility);

        if (! self::hasOperatingFacility($request)) {
            return;
        }

        $query = MarketplaceOrder::query()->whereKey($order->getKey());

        abort_unless(
            self::scopeOrders($request, $query, $visibility)->exists(),
            404
        );
    }

    private static function validateVisibility(string $visibility): void
    {
        if (! in_array($visibility, self::VALID_ORDER_SCOPES, true)) {
            throw new InvalidArgumentException('Unknown Logistics Hub scope.');
        }
    }

    private static function applyStopVisibility(
        Builder $query,
        int $facilityId,
        string $visibility
    ): void {
        $query->where('logistics_facility_id', $facilityId);

        if ($visibility === 'origin') {
            $query->where('stop_key', 'origin_facility');
        } elseif ($visibility === 'destination') {
            $query->where('stop_key', 'destination_facility');
        } else {
            $query->whereIn('stop_key', [
                'origin_facility',
                'destination_facility',
            ]);
        }
    }

    public static function clearOperatingContext(Request $request): void
    {
        $request->session()->forget([
            'logistics_operating_facility_id',
            'logistics_operating_facility_name',
            'logistics_operating_facility_code',
            'logistics_operating_session_id',
        ]);
    }

    public static function clearSession(Request $request): void
    {
        $request->session()->forget([
            'is_logistics_hub_operator',
            'logistics_hub_account_id',
            'logistics_facility_id',
            'logistics_hub_name',
            'logistics_hub_email',
            'logistics_operating_facility_id',
            'logistics_operating_facility_name',
            'logistics_operating_facility_code',
            'logistics_operating_session_id',
        ]);
    }
}
