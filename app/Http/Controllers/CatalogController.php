<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Enquiry;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\ProductShade;
use App\Models\Setting;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Customer Portal Homepage
     */
    public function index()
    {
        $heroSlides = HeroSlide::active()->ordered()->get();

        $categories = Category::active()->withCount(['publishedProducts'])->get();

        $featuredProducts = Product::featured()
            ->with(['category', 'publicShades'])
            ->take(6)
            ->get();

        $newArrivals = Product::publicCatalog()
            ->with(['category', 'publicShades'])
            ->latest()
            ->take(4)
            ->get();

        $hero = [
            'eyebrow' => Setting::get('hero_eyebrow', 'FLOORING FOR BETTER SPACES'),
            'title' => Setting::get('hero_title', 'Beautiful Floors. Better Spaces.'),
            'subtitle' => Setting::get('hero_subtitle', 'Explore flooring styles, textures, and shades to find the right fit for your space.'),
        ];

        return view('public.home', compact('categories', 'featuredProducts', 'newArrivals', 'hero', 'heroSlides'));
    }

    /**
     * Complete Public Product Catalog
     */
    public function catalog(Request $request)
    {
        $query = Product::publicCatalog()->with(['category', 'publicShades']);

        // Search by product name, collection, brand, description, or shade name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('collection_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('shades', function ($sq) use ($search) {
                        $sq->where('shade_name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhere('shade_code', 'like', "%{$search}%");
                    });
            });
        }

        // Category filter
        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Material filter
        if ($material = $request->input('material')) {
            $query->where('material_type', 'like', "%{$material}%");
        }

        // Finish filter
        if ($finish = $request->input('finish')) {
            $query->where('surface_finish', 'like', "%{$finish}%");
        }

        // Price range
        if ($minPrice = $request->input('min_price')) {
            $query->where('base_price', '>=', (float) $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('base_price', '<=', (float) $maxPrice);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->latest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount('publishedProducts')->get();

        // Available materials for filter dropdown
        $materials = Product::publicCatalog()
            ->whereNotNull('material_type')
            ->distinct()
            ->pluck('material_type');

        return view('public.catalog', compact('products', 'categories', 'materials', 'sort'));
    }

    /**
     * Category filtered catalog
     */
    public function category(Category $category, Request $request)
    {
        $request->merge(['category' => $category->slug]);
        return $this->catalog($request);
    }

    /**
     * Product Details and Shade Selection
     */
    public function product(Product $product)
    {
        if ($product->status !== 'published' || !$product->is_public) {
            abort(404, 'Product not found or unavailable in the public catalog.');
        }

        $product->load(['category', 'publicShades']);

        $relatedProducts = Product::publicCatalog()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'publicShades'])
            ->take(3)
            ->get();

        $publicPricingEnabled = (bool) Setting::get('public_pricing_enabled', '1');
        $publicStockVisibility = Setting::get('public_stock_visibility', 'badge');

        return view('public.product-detail', compact(
            'product', 'relatedProducts', 'publicPricingEnabled', 'publicStockVisibility'
        ));
    }

    /**
     * Submit Product Enquiry or General Request
     */
    public function enquirySubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:100',
            'product_id' => 'nullable|exists:products,id',
            'product_shade_id' => 'nullable|exists:product_shades,id',
            'area_sqft' => 'nullable|numeric|min:1|max:100000',
            'preferred_contact' => 'required|in:whatsapp,call,email',
            'message' => 'nullable|string|max:2000',
        ]);

        $enquiryNumber = 'ENQ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        Enquiry::create([
            'enquiry_number' => $enquiryNumber,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'product_shade_id' => $validated['product_shade_id'] ?? null,
            'area_sqft' => $validated['area_sqft'] ?? null,
            'preferred_contact' => $validated['preferred_contact'],
            'message' => $validated['message'] ?? 'Customer requested a quotation/consultation.',
            'status' => 'new',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your enquiry has been received! Our flooring specialist will contact you shortly.',
                'reference' => $enquiryNumber,
            ]);
        }

        return back()->with('success', "Thank you, {$validated['name']}! Your enquiry (#{$enquiryNumber}) has been submitted. Our flooring specialist will reach out shortly.");
    }

    /**
     * Contact and Showroom Location
     */
    public function contact()
    {
        return view('public.contact');
    }

    /**
     * Privacy Policy
     */
    public function privacyPolicy()
    {
        return view('public.privacy-policy');
    }
}
