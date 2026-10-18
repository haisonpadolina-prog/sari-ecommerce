<?php

namespace App\Support;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Http\Request;

final class LogisticsOrderAccess
{
    /**
     * Resolve the signed-in Logistics provider only.
     *
     * Legacy order backfilling is intentionally NOT performed on every page
     * request because route resolution can involve geocoding and multiple DB
     * queries. New orders are routed when Seller marks them ready; legacy
     * orders are handled by the explicit maintenance seeder.
     */
    public function prepare(
        Request $request,
        int $legacyAssignmentLimit = 0
    ): LogisticsAccount {
        return CurrentLogisticsAccount::resolve($request);
    }

    public function ownedOrder(
        Request $request,
        MarketplaceOrder $order
    ): MarketplaceOrder {
        $logistics = CurrentLogisticsAccount::resolve($request);

        abort_unless(
            (int) $order->logistics_account_id
                === (int) $logistics->id,
            404
        );

        return $order;
    }
}