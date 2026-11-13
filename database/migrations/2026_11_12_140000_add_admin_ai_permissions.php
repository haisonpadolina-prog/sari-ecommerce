<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_accounts', function (Blueprint $table) {
            $table->string('ai_role', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('admin_accounts', fn (Blueprint $table) => $table->dropColumn('ai_role'));
    }
};
