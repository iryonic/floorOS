<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Appearance & Dynamic CSS Variables
            ['key' => 'theme_font', 'value' => 'Plus Jakarta Sans', 'group' => 'appearance'],
            ['key' => 'theme_primary', 'value' => '#176B52', 'group' => 'appearance'], // Forest Green
            ['key' => 'theme_primary_dark', 'value' => '#124B3C', 'group' => 'appearance'],
            ['key' => 'theme_primary_light', 'value' => '#E8F3ED', 'group' => 'appearance'],
            ['key' => 'theme_accent', 'value' => '#D4AF37', 'group' => 'appearance'], // Warm Gold / Brass
            ['key' => 'theme_bg', 'value' => '#F7F8F6', 'group' => 'appearance'],
            ['key' => 'theme_surface', 'value' => '#FFFFFF', 'group' => 'appearance'],
            ['key' => 'theme_border', 'value' => '#E3E8E4', 'group' => 'appearance'],
            ['key' => 'theme_text', 'value' => '#202925', 'group' => 'appearance'],
            ['key' => 'theme_text_muted', 'value' => '#6B756F', 'group' => 'appearance'],
            ['key' => 'theme_radius', 'value' => '0.5rem', 'group' => 'appearance'], // border-radius

            // Store Info
            ['key' => 'store_name', 'value' => 'FloorOS Gallery & Atelier', 'group' => 'store'],
            ['key' => 'store_tagline', 'value' => 'Engineered Hardwood, SPC & Architectural Surfaces', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '+91 98450 12345', 'group' => 'store'],
            ['key' => 'store_email', 'value' => 'hello@flooros.gallery', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Building 48, Timber & Stone Design District, 100ft Road, Indiranagar, Bengaluru, Karnataka 560038', 'group' => 'store'],
            ['key' => 'store_gstin', 'value' => '29AAACH7409R1ZV', 'group' => 'store'],
            ['key' => 'store_hours', 'value' => 'Monday - Saturday: 10:00 AM - 8:00 PM | Sunday: By Appointment', 'group' => 'store'],
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'store'],
            ['key' => 'currency_code', 'value' => 'INR', 'group' => 'store'],

            // Public Catalog Settings
            ['key' => 'public_pricing_enabled', 'value' => '1', 'group' => 'catalog'],
            ['key' => 'public_stock_visibility', 'value' => 'badge', 'group' => 'catalog'], // badge, exact, none
            ['key' => 'hero_eyebrow', 'value' => 'FLOORING FOR BETTER SPACES', 'group' => 'catalog'],
            ['key' => 'hero_title', 'value' => 'Beautiful Floors. Better Spaces.', 'group' => 'catalog'],
            ['key' => 'hero_subtitle', 'value' => 'Explore authentic hardwood textures, click-lock SPC, and luxury acoustic surfaces curated for discerning homes and commercial spaces.', 'group' => 'catalog'],
            ['key' => 'contact_whatsapp', 'value' => '+919845012345', 'group' => 'catalog'],

            // Inventory & Financial Rules
            ['key' => 'tax_gst_enabled', 'value' => '1', 'group' => 'accounting'],
            ['key' => 'default_gst_rate', 'value' => '18.00', 'group' => 'accounting'],
            ['key' => 'invoice_prefix', 'value' => 'INV', 'group' => 'accounting'],
            ['key' => 'quotation_prefix', 'value' => 'EST', 'group' => 'accounting'],
            ['key' => 'purchase_prefix', 'value' => 'PUR', 'group' => 'accounting'],
            ['key' => 'default_reorder_threshold', 'value' => '100', 'group' => 'inventory'],
            ['key' => 'allow_negative_stock', 'value' => '0', 'group' => 'inventory'],
        ];

        foreach ($settings as $setting) {
            Setting::set($setting['key'], $setting['value'], $setting['group']);
        }
    }
}
