<?php

namespace Database\Seeders;

use App\Models\Accounts\LogisticsAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * LOCAL / TEST ONLY. Creates 5 demo riders in each of 26 barangays across 18 representative
 * Philippine regional locations (130 accounts total), for ONE logistics provider.
 *
 * Rider duty area is explicit. Previous 90 login credentials are preserved.
 * No production-safe identity verification is implied by this seeder.
 * Does not create logistics hubs, orders, or rider assignments.
 */
class NationwideDemoRidersSeeder extends Seeder
{
    private const MARKER = 'SARI_NATIONWIDE_DEMO_RIDER_SEED_V1';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Refusing to seed demo rider credentials outside local/testing.');
        }

        $source = storage_path('app/private/sari_nationwide_riders_credentials.csv');
        if (! is_file($source) || ! is_readable($source)) {
            throw new RuntimeException('Missing readable demo credentials CSV: ' . $source);
        }

        $logisticsEmail = strtolower(trim((string) env('SARI_TEST_LOGISTICS_EMAIL', 'logistics@gmail.com')));
        $provider = LogisticsAccount::query()->where('email', $logisticsEmail)->first();
        if (! $provider || $provider->account_status !== 'active') {
            throw new RuntimeException('First create/activate the test logistics account: ' . $logisticsEmail);
        }

        $columns = [
            'region_code','region_name','province_name','municipality_name','barangay_name',
            'street_address','first_name','last_name','sex','email','password',
            'contact_no','plate_number','license_number',
        ];
        $handle = fopen($source, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Cannot read credentials file.');
        }
        try {
            $header = fgetcsv($handle);
            if ($header !== $columns) {
                throw new RuntimeException('Unexpected credentials CSV header; use the supplied file.');
            }
            $rows = [];
            $emails = [];
            $regions = [];
            $dutyAreas = [];
            while (($values = fgetcsv($handle)) !== false) {
                if ($values === [null]) continue;
                if (count($values) !== count($columns)) {
                    throw new RuntimeException('Malformed credentials CSV row.');
                }
                $row = array_combine($columns, $values);
                $email = strtolower(trim($row['email']));
                if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! str_ends_with($email, '@example.test')) {
                    throw new RuntimeException('Demo account email must use the reserved @example.test domain.');
                }
                if (isset($emails[$email]) || strlen($row['password']) < 16) {
                    throw new RuntimeException('Duplicate demo email or weak password detected.');
                }
                $emails[$email] = true;
                $regions[$row['region_code']] = ($regions[$row['region_code']] ?? 0) + 1;
                $key = mb_strtolower(trim($row['province_name'].'|'.$row['municipality_name'].'|'.$row['barangay_name']));
                $dutyAreas[$key] = ($dutyAreas[$key] ?? 0) + 1;
                $rows[] = $row;
            }
        } finally {
            fclose($handle);
        }

        if (count($rows) !== 130 || count($regions) !== 18 || count($dutyAreas) !== 26 || count(array_filter($dutyAreas, fn ($n) => $n !== 5)) > 0) {
            throw new RuntimeException('Expected 130 accounts: exactly 5 in each of 26 distinct city/barangay service areas, across 18 regions.');
        }

        DB::transaction(function () use ($rows, $provider): void {
            foreach ($rows as $row) {
                $email = strtolower(trim($row['email']));
                $existing = DB::table('registration_applications')->where('email', $email)->first();
                if ($existing && (
                    $existing->role !== 'rider'
                    || $existing->admin_note !== self::MARKER
                    || (int) $existing->logistics_account_id !== (int) $provider->id
                )) {
                    throw new RuntimeException("Refusing to overwrite non-demo registration: {$email}");
                }
                $currentCourier = DB::table('courier_accounts')->where('email', $email)->first();
                if ($currentCourier && (
                    ! $existing
                    || (int) $currentCourier->registration_application_id !== (int) $existing->id
                    || (int) $currentCourier->logistics_account_id !== (int) $provider->id
                )) {
                    throw new RuntimeException("Refusing to overwrite non-demo courier: {$email}");
                }

                $now = now();
                $hashedPassword = Hash::make($row['password']);
                $birthday = '2000-01-01';
                $identity = [
                    'last_name' => $row['last_name'],
                    'first_name' => $row['first_name'],
                    'middle_initial' => null,
                    'sex' => $row['sex'],
                    'contact_no' => $row['contact_no'],
                    'birthday' => $birthday,
                    'age' => Carbon::parse($birthday)->age,
                    'province_code' => 'DEMO-' . $row['region_code'],
                    'province_name' => $row['province_name'],
                    'municipality_code' => 'DEMO-' . strtoupper(preg_replace('/[^a-z0-9]+/i', '-', $row['municipality_name'])),
                    'municipality_name' => $row['municipality_name'],
                    'barangay_code' => 'DEMO-'.substr(strtoupper(preg_replace('/[^A-Z0-9]+/i', '-', trim($row['barangay_name']))), 0, 25),
                    'barangay_name' => $row['barangay_name'],
                    'duty_province_name' => $row['province_name'],
                    'duty_municipality_name' => $row['municipality_name'],
                    'duty_barangay_name' => $row['barangay_name'],
                    'street_address' => $row['street_address'],
                    'password' => $hashedPassword,
                    'vehicle_type' => 'Motorcycle',
                    'plate_number' => $row['plate_number'],
                    'updated_at' => $now,
                ];
                $registrationIdentity = $identity;
                unset($registrationIdentity['duty_province_name'], $registrationIdentity['duty_municipality_name'], $registrationIdentity['duty_barangay_name']);
                $registration = array_merge($registrationIdentity, [
                    'role' => 'rider',
                    'email' => $email,
                    'logistics_account_id' => $provider->id,
                    'region_code' => $row['region_code'],
                    'region_name' => $row['region_name'],
                    'license_number' => $row['license_number'],
                    'license_expiry_date' => '2032-12-31',
                    'status' => 'approved',
                    'admin_note' => self::MARKER,
                    'reviewed_at' => $now,
                    'approved_at' => $now,
                    'rejected_at' => null,
                ]);
                if ($existing) {
                    DB::table('registration_applications')->where('id', $existing->id)->update($registration);
                    $appId = $existing->id;
                } else {
                    $appId = DB::table('registration_applications')->insertGetId(array_merge($registration, ['created_at' => $now]));
                }

                $courier = array_merge($identity, [
                    'email' => $email,
                    'registration_application_id' => $appId,
                    'logistics_account_id' => $provider->id,
                    'vehicle_model' => 'Test motorcycle',
                    'license_number' => $row['license_number'],
                    'license_expiry_date' => '2032-12-31',
                    'activation_token_hash' => null,
                    'activation_expires_at' => null,
                    'activated_at' => $now,
                    'account_status' => 'active',
                    'approved_at' => $now,
                    'availability_status' => 'online',
                    'rating' => 5.00,
                    'deleted_at' => null,
                ]);
                if ($currentCourier) {
                    DB::table('courier_accounts')->where('id', $currentCourier->id)->update($courier);
                } else {
                    DB::table('courier_accounts')->insert(array_merge($courier, ['created_at' => $now]));
                }
            }
        });

        $this->command?->info('Seeded/repaired 130 demo rider accounts, 5 per each of 26 sample barangays, for logistics provider: ' . $logisticsEmail);
        $this->command?->warn('Credentials: storage/app/private/sari_nationwide_riders_credentials.csv. Do not commit or deploy this file.');
        $this->command?->warn('Only cities with matching logistics facilities will have all hub-based assignment paths available.');
    }
}
