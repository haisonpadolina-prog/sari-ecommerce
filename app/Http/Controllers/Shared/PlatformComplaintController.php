<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;

use App\Models\Platform\PlatformComplaint;
use App\Services\Messaging\PlatformMessagingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlatformComplaintController extends Controller
{
    public function __construct(private readonly PlatformMessagingService $messaging)
    {
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $role = null;
        $identifier = null;

        foreach ([
            'buyer' => 'buyer_email',
            'seller' => 'seller_account_id',
            'courier' => 'courier_email',
            'logistics' => 'logistics_email',
        ] as $candidate => $sessionKey) {
            if ($request->session()->get('is_' . $candidate)) {
                $role = $candidate === 'courier' ? 'rider' : $candidate;
                $identifier = (string) $request->session()->get($sessionKey, '');
                break;
            }
        }

        abort_unless($role, 403, 'Authenticated account required.');

        $validated = $request->validate([
            'subject' => ['required','string','max:150'],
            'description' => ['required','string','min:10','max:3000'],
        ]);

        $result = DB::transaction(function () use ($request, $role, $identifier, $validated): array {
            $complaint = PlatformComplaint::create([
                'reporter_role' => $role,
                'reporter_identifier' => $identifier ?: null,
                'subject' => trim((string) $validated['subject']),
                'description' => trim((string) $validated['description']),
                'status' => 'open',
            ]);

            $conversation = $this->messaging->openReportConversation($request, $complaint);
            $this->messaging->send($request, $conversation, (string) $complaint->description);

            return ['complaint' => $complaint, 'conversation' => $conversation];
        });

        $complaint = $result['complaint'];

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your report was submitted to SARI Admin.',
                'complaint' => [
                    'id' => $complaint->id,
                    'subject' => $complaint->subject,
                    'status' => $complaint->status,
                ],
                'conversation' => [
                    'uuid' => $result['conversation']->uuid,
                    'type' => $result['conversation']->conversation_type,
                ],
            ], 201);
        }

        return back()
            ->with('success', 'Your issue was sent to SARI Admin and added to your support messages.')
            ->with('submitted_complaint_id', $complaint->id);
    }
}
