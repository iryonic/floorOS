<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_shades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('shade_name'); // e.g. Natural Oak, Smoked Honey, Ash Grey
            $table->string('shade_code', 50)->nullable(); // e.g. OAK-01
            $table->string('sku', 100)->unique(); // e.g. OAK-01-ENG
            $table->string('color_family', 50)->nullable(); // Natural, Honey, Grey, Dark Walnut, Beige
            $table->string('finish', 50)->nullable(); // Matte, Satin, Brushed, Glossy
            $table->string('image')->nullable();
            $table->string('swatch_image')->nullable();
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 2)->default(0); // restricted to authorized staff
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->string('unit', 20)->default('sq ft');
            $table->decimal('reorder_threshold', 10, 2)->default(100);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'is_active', 'is_public']);
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_shades');
    }
};
