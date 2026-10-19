<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request)
    {
        $query = Expense::with(['category', 'creator']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('payee', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('expense_category_id')) {
            $query->where('expense_category_id', $categoryId);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('date', '<=', $dateTo);
        }

        $expenses = $query->latest('date')->paginate(15)->withQueryString();
        $categories = ExpenseCategory::where('is_active', true)->get();

        $totalExpenses = (float) Expense::sum('amount');
        $thisMonthExpenses = (float) Expense::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('admin.expenses.index', compact(
            'expenses', 'categories', 'totalExpenses', 'thisMonthExpenses'
        ));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $nextRef = 'EXP-' . date('Ym') . '-' . str_pad((Expense::count() + 1), 3, '0', STR_PAD_LEFT);

        return view('admin.expenses.create', compact('categories', 'nextRef'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,upi,bank_transfer,card,cheque',
            'payee' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $receiptPath = '/uploads/receipts/' . $filename;
        }

        $reference = 'EXP-' . date('Ym') . '-' . str_pad((Expense::count() + 1), 3, '0', STR_PAD_LEFT);

        $expense = Expense::create([
            'reference_number' => $reference,
            'expense_category_id' => $validated['expense_category_id'],
            'date' => $validated['date'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payee' => $validated['payee'] ?? null,
            'receipt_attachment' => $receiptPath,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $this->accountingService->postExpense($expense);

        return redirect()->route('admin.expenses.index')
            ->with('success', "Expense '{$expense->title}' (₹" . number_format($expense->amount, 2) . ") recorded.");
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted.');
    }
}
