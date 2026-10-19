<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductShade;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    protected InventoryService $inventoryService;
    protected AccountingService $accountingService;

    public function __construct(InventoryService $inventoryService, AccountingService $accountingService)
    {
        $this->inventoryService = $inventoryService;
        $this->accountingService = $accountingService;
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'creator']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                    ->orWhere('supplier_invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $purchases = $query->latest('purchase_date')->paginate(15)->withQueryString();

        $totalPurchases = (float) Purchase::where('status', 'received')->sum('total_amount');
        $totalPaid = (float) Purchase::where('status', 'received')->sum('paid_amount');
        $totalPayables = (float) Purchase::where('status', 'received')->sum('due_amount');

        return view('admin.purchases.index', compact(
            'purchases', 'totalPurchases', 'totalPaid', 'totalPayables'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $shades = ProductShade::with('product')->where('is_active', true)->get();
        $nextNumber = 'PUR-' . date('Ym') . '-' . str_pad((Purchase::count() + 1), 3, '0', STR_PAD_LEFT);

        return view('admin.purchases.create', compact('suppliers', 'shades', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_invoice_number' => 'nullable|string|max:100',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_shade_id' => 'required|exists:product_shades,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'other_charges' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,upi,cheque,cash',
            'payment_reference' => 'nullable|string|max:100',
            'status' => 'required|in:draft,received',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $subtotal = 0.0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $shade = ProductShade::findOrFail($item['product_shade_id']);
                $qty = (float) $item['quantity'];
                $cost = (float) $item['unit_cost'];
                $lineTotal = $qty * $cost;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'shade' => $shade,
                    'qty' => $qty,
                    'cost' => $cost,
                    'total' => $lineTotal,
                ];
            }

            $discount = (float) ($validated['discount_amount'] ?? 0);
            $taxable = max(0, $subtotal - $discount);
            $taxRate = (float) $validated['tax_rate'];
            $taxAmount = round($taxable * ($taxRate / 100), 2);
            $otherCharges = (float) ($validated['other_charges'] ?? 0);
            $finalTotal = round($taxable + $taxAmount + $otherCharges, 2);

            $paid = (float) ($validated['paid_amount'] ?? 0);
            $paid = min($finalTotal, $paid);
            $due = max(0, $finalTotal - $paid);

            $paymentStatus = 'unpaid';
            if ($paid >= $finalTotal && $finalTotal > 0) {
                $paymentStatus = 'paid';
            } elseif ($paid > 0) {
                $paymentStatus = 'partially_paid';
            }

            $number = 'PUR-' . date('Ym') . '-' . str_pad((Purchase::count() + 1), 3, '0', STR_PAD_LEFT);

            $purchase = Purchase::create([
                'purchase_number' => $number,
                'supplier_id' => $validated['supplier_id'],
                'supplier_invoice_number' => $validated['supplier_invoice_number'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'other_charges' => $otherCharges,
                'total_amount' => $finalTotal,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_status' => $paymentStatus,
                'status' => $validated['status'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Save purchase items
            foreach ($itemsData as $row) {
                $shade = $row['shade'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $shade->product_id,
                    'product_shade_id' => $shade->id,
                    'description' => "{$shade->product->name} — {$shade->shade_name}",
                    'sku' => $shade->sku,
                    'quantity' => $row['qty'],
                    'unit' => $shade->unit,
                    'unit_cost' => $row['cost'],
                    'tax_amount' => 0,
                    'total' => $row['total'],
                ]);

                // ONLY received purchases increase inventory!
                if ($purchase->status === 'received') {
                    $this->inventoryService->recordMovement(
                        shade: $shade,
                        type: 'purchase',
                        quantity: $row['qty'],
                        direction: 'in',
                        unitCost: $row['cost'],
                        reason: "Purchase receipt #{$purchase->purchase_number}",
                        related: $purchase,
                        user: auth()->user()
                    );

                    // Update shade cost price with weighted average
                    $prevStock = (float) $shade->stock_quantity;
                    $prevCost = (float) $shade->cost_price;
                    $newCost = $row['cost'];
                    if (($prevStock + $row['qty']) > 0) {
                        $weightedCost = (($prevStock * $prevCost) + ($row['qty'] * $newCost)) / ($prevStock + $row['qty']);
                        $shade->update(['cost_price' => round($weightedCost, 2)]);
                    }
                }
            }

            // If paid > 0, record supplier payment
            if ($paid > 0) {
                $paymentNumber = 'SPAY-' . date('Ym') . '-' . str_pad((SupplierPayment::count() + 1), 3, '0', STR_PAD_LEFT);
                SupplierPayment::create([
                    'payment_number' => $paymentNumber,
                    'supplier_id' => $purchase->supplier_id,
                    'purchase_id' => $purchase->id,
                    'payment_date' => $validated['purchase_date'],
                    'amount' => $paid,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['payment_reference'] ?? null,
                    'notes' => "Payment on purchase #{$purchase->purchase_number}",
                    'created_by' => auth()->id(),
                ]);
            }

            if ($purchase->status === 'received') {
                $this->accountingService->postPurchase($purchase);
                $purchase->supplier->recalculateBalance();
            }

            return redirect()->route('admin.purchases.show', $purchase)
                ->with('success', "Purchase #{$purchase->purchase_number} saved successfully.");
        });
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.shade.product', 'payments', 'creator']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function addPayment(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . (float) $purchase->due_amount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,upi,cheque,cash',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $paymentNumber = 'SPAY-' . date('Ym') . '-' . str_pad((SupplierPayment::count() + 1), 3, '0', STR_PAD_LEFT);

        $payment = SupplierPayment::create([
            'payment_number' => $paymentNumber,
            'supplier_id' => $purchase->supplier_id,
            'purchase_id' => $purchase->id,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? "Payment for purchase #{$purchase->purchase_number}",
            'created_by' => auth()->id(),
        ]);

        $this->accountingService->postSupplierPayment($payment);

        return redirect()->route('admin.purchases.show', $purchase)
            ->with('success', "Supplier payment #{$payment->payment_number} recorded successfully.");
    }
}
