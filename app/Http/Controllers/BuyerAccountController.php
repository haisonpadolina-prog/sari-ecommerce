<?php

namespace App\Http\Controllers;

use App\Services\BuyerIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerAccountController extends Controller
{
    public function __construct(private readonly BuyerIdentityService $identity)
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $this->identity->guard($request);

        return view('buyer.account', [
            'buyerAccount' => $this->identity->account($request),
            'socialAccount' => $this->identity->social($request),
            'profile' => $this->identity->snapshot($request),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->identity->guard($request);

        if ($buyer = $this->identity->account($request)) {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'contact_no' => ['required', 'string', 'max:30'],
                'street_address' => ['required', 'string', 'max:500'],
            ]);

            $buyer->update($validated);

            $request->session()->put([
                'buyer_name' => trim($buyer->first_name . ' ' . $buyer->last_name),
                'buyer_email' => $buyer->email,
            ]);

            return back()->with('success', 'Buyer account updated.');
        }

        $social = $this->identity->social($request);
        abort_unless($social, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
        ]);

        $social->update(['name' => $validated['name']]);
        $request->session()->put('buyer_name', $social->name);

        return back()->with('success', 'Social buyer display name updated. Shipping details are entered during checkout.');
    }
}
