<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('social_accounts')) {
            return;
        }

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('provider', 30);
            $table->string('provider_user_id', 191);

            $table->string('email')->nullable()->index();
            $table->string('name')->nullable();
            $table->text('avatar_url')->nullable();

            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'provider',
                    'provider_user_id',
                ],
                'social_accounts_provider_user_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
