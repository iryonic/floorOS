@extends('layouts.admin')

@section('title', 'Products & Shades Management')
@section('page_title', 'Products & Collections')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">Products</h1>
            <p class="text-xs text-[#6B756F] mt-0.5">Manage collections, specifications, individual shade variants, and sellable SKUs.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <!-- View Layout Switcher (Default: Grid) -->
            <div class="flex items-center bg-white border border-slate-200 rounded-lg p-1 shadow-2xs">
                <button type="button" 
                        id="viewGridBtn"
                        onclick="setProductsView('grid')" 
                        class="px-2.5 py-1.5 rounded text-xs font-semibold flex items-center gap-1.5 transition bg-brand text-white shadow-xs" 
                        title="Grid View (Default)">
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                    <span>Grid</span>
                </button>
                <button type="button" 
                        id="viewTableBtn"
                        onclick="setProductsView('table')" 
                        class="px-2.5 py-1.5 rounded text-xs font-semibold flex items-center gap-1.5 transition text-slate-600 hover:text-slate-900" 
                        title="Table View">
                    <i data-lucide="list" class="w-3.5 h-3.5"></i>
                    <span>Table</span>
                </button>
            </div>

            <a href="{{ route('admin.categories.index') }}" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="grid" class="w-4 h-4"></i> Categories
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn-brand text-xs py-2 px-4 shadow-xs">
                <i data-lucide="plus" class="w-4 h-4"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="card-surface p-4">
        <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <i data-lucide="search" class="w-4 h-4 text-neutral-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name, brand, collection, or shade SKU..." class="w-full pl-9 pr-3 py-2 text-xs input-custom">
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
                <select name="status" onchange="this.form.submit()" class="w-full py-2 text-xs input-custom">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
        </form>
    </div>

    @if($products->isEmpty())
        <div class="card-surface p-12 text-center text-xs text-[#6B756F]">
            <i data-lucide="layers" class="w-10 h-10 text-neutral-300 mx-auto mb-3"></i>
            <p class="text-sm font-semibold text-slate-800 mb-1">No products found</p>
            <p class="text-xs text-slate-500 mb-4">No flooring products matched your current search filters.</p>
            <a href="{{ route('admin.products.create') }}" class="btn-brand text-xs py-2 px-4 inline-flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Create First Product
            </a>
        </div>
    @else
        <!-- ========================================== -->
        <!-- 1. GRID VIEW (DEFAULT)                     -->
        <!-- ========================================== -->
        <div id="productsGridView" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="card-surface flex flex-col justify-between overflow-hidden hover:border-brand/40 hover:shadow-md transition group">
                    <div>
                        <!-- Product Card Top Image & Badges -->
                        <div class="relative w-full h-49 bg-slate-100 overflow-hidden border-b border-slate-100">
                            <img src="{{ $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <!-- Top Badges -->
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-white/95 text-brand shadow-xs border border-brand/20 backdrop-blur-xs">
                                    {{ $product->category->name }}
                                </span>
                                @if($product->is_featured)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-400 text-slate-900 shadow-xs flex items-center gap-1">
                                        <i data-lucide="star" class="w-3 h-3 fill-slate-900"></i> Featured
                                    </span>
                                @endif
                            </div>

                            <div class="absolute top-3 right-3">
                                @if($product->status === 'published')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 text-emerald-700 border border-emerald-200 shadow-xs">Published</span>
                                @elseif($product->status === 'draft')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 text-slate-600 border border-slate-200 shadow-xs">Draft</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 text-rose-700 border border-rose-200 shadow-xs">Archived</span>
                                @endif
                            </div>

                            <!-- Bottom Overlay Stock Summary -->
                            <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between px-2.5 py-1 rounded bg-black/60 backdrop-blur-xs text-white text-[11px]">
                                <span class="font-medium text-slate-200">Stock: <strong class="text-white">{{ number_format($product->total_stock, 1) }}</strong> {{ $product->unit_of_measure }}</span>
                                <span class="text-accent font-bold">
                                    @if($product->min_price == $product->max_price)
                                        ₹{{ number_format($product->min_price, 0) }}/{{ $product->unit_of_measure }}
                                    @else
                                        ₹{{ number_format($product->min_price, 0) }} - ₹{{ number_format($product->max_price, 0) }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 space-y-3">
                            <div>
                                <a href="{{ route('admin.products.edit', $product) }}" class="font-bold text-sm text-[#202925] hover:text-brand line-clamp-1 transition" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </a>
                                <div class="flex items-center gap-2 text-[11px] text-[#6B756F] mt-0.5">
                                    @if($product->brand)
                                        <span class="font-semibold text-slate-700">{{ $product->brand }}</span>
                                        <span>•</span>
                                    @endif
                                    <span>{{ $product->material_type ?: 'Hardwood Flooring' }}</span>
                                    @if($product->thickness)
                                        <span>•</span>
                                        <span>{{ $product->thickness }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Shades Strip -->
                            <div class="pt-2 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                        <i data-lucide="palette" class="w-3 h-3 text-brand"></i>
                                        Shades ({{ $product->shades->count() }})
                                    </span>
                                    <button type="button" 
                                            onclick="openAddShadeModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->unit_of_measure }}', '{{ $product->primary_image ?: '' }}')"
                                            class="text-[11px] font-bold text-brand hover:underline inline-flex items-center gap-1">
                                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Add Shade
                                    </button>
                                </div>

                                <!-- Shades Thumbnails List -->
                                <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                                    @forelse($product->shades as $shade)
                                        <div class="flex items-center justify-between gap-2 p-1.5 rounded-lg bg-slate-50/70 hover:bg-slate-100 border border-slate-100 transition text-[11px]">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="w-6 h-6 rounded-md overflow-hidden bg-white shrink-0 border border-slate-200">
                                                    <img src="{{ $shade->image ?: $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                                                         alt="{{ $shade->shade_name }}" 
                                                         class="w-full h-full object-cover">
                                                </div>
                                                <div class="truncate">
                                                    <span class="font-semibold text-slate-800 block truncate">{{ $shade->shade_name }}</span>
                                                    <span class="font-mono text-[9px] text-slate-400 block">{{ $shade->sku }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="font-bold text-brand block">₹{{ number_format($shade->selling_price, 2) }}</span>
                                                <span class="text-[9px] font-semibold {{ $shade->stock_quantity <= $shade->reorder_threshold ? 'text-amber-600' : 'text-slate-500' }}">
                                                    {{ number_format($shade->stock_quantity, 0) }} {{ $product->unit_of_measure }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-[11px] text-slate-400 text-center py-2 bg-slate-50 rounded-lg">
                                            No shades created yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="p-3 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between gap-2">
                        <button type="button" 
                                onclick="openAddShadeModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->unit_of_measure }}', '{{ $product->primary_image ?: '' }}')"
                                class="btn-brand text-[11px] py-1.5 px-3 shadow-2xs flex items-center gap-1">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Shade
                        </button>

                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('products.show', $product) }}" target="_blank" title="View Public Catalog Page" class="p-1.5 rounded-md text-slate-500 hover:text-brand hover:bg-white transition border border-transparent hover:border-slate-200">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" title="Edit Product Details & Shades" class="p-1.5 rounded-md text-slate-500 hover:text-brand hover:bg-white transition border border-transparent hover:border-slate-200">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" data-confirm="Archive this product? Historical sales and inventory movements will remain preserved." data-confirm-title="Archive Product" data-confirm-destructive="true">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Archive Product" class="p-1.5 rounded-md text-slate-500 hover:text-rose-600 hover:bg-white transition border border-transparent hover:border-slate-200">
                                    <i data-lucide="archive" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ========================================== -->
        <!-- 2. TABLE VIEW (ALTERNATIVE)                -->
        <!-- ========================================== -->
        <div id="productsTableView" class="hidden card-surface overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-xs text-left whitespace-nowrap">
                    <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="p-3.5 pl-5">Product Collection</th>
                            <th class="p-3.5">Category & Brand</th>
                            <th class="p-3.5 text-center">Shades</th>
                            <th class="p-3.5 text-right">Available Stock</th>
                            <th class="p-3.5 text-right">Price Range</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 pr-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($products as $product)
                            <tr class="hover:bg-brand-light/30 transition">
                                <td class="p-3.5 pl-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-10 rounded-md bg-white overflow-hidden shrink-0 border border-slate-200">
                                            <img src="{{ $product->primary_image ?: '/images/flooring/category-wood.svg' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="overflow-hidden">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="font-bold text-[#202925] hover:text-brand truncate block">
                                                {{ $product->name }}
                                            </a>
                                            <span class="text-[11px] text-[#6B756F] block truncate">
                                                {{ $product->material_type }} {{ $product->thickness ? '• ' . $product->thickness : '' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-brand-light text-brand">
                                        {{ $product->category->name }}
                                    </span>
                                    <span class="block text-[11px] text-[#6B756F] mt-1">{{ $product->brand ?: '—' }}</span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-[#202925]">
                                        <i data-lucide="palette" class="w-3.5 h-3.5 text-brand"></i>
                                        {{ $product->shades->count() }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-right">
                                    <span class="font-bold text-[#202925]">
                                        {{ number_format($product->total_stock, 1) }}
                                    </span>
                                    <span class="text-[10px] text-[#6B756F] block">{{ $product->unit_of_measure }}</span>
                                </td>
                                <td class="p-3.5 text-right font-bold text-brand">
                                    @if($product->min_price == $product->max_price)
                                        ₹{{ number_format($product->min_price, 2) }}
                                    @else
                                        ₹{{ number_format($product->min_price, 0) }} - ₹{{ number_format($product->max_price, 0) }}
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($product->status === 'published')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Published</span>
                                    @elseif($product->status === 'draft')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-300">Draft</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Archived</span>
                                    @endif
                                </td>
                                <td class="p-3.5 pr-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" 
                                                onclick="openAddShadeModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->unit_of_measure }}', '{{ $product->primary_image ?: '' }}')"
                                                class="px-2 py-1 rounded text-[11px] font-semibold bg-brand-light text-brand hover:bg-brand hover:text-white transition flex items-center gap-1">
                                            <i data-lucide="plus" class="w-3 h-3"></i> Shade
                                        </button>
                                        <a href="{{ route('products.show', $product) }}" target="_blank" title="View Public Page" class="p-1 text-[#6B756F] hover:text-brand">
                                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" title="Edit Product" class="p-1 text-[#6B756F] hover:text-brand">
                                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" data-confirm="Archive this product? Historical sales and inventory movements will remain preserved." data-confirm-title="Archive Product" data-confirm-destructive="true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Archive" class="p-1 text-[#6B756F] hover:text-rose-600">
                                                <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-4 card-surface flex justify-center">
            {{ $products->links() }}
        </div>
    @endif
</div>

<!-- ======================================================== -->
<!-- MODAL: ADD NEW SHADE DIRECTLY TO A PRODUCT                -->
<!-- ======================================================== -->
<div id="addShadeModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand-light text-brand flex items-center justify-center">
                    <i data-lucide="palette" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Add New Shade Variant</h3>
                    <p id="modalProductName" class="text-xs text-brand font-semibold truncate max-w-sm"></p>
                </div>
            </div>
            <button type="button" onclick="closeAddShadeModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body / Form -->
        <form id="addShadeForm" action="" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-5 space-y-4">
            @csrf

            <!-- Dedicated Shade Image Upload -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-1.5">Dedicated Shade Photo *</label>
                <div class="flex items-center gap-4 p-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-brand/40 bg-slate-50/50 transition">
                    <div class="w-16 h-16 rounded-lg bg-white border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center shadow-2xs">
                        <img id="modalShadePreview" src="/images/flooring/category-wood.svg" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <input type="file" 
                               name="image" 
                               id="modalShadeImageInput" 
                               accept="image/*" 
                               onchange="previewModalShadeImage(this)"
                               class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-brand-light file:text-brand hover:file:bg-brand hover:file:text-white transition cursor-pointer">
                        <span class="text-[10px] text-slate-400 block mt-1">High-resolution swatch or board photo for this specific shade color.</span>
                    </div>
                </div>
            </div>

            <!-- Shade Name & SKU -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1">Shade Name *</label>
                    <input type="text" 
                           name="shade_name" 
                           id="modalShadeName" 
                           required 
                           oninput="autoGenerateSku(this.value)"
                           placeholder="e.g. Smoked Champagne Oak" 
                           class="w-full input-custom text-xs">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1">Unique SKU Code *</label>
                    <input type="text" 
                           name="sku" 
                           id="modalShadeSku" 
                           required 
                           oninput="this.dataset.manuallyEdited='true'"
                           placeholder="e.g. OAK-SMK-01" 
                           class="w-full input-custom text-xs font-mono uppercase">
                </div>
            </div>

            <!-- Shade Code, Color Family, Finish -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1">Shade / Color Code</label>
                    <input type="text" name="shade_code" placeholder="e.g. SH-402" class="w-full input-custom text-xs font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1">Color Family</label>
                    <input type="text" name="color_family" placeholder="e.g. Warm Honey" class="w-full input-custom text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1">Surface Finish</label>
                    <input type="text" name="finish" placeholder="e.g. Wire-brushed Matte" class="w-full input-custom text-xs">
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/80 space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 block">Pricing & Opening Stock</span>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-800 mb-1">Selling Price (₹)*</label>
                        <input type="number" step="0.01" name="selling_price" value="480.00" required class="w-full input-custom text-xs text-right font-bold text-brand">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-800 mb-1">Cost Price (₹)*</label>
                        <input type="number" step="0.01" name="cost_price" value="310.00" required class="w-full input-custom text-xs text-right">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-800 mb-1">Opening Stock *</label>
                        <input type="number" step="0.01" name="stock_quantity" value="250.00" required class="w-full input-custom text-xs text-right font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-800 mb-1">Reorder Alert *</label>
                        <input type="number" step="0.01" name="reorder_threshold" value="50.00" required class="w-full input-custom text-xs text-right">
                    </div>
                </div>
            </div>

            <!-- Modal Footer Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeAddShadeModal()" class="btn-secondary text-xs py-2 px-4">
                    Cancel
                </button>
                <button type="submit" class="btn-brand text-xs py-2 px-6 shadow-xs flex items-center gap-1.5 font-bold">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Save & Add Shade
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // View state switcher: Defaults to 'grid'
    function setProductsView(view) {
        const gridView = document.getElementById('productsGridView');
        const tableView = document.getElementById('productsTableView');
        const gridBtn = document.getElementById('viewGridBtn');
        const tableBtn = document.getElementById('viewTableBtn');

        if (!gridView || !tableView) return;

        if (view === 'table') {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');

            tableBtn.classList.add('bg-brand', 'text-white', 'shadow-xs');
            tableBtn.classList.remove('text-slate-600');
            gridBtn.classList.remove('bg-brand', 'text-white', 'shadow-xs');
            gridBtn.classList.add('text-slate-600');

            localStorage.setItem('flooros_admin_products_view', 'table');
        } else {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');

            gridBtn.classList.add('bg-brand', 'text-white', 'shadow-xs');
            gridBtn.classList.remove('text-slate-600');
            tableBtn.classList.remove('bg-brand', 'text-white', 'shadow-xs');
            tableBtn.classList.add('text-slate-600');

            localStorage.setItem('flooros_admin_products_view', 'grid');
        }
    }

    // Modal helpers
    let currentModalProductId = null;

    function openAddShadeModal(productId, productName, unit, fallbackImage) {
        currentModalProductId = productId;
        const modal = document.getElementById('addShadeModal');
        const form = document.getElementById('addShadeForm');
        const nameEl = document.getElementById('modalProductName');
        const preview = document.getElementById('modalShadePreview');

        nameEl.textContent = 'For Collection: ' + productName + ' (' + unit + ')';
        form.action = `/admin/products/${productId}/shades`;
        preview.src = fallbackImage || '/images/flooring/category-wood.svg';

        // Reset inputs
        document.getElementById('modalShadeName').value = '';
        const skuInput = document.getElementById('modalShadeSku');
        skuInput.value = '';
        skuInput.dataset.manuallyEdited = 'false';
        document.getElementById('modalShadeImageInput').value = '';

        modal.classList.remove('hidden');
        document.getElementById('modalShadeName').focus();
    }

    function closeAddShadeModal() {
        document.getElementById('addShadeModal').classList.add('hidden');
    }

    function previewModalShadeImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('modalShadePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function autoGenerateSku(val) {
        const skuInput = document.getElementById('modalShadeSku');
        if (skuInput && skuInput.dataset.manuallyEdited !== 'true' && val) {
            const clean = val.trim().toUpperCase().replace(/[^A-Z0-9]/g, '-').replace(/-+/g, '-').slice(0, 10);
            const rand = Math.floor(1000 + Math.random() * 9000);
            skuInput.value = (clean ? 'SKU-' + clean + '-' : 'SKU-') + rand;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Load persisted view or default to 'grid'
        const savedView = localStorage.getItem('flooros_admin_products_view') || 'grid';
        setProductsView(savedView);

        // Escape key closes modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAddShadeModal();
        });
    });
</script>
@endpush
@endsection
