<?php

namespace App\Http\Controllers;

use App\Models\ComplianceMessage;
use App\Models\SellerAccount;
use Illuminate\Http\Request;

class SellerComplianceMessageController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->session()->get('is_seller')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $seller = SellerAccount::findOrFail(
            $request->session()->get('seller_account_id')
        );

        ComplianceMessage::create([
            'seller_account_id' => $seller->id,
            'sender_role' => 'seller',
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Your message was sent to the SARI administrator.');
    }
}
