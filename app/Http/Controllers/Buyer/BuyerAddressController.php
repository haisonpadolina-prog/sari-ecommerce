<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAddress;
use App\Models\Accounts\BuyerAccount;
use App\Services\Buyer\BuyerIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BuyerAddressController extends Controller
{
    private const MAX_ADDRESSES = 5;

    public function __construct(private readonly BuyerIdentityService $identity)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        $buyer = $this->buyer($request);

        if ($buyer->addresses()->count() >= self::MAX_ADDRESSES) {
            throw ValidationException::withMessages([
                'address' => 'You can save up to 5 delivery addresses.',
            ]);
        }

        $validated = $this->validateAddress($request);
        $makeDefault = $request->boolean('is_default') || !$buyer->addresses()->exists();

        DB::transaction(function () use ($buyer, $validated, $makeDefault): void {
            if ($makeDefault) {
                $buyer->addresses()->update(['is_default' => false]);
            }

            $buyer->addresses()->create(array_merge($validated, [
                'is_default' => $makeDefault,
            ]));
        });

        return back()->with('success', 'Delivery address added successfully.');
    }

    public function update(
        Request $request,
        BuyerAddress $address
    ): RedirectResponse {
        $buyer = $this->buyer($request);
        $this->guardOwnership($buyer, $address);

        $validated = $this->validateAddress($request);
        $makeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($buyer, $address, $validated, $makeDefault): void {
            if ($makeDefault) {
                $buyer->addresses()
                    ->where('id', '!=', $address->getKey())
                    ->update(['is_default' => false]);
            }

            $address->update(array_merge($validated, [
                'is_default' => $makeDefault ? true : (bool) $address->is_default,
            ]));
        });

        return back()->with('success', 'Delivery address updated successfully.');
    }

    public function setDefault(
        Request $request,
        BuyerAddress $address
    ): RedirectResponse {
        $buyer = $this->buyer($request);
        $this->guardOwnership($buyer, $address);

        DB::transaction(function () use ($buyer, $address): void {
            $buyer->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return back()->with('success', 'Active delivery address updated.');
    }

    public function useRegistration(Request $request): RedirectResponse
    {
        $buyer = $this->buyer($request);

        $buyer->addresses()->update(['is_default' => false]);

        return back()->with('success', 'Registered address is now active.');
    }

    public function destroy(
        Request $request,
        BuyerAddress $address
    ): RedirectResponse {
        $buyer = $this->buyer($request);
        $this->guardOwnership($buyer, $address);

        $wasActive = (bool) $address->is_default;

        DB::transaction(function () use ($address): void {
            $address->delete();
        });

        return back()->with(
            'success',
            $wasActive
                ? 'Delivery address removed. Registered address is now active.'
                : 'Delivery address removed.'
        );
    }

    private function buyer(Request $request): BuyerAccount
    {
        $this->identity->guard($request);

        $buyer = $this->identity->account($request);

        abort_unless($buyer, 404, 'Buyer account not found.');

        return $buyer;
    }

    private function guardOwnership(
        BuyerAccount $buyer,
        BuyerAddress $address
    ): void {
        abort_unless(
            (int) $address->buyer_account_id === (int) $buyer->getKey(),
            404
        );
    }

    private function validateAddress(Request $request): array
    {
        $validated = $request->validateWithBag('address', [
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => [
                'required',
                'string',
                'min:2',
                'max:150',
                'regex:/^[\pL\s.\'\-]+$/u',
            ],
            'contact_no' => ['required', 'string', 'regex:/^(?:09\d{9}|\+639\d{9})$/'],
            'street_address' => ['required', 'string', 'max:500'],
            'barangay_name' => ['required', 'string', 'max:150'],
            'municipality_name' => ['required', 'string', 'max:150'],
            'province_name' => ['required', 'string', 'max:150'],
            'postal_code' => ['nullable', 'regex:/^\d{4}$/'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'recipient_name.regex' => 'Recipient name may only contain letters, spaces, apostrophes, periods, and hyphens.',
            'contact_no.regex' => 'Enter a Philippine mobile number as 09XXXXXXXXX or +639XXXXXXXXX.',
            'postal_code.regex' => 'Postal code must contain exactly four digits.',
        ]);

        foreach ([
            'label',
            'recipient_name',
            'contact_no',
            'street_address',
            'barangay_name',
            'municipality_name',
            'province_name',
            'postal_code',
        ] as $field) {
            if (array_key_exists($field, $validated) && is_string($validated[$field])) {
                $validated[$field] = trim($validated[$field]);
            }
        }

        $validated['contact_no'] = $this->normalizeMobile((string) $validated['contact_no']);

        unset($validated['is_default']);

        return $validated;
    }

    private function normalizeMobile(string $value): string
    {
        $value = trim($value);

        if (str_starts_with($value, '+63')) {
            return '0' . substr($value, 3);
        }

        return preg_replace('/\D+/', '', $value) ?? '';
    }
}
