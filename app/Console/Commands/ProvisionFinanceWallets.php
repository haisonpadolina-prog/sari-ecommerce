<?php

namespace App\Console\Commands;

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Services\Finance\WalletService;
use Illuminate\Console\Command;

class ProvisionFinanceWallets extends Command
{
    protected $signature = 'sari:finance:provision-wallets';

    protected $description = 'Idempotently prepare internal PHP wallets for existing marketplace accounts.';

    public function handle(WalletService $wallets): int
    {
        $wallets->platformWallet();
        $counts = [];

        foreach ([
            'buyer' => BuyerAccount::class,
            'social_buyer' => SocialAccount::class,
            'seller' => SellerAccount::class,
            'logistics' => LogisticsAccount::class,
            'courier' => CourierAccount::class,
        ] as $type => $modelClass) {
            $count = 0;

            $modelClass::query()->select('id')->chunkById(200, function ($accounts) use (
                $wallets,
                $type,
                &$count
            ): void {
                foreach ($accounts as $account) {
                    $wallets->wallet($type, (int) $account->id);
                    $count++;
                }
            });

            $counts[$type] = $count;
        }

        $this->info('SARI platform wallet ready. All discovered account wallets exist.');

        foreach ($counts as $type => $count) {
            $this->line(ucfirst(str_replace('_', ' ', $type)).': '.$count.' account(s)');
        }

        $this->warn('No balances were credited. COD Buyers do not earn or spend wallet funds.');

        return self::SUCCESS;
    }
}
