<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HeroSlideController extends Controller
{
    /**
     * Display a listing of hero slides with drag-and-drop sort order.
     */
    public function index()
    {
        $slides = HeroSlide::ordered()->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.hero-slides.index', compact('slides', 'categories'));
    }

    /**
     * Store a newly created hero slide in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'desktop_banner' => 'nullable|string|max:1000',
            'desktop_banner_file' => 'nullable|image|max:10240', // 10MB max
            'tablet_banner' => 'nullable|string|max:1000',
            'tablet_banner_file' => 'nullable|image|max:10240',
            'mobile_banner' => 'nullable|string|max:1000',
            'mobile_banner_file' => 'nullable|image|max:10240',
            'link_destination' => 'nullable|string|max:500',
            'custom_link_url' => 'nullable|string|max:500',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        if (!file_exists(public_path('uploads/hero'))) {
            mkdir(public_path('uploads/hero'), 0777, true);
        }

        // Process Desktop Image (Required either via file or text path)
        $desktopPath = $request->input('desktop_banner');
        if ($request->hasFile('desktop_banner_file')) {
            $file = $request->file('desktop_banner_file');
            $filename = 'desktop_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hero'), $filename);
            $desktopPath = '/uploads/hero/' . $filename;
        }

        if (empty($desktopPath)) {
            return back()->withErrors(['desktop_banner' => 'Please provide a Desktop Banner image or upload a file.'])->withInput();
        }

        // Process Tablet Image (Optional)
        $tabletPath = $request->input('tablet_banner');
        if ($request->hasFile('tablet_banner_file')) {
            $file = $request->file('tablet_banner_file');
            $filename = 'tablet_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hero'), $filename);
            $tabletPath = '/uploads/hero/' . $filename;
        }

        // Process Mobile Image (Optional)
        $mobilePath = $request->input('mobile_banner');
        if ($request->hasFile('mobile_banner_file')) {
            $file = $request->file('mobile_banner_file');
            $filename = 'mobile_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hero'), $filename);
            $mobilePath = '/uploads/hero/' . $filename;
        }

        // Determine destination link
        $linkDestination = $request->input('link_destination', 'none');
        $finalUrl = null;
        if ($linkDestination === 'custom') {
            $finalUrl = $request->input('custom_link_url');
        } elseif ($linkDestination !== 'none' && !empty($linkDestination)) {
            $finalUrl = $linkDestination;
        }

        HeroSlide::create([
            'desktop_image' => $desktopPath,
            'tablet_image' => $tabletPath ?: null,
            'mobile_image' => $mobilePath ?: null,
            'link_type' => $linkDestination === 'none' ? 'none' : ($linkDestination === 'custom' ? 'custom' : 'preset'),
            'link_url' => $finalUrl,
            'alt_text' => $request->input('alt_text'),
            'sort_order' => (int) $request->input('sort_order', 1),
            'status' => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero banner slide added successfully!');
    }

    /**
     * Update the specified hero slide in storage.
     */
    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'desktop_banner' => 'nullable|string|max:1000',
            'desktop_banner_file' => 'nullable|image|max:10240',
            'tablet_banner' => 'nullable|string|max:1000',
            'tablet_banner_file' => 'nullable|image|max:10240',
            'mobile_banner' => 'nullable|string|max:1000',
            'mobile_banner_file' => 'nullable|image|max:10240',
            'link_destination' => 'nullable|string|max:500',
            'custom_link_url' => 'nullable|string|max:500',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        if (!file_exists(public_path('uploads/hero'))) {
            mkdir(public_path('uploads/hero'), 0777, true);
        }

        // Process Desktop Image
        $desktopPath = $request->input('desktop_banner', $heroSlide->desktop_image);
        if ($request->hasFile('desktop_banner_file')) {
            $file = $request->file('desktop_banner_file');
            $filename = 'desktop_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hero'), $filename);
            $desktopPath = '/uploads/hero/' . $filename;
        }

        // Process Tablet Image
        $tabletPath = $request->input('tablet_banner', $heroSlide->tablet_image);
        if ($request->hasFile('tablet_banner_file')) {
            $file = $request->file('tablet_banner_file');
            $filename = 'tablet_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hero'), $filename);
            $tabletPath = '/uploads/hero/' . $filename;
        }

        // Process Mobile Image
        $mobilePath = $request->input('mobile_banner', $heroSlide->mobile_image);
        if ($request->hasFile('mobile_banner_file')) {
            $file = $request->file('mobile_banner_file');
            $filename = 'mobile_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hero'), $filename);
            $mobilePath = '/uploads/hero/' . $filename;
        }

        // Determine destination link
        $linkDestination = $request->input('link_destination', 'none');
        $finalUrl = null;
        if ($linkDestination === 'custom') {
            $finalUrl = $request->input('custom_link_url');
        } elseif ($linkDestination !== 'none' && !empty($linkDestination)) {
            $finalUrl = $linkDestination;
        }

        $heroSlide->update([
            'desktop_image' => $desktopPath,
            'tablet_image' => $tabletPath ?: null,
            'mobile_image' => $mobilePath ?: null,
            'link_type' => $linkDestination === 'none' ? 'none' : ($linkDestination === 'custom' ? 'custom' : 'preset'),
            'link_url' => $finalUrl,
            'alt_text' => $request->input('alt_text'),
            'sort_order' => (int) $request->input('sort_order', 1),
            'status' => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero banner slide updated successfully!');
    }

    /**
     * Remove the specified hero slide from storage.
     */
    public function destroy(Request $request, HeroSlide $heroSlide)
    {
        foreach (['desktop_image', 'tablet_image', 'mobile_image'] as $field) {
            $path = $heroSlide->$field;
            if ($path && str_starts_with($path, '/uploads/hero/')) {
                $file = public_path(ltrim($path, '/'));
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }

        $heroSlide->delete();

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hero banner slide deleted successfully!',
            ]);
        }

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero banner slide deleted successfully!');
    }

    /**
     * Update sort sequence order from drag-and-drop AJAX request.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:hero_slides,id',
        ]);

        foreach ($request->input('order') as $index => $id) {
            HeroSlide::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Slide sequence reordered successfully!',
        ]);
    }
}
