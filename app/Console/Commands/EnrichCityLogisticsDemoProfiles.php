<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class EnrichCityLogisticsDemoProfiles extends Command
{
    protected $signature = 'sari:enrich-city-logistics-profiles {--dry-run : Validate profiles and existing seed links only} {--allow-nonlocal : Explicitly allow synthetic profiles outside local/testing}';
    protected $description = 'Add original illustrated PNG avatars and fictional profile details to the managed 149-city logistics demo accounts.';
    private const SEED_MARKER = 'SARI_CITY149_DEMO_V1';
    private const ASSET_PREFIX = 'demo-city149-profiles-v1/';
    private const FIELDS = ['first_name','last_name','middle_initial','sex','birthday','age','street_address','profile_image_path','business_name','position','vehicle_model'];

    public function handle(): int
    {
        if (!app()->environment(['local','testing']) && !$this->option('allow-nonlocal')) {
            $this->error('Use --allow-nonlocal only when intentionally enriching synthetic demo identities in this environment.');
            return self::FAILURE;
        }
        $lock = null;
        try {
            $profiles = $this->profiles();
            foreach (['logistics_accounts','courier_accounts','registration_applications'] as $table) {
                if (!Schema::hasColumn($table, 'profile_image_path')) throw new RuntimeException('Missing profile_image_path column on '.$table.'. Apply the existing profile-image migration first.');
            }
            $this->line('Validating all 7,599 demo identities and PNG assets...');
            $this->preflight($profiles);
            if ($this->option('dry-run')) {
                $this->info('Validated 149 logistics logos, 7,450 rider avatars and their managed account links. No profiles/files changed.');
                return self::SUCCESS;
            }
            $backupDirectory = storage_path('app/private/sari-city-logistics-profile-backups');
            if (!is_dir($backupDirectory) && !mkdir($backupDirectory, 0700, true) && !is_dir($backupDirectory)) throw new RuntimeException('Cannot create private profile backup directory.');
            @chmod($backupDirectory, 0700);
            $lock = fopen($backupDirectory.'/enrich.lock', 'c');
            if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) throw new RuntimeException('Another demo profile enrichment is running.');
            @chmod($backupDirectory.'/enrich.lock', 0600);
            $changed = 0; $protectedPhotos = 0; $cityCount = 0;
            foreach (collect($profiles)->groupBy('city_code') as $cityCode => $cityProfiles) {
                $this->backupCity($backupDirectory, (string) $cityCode, $cityProfiles->all());
                $counts = DB::transaction(function () use ($cityProfiles): array {
                    $updated = 0; $customPhotos = 0;
                    foreach ($cityProfiles as $profile) {
                        $table = $profile['role'] === 'logistics' ? 'logistics_accounts' : 'courier_accounts';
                        $account = DB::table($table)->where('email', $profile['email'])->lockForUpdate()->first();
                        $registration = DB::table('registration_applications')->where('id', $account->registration_application_id)->lockForUpdate()->first();
                        $this->assertManaged($profile, $account, $registration);
                        $fields = $this->details($profile, $account);
                        $oldImage = trim((string) ($account->profile_image_path ?? ''));
                        if ($oldImage === '' || str_starts_with($oldImage, self::ASSET_PREFIX)) {
                            $path = self::ASSET_PREFIX.$profile['asset'];
                            $source = database_path('data/sari_city_demo_profiles/avatars/'.$profile['asset']);
                            $bytes = file_get_contents($source);
                            if (!Storage::disk('public')->exists($path) || hash('sha256', Storage::disk('public')->get($path)) !== hash('sha256', $bytes)) {
                                if (!Storage::disk('public')->put($path, $bytes)) throw new RuntimeException('Could not save a demo PNG. Earlier completed cities are preserved.');
                            }
                            $fields['profile_image_path'] = $path;
                        } else $customPhotos++;
                        $registrationFields = $this->details($profile, $registration, true);
                        if (isset($fields['profile_image_path']) && (empty($registration->profile_image_path) || str_starts_with($registration->profile_image_path, self::ASSET_PREFIX))) {
                            $registrationFields['profile_image_path'] = $fields['profile_image_path'];
                        }
                        $fields = $this->changedFields($account, $fields);
                        $registrationFields = $this->changedFields($registration, $registrationFields);
                        if ($fields) {
                            DB::table($table)->where('id', $account->id)->update(array_merge($fields, ['updated_at' => now()]));
                            $updated++;
                        }
                        if ($registrationFields) DB::table('registration_applications')->where('id', $registration->id)->update(array_merge($registrationFields, ['updated_at' => now()]));
                    }
                    return ['updated' => $updated, 'protected' => $customPhotos];
                }, 3);
                $changed += $counts['updated']; $protectedPhotos += $counts['protected']; $cityCount++;
                if ($cityCount % 10 === 0 || $cityCount === 149) $this->line('Cities enriched: '.$cityCount.'/149');
            }
            $this->info("Updated {$changed} demo profiles; preserved {$protectedPhotos} custom profile photos.");
            $this->line('Profile photos use the public storage disk. Run php artisan storage:link if the public storage link is not configured.');
            $this->line('Passwords, emails, phone numbers, provider links, availability, orders and financial records were unchanged.');
            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error(get_class($exception) === RuntimeException::class ? $exception->getMessage() : 'Profile enrichment stopped due to a database/filesystem issue. Completed cities remain; resolve the issue and rerun.');
            return self::FAILURE;
        } finally { if (is_resource($lock)) { flock($lock, LOCK_UN); fclose($lock); } }
    }

    private function profiles(): array
    {
        $path = database_path('data/sari_city_demo_profiles/profiles.json');
        if (!is_file($path)) throw new RuntimeException('Missing profile manifest. Install all package files.');
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $profiles = $data['profiles'] ?? [];
        if (($data['marker'] ?? '') !== 'SARI_CITY149_PROFILES_V1' || count($profiles) !== 7599 || count(array_unique(array_column($profiles, 'email'))) !== 7599) throw new RuntimeException('Expected 7,599 unique profile records.');
        $groups = collect($profiles)->groupBy('city_code');
        if ($groups->count() !== 149) throw new RuntimeException('Expected 149 profile city groups.');
        foreach ($groups as $group) {
            if ($group->count() !== 51 || $group->where('role','logistics')->count() !== 1 || $group->where('role','rider')->count() !== 50) throw new RuntimeException('Each city must have one logistics logo and 50 rider avatars.');
        }
        foreach ($profiles as $profile) {
            $code = $profile['city_code'] ?? '';
            $slot = (int) ($profile['slot'] ?? -1);
            $isProvider = $profile['role'] === 'logistics';
            $expectedEmail = ($isProvider ? 'logistics' : 'rider').'.city-'.$code.($isProvider ? '' : '.'.str_pad((string) $slot,3,'0',STR_PAD_LEFT)).'@example.test';
            if (!preg_match('/^\d{10}$/D', $code) || $profile['email'] !== $expectedEmail || ($isProvider ? $slot !== 0 : ($slot < 1 || $slot > 50))) throw new RuntimeException('Invalid managed profile identity.');
            $expectedAsset = $code.'/'.($isProvider ? 'logistics.png' : 'rider-'.str_pad((string) $slot,3,'0',STR_PAD_LEFT).'.png');
            if (($profile['asset'] ?? '') !== $expectedAsset) throw new RuntimeException('Invalid profile asset path.');
            $asset = database_path('data/sari_city_demo_profiles/avatars/'.$expectedAsset);
            if (!is_file($asset)) throw new RuntimeException('A required demo PNG is missing. Extract the complete package.');
            $size = getimagesize($asset);
            if (!$size || $size[0] !== 192 || $size[1] !== 192 || $size[2] !== IMAGETYPE_PNG) throw new RuntimeException('Unexpected demo image format.');
        }
        return $profiles;
    }

    private function assertManaged(array $profile, ?object $account, ?object $registration): void
    {
        if (!$account || !$registration || $account->deleted_at !== null || $registration->role !== $profile['role'] || $registration->admin_note !== self::SEED_MARKER.':'.$profile['city_code']) throw new RuntimeException('Missing/unrelated demo account: '.$profile['email'].'. Run the original city account seed first.');
        if ($profile['role'] === 'rider' && (int) $account->logistics_account_id !== (int) $registration->logistics_account_id) throw new RuntimeException('Rider provider link mismatch: '.$profile['email']);
    }

    private function preflight(array $profiles): void
    {
        foreach (collect($profiles)->groupBy('city_code') as $cityProfiles) {
            $emails = $cityProfiles->pluck('email')->all();
            $accounts = DB::table('logistics_accounts')->whereIn('email', $emails)->get()->keyBy('email')
                ->merge(DB::table('courier_accounts')->whereIn('email', $emails)->get()->keyBy('email'));
            $registrations = DB::table('registration_applications')->whereIn('email', $emails)->get()->keyBy('id');
            foreach ($cityProfiles as $profile) {
                $account = $accounts->get($profile['email']);
                $this->assertManaged($profile, $account, $registrations->get($account?->registration_application_id ?? 0));
            }
        }
    }

    private function details(array $profile, object $row, bool $registration = false): array
    {
        $fields = [];
        $originalName = $profile['role'] === 'logistics' ? 'Logistics Demo' : 'Rider Demo '.str_pad((string) $profile['slot'],3,'0',STR_PAD_LEFT);
        if (trim($row->first_name.' '.$row->last_name) === $originalName) {
            $fields['first_name'] = $profile['first_name']; $fields['last_name'] = $profile['last_name'];
        }
        if (empty($row->middle_initial)) $fields['middle_initial'] = $profile['middle_initial'];
        if ($row->sex === 'N/A') $fields['sex'] = $profile['sex'];
        if (substr((string) $row->birthday,0,10) === '2000-01-01') {
            $fields['birthday'] = $profile['birthday']; $fields['age'] = Carbon::parse($profile['birthday'])->age;
        }
        if ($row->street_address === 'SARI DEMO ADDRESS - not a verified hub') {
            $fields['street_address'] = implode(', ', ['SARI Demo Address (not a verified hub)', $row->barangay_name, $row->municipality_name, $row->province_name, 'Philippines']);
        }
        if ($profile['role'] === 'logistics') {
            if (empty($row->business_name) || $row->business_name === 'SARI Demo Logistics - '.$row->municipality_name) $fields['business_name'] = $profile['business_name'];
            if (empty($row->position) || $row->position === 'Demo manager') $fields['position'] = $profile['position'];
        } elseif (!$registration && (empty($row->vehicle_model) || $row->vehicle_model === 'Demo motorcycle')) $fields['vehicle_model'] = $profile['vehicle_model'];
        return $fields;
    }

    private function changedFields(object $row, array $fields): array
    {
        return array_filter($fields, fn ($value,$key) => (string) ($row->{$key} ?? '') !== (string) $value, ARRAY_FILTER_USE_BOTH);
    }

    private function backupCity(string $directory, string $cityCode, array $profiles): void
    {
        $path = $directory.'/'.$cityCode.'.json';
        if (is_file($path)) {
            $saved = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($saved) || count($saved) !== 51) throw new RuntimeException('Incomplete city profile backup; restore it before continuing.');
            return;
        }
        $snapshots = [];
        foreach ($profiles as $profile) {
            $table = $profile['role'] === 'logistics' ? 'logistics_accounts' : 'courier_accounts';
            $account = DB::table($table)->where('email', $profile['email'])->first();
            $registration = DB::table('registration_applications')->where('id', $account->registration_application_id)->first();
            $snapshots[] = ['table'=>$table,'id'=>$account->id,'email'=>$account->email,
                'before'=>array_intersect_key((array)$account,array_flip(self::FIELDS)),
                'registration_id'=>$registration->id,'registration_before'=>array_intersect_key((array)$registration,array_flip(self::FIELDS))];
        }
        $bytes = json_encode($snapshots, JSON_THROW_ON_ERROR);
        $file = fopen($path,'x');
        if (!$file) throw new RuntimeException('Could not create original-profile backup.');
        @chmod($path,0600);
        try { if (fwrite($file,$bytes) !== strlen($bytes) || !fflush($file)) throw new RuntimeException('Could not persist original-profile backup.'); }
        finally { fclose($file); }
    }
}
