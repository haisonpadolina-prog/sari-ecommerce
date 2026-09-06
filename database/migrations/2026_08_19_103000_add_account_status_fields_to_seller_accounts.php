<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('seller_accounts', 'account_status')) {
            Schema::table('seller_accounts', function (Blueprint $table) {
                $table->string('account_status', 20)->default('active')->index();
            });
        }

        if (!Schema::hasColumn('seller_accounts', 'banned_at')) {
            Schema::table('seller_accounts', function (Blueprint $table) {
                $table->timestamp('banned_at')->nullable();
            });
        }

        if (!Schema::hasColumn('seller_accounts', 'ban_reason')) {
            Schema::table('seller_accounts', function (Blueprint $table) {
                $table->text('ban_reason')->nullable();
            });
        }

        if (!Schema::hasColumn('seller_accounts', 'deactivated_at')) {
            Schema::table('seller_accounts', function (Blueprint $table) {
                $table->timestamp('deactivated_at')->nullable();
            });
        }

        if (!Schema::hasColumn('seller_accounts', 'deactivation_reason')) {
            Schema::table('seller_accounts', function (Blueprint $table) {
                $table->text('deactivation_reason')->nullable();
            });
        }
    }

    public function down(): void
    {
        $columns = [];

        foreach ([
            'account_status',
            'banned_at',
            'ban_reason',
            'deactivated_at',
            'deactivation_reason',
        ] as $column) {
            if (Schema::hasColumn('seller_accounts', $column)) {
                $columns[] = $column;
            }
        }

        if ($columns) {
            Schema::table('seller_accounts', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
