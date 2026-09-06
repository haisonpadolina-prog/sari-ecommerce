<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('seller_products', 'risk_score')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->unsignedTinyInteger('risk_score')->nullable()->after('screening_risk');
            });
        }

        if (!Schema::hasColumn('seller_products', 'screening_engine')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->string('screening_engine', 80)->nullable()->after('risk_score');
            });
        }

        if (!Schema::hasColumn('seller_products', 'ai_flagged')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->boolean('ai_flagged')->default(false)->after('screening_engine');
            });
        }

        if (!Schema::hasColumn('seller_products', 'ai_categories')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->json('ai_categories')->nullable()->after('ai_flagged');
            });
        }

        if (!Schema::hasColumn('seller_products', 'screened_at')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->timestamp('screened_at')->nullable()->after('ai_categories');
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter([
            Schema::hasColumn('seller_products', 'risk_score') ? 'risk_score' : null,
            Schema::hasColumn('seller_products', 'screening_engine') ? 'screening_engine' : null,
            Schema::hasColumn('seller_products', 'ai_flagged') ? 'ai_flagged' : null,
            Schema::hasColumn('seller_products', 'ai_categories') ? 'ai_categories' : null,
            Schema::hasColumn('seller_products', 'screened_at') ? 'screened_at' : null,
        ]));

        if ($columns) {
            Schema::table('seller_products', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
