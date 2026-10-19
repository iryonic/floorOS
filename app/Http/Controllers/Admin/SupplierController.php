<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('purchases');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($payablesOnly = $request->boolean('payables_only')) {
            $query->where('current_balance', '>', 0);
        }

        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();

        $totalPayables = (float) Supplier::where('current_balance', '>', 0)->sum('current_balance');
        $totalSuppliers = Supplier::count();

        return view('admin.suppliers.index', compact('suppliers', 'totalPayables', 'totalSuppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:30',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $opening = (float) ($validated['opening_balance'] ?? 0);
        $validated['current_balance'] = $opening;

        $supplier = Supplier::create($validated);

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', "Supplier '{$supplier->name}' added successfully.");
    }

    public function show(Supplier $supplier)
    {
        $supplier->recalculateBalance();
        $supplier->load(['purchases.items', 'payments']);

        $totalPurchases = (float) $supplier->purchases()->where('status', 'received')->sum('total_amount');
        $totalPaid = (float) $supplier->payments()->sum('amount');

        return view('admin.suppliers.show', compact('supplier', 'totalPurchases', 'totalPaid'));
    }

    public function statement(Supplier $supplier)
    {
        $supplier->recalculateBalance();
        $supplier->load(['purchases' => function ($q) {
            $q->where('status', 'received')->orderBy('purchase_date', 'asc');
        }, 'payments' => function ($q) {
            $q->orderBy('payment_date', 'asc');
        }]);

        $ledger = [];
        $balance = (float) $supplier->opening_balance;

        if ($balance > 0) {
            $ledger[] = [
                'date' => $supplier->created_at->format('Y-m-d'),
                'type' => 'Opening Balance',
                'ref' => 'OPENING',
                'description' => 'Initial supplier ledger payable',
                'debit' => 0,
                'credit' => $balance,
                'balance' => $balance,
            ];
        }

        $events = collect();
        foreach ($supplier->purchases as $pur) {
            $events->push([
                'date' => $pur->purchase_date->format('Y-m-d'),
                'type' => 'Purchase',
                'ref' => $pur->purchase_number,
                'description' => "Purchase #{$pur->purchase_number} (Inv #{$pur->supplier_invoice_number})",
                'debit' => 0,
                'credit' => (float) $pur->total_amount,
            ]);
        }

        foreach ($supplier->payments as $payment) {
            $events->push([
                'date' => $payment->payment_date->format('Y-m-d'),
                'type' => 'Payment',
                'ref' => $payment->payment_number,
                'description' => "Payment settlement via " . strtoupper($payment->payment_method),
                'debit' => (float) $payment->amount,
                'credit' => 0,
            ]);
        }

        $sortedEvents = $events->sortBy('date');
        foreach ($sortedEvents as $e) {
            $balance = $balance + $e['credit'] - $e['debit'];
            $e['balance'] = $balance;
            $ledger[] = $e;
        }

        return view('admin.suppliers.statement', compact('supplier', 'ledger'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:1000',
        ]);

        $supplier->update($validated);
        $supplier->recalculateBalance();

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', "Supplier details updated.");
    }
}
