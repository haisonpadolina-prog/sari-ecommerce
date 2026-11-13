<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MobileDeviceController extends Controller
{
    private function identity(Request $request): array
    {
        $user = $request->user();
        $role = match (true) {
            $user instanceof BuyerAccount => 'buyer',
            $user instanceof CourierAccount => 'rider',
            $user instanceof LogisticsAccount => 'logistics',
            default => abort(403),
        };
        return [$role, (int) $user->id];
    }

    public function register(Request $request): JsonResponse
    {
        [$role, $userId] = $this->identity($request);
        $input = $request->validate([
            'device_id' => ['required', 'string', 'min:8', 'max:120'],
            'platform' => ['required', Rule::in(['android', 'ios'])],
            'fcm_token' => ['required', 'string', 'min:40', 'max:4096'],
            'push_enabled' => ['sometimes', 'boolean'],
        ]);
        $hash = hash('sha256', $input['fcm_token']);
        // Invalidate old owner of the token before binding it to this account.
        DB::transaction(function () use ($request, $role, $userId, $input, $hash): void {
            DB::table('sari_mobile_devices')->where('token_hash', $hash)
                ->where(function ($q) use ($role, $userId, $input): void {
                    $q->where('account_role', '!=', $role)
                        ->orWhere('account_id', '!=', $userId)
                        ->orWhere('device_id', '!=', $input['device_id']);
                })->delete();
            $identity = ['account_role' => $role, 'account_id' => $userId, 'device_id' => $input['device_id']];
            $now = now();
            // Preserve the original push cutoff, including on token refresh or app resume.
            DB::table('sari_mobile_devices')->insertOrIgnore($identity + [
                'platform' => $input['platform'],
                'personal_access_token_id' => (int) $request->user()->currentAccessToken()->id,
                'token_hash' => $hash,
                'token_ciphertext' => Crypt::encryptString($input['fcm_token']),
                'push_enabled' => $input['push_enabled'] ?? true,
                'registered_at' => $now, 'last_seen_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $device = DB::table('sari_mobile_devices')->where($identity)->lockForUpdate()->first();
            abort_unless($device, 409, 'Device registration changed; please retry.');
            DB::table('sari_mobile_devices')->where('id', $device->id)->update([
                'platform' => $input['platform'],
                'personal_access_token_id' => (int) $request->user()->currentAccessToken()->id,
                'token_hash' => $hash,
                'token_ciphertext' => Crypt::encryptString($input['fcm_token']),
                'push_enabled' => $input['push_enabled'] ?? (bool) $device->push_enabled,
                'last_seen_at' => $now, 'updated_at' => $now,
            ]);
        }, 3);
        return response()->json(['success' => true, 'message' => 'Push device registered.'])
            ->header('Cache-Control', 'no-store');
    }

    public function unregister(Request $request): JsonResponse
    {
        [$role, $userId] = $this->identity($request);
        $data = $request->validate(['device_id' => ['required', 'string', 'max:120']]);
        DB::table('sari_mobile_devices')->where('account_role', $role)->where('account_id', $userId)
            ->where('device_id', $data['device_id'])->delete();
        return response()->json(['success' => true, 'message' => 'Push device unregistered.']);
    }
}
