<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget("setting_{$key}");
        Cache::forget('theme_css_variables');
        Cache::forget('all_settings');
    }

    public static function getThemeCssVariables(): string
    {
        return Cache::rememberForever('theme_css_variables', function () {
            $font = static::get('theme_font', 'Plus Jakarta Sans');
            $primary = static::get('theme_primary', '#2EAA1F');
            $primaryDark = static::get('theme_primary_dark', '#1D7A13');
            $primaryLight = static::get('theme_primary_light', '#EDFBE8');
            $accent = static::get('theme_accent', '#EAB308');
            $bg = static::get('theme_bg', '#FFFFFF');
            $surface = static::get('theme_surface', '#FFFFFF');
            $border = static::get('theme_border', '#E5E7EB');
            $text = static::get('theme_text', '#0F172A');
            $textMuted = static::get('theme_text_muted', '#64748B');
            $radius = static::get('theme_radius', '0.75rem');

            return <<<CSS
:root {
    --font-sans: '{$font}', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --color-primary: {$primary};
    --color-primary-dark: {$primaryDark};
    --color-primary-light: {$primaryLight};
    --color-accent: {$accent};
    --color-bg: {$bg};
    --color-surface: {$surface};
    --color-border: {$border};
    --color-text: {$text};
    --color-text-muted: {$textMuted};
    --radius-base: {$radius};
}
CSS;
        });
    }

    public static function getPresets(): array
    {
        return [
            'kelly_vibrant' => [
                'name' => 'Kelly Green & Sun Yellow (Default)',
                'description' => 'Energetic Kelly green, radiant sun yellow accents, and clean white architectural space.',
                'values' => [
                    'theme_primary' => '#2EAA1F',
                    'theme_primary_dark' => '#1D7A13',
                    'theme_primary_light' => '#EDFBE8',
                    'theme_accent' => '#EAB308',
                    'theme_bg' => '#FFFFFF',
                    'theme_surface' => '#FFFFFF',
                    'theme_border' => '#E5E7EB',
                    'theme_font' => 'Plus Jakarta Sans',
                    'theme_radius' => '0.75rem',
                ],
            ],
            'citrus_lime' => [
                'name' => 'Citrus Lime & Bright Gold',
                'description' => 'Punchy botanical lime green paired with vivid sunny citrus accents.',
                'values' => [
                    'theme_primary' => '#65A30D',
                    'theme_primary_dark' => '#4D7C0F',
                    'theme_primary_light' => '#F7FEE7',
                    'theme_accent' => '#F59E0B',
                    'theme_bg' => '#FFFFFF',
                    'theme_surface' => '#FFFFFF',
                    'theme_border' => '#E5E7EB',
                    'theme_font' => 'Inter',
                    'theme_radius' => '0.75rem',
                ],
            ],
            'emerald_gold' => [
                'name' => 'Radiant Emerald & Honey Gold',
                'description' => 'Jewel-toned rich emerald green with warm golden amber accents.',
                'values' => [
                    'theme_primary' => '#059669',
                    'theme_primary_dark' => '#047857',
                    'theme_primary_light' => '#ECFDF5',
                    'theme_accent' => '#F59E0B',
                    'theme_bg' => '#FFFFFF',
                    'theme_surface' => '#FFFFFF',
                    'theme_border' => '#E5E7EB',
                    'theme_font' => 'Plus Jakarta Sans',
                    'theme_radius' => '0.75rem',
                ],
            ],
            'modern_azure' => [
                'name' => 'Electric Azure & Canary Yellow',
                'description' => 'Crisp oceanic cobalt blue paired with high-contrast electric canary yellow.',
                'values' => [
                    'theme_primary' => '#0284C7',
                    'theme_primary_dark' => '#0369A1',
                    'theme_primary_light' => '#F0F9FF',
                    'theme_accent' => '#FACC15',
                    'theme_bg' => '#FFFFFF',
                    'theme_surface' => '#FFFFFF',
                    'theme_border' => '#E2E8F0',
                    'theme_font' => 'Outfit',
                    'theme_radius' => '0.75rem',
                ],
            ],
            'royal_neon' => [
                'name' => 'Royal Violet & Lemon Flash',
                'description' => 'Deep royal violet paired with luminous lemon accents on pristine surfaces.',
                'values' => [
                    'theme_primary' => '#6366F1',
                    'theme_primary_dark' => '#4338CA',
                    'theme_primary_light' => '#EEF2FF',
                    'theme_accent' => '#EAB308',
                    'theme_bg' => '#FFFFFF',
                    'theme_surface' => '#FFFFFF',
                    'theme_border' => '#E2E8F0',
                    'theme_font' => 'Plus Jakarta Sans',
                    'theme_radius' => '0.75rem',
                ],
            ],
        ];
    }

    /**
     * Get optional product form field visibility configuration
     */
    public static function getProductFieldToggles(): array
    {
        $default = [
            'show_brand_collection' => true,
            'show_material_surface' => true,
            'show_dimensions_thickness' => true,
            'show_installation_warranty' => true,
            'show_coverage_per_box' => true,
            'show_description' => true,
            'show_seo_fields' => false,
        ];

        $stored = static::get('product_form_optional_fields');
        if (!$stored) {
            return $default;
        }

        $decoded = is_string($stored) ? json_decode($stored, true) : $stored;
        return is_array($decoded) ? array_merge($default, $decoded) : $default;
    }

    public static function isProductFieldVisible(string $fieldKey): bool
    {
        $toggles = static::getProductFieldToggles();
        return $toggles[$fieldKey] ?? true;
    }

    /**
     * Get invoice printing and layout configuration
     */
    public static function getInvoiceSettings(): array
    {
        return [
            'invoice_title' => static::get('invoice_title', 'TAX INVOICE'),
            'invoice_subtitle' => static::get('invoice_subtitle', 'Original for Recipient'),
            'invoice_terms' => static::get('invoice_terms', "1. Goods once inspected and delivered cannot be returned.\n2. Interest @ 18% p.a. will be levied on bills delayed past 15 days.\n3. Subject to local jurisdiction."),
            'invoice_notes' => static::get('invoice_notes', 'Thank you for choosing FloorOS for your premium architectural flooring project!'),
            'invoice_bank_name' => static::get('invoice_bank_name', 'HDFC Bank Ltd.'),
            'invoice_bank_account' => static::get('invoice_bank_account', '50200088991122'),
            'invoice_bank_ifsc' => static::get('invoice_bank_ifsc', 'HDFC0001234'),
            'invoice_bank_branch' => static::get('invoice_bank_branch', 'Commercial Main Branch'),
            'invoice_upi_id' => static::get('invoice_upi_id', 'flooros@upi'),
            'invoice_authorized_signatory' => static::get('invoice_authorized_signatory', 'For FloorOS Flooring Studio'),
            'invoice_declaration' => static::get('invoice_declaration', 'We declare that this invoice shows the actual price of the goods described and all particulars are true and correct.'),
        ];
    }
}
