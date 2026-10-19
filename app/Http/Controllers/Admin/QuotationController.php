<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ProductShade;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with(['customer', 'creator']);

        if ($search = $request->input('search')) {
            $query->where('quotation_number', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%");
                });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $quotations = $query->latest('date')->paginate(15)->withQueryString();

        return view('admin.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $shades = ProductShade::with('product')->where('is_active', true)->get();
        $nextNumber = 'EST-' . date('Ym') . '-' . str_pad((Quotation::count() + 1), 3, '0', STR_PAD_LEFT);

        return view('admin.quotations.create', compact('customers', 'shades', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'valid_until' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_shade_id' => 'required|exists:product_shades,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0.0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $shade = ProductShade::findOrFail($item['product_shade_id']);
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $lineTotal = $qty * $price;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'shade' => $shade,
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $lineTotal,
                ];
            }

            $discountType = $validated['discount_type'];
            $discountVal = (float) ($validated['discount_value'] ?? 0);
            $discountAmount = $discountType === 'percent' ? ($subtotal * ($discountVal / 100)) : $discountVal;
            $discountAmount = min($subtotal, $discountAmount);

            $taxable = max(0, $subtotal - $discountAmount);
            $taxRate = (float) $validated['tax_rate'];
            $taxAmount = round($taxable * ($taxRate / 100), 2);
            $finalTotal = round($taxable + $taxAmount, 2);

            $number = 'EST-' . date('Ym') . '-' . str_pad((Quotation::count() + 1), 3, '0', STR_PAD_LEFT);

            $quote = Quotation::create([
                'quotation_number' => $number,
                'customer_id' => $validated['customer_id'],
                'date' => $validated['date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_rate' => $discountType === 'percent' ? $discountVal : 0,
                'discount_amount' => $discountAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $finalTotal,
                'status' => 'sent',
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? 'Prices valid for 15 days. Delivery charges extra.',
                'created_by' => auth()->id(),
            ]);

            foreach ($itemsData as $row) {
                $shade = $row['shade'];
                QuotationItem::create([
                    'quotation_id' => $quote->id,
                    'product_id' => $shade->product_id,
                    'product_shade_id' => $shade->id,
                    'description' => "{$shade->product->name} — {$shade->shade_name}",
                    'sku' => $shade->sku,
                    'quantity' => $row['qty'],
                    'unit' => $shade->unit,
                    'unit_price' => $row['price'],
                    'total' => $row['total'],
                ]);
            }

            return redirect()->route('admin.quotations.show', $quote)
                ->with('success', "Quotation #{$quote->quotation_number} generated successfully.");
        });
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.shade.product', 'creator', 'sale']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function convertToSale(Quotation $quotation, InventoryService $inventoryService, AccountingService $accountingService)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'This quotation has already been converted to an invoice.');
        }

        return DB::transaction(function () use ($quotation, $inventoryService, $accountingService) {
            $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad((Sale::count() + 1), 3, '0', STR_PAD_LEFT);

            // 1. Create Sale from Quotation
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $quotation->customer_id,
                'date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'subtotal' => $quotation->subtotal,
                'discount_type' => $quotation->discount_type,
                'discount_rate' => $quotation->discount_rate,
                'discount_amount' => $quotation->discount_amount,
                'tax_rate' => $quotation->tax_rate,
                'tax_amount' => $quotation->tax_amount,
                'total_amount' => $quotation->total_amount,
                'paid_amount' => 0.00,
                'due_amount' => $quotation->total_amount,
                'payment_status' => 'unpaid',
                'status' => 'confirmed',
                'payment_method' => 'bank_transfer',
                'quotation_id' => $quotation->id,
                'notes' => "Converted from quotation #{$quotation->quotation_number}",
                'created_by' => auth()->id(),
            ]);

            // 2. Create Sale Items & Deduct Stock Atomically
            foreach ($quotation->items as $item) {
                $shade = ProductShade::where('id', $item->product_shade_id)->lockForUpdate()->firstOrFail();

                if ($shade->stock_quantity < $item->quantity) {
                    throw ValidationException::withMessages([
                        'error' => "Cannot convert to sale: Insufficient stock for SKU '{$shade->sku}'. Available: {$shade->stock_quantity} {$shade->unit}, Requested: {$item->quantity} {$shade->unit}."
                    ]);
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'product_shade_id' => $shade->id,
                    'description' => $item->description,
                    'sku' => $shade->sku,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'cost_price_at_sale' => (float) $shade->cost_price,
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'total' => $item->total,
                ]);

                $inventoryService->recordMovement(
                    shade: $shade,
                    type: 'sale',
                    quantity: (float) $item->quantity,
                    direction: 'out',
                    unitCost: (float) $shade->cost_price,
                    reason: "Converted from quotation #{$quotation->quotation_number}",
                    related: $sale,
                    user: auth()->user()
                );
            }

            // 3. Mark Quotation converted
            $quotation->update(['status' => 'converted']);

            // 4. Ledger & Balance
            $accountingService->postSale($sale);
            $sale->customer->recalculateBalance();

            return redirect()->route('admin.sales.show', $sale)
                ->with('success', "Quotation #{$quotation->quotation_number} converted to Sale #{$sale->invoice_number} successfully.");
        });
    }
}
