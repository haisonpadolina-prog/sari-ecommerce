<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_accounts', function (Blueprint $table) {
            $table->string('role', 40)
                ->default('super_admin')
                ->after('password');
            $table->boolean('can_manage_platform_settings')
                ->default(true)
                ->after('role');
        });

        Schema::create('platform_setting_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('version_number')->unique();
            $table->json('settings');
            $table->dateTime('effective_at')->index();
            $table->string('status', 30)->default('published')->index();
            $table->text('change_reason');
            $table->foreignId('created_by_admin_id')
                ->nullable()
                ->constrained('admin_accounts')
                ->nullOnDelete();
            $table->foreignId('commission_rate_id')
                ->nullable()
                ->constrained('commission_rates')
                ->nullOnDelete();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(
                ['status', 'effective_at'],
                'platform_setting_versions_status_effective'
            );
        });

        Schema::create('platform_setting_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_setting_version_id')
                ->nullable()
                ->constrained('platform_setting_versions')
                ->nullOnDelete();
            $table->foreignId('admin_account_id')
                ->nullable()
                ->constrained('admin_accounts')
                ->nullOnDelete();
            $table->string('setting_key', 120)->index();
            $table->string('action', 60)->index();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('reason')->nullable();
            $table->dateTime('effective_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(
                ['setting_key', 'created_at'],
                'platform_setting_audits_key_time'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_setting_audits');
        Schema::dropIfExists('platform_setting_versions');

        Schema::table('admin_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'can_manage_platform_settings',
            ]);
        });
    }
};
