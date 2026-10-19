@extends('layouts.admin')

@section('title', 'Add New Flooring Product')
@section('page_title', 'Create Product')

@section('content')
<div class="w-full max-w-7xl 2xl:max-w-[1600px] mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">Add New Flooring Product</h1>
            <p class="text-xs text-[#6B756F]">Configure collection attributes, specifications, and individual shade variants.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn-secondary text-xs py-2 px-3">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back to Products
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. Essential Product Information -->
        <div class="card-surface p-6 space-y-4">
            <h3 class="text-sm font-bold text-[#202925] border-b border-[#E3E8E4] pb-3 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-brand"></i> 1. Essential Product Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Product / Collection Name *</label>
                    <input type="text" name="name" id="product_name_input" value="{{ old('name') }}" required placeholder="e.g. Royal Oak Heritage Collection" class="w-full input-custom text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Category *</label>
                    <select name="category_id" required class="w-full input-custom text-sm">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Unit of Measurement *</label>
                    <select name="unit_of_measure" required class="w-full input-custom text-sm">
                        <option value="sq ft" {{ old('unit_of_measure') === 'sq ft' ? 'selected' : '' }}>Square Feet (sq ft)</option>
                        <option value="sq m" {{ old('unit_of_measure') === 'sq m' ? 'selected' : '' }}>Square Metres (sq m)</option>
                        <option value="box" {{ old('unit_of_measure') === 'box' ? 'selected' : '' }}>Box</option>
                        <option value="piece" {{ old('unit_of_measure') === 'piece' ? 'selected' : '' }}>Piece</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Base Starting Price (₹ / unit) *</label>
                    <input type="number" step="0.01" name="base_price" value="{{ old('base_price', '450.00') }}" required class="w-full input-custom text-sm">
                </div>

                @if(\App\Models\Setting::isProductFieldVisible('show_brand_collection'))
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Brand / Manufacturer</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" placeholder="e.g. Mikasa Atelier / Kronos" class="w-full input-custom text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Collection / Series</label>
                        <input type="text" name="collection_name" value="{{ old('collection_name') }}" placeholder="e.g. Heritage Reserve" class="w-full input-custom text-sm">
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Collapsible Advanced Specifications & Dimensions (Optional Fields) -->
        <details class="group card-surface p-6 space-y-4" open>
            <summary class="flex items-center justify-between cursor-pointer list-none border-b border-[#E3E8E4] pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="sliders" class="w-4 h-4 text-brand"></i>
                    <h3 class="text-sm font-bold text-[#202925] uppercase tracking-wider">
                        2. Flooring Specifications & Details (Optional)
                    </h3>
                </div>
                <div class="flex items-center gap-2 text-xs text-[#6B756F]">
                    <span class="group-open:hidden">Click to Expand</span>
                    <span class="hidden group-open:inline">Click to Collapse</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-neutral-400 group-open:rotate-180 transition-transform"></i>
                </div>
            </summary>

            <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @if(\App\Models\Setting::isProductFieldVisible('show_material_surface'))
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Material Composition</label>
                        <input type="text" name="material_type" value="{{ old('material_type') }}" placeholder="e.g. Engineered European Oak" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Surface Finish</label>
                        <input type="text" name="surface_finish" value="{{ old('surface_finish') }}" placeholder="e.g. Brushed UV Matte Lacquer" class="w-full input-custom text-sm">
                    </div>
                @endif

                @if(\App\Models\Setting::isProductFieldVisible('show_dimensions_thickness'))
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Plank Thickness</label>
                        <input type="text" name="thickness" value="{{ old('thickness') }}" placeholder="e.g. 14 mm (3mm wear layer)" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Dimensions (L x W)</label>
                        <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="e.g. 1820 mm x 190 mm" class="w-full input-custom text-sm">
                    </div>
                @endif

                @if(\App\Models\Setting::isProductFieldVisible('show_installation_warranty'))
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Installation Method</label>
                        <input type="text" name="installation_type" value="{{ old('installation_type') }}" placeholder="e.g. Valinge 5G Click-Lock" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Warranty Years</label>
                        <input type="text" name="warranty_years" value="{{ old('warranty_years') }}" placeholder="e.g. 25 Years Residential" class="w-full input-custom text-sm">
                    </div>
                @endif

                @if(\App\Models\Setting::isProductFieldVisible('show_coverage_per_box'))
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Coverage Per Box (Sq Ft)</label>
                        <input type="number" step="0.01" name="coverage_per_box" value="{{ old('coverage_per_box', '21.50') }}" placeholder="e.g. 21.50" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Suitable Applications</label>
                        <input type="text" name="suitable_for" value="{{ old('suitable_for') }}" placeholder="e.g. Living, Bedroom, Commercial" class="w-full input-custom text-sm">
                    </div>
                @endif
            </div>

            @if(\App\Models\Setting::isProductFieldVisible('show_description'))
                <div class="pt-3 border-t border-slate-100">
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Architectural Marketing Description</label>
                    <textarea name="description" rows="3" placeholder="Detailed architectural description for public website..." class="w-full input-custom text-sm">{{ old('description') }}</textarea>
                </div>
            @endif
        </details>

        <!-- 3. Dynamic Shades & Variants Table -->
        <div class="card-surface p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-[#E3E8E4] pb-3">
                <div>
                    <h3 class="text-sm font-bold text-[#202925] uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="palette" class="w-4 h-4 text-brand"></i> 3. Shades & Sellable SKUs (Variants)
                    </h3>
                    <p class="text-xs text-[#6B756F]">Each shade is a unique SKU with separate stock quantity and pricing.</p>
                </div>
                <button type="button" onclick="addShadeRow()" class="btn-brand text-xs py-1.5 px-3">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Shade
                </button>
            </div>

            <div class="overflow-x-auto w-full">
                <table id="shadesTable" class="w-full text-xs text-left whitespace-nowrap">
                    <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="p-2.5">Shade Photo</th>
                            <th class="p-2.5">Shade Name *</th>
                            <th class="p-2.5">SKU *</th>
                            <th class="p-2.5">Code</th>
                            <th class="p-2.5 text-right">Selling Price (₹) *</th>
                            <th class="p-2.5 text-right">Cost Price (₹) *</th>
                            <th class="p-2.5 text-right">Opening Stock *</th>
                            <th class="p-2.5 text-right">Threshold *</th>
                            <th class="p-2.5 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="shadesTbody" class="divide-y divide-slate-100 bg-white">
                        <!-- Row 0 -->
                        <tr class="shade-row">
                            <td class="p-2">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-9 h-9 rounded-md bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center shadow-2xs">
                                        <img id="shadePreview_0" src="/images/flooring/category-wood.svg" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <input type="file" 
                                           name="shades[0][image]" 
                                           accept="image/*" 
                                           onchange="previewShadeRowImage(this, 'shadePreview_0')" 
                                           class="w-32 text-[10px] file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-brand-light file:text-brand hover:file:bg-brand hover:file:text-white transition cursor-pointer"
                                           title="Upload dedicated photo for this shade">
                                </div>
                            </td>
                            <td class="p-2">
                                <input type="text" name="shades[0][shade_name]" id="shade_name_0" value="{{ old('shades.0.shade_name') }}" required placeholder="e.g. Natural Oak" class="w-full input-custom text-xs" oninput="syncSkuRow(0)">
                            </td>
                            <td class="p-2">
                                <input type="text" name="shades[0][sku]" id="shade_sku_0" value="{{ old('shades.0.sku', 'SKU-' . strtoupper(\Illuminate\Support\Str::random(5))) }}" required oninput="this.dataset.manuallyEdited='true'" placeholder="e.g. SKU-NAT-1001" class="w-full input-custom text-xs font-mono uppercase">
                            </td>
                            <td class="p-2">
                                <input type="text" name="shades[0][shade_code]" value="{{ old('shades.0.shade_code', 'SH-01') }}" placeholder="Code" class="w-20 input-custom text-xs font-mono uppercase">
                            </td>
                            <td class="p-2 text-right">
                                <input type="number" step="0.01" name="shades[0][selling_price]" value="485.00" required class="w-24 input-custom text-xs text-right">
                            </td>
                            <td class="p-2 text-right">
                                <input type="number" step="0.01" name="shades[0][cost_price]" value="320.00" required class="w-24 input-custom text-xs text-right">
                            </td>
                            <td class="p-2 text-right">
                                <input type="number" step="0.01" name="shades[0][stock_quantity]" value="500.00" required class="w-24 input-custom text-xs text-right font-bold text-brand">
                            </td>
                            <td class="p-2 text-right">
                                <input type="number" step="0.01" name="shades[0][reorder_threshold]" value="100.00" required class="w-20 input-custom text-xs text-right">
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removeShadeRow(this)" class="p-1 text-neutral-400 hover:text-rose-600">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Media & Publication Status -->
        <div class="card-surface p-6 space-y-4">
            <h3 class="text-sm font-bold text-[#202925] border-b border-[#E3E8E4] pb-3 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="image" class="w-4 h-4 text-brand"></i> 4. Media & Visibility Settings
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Primary Collection Image</label>
                    <input type="file" name="primary_image" accept="image/*" class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-brand-light file:text-brand hover:file:bg-brand hover:file:text-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#202925] mb-1">Publication Status</label>
                    <select name="status" class="w-full input-custom text-sm">
                        <option value="published">Published (Visible in Catalog)</option>
                        <option value="draft">Draft (Internal Only)</option>
                    </select>
                </div>
            </div>

            <div class="pt-2 flex items-center gap-6">
                <label class="flex items-center gap-2 text-xs font-medium text-[#202925] cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-brand focus:ring-brand">
                    Feature on Customer Homepage
                </label>
                <label class="flex items-center gap-2 text-xs font-medium text-[#202925] cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" checked class="rounded text-brand focus:ring-brand">
                    Visible in Public Catalog
                </label>
            </div>
        </div>

        <!-- Save Button Bar -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary text-sm py-2.5 px-5">Cancel</a>
            <button type="submit" class="btn-brand text-sm py-2.5 px-8 font-semibold shadow-xs">
                <i data-lucide="check" class="w-4 h-4"></i> Create Product & SKUs
            </button>
        </div>
    </form>
</div>

<script>
    let shadeIndex = 1;

    function previewShadeRowImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                if (img) img.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function syncSkuRow(idx) {
        const nameInput = document.getElementById(`shade_name_${idx}`);
        const skuInput = document.getElementById(`shade_sku_${idx}`);
        if (skuInput && skuInput.dataset.manuallyEdited !== 'true' && nameInput && nameInput.value) {
            const prodName = document.getElementById('product_name_input')?.value || '';
            const prodPrefix = prodName.trim().slice(0, 3).toUpperCase().replace(/[^A-Z0-9]/g, '') || 'PRD';
            const shadeClean = nameInput.value.trim().slice(0, 4).toUpperCase().replace(/[^A-Z0-9]/g, '') || 'SHD';
            const rand = Math.floor(1000 + Math.random() * 9000);
            skuInput.value = `${prodPrefix}-${shadeClean}-${rand}`;
        }
    }

    function addShadeRow() {
        const tbody = document.getElementById('shadesTbody');
        const curIndex = shadeIndex;
        const prodName = document.getElementById('product_name_input')?.value || '';
        const prodPrefix = prodName.trim().slice(0, 3).toUpperCase().replace(/[^A-Z0-9]/g, '') || 'SKU';
        const rand = Math.floor(1000 + Math.random() * 9000);
        const tr = document.createElement('tr');
        tr.className = 'shade-row';
        tr.innerHTML = `
            <td class="p-2">
                <div class="flex items-center gap-1.5">
                    <div class="w-9 h-9 rounded-md bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center shadow-2xs">
                        <img id="shadePreview_${curIndex}" src="/images/flooring/category-wood.svg" alt="" class="w-full h-full object-cover">
                    </div>
                    <input type="file" 
                           name="shades[${curIndex}][image]" 
                           accept="image/*" 
                           onchange="previewShadeRowImage(this, 'shadePreview_${curIndex}')" 
                           class="w-32 text-[10px] file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-brand-light file:text-brand hover:file:bg-brand hover:file:text-white transition cursor-pointer">
                </div>
            </td>
            <td class="p-2">
                <input type="text" name="shades[${curIndex}][shade_name]" id="shade_name_${curIndex}" required placeholder="e.g. Smoked Honey" class="w-full input-custom text-xs" oninput="syncSkuRow(${curIndex})">
            </td>
            <td class="p-2">
                <input type="text" name="shades[${curIndex}][sku]" id="shade_sku_${curIndex}" value="${prodPrefix}-VAR-${rand}" required oninput="this.dataset.manuallyEdited='true'" placeholder="SKU-02" class="w-full input-custom text-xs font-mono uppercase">
            </td>
            <td class="p-2">
                <input type="text" name="shades[${curIndex}][shade_code]" value="CODE-${curIndex+1}" placeholder="Code" class="w-20 input-custom text-xs font-mono uppercase">
            </td>
            <td class="p-2 text-right">
                <input type="number" step="0.01" name="shades[${curIndex}][selling_price]" value="485.00" required class="w-24 input-custom text-xs text-right">
            </td>
            <td class="p-2 text-right">
                <input type="number" step="0.01" name="shades[${curIndex}][cost_price]" value="320.00" required class="w-24 input-custom text-xs text-right">
            </td>
            <td class="p-2 text-right">
                <input type="number" step="0.01" name="shades[${curIndex}][stock_quantity]" value="250.00" required class="w-24 input-custom text-xs text-right font-bold text-brand">
            </td>
            <td class="p-2 text-right">
                <input type="number" step="0.01" name="shades[${curIndex}][reorder_threshold]" value="100.00" required class="w-20 input-custom text-xs text-right">
            </td>
            <td class="p-2 text-center">
                <button type="button" onclick="removeShadeRow(this)" class="p-1 text-neutral-400 hover:text-rose-600">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        shadeIndex++;
        if (window.createFloorIcons) window.createFloorIcons();
    }

    function removeShadeRow(btn) {
        const rows = document.querySelectorAll('.shade-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
        } else {
            window.showAlert({
                title: 'Notice',
                message: 'A product must contain at least one shade variant.',
                type: 'warning'
            });
        }
    }
</script>
@endsection
