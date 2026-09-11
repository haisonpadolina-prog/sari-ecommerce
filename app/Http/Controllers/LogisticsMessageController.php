<?php

namespace App\Http\Controllers;

use App\Models\CourierAccount;
use App\Models\LogisticsMessage;
use App\Models\RegistrationApplication;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsMessageController extends Controller
{
    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $approvedRiderApplicationIds = RegistrationApplication::query()
            ->where('role','rider')
            ->where('status','approved')
            ->select('id');

        $riders = CourierAccount::query()
            ->where(function ($query) use ($logistics): void {
                $query->where('logistics_account_id', $logistics->id)
                    ->orWhereNull('logistics_account_id');
            })
            ->where('account_status','active')
            ->whereIn('registration_application_id', $approvedRiderApplicationIds)
            ->orderBy('first_name')
            ->get();

        $selectedRider = null;
        $selectedId = (int) $request->query('rider', 0);
        if ($selectedId > 0) {
            $selectedRider = $riders->firstWhere('id', $selectedId);
        }
        $selectedRider ??= $riders->first();

        $messages = collect();
        if ($selectedRider) {
            LogisticsMessage::query()
                ->where('courier_account_id', $selectedRider->id)
                ->where('sender_role', 'rider')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = LogisticsMessage::query()
                ->where('courier_account_id', $selectedRider->id)
                ->oldest('id')
                ->get();
        }

        $unreadByRider = LogisticsMessage::query()
            ->whereIn('courier_account_id', $riders->pluck('id'))
            ->where('sender_role','rider')
            ->whereNull('read_at')
            ->selectRaw('courier_account_id, COUNT(*) AS unread_count')
            ->groupBy('courier_account_id')
            ->pluck('unread_count','courier_account_id');

        return view('logistics.messages', compact('riders','selectedRider','messages','unreadByRider'));
    }

    public function send(Request $request, CourierAccount $rider): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        abort_unless(
            !$rider->logistics_account_id
            || (int) $rider->logistics_account_id === (int) $logistics->id,
            404
        );

        if (!$rider->logistics_account_id) {
            $rider->forceFill(['logistics_account_id' => $logistics->id])->save();

            if ($rider->registration_application_id) {
                RegistrationApplication::query()
                    ->whereKey($rider->registration_application_id)
                    ->whereNull('logistics_account_id')
                    ->update(['logistics_account_id' => $logistics->id]);
            }
        }
        $approved = RegistrationApplication::query()
            ->whereKey($rider->registration_application_id)
            ->where('role','rider')
            ->where('status','approved')
            ->exists();
        abort_unless($approved && $rider->account_status === 'active', 422, 'Only approved active Riders can be messaged here.');

        $validated = $request->validate(['body' => ['required','string','max:3000']]);

        LogisticsMessage::create([
            'courier_account_id' => $rider->id,
            'logistics_account_id' => $logistics->id,
            'sender_role' => 'logistics',
            'body' => $validated['body'],
        ]);

        return redirect()->route('logistics.messages', ['rider' => $rider->id])->with('success', 'Message sent to Rider.');
    }
}
