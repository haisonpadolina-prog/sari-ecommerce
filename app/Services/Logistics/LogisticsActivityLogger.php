<?php

namespace App\Services\Logistics;

use App\Models\Delivery\LogisticsFacility;
use App\Models\Logistics\LogisticsActivityLog;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Illuminate\Http\Request;

final class LogisticsActivityLogger
{
    /*
    |--------------------------------------------------------------------------
    | Record Logistics Activity
    |--------------------------------------------------------------------------
    |
    | Records a completed Logistics action.
    |
    | The authenticated account and hub are resolved
    | from the server-side session, never from user input.
    |
    | For Hub Operators, their assigned facility is enforced.
    | Main Logistics may record network-wide or hub-specific actions.
    |
    */

    public function record(
        Request $request,
        string $module,
        string $action,
        string $description,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?int $facilityId = null
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Resolve Logistics Provider
        |--------------------------------------------------------------------------
        */

        $provider = CurrentLogisticsAccount::resolve($request);

        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);

        /*
        |--------------------------------------------------------------------------
        | Resolve Actor Identity
        |--------------------------------------------------------------------------
        */

        if ($isHubOperator) {

            /*
             * Hub Operator identity must come from the
             * authenticated Hub Account.
             */

            $operator = CurrentLogisticsHub::requireAccount($request);

            $actorType = 'hub_operator';

            $actorId = (int) $operator->id;

            $actorName = $operator->name;

            /*
             * Always use the operator's assigned hub.
             * Never trust a manually supplied facility ID.
             */

            $facility = CurrentLogisticsHub::requireFacility($request);

        } else {

            /*
             * Main Logistics Manager.
             */

            $actorType = 'manager';

            $actorId = (int) $provider->id;

            $actorName = $provider->displayName();

            /*
             * Main Logistics may perform network-wide actions
             * or actions affecting a specific facility.
             */

            $facility = null;

            if ($facilityId !== null) {

                /*
                 * Confirm that the selected facility belongs
                 * to the authenticated Logistics provider.
                 */

                $facility = LogisticsFacility::query()
                    ->where(
                        'logistics_account_id',
                        $provider->id
                    )
                    ->find($facilityId);

                abort_unless(
                    $facility,
                    404,
                    'The selected Logistics facility was not found.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Save Activity Record
        |--------------------------------------------------------------------------
        |
        | Store a snapshot of the actor and facility names.
        | This keeps historical records readable even if
        | their names are changed later.
        |
        */

        LogisticsActivityLog::query()->create([

            'logistics_account_id' => $provider->id,

            'actor_type' => $actorType,

            'actor_id' => $actorId,

            'actor_name' => $actorName,

            'logistics_facility_id' => $facility?->id,

            'facility_name' => $facility?->name,

            'module' => $module,

            'action' => $action,

            'description' => $description,

            'subject_type' => $subjectType,

            'subject_id' => $subjectId,

            'created_at' => now(),

        ]);
    }
}
