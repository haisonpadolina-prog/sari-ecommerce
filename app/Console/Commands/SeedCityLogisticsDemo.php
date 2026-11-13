<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class SeedCityLogisticsDemo extends Command
{
    protected $signature = 'sari:seed-city-logistics {--dry-run : Validate data and collisions without inserting accounts} {--allow-nonlocal : Explicitly allow synthetic demo accounts outside local/testing}';
    protected $description = 'Create 149 synthetic city logistics accounts and 50 riders per provider, without replacing existing accounts.';
    private const MARKER = 'SARI_CITY149_DEMO_V1';
    private const RIDERS = 50;

    public function handle(): int
    {
        if (!app()->environment(['local', 'testing']) && !$this->option('allow-nonlocal')) {
            $this->error('These are synthetic test accounts. Use --allow-nonlocal only when intentionally adding demo data to this environment.');
            return self::FAILURE;
        }
        $handle = null;
        try {
            $cities = $this->cities();
            $this->schemaCheck();
            $this->line('Validating city data and account collisions...');
            $this->preflight($cities);
            if ($this->option('dry-run')) {
                $this->info('Validated 149 cities, 50 riders each: 149 logistics and 7,450 riders. No files or accounts created.');
                return self::SUCCESS;
            }
            $directory = storage_path('app/private/sari-city-logistics-demo');
            if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) throw new RuntimeException('Cannot create private credential directory.');
            @chmod($directory, 0700);
            $handle = fopen($directory.'/seed.lock', 'c');
            if (!$handle || !flock($handle, LOCK_EX | LOCK_NB)) throw new RuntimeException('Another city logistics seed is running.');
            @chmod($directory.'/seed.lock', 0600);
            // Recheck after acquiring the local process lock.
            $this->preflight($cities);
            $created = ['logistics' => 0, 'riders' => 0];
            foreach ($cities as $index => $city) {
                $plan = $this->credentialPlan($directory, $city);
                $counts = $this->seedCity($city, $plan, $index + 1);
                $created['logistics'] += $counts['logistics'];
                $created['riders'] += $counts['riders'];
                if (($index + 1) % 10 === 0 || $index === 148) $this->line('Cities processed: '.($index + 1).'/149');
            }
            $csv = $this->exportCredentials($directory, $cities);
            $this->info("Created {$created['logistics']} logistics and {$created['riders']} riders. Existing managed accounts were left unchanged.");
            $this->line('Initial login credentials: '.$csv);
            $this->line('Providers are active; riders are active and OFFLINE. No orders, hub coordinates, earnings, emails or SMS were generated.');
            return self::SUCCESS;
        } catch (\Throwable $exception) {
            // Never include passwords or credential-plan content in console output.
            $this->error(get_class($exception) === RuntimeException::class ? $exception->getMessage() : 'Seed stopped because of a database/filesystem error. Earlier complete cities are preserved; fix the cause and rerun.');
            return self::FAILURE;
        } finally {
            if (is_resource($handle)) { flock($handle, LOCK_UN); fclose($handle); }
        }
    }

    private function cities(): array
    {
        $path = database_path('data/sari_city_logistics_demo.json');
        if (!is_file($path)) throw new RuntimeException('Missing database/data/sari_city_logistics_demo.json.');
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $cities = $data['cities'] ?? [];
        if (count($cities) !== 149 || count(array_unique(array_column($cities, 'psgc_code'))) !== 149) throw new RuntimeException('Expected exactly 149 unique PSA city codes.');
        foreach ($cities as $city) {
            if (!preg_match('/^\d{10}$/D', $city['psgc_code'] ?? '') || empty($city['barangays'])) throw new RuntimeException('Invalid city or missing barangay data.');
            foreach (['municipality_code','municipality_name','province_code','province_name','region_code','region_name'] as $field) {
                if (empty($city[$field])) throw new RuntimeException('Incomplete city address metadata.');
            }
            foreach ($city['barangays'] as $barangay) {
                if (empty($barangay['name']) || !preg_match('/^\d{9,10}$/D', $barangay['code'] ?? '')) throw new RuntimeException('Invalid barangay data.');
            }
        }
        return $cities;
    }

    private function schemaCheck(): void
    {
        $needed = [
            'logistics_accounts' => ['registration_application_id','region_code','region_name','business_name','service_areas','deleted_at'],
            'courier_accounts' => ['registration_application_id','logistics_account_id','duty_province_name','duty_municipality_name','duty_barangay_name','activation_token_hash','activated_at','license_number','availability_status','deleted_at'],
            'registration_applications' => ['logistics_account_id','region_code','region_name','license_number','license_expiry_date','admin_note','service_areas'],
        ];
        foreach ($needed as $table => $columns) {
            if (!Schema::hasTable($table) || !Schema::hasColumns($table, $columns)) throw new RuntimeException('Required account schema is missing. Apply your project migrations first: '.$table);
        }
    }

    private function email(array $city, ?int $rider = null): string
    {
        return ($rider === null ? 'logistics' : 'rider').'.city-'.$city['psgc_code']
            .($rider === null ? '' : '.'.str_pad((string) $rider, 3, '0', STR_PAD_LEFT)).'@example.test';
    }

    /** Never adopt or overwrite an unrelated account, even when its email matches. */
    private function preflight(array $cities): void
    {
        foreach ($cities as $city) {
            $emails = [$this->email($city)];
            for ($i = 1; $i <= self::RIDERS; $i++) $emails[] = $this->email($city, $i);
            foreach (['buyer_accounts', 'seller_accounts', 'admin_accounts', 'social_accounts'] as $otherTable) {
                if (Schema::hasTable($otherTable) && DB::table($otherTable)->whereIn('email', $emails)->exists()) throw new RuntimeException('Demo email is already used by another account role in '.$city['municipality_name']);
            }
            $registrations = DB::table('registration_applications')->whereIn('email', $emails)->get()->keyBy('email');
            $providers = DB::table('logistics_accounts')->whereIn('email', $emails)->get()->keyBy('email');
            $riders = DB::table('courier_accounts')->whereIn('email', $emails)->get()->keyBy('email');
            foreach ($emails as $email) {
                $application = $registrations->get($email);
                $providerEmail = $this->email($city);
                $isProvider = $email === $providerEmail;
                if ($application && ($application->admin_note !== self::MARKER.':'.$city['psgc_code'] || $application->role !== ($isProvider ? 'logistics' : 'rider'))) throw new RuntimeException('Email collision with an unrelated registration: '.$email);
                if (!$isProvider && $application && (!$providers->has($providerEmail) || (int) $application->logistics_account_id !== (int) $providers->get($providerEmail)->id)) throw new RuntimeException('Existing rider registration has a different provider: '.$email);
                $account = $isProvider ? $providers->get($email) : $riders->get($email);
                if (($isProvider && $riders->has($email)) || (!$isProvider && $providers->has($email))) throw new RuntimeException('Cross-role account collision: '.$email);
                if ($account && (!$application || (int) $account->registration_application_id !== (int) $application->id || $account->deleted_at !== null)) throw new RuntimeException('Existing account is unrelated or archived; refusing to alter: '.$email);
                if (!$isProvider && $account && (int) $account->logistics_account_id !== (int) ($providers->get($providerEmail)?->id ?? 0)) throw new RuntimeException('Rider belongs to another logistics provider: '.$email);
            }
        }
    }

    /** Save strong per-city initial credentials before committing its first account. */
    private function credentialPlan(string $directory, array $city): array
    {
        $path = $directory.'/'.$city['psgc_code'].'.json';
        if (!is_file($path)) {
            if (DB::table('logistics_accounts')->where('email', $this->email($city))->exists()) {
                throw new RuntimeException('Private city credentials are missing for '.$city['municipality_name'].'. Restore the credential directory; existing passwords will not be reset.');
            }
            $plan = ['marker' => self::MARKER, 'city_code' => $city['psgc_code'], 'initial_password' => 'Sari!9-'.rtrim(strtr(base64_encode(random_bytes(24)), '+/', '-_'), '=')];
            $file = fopen($path, 'x');
            if (!$file) throw new RuntimeException('Cannot create city credential plan.');
            @chmod($path, 0600);
            try {
                $json = json_encode($plan, JSON_THROW_ON_ERROR);
                if (fwrite($file, $json) !== strlen($json) || !fflush($file)) throw new RuntimeException('Could not persist city credentials.');
            } finally { fclose($file); }
        }
        $plan = json_decode(file_get_contents($path), true, 32, JSON_THROW_ON_ERROR);
        if (($plan['marker'] ?? null) !== self::MARKER || ($plan['city_code'] ?? null) !== $city['psgc_code'] || strlen($plan['initial_password'] ?? '') < 24) throw new RuntimeException('Invalid private credential plan; restore its backup before retrying.');
        return $plan;
    }

    private function seedCity(array $city, array $plan, int $cityIndex): array
    {
        return DB::transaction(function () use ($city, $plan, $cityIndex): array {
            $now = now(); $birthday = '2000-01-01'; $hash = null;
            $common = ['last_name' => 'Demo', 'first_name' => 'Logistics', 'middle_initial' => null,
                'sex' => 'N/A', 'contact_no' => '00000000000', 'birthday' => $birthday,
                'age' => Carbon::parse($birthday)->age, 'province_code' => $city['province_code'],
                'province_name' => $city['province_name'], 'municipality_code' => $city['municipality_code'],
                'municipality_name' => $city['municipality_name'], 'barangay_code' => $city['barangays'][0]['code'],
                'barangay_name' => $city['barangays'][0]['name'], 'street_address' => 'SARI DEMO ADDRESS - not a verified hub',
                'created_at' => $now, 'updated_at' => $now];
            $providerEmail = $this->email($city);
            $provider = DB::table('logistics_accounts')->where('email', $providerEmail)->first();
            $created = ['logistics' => 0, 'riders' => 0];
            if (!$provider) {
                $hash = Hash::make($plan['initial_password']);
                $application = DB::table('registration_applications')->where('email', $providerEmail)->first();
                $appId = $application?->id ?? DB::table('registration_applications')->insertGetId(array_merge($common, [
                    'email' => $providerEmail, 'password' => $hash, 'role' => 'logistics',
                    'region_code' => $city['region_code'], 'region_name' => $city['region_name'],
                    'business_name' => 'SARI Demo Logistics - '.$city['municipality_name'], 'service_areas' => $city['municipality_name'],
                    'status' => 'approved', 'approved_at' => $now, 'reviewed_at' => $now,
                    'admin_note' => self::MARKER.':'.$city['psgc_code'],
                ]));
                $id = DB::table('logistics_accounts')->insertGetId(array_merge($common, [
                    'registration_application_id' => $appId, 'email' => $providerEmail, 'password' => $hash,
                    'region_code' => $city['region_code'], 'region_name' => $city['region_name'],
                    'business_name' => 'SARI Demo Logistics - '.$city['municipality_name'], 'position' => 'Demo manager',
                    'service_areas' => $city['municipality_name'], 'account_status' => 'active', 'approved_at' => $now,
                ]));
                $provider = (object) ['id' => $id]; $created['logistics']++;
            }
            for ($i = 1; $i <= self::RIDERS; $i++) {
                $email = $this->email($city, $i);
                if (DB::table('courier_accounts')->where('email', $email)->exists()) continue;
                $hash ??= Hash::make($plan['initial_password']);
                $barangay = $city['barangays'][($i - 1) % count($city['barangays'])];
                $identity = array_merge($common, ['first_name' => 'Rider', 'last_name' => 'Demo '.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'email' => $email, 'password' => $hash, 'barangay_code' => $barangay['code'], 'barangay_name' => $barangay['name'],
                    'vehicle_type' => 'Motorcycle', 'plate_number' => sprintf('D%03dR%02d', $cityIndex, $i)]);
                $application = DB::table('registration_applications')->where('email', $email)->first();
                $appId = $application?->id ?? DB::table('registration_applications')->insertGetId(array_merge($identity, [
                    'role' => 'rider', 'logistics_account_id' => $provider->id, 'region_code' => $city['region_code'], 'region_name' => $city['region_name'],
                    'status' => 'approved', 'approved_at' => $now, 'reviewed_at' => $now,
                    'admin_note' => self::MARKER.':'.$city['psgc_code'],
                    'license_number' => sprintf('DEMO-%03d-%02d', $cityIndex, $i), 'license_expiry_date' => '2032-12-31',
                ]));
                DB::table('courier_accounts')->insert(array_merge($identity, [
                    'registration_application_id' => $appId, 'logistics_account_id' => $provider->id,
                    'duty_province_name' => $city['province_name'], 'duty_municipality_name' => $city['municipality_name'],
                    'duty_barangay_name' => $barangay['name'], 'license_number' => sprintf('DEMO-%03d-%02d', $cityIndex, $i),
                    'license_expiry_date' => '2032-12-31', 'vehicle_model' => 'Demo motorcycle',
                    'account_status' => 'active', 'approved_at' => $now, 'activated_at' => $now,
                    'availability_status' => 'offline', 'rating' => 0,
                ]));
                $created['riders']++;
            }
            return $created;
        }, 3);
    }

    private function exportCredentials(string $directory, array $cities): string
    {
        $path = $directory.'/initial-login-credentials.csv'; $temp = $path.'.tmp';
        $file = fopen($temp, 'wb');
        if (!$file) throw new RuntimeException('Could not open credential export. City JSON files retain initial passwords.');
        @chmod($temp, 0600);
        try {
            fputcsv($file, ['role','city','province','barangay','email','initial_password','logistics_email'], ',', '"', '');
            foreach ($cities as $city) {
                $plan = $this->credentialPlan($directory, $city);
                for ($i = 0; $i <= self::RIDERS; $i++) {
                    $barangay = $city['barangays'][max(0, $i - 1) % count($city['barangays'])];
                    if (fputcsv($file, [$i === 0 ? 'logistics' : 'rider', $city['municipality_name'], $city['province_name'], $barangay['name'],
                        $this->email($city, $i === 0 ? null : $i), $plan['initial_password'], $this->email($city)], ',', '"', '') === false) throw new RuntimeException('Could not finish credential export.');
                }
            }
            if (!fflush($file)) throw new RuntimeException('Could not flush credential export.');
        } finally { fclose($file); }
        if (is_file($path) && !unlink($path)) throw new RuntimeException('Close the previous credential CSV and rerun to export.');
        if (!rename($temp, $path)) throw new RuntimeException('Could not publish credential CSV; private city plans retain passwords.');
        return $path;
    }
}
