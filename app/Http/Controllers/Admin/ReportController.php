<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ProductShade;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $sales = Sale::with(['customer', 'items'])
            ->where('status', 'confirmed')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->latest('date')
            ->get();

        $totalRevenue = (float) $sales->sum('total_amount');
        $totalCollected = (float) $sales->sum('paid_amount');
        $totalDue = (float) $sales->sum('due_amount');

        // Best selling shades in period
        $topShades = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->where('status', 'confirmed')->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
        })
            ->selectRaw('sku, description, unit, SUM(quantity) as total_qty, SUM(total) as total_revenue')
            ->groupBy('sku', 'description', 'unit')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        return view('admin.reports.sales', compact(
            'sales', 'startDate', 'endDate', 'totalRevenue', 'totalCollected', 'totalDue', 'topShades'
        ));
    }

    public function inventory()
    {
        $shades = ProductShade::with(['product.category'])
            ->orderBy('stock_quantity', 'asc')
            ->get();

        $totalValuation = (float) $shades->sum(function ($s) {
            return $s->stock_quantity * $s->cost_price;
        });

        $totalRetailValuation = (float) $shades->sum(function ($s) {
            return $s->stock_quantity * $s->selling_price;
        });

        $lowStockCount = $shades->filter->isLowStock()->count();
        $outOfStockCount = $shades->filter->isOutOfStock()->count();

        return view('admin.reports.inventory', compact(
            'shades', 'totalValuation', 'totalRetailValuation', 'lowStockCount', 'outOfStockCount'
        ));
    }

    public function dues()
    {
        $customersWithDues = Customer::where('current_balance', '>', 0)
            ->with(['sales' => function ($q) {
                $q->where('due_amount', '>', 0)->latest('date');
            }])
            ->orderBy('current_balance', 'desc')
            ->get();

        $suppliersWithPayables = Supplier::where('current_balance', '>', 0)
            ->with(['purchases' => function ($q) {
                $q->where('due_amount', '>', 0)->latest('purchase_date');
            }])
            ->orderBy('current_balance', 'desc')
            ->get();

        $totalReceivables = (float) $customersWithDues->sum('current_balance');
        $totalPayables = (float) $suppliersWithPayables->sum('current_balance');

        return view('admin.reports.dues', compact(
            'customersWithDues', 'suppliersWithPayables', 'totalReceivables', 'totalPayables'
        ));
    }

    public function exportSales(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $sales = Sale::with('customer')
            ->where('status', 'confirmed')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'asc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="flooros_sales_' . date('Ymd') . '.csv"',
        ];

        return response()->stream(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice #', 'Date', 'Customer', 'Subtotal', 'Discount', 'Tax', 'Total Amount', 'Paid', 'Due', 'Payment Status']);

            foreach ($sales as $s) {
                fputcsv($handle, [
                    $s->invoice_number,
                    $s->date->format('Y-m-d'),
                    $s->customer->name,
                    $s->subtotal,
                    $s->discount_amount,
                    $s->tax_amount,
                    $s->total_amount,
                    $s->paid_amount,
                    $s->due_amount,
                    ucfirst(str_replace('_', ' ', $s->payment_status)),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function exportInventory(): StreamedResponse
    {
        $shades = ProductShade::with('product')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="flooros_inventory_' . date('Ymd') . '.csv"',
        ];

        return response()->stream(function () use ($shades) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['SKU', 'Product Name', 'Shade Name', 'Stock Qty', 'Unit', 'Cost Price', 'Selling Price', 'Inventory Valuation', 'Reorder Threshold', 'Status']);

            foreach ($shades as $s) {
                fputcsv($handle, [
                    $s->sku,
                    $s->product->name,
                    $s->shade_name,
                    $s->stock_quantity,
                    $s->unit,
                    $s->cost_price,
                    $s->selling_price,
                    round($s->stock_quantity * $s->cost_price, 2),
                    $s->reorder_threshold,
                    $s->stock_status,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
