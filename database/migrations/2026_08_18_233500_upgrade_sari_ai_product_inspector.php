<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seller_products')) {
            throw new RuntimeException('seller_products table was not found. Run your original product migrations first.');
        }

        $this->addSellerProductColumns();
        $this->createOrUpgradeModerationLogs();
    }

    public function down(): void
    {
        if (Schema::hasTable('seller_products')) {
            $columns = array_values(array_filter([
                Schema::hasColumn('seller_products', 'ai_decision') ? 'ai_decision' : null,
                Schema::hasColumn('seller_products', 'ai_policy_category') ? 'ai_policy_category' : null,
                Schema::hasColumn('seller_products', 'ai_confidence') ? 'ai_confidence' : null,
                Schema::hasColumn('seller_products', 'ai_reason') ? 'ai_reason' : null,
                Schema::hasColumn('seller_products', 'ai_signals') ? 'ai_signals' : null,
                Schema::hasColumn('seller_products', 'ai_image_reviewed') ? 'ai_image_reviewed' : null,
                Schema::hasColumn('seller_products', 'ai_text_reviewed') ? 'ai_text_reviewed' : null,
                Schema::hasColumn('seller_products', 'ai_status') ? 'ai_status' : null,
                Schema::hasColumn('seller_products', 'ai_response_id') ? 'ai_response_id' : null,
            ]));

            if ($columns) {
                Schema::table('seller_products', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }

        // Keep product_moderation_logs and the V1 moderation fields intact on rollback
        // because an earlier SARI moderation installation may already depend on them.
        if (Schema::hasTable('product_moderation_logs')) {
            $columns = array_values(array_filter([
                Schema::hasColumn('product_moderation_logs', 'ai_decision') ? 'ai_decision' : null,
                Schema::hasColumn('product_moderation_logs', 'ai_policy_category') ? 'ai_policy_category' : null,
                Schema::hasColumn('product_moderation_logs', 'ai_confidence') ? 'ai_confidence' : null,
                Schema::hasColumn('product_moderation_logs', 'ai_reason') ? 'ai_reason' : null,
                Schema::hasColumn('product_moderation_logs', 'ai_signals') ? 'ai_signals' : null,
                Schema::hasColumn('product_moderation_logs', 'ai_image_reviewed') ? 'ai_image_reviewed' : null,
                Schema::hasColumn('product_moderation_logs', 'ai_text_reviewed') ? 'ai_text_reviewed' : null,
                Schema::hasColumn('product_moderation_logs', 'ai_status') ? 'ai_status' : null,
                Schema::hasColumn('product_moderation_logs', 'ai_response_id') ? 'ai_response_id' : null,
            ]));

            if ($columns) {
                Schema::table('product_moderation_logs', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }
    }

    private function addSellerProductColumns(): void
    {
        $columns = [
            'risk_score' => fn (Blueprint $table) => $table->unsignedTinyInteger('risk_score')->nullable(),
            'screening_engine' => fn (Blueprint $table) => $table->string('screening_engine', 150)->nullable(),
            'ai_flagged' => fn (Blueprint $table) => $table->boolean('ai_flagged')->default(false),
            'ai_categories' => fn (Blueprint $table) => $table->json('ai_categories')->nullable(),
            'screened_at' => fn (Blueprint $table) => $table->timestamp('screened_at')->nullable(),

            'ai_decision' => fn (Blueprint $table) => $table->string('ai_decision', 40)->nullable(),
            'ai_policy_category' => fn (Blueprint $table) => $table->string('ai_policy_category', 80)->nullable(),
            'ai_confidence' => fn (Blueprint $table) => $table->unsignedTinyInteger('ai_confidence')->nullable(),
            'ai_reason' => fn (Blueprint $table) => $table->text('ai_reason')->nullable(),
            'ai_signals' => fn (Blueprint $table) => $table->json('ai_signals')->nullable(),
            'ai_image_reviewed' => fn (Blueprint $table) => $table->boolean('ai_image_reviewed')->default(false),
            'ai_text_reviewed' => fn (Blueprint $table) => $table->boolean('ai_text_reviewed')->default(false),
            'ai_status' => fn (Blueprint $table) => $table->string('ai_status', 40)->nullable(),
            'ai_response_id' => fn (Blueprint $table) => $table->string('ai_response_id', 120)->nullable(),
        ];

        foreach ($columns as $name => $definition) {
            if (!Schema::hasColumn('seller_products', $name)) {
                Schema::table('seller_products', function (Blueprint $table) use ($definition) {
                    $definition($table);
                });
            }
        }
    }

    private function createOrUpgradeModerationLogs(): void
    {
        if (!Schema::hasTable('product_moderation_logs')) {
            Schema::create('product_moderation_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_product_id')->constrained('seller_products')->cascadeOnDelete();
                $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
                $table->string('source', 30);
                $table->string('decision', 30);
                $table->string('risk', 20);
                $table->unsignedTinyInteger('risk_score')->default(0);
                $table->string('engine', 150)->nullable();
                $table->json('matched_rules')->nullable();
                $table->json('matched_terms')->nullable();
                $table->boolean('ai_flagged')->default(false);
                $table->json('ai_categories')->nullable();
                $table->string('ai_decision', 40)->nullable();
                $table->string('ai_policy_category', 80)->nullable();
                $table->unsignedTinyInteger('ai_confidence')->nullable();
                $table->text('ai_reason')->nullable();
                $table->json('ai_signals')->nullable();
                $table->boolean('ai_image_reviewed')->default(false);
                $table->boolean('ai_text_reviewed')->default(false);
                $table->string('ai_status', 40)->nullable();
                $table->string('ai_response_id', 120)->nullable();
                $table->text('reason')->nullable();
                $table->timestamps();

                $table->index(['seller_account_id', 'created_at']);
                $table->index(['risk', 'decision']);
            });

            return;
        }

        $columns = [
            'ai_decision' => fn (Blueprint $table) => $table->string('ai_decision', 40)->nullable(),
            'ai_policy_category' => fn (Blueprint $table) => $table->string('ai_policy_category', 80)->nullable(),
            'ai_confidence' => fn (Blueprint $table) => $table->unsignedTinyInteger('ai_confidence')->nullable(),
            'ai_reason' => fn (Blueprint $table) => $table->text('ai_reason')->nullable(),
            'ai_signals' => fn (Blueprint $table) => $table->json('ai_signals')->nullable(),
            'ai_image_reviewed' => fn (Blueprint $table) => $table->boolean('ai_image_reviewed')->default(false),
            'ai_text_reviewed' => fn (Blueprint $table) => $table->boolean('ai_text_reviewed')->default(false),
            'ai_status' => fn (Blueprint $table) => $table->string('ai_status', 40)->nullable(),
            'ai_response_id' => fn (Blueprint $table) => $table->string('ai_response_id', 120)->nullable(),
        ];

        foreach ($columns as $name => $definition) {
            if (!Schema::hasColumn('product_moderation_logs', $name)) {
                Schema::table('product_moderation_logs', function (Blueprint $table) use ($definition) {
                    $definition($table);
                });
            }
        }
    }
};
