@extends('layouts.public')

@section('title', 'Flooring Catalog — FloorOS')
@section('meta_description', 'Explore our wide range of premium hardwood, SPC, laminate, and architectural flooring options for your space.')

@section('content')
<div class="py-4 sm:py-8 bg-white min-h-screen">
    <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-5 sm:space-y-6">

        <!-- 1. Catalog Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
               
                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                  Our Catalog
                </h1>
                
            </div>

           
        </div>

        <!-- 2. Search Bar Capsule -->
        <form action="{{ route('catalog') }}" method="GET" class="relative">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products, shades, textures, or collections..." class="w-full pl-11 pr-10 py-3 bg-white border border-slate-200 rounded-full text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-brand focus:border-brand outline-none shadow-2xs transition">
            
            @if(request('search'))
                <a href="{{ route('catalog', request()->except('search')) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>

        <!-- 3. Horizontal Category Chips -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
            <a href="{{ route('catalog', array_merge(request()->except('category'), ['category' => null])) }}" class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition {{ !request('category') ? 'bg-brand text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:border-brand' }}">
                All 
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('catalog', array_merge(request()->except('category'), ['category' => $cat->slug])) }}" class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition {{ request('category') === $cat->slug ? 'bg-brand text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:border-brand' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        <!-- 4. Filter & Sort Bar -->
        <div class="flex items-center justify-between gap-3 pt-1">
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Filter Drawer Button -->
                <button type="button" onclick="document.getElementById('catalogFilterDrawer').classList.toggle('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 hover:border-brand shadow-2xs transition">
                    <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-brand"></i>
                    <span>Filters</span>
                    @if(request()->anyFilled(['material', 'finish', 'min_price', 'max_price']))
                        <span class="w-2 h-2 rounded-full bg-brand"></span>
                    @endif
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                </button>

                <!-- Sort Dropdown -->
                <div class="relative">
                    <form id="sortForm" action="{{ route('catalog') }}" method="GET">
                        @foreach(request()->except('sort') as $k => $v)
                            @if(is_array($v))
                                @foreach($v as $subV)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $subV }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <select name="sort" onchange="document.getElementById('sortForm').submit()" class="appearance-none pl-7  py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 hover:border-brand shadow-2xs focus:outline-none cursor-pointer">
                            <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Popularity</option>
                            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        </select>
                        <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </form>
                </div>

                @if(request()->anyFilled(['search', 'category', 'material', 'finish', 'min_price', 'max_price']))
                    <a href="{{ route('catalog') }}" class="py-2 px-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 hover:text-slate-900 transition text-xs flex items-center gap-1.5 shadow-2xs" title="Reset all filters">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-brand"></i>
                        <span>Reset Filters</span>
                    </a>
                @endif
            </div>

            <!-- Layout View Switcher (Default: Grid) -->
            <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-2xs shrink-0">
                <button type="button" 
                        id="viewToggleGrid"
                        onclick="setCatalogLayout('grid')" 
                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition bg-brand text-white shadow-xs" 
                        title="Grid View (Default)">
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Grid</span>
                </button>
                <button type="button" 
                        id="viewToggleTable"
                        onclick="setCatalogLayout('table')" 
                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition text-slate-500 hover:text-slate-900" 
                        title="List View">
                    <i data-lucide="list" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">List</span>
                </button>
            </div>
        </div>

        <!-- Collapsible Filter Drawer -->
        <div id="catalogFilterDrawer" class="{{ request()->anyFilled(['material', 'finish', 'min_price', 'max_price']) ? '' : 'hidden' }} rounded-2xl bg-white border border-slate-200 p-5 shadow-xs transition-all">
            <form action="{{ route('catalog') }}" method="GET" class="space-y-4">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Price Range -->
                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-1.5">Price Range (₹ / sq ft)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full input-custom text-xs py-1.5 bg-white font-mono">
                            <span class="text-slate-400 text-xs">to</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full input-custom text-xs py-1.5 bg-white font-mono">
                        </div>
                    </div>

                    <!-- Material Type -->
                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-1.5">Material Composition</label>
                        <select name="material" class="w-full input-custom text-xs py-2 bg-white">
                            <option value="">All Materials</option>
                            <option value="Hardwood" {{ request('material') === 'Hardwood' ? 'selected' : '' }}>Natural Hardwood / Oak</option>
                            <option value="SPC" {{ request('material') === 'SPC' ? 'selected' : '' }}>SPC Stone Polymer Core</option>
                            <option value="Laminate" {{ request('material') === 'Laminate' ? 'selected' : '' }}>HDF High Density Laminate</option>
                            <option value="Vinyl" {{ request('material') === 'Vinyl' ? 'selected' : '' }}>Luxury Vinyl Plank (LVP)</option>
                        </select>
                    </div>

                    <!-- Surface Finish -->
                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-1.5">Surface Finish</label>
                        <select name="finish" class="w-full input-custom text-xs py-2 bg-white">
                            <option value="">All Finishes</option>
                            <option value="Matte" {{ request('finish') === 'Matte' ? 'selected' : '' }}>Matte Lacquered</option>
                            <option value="Embossed" {{ request('finish') === 'Embossed' ? 'selected' : '' }}>Embossed in Register (EIR)</option>
                            <option value="Gloss" {{ request('finish') === 'Gloss' ? 'selected' : '' }}>High Gloss / Polished</option>
                            <option value="Handscraped" {{ request('finish') === 'Handscraped' ? 'selected' : '' }}>Artisan Handscraped</option>
                        </select>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full btn-brand py-2 text-xs font-bold shadow-xs">
                            Apply Filters
                        </button>
                        <a href="{{ route('catalog', request()->only(['category'])) }}" class="py-2 px-3 rounded-xl border border-slate-200 text-xs text-slate-600 hover:text-slate-900 text-center font-medium">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- 5. Product Grid & Table Views -->
        <div id="catalogDisplayArea">
            @if($products->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-brand-light text-brand flex items-center justify-center mx-auto">
                        <i data-lucide="search-x" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No flooring collections found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        We couldn't find any flooring matching your selected search terms or filters. Try resetting the filters or searching for another material.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('catalog') }}" class="btn-brand py-2 px-5 text-xs font-bold shadow-xs inline-flex">
                            Reset All Filters
                        </a>
                    </div>
                </div>
            @else
                <!-- 5A. Grid View -->
                <div id="catalogGridView" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-3.5 sm:gap-5">
                    @foreach($products as $product)
                        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-2xs hover:shadow-md hover:border-brand transition flex flex-col justify-between group">
                            <div>
                                <!-- Image Card -->
                                <div class="aspect-4/3 relative overflow-hidden bg-white border-b border-slate-100">
                                    <a href="{{ route('products.show', $product) }}" class="block w-full h-full">
                                        <img id="catalog-card-img-{{ $product->id }}" 
                                             src="{{ $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </a>

                                    <div class="absolute bottom-2.5 left-2.5 bg-black/60 backdrop-blur-xs text-white text-[10px] sm:text-[11px] font-medium px-2.5 py-0.5 rounded-full pointer-events-none">
                                        {{ $product->category->name }}
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-3 sm:p-4 space-y-1">
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-brand transition truncate">
                                        <a href="{{ route('products.show', $product) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    <p class="text-[11px] text-slate-400 font-medium truncate">
                                        {{ $product->brand ?: ($product->category->name . ' Flooring') }}
                                    </p>
                                    <div class="pt-1 flex items-baseline gap-1">
                                        <span class="text-xs sm:text-sm font-extrabold text-slate-900 font-mono">
                                            ₹{{ number_format($product->min_price, 0) }}
                                        </span>
                                        <span class="text-[10px] sm:text-xs text-slate-400 font-normal">/ sq ft</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer: Interactive Swatch Dots -->
                            <div class="px-3 sm:px-4 pb-3 pt-1.5 flex items-center justify-between border-t border-slate-100">
                                <div class="flex items-center gap-1.5">
                                    @forelse($product->publicShades->take(5) as $idx => $s)
                                        <button type="button" 
                                                onclick="selectCardShade(this, 'catalog-card-img-{{ $product->id }}', '{{ $s->image ?: $product->primary_image }}', '{{ addslashes($s->shade_name) }}')"
                                                title="{{ $s->shade_name }}" 
                                                class="shade-swatch-btn w-4 h-4 sm:w-5 sm:h-5 rounded-full border-2 border-white shadow-2xs overflow-hidden transition cursor-pointer {{ $idx === 0 ? 'ring-2 ring-brand scale-110' : 'ring-1 ring-slate-300 hover:ring-slate-400' }}">
                                            <img src="{{ $s->image ?: $product->primary_image }}" alt="{{ $s->shade_name }}" class="w-full h-full object-cover pointer-events-none">
                                        </button>
                                    @empty
                                        <span class="w-3.5 h-3.5 rounded-full bg-[#8C532E]"></span>
                                        <span class="w-3.5 h-3.5 rounded-full bg-[#5C351B]"></span>
                                        <span class="w-3.5 h-3.5 rounded-full bg-accent"></span>
                                    @endforelse
                                </div>
                                <a href="{{ route('products.show', $product) }}" class="text-slate-400 group-hover:text-brand group-hover:translate-x-0.5 transition" title="View details">
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 5B. Table View -->
                <div id="catalogTableView" class="hidden overflow-x-auto w-full bg-white rounded-2xl border border-slate-200 shadow-2xs">
                    <table class="w-full text-left text-xs whitespace-nowrap divide-y divide-slate-100">
                        <thead class="bg-white text-slate-600 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4">Flooring Model</th>
                                <th class="py-3.5 px-4">Category</th>
                                <th class="py-3.5 px-4">Thickness & Size</th>
                                <th class="py-3.5 px-4">Available Shades</th>
                                <th class="py-3.5 px-4">Starting Price</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($products as $product)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden border border-slate-200 bg-white shrink-0">
                                                <img src="{{ $product->primary_image ?: '/images/flooring/category-wood.svg' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <a href="{{ route('products.show', $product) }}" class="font-bold text-sm text-slate-900 hover:text-brand transition block">
                                                    {{ $product->name }}
                                                </a>
                                                <span class="text-[10px] text-slate-400 font-mono">
                                                    SKU: {{ $product->publicShades->first()?->sku ?? ('SKU-' . str_pad($product->id, 3, '0', STR_PAD_LEFT)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge-brand">
                                            {{ $product->category->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">
                                        <span class="font-semibold text-slate-900 block">{{ $product->thickness ?: 'Standard' }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $product->dimensions ?: 'Standard Planks' }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-1.5">
                                            @foreach($product->publicShades->take(4) as $s)
                                                <span title="{{ $s->shade_name }}" class="w-4 h-4 rounded-full border border-white shadow-2xs overflow-hidden ring-1 ring-slate-200">
                                                    <img src="{{ $s->image ?: $product->primary_image }}" alt="{{ $s->shade_name }}" class="w-full h-full object-cover">
                                                </span>
                                            @endforeach
                                            <span class="text-[10px] font-bold text-slate-500 ml-1">
                                                {{ $product->publicShades->count() }} {{ Str::plural('shade', $product->publicShades->count()) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="text-sm font-extrabold text-slate-900 font-mono">
                                            ₹{{ number_format($product->min_price, 0) }}
                                            <span class="text-[10px] text-slate-400 font-normal">/sqft</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('products.show', $product) }}" class="btn-brand text-xs py-1.5 px-3">
                                            View Shades
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function setCatalogLayout(layout) {
        const gridView = document.getElementById('catalogGridView');
        const tableView = document.getElementById('catalogTableView');
        const toggleGrid = document.getElementById('viewToggleGrid');
        const toggleTable = document.getElementById('viewToggleTable');

        if (!gridView || !tableView) return;

        if (layout === 'table') {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');

            toggleTable.classList.remove('text-slate-400', 'bg-white');
            toggleTable.classList.add('bg-brand', 'text-white', 'shadow-xs');

            toggleGrid.classList.remove('bg-brand', 'text-white', 'shadow-xs');
            toggleGrid.classList.add('text-slate-400', 'bg-white');

            localStorage.setItem('flooros_catalog_layout', 'table');
        } else {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');

            toggleGrid.classList.remove('text-slate-400', 'bg-white');
            toggleGrid.classList.add('bg-brand', 'text-white', 'shadow-xs');

            toggleTable.classList.remove('bg-brand', 'text-white', 'shadow-xs');
            toggleTable.classList.add('text-slate-400', 'bg-white');

            localStorage.setItem('flooros_catalog_layout', 'grid');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem('flooros_catalog_layout') || new URLSearchParams(window.location.search).get('view') || 'grid';
        setCatalogLayout(saved);
    });
</script>
@endsection
