<?php

namespace App\Support;

use App\Models\Accounts\LogisticsHubAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * Resolves the signed-in Hub Operator and provides reusable hub-level access
 * rules. A manager retains network-level access; callers must still apply
 * their existing parent Logistics account scope for manager queries.
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

    public static function facility(Request $request): ?LogisticsFacility
    {
        return self::account($request)?->facility;
    }

    public static function facilityId(Request $request): ?int
    {
        return self::facility($request)?->id;
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
     * Apply hub visibility to a MarketplaceOrder query.
     *
     * origin: Seller pickup / origin intake
     * destination: destination intake / last-mile dispatch
     * involved: read-only journey / reporting of orders involving this hub
     *
     * The manager's existing provider-scoped query is returned unchanged.
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

        if (! self::isHubOperator($request)) {
            return $query;
        }

        $facility = self::requireFacility($request);

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
     * Apply hub visibility to a LogisticsParcel query through its order.
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

        if (! self::isHubOperator($request)) {
            return $query;
        }

        $facility = self::requireFacility($request);

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
     * For an individual order action, return 404 rather than disclosing an
     * unrelated order's existence. This method is read-only: it never creates
     * missing delivery route stops or reassigns an order.
     */
    public static function authorizeOrder(
        Request $request,
        MarketplaceOrder $order,
        string $visibility = 'involved'
    ): void {
        self::validateVisibility($visibility);

        if (! self::isHubOperator($request)) {
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

    public static function clearSession(Request $request): void
    {
        $request->session()->forget([
            'is_logistics_hub_operator',
            'logistics_hub_account_id',
            'logistics_facility_id',
            'logistics_hub_name',
            'logistics_hub_email',
        ]);
    }
}
