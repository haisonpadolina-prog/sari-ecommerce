<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Finance\RiderEarning;
use App\Services\Shared\AccountProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class MobileRiderProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->profile($this->rider($request)),
        ])->header('Cache-Control', 'no-store');
    }

    public function update(Request $request, AccountProfileService $profiles): JsonResponse
    {
        $rider = $this->rider($request);

        $validated = $request->validate([
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'contact_no' => ['required', 'string', 'max:30'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('courier_accounts', 'email')->ignore($rider->id),
            ],
            'street_address' => ['required', 'string', 'max:1000'],
            'vehicle_type' => ['required', 'string', 'max:120'],
            'vehicle_model' => ['nullable', 'string', 'max:120'],
            'plate_number' => ['required', 'string', 'max:120'],
            'license_number' => ['nullable', 'string', 'max:120'],
            'remove_profile_image' => ['nullable', 'boolean'],
        ]);

        unset($validated['profile_image'], $validated['remove_profile_image']);
        $validated['email'] = strtolower(trim($validated['email']));

        $profiles->save($rider, $validated, $request);

        return response()->json([
            'success' => true,
            'message' => 'Rider profile updated.',
            'data' => $this->profile($rider->fresh()),
        ]);
    }

    public function availability(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'availability_status' => ['required', Rule::in(['online', 'offline'])],
        ]);

        $rider->forceFill([
            'availability_status' => $validated['availability_status'],
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Rider availability updated.',
            'data' => ['availability_status' => $rider->availability_status],
        ]);
    }

    public function password(Request $request): JsonResponse
    {
        $rider = $this->rider($request);
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if (!Hash::check($validated['current_password'], (string) $rider->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $rider->forceFill(['password' => Hash::make($validated['password'])])->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    private function profile(CourierAccount $rider): array
    {
        $completed = RiderEarning::query()
            ->where('courier_account_id', $rider->id)
            ->whereIn('earning_type', ['direct_delivery', 'last_mile_leg'])
            ->count();

        return [
            'id' => (int) $rider->id,
            'first_name' => $rider->first_name,
            'last_name' => $rider->last_name,
            'full_name' => trim($rider->first_name.' '.$rider->last_name),
            'email' => $rider->email,
            'contact_no' => $rider->contact_no,
            'province_name' => $rider->province_name,
            'municipality_name' => $rider->municipality_name,
            'barangay_name' => $rider->barangay_name,
            'street_address' => $rider->street_address,
            'vehicle_type' => $rider->vehicle_type,
            'vehicle_model' => $rider->vehicle_model,
            'plate_number' => $rider->plate_number,
            'license_number' => $rider->license_number,
            'license_expiry_date' => $rider->license_expiry_date?->toDateString(),
            'availability_status' => $rider->availability_status === 'online' ? 'online' : 'offline',
            'rating' => (float) ($rider->rating ?? 5),
            'completed_deliveries' => $completed,
            'logistics_account_id' => $rider->logistics_account_id ? (int) $rider->logistics_account_id : null,
            'profile_image_url' => $rider->profile_image_path
                ? Storage::disk('public')->url($rider->profile_image_path)
                : null,
        ];
    }

    private function rider(Request $request): CourierAccount
    {
        /** @var CourierAccount $rider */
        $rider = $request->user();
        return $rider;
    }
}
