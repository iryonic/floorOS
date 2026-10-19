<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\FinancialTransaction;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\ProductShade;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request)
    {
        // 1. Resolve Date Range Filters (default: 'this_month')
        $period = $request->input('period', 'this_month');
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'this_week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'this_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'this_quarter':
                $startDate = now()->startOfQuarter();
                $endDate = now()->endOfQuarter();
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                }
                if ($request->filled('end_date')) {
                    $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                }
                break;
            case 'all':
            default:
                // No date restriction
                break;
        }

        // 2. Synchronized KPI Metrics for the selected period
        $creditsQuery = FinancialTransaction::where('type', 'credit');
        $debitsQuery = FinancialTransaction::where('type', 'debit');

        if ($startDate && $endDate) {
            $creditsQuery->whereBetween('transaction_date', [$startDate, $endDate]);
            $debitsQuery->whereBetween('transaction_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $creditsQuery->where('transaction_date', '>=', $startDate);
            $debitsQuery->where('transaction_date', '>=', $startDate);
        } elseif ($endDate) {
            $creditsQuery->where('transaction_date', '<=', $endDate);
            $debitsQuery->where('transaction_date', '<=', $endDate);
        }

        $totalCredit = (float) $creditsQuery->sum('amount');
        $creditCount = $creditsQuery->count();

        $totalDebit = (float) $debitsQuery->sum('amount');
        $debitCount = $debitsQuery->count();

        $netCashFlow = $totalCredit - $totalDebit;

        // Sales Revenue from confirmed sales for the same period
        $salesQuery = Sale::where('status', 'confirmed');
        if ($startDate && $endDate) {
            $salesQuery->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
        } elseif ($startDate) {
            $salesQuery->where('date', '>=', $startDate->toDateString());
        } elseif ($endDate) {
            $salesQuery->where('date', '<=', $endDate->toDateString());
        }
        $salesRevenue = (float) $salesQuery->sum('total_amount');
        $salesCount = $salesQuery->count();

        // Current Inventory Value at Cost (Kept separate from cash balances)
        $inventoryValue = (float) ProductShade::where('stock_quantity', '>', 0)
            ->selectRaw('SUM(stock_quantity * cost_price) as total_val')
            ->value('total_val') ?? 0.00;

        // 3. Transactions query with filters
        $txQuery = FinancialTransaction::with(['creator', 'reference']);

        if ($startDate && $endDate) {
            $txQuery->whereBetween('transaction_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $txQuery->where('transaction_date', '>=', $startDate);
        } elseif ($endDate) {
            $txQuery->where('transaction_date', '<=', $endDate);
        }

        if ($type = $request->input('type')) {
            if (in_array($type, ['credit', 'debit'])) {
                $txQuery->where('type', $type);
            }
        }

        if ($source = $request->input('source')) {
            if ($source === 'manual') {
                $txQuery->where('source', 'manual');
            } elseif ($source === 'auto') {
                $txQuery->where('source', '!=', 'manual');
            }
        }

        if ($paymentMethod = $request->input('payment_method')) {
            if (in_array($paymentMethod, ['cash', 'mpay', 'gpay', 'paytm', 'phonepe', 'bank_transfer', 'cheque'])) {
                $txQuery->where('payment_method', $paymentMethod);
            }
        }

        if ($search = $request->input('search')) {
            $txQuery->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $transactions = $txQuery->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.accounting.index', compact(
            'period',
            'startDate',
            'endDate',
            'totalCredit',
            'creditCount',
            'totalDebit',
            'debitCount',
            'netCashFlow',
            'salesRevenue',
            'salesCount',
            'inventoryValue',
            'paymentMethod',
            'transactions'
        ));
    }

    public function storeCredit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'purpose' => 'required|string|max:255',
            'payment_method' => 'nullable|string|in:cash,mpay,gpay,paytm,phonepe,bank_transfer,cheque',
            'notes' => 'nullable|string|max:1000',
            'return_url' => 'nullable|string',
        ]);

        $paymentMethod = !empty($validated['payment_method']) ? strtolower($validated['payment_method']) : 'cash';

        $transaction = DB::transaction(function () use ($validated, $paymentMethod) {
            $txn = FinancialTransaction::create([
                'type' => 'credit',
                'amount' => $validated['amount'],
                'purpose' => trim($validated['purpose']),
                'transaction_date' => now(),
                'source' => 'manual',
                'payment_method' => $paymentMethod,
                'created_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            try {
                $this->accountingService->postManualCredit($txn);
            } catch (\Throwable $e) {
                logger()->error('Failed to post manual credit journal entry: ' . $e->getMessage());
            }

            return $txn;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Credit of ₹' . number_format($transaction->amount, 2) . ' recorded successfully.',
                'transaction' => $transaction,
            ]);
        }

        if ($request->filled('return_url')) {
            return redirect($request->input('return_url'))
                ->with('success', 'Credit deposit of ₹' . number_format($transaction->amount, 2) . ' recorded successfully.');
        }

        return redirect()->back(fallback: route('admin.dashboard'))
            ->with('success', 'Credit deposit of ₹' . number_format($transaction->amount, 2) . ' recorded successfully.');
    }

    public function storeDebit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'purpose' => 'required|string|max:255',
            'payment_method' => 'nullable|string|in:cash,mpay,gpay,paytm,phonepe,bank_transfer,cheque',
            'notes' => 'nullable|string|max:1000',
            'return_url' => 'nullable|string',
        ]);

        $paymentMethod = !empty($validated['payment_method']) ? strtolower($validated['payment_method']) : 'cash';

        $transaction = DB::transaction(function () use ($validated, $paymentMethod) {
            $txn = FinancialTransaction::create([
                'type' => 'debit',
                'amount' => $validated['amount'],
                'purpose' => trim($validated['purpose']),
                'transaction_date' => now(),
                'source' => 'manual',
                'payment_method' => $paymentMethod,
                'created_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            try {
                $this->accountingService->postManualDebit($txn);
            } catch (\Throwable $e) {
                logger()->error('Failed to post manual debit journal entry: ' . $e->getMessage());
            }

            return $txn;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Debit of ₹' . number_format($transaction->amount, 2) . ' recorded successfully.',
                'transaction' => $transaction,
            ]);
        }

        if ($request->filled('return_url')) {
            return redirect($request->input('return_url'))
                ->with('success', 'Debit expense of ₹' . number_format($transaction->amount, 2) . ' recorded successfully.');
        }

        return redirect()->back(fallback: route('admin.dashboard'))
            ->with('success', 'Debit expense of ₹' . number_format($transaction->amount, 2) . ' recorded successfully.');
    }

    public function destroyTransaction(FinancialTransaction $transaction)
    {
        if ($transaction->source !== 'manual') {
            return redirect()->back()
                ->with('error', 'Only manually created financial records can be deleted here. Automatic transactions must be managed via their original sale, purchase, or expense vouchers.');
        }

        $amount = $transaction->amount;
        $type = ucfirst($transaction->type);
        $transaction->delete();

        return redirect()->back()
            ->with('success', "Manual {$type} of ₹" . number_format($amount, 2) . " removed successfully.");
    }

    public function profitAndLoss(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // 1. Net Sales Revenue (excluding sales taxes)
        $salesQuery = Sale::where('status', 'confirmed')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

        $grossSales = (float) $salesQuery->sum('subtotal');
        $totalDiscounts = (float) $salesQuery->sum('discount_amount');
        $netRevenue = max(0, $grossSales - $totalDiscounts);

        // 2. Cost of Goods Sold (COGS)
        $cogs = (float) $salesQuery->with('items')->get()->sum(function ($sale) {
            return $sale->total_cost;
        });

        // 3. Gross Profit
        $grossProfit = $netRevenue - $cogs;
        $grossMarginPercent = $netRevenue > 0 ? round(($grossProfit / $netRevenue) * 100, 1) : 0;

        // 4. Operating Expenses breakdown
        $expenses = Expense::with('category')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get();

        $expensesByCategory = $expenses->groupBy('expense_category_id')->map(function ($group) {
            return [
                'category' => $group->first()->category->name ?? 'Uncategorized',
                'total' => (float) $group->sum('amount'),
            ];
        });

        $totalOpex = (float) $expenses->sum('amount');

        // 5. Operating Profit (Net Profit from Operations)
        $operatingProfit = $grossProfit - $totalOpex;
        $netMarginPercent = $netRevenue > 0 ? round(($operatingProfit / $netRevenue) * 100, 1) : 0;

        return view('admin.accounting.profit-loss', compact(
            'startDate',
            'endDate',
            'grossSales',
            'totalDiscounts',
            'netRevenue',
            'cogs',
            'grossProfit',
            'grossMarginPercent',
            'expensesByCategory',
            'totalOpex',
            'operatingProfit',
            'netMarginPercent'
        ));
    }

    public function journal(Request $request)
    {
        $query = JournalEntry::with(['lines.account', 'creator']);

        if ($search = $request->input('search')) {
            $query->where('entry_number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $entries = $query->latest('date')->paginate(15)->withQueryString();

        return view('admin.accounting.journal', compact('entries'));
    }
}
