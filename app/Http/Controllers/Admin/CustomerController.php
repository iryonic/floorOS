<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('sales');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('customer_type')) {
            $query->where('customer_type', $type);
        }

        if ($duesOnly = $request->boolean('dues_only')) {
            $query->where('current_balance', '>', 0);
        }

        $customers = $query->orderBy('name')->paginate(15)->withQueryString();

        $totalReceivables = (float) Customer::where('current_balance', '>', 0)->sum('current_balance');
        $totalCustomers = Customer::count();

        return view('admin.customers.index', compact('customers', 'totalReceivables', 'totalCustomers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'customer_type' => 'required|in:individual,contractor,builder,interior_designer,business',
            'phone' => 'required|string|max:30|unique:customers,phone',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'gstin' => 'nullable|string|max:30',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $opening = (float) ($validated['opening_balance'] ?? 0);
        $validated['current_balance'] = $opening;

        $customer = Customer::create($validated);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', "Customer '{$customer->name}' added successfully.");
    }

    public function show(Customer $customer)
    {
        $customer->recalculateBalance();
        $customer->load(['sales.items', 'payments', 'quotations']);

        $totalPurchased = (float) $customer->sales()->where('status', 'confirmed')->sum('total_amount');
        $totalPaid = (float) $customer->payments()->sum('amount');

        return view('admin.customers.show', compact('customer', 'totalPurchased', 'totalPaid'));
    }

    public function statement(Customer $customer)
    {
        $customer->recalculateBalance();
        $customer->load(['sales' => function ($q) {
            $q->where('status', 'confirmed')->orderBy('date', 'asc');
        }, 'payments' => function ($q) {
            $q->orderBy('payment_date', 'asc');
        }]);

        // Build sorted ledger rows (opening, invoices, payments)
        $ledger = [];
        $balance = (float) $customer->opening_balance;

        if ($balance > 0) {
            $ledger[] = [
                'date' => $customer->created_at->format('Y-m-d'),
                'type' => 'Opening Balance',
                'ref' => 'OPENING',
                'description' => 'Initial customer ledger balance',
                'debit' => $balance,
                'credit' => 0,
                'balance' => $balance,
            ];
        }

        // Combine sales & payments
        $events = collect();
        foreach ($customer->sales as $sale) {
            $events->push([
                'date' => $sale->date->format('Y-m-d'),
                'type' => 'Invoice',
                'ref' => $sale->invoice_number,
                'description' => "Invoice #{$sale->invoice_number}",
                'debit' => (float) $sale->total_amount,
                'credit' => 0,
            ]);
        }

        foreach ($customer->payments as $payment) {
            $events->push([
                'date' => $payment->payment_date->format('Y-m-d'),
                'type' => 'Payment',
                'ref' => $payment->receipt_number,
                'description' => "Payment receipt via " . strtoupper($payment->payment_method),
                'debit' => 0,
                'credit' => (float) $payment->amount,
            ]);
        }

        $sortedEvents = $events->sortBy('date');
        foreach ($sortedEvents as $e) {
            $balance = $balance + $e['debit'] - $e['credit'];
            $e['balance'] = $balance;
            $ledger[] = $e;
        }

        return view('admin.customers.statement', compact('customer', 'ledger'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'customer_type' => 'required|in:individual,contractor,builder,interior_designer,business',
            'phone' => 'required|string|max:30|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'gstin' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer->update($validated);
        $customer->recalculateBalance();

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', "Customer details updated.");
    }
}
