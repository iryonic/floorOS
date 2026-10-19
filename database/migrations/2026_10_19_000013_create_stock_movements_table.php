<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique(); // MOV-202610-001
            $table->foreignId('product_shade_id')->constrained()->cascadeOnDelete();
            $table->string('sku', 100);
            $table->string('type', 30); // opening, purchase, sale, customer_return, supplier_return, damage, adjustment, audit
            $table->enum('direction', ['in', 'out']);
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 20)->default('sq ft');
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('previous_stock', 10, 2);
            $table->decimal('new_stock', 10, 2);
            $table->string('related_type')->nullable(); // Sale, Purchase, Adjustment
            $table->unsignedBigInteger('related_id')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['product_shade_id', 'type', 'occurred_at']);
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
