<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Standard Account Codes
     */
    public const CODE_CASH = '1010';
    public const CODE_BANK = '1020';
    public const CODE_AR = '1100'; // Accounts Receivable
    public const CODE_INVENTORY = '1200'; // Inventory Asset
    public const CODE_AP = '2100'; // Accounts Payable
    public const CODE_EQUITY = '3000'; // Owner's Equity
    public const CODE_SALES = '4000'; // Sales Revenue
    public const CODE_COGS = '5000'; // Cost of Goods Sold
    public const CODE_OPEX = '6000'; // Operating Expenses

    public function getOrCreateAccount(string $code, string $name, string $type): Account
    {
        return Account::firstOrCreate(
            ['code' => $code],
            ['name' => $name, 'type' => $type, 'is_active' => true]
        );
    }

    public function initializeChartOfAccounts(): void
    {
        $this->getOrCreateAccount(self::CODE_CASH, 'Cash on Hand', 'asset');
        $this->getOrCreateAccount(self::CODE_BANK, 'Bank Account', 'asset');
        $this->getOrCreateAccount(self::CODE_AR, 'Accounts Receivable (Customer Dues)', 'asset');
        $this->getOrCreateAccount(self::CODE_INVENTORY, 'Inventory Asset (Flooring Stock)', 'asset');
        $this->getOrCreateAccount(self::CODE_AP, 'Accounts Payable (Supplier Dues)', 'liability');
        $this->getOrCreateAccount(self::CODE_EQUITY, "Owner's Equity / Capital", 'equity');
        $this->getOrCreateAccount(self::CODE_SALES, 'Sales Revenue', 'revenue');
        $this->getOrCreateAccount(self::CODE_COGS, 'Cost of Goods Sold (COGS)', 'expense');
        $this->getOrCreateAccount(self::CODE_OPEX, 'Operating & Showroom Expenses', 'expense');
    }

    /**
     * Post a confirmed Sale to the general ledger
     */
    public function postSale(Sale $sale): JournalEntry
    {
        $this->initializeChartOfAccounts();

        return DB::transaction(function () use ($sale) {
            $arAccount = Account::where('code', self::CODE_AR)->firstOrFail();
            $salesAccount = Account::where('code', self::CODE_SALES)->firstOrFail();
            $cogsAccount = Account::where('code', self::CODE_COGS)->firstOrFail();
            $invAccount = Account::where('code', self::CODE_INVENTORY)->firstOrFail();

            $totalSale = (float) $sale->total_amount;
            $cogsTotal = (float) $sale->total_cost;

            $entry = JournalEntry::create([
                'entry_number' => 'JRN-SALE-' . $sale->invoice_number,
                'date' => $sale->date,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'description' => "Invoice #{$sale->invoice_number} for customer {$sale->customer->name}",
                'created_by' => $sale->created_by,
            ]);

            // 1. Revenue recognition: Debit AR, Credit Sales Revenue
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $arAccount->id,
                'debit' => $totalSale,
                'credit' => 0,
                'description' => "Receivable for invoice #{$sale->invoice_number}",
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $totalSale,
                'description' => "Sales revenue from invoice #{$sale->invoice_number}",
            ]);

            // 2. Cost recognition: Debit COGS, Credit Inventory Asset
            if ($cogsTotal > 0) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $cogsAccount->id,
                    'debit' => $cogsTotal,
                    'credit' => 0,
                    'description' => "Cost of goods sold for invoice #{$sale->invoice_number}",
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $invAccount->id,
                    'debit' => 0,
                    'credit' => $cogsTotal,
                    'description' => "Inventory reduction for invoice #{$sale->invoice_number}",
                ]);
            }

            return $entry;
        });
    }

    /**
     * Post a Customer Payment receipt
     */
    public function postPayment(Payment $payment): JournalEntry
    {
        $this->initializeChartOfAccounts();

        return DB::transaction(function () use ($payment) {
            $settleCode = in_array($payment->payment_method, ['cash']) ? self::CODE_CASH : self::CODE_BANK;
            $assetAccount = Account::where('code', $settleCode)->firstOrFail();
            $arAccount = Account::where('code', self::CODE_AR)->firstOrFail();

            $amount = (float) $payment->amount;

            $entry = JournalEntry::create([
                'entry_number' => 'JRN-PAY-' . $payment->receipt_number,
                'date' => $payment->payment_date,
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'description' => "Payment receipt #{$payment->receipt_number} from {$payment->customer->name}",
                'created_by' => $payment->created_by,
            ]);

            // Debit Cash/Bank, Credit AR
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $assetAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => "Cash/Bank received - {$payment->receipt_number}",
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $arAccount->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => "Customer dues reduction - {$payment->receipt_number}",
            ]);

            return $entry;
        });
    }

    /**
     * Post a confirmed Purchase receipt
     */
    public function postPurchase(Purchase $purchase): JournalEntry
    {
        $this->initializeChartOfAccounts();

        return DB::transaction(function () use ($purchase) {
            $invAccount = Account::where('code', self::CODE_INVENTORY)->firstOrFail();
            $apAccount = Account::where('code', self::CODE_AP)->firstOrFail();

            $total = (float) $purchase->total_amount;

            $entry = JournalEntry::create([
                'entry_number' => 'JRN-PUR-' . $purchase->purchase_number,
                'date' => $purchase->purchase_date,
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'description' => "Purchase #{$purchase->purchase_number} from supplier {$purchase->supplier->name}",
                'created_by' => $purchase->created_by,
            ]);

            // Debit Inventory Asset, Credit AP
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $invAccount->id,
                'debit' => $total,
                'credit' => 0,
                'description' => "Flooring stock received from {$purchase->supplier->name}",
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $apAccount->id,
                'debit' => 0,
                'credit' => $total,
                'description' => "Accounts payable for purchase #{$purchase->purchase_number}",
            ]);

            return $entry;
        });
    }

    /**
     * Post a Supplier Settlement payment
     */
    public function postSupplierPayment(SupplierPayment $payment): JournalEntry
    {
        $this->initializeChartOfAccounts();

        return DB::transaction(function () use ($payment) {
            $settleCode = in_array($payment->payment_method, ['cash']) ? self::CODE_CASH : self::CODE_BANK;
            $assetAccount = Account::where('code', $settleCode)->firstOrFail();
            $apAccount = Account::where('code', self::CODE_AP)->firstOrFail();

            $amount = (float) $payment->amount;

            $entry = JournalEntry::create([
                'entry_number' => 'JRN-SPAY-' . $payment->payment_number,
                'date' => $payment->payment_date,
                'reference_type' => SupplierPayment::class,
                'reference_id' => $payment->id,
                'description' => "Payment #{$payment->payment_number} to supplier {$payment->supplier->name}",
                'created_by' => $payment->created_by,
            ]);

            // Debit AP, Credit Cash/Bank
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $apAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => "Accounts payable settlement - {$payment->payment_number}",
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $assetAccount->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => "Outflow from Cash/Bank - {$payment->payment_number}",
            ]);

            return $entry;
        });
    }

    /**
     * Post an operating expense
     */
    public function postExpense(Expense $expense): JournalEntry
    {
        $this->initializeChartOfAccounts();

        return DB::transaction(function () use ($expense) {
            $settleCode = in_array($expense->payment_method, ['cash']) ? self::CODE_CASH : self::CODE_BANK;
            $assetAccount = Account::where('code', $settleCode)->firstOrFail();
            $opexAccount = Account::where('code', self::CODE_OPEX)->firstOrFail();

            $amount = (float) $expense->amount;

            $entry = JournalEntry::create([
                'entry_number' => 'JRN-EXP-' . $expense->reference_number,
                'date' => $expense->date,
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'description' => "Expense: {$expense->title} ({$expense->category->name})",
                'created_by' => $expense->created_by,
            ]);

            // Debit Opex, Credit Cash/Bank
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $opexAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => "{$expense->title} - {$expense->category->name}",
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $assetAccount->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => "Payment for {$expense->title}",
            ]);

            return $entry;
        });
    }

    /**
     * Post a manual Credit (Money received)
     */
    public function postManualCredit(\App\Models\FinancialTransaction $txn): JournalEntry
    {
        $this->initializeChartOfAccounts();

        return DB::transaction(function () use ($txn) {
            $settleCode = in_array(strtolower($txn->payment_method ?? 'cash'), ['cash']) ? self::CODE_CASH : self::CODE_BANK;
            $cashAccount = Account::where('code', $settleCode)->firstOrFail();
            $equityAccount = Account::where('code', self::CODE_EQUITY)->firstOrFail();

            $amount = (float) $txn->amount;

            $entry = JournalEntry::create([
                'entry_number' => 'JRN-CR-' . str_pad($txn->id, 5, '0', STR_PAD_LEFT),
                'date' => $txn->transaction_date ? $txn->transaction_date->toDateString() : now()->toDateString(),
                'reference_type' => \App\Models\FinancialTransaction::class,
                'reference_id' => $txn->id,
                'description' => "Credit: {$txn->purpose}",
                'created_by' => $txn->created_by,
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $cashAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => $txn->purpose,
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $equityAccount->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => $txn->purpose,
            ]);

            return $entry;
        });
    }

    /**
     * Post a manual Debit (Money paid)
     */
    public function postManualDebit(\App\Models\FinancialTransaction $txn): JournalEntry
    {
        $this->initializeChartOfAccounts();

        return DB::transaction(function () use ($txn) {
            $settleCode = in_array(strtolower($txn->payment_method ?? 'cash'), ['cash']) ? self::CODE_CASH : self::CODE_BANK;
            $cashAccount = Account::where('code', $settleCode)->firstOrFail();
            $opexAccount = Account::where('code', self::CODE_OPEX)->firstOrFail();

            $amount = (float) $txn->amount;

            $entry = JournalEntry::create([
                'entry_number' => 'JRN-DR-' . str_pad($txn->id, 5, '0', STR_PAD_LEFT),
                'date' => $txn->transaction_date ? $txn->transaction_date->toDateString() : now()->toDateString(),
                'reference_type' => \App\Models\FinancialTransaction::class,
                'reference_id' => $txn->id,
                'description' => "Debit: {$txn->purpose}",
                'created_by' => $txn->created_by,
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $opexAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => $txn->purpose,
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => $txn->purpose,
            ]);

            return $entry;
        });
    }
}
