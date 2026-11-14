<?php

namespace App\Console\Commands;

use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Services\Delivery\PhilippineAddressGeocoder;
use App\Support\LogisticsServiceArea;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class SeedCityLogisticsHubsDemo extends Command
{
    protected $signature = 'sari:seed-city-logistics-hubs {--dry-run : Validate account links and plan facilities without changes} {--allow-nonlocal : Intentionally create city-reference QA hubs outside local/testing}';

    protected $description = 'Prepare city operating hubs and Main Branch flags for the managed 149-city demo Logistics network.';

    public function handle(): int
    {
        if (! app()->environment(['local', 'testing']) && ! $this->option('allow-nonlocal')) {
            $this->error('Use --allow-nonlocal only for intentionally creating synthetic demo hubs. Coordinates are city reference points, not verified physical warehouse locations.');

            return self::FAILURE;
        }
        $lock = null;
        try {
            $hubs = $this->definitions();
            $plan = $this->plan($hubs);
            $new = count(array_filter($plan, fn ($row) => $row['facility_id'] === null));
            $promoted = count(array_filter($plan, fn ($row) => $row['make_main']));
            $this->table(['Providers', 'New city hubs', 'Existing hubs reused', 'Main flags to set', 'Linked city riders'], [[149, $new, 149 - $new, $promoted, array_sum(array_column($plan, 'riders'))]]);
            if ($this->option('dry-run')) {
                $this->info('Preflight passed. No facilities, accounts, routes or files changed.');

                return self::SUCCESS;
            }
            $directory = storage_path('app/private/sari-city-hubs-demo');
            if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
                throw new RuntimeException('Cannot create the private hub report directory.');
            }
            $lock = fopen($directory.'/seed.lock', 'c');
            if (! $lock || ! flock($lock, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Another city hub seed is running.');
            }
            $counts = DB::transaction(function () use ($hubs, $directory): array {
                $ids = LogisticsAccount::query()->whereIn('email', array_column($hubs, 'provider_email'))->orderBy('id')->pluck('id')->all();
                LogisticsAccount::query()->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
                LogisticsFacility::query()->whereIn('logistics_account_id', $ids)->orderBy('id')->lockForUpdate()->get();
                $plan = $this->plan($hubs);
                $before = [];
                foreach ($plan as $row) {
                    if ($row['facility_id'] !== null && $row['make_main']) {
                        $before[] = ['facility_id' => $row['facility_id'], 'logistics_account_id' => $row['provider_id'], 'is_main_branch_before' => false];
                    }
                }
                if ($before) {
                    $this->writeNew($directory.'/main-flags-before-'.now()->format('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json', json_encode($before, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
                }
                $counts = ['created' => 0, 'promoted' => 0];
                foreach ($plan as $row) {
                    if ($row['facility_id'] === null) {
                        $hub = $row['hub'];
                        LogisticsFacility::create([
                            'logistics_account_id' => $row['provider_id'], 'code' => $hub['code'], 'name' => $hub['name'],
                            'address' => $hub['address'], 'province_name' => $hub['province_name'], 'municipality_name' => $hub['municipality_name'],
                            'barangay_name' => null, 'latitude' => $hub['latitude'], 'longitude' => $hub['longitude'],
                            'is_active' => true, 'is_main_branch' => $row['make_main'],
                        ]);
                        $counts['created']++;
                    } elseif ($row['make_main']) {
                        LogisticsFacility::query()->whereKey($row['facility_id'])->where('logistics_account_id', $row['provider_id'])->update(['is_main_branch' => true, 'updated_at' => now()]);
                        $counts['promoted']++;
                    }
                }

                return $counts;
            }, 3);
            $plan = $this->plan($hubs);
            [$csv,$flowCsv] = $this->reports($directory, $plan);
            $this->info('Created '.$counts['created'].' city hubs; promoted '.$counts['promoted'].' existing city hubs to Main Branch. Existing facility addresses, coordinates and main-branch choices were preserved.');
            $this->line('Hub/provider/rider mapping: '.$csv);
            $this->line('First 10 Buyer/Seller/Rider readiness checks: '.$flowCsv);
            $this->line('Sign in as the existing Logistics manager, then select the city hub on Select Operating Branch. Riders must sign in and switch ONLINE before assignment.');
            $this->warn('New hub map pins are attributed city reference points, not verified warehouse/street coordinates. Buyer/Seller endpoint geocoding and actual order routing still run through the existing workflow.');
            $this->line('One hub per provider/city; the city hub is the Main Branch when no main already exists. No extra same-city duplicate branch was created.');
            $this->line('No accounts, passwords, rider availability, orders, route snapshots or financial balances were changed. Existing in-progress routes were not rerouted.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error(get_class($exception) === RuntimeException::class ? $exception->getMessage() : 'Hub setup stopped due to a database/filesystem issue. Facility changes are transactional. If report export failed after commit, rerun without duplicating hubs.');

            return self::FAILURE;
        } finally {
            if (is_resource($lock)) {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }
    }

    private function definitions(): array
    {
        $path = database_path('data/sari_city_hubs_demo/hubs.json');
        if (! is_file($path)) {
            throw new RuntimeException('Missing bundled city hub reference data.');
        }
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $hubs = $data['hubs'] ?? [];
        if (($data['marker'] ?? '') !== 'SARI_CITY149_HUBS_V1' || count($hubs) !== 149 || count(array_unique(array_column($hubs, 'city_code'))) !== 149) {
            throw new RuntimeException('Expected exactly 149 unique city hub definitions.');
        }
        $geocoder = app(PhilippineAddressGeocoder::class);
        foreach ($hubs as $hub) {
            $code = $hub['city_code'] ?? '';
            if (! preg_match('/^\d{10}$/D', $code) || ($hub['provider_email'] ?? '') !== 'logistics.city-'.$code.'@example.test' || ($hub['code'] ?? '') !== 'SARI-QA149-'.$code || ($hub['first_rider_email'] ?? '') !== 'rider.city-'.$code.'.001@example.test') {
                throw new RuntimeException('Invalid managed city hub identity.');
            }
            if (! $geocoder->isPhilippineCoordinate($hub['latitude'] ?? null, $hub['longitude'] ?? null) || empty($hub['province_name']) || empty($hub['municipality_name']) || empty($hub['first_barangay'])) {
                throw new RuntimeException('Invalid reference point or city metadata.');
            }
        }

        return $hubs;
    }

    private function plan(array $hubs): array
    {
        foreach ([
            'logistics_accounts' => ['registration_application_id', 'municipality_name', 'province_name', 'deleted_at'],
            'registration_applications' => ['admin_note', 'logistics_account_id', 'status'],
            'courier_accounts' => ['registration_application_id', 'logistics_account_id', 'availability_status', 'duty_province_name', 'duty_municipality_name', 'duty_barangay_name', 'deleted_at'],
            'logistics_facilities' => ['code', 'logistics_account_id', 'latitude', 'longitude', 'is_active', 'is_main_branch', 'province_name', 'municipality_name'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table) || ! Schema::hasColumns($table, $columns)) {
                throw new RuntimeException('Required existing project migrations are missing for '.$table.'.');
            }
        }
        $providers = LogisticsAccount::query()->select(['id', 'registration_application_id', 'email', 'account_status', 'municipality_name', 'province_name'])->with('registrationApplication:id,admin_note,role')->whereIn('email', array_column($hubs, 'provider_email'))->get()->keyBy('email');
        $providerIds = $providers->pluck('id')->all();
        $facilities = LogisticsFacility::query()->whereIn('logistics_account_id', $providerIds)->get()->groupBy('logistics_account_id');
        $reserved = LogisticsFacility::whereIn('code', array_column($hubs, 'code'))->get()->keyBy('code');
        $riders = CourierAccount::query()->select(['id', 'registration_application_id', 'logistics_account_id', 'email', 'municipality_name', 'province_name', 'barangay_name', 'duty_province_name', 'duty_municipality_name', 'duty_barangay_name', 'account_status', 'availability_status'])->with('registrationApplication:id,role,status,logistics_account_id')->whereIn('logistics_account_id', $providerIds)->get()->groupBy('logistics_account_id');
        $geocoder = app(PhilippineAddressGeocoder::class);
        $plan = [];
        foreach ($hubs as $hub) {
            $provider = $providers->get($hub['provider_email']);
            if (! $provider || $provider->registrationApplication?->admin_note !== 'SARI_CITY149_DEMO_V1:'.$hub['city_code'] || $provider->registrationApplication?->role !== 'logistics' || $provider->account_status !== 'active') {
                throw new RuntimeException('Missing, unrelated, or inactive managed provider: '.$hub['provider_email'].'. Run the original city logistics seed first; existing accounts will not be replaced.');
            }
            if (! $this->sameCity($provider, $hub)) {
                throw new RuntimeException('A managed provider city/province was changed. Review its service coverage before adding a hub.');
            }
            $owned = $facilities->get($provider->id, collect());
            $matching = $owned->filter(fn ($f) => $this->sameCity($f, $hub))->values();
            $main = $owned->where('is_main_branch', true)->values();
            if ($matching->count() > 1 || $main->count() > 1 || ($main->count() === 1 && ! $main->first()->is_active)) {
                throw new RuntimeException('Existing city hubs or Main Branch flags are ambiguous/inactive for '.$hub['provider_email'].'. Review those facilities first.');
            }
            $facility = $matching->first();
            $reservedFacility = $reserved->get($hub['code']);
            if ($reservedFacility && (! $facility || (int) $reservedFacility->id !== (int) $facility->id)) {
                throw new RuntimeException('A reserved city hub code belongs to another facility. No facilities will be adopted or overwritten.');
            }
            if ($facility && (! $facility->is_active || ! $geocoder->isPhilippineCoordinate($facility->latitude, $facility->longitude))) {
                throw new RuntimeException('An existing city hub is inactive or has invalid coordinates. Preserve and fix it through Facility Management first.');
            }
            $cityRiders = $riders->get($provider->id, collect())->filter(fn ($r) => $this->sameCity((object) ['municipality_name' => $r->duty_municipality_name ?: $r->municipality_name, 'province_name' => $r->duty_province_name ?: $r->province_name], $hub));
            $first = $cityRiders->firstWhere('email', $hub['first_rider_email']);
            if (! $first || $first->account_status !== 'active' || $first->registrationApplication?->role !== 'rider' || $first->registrationApplication?->status !== 'approved' || (int) $first->registrationApplication?->logistics_account_id !== (int) $provider->id || LogisticsServiceArea::barangay($first->duty_barangay_name ?: $first->barangay_name) !== LogisticsServiceArea::barangay($hub['first_barangay'])) {
                throw new RuntimeException('The expected first city Rider is missing, unapproved, or outside its original provider/duty area: '.$hub['first_rider_email'].'. Rider accounts will not be reassigned automatically.');
            }
            $plan[] = ['hub' => $hub, 'provider_id' => (int) $provider->id, 'facility_id' => $facility?->id, 'make_main' => $main->isEmpty(), 'riders' => $cityRiders->count(), 'online_riders' => $cityRiders->where('account_status', 'active')->where('availability_status', 'online')->count(), 'first_rider_id' => (int) $first->id, 'first_rider_online' => $first->availability_status === 'online'];
        }

        return $plan;
    }

    private function sameCity(object $record, array $hub): bool
    {
        return LogisticsServiceArea::municipality($record->municipality_name ?? '') === LogisticsServiceArea::municipality($hub['municipality_name']) && LogisticsServiceArea::normalize($record->province_name ?? '') === LogisticsServiceArea::normalize($hub['province_name']);
    }

    private function reports(string $directory, array $plan): array
    {
        $facilities = LogisticsFacility::whereIn('id', array_column($plan, 'facility_id'))->get()->keyBy('id');
        $hubRows = [['provider_email', 'provider_id', 'facility_id', 'facility_name', 'is_main_branch', 'city', 'province', 'latitude', 'longitude', 'location_basis', 'linked_city_riders', 'online_city_riders', 'first_rider_email', 'first_rider_id', 'first_rider_online']];
        $flowRows = [['flow', 'buyer_email', 'buyer_record_city_matches', 'seller_email', 'seller_record_city_matches', 'first_rider_email', 'first_rider_online', 'provider_email', 'facility_id', 'city', 'target_barangay']];
        foreach ($plan as $index => $row) {
            $hub = $row['hub'];
            $facility = $facilities[$row['facility_id']];
            $hubRows[] = [$hub['provider_email'], $row['provider_id'], $facility->id, $facility->name, (int) $facility->is_main_branch, $hub['municipality_name'], $hub['province_name'], $facility->latitude, $facility->longitude, $facility->code === $hub['code'] ? 'city_reference_QA_not_verified_warehouse' : 'preserved_existing_facility', $row['riders'], $row['online_riders'], $hub['first_rider_email'], $row['first_rider_id'], (int) $row['first_rider_online']];
            if ($index < 10) {
                $slot = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
                $buyer = Schema::hasTable('buyer_accounts') ? DB::table('buyer_accounts')->where('email', 'buyer.demo.'.$slot.'@example.test')->whereNull('deleted_at')->first(['municipality_name', 'province_name', 'barangay_name']) : null;
                $seller = Schema::hasTable('seller_accounts') ? DB::table('seller_accounts')->where('email', 'seller.flow.'.$slot.'@example.test')->whereNull('deleted_at')->first(['municipality_name', 'province_name', 'barangay_name']) : null;
                $flowRows[] = [$index + 1, 'buyer.demo.'.$slot.'@example.test', (int) ($buyer && $this->sameCity($buyer, $hub) && LogisticsServiceArea::barangay($buyer->barangay_name) === LogisticsServiceArea::barangay($hub['first_barangay'])), 'seller.flow.'.$slot.'@example.test', (int) ($seller && $this->sameCity($seller, $hub) && LogisticsServiceArea::barangay($seller->barangay_name) === LogisticsServiceArea::barangay($hub['first_barangay'])), $hub['first_rider_email'], (int) $row['first_rider_online'], $hub['provider_email'], $facility->id, $hub['municipality_name'], $hub['first_barangay']];
            }
        }
        $csv = $directory.'/hub-provider-rider-mapping.csv';
        $flowCsv = $directory.'/first-10-flow-readiness.csv';
        $this->writeCsv($csv, $hubRows);
        $this->writeCsv($flowCsv, $flowRows);

        return [$csv, $flowCsv];
    }

    private function writeCsv(string $path, array $rows): void
    {
        $stream = fopen('php://temp', 'w+');
        if (! $stream) {
            throw new RuntimeException('Cannot prepare the hub mapping report.');
        }
        try {
            foreach ($rows as $row) {
                $row = array_map(fn ($value) => is_string($value) && preg_match('/^[=+@-]/', $value) ? "'".$value : $value, $row);
                if (fputcsv($stream, $row, ',', '"', '') === false) {
                    throw new RuntimeException('Could not finish the hub mapping report.');
                }
            }
            rewind($stream);
            $bytes = stream_get_contents($stream);
        } finally {
            fclose($stream);
        }
        $temp = $path.'.'.bin2hex(random_bytes(6)).'.tmp';
        $this->writeNew($temp, $bytes);
        if (is_file($path) && ! unlink($path)) {
            throw new RuntimeException('Close the previous mapping CSV and rerun to export.');
        }
        if (! rename($temp, $path)) {
            throw new RuntimeException('Could not publish the mapping CSV. Rerun to export without duplicate hubs.');
        }
    }

    private function writeNew(string $path, string $bytes): void
    {
        $file = fopen($path, 'x');
        if (! $file) {
            throw new RuntimeException('Could not create the private hub report/backup.');
        }
        @chmod($path, 0600);
        try {
            if (fwrite($file, $bytes) !== strlen($bytes) || ! fflush($file)) {
                throw new RuntimeException('Could not persist the hub report/backup.');
            }
        } finally {
            fclose($file);
        }
    }
}
