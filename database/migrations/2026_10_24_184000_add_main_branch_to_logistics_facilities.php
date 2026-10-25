<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_facilities', function (Blueprint $table): void {
            $table->boolean('is_main_branch')
                ->default(false)
                ->after('is_active');

            $table->index(
                ['logistics_account_id', 'is_main_branch', 'is_active'],
                'logistics_facilities_main_branch_idx'
            );
        });

        // Preserve existing networks without requiring manual setup before use.
        // For every Logistics provider that already has facilities, promote the
        // oldest active hub (or oldest hub if none are active) as its first Main Branch.
        DB::table('logistics_facilities')
            ->select('logistics_account_id')
            ->whereNotNull('logistics_account_id')
            ->distinct()
            ->orderBy('logistics_account_id')
            ->pluck('logistics_account_id')
            ->each(function ($providerId): void {
                $alreadyMain = DB::table('logistics_facilities')
                    ->where('logistics_account_id', $providerId)
                    ->where('is_main_branch', true)
                    ->exists();

                if ($alreadyMain) {
                    return;
                }

                $facilityId = DB::table('logistics_facilities')
                    ->where('logistics_account_id', $providerId)
                    ->orderByDesc('is_active')
                    ->orderBy('id')
                    ->value('id');

                if ($facilityId) {
                    DB::table('logistics_facilities')
                        ->where('id', $facilityId)
                        ->update([
                            'is_main_branch' => true,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('logistics_facilities', function (Blueprint $table): void {
            $table->dropIndex('logistics_facilities_main_branch_idx');
            $table->dropColumn('is_main_branch');
        });
    }
};
