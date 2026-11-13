<?php

namespace App\Services\Mobile;

use App\Models\Accounts\BuyerAccount;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;

/** Adapter ONLY for existing Buyer services that inspect web session identity.
 *  The buyer ID ALWAYS comes from the verified Sanctum token, never request JSON.
 *  No web login session or session cookie is created/persisted.
 */
final class MobileBuyerSessionBridge
{
    public function attach(Request $request): void
    {
        $user = $request->user();
        abort_unless($user instanceof BuyerAccount, 403);
        $session = new Store('sari-mobile-ephemeral', new ArraySessionHandler(120));
        $session->put('is_buyer', true);
        $session->put('buyer_account_id', (int) $user->id);
        $request->setLaravelSession($session);
    }
}
