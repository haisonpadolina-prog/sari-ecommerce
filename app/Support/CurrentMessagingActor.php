<?php

namespace App\Support;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
                    strtolower((string) ($account->account_status ?: 'active'))
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
        $normalizedStatus = strtolower(trim($status ?: 'active'));

        if (
            $role !== 'admin'
            && in_array($normalizedStatus, ['banned', 'deactivated'], true)
        ) {
            abort(
                403,
                $normalizedStatus === 'banned'
                    ? 'This SARI account has been banned by an administrator.'
                    : 'This SARI account is currently suspended by an administrator.'
            );
        }

        return [
            'role' => $role,
            'id' => (int) $model->getKey(),
            'name' => trim($name),
            'email' => $email,
            'status' => $normalizedStatus,
            'model' => $model,
        ];
    }
}
