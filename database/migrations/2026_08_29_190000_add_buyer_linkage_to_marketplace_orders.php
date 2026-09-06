<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('marketplace_orders', 'buyer_account_id')) {
                $table->foreignId('buyer_account_id')
                    ->nullable()
                    ->after('seller_account_id')
                    ->constrained('buyer_accounts')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('marketplace_orders', 'buyer_social_account_id')) {
                $table->foreignId('buyer_social_account_id')
                    ->nullable()
                    ->after('buyer_account_id')
                    ->constrained('social_accounts')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('marketplace_orders', 'buyer_email')) {
                $table->string('buyer_email')->nullable()->after('buyer_phone')->index();
            }

            if (!Schema::hasColumn('marketplace_orders', 'checkout_reference')) {
                $table->string('checkout_reference', 80)->nullable()->after('order_number')->index();
            }

            if (!Schema::hasColumn('marketplace_orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            }

            if (!Schema::hasColumn('marketplace_orders', 'cancellation_reason')) {
                $table->string('cancellation_reason')->nullable()->after('cancelled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            if (Schema::hasColumn('marketplace_orders', 'buyer_social_account_id')) {
                $table->dropConstrainedForeignId('buyer_social_account_id');
            }

            if (Schema::hasColumn('marketplace_orders', 'buyer_account_id')) {
                $table->dropConstrainedForeignId('buyer_account_id');
            }

            foreach (['buyer_email', 'checkout_reference', 'cancelled_at', 'cancellation_reason'] as $column) {
                if (Schema::hasColumn('marketplace_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
