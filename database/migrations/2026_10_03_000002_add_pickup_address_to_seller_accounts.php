<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('seller_accounts', 'pickup_address')) {
            Schema::table('seller_accounts', function (Blueprint $table): void {
                $table->text('pickup_address')->nullable()->after('pickup_instructions');
            });
        }

        // Preserve current behavior for existing sellers while separating the
        // editable pickup location from the verified registration address.
        DB::table('seller_accounts')
            ->whereNull('pickup_address')
            ->whereNotNull('street_address')
            ->update(['pickup_address' => DB::raw('street_address')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('seller_accounts', 'pickup_address')) {
            Schema::table('seller_accounts', function (Blueprint $table): void {
                $table->dropColumn('pickup_address');
            });
        }
    }
};
