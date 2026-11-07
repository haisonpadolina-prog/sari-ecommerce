<?php

namespace Database\Seeders;

use App\Support\SariDemoSellerBlueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Explicit, opt-in QA seed. Does not touch real sellers, users, role_user,
 * registration applications, catalog products, orders, or finance balances.
 *
 * On non-local environments, requires an additional explicit opt-in, and
 * every storefront stays paused so buyers cannot see/purchase fake listings.
 */
class Sari250DemoSellerAccountsSeeder extends Seeder
{
    public function run(): void
    {
        if (
            !app()->environment(['local', 'testing'])
            && getenv('SARI_ALLOW_DEMO_SELLERS') !== 'YES_FOR_QA_ONLY'
        ) {
            throw new RuntimeException(
                'Demo sellers are disabled outside local/testing. '
                .'Only an isolated QA environment may use SARI_ALLOW_DEMO_SELLERS=YES_FOR_QA_ONLY.'
            );
        }

        $secret = SariDemoSellerBlueprint::assertStrongMasterSecret(
            getenv('SARI_DEMO_SELLER_MASTER_SECRET') ?: null
        );

        if (!Schema::hasTable('seller_accounts')) {
            throw new RuntimeException('seller_accounts table is missing: run migrations first.');
        }

        $required = [
            'email', 'password', 'first_name', 'last_name', 'store_name',
            'line_of_business', 'store_description', 'seller_type',
            'registration_status', 'account_status', 'store_status',
            'approved_at', 'warning_count', 'realtime_token',
            'deleted_at', 'created_at', 'updated_at',
        ];
        $missing = array_diff($required, Schema::getColumnListing('seller_accounts'));
        if ($missing) {
            throw new RuntimeException(
                'Seller schema is outdated; run migrations. Missing: '.implode(', ', $missing)
            );
        }

        $blueprints = [];
        for ($index = 1; $index <= SariDemoSellerBlueprint::COUNT; $index++) {
            $account = SariDemoSellerBlueprint::account($index);
            $blueprints[$account['email']] = $account;
        }
        $emails = array_keys($blueprints);

        // Buyer login is resolved BEFORE seller login; reject cross-role email
        // collisions rather than silently generating unloggable seller accounts.
        foreach ([
            'admin_accounts', 'buyer_accounts', 'courier_accounts',
            'logistics_accounts', 'social_accounts', 'registration_applications',
        ] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'email')) {
                if (DB::table($table)->whereIn('email', $emails)->exists()) {
                    throw new RuntimeException("Reserved demo email conflict in {$table}; no accounts created.");
                }
            }
        }

        $existing = DB::table('seller_accounts')
            ->whereIn('email', $emails)
            ->get(['email', 'password', 'line_of_business', 'deleted_at'])
            ->keyBy('email');

        foreach ($existing as $email => $row) {
            if ($row->deleted_at !== null) {
                throw new RuntimeException("Demo email {$email} belongs to a deleted seller; aborting without restoring it.");
            }
            if ((string) $row->line_of_business !== $blueprints[$email]['line_of_business']) {
                throw new RuntimeException("Existing {$email} has an unexpected category; refusing to modify that seller.");
            }
            if (
                !is_string($row->password)
                || !Hash::check(SariDemoSellerBlueprint::password($email, $secret), $row->password)
            ) {
                throw new RuntimeException(
                    "Existing {$email} uses a different password/secret. "
                    .'Reuse the original master secret; no credentials were changed.'
                );
            }
        }

        $toCreate = [];
        $timestamp = now();

        foreach ($blueprints as $email => $definition) {
            if ($existing->has($email)) {
                continue;
            }

            $toCreate[] = [
                'email' => $email,
                'password' => Hash::make(SariDemoSellerBlueprint::password($email, $secret)),
                'first_name' => $definition['first_name'],
                'last_name' => $definition['last_name'],
                'store_name' => $definition['store_name'],
                'line_of_business' => $definition['line_of_business'],
                'store_description' => $definition['store_description'],
                'seller_type' => 'individual',
                'registration_status' => 'approved', // Synthetic login access only, not legal verification.
                'account_status' => 'active',
                'store_status' => 'paused', // Always hidden from buyer storefronts.
                'approved_at' => $timestamp,
                'warning_count' => 0,
                'realtime_token' => Str::random(64),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        DB::transaction(static function () use ($toCreate, $emails): void {
            foreach (array_chunk($toCreate, 50) as $batch) {
                DB::table('seller_accounts')->insert($batch);
            }

            $totals = DB::table('seller_accounts')
                ->whereIn('email', $emails)
                ->whereNull('deleted_at')
                ->selectRaw('line_of_business, COUNT(*) as total')
                ->groupBy('line_of_business')
                ->pluck('total', 'line_of_business');

            if ((int) $totals->sum() !== SariDemoSellerBlueprint::COUNT
                || $totals->count() !== count(SariDemoSellerBlueprint::CATEGORIES)) {
                throw new RuntimeException('Demo seller total/category coverage validation failed. Rolled back.');
            }

            foreach (SariDemoSellerBlueprint::CATEGORIES as $category) {
                if ((int) ($totals[$category] ?? 0) < 1) {
                    throw new RuntimeException("Missing {$category} category. Rolled back.");
                }
            }
        });

        if ($this->command) {
            $this->command->info(sprintf(
                'SARI QA sellers: %d newly created; %d already existed; %d total across %d categories. Stores are PAUSED.',
                count($toCreate), $existing->count(), SariDemoSellerBlueprint::COUNT,
                count(SariDemoSellerBlueprint::CATEGORIES)
            ));
            $this->command->warn('Synthetic auto-approved demo accounts ONLY. Do not use as genuine seller/KYC records.');
        }
    }
}
