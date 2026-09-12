<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('reporter_role', 20)->index();
            $table->string('reporter_identifier')->nullable()->index();
            $table->string('subject');
            $table->text('description');
            $table->string('status', 20)->default('open')->index();
            $table->text('admin_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_complaints');
    }
};
