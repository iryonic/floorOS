<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_number', 50)->unique();
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_shade_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('area_sqft', 8, 2)->nullable();
            $table->string('preferred_contact', 20)->default('whatsapp'); // call, whatsapp, email
            $table->text('message')->nullable();
            $table->string('status', 30)->default('new'); // new, contacted, quotation_sent, follow_up, converted, closed
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->foreignId('converted_quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->foreignId('converted_sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
