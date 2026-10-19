<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('desktop_image'); // 1350x620 px recommended (~2.18:1)
            $table->string('tablet_image')->nullable(); // 1024x640 px (16:10)
            $table->string('mobile_image')->nullable(); // 768x768 px (1:1 square)
            $table->string('link_type')->default('none'); // none, catalog, category, contact, custom
            $table->string('link_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(1);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
