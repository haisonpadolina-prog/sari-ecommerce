<?php

namespace App\Http\Controllers;

use App\Models\PlatformComplaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlatformComplaintController extends Controller
{
    public function store(Request $request): RedirectResponse
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

        PlatformComplaint::create([
            'reporter_role' => $role,
            'reporter_identifier' => $identifier ?: null,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return back()->with('success', 'Your issue was submitted to the SARI Administrator.');
    }
}
