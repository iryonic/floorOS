@extends('layouts.admin')

@section('title', 'Inventory & Stock Balances')
@section('page_title', 'Inventory Balances')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">Stock</h1>
            <p class="text-xs text-[#6B756F]">Per-SKU stock balances, weighted cost valuations, and reorder alerts.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.exportInventory') }}" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="download" class="w-4 h-4"></i> Export CSV
            </a>
            <a href="{{ route('admin.inventory.movements') }}" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="history" class="w-4 h-4"></i> Movement History
            </a>
            <a href="{{ route('admin.inventory.adjust') }}" class="btn-brand text-xs py-2 px-4 shadow-xs">
                <i data-lucide="sliders" class="w-4 h-4"></i> Stock Adjustment
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card-surface p-4">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#6B756F] block">Total Stock Valuation</span>
            <div class="text-2xl font-extrabold text-brand mt-1">₹{{ number_format($totalValuation, 2) }}</div>
            <span class="text-[10px] text-[#6B756F]">Calculated at weighted cost basis</span>
        </div>
        <div class="card-surface p-4">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#6B756F] block">Total Physical Units</span>
            <div class="text-2xl font-extrabold text-[#202925] mt-1">{{ number_format($totalUnits, 1) }}</div>
            <span class="text-[10px] text-[#6B756F]">Combined sq ft across all collections</span>
        </div>
        <div class="card-surface p-4">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#6B756F] block">Low Stock Alerts</span>
            <div class="text-2xl font-extrabold {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-[#202925]' }} mt-1">{{ $lowStockCount }} SKUs</div>
            <span class="text-[10px] text-[#6B756F]">Variants requiring replenishment</span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card-surface p-4">
        <form action="{{ route('admin.inventory.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <i data-lucide="search" class="w-4 h-4 text-neutral-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by SKU, product name, or shade..." class="w-full pl-9 pr-3 py-2 text-xs input-custom">
            </div>

            <div>
                <select name="category_id" onchange="this.form.submit()" class="w-full py-2 text-xs input-custom">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="stock_status" onchange="this.form.submit()" class="w-full py-2 text-xs input-custom">
                    <option value="">All Stock Statuses</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock Alerts</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="card-surface overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                    <tr>
                        <th class="p-3 pl-5">SKU & Code</th>
                        <th class="p-3">Product Collection</th>
                        <th class="p-3">Shade Name</th>
                        <th class="p-3 text-right">Available Stock</th>
                        <th class="p-3 text-right">Cost Price</th>
                        <th class="p-3 text-right">Selling Price</th>
                        <th class="p-3 text-right">Valuation (Cost)</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($shades as $shade)
                        <tr class="hover:bg-brand-light/30 transition">
                            <td class="p-3 pl-5 font-mono font-bold text-[#202925]">
                                <div>{{ $shade->sku }}</div>
                                <span class="text-[10px] text-neutral-500 font-sans">{{ $shade->shade_code ?: '—' }}</span>
                            </td>
                            <td class="p-3 font-medium text-[#202925]">
                                <a href="{{ route('admin.products.edit', $shade->product) }}" class="hover:text-brand">
                                    {{ $shade->product->name }}
                                </a>
                                <span class="block text-[10px] text-[#6B756F]">{{ $shade->product->category->name }}</span>
                            </td>
                            <td class="p-3 font-semibold text-[#202925]">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded overflow-hidden border border-neutral-200">
                                        <img src="{{ $shade->effective_image }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <span>{{ $shade->shade_name }}</span>
                                </div>
                            </td>
                            <td class="p-3 text-right font-bold {{ $shade->stock_quantity <= 0 ? 'text-rose-600' : ($shade->stock_quantity <= $shade->reorder_threshold ? 'text-amber-600' : 'text-[#202925]') }}">
                                {{ number_format($shade->stock_quantity, 1) }} {{ $shade->unit }}
                                <span class="block text-[10px] font-normal text-neutral-400">Reorder at {{ number_format($shade->reorder_threshold, 0) }}</span>
                            </td>
                            <td class="p-3 text-right text-[#6B756F]">
                                ₹{{ number_format($shade->cost_price, 2) }}
                            </td>
                            <td class="p-3 text-right font-bold text-brand">
                                ₹{{ number_format($shade->selling_price, 2) }}
                            </td>
                            <td class="p-3 text-right font-bold text-[#202925]">
                                ₹{{ number_format($shade->stock_quantity * $shade->cost_price, 2) }}
                            </td>
                            <td class="p-3 text-center">
                                @if($shade->stock_quantity <= 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-rose-700 border border-rose-300">Out of Stock</span>
                                @elseif($shade->stock_quantity <= $shade->reorder_threshold)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Low Stock</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">In Stock</span>
                                @endif
                            </td>
                            <td class="p-3 pr-5 text-right">
                                <a href="{{ route('admin.inventory.adjust') }}?shade_id={{ $shade->id }}" class="px-2.5 py-1 text-[11px] rounded bg-white border border-slate-200 hover:bg-brand-light hover:border-brand text-brand font-semibold transition">
                                    Adjust
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#E3E8E4]">
            {{ $shades->links() }}
        </div>
    </div>
</div>
@endsection
