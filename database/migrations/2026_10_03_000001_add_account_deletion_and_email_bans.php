<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['buyer_accounts', 'seller_accounts', 'courier_accounts', 'logistics_accounts'] as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }

        if (!Schema::hasTable('account_email_bans')) {
            Schema::create('account_email_bans', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('role', 30)->nullable();
                $table->unsignedBigInteger('account_id')->nullable();
                $table->unsignedBigInteger('banned_by_admin_account_id')->nullable();
                $table->text('reason')->nullable();
                $table->timestamp('banned_at')->nullable();
                $table->timestamp('unbanned_at')->nullable();
                $table->timestamps();
                $table->index(['email', 'unbanned_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('account_email_bans');

        foreach (['buyer_accounts', 'seller_accounts', 'courier_accounts', 'logistics_accounts'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
