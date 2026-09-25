<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('social_accounts', 'account_status')) {
            Schema::table('social_accounts', function (Blueprint $table): void {
                $table->string('account_status', 20)
                    ->default('active')
                    ->index()
                    ->after('avatar_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('social_accounts', 'account_status')) {
            Schema::table('social_accounts', function (Blueprint $table): void {
                $table->dropColumn('account_status');
            });
        }
    }
};
