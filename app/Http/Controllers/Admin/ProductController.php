<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductShade;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'shades']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('collection_name', 'like', "%{$search}%")
                    ->orWhereHas('shades', function ($sq) use ($search) {
                        $sq->where('sku', 'like', "%{$search}%")
                            ->orWhere('shade_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'collection_name' => 'nullable|string|max:100',
            'material_type' => 'nullable|string|max:100',
            'thickness' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:100',
            'surface_finish' => 'nullable|string|max:100',
            'suitable_for' => 'nullable|string|max:255',
            'warranty_years' => 'nullable|string|max:100',
            'installation_type' => 'nullable|string|max:100',
            'unit_of_measure' => 'required|string|max:20',
            'coverage_per_box' => 'nullable|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
            'status' => 'required|in:draft,published,archived',
            'primary_image' => 'nullable|image|max:4096',
            'shades' => 'required|array|min:1',
            'shades.*.shade_name' => 'required|string|max:100',
            'shades.*.shade_code' => 'nullable|string|max:50',
            'shades.*.sku' => ['required', 'string', 'max:100', 'distinct', Rule::unique('product_shades', 'sku')->withoutTrashed()],
            'shades.*.color_family' => 'nullable|string|max:50',
            'shades.*.finish' => 'nullable|string|max:50',
            'shades.*.selling_price' => 'required|numeric|min:0',
            'shades.*.cost_price' => 'required|numeric|min:0',
            'shades.*.stock_quantity' => 'required|numeric|min:0',
            'shades.*.reorder_threshold' => 'required|numeric|min:0',
            'shades.*.image' => 'nullable|image|max:4096',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $slug = Str::slug($validated['name']);
            if (Product::where('slug', $slug)->exists()) {
                $slug = $slug . '-' . time();
            }

            if (!file_exists(public_path('uploads/products'))) {
                mkdir(public_path('uploads/products'), 0777, true);
            }
            if (!file_exists(public_path('uploads/shades'))) {
                mkdir(public_path('uploads/shades'), 0777, true);
            }

            $primaryImagePath = '/images/flooring/category-wood.svg';
            if ($request->hasFile('primary_image')) {
                $file = $request->file('primary_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/products'), $filename);
                $primaryImagePath = '/uploads/products/' . $filename;
            }

            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'category_id' => $validated['category_id'],
                'brand' => $validated['brand'] ?? null,
                'collection_name' => $validated['collection_name'] ?? null,
                'material_type' => $validated['material_type'] ?? null,
                'thickness' => $validated['thickness'] ?? null,
                'dimensions' => $validated['dimensions'] ?? null,
                'surface_finish' => $validated['surface_finish'] ?? null,
                'suitable_for' => $validated['suitable_for'] ?? null,
                'warranty_years' => $validated['warranty_years'] ?? null,
                'installation_type' => $validated['installation_type'] ?? null,
                'unit_of_measure' => $validated['unit_of_measure'],
                'coverage_per_box' => $validated['coverage_per_box'] ?? null,
                'base_price' => $validated['base_price'],
                'description' => $validated['description'] ?? null,
                'primary_image' => $primaryImagePath,
                'gallery_images' => [$primaryImagePath],
                'is_featured' => $request->boolean('is_featured'),
                'is_public' => $request->boolean('is_public', true),
                'status' => $validated['status'],
                'seo_title' => $validated['name'] . ' | FloorOS Gallery',
                'seo_description' => Str::limit(strip_tags($validated['description'] ?? ''), 150),
            ]);

            foreach ($validated['shades'] as $index => $sData) {
                $shadeImagePath = $primaryImagePath;
                if ($request->hasFile("shades.{$index}.image")) {
                    $sFile = $request->file("shades.{$index}.image");
                    $sFilename = time() . '_' . $index . '_' . Str::slug(pathinfo($sFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $sFile->getClientOriginalExtension();
                    $sFile->move(public_path('uploads/shades'), $sFilename);
                    $shadeImagePath = '/uploads/shades/' . $sFilename;
                }

                $shade = ProductShade::create([
                    'product_id' => $product->id,
                    'shade_name' => $sData['shade_name'],
                    'shade_code' => $sData['shade_code'] ?? null,
                    'sku' => $sData['sku'],
                    'color_family' => $sData['color_family'] ?? null,
                    'finish' => $sData['finish'] ?? null,
                    'image' => $shadeImagePath,
                    'selling_price' => $sData['selling_price'],
                    'cost_price' => $sData['cost_price'],
                    'stock_quantity' => $sData['stock_quantity'],
                    'unit' => $product->unit_of_measure,
                    'reorder_threshold' => $sData['reorder_threshold'],
                    'is_active' => true,
                    'is_public' => true,
                ]);

                // Record opening balance movement if opening stock > 0
                if ((float) $sData['stock_quantity'] > 0) {
                    StockMovement::create([
                        'reference_number' => 'MOV-INIT-' . $shade->sku . '-' . time(),
                        'product_shade_id' => $shade->id,
                        'sku' => $shade->sku,
                        'type' => 'opening',
                        'direction' => 'in',
                        'quantity' => $sData['stock_quantity'],
                        'unit' => $shade->unit,
                        'unit_cost' => $sData['cost_price'],
                        'previous_stock' => 0,
                        'new_stock' => $sData['stock_quantity'],
                        'reason' => 'Initial stock on product creation',
                        'user_id' => auth()->id(),
                        'occurred_at' => now(),
                    ]);
                }
            }

            return redirect()->route('admin.products.index')
                ->with('success', "Product '{$product->name}' created with " . count($validated['shades']) . " shades.");
        });
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'shades']);
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'collection_name' => 'nullable|string|max:100',
            'material_type' => 'nullable|string|max:100',
            'thickness' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:100',
            'surface_finish' => 'nullable|string|max:100',
            'suitable_for' => 'nullable|string|max:255',
            'warranty_years' => 'nullable|string|max:100',
            'installation_type' => 'nullable|string|max:100',
            'unit_of_measure' => 'required|string|max:20',
            'coverage_per_box' => 'nullable|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
            'status' => 'required|in:draft,published,archived',
            'primary_image' => 'nullable|image|max:4096',
            'existing_shades' => 'nullable|array',
            'existing_shades.*.shade_name' => 'nullable|string|max:100',
            'existing_shades.*.shade_code' => 'nullable|string|max:50',
            'existing_shades.*.selling_price' => 'nullable|numeric|min:0',
            'existing_shades.*.cost_price' => 'nullable|numeric|min:0',
            'existing_shades.*.reorder_threshold' => 'nullable|numeric|min:0',
            'existing_shades.*.is_active' => 'nullable|boolean',
            'existing_shades.*.image' => 'nullable|image|max:4096',
            'new_shades' => 'nullable|array',
            'new_shades.*.shade_name' => 'required_with:new_shades|string|max:100',
            'new_shades.*.sku' => ['required_with:new_shades', 'string', 'max:100', 'distinct', Rule::unique('product_shades', 'sku')->withoutTrashed()],
            'new_shades.*.shade_code' => 'nullable|string|max:50',
            'new_shades.*.selling_price' => 'required_with:new_shades|numeric|min:0',
            'new_shades.*.cost_price' => 'required_with:new_shades|numeric|min:0',
            'new_shades.*.stock_quantity' => 'required_with:new_shades|numeric|min:0',
            'new_shades.*.reorder_threshold' => 'required_with:new_shades|numeric|min:0',
            'new_shades.*.image' => 'nullable|image|max:4096',
        ]);

        return DB::transaction(function () use ($request, $validated, $product) {
            if (!file_exists(public_path('uploads/products'))) {
                mkdir(public_path('uploads/products'), 0777, true);
            }
            if (!file_exists(public_path('uploads/shades'))) {
                mkdir(public_path('uploads/shades'), 0777, true);
            }

            if ($request->hasFile('primary_image')) {
                $file = $request->file('primary_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/products'), $filename);
                $validated['primary_image'] = '/uploads/products/' . $filename;
            }

            $validated['is_featured'] = $request->boolean('is_featured');
            $validated['is_public'] = $request->boolean('is_public', true);

            $product->update($validated);

            // Update existing shades & dedicated images
            if ($request->has('existing_shades')) {
                foreach ($request->input('existing_shades', []) as $shadeId => $sData) {
                    $shade = ProductShade::where('product_id', $product->id)->find($shadeId);
                    if ($shade) {
                        $updateData = [
                            'shade_name' => $sData['shade_name'] ?? $shade->shade_name,
                            'shade_code' => $sData['shade_code'] ?? $shade->shade_code,
                            'selling_price' => $sData['selling_price'] ?? $shade->selling_price,
                            'cost_price' => $sData['cost_price'] ?? $shade->cost_price,
                            'reorder_threshold' => $sData['reorder_threshold'] ?? $shade->reorder_threshold,
                            'is_active' => isset($sData['is_active']) ? (bool)$sData['is_active'] : $shade->is_active,
                        ];

                        if ($request->hasFile("existing_shades.{$shadeId}.image")) {
                            $sFile = $request->file("existing_shades.{$shadeId}.image");
                            $sFilename = time() . '_' . $shadeId . '_' . Str::slug(pathinfo($sFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $sFile->getClientOriginalExtension();
                            $sFile->move(public_path('uploads/shades'), $sFilename);
                            $updateData['image'] = '/uploads/shades/' . $sFilename;
                        }

                        $shade->update($updateData);
                    }
                }
            }

            // Create newly added shades in edit view
            if (!empty($validated['new_shades'])) {
                foreach ($validated['new_shades'] as $index => $sData) {
                    $shadeImagePath = $product->primary_image ?: '/images/flooring/category-wood.svg';
                    if ($request->hasFile("new_shades.{$index}.image")) {
                        $sFile = $request->file("new_shades.{$index}.image");
                        $sFilename = time() . '_new_' . $index . '_' . Str::slug(pathinfo($sFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $sFile->getClientOriginalExtension();
                        $sFile->move(public_path('uploads/shades'), $sFilename);
                        $shadeImagePath = '/uploads/shades/' . $sFilename;
                    }

                    $newShade = ProductShade::create([
                        'product_id' => $product->id,
                        'shade_name' => $sData['shade_name'],
                        'shade_code' => $sData['shade_code'] ?? null,
                        'sku' => $sData['sku'],
                        'color_family' => $sData['color_family'] ?? null,
                        'finish' => $sData['finish'] ?? null,
                        'image' => $shadeImagePath,
                        'selling_price' => $sData['selling_price'],
                        'cost_price' => $sData['cost_price'],
                        'stock_quantity' => $sData['stock_quantity'],
                        'unit' => $product->unit_of_measure,
                        'reorder_threshold' => $sData['reorder_threshold'],
                        'is_active' => true,
                        'is_public' => true,
                    ]);

                    if ((float) $sData['stock_quantity'] > 0) {
                        StockMovement::create([
                            'reference_number' => 'MOV-INIT-' . $newShade->sku . '-' . time(),
                            'product_shade_id' => $newShade->id,
                            'sku' => $newShade->sku,
                            'type' => 'opening',
                            'direction' => 'in',
                            'quantity' => $sData['stock_quantity'],
                            'unit' => $newShade->unit,
                            'unit_cost' => $sData['cost_price'],
                            'previous_stock' => 0,
                            'new_stock' => $sData['stock_quantity'],
                            'reason' => 'Added new shade during product edit',
                            'user_id' => auth()->id(),
                            'occurred_at' => now(),
                        ]);
                    }
                }
            }

            return redirect()->route('admin.products.index')
                ->with('success', "Product '{$product->name}' updated successfully.");
        });
    }

    public function storeShade(Request $request, Product $product)
    {
        $validated = $request->validate([
            'shade_name' => 'required|string|max:100',
            'sku' => ['required', 'string', 'max:100', Rule::unique('product_shades', 'sku')->withoutTrashed()],
            'shade_code' => 'nullable|string|max:50',
            'color_family' => 'nullable|string|max:50',
            'finish' => 'nullable|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'reorder_threshold' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:4096',
        ]);

        return DB::transaction(function () use ($request, $validated, $product) {
            if (!file_exists(public_path('uploads/shades'))) {
                mkdir(public_path('uploads/shades'), 0777, true);
            }

            $shadeImagePath = $product->primary_image ?: '/images/flooring/category-wood.svg';
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . Str::slug($validated['shade_name']) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/shades'), $filename);
                $shadeImagePath = '/uploads/shades/' . $filename;
            }

            $shade = ProductShade::create([
                'product_id' => $product->id,
                'shade_name' => $validated['shade_name'],
                'shade_code' => $validated['shade_code'] ?? null,
                'sku' => $validated['sku'],
                'color_family' => $validated['color_family'] ?? null,
                'finish' => $validated['finish'] ?? null,
                'image' => $shadeImagePath,
                'selling_price' => $validated['selling_price'],
                'cost_price' => $validated['cost_price'],
                'stock_quantity' => $validated['stock_quantity'],
                'unit' => $product->unit_of_measure,
                'reorder_threshold' => $validated['reorder_threshold'],
                'is_active' => true,
                'is_public' => true,
            ]);

            if ((float) $validated['stock_quantity'] > 0) {
                StockMovement::create([
                    'reference_number' => 'MOV-INIT-' . $shade->sku . '-' . time(),
                    'product_shade_id' => $shade->id,
                    'sku' => $shade->sku,
                    'type' => 'opening',
                    'direction' => 'in',
                    'quantity' => $validated['stock_quantity'],
                    'unit' => $shade->unit,
                    'unit_cost' => $validated['cost_price'],
                    'previous_stock' => 0,
                    'new_stock' => $validated['stock_quantity'],
                    'reason' => 'Initial stock on shade creation',
                    'user_id' => auth()->id(),
                    'occurred_at' => now(),
                ]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Shade '{$shade->shade_name}' created successfully.",
                    'shade' => $shade,
                ]);
            }

            return redirect()->back()
                ->with('success', "Shade '{$shade->shade_name}' (SKU: {$shade->sku}) added to '{$product->name}'.");
        });
    }

    public function destroyShade(ProductShade $shade)
    {
        $product = $shade->product;
        // Check if shade has sales
        if ($shade->saleItems()->count() > 0) {
            $shade->update(['is_active' => false]);
            return redirect()->back()->with('success', "Shade '{$shade->shade_name}' has past sales and was deactivated.");
        }

        $shade->delete();
        return redirect()->back()->with('success', "Shade '{$shade->shade_name}' removed successfully.");
    }

    public function destroy(Product $product)
    {
        // Safe archival preserving historical sales and stock movements
        $product->update(['status' => 'archived', 'is_public' => false]);
        $product->shades()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$product->name}' has been archived.");
    }
}
