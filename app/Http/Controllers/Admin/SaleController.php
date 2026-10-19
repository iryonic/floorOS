<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\ProductShade;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
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
        $query = Sale::with(['customer', 'creator']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->input('payment_status')) {
            $query->where('payment_status', $status);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('date', '<=', $dateTo);
        }

        $sales = $query->latest('date')->paginate(15)->withQueryString();

        $totalSalesAmount = (float) Sale::where('status', 'confirmed')->sum('total_amount');
        $totalReceived = (float) Sale::where('status', 'confirmed')->sum('paid_amount');
        $totalOutstanding = (float) Sale::where('status', 'confirmed')->sum('due_amount');

        return view('admin.sales.index', compact(
            'sales', 'totalSalesAmount', 'totalReceived', 'totalOutstanding'
        ));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $shades = ProductShade::with('product')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();

        $nextInvoiceNumber = 'INV-' . date('Ym') . '-' . str_pad((Sale::count() + 1), 3, '0', STR_PAD_LEFT);

        return view('admin.sales.create', compact('customers', 'shades', 'nextInvoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_shade_id' => 'required|exists:product_shades,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,upi,bank_transfer,card,cheque,split',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $subtotal = 0.0;
            $itemsData = [];

            // 1. Validate stock and calculate server-side line totals
            foreach ($validated['items'] as $item) {
                $shade = ProductShade::where('id', $item['product_shade_id'])->lockForUpdate()->firstOrFail();
                $qty = (float) $item['quantity'];

                if ($shade->stock_quantity < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for SKU '{$shade->sku}'. Available: {$shade->stock_quantity} {$shade->unit}, Requested: {$qty} {$shade->unit}."
                    ]);
                }

                $price = (float) $item['unit_price'];
                $itemDiscount = (float) ($item['discount_amount'] ?? 0);
                $lineTotal = max(0, ($qty * $price) - $itemDiscount);

                $subtotal += $lineTotal;

                $itemsData[] = [
                    'shade' => $shade,
                    'qty' => $qty,
                    'price' => $price,
                    'cost' => (float) $shade->cost_price,
                    'discount' => $itemDiscount,
                    'total' => $lineTotal,
                ];
            }

            // 2. Invoice-level discount and tax calculations
            $discountType = $validated['discount_type'];
            $discountVal = (float) ($validated['discount_value'] ?? 0);
            $invoiceDiscount = $discountType === 'percent' ? ($subtotal * ($discountVal / 100)) : $discountVal;
            $invoiceDiscount = min($subtotal, $invoiceDiscount);

            $taxableValue = max(0, $subtotal - $invoiceDiscount);
            $taxRate = (float) $validated['tax_rate'];
            $taxAmount = round($taxableValue * ($taxRate / 100), 2);
            $finalTotal = round($taxableValue + $taxAmount, 2);

            $paid = (float) ($validated['paid_amount'] ?? 0);
            $paid = min($finalTotal, $paid);
            $due = max(0, $finalTotal - $paid);

            $paymentStatus = 'unpaid';
            if ($paid >= $finalTotal && $finalTotal > 0) {
                $paymentStatus = 'paid';
            } elseif ($paid > 0) {
                $paymentStatus = 'partially_paid';
            }

            $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad((Sale::count() + 1), 3, '0', STR_PAD_LEFT);

            // 3. Create Sale Record
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $validated['customer_id'],
                'date' => $validated['date'],
                'due_date' => $validated['due_date'] ?? $validated['date'],
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_rate' => $discountType === 'percent' ? $discountVal : 0,
                'discount_amount' => $invoiceDiscount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $finalTotal,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_status' => $paymentStatus,
                'status' => 'confirmed',
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // 4. Create Sale Items and Deduct Inventory Atomically
            foreach ($itemsData as $row) {
                $shade = $row['shade'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $shade->product_id,
                    'product_shade_id' => $shade->id,
                    'description' => "{$shade->product->name} — {$shade->shade_name}",
                    'sku' => $shade->sku,
                    'quantity' => $row['qty'],
                    'unit' => $shade->unit,
                    'unit_price' => $row['price'],
                    'cost_price_at_sale' => $row['cost'],
                    'discount_amount' => $row['discount'],
                    'tax_amount' => 0, // already aggregated on invoice level
                    'total' => $row['total'],
                ]);

                // Record stock movement
                $this->inventoryService->recordMovement(
                    shade: $shade,
                    type: 'sale',
                    quantity: $row['qty'],
                    direction: 'out',
                    unitCost: $row['cost'],
                    reason: "Sale invoice #{$sale->invoice_number}",
                    related: $sale,
                    user: auth()->user()
                );
            }

            // 5. If immediate payment was made, record payment receipt
            if ($paid > 0) {
                $receiptNumber = 'RCP-' . date('Ym') . '-' . str_pad((Payment::count() + 1), 3, '0', STR_PAD_LEFT);
                Payment::create([
                    'receipt_number' => $receiptNumber,
                    'customer_id' => $sale->customer_id,
                    'sale_id' => $sale->id,
                    'payment_date' => $validated['date'],
                    'amount' => $paid,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['payment_reference'] ?? null,
                    'notes' => "Payment on invoice #{$sale->invoice_number}",
                    'created_by' => auth()->id(),
                ]);
            }

            // 6. Post double-entry financial ledger
            $this->accountingService->postSale($sale);
            $sale->customer->recalculateBalance();

            return redirect()->route('admin.sales.show', $sale)
                ->with('success', "Invoice #{$sale->invoice_number} created and inventory deducted successfully.");
        });
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.shade.product', 'payments', 'creator']);
        $invoiceSettings = Setting::getInvoiceSettings();
        $storeSettings = Setting::all()->pluck('value', 'key');
        return view('admin.sales.show', compact('sale', 'invoiceSettings', 'storeSettings'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['customer', 'items.shade.product', 'payments', 'creator']);
        $invoiceSettings = Setting::getInvoiceSettings();
        $storeSettings = Setting::all()->pluck('value', 'key');
        return view('admin.sales.print', compact('sale', 'invoiceSettings', 'storeSettings'));
    }

    public function addPayment(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . (float) $sale->due_amount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,upi,bank_transfer,card,cheque',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $receiptNumber = 'RCP-' . date('Ym') . '-' . str_pad((Payment::count() + 1), 3, '0', STR_PAD_LEFT);

        $payment = Payment::create([
            'receipt_number' => $receiptNumber,
            'customer_id' => $sale->customer_id,
            'sale_id' => $sale->id,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? "Payment received for invoice #{$sale->invoice_number}",
            'created_by' => auth()->id(),
        ]);

        $this->accountingService->postPayment($payment);

        return redirect()->route('admin.sales.show', $sale)
            ->with('success', "Payment receipt #{$payment->receipt_number} for ₹" . number_format($payment->amount, 2) . " recorded.");
    }
}
