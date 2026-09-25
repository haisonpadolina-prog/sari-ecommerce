<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sellers')) {
            Schema::create('sellers', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('banner_path')->nullable();

                $table->enum('status', [
                    'pending',
                    'approved',
                    'suspended',
                    'rejected',
                ])->default('pending');

                $table->text('rejection_reason')->nullable();
                $table->unsignedSmallInteger('commission_bps')->default(800);

                $table->foreignId('pickup_address_id')
                    ->nullable()
                    ->constrained('addresses')
                    ->nullOnDelete();

                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('status');
            });
        }

        if (!Schema::hasTable('logistics_providers')) {
            Schema::create('logistics_providers', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('name');
                $table->string('slug')->unique();

                $table->enum('status', [
                    'pending',
                    'approved',
                    'suspended',
                    'rejected',
                ])->default('pending');

                $table->text('rejection_reason')->nullable();
                $table->string('contact_phone', 32)->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('status');
            });
        }

        if (!Schema::hasTable('riders')) {
            Schema::create('riders', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('logistics_provider_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->string('vehicle_type', 80);
                $table->string('plate_no', 40)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['logistics_provider_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();

                $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('categories')
                    ->nullOnDelete();

                $table->string('name');
                $table->string('slug')->unique();
                $table->unsignedInteger('position')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['parent_id', 'is_active']);
                $table->index(['is_active', 'position']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('riders');
        Schema::dropIfExists('logistics_providers');
        Schema::dropIfExists('sellers');
    }
};
