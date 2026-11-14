<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class SeedSellerFlowDemo extends Command
{
    protected $signature = 'sari:seed-seller-flow-demo {--dry-run : Validate only; no accounts or files changed} {--allow-nonlocal : Intentionally add fictional Sellers outside local/testing}';

    protected $description = 'Create 50 distinct demo Sellers with personal/store profiles and private random initial credentials.';

    private const MARKER = 'SARI_SELLER_FLOW50_V1';

    public function handle(): int
    {
        if (! app()->environment(['local', 'testing']) && ! $this->option('allow-nonlocal')) {
            $this->error('Synthetic Sellers are disabled here. Use --allow-nonlocal only for an intentional demo environment.');

            return self::FAILURE;
        }
        $lock = null;
        try {
            $profiles = $this->profiles();
            $this->preflight($profiles);
            if ($this->option('dry-run')) {
                $this->info('Validated 50 Seller profiles and 150 PNG assets. No accounts, credentials or files changed.');

                return self::SUCCESS;
            }
            $directory = storage_path('app/private/sari-seller-flow-demo');
            if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
                throw new RuntimeException('Cannot create private credential directory.');
            }
            @chmod($directory, 0700);
            $lock = fopen($directory.'/seed.lock', 'c');
            if (! $lock || ! flock($lock, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Another Seller demo seed is running.');
            }
            @chmod($directory.'/seed.lock', 0600);
            $this->preflight($profiles);
            $plan = $this->credentials($directory, $profiles);
            $disk = Storage::disk('public');
            foreach ($profiles as $profile) {
                foreach ($profile['assets'] as $asset) {
                    $relative = 'demo-seller-flow-v1/'.$asset['file'];
                    if ($disk->exists($relative)) {
                        if (hash('sha256', $disk->get($relative)) !== $asset['sha256']) {
                            throw new RuntimeException('A bundled media path contains a custom file. Refusing to overwrite it.');
                        }
                    } elseif (! $disk->put($relative, file_get_contents(database_path('data/sari_seller_flow_demo/media/'.$asset['file'])))) {
                        throw new RuntimeException('Could not install Seller profile media.');
                    }
                }
            }
            $created = DB::transaction(function () use ($profiles, $plan): int {
                $this->preflight($profiles);
                $created = 0;
                foreach ($profiles as $profile) {
                    if (DB::table('seller_accounts')->where('email', $profile['email'])->exists()) {
                        continue;
                    }
                    $fields = array_intersect_key($profile, array_flip(['first_name', 'last_name', 'sex', 'birthday', 'email', 'province_code', 'province_name', 'municipality_code', 'municipality_name', 'barangay_code', 'barangay_name', 'line_of_business']));
                    $fields += [
                        'middle_initial' => null, 'contact_no' => '00000000000',
                        'age' => Carbon::parse($profile['birthday'])->age,
                        'street_address' => $profile['pickup_address'],
                        'password' => Hash::make($plan[$profile['email']]),
                        'profile_image_path' => 'demo-seller-flow-v1/'.$profile['assets']['avatar']['file'],
                        'approved_at' => now(), 'created_at' => now(), 'updated_at' => now(),
                    ];
                    $applicationId = DB::table('registration_applications')->insertGetId($fields + [
                        'role' => 'seller', 'business_name' => $profile['store_name'],
                        'status' => 'approved', 'reviewed_at' => now(), 'admin_note' => self::MARKER,
                    ]);
                    DB::table('seller_accounts')->insert($fields + [
                        'registration_application_id' => $applicationId,
                        'store_name' => $profile['store_name'],
                        'seller_type' => 'individual', 'registration_status' => 'approved',
                        'account_status' => 'active', 'warning_count' => 0, 'store_status' => 'open',
                        'store_description' => $profile['store_description'],
                        'store_phone' => '00000000000', 'store_public_email' => $profile['email'],
                        'pickup_address' => $profile['pickup_address'],
                        'pickup_instructions' => 'Synthetic test pickup location only. Configure and verify the hub/rider workflow before a test order.',
                        'store_logo_path' => 'demo-seller-flow-v1/'.$profile['assets']['logo']['file'],
                        'store_banner_path' => 'demo-seller-flow-v1/'.$profile['assets']['banner']['file'],
                        'realtime_token' => Str::random(64),
                    ]);
                    $created++;
                }

                return $created;
            }, 3);
            $csv = $this->export($directory, $profiles, $plan);
            $this->info('Created '.$created.' Sellers. Existing managed accounts, passwords, photos and store changes were preserved.');
            $this->line('Initial login credentials: '.$csv);
            $this->line('Stores are active/open for QA with fictional personal/store profiles; this is not genuine seller/KYC verification.');
            $this->line('No products, orders, hubs, routes, financial credits, emails or SMS were created.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error(get_class($exception) === RuntimeException::class ? $exception->getMessage() : 'Seller seed stopped because of a database/filesystem issue. New registrations and Sellers are atomic. Rerun to recover the CSV after an export failure; existing passwords are not reset.');

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
        $path = database_path('data/sari_seller_flow_demo/profiles.json');
        if (! is_file($path)) {
            throw new RuntimeException('Missing bundled Seller profile data.');
        }
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $profiles = $data['profiles'] ?? [];
        if (($data['marker'] ?? '') !== self::MARKER || count($profiles) !== 50 || count(array_unique(array_column($profiles, 'email'))) !== 50) {
            throw new RuntimeException('Expected exactly 50 unique managed Seller profiles.');
        }
        foreach ($profiles as $index => $profile) {
            $slot = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            if (($profile['email'] ?? '') !== 'seller.flow.'.$slot.'@example.test') {
                throw new RuntimeException('Invalid managed Seller identity.');
            }
            foreach (['first_name', 'last_name', 'sex', 'birthday', 'province_code', 'province_name', 'municipality_code', 'municipality_name', 'barangay_code', 'barangay_name', 'store_name', 'store_description', 'line_of_business', 'pickup_address'] as $field) {
                if (empty($profile[$field])) {
                    throw new RuntimeException('Incomplete Seller profile metadata.');
                }
            }
            foreach (['avatar', 'logo', 'banner'] as $kind) {
                $asset = $profile['assets'][$kind] ?? [];
                if (($asset['file'] ?? '') !== 'seller-'.$slot.'-'.$kind.'.png') {
                    throw new RuntimeException('Invalid bundled Seller media path.');
                }
                $file = database_path('data/sari_seller_flow_demo/media/'.$asset['file']);
                if (! is_file($file) || hash_file('sha256', $file) !== ($asset['sha256'] ?? '')) {
                    throw new RuntimeException('Missing or changed Seller media asset.');
                }
            }
        }

        return $profiles;
    }

    private function preflight(array $profiles): void
    {
        foreach ([
            'seller_accounts' => ['registration_application_id', 'profile_image_path', 'store_logo_path', 'store_banner_path', 'store_status', 'store_description', 'store_phone', 'store_public_email', 'pickup_address', 'pickup_instructions', 'realtime_token', 'seller_type', 'deleted_at'],
            'registration_applications' => ['profile_image_path', 'admin_note', 'business_name', 'line_of_business'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table) || ! Schema::hasColumns($table, $columns)) {
                throw new RuntimeException('Required existing project migrations are missing for '.$table.'.');
            }
        }
        $emails = array_column($profiles, 'email');
        foreach (['buyer_accounts', 'logistics_accounts', 'courier_accounts', 'admin_accounts', 'social_accounts'] as $table) {
            if (Schema::hasTable($table) && DB::table($table)->whereIn('email', $emails)->exists()) {
                throw new RuntimeException('A planned Seller email belongs to another account role. No account will be adopted.');
            }
        }
        if (Schema::hasTable('account_email_bans') && DB::table('account_email_bans')->whereIn('email', $emails)->whereNull('unbanned_at')->exists()) {
            throw new RuntimeException('A planned Seller email is banned. Existing bans will not be changed.');
        }
        $sellers = DB::table('seller_accounts')->whereIn('email', $emails)->get()->keyBy('email');
        $applications = DB::table('registration_applications')->whereIn('email', $emails)->get()->keyBy('email');
        if (DB::table('registration_applications')->where('admin_note', self::MARKER)->whereNotIn('email', $emails)->exists()) {
            throw new RuntimeException('A managed registration email changed. Resolve the record before rerunning.');
        }
        foreach ($emails as $email) {
            $seller = $sellers->get($email);
            $application = $applications->get($email);
            if (! $seller && ! $application) {
                continue;
            }
            if (! $seller || ! $application || $seller->deleted_at !== null || $application->role !== 'seller' || $application->admin_note !== self::MARKER || (int) $seller->registration_application_id !== (int) $application->id) {
                throw new RuntimeException('A planned Seller identity is unrelated, archived, or inconsistent. Existing accounts will not be overwritten.');
            }
        }
    }

    private function credentials(string $directory, array $profiles): array
    {
        $path = $directory.'/initial-credentials.json';
        if (! is_file($path)) {
            if (DB::table('seller_accounts')->whereIn('email', array_column($profiles, 'email'))->exists()) {
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
        if (($saved['marker'] ?? '') !== self::MARKER || count($plan) !== 50) {
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
            fputcsv($file, ['name', 'store_name', 'email', 'initial_password', 'category', 'city', 'province', 'barangay', 'pickup_address'], ',', '"', '');
            foreach ($profiles as $profile) {
                if (fputcsv($file, [$profile['first_name'].' '.$profile['last_name'], $profile['store_name'], $profile['email'], $plan[$profile['email']], $profile['line_of_business'], $profile['municipality_name'], $profile['province_name'], $profile['barangay_name'], $profile['pickup_address']], ',', '"', '') === false) {
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
