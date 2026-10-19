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
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->string('payment_method', 50)->default('cash')->after('source');
        });

        // Sync payment methods from existing reference records if available
        if (Schema::hasTable('payments')) {
            $payments = DB::table('payments')->select('id', 'payment_method')->get();
            foreach ($payments as $p) {
                if ($p->payment_method) {
                    DB::table('financial_transactions')
                        ->where('reference_type', Payment::class)
                        ->where('reference_id', $p->id)
                        ->update(['payment_method' => $p->payment_method]);
                }
            }
        }

        if (Schema::hasTable('supplier_payments')) {
            $supplierPayments = DB::table('supplier_payments')->select('id', 'payment_method')->get();
            foreach ($supplierPayments as $sp) {
                if ($sp->payment_method) {
                    DB::table('financial_transactions')
                        ->where('reference_type', SupplierPayment::class)
                        ->where('reference_id', $sp->id)
                        ->update(['payment_method' => $sp->payment_method]);
                }
            }
        }

        if (Schema::hasTable('expenses')) {
            $expenses = DB::table('expenses')->select('id', 'payment_method')->get();
            foreach ($expenses as $exp) {
                if ($exp->payment_method) {
                    DB::table('financial_transactions')
                        ->where('reference_type', Expense::class)
                        ->where('reference_id', $exp->id)
                        ->update(['payment_method' => $exp->payment_method]);
                }
            }
        }

        // Guarantee any NULL value becomes 'cash'
        DB::table('financial_transactions')
            ->whereNull('payment_method')
            ->orWhere('payment_method', '')
            ->update(['payment_method' => 'cash']);
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
