<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable();
            $table->string('collection_name')->nullable();
            $table->string('material_type')->nullable(); // Engineered Hardwood, Solid Wood, SPC, Luxury Vinyl, Laminate, Ceramic
            $table->string('thickness')->nullable(); // e.g. 12mm, 8mm, 5.5mm
            $table->string('dimensions')->nullable(); // e.g. 1220 x 180 mm / 48 x 7 inches
            $table->string('surface_finish')->nullable(); // Hand-scraped, Embossed in Register, Matte, Wire-brushed
            $table->string('suitable_for')->nullable(); // Living, Commercial, High Traffic, Bedroom, Kitchen
            $table->string('warranty_years')->nullable(); // e.g. 15 Years Residential
            $table->string('installation_type')->nullable(); // Click-Lock, Tongue & Groove, Direct Glue
            $table->string('unit_of_measure', 20)->default('sq ft'); // sq ft, sq m, box, piece, roll
            $table->decimal('coverage_per_box', 8, 2)->nullable(); // e.g. 21.50 sq ft/box
            $table->string('primary_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->longText('description')->nullable();
            $table->json('specifications')->nullable();
            $table->decimal('base_price', 10, 2)->default(0);
            $table->boolean('is_featured')->default(false);
            $table->string('status', 20)->default('published'); // draft, published, archived
            $table->boolean('is_public')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
