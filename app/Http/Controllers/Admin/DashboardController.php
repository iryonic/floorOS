<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\ProductShade;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Core KPIs
        $totalProducts = Product::count();
        $totalShades = ProductShade::where('is_active', true)->count();

        // Current Inventory Value at Cost Price
        $inventoryValue = (float) ProductShade::where('stock_quantity', '>', 0)
            ->selectRaw('SUM(stock_quantity * cost_price) as total_val')
            ->value('total_val') ?? 0.00;

        $lowStockCount = ProductShade::whereColumn('stock_quantity', '<=', 'reorder_threshold')
            ->count();

        // 2. Low-Stock Alert List
        $lowStockShades = ProductShade::whereHas('product')
            ->with('product')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_threshold')
            ->orderBy('stock_quantity', 'asc')
            ->take(6)
            ->get();

        // 3. Recent Enquiries
        $recentEnquiries = Enquiry::with('product')
            ->latest()
            ->take(6)
            ->get();

        // 4. Live Accounting & Cash Flow Metrics (This Month)
        $monthCredits = (float) FinancialTransaction::where('type', 'credit')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $monthDebits = (float) FinancialTransaction::where('type', 'debit')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $netCashFlow = $monthCredits - $monthDebits;

        // 5. Cash Flow Trend Chart Data (default last 30 days)
        $chartPeriod = $request->input('period', '30'); // 7, 30, 90
        $days = (int) $chartPeriod;
        $startDate = now()->subDays($days - 1)->startOfDay();

        $creditsTrend = FinancialTransaction::where('type', 'credit')
            ->where('transaction_date', '>=', $startDate->toDateString())
            ->groupBy('transaction_date')
            ->orderBy('transaction_date', 'asc')
            ->selectRaw('DATE(transaction_date) as t_date, SUM(amount) as total')
            ->get()
            ->keyBy('t_date');

        $debitsTrend = FinancialTransaction::where('type', 'debit')
            ->where('transaction_date', '>=', $startDate->toDateString())
            ->groupBy('transaction_date')
            ->orderBy('transaction_date', 'asc')
            ->selectRaw('DATE(transaction_date) as t_date, SUM(amount) as total')
            ->get()
            ->keyBy('t_date');

        $chartLabels = [];
        $chartCredits = [];
        $chartDebits = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('d M');
            $chartLabels[] = $label;
            $chartCredits[] = isset($creditsTrend[$d]) ? (float) $creditsTrend[$d]->total : 0.0;
            $chartDebits[] = isset($debitsTrend[$d]) ? (float) $debitsTrend[$d]->total : 0.0;
        }

        $recentFinancialTransactions = FinancialTransaction::with('creator')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalShades',
            'inventoryValue',
            'lowStockCount',
            'lowStockShades',
            'recentEnquiries',
            'monthCredits',
            'monthDebits',
            'netCashFlow',
            'chartLabels',
            'chartCredits',
            'chartDebits',
            'chartPeriod',
            'recentFinancialTransactions'
        ));
    }
}
