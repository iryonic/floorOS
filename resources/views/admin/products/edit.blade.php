@extends('layouts.admin')

@section('title', "Edit {$product->name}")
@section('page_title', 'Edit Product')

@section('content')
<div class="w-full max-w-7xl 2xl:max-w-[1600px] mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">Edit: {{ $product->name }}</h1>
            <p class="text-xs text-[#6B756F]">Update specifications, publication status, or review current shade variants.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('products.show', $product) }}" target="_blank" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-brand"></i> View Public
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card-surface p-6 space-y-4">
            <h3 class="text-sm font-bold text-[#202925] border-b border-[#E3E8E4] pb-3 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-brand"></i> Core Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full input-custom text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Category *</label>
                    <select name="category_id" required class="w-full input-custom text-sm">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Base Price (₹ / unit) *</label>
                    <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price) }}" required class="w-full input-custom text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Unit of Measurement *</label>
                    <select name="unit_of_measure" required class="w-full input-custom text-sm">
                        <option value="sq ft" {{ $product->unit_of_measure === 'sq ft' ? 'selected' : '' }}>Square Feet (sq ft)</option>
                        <option value="sq m" {{ $product->unit_of_measure === 'sq m' ? 'selected' : '' }}>Square Metres (sq m)</option>
                        <option value="box" {{ $product->unit_of_measure === 'box' ? 'selected' : '' }}>Box</option>
                        <option value="piece" {{ $product->unit_of_measure === 'piece' ? 'selected' : '' }}>Piece</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" class="w-full input-custom text-sm">
                </div>
            </div>
        </div>

        <!-- Collapsible Specifications & Details -->
        <details class="group card-surface p-6 space-y-4" open>
            <summary class="flex items-center justify-between cursor-pointer list-none border-b border-[#E3E8E4] pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="sliders" class="w-4 h-4 text-brand"></i>
                    <h3 class="text-sm font-bold text-[#202925] uppercase tracking-wider">
                        Specifications & Physical Attributes
                    </h3>
                </div>
                <div class="flex items-center gap-2 text-xs text-[#6B756F]">
                    <span class="group-open:hidden">Click to Expand</span>
                    <span class="hidden group-open:inline">Click to Collapse</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-neutral-400 group-open:rotate-180 transition-transform"></i>
                </div>
            </summary>

            <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Coverage Per Box (Sq Ft)</label>
                    <input type="number" step="0.01" name="coverage_per_box" value="{{ old('coverage_per_box', $product->coverage_per_box) }}" class="w-full input-custom text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions', $product->dimensions) }}" class="w-full input-custom text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Thickness</label>
                    <input type="text" name="thickness" value="{{ old('thickness', $product->thickness) }}" class="w-full input-custom text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Surface Finish</label>
                    <input type="text" name="surface_finish" value="{{ old('surface_finish', $product->surface_finish) }}" class="w-full input-custom text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Warranty</label>
                    <input type="text" name="warranty_years" value="{{ old('warranty_years', $product->warranty_years) }}" class="w-full input-custom text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Installation Method</label>
                    <input type="text" name="installation_type" value="{{ old('installation_type', $product->installation_type) }}" class="w-full input-custom text-sm">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100">
                <label class="block text-xs font-semibold text-[#202925] mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full input-custom text-sm">{{ old('description', $product->description) }}</textarea>
            </div>
        </details>

        <!-- Associated Shade Variants & Dedicated Images -->
        <div class="card-surface p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#E3E8E4] pb-3">
                <div>
                    <h3 class="text-sm font-bold text-[#202925] uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="palette" class="w-4 h-4 text-brand"></i>
                        Associated Shade Variants & Dedicated Photos ({{ $product->shades->count() }})
                    </h3>
                    <p class="text-xs text-[#6B756F] mt-0.5">Upload individual dedicated photos for each shade, update sellable prices, or add new shades.</p>
                </div>
                <button type="button" onclick="addNewShadeRowInEdit()" class="btn-brand text-xs py-1.5 px-3 self-start sm:self-auto flex items-center gap-1.5 shadow-2xs">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add New Shade
                </button>
            </div>

            <!-- Existing Shades List -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-xs text-left whitespace-nowrap">
                    <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="p-2.5">Dedicated Shade Photo</th>
                            <th class="p-2.5">Shade Name *</th>
                            <th class="p-2.5">SKU</th>
                            <th class="p-2.5">Code</th>
                            <th class="p-2.5 text-right">Selling Price (₹)</th>
                            <th class="p-2.5 text-right">Cost Price (₹)</th>
                            <th class="p-2.5 text-right">Stock</th>
                            <th class="p-2.5 text-right">Alert Level</th>
                            <th class="p-2.5 text-center">Active</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($product->shades as $shade)
                            <tr class="hover:bg-brand-light/30 transition">
                                <!-- Dedicated Image with preview and replace input -->
                                <td class="p-2.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0 shadow-2xs">
                                            <img id="existingShadePreview_{{ $shade->id }}" 
                                                 src="{{ $shade->image ?: $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                                                 alt="{{ $shade->shade_name }}" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <input type="file" 
                                                   name="existing_shades[{{ $shade->id }}][image]" 
                                                   accept="image/*" 
                                                   onchange="previewExistingShadeImage(this, 'existingShadePreview_{{ $shade->id }}')" 
                                                   class="w-32 text-[10px] file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-brand-light file:text-brand hover:file:bg-brand hover:file:text-white transition cursor-pointer"
                                                   title="Upload dedicated photo for this shade">
                                            <span class="text-[9px] text-slate-400 block mt-0.5">Click to replace photo</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Shade Name -->
                                <td class="p-2.5">
                                    <input type="text" 
                                           name="existing_shades[{{ $shade->id }}][shade_name]" 
                                           value="{{ $shade->shade_name }}" 
                                           required 
                                           class="w-40 input-custom text-xs font-semibold">
                                </td>

                                <!-- SKU (Read-only identifier) -->
                                <td class="p-2.5">
                                    <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded border border-slate-200">
                                        {{ $shade->sku }}
                                    </span>
                                </td>

                                <!-- Code -->
                                <td class="p-2.5">
                                    <input type="text" 
                                           name="existing_shades[{{ $shade->id }}][shade_code]" 
                                           value="{{ $shade->shade_code }}" 
                                           class="w-20 input-custom text-xs font-mono">
                                </td>

                                <!-- Selling Price -->
                                <td class="p-2.5 text-right">
                                    <input type="number" 
                                           step="0.01" 
                                           name="existing_shades[{{ $shade->id }}][selling_price]" 
                                           value="{{ $shade->selling_price }}" 
                                           required 
                                           class="w-24 input-custom text-xs text-right font-bold text-brand">
                                </td>

                                <!-- Cost Price -->
                                <td class="p-2.5 text-right">
                                    <input type="number" 
                                           step="0.01" 
                                           name="existing_shades[{{ $shade->id }}][cost_price]" 
                                           value="{{ $shade->cost_price }}" 
                                           required 
                                           class="w-24 input-custom text-xs text-right">
                                </td>

                                <!-- Current Stock (managed via adjustments/purchases) -->
                                <td class="p-2.5 text-right">
                                    <span class="font-bold text-xs {{ $shade->stock_quantity <= $shade->reorder_threshold ? 'text-amber-600' : 'text-slate-800' }}">
                                        {{ number_format($shade->stock_quantity, 1) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">{{ $shade->unit }}</span>
                                </td>

                                <!-- Reorder Threshold -->
                                <td class="p-2.5 text-right">
                                    <input type="number" 
                                           step="0.01" 
                                           name="existing_shades[{{ $shade->id }}][reorder_threshold]" 
                                           value="{{ $shade->reorder_threshold }}" 
                                           required 
                                           class="w-20 input-custom text-xs text-right">
                                </td>

                                <!-- Is Active Toggle -->
                                <td class="p-2.5 text-center">
                                    <input type="hidden" name="existing_shades[{{ $shade->id }}][is_active]" value="0">
                                    <input type="checkbox" 
                                           name="existing_shades[{{ $shade->id }}][is_active]" 
                                           value="1" 
                                           {{ $shade->is_active ? 'checked' : '' }} 
                                           class="rounded text-brand focus:ring-brand">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <!-- Container for New Shades Added During Edit -->
                    <tbody id="newShadesTbody" class="divide-y divide-slate-100 bg-emerald-50/20"></tbody>
                </table>
            </div>
        </div>

        <!-- Publication & Settings -->
        <div class="card-surface p-6 space-y-4">
            <h3 class="text-sm font-bold text-[#202925] border-b border-[#E3E8E4] pb-3 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="eye" class="w-4 h-4 text-brand"></i> Publication Status
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Status</label>
                    <select name="status" class="w-full input-custom text-sm">
                        <option value="published" {{ $product->status === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ $product->status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="pt-6 flex items-center gap-6">
                    <label class="flex items-center gap-2 text-xs font-medium text-[#202925] cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded text-brand focus:ring-brand">
                        Feature on Homepage
                    </label>
                    <label class="flex items-center gap-2 text-xs font-medium text-[#202925] cursor-pointer">
                        <input type="checkbox" name="is_public" value="1" {{ $product->is_public ? 'checked' : '' }} class="rounded text-brand focus:ring-brand">
                        Visible in Public Catalog
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary text-sm py-2.5 px-5">Cancel</a>
            <button type="submit" class="btn-brand text-sm py-2.5 px-8 font-semibold shadow-xs">
                Update Product Details
            </button>
        </div>
    </form>
</div>

<script>
    let newShadeIndex = 0;

    function previewExistingShadeImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                if (img) img.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewNewShadeImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                if (img) img.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addNewShadeRowInEdit() {
        const tbody = document.getElementById('newShadesTbody');
        const rand = Math.floor(1000 + Math.random() * 9000);
        const curIndex = newShadeIndex;
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-emerald-50/40 transition';
        tr.innerHTML = `
            <td class="p-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-white border border-slate-200 shrink-0 shadow-2xs">
                        <img id="newShadePreview_${curIndex}" src="{{ $product->primary_image ?: '/images/flooring/category-wood.svg' }}" alt="New Shade" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <input type="file" 
                               name="new_shades[${curIndex}][image]" 
                               accept="image/*" 
                               onchange="previewNewShadeImage(this, 'newShadePreview_${curIndex}')" 
                               class="w-32 text-[10px] file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-brand-light file:text-brand hover:file:bg-brand hover:file:text-white transition cursor-pointer"
                               title="Upload dedicated photo for this shade">
                        <span class="text-[9px] text-brand font-semibold block mt-0.5">New Shade Photo</span>
                    </div>
                </div>
            </td>
            <td class="p-2.5">
                <input type="text" name="new_shades[${curIndex}][shade_name]" required placeholder="e.g. Weathered Grey" class="w-40 input-custom text-xs font-semibold">
            </td>
            <td class="p-2.5">
                <input type="text" name="new_shades[${curIndex}][sku]" value="SKU-VAR-${rand}" required class="w-28 input-custom text-xs font-mono uppercase">
            </td>
            <td class="p-2.5">
                <input type="text" name="new_shades[${curIndex}][shade_code]" value="SH-${curIndex+1}" class="w-20 input-custom text-xs font-mono">
            </td>
            <td class="p-2.5 text-right">
                <input type="number" step="0.01" name="new_shades[${curIndex}][selling_price]" value="{{ $product->base_price }}" required class="w-24 input-custom text-xs text-right font-bold text-brand">
            </td>
            <td class="p-2.5 text-right">
                <input type="number" step="0.01" name="new_shades[${curIndex}][cost_price]" value="300.00" required class="w-24 input-custom text-xs text-right">
            </td>
            <td class="p-2.5 text-right">
                <input type="number" step="0.01" name="new_shades[${curIndex}][stock_quantity]" value="100.00" required class="w-20 input-custom text-xs text-right font-semibold">
            </td>
            <td class="p-2.5 text-right">
                <input type="number" step="0.01" name="new_shades[${curIndex}][reorder_threshold]" value="25.00" required class="w-20 input-custom text-xs text-right">
            </td>
            <td class="p-2.5 text-center">
                <button type="button" onclick="this.closest('tr').remove()" class="p-1 text-slate-400 hover:text-rose-600">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        newShadeIndex++;
        if (window.createFloorIcons) window.createFloorIcons();
    }
</script>
@endsection
