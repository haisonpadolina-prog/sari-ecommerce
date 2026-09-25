<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_accounts', function (Blueprint $table) {
            $table->timestamp('profile_updated_at')->nullable()->after('can_manage_platform_settings');
            $table->timestamp('password_changed_at')->nullable()->after('profile_updated_at');
            $table->timestamp('last_login_at')->nullable()->after('password_changed_at');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->text('last_login_user_agent')->nullable()->after('last_login_ip');
            $table->unsignedBigInteger('login_count')->default(0)->after('last_login_user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('admin_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'profile_updated_at',
                'password_changed_at',
                'last_login_at',
                'last_login_ip',
                'last_login_user_agent',
                'login_count',
            ]);
        });
    }
};
