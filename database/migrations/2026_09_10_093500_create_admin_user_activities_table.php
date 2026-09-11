<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_user_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_account_id')->nullable()->index();
            $table->string('user_role', 30);
            $table->unsignedBigInteger('user_id');
            $table->string('action', 60);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_role', 'user_id', 'created_at'], 'admin_user_activity_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_user_activities');
    }
};
