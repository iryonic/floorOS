<?php

use App\Models\Expense;
use App\Models\Payment;
use App\Models\SupplierPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 14, 2);
            $table->string('purpose', 255);
            $table->dateTime('transaction_date');
            $table->string('source', 50)->default('manual'); // manual, sale_payment, purchase_payment, expense
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['transaction_date', 'type']);
            $table->index(['source', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
        });

        // Seed existing financial transactions from existing payments, supplier payments, and expenses
        // 1. Existing Customer Sale Payments -> Credits
        if (Schema::hasTable('payments')) {
            $payments = Payment::with(['customer', 'sale'])->get();
            foreach ($payments as $p) {
                $customerName = $p->customer?->name ?? 'Customer';
                $invoiceRef = $p->sale ? " (Invoice #{$p->sale->invoice_number})" : " (#{$p->receipt_number})";
                DB::table('financial_transactions')->insert([
                    'type' => 'credit',
                    'amount' => $p->amount,
                    'purpose' => "Customer Payment: {$customerName}{$invoiceRef}",
                    'transaction_date' => $p->payment_date ? date('Y-m-d H:i:s', strtotime($p->payment_date)) : $p->created_at,
                    'source' => 'sale_payment',
                    'reference_type' => Payment::class,
                    'reference_id' => $p->id,
                    'created_by' => $p->created_by,
                    'notes' => $p->notes,
                    'created_at' => $p->created_at ?? now(),
                    'updated_at' => $p->updated_at ?? now(),
                ]);
            }
        }

        // 2. Existing Supplier Payments -> Debits
        if (Schema::hasTable('supplier_payments')) {
            $supplierPayments = SupplierPayment::with(['supplier', 'purchase'])->get();
            foreach ($supplierPayments as $sp) {
                $supplierName = $sp->supplier?->name ?? 'Supplier';
                $poRef = $sp->purchase ? " (PO #{$sp->purchase->purchase_number})" : " (#{$sp->payment_number})";
                DB::table('financial_transactions')->insert([
                    'type' => 'debit',
                    'amount' => $sp->amount,
                    'purpose' => "Supplier Payment: {$supplierName}{$poRef}",
                    'transaction_date' => $sp->payment_date ? date('Y-m-d H:i:s', strtotime($sp->payment_date)) : $sp->created_at,
                    'source' => 'purchase_payment',
                    'reference_type' => SupplierPayment::class,
                    'reference_id' => $sp->id,
                    'created_by' => $sp->created_by,
                    'notes' => $sp->notes,
                    'created_at' => $sp->created_at ?? now(),
                    'updated_at' => $sp->updated_at ?? now(),
                ]);
            }
        }

        // 3. Existing Showroom Expenses -> Debits
        if (Schema::hasTable('expenses')) {
            $expenses = Expense::with('category')->get();
            foreach ($expenses as $e) {
                $categoryName = $e->category?->name ?? 'General';
                DB::table('financial_transactions')->insert([
                    'type' => 'debit',
                    'amount' => $e->amount,
                    'purpose' => "Expense: {$e->title} ({$categoryName})",
                    'transaction_date' => $e->date ? date('Y-m-d H:i:s', strtotime($e->date)) : $e->created_at,
                    'source' => 'expense',
                    'reference_type' => Expense::class,
                    'reference_id' => $e->id,
                    'created_by' => $e->created_by,
                    'notes' => $e->notes,
                    'created_at' => $e->created_at ?? now(),
                    'updated_at' => $e->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
