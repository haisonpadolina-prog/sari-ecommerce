<?php

namespace App\Console\Commands;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Finance\FinanceWalletEntry;
use App\Services\Finance\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class SeedMarketplaceEnterpriseDemo extends Command
{
    protected $signature = 'sari:seed-enterprise-demo {--dry-run : Validate without changing accounts, files, or wallets} {--allow-nonlocal : Intentionally seed synthetic accounts and ledger credits outside local/testing}';

    protected $description = 'Create 150 fictional Buyer profiles and one-time, labeled demo credits for platform, sellers and logistics.';

    private const MARKER = 'SARI_ENTERPRISE_DEMO_V1';

    private const AMOUNTS = ['platform' => 100000000, 'seller' => 500000, 'logistics' => 1000000];

    public function handle(WalletService $wallets): int
    {
        if (! app()->environment(['local', 'testing']) && ! $this->option('allow-nonlocal')) {
            $this->error('Demo-only data changes financial ledger totals. Use --allow-nonlocal only for an intentional demonstration environment.');

            return self::FAILURE;
        }
        $lock = null;
        try {
            $profiles = $this->profiles();
            $this->preflight($profiles);
            $sellerIds = SellerAccount::query()->orderBy('id')->pluck('id')->all();
            $logisticsIds = LogisticsAccount::query()->orderBy('id')->pluck('id')->all();
            $this->table(['Role', 'Accounts', 'One-time demo credit per wallet (PHP)'], [
                ['Platform (shared Admin wallet)', 1, '100,000,000.00'],
                ['Seller', count($sellerIds), '500,000.00'],
                ['Logistics', count($logisticsIds), '1,000,000.00'],
                ['Managed Buyer target', 150, '0.00 (COD only)'],
            ]);
            if ($this->option('dry-run')) {
                $this->info('Preflight passed. No accounts, files, wallets or credits changed.');

                return self::SUCCESS;
            }
            $directory = storage_path('app/private/sari-enterprise-demo');
            if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
                throw new RuntimeException('Cannot create the private demo credential directory.');
            }
            @chmod($directory, 0700);
            $lock = fopen($directory.'/seed.lock', 'c');
            if (! $lock || ! flock($lock, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Another enterprise demo seed is running.');
            }
            @chmod($directory.'/seed.lock', 0600);
            $this->preflight($profiles);
            $plan = $this->credentials($directory, $profiles);
            foreach ($profiles as $profile) {
                $relative = 'demo-buyers-v1/'.$profile['asset'];
                $disk = Storage::disk('public');
                if ($disk->exists($relative)) {
                    if (hash('sha256', $disk->get($relative)) !== $profile['sha256']) {
                        throw new RuntimeException('A managed avatar path contains a custom file. No database changes were made.');
                    }
                } elseif (! $disk->put($relative, file_get_contents(database_path('data/sari_buyer_demo/avatars/'.$profile['asset'])))) {
                    throw new RuntimeException('Could not install a demo profile image.');
                }
            }
            $created = DB::transaction(function () use ($profiles, $plan, $wallets, $sellerIds, $logisticsIds): int {
                $platform = $wallets->platformWallet();
                DB::table('finance_wallets')->where('id', $platform->id)->lockForUpdate()->first();
                $this->preflight($profiles);
                $created = 0;
                foreach ($profiles as $profile) {
                    if (DB::table('buyer_accounts')->where('email', $profile['email'])->exists()) {
                        continue;
                    }
                    $fields = array_intersect_key($profile, array_flip(['first_name', 'last_name', 'sex', 'birthday', 'email', 'province_code', 'province_name', 'municipality_code', 'municipality_name', 'barangay_code', 'barangay_name']));
                    $fields += [
                        'middle_initial' => null, 'contact_no' => '00000000000',
                        'age' => Carbon::parse($profile['birthday'])->age,
                        'street_address' => 'SARI DEMO ADDRESS - fictional residence; not a delivery destination',
                        'password' => Hash::make($plan[$profile['email']]),
                        'profile_image_path' => 'demo-buyers-v1/'.$profile['asset'],
                        'approved_at' => now(), 'created_at' => now(), 'updated_at' => now(),
                    ];
                    $applicationId = DB::table('registration_applications')->insertGetId($fields + [
                        'role' => 'buyer', 'status' => 'approved', 'reviewed_at' => now(), 'admin_note' => self::MARKER,
                    ]);
                    $buyerId = DB::table('buyer_accounts')->insertGetId($fields + [
                        'registration_application_id' => $applicationId, 'account_status' => 'active',
                    ]);
                    $wallets->wallet('buyer', $buyerId);
                    $created++;
                }
                foreach (['platform' => [WalletService::PLATFORM_OWNER_ID], 'seller' => $sellerIds, 'logistics' => $logisticsIds] as $type => $ids) {
                    foreach ($ids as $id) {
                        $wallets->credit(
                            $wallets->wallet($type, (int) $id), self::AMOUNTS[$type], 'demo_seed_credit',
                            self::MARKER.':'.$type.':'.$id, null, 'demo_seed', null,
                            'SARI synthetic demo capital — not bank funds, sales earnings, COD receipts or an external transfer.',
                            ['demo' => true, 'seed' => self::MARKER, 'funding_origin' => 'synthetic_test_credit']
                        );
                    }
                }

                return $created;
            }, 3);
            $csv = $this->export($directory, $profiles, $plan);
            $this->info('Created '.$created.' Buyers. Existing managed accounts and passwords were preserved.');
            $this->line('Initial login credentials: '.$csv);
            $this->line('150 profiles use public storage. Existing custom profile photos are not changed.');
            $this->warn('Demo credits are in the existing ledger and affect wallet totals. They are not actual bank deposits or COD collections. Rerunning does not add the same credits again.');
            $this->line('No orders, payments, remittances, payouts, campaign budgets, emails or SMS were created.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error(get_class($exception) === RuntimeException::class ? $exception->getMessage() : 'Demo seed stopped because of a database/filesystem error. The database transaction is atomic. If credential export failed after commit, rerun to export without duplicate credits.');

            return self::FAILURE;
        } finally {
            if (is_resource($lock)) {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }
    }

    private function profiles(): array
    {
        $path = database_path('data/sari_buyer_demo/profiles.json');
        if (! is_file($path)) {
            throw new RuntimeException('Missing bundled Buyer profiles. Install the complete package.');
        }
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $profiles = $data['profiles'] ?? [];
        if (($data['marker'] ?? '') !== self::MARKER || count($profiles) !== 150 || count(array_unique(array_column($profiles, 'email'))) !== 150) {
            throw new RuntimeException('Expected exactly 150 unique managed Buyer profiles.');
        }
        foreach ($profiles as $index => $profile) {
            $slot = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            if (($profile['email'] ?? '') !== 'buyer.demo.'.$slot.'@example.test' || ($profile['asset'] ?? '') !== 'buyer-'.$slot.'.png') {
                throw new RuntimeException('Invalid managed Buyer identity or avatar path.');
            }
            foreach (['first_name', 'last_name', 'sex', 'birthday', 'province_code', 'province_name', 'municipality_code', 'municipality_name', 'barangay_code', 'barangay_name'] as $field) {
                if (empty($profile[$field])) {
                    throw new RuntimeException('Incomplete Buyer profile metadata.');
                }
            }
            $asset = database_path('data/sari_buyer_demo/avatars/'.$profile['asset']);
            if (! is_file($asset) || hash_file('sha256', $asset) !== ($profile['sha256'] ?? '')) {
                throw new RuntimeException('Missing or changed Buyer avatar asset.');
            }
        }

        return $profiles;
    }

    private function preflight(array $profiles): void
    {
        foreach ([
            'buyer_accounts' => ['registration_application_id', 'profile_image_path', 'deleted_at'],
            'registration_applications' => ['profile_image_path', 'admin_note'],
            'finance_wallets' => ['owner_type', 'owner_id', 'available_balance', 'lifetime_credits'],
            'finance_wallet_entries' => ['idempotency_key', 'metadata', 'category'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table) || ! Schema::hasColumns($table, $columns)) {
                throw new RuntimeException('Required existing project migrations are missing for '.$table.'.');
            }
        }
        $emails = array_column($profiles, 'email');
        foreach (['seller_accounts', 'logistics_accounts', 'courier_accounts', 'admin_accounts', 'social_accounts'] as $table) {
            if (Schema::hasTable($table) && DB::table($table)->whereIn('email', $emails)->exists()) {
                throw new RuntimeException('A planned Buyer email belongs to another account role. Refusing to adopt it.');
            }
        }
        if (Schema::hasTable('account_email_bans') && DB::table('account_email_bans')->whereIn('email', $emails)->whereNull('unbanned_at')->exists()) {
            throw new RuntimeException('A planned demo email is banned. Existing bans will not be changed.');
        }
        $buyers = DB::table('buyer_accounts')->whereIn('email', $emails)->get()->keyBy('email');
        $applications = DB::table('registration_applications')->whereIn('email', $emails)->get()->keyBy('email');
        if (DB::table('registration_applications')->where('admin_note', self::MARKER)->whereNotIn('email', $emails)->exists()) {
            throw new RuntimeException('A managed registration email was changed. Resolve that record before rerunning.');
        }
        foreach ($emails as $email) {
            $buyer = $buyers->get($email);
            $application = $applications->get($email);
            if (! $buyer && ! $application) {
                continue;
            }
            if (! $buyer || ! $application || $buyer->deleted_at !== null || $application->role !== 'buyer' || $application->admin_note !== self::MARKER || (int) $buyer->registration_application_id !== (int) $application->id) {
                throw new RuntimeException('A planned Buyer identity is unrelated, archived, or inconsistent. Existing accounts will not be overwritten.');
            }
        }
        $expected = [];
        foreach (['platform' => [WalletService::PLATFORM_OWNER_ID], 'seller' => SellerAccount::query()->pluck('id')->all(), 'logistics' => LogisticsAccount::query()->pluck('id')->all()] as $type => $ids) {
            foreach ($ids as $id) {
                $expected[self::MARKER.':'.$type.':'.$id] = ['type' => $type, 'id' => (int) $id, 'amount' => self::AMOUNTS[$type]];
            }
        }
        foreach (FinanceWalletEntry::query()->with('wallet')->whereIn('idempotency_key', array_keys($expected))->get() as $entry) {
            $target = $expected[$entry->idempotency_key];
            if ($entry->category !== 'demo_seed_credit' || $entry->entry_type !== 'credit' || (float) $entry->amount !== (float) $target['amount'] || ! ($entry->metadata['demo'] ?? false) || $entry->wallet?->owner_type !== $target['type'] || (int) $entry->wallet?->owner_id !== $target['id']) {
                throw new RuntimeException('An existing demo ledger key has conflicting details. No additional credits applied.');
            }
        }
    }

    private function credentials(string $directory, array $profiles): array
    {
        $path = $directory.'/initial-credentials.json';
        if (! is_file($path)) {
            if (DB::table('buyer_accounts')->whereIn('email', array_column($profiles, 'email'))->exists()) {
                throw new RuntimeException('Private initial credentials are missing. Restore their backup; existing passwords will not be reset.');
            }
            $plan = [];
            foreach ($profiles as $profile) {
                $plan[$profile['email']] = 'Sari!'.bin2hex(random_bytes(18));
            }
            $this->writePrivate($path, json_encode(['marker' => self::MARKER, 'passwords' => $plan], JSON_THROW_ON_ERROR));
        }
        $saved = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $plan = $saved['passwords'] ?? [];
        if (($saved['marker'] ?? '') !== self::MARKER || count($plan) !== 150) {
            throw new RuntimeException('Invalid private credential plan. Restore its backup.');
        }
        foreach ($profiles as $profile) {
            if (! is_string($plan[$profile['email']] ?? null) || strlen($plan[$profile['email']]) < 24) {
                throw new RuntimeException('Incomplete private credential plan.');
            }
        }

        return $plan;
    }

    private function writePrivate(string $path, string $bytes): void
    {
        $file = fopen($path, 'x');
        if (! $file) {
            throw new RuntimeException('Cannot create private credential file.');
        }
        @chmod($path, 0600);
        try {
            if (fwrite($file, $bytes) !== strlen($bytes) || ! fflush($file)) {
                throw new RuntimeException('Could not persist private credentials. Restore or remove the incomplete file before retrying.');
            }
        } finally {
            fclose($file);
        }
    }

    private function export(string $directory, array $profiles, array $plan): string
    {
        $path = $directory.'/initial-login-credentials.csv';
        $file = fopen('php://temp', 'w+');
        if (! $file) {
            throw new RuntimeException('Cannot prepare credential export.');
        }
        try {
            fputcsv($file, ['name', 'email', 'initial_password', 'city', 'province'], ',', '"', '');
            foreach ($profiles as $profile) {
                if (fputcsv($file, [$profile['first_name'].' '.$profile['last_name'], $profile['email'], $plan[$profile['email']], $profile['municipality_name'], $profile['province_name']], ',', '"', '') === false) {
                    throw new RuntimeException('Could not complete credential export.');
                }
            }
            rewind($file);
            $bytes = stream_get_contents($file);
        } finally {
            fclose($file);
        }
        $temp = $directory.'/export-'.bin2hex(random_bytes(8)).'.tmp';
        $this->writePrivate($temp, $bytes);
        if (is_file($path) && ! unlink($path)) {
            throw new RuntimeException('Close the credential CSV before rerunning. Initial JSON credentials remain intact.');
        }
        if (! rename($temp, $path)) {
            throw new RuntimeException('Cannot publish credential CSV. Initial JSON credentials remain intact.');
        }

        return $path;
    }
}
