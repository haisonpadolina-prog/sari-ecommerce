<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\BuyerAddress;
use App\Services\Shared\AccountProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class MobileBuyerAccountController extends Controller
{
    private const MAX_ADDRESSES = 5;

    public function show(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $buyer->load('addresses');

        return response()->json([
            'success' => true,
            'data' => [
                'profile' => $this->profile($buyer),
                'addresses' => $buyer->addresses
                    ->sortByDesc('is_default')
                    ->values()
                    ->map(fn (BuyerAddress $address) => $this->address($address)),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    public function update(Request $request, AccountProfileService $profiles): JsonResponse
    {
        $buyer = $this->buyer($request);

        $validated = $request->validate([
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'first_name' => ['required', 'string', 'min:2', 'max:50', "regex:/^[\pL\s.'\-]+$/u"],
            'middle_initial' => ['nullable', 'string', 'max:2', 'regex:/^\pL{1,2}$/u'],
            'last_name' => ['required', 'string', 'min:2', 'max:50', "regex:/^[\pL\s.'\-]+$/u"],
            'sex' => ['nullable', Rule::in(['Male', 'Female'])],
            'birthday' => [
                'required', 'date', 'after_or_equal:1900-01-01',
                'before_or_equal:'.now()->subYears(18)->toDateString(),
            ],
            'contact_no' => ['required', 'string', 'regex:/^(?:09\d{9}|\+639\d{9})$/'],
            'street_address' => ['required', 'string', 'max:500'],
            'remove_profile_image' => ['nullable', 'boolean'],
        ]);

        unset($validated['profile_image'], $validated['remove_profile_image']);
        $birthday = Carbon::parse($validated['birthday'])->startOfDay();
        $validated['first_name'] = $this->cleanName($validated['first_name']);
        $validated['last_name'] = $this->cleanName($validated['last_name']);
        $validated['middle_initial'] = filled($validated['middle_initial'] ?? null)
            ? mb_strtoupper(trim((string) $validated['middle_initial']))
            : null;
        $validated['birthday'] = $birthday->toDateString();
        $validated['age'] = $birthday->age;
        $validated['contact_no'] = $this->normalizeMobile($validated['contact_no']);
        $validated['street_address'] = trim($validated['street_address']);

        if (blank($validated['sex'] ?? null)) {
            unset($validated['sex']);
        }

        $profiles->save($buyer, $validated, $request);

        return response()->json([
            'success' => true,
            'message' => 'Buyer account updated.',
            'data' => $this->profile($buyer->fresh()),
        ]);
    }

    public function password(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if (!Hash::check($validated['current_password'], (string) $buyer->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $buyer->forceFill(['password' => Hash::make($validated['password'])])->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    public function addresses(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);
        $addresses = $buyer->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses->map(fn (BuyerAddress $address) => $this->address($address))->values(),
        ]);
    }

    public function storeAddress(Request $request): JsonResponse
    {
        $buyer = $this->buyer($request);

        if ($buyer->addresses()->count() >= self::MAX_ADDRESSES) {
            throw ValidationException::withMessages([
                'address' => 'You can save up to 5 delivery addresses.',
            ]);
        }

        $validated = $this->validateAddress($request);
        $makeDefault = $request->boolean('is_default') || !$buyer->addresses()->exists();

        $address = DB::transaction(function () use ($buyer, $validated, $makeDefault): BuyerAddress {
            if ($makeDefault) {
                $buyer->addresses()->update(['is_default' => false]);
            }

            return $buyer->addresses()->create(array_merge($validated, [
                'is_default' => $makeDefault,
            ]));
        });

        return response()->json([
            'success' => true,
            'message' => 'Delivery address added.',
            'data' => $this->address($address),
        ], 201);
    }

    public function updateAddress(Request $request, BuyerAddress $address): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardAddress($buyer, $address);
        $validated = $this->validateAddress($request);
        $makeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($buyer, $address, $validated, $makeDefault): void {
            if ($makeDefault) {
                $buyer->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update(array_merge($validated, [
                'is_default' => $makeDefault ? true : (bool) $address->is_default,
            ]));
        });

        return response()->json([
            'success' => true,
            'message' => 'Delivery address updated.',
            'data' => $this->address($address->fresh()),
        ]);
    }

    public function defaultAddress(Request $request, BuyerAddress $address): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardAddress($buyer, $address);

        DB::transaction(function () use ($buyer, $address): void {
            $buyer->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Default delivery address updated.',
        ]);
    }

    public function destroyAddress(Request $request, BuyerAddress $address): JsonResponse
    {
        $buyer = $this->buyer($request);
        $this->guardAddress($buyer, $address);
        $wasDefault = (bool) $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $buyer->addresses()->latest('updated_at')->first()?->update(['is_default' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Delivery address removed.',
        ]);
    }

    private function buyer(Request $request): BuyerAccount
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();
        return $buyer;
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'contact_no' => ['required', 'string', 'regex:/^(?:09\d{9}|\+639\d{9})$/'],
            'street_address' => ['required', 'string', 'max:500'],
            'barangay_name' => ['required', 'string', 'max:150'],
            'municipality_name' => ['required', 'string', 'max:150'],
            'province_name' => ['required', 'string', 'max:150'],
            'postal_code' => ['nullable', 'regex:/^\d{4}$/'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    private function guardAddress(BuyerAccount $buyer, BuyerAddress $address): void
    {
        abort_unless((int) $address->buyer_account_id === (int) $buyer->id, 404);
    }

    private function profile(BuyerAccount $buyer): array
    {
        return [
            'id' => (int) $buyer->id,
            'first_name' => $buyer->first_name,
            'middle_initial' => $buyer->middle_initial,
            'last_name' => $buyer->last_name,
            'full_name' => trim($buyer->first_name.' '.$buyer->last_name),
            'sex' => $buyer->sex,
            'email' => $buyer->email,
            'contact_no' => $buyer->contact_no,
            'birthday' => $buyer->birthday?->toDateString(),
            'province_name' => $buyer->province_name,
            'municipality_name' => $buyer->municipality_name,
            'barangay_name' => $buyer->barangay_name,
            'street_address' => $buyer->street_address,
            'account_status' => $buyer->account_status,
            'profile_image_url' => $buyer->profile_image_path
                ? Storage::disk('public')->url($buyer->profile_image_path)
                : null,
        ];
    }

    private function address(BuyerAddress $address): array
    {
        return [
            'id' => (int) $address->id,
            'label' => $address->label,
            'recipient_name' => $address->recipient_name,
            'contact_no' => $address->contact_no,
            'street_address' => $address->street_address,
            'barangay_name' => $address->barangay_name,
            'municipality_name' => $address->municipality_name,
            'province_name' => $address->province_name,
            'postal_code' => $address->postal_code,
            'full_address' => $address->fullAddress(),
            'is_default' => (bool) $address->is_default,
        ];
    }

    private function normalizeMobile(string $value): string
    {
        $value = trim($value);
        return str_starts_with($value, '+63')
            ? '0'.substr($value, 3)
            : (preg_replace('/\D+/', '', $value) ?? '');
    }

    private function cleanName(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value)) ?: trim($value);
    }
}
