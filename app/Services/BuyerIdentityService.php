<?php

namespace App\Services;

use App\Models\BuyerAccount;
use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BuyerIdentityService
{
    public function guard(Request $request): void
    {
        abort_unless(
            (bool) $request->session()->get('is_buyer'),
            403,
            'Buyer session required.'
        );

        abort_if(
            !$this->accountId($request) && !$this->socialId($request),
            403,
            'Buyer identity is missing from the session.'
        );
    }

    public function accountId(Request $request): ?int
    {
        $id = (int) $request->session()->get('buyer_account_id', 0);

        return $id > 0 ? $id : null;
    }

    public function socialId(Request $request): ?int
    {
        $id = (int) $request->session()->get('buyer_social_account_id', 0);

        return $id > 0 ? $id : null;
    }

    public function columns(Request $request): array
    {
        $this->guard($request);

        return [
            'buyer_account_id' => $this->accountId($request),
            'buyer_social_account_id' => $this->socialId($request),
        ];
    }

    public function apply(Builder $query, Request $request): Builder
    {
        $this->guard($request);

        if ($accountId = $this->accountId($request)) {
            return $query->where('buyer_account_id', $accountId);
        }

        return $query->where('buyer_social_account_id', $this->socialId($request));
    }

    public function key(Request $request): string
    {
        return $this->accountId($request)
            ? 'account-' . $this->accountId($request)
            : 'social-' . $this->socialId($request);
    }

    public function account(Request $request): ?BuyerAccount
    {
        $id = $this->accountId($request);

        return $id ? BuyerAccount::query()->find($id) : null;
    }

    public function social(Request $request): ?SocialAccount
    {
        $id = $this->socialId($request);

        return $id ? SocialAccount::query()->find($id) : null;
    }

    public function snapshot(Request $request): array
    {
        $this->guard($request);

        if ($buyer = $this->account($request)) {
            $name = trim($buyer->first_name . ' ' . $buyer->last_name);
            $address = collect([
                $buyer->street_address,
                $buyer->barangay_name,
                $buyer->municipality_name,
                $buyer->province_name,
                'Philippines',
            ])->filter(fn ($part) => filled($part))->implode(', ');

            return [
                'name' => $name !== '' ? $name : 'SARI Buyer',
                'email' => (string) $buyer->email,
                'phone' => (string) ($buyer->contact_no ?? ''),
                'address' => $address,
            ];
        }

        $social = $this->social($request);

        return [
            'name' => trim((string) ($social?->name ?: $request->session()->get('buyer_name', 'SARI Buyer'))),
            'email' => trim((string) ($social?->email ?: $request->session()->get('buyer_email', ''))),
            'phone' => '',
            'address' => '',
        ];
    }
}
