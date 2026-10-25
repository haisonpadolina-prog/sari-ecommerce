<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Messaging\LogisticsMessage;
use App\Models\Registration\RegistrationApplication;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LogisticsMessageController extends Controller
{
    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        $approvedRiderApplicationIds = RegistrationApplication::query()
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('role', 'rider')
            ->where('status', 'approved')
            ->select('id');

        $riders = CourierAccount::query()
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('account_status', 'active')
            ->whereIn(
                'registration_application_id',
                $approvedRiderApplicationIds
            )
            ->orderBy('first_name')
            ->get();

        $selectedRider = null;

        $selectedId = (int) $request->query('rider', 0);

        if ($selectedId > 0) {
            $selectedRider = $riders->firstWhere(
                'id',
                $selectedId
            );
        }

        $selectedRider ??= $riders->first();

        $messages = collect();

        if ($selectedRider) {

            /*
            |--------------------------------------------------------------------------
            | Mark Rider Messages as Read
            |--------------------------------------------------------------------------
            */

            LogisticsMessage::query()
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->where(
                    'courier_account_id',
                    $selectedRider->id
                )
                ->where('sender_role', 'rider')
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                ]);

            /*
            |--------------------------------------------------------------------------
            | Retrieve Conversation
            |--------------------------------------------------------------------------
            */

            $messages = LogisticsMessage::query()
                ->where(
                    'logistics_account_id',
                    $logistics->id
                )
                ->where(
                    'courier_account_id',
                    $selectedRider->id
                )
                ->oldest('id')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Unread Message Counters
        |--------------------------------------------------------------------------
        */

        $unreadByRider = LogisticsMessage::query()
            ->where(
                'logistics_account_id',
                $logistics->id
            )
            ->whereIn(
                'courier_account_id',
                $riders->pluck('id')
            )
            ->where('sender_role', 'rider')
            ->whereNull('read_at')
            ->selectRaw(
                'courier_account_id, COUNT(*) AS unread_count'
            )
            ->groupBy('courier_account_id')
            ->pluck(
                'unread_count',
                'courier_account_id'
            );

        return view(
            'logistics.messages',
            compact(
                'riders',
                'selectedRider',
                'messages',
                'unreadByRider'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Send Message to Rider
    |--------------------------------------------------------------------------
    */

    public function send(
        Request $request,
        CourierAccount $rider
    ): RedirectResponse {

        $logistics = CurrentLogisticsAccount::resolve($request);

        /*
        |--------------------------------------------------------------------------
        | Validate Message
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Save Message Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $rider,
            $logistics,
            $validated
        ): void {

            /*
            |--------------------------------------------------------------------------
            | Lock Rider Account
            |--------------------------------------------------------------------------
            */

            $locked = CourierAccount::query()
                ->whereKey($rider->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Rider must belong to the current Logistics provider
             * or be an eligible unclaimed legacy Rider.
             */

            abort_unless(
                ! $locked->logistics_account_id
                    || (int) $locked->logistics_account_id
                        === (int) $logistics->id,
                404
            );

            /*
            |--------------------------------------------------------------------------
            | Verify Rider Application
            |--------------------------------------------------------------------------
            */

            $application = RegistrationApplication::query()
                ->whereKey(
                    $locked->registration_application_id
                )
                ->lockForUpdate()
                ->first();

            abort_unless(
                $application
                    && $application->role === 'rider'
                    && $application->status === 'approved'
                    && (
                        ! $application->logistics_account_id
                        || (int) $application->logistics_account_id
                            === (int) $logistics->id
                    )
                    && $locked->account_status === 'active',
                422,
                'Only approved active Riders can be messaged here.'
            );

            /*
            |--------------------------------------------------------------------------
            | Claim Eligible Legacy Rider
            |--------------------------------------------------------------------------
            */

            if (! $locked->logistics_account_id) {

                $locked->forceFill([
                    'logistics_account_id' => $logistics->id,
                ])->save();

                $application->forceFill([
                    'logistics_account_id' => $logistics->id,
                ])->save();
            }

            /*
            |--------------------------------------------------------------------------
            | Create Message
            |--------------------------------------------------------------------------
            |
            | The actual message body is stored in the existing
            | Messaging table, NOT in the Activity Logs table.
            |
            */

            LogisticsMessage::create([
                'courier_account_id' => $locked->id,

                'logistics_account_id' => $logistics->id,

                'sender_role' => 'logistics',

                'body' => $validated['body'],
            ]);

        }, 3);

        /*
        |--------------------------------------------------------------------------
        | Activity Logs Integration
        |--------------------------------------------------------------------------
        |
        | Set this flag only after the transaction commits.
        |
        | The middleware automatically resolves:
        | - Module: Messaging
        | - Action: sent
        | - Description: Message sent to Rider
        | - Subject: rider
        | - Subject ID: Authorized Rider route parameter
        |
        | Do not pass the message body or private conversation
        | contents to the Activity Logger.
        |
        */

        $request->attributes->set(
            'activity_changed',
            true
        );

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('logistics.messages', [
                'rider' => $rider->id,
            ])
            ->with(
                'success',
                'Message sent to Rider.'
            );
    }
}
