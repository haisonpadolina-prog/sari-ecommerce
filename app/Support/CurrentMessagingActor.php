<?php

namespace App\Support;

use App\Models\AdminAccount;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\SellerAccount;
use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class CurrentMessagingActor
{
    /**
     * @return array{
     *     role:string,
     *     id:int,
     *     name:string,
     *     email:?string,
     *     status:string,
     *     model:Model
     * }
     */
    public static function resolve(Request $request): array
    {
        if ($request->session()->get('is_admin')) {
            $account = AdminAccount::query()->findOrFail(
                (int) $request->session()->get('admin_account_id')
            );

            return self::payload(
                'admin',
                $account,
                $account->name ?: 'SARI Administrator',
                $account->email,
                'active'
            );
        }

        if ($request->session()->get('is_seller')) {
            $account = SellerAccount::query()->findOrFail(
                (int) $request->session()->get('seller_account_id')
            );

            return self::payload(
                'seller',
                $account,
                $account->store_name
                    ?: trim(
                        ($account->first_name ?? '') . ' ' .
                        ($account->last_name ?? '')
                    )
                    ?: 'SARI Seller',
                $account->email,
                strtolower((string) ($account->account_status ?: 'active'))
            );
        }

        if ($request->session()->get('is_buyer')) {
            $buyerId = (int) $request->session()->get(
                'buyer_account_id',
                0
            );

            if ($buyerId > 0) {
                $account = BuyerAccount::query()->findOrFail($buyerId);

                return self::payload(
                    'buyer',
                    $account,
                    trim(
                        ($account->first_name ?? '') . ' ' .
                        ($account->last_name ?? '')
                    ) ?: 'SARI Buyer',
                    $account->email,
                    strtolower((string) ($account->account_status ?: 'active'))
                );
            }

            $socialId = (int) $request->session()->get(
                'buyer_social_account_id',
                0
            );

            if ($socialId > 0) {
                $account = SocialAccount::query()->findOrFail($socialId);

                return self::payload(
                    'social_buyer',
                    $account,
                    $account->name ?: 'Social Buyer',
                    $account->email,
                    'active'
                );
            }
        }

        if ($request->session()->get('is_courier')) {
            $account = self::resolveRider($request);

            return self::payload(
                'rider',
                $account,
                trim(
                    ($account->first_name ?? '') . ' ' .
                    ($account->last_name ?? '')
                ) ?: 'SARI Rider',
                $account->email,
                strtolower((string) ($account->account_status ?: 'active'))
            );
        }

        if ($request->session()->get('is_logistics')) {
            $account = CurrentLogisticsAccount::resolve($request);

            return self::payload(
                'logistics',
                $account,
                method_exists($account, 'displayName')
                    ? $account->displayName()
                    : (
                        $account->business_name
                        ?: trim(
                            ($account->first_name ?? '') . ' ' .
                            ($account->last_name ?? '')
                        )
                        ?: 'SARI Logistics'
                    ),
                $account->email,
                strtolower((string) ($account->account_status ?: 'active'))
            );
        }

        abort(403, 'A signed-in SARI account is required for messaging.');
    }

    private static function resolveRider(Request $request): CourierAccount
    {
        $id = (int) $request->session()->get(
            'courier_account_id',
            0
        );

        $account = $id > 0
            ? CourierAccount::query()->find($id)
            : null;

        if (!$account) {
            $email = strtolower(trim((string) $request->session()->get(
                'courier_email',
                ''
            )));

            if ($email !== '') {
                $account = CourierAccount::query()
                    ->whereRaw('LOWER(email) = ?', [$email])
                    ->first();
            }
        }

        if (
            !$account
            && app()->environment(['local', 'testing'])
            && strtolower((string) $request->session()->get(
                'courier_email',
                ''
            )) === 'courier@gmail.com'
        ) {
            $account = CourierAccount::query()->firstOrCreate(
                ['email' => 'courier@gmail.com'],
                [
                    'registration_application_id' => null,
                    'logistics_account_id' => null,
                    'last_name' => 'Rider',
                    'first_name' => 'SARI',
                    'middle_initial' => null,
                    'sex' => 'Male',
                    'contact_no' => '09123456789',
                    'birthday' => '2000-01-01',
                    'age' => 26,
                    'province_code' => 'TEST',
                    'province_name' => 'Metro Manila',
                    'municipality_code' => 'TEST',
                    'municipality_name' => 'Manila',
                    'barangay_code' => 'TEST',
                    'barangay_name' => 'Test Barangay',
                    'street_address' => 'SARI Rider Test Address',
                    'password' => Hash::make('courier123'),
                    'vehicle_type' => 'Motorcycle',
                    'plate_number' => 'TEST-001',
                    'account_status' => 'active',
                    'approved_at' => now(),
                    'availability_status' => 'online',
                    'rating' => 5.00,
                ]
            );
        }

        abort_unless(
            $account,
            403,
            'A database-backed Rider account is required for messaging.'
        );

        $request->session()->put('courier_account_id', $account->id);

        return $account;
    }

    /**
     * @return array{
     *     role:string,
     *     id:int,
     *     name:string,
     *     email:?string,
     *     status:string,
     *     model:Model
     * }
     */
    private static function payload(
        string $role,
        Model $model,
        string $name,
        ?string $email,
        string $status
    ): array {
        return [
            'role' => $role,
            'id' => (int) $model->getKey(),
            'name' => trim($name),
            'email' => $email,
            'status' => $status,
            'model' => $model,
        ];
    }
}
