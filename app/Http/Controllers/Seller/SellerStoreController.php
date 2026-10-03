<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SellerStoreController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $this->seller($request);

        return view('seller.store', [
            'seller' => $seller,
            'profilePhotoUrl' => filled($seller->profile_image_path)
                && Storage::disk('public')->exists((string) $seller->profile_image_path)
                    ? route('seller.account.profile-photo')
                    : null,
        ]);
    }

    /**
     * Public storefront and fulfillment settings only.
     * Verified registration identity/address fields are never overwritten here.
     */
    public function update(Request $request): RedirectResponse
    {
        $seller = $this->seller($request);

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:150'],
            'store_description' => ['nullable', 'string', 'max:2000'],
            'store_phone' => ['nullable', 'string', 'max:40'],
            'store_public_email' => ['nullable', 'email:rfc', 'max:255'],
            'store_status' => ['required', 'in:open,paused'],
            'pickup_address' => ['required', 'string', 'max:1000'],
            'pickup_instructions' => ['nullable', 'string', 'max:1200'],
        ]);

        $seller->forceFill([
            'store_name' => trim($validated['store_name']),
            'store_description' => filled($validated['store_description'] ?? null)
                ? trim((string) $validated['store_description'])
                : null,
            'store_phone' => filled($validated['store_phone'] ?? null)
                ? trim((string) $validated['store_phone'])
                : null,
            'store_public_email' => filled($validated['store_public_email'] ?? null)
                ? strtolower(trim((string) $validated['store_public_email']))
                : null,
            'store_status' => $validated['store_status'],
            'pickup_address' => trim($validated['pickup_address']),
            'pickup_instructions' => filled($validated['pickup_instructions'] ?? null)
                ? trim((string) $validated['pickup_instructions'])
                : null,
        ])->save();

        return back()->with('success', 'Store settings updated successfully.');
    }

    private function seller(Request $request): SellerAccount
    {
        $seller = $request->attributes->get('sellerAccount');

        if ($seller instanceof SellerAccount) {
            return $seller;
        }

        abort_unless($request->session()->get('is_seller'), 403);

        return SellerAccount::query()->findOrFail(
            (int) $request->session()->get('seller_account_id')
        );
    }
}
