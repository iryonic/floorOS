<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductShade;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = ProductShade::with(['product.category']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('shade_name', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($status = $request->input('stock_status')) {
            if ($status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($status === 'low_stock') {
                $query->whereColumn('stock_quantity', '<=', 'reorder_threshold')->where('stock_quantity', '>', 0);
            } elseif ($status === 'in_stock') {
                $query->whereColumn('stock_quantity', '>', 'reorder_threshold');
            }
        }

        $shades = $query->orderBy('stock_quantity', 'asc')->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        $totalValuation = (float) ProductShade::where('stock_quantity', '>', 0)
            ->selectRaw('SUM(stock_quantity * cost_price) as total')
            ->value('total') ?? 0;

        $totalUnits = (float) ProductShade::sum('stock_quantity');
        $lowStockCount = ProductShade::whereColumn('stock_quantity', '<=', 'reorder_threshold')->count();

        return view('admin.inventory.index', compact(
            'shades', 'categories', 'totalValuation', 'totalUnits', 'lowStockCount'
        ));
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['shade.product', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('occurred_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('occurred_at', '<=', $dateTo);
        }

        $movements = $query->latest('occurred_at')->paginate(20)->withQueryString();

        return view('admin.inventory.movements', compact('movements'));
    }

    public function createAdjustment()
    {
        $shades = ProductShade::with('product')->where('is_active', true)->get();
        return view('admin.inventory.adjust', compact('shades'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_shade_id' => 'required|exists:product_shades,id',
            'type' => 'required|in:adjustment,damage,audit,opening',
            'direction' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $shade = ProductShade::findOrFail($validated['product_shade_id']);

        $movement = $this->inventoryService->recordMovement(
            shade: $shade,
            type: $validated['type'],
            quantity: (float) $validated['quantity'],
            direction: $validated['direction'],
            reason: $validated['reason'],
            notes: $validated['notes'] ?? null,
            user: auth()->user()
        );

        return redirect()->route('admin.inventory.index')
            ->with('success', "Stock movement #{$movement->reference_number} recorded. New balance for SKU '{$shade->sku}': {$shade->fresh()->stock_quantity} {$shade->unit}.");
    }
}
