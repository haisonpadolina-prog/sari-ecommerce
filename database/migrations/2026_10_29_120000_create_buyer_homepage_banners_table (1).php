<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_homepage_banners', function (Blueprint $table) {
            $table->id();
            $table->string('slot', 32)->unique();
            $table->string('image_path', 500);
            $table->string('kicker', 100)->default('');
            $table->string('title', 160);
            $table->string('description', 250)->default('');
            $table->string('button_text', 60)->default('');
            $table->string('button_url', 255)->default('/buyer/products');
            $table->string('font_family', 32)->default('poppins');
            $table->unsignedTinyInteger('title_size')->default(30);
            $table->unsignedTinyInteger('description_size')->default(11);
            $table->unsignedTinyInteger('button_size')->default(11);
            $table->char('text_color', 7)->default('#FFFFFF');
            $table->char('button_bg', 7)->default('#C88912');
            $table->char('button_color', 7)->default('#FFFFFF');
            $table->unsignedTinyInteger('text_x')->default(6);
            $table->unsignedTinyInteger('text_y')->default(43);
            $table->unsignedTinyInteger('button_x')->default(6);
            $table->unsignedTinyInteger('button_y')->default(69);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_homepage_banners');
    }
};
