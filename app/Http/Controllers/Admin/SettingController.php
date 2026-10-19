<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $presets = Setting::getPresets();
        $productFieldToggles = Setting::getProductFieldToggles();
        return view('admin.settings.index', compact('settings', 'presets', 'productFieldToggles'));
    }

    public function update(Request $request)
    {
        // Handle optional product form field visibility configuration
        if ($request->has('product_field_toggles_submitted')) {
            $toggles = [
                'show_brand_collection' => $request->boolean('toggle_show_brand_collection'),
                'show_material_surface' => $request->boolean('toggle_show_material_surface'),
                'show_dimensions_thickness' => $request->boolean('toggle_show_dimensions_thickness'),
                'show_installation_warranty' => $request->boolean('toggle_show_installation_warranty'),
                'show_coverage_per_box' => $request->boolean('toggle_show_coverage_per_box'),
                'show_description' => $request->boolean('toggle_show_description'),
                'show_seo_fields' => $request->boolean('toggle_show_seo_fields'),
            ];
            Setting::set('product_form_optional_fields', json_encode($toggles), 'catalog');
        }

        $data = $request->except(['_token', '_method', 'product_field_toggles_submitted', 'toggle_show_brand_collection', 'toggle_show_material_surface', 'toggle_show_dimensions_thickness', 'toggle_show_installation_warranty', 'toggle_show_coverage_per_box', 'toggle_show_description', 'toggle_show_seo_fields']);

        foreach ($data as $key => $value) {
            $group = 'general';
            if (str_starts_with($key, 'theme_')) {
                $group = 'appearance';
            } elseif (str_starts_with($key, 'store_')) {
                $group = 'store';
            } elseif (str_starts_with($key, 'hero_') || str_starts_with($key, 'public_') || str_starts_with($key, 'contact_')) {
                $group = 'catalog';
            } elseif (str_starts_with($key, 'tax_') || str_starts_with($key, 'default_gst_') || str_ends_with($key, '_prefix')) {
                $group = 'accounting';
            } elseif (str_starts_with($key, 'session_') || str_contains($key, 'login_')) {
                $group = 'security';
            } elseif (str_starts_with($key, 'pwa_')) {
                $group = 'pwa';
            }

            Setting::set($key, (string) $value, $group);
        }

        // Clear view and theme caches
        Cache::forget('theme_css_variables');
        Cache::flush();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings, UI variables, and product field preferences updated successfully!');
    }
}
