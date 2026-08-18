<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_accounts', function (Blueprint $table) {
            $table->string('realtime_token', 64)->nullable()->after('suspension_reason');
        });

        DB::table('seller_accounts')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->each(function ($seller) {
                DB::table('seller_accounts')
                    ->where('id', $seller->id)
                    ->update(['realtime_token' => Str::random(64)]);
            });

        Schema::table('seller_products', function (Blueprint $table) {
            $table->boolean('requires_re_review')->default(false)->after('reviewed_at');
            $table->timestamp('last_sensitive_edit_at')->nullable()->after('requires_re_review');
            $table->timestamp('archived_at')->nullable()->after('last_sensitive_edit_at');
            $table->string('archive_reason', 30)->nullable()->after('archived_at');

            $table->index(['seller_account_id', 'archived_at']);
        });
    }

    public function down(): void
    {
        Schema::table('seller_products', function (Blueprint $table) {
            $table->dropIndex(['seller_account_id', 'archived_at']);
            $table->dropColumn([
                'requires_re_review',
                'last_sensitive_edit_at',
                'archived_at',
                'archive_reason',
            ]);
        });

        Schema::table('seller_accounts', function (Blueprint $table) {
            $table->dropColumn('realtime_token');
        });
    }
};
