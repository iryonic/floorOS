@extends('layouts.admin')

@section('title', 'Business Settings & UI Appearance Variables')
@section('page_title', 'System Settings & Appearance')

@section('content')
<div class="w-full max-w-7xl 2xl:max-w-[1700px] mx-auto space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">System Configuration & UI Styling</h1>
            <p class="text-xs text-[#6B756F] mt-0.5">Control dynamic theme tokens, invoice layout details, product fields visibility, and business rules.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="eye" class="w-3.5 h-3.5 text-brand"></i> Preview Storefront
            </a>
            <button type="button" onclick="document.getElementById('settingsMainForm').submit()" class="btn-brand text-xs py-2 px-4 shadow-sm">
                <i data-lucide="save" class="w-3.5 h-3.5"></i> Save All Settings
            </button>
        </div>
    </div>

    <!-- Tab Navigation Bar -->
    <div class="border-b border-[#E3E8E4] bg-white rounded-t-xl px-2 pt-2 shadow-2xs">
        <nav class="flex space-x-1 sm:space-x-3 overflow-x-auto no-scrollbar text-xs font-semibold" aria-label="Settings Tabs">
            <button type="button" onclick="switchSettingsTab('theme')" id="tabBtn-theme" class="tab-nav-btn py-3 px-4 border-b-2 border-brand text-brand flex items-center gap-2 transition whitespace-nowrap">
                <i data-lucide="palette" class="w-4 h-4"></i> Theme & Appearance
            </button>
            <button type="button" onclick="switchSettingsTab('store')" id="tabBtn-store" class="tab-nav-btn py-3 px-4 border-b-2 border-transparent text-[#6B756F] hover:text-[#202925] flex items-center gap-2 transition whitespace-nowrap">
                <i data-lucide="store" class="w-4 h-4"></i> Store Profile & Legal
            </button>
            <button type="button" onclick="switchSettingsTab('invoice')" id="tabBtn-invoice" class="tab-nav-btn py-3 px-4 border-b-2 border-transparent text-[#6B756F] hover:text-[#202925] flex items-center gap-2 transition whitespace-nowrap">
                <i data-lucide="receipt" class="w-4 h-4"></i> Invoice & Print Layout
            </button>
            <button type="button" onclick="switchSettingsTab('products')" id="tabBtn-products" class="tab-nav-btn py-3 px-4 border-b-2 border-transparent text-[#6B756F] hover:text-[#202925] flex items-center gap-2 transition whitespace-nowrap">
                <i data-lucide="layers" class="w-4 h-4"></i> Product Form Fields
            </button>
            <button type="button" onclick="switchSettingsTab('operations')" id="tabBtn-operations" class="tab-nav-btn py-3 px-4 border-b-2 border-transparent text-[#6B756F] hover:text-[#202925] flex items-center gap-2 transition whitespace-nowrap">
                <i data-lucide="settings" class="w-4 h-4"></i> Operating & Tax Rules
            </button>
            <button type="button" onclick="switchSettingsTab('pwa')" id="tabBtn-pwa" class="tab-nav-btn py-3 px-4 border-b-2 border-transparent text-[#6B756F] hover:text-[#202925] flex items-center gap-2 transition whitespace-nowrap">
                <i data-lucide="smartphone" class="w-4 h-4"></i> PWA App & Mobile Install
            </button>
        </nav>
    </div>

    <!-- Main Settings Form -->
    <form id="settingsMainForm" action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- ========================================== -->
        <!-- TAB 1: Theme & Appearance                  -->
        <!-- ========================================== -->
        <div id="tabContent-theme" class="tab-pane space-y-6">
            <div class="card-surface p-6 sm:p-8 space-y-6 bg-white">
                <div class="border-b border-[#E3E8E4] pb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#202925] flex items-center gap-2">
                            <i data-lucide="palette" class="w-5 h-5 text-brand"></i>
                            Dynamic Theme & Appearance Engine
                        </h2>
                        <p class="text-xs text-[#6B756F] mt-0.5">Choose from pre-designed high-contrast color palettes or fine-tune individual CSS variables.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-brand-light text-brand uppercase tracking-wider">
                        1-Click Realtime Variables
                    </span>
                </div>

                <!-- Ready-Made Theme Templates -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#202925] flex items-center gap-1.5">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-brand"></i>
                            Curated Theme Templates
                        </span>
                        <span class="text-[11px] text-[#6B756F]">Select any template to instantly populate the color tokens below</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3.5">
                        @foreach($presets as $key => $preset)
                            <div id="presetCard-{{ $key }}" onclick="applyThemePreset('{{ $key }}')" class="preset-card cursor-pointer p-4 rounded-xl border border-[#E3E8E4] hover:border-brand hover:shadow-md transition bg-white space-y-3 group">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-6 h-6 rounded-full border border-white shadow-2xs" style="background-color: {{ $preset['values']['theme_primary'] }};"></span>
                                    <span class="w-6 h-6 rounded-full border border-white shadow-2xs" style="background-color: {{ $preset['values']['theme_primary_dark'] }};"></span>
                                    <span class="w-6 h-6 rounded-full border border-slate-200 shadow-2xs" style="background-color: {{ $preset['values']['theme_primary_light'] }};"></span>
                                    <span class="w-6 h-6 rounded-full border border-white shadow-2xs" style="background-color: {{ $preset['values']['theme_accent'] }};"></span>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-[#202925] group-hover:text-brand transition">{{ $preset['name'] }}</h4>
                                    <p class="text-[10px] text-[#6B756F] mt-0.5 line-clamp-2 leading-tight">{{ $preset['description'] }}</p>
                                </div>

                                <button type="button" class="w-full py-1.5 text-[11px] font-bold rounded-lg bg-white border border-slate-200 text-slate-700 group-hover:bg-brand group-hover:text-white group-hover:border-transparent transition">
                                    Apply Theme
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-[#E3E8E4]">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#202925] block mb-3">
                        Custom Color Palette Fine-Tuning
                    </span>
                </div>

                <!-- Color Palette -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Primary Brand Color -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-[#202925]">Primary Brand Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="theme_primary" value="{{ $settings['theme_primary'] ?? '#2EAA1F' }}" oninput="updateColorHex(this, 'theme_primary_hex')" class="w-9 h-9 rounded border border-slate-200 cursor-pointer p-0.5 bg-white">
                            <input type="text" id="theme_primary_hex" value="{{ $settings['theme_primary'] ?? '#2EAA1F' }}" oninput="updateColorPicker(this, 'theme_primary')" class="w-full input-custom text-xs font-mono">
                        </div>
                        <span class="text-[10px] text-[#6B756F]">Buttons, active tabs, brand accents</span>
                    </div>

                    <!-- Dark Primary -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-[#202925]">Dark Primary Shade</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="theme_primary_dark" value="{{ $settings['theme_primary_dark'] ?? '#1D7A13' }}" oninput="updateColorHex(this, 'theme_primary_dark_hex')" class="w-9 h-9 rounded border border-slate-200 cursor-pointer p-0.5 bg-white">
                            <input type="text" id="theme_primary_dark_hex" value="{{ $settings['theme_primary_dark'] ?? '#1D7A13' }}" oninput="updateColorPicker(this, 'theme_primary_dark')" class="w-full input-custom text-xs font-mono">
                        </div>
                        <span class="text-[10px] text-[#6B756F]">Hover states, header accents</span>
                    </div>

                    <!-- Light Primary Tint -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-[#202925]">Light Primary Tint</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="theme_primary_light" value="{{ $settings['theme_primary_light'] ?? '#EDFBE8' }}" oninput="updateColorHex(this, 'theme_primary_light_hex')" class="w-9 h-9 rounded border border-slate-200 cursor-pointer p-0.5 bg-white">
                            <input type="text" id="theme_primary_light_hex" value="{{ $settings['theme_primary_light'] ?? '#EDFBE8' }}" oninput="updateColorPicker(this, 'theme_primary_light')" class="w-full input-custom text-xs font-mono">
                        </div>
                        <span class="text-[10px] text-[#6B756F]">Pills, badge backgrounds, selected states</span>
                    </div>

                    <!-- Accent Color (Sun Yellow) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-[#202925]">Accent Highlight Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="theme_accent" value="{{ $settings['theme_accent'] ?? '#EAB308' }}" oninput="updateColorHex(this, 'theme_accent_hex')" class="w-9 h-9 rounded border border-slate-200 cursor-pointer p-0.5 bg-white">
                            <input type="text" id="theme_accent_hex" value="{{ $settings['theme_accent'] ?? '#EAB308' }}" oninput="updateColorPicker(this, 'theme_accent')" class="w-full input-custom text-xs font-mono">
                        </div>
                        <span class="text-[10px] text-[#6B756F]">Special badges, attention highlights</span>
                    </div>
                </div>

                <!-- Typography & Radius -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 pt-4 border-t border-[#E3E8E4]">
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Typography Font Family</label>
                        <select name="theme_font" class="w-full input-custom text-sm">
                            <option value="Plus Jakarta Sans" {{ ($settings['theme_font'] ?? '') === 'Plus Jakarta Sans' ? 'selected' : '' }}>Plus Jakarta Sans (Modern Clean)</option>
                            <option value="Inter" {{ ($settings['theme_font'] ?? '') === 'Inter' ? 'selected' : '' }}>Inter (Structured Swiss Style)</option>
                            <option value="Outfit" {{ ($settings['theme_font'] ?? '') === 'Outfit' ? 'selected' : '' }}>Outfit (Contemporary Display)</option>
                            <option value="DM Sans" {{ ($settings['theme_font'] ?? '') === 'DM Sans' ? 'selected' : '' }}>DM Sans (Friendly Geometric)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Base Corner Radius</label>
                        <select name="theme_radius" class="w-full input-custom text-sm">
                            <option value="0.5rem" {{ ($settings['theme_radius'] ?? '') === '0.5rem' ? 'selected' : '' }}>0.5rem (Subtle Rounded)</option>
                            <option value="0.75rem" {{ ($settings['theme_radius'] ?? '0.75rem') === '0.75rem' ? 'selected' : '' }}>0.75rem (Modern Balanced)</option>
                            <option value="1rem" {{ ($settings['theme_radius'] ?? '') === '1rem' ? 'selected' : '' }}>1.0rem (Soft Organic)</option>
                            <option value="1.5rem" {{ ($settings['theme_radius'] ?? '') === '1.5rem' ? 'selected' : '' }}>1.5rem (Curved App-Style)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: Store Profile & Legal Details       -->
        <!-- ========================================== -->
        <div id="tabContent-store" class="tab-pane hidden space-y-6">
            <div class="card-surface p-6 sm:p-8 space-y-6 bg-white">
                <div class="border-b border-[#E3E8E4] pb-4">
                    <h2 class="text-base font-bold text-[#202925] flex items-center gap-2">
                        <i data-lucide="store" class="w-5 h-5 text-brand"></i>
                        Store Profile & Business Details
                    </h2>
                    <p class="text-xs text-[#6B756F] mt-0.5">Information displayed on official invoices, quotations, customer communications, and public website.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Store / Business Name</label>
                        <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'FloorOS Gallery & Atelier' }}" required class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Store Tagline</label>
                        <input type="text" name="store_tagline" value="{{ $settings['store_tagline'] ?? 'Engineered Hardwood, SPC & Surfaces' }}" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Primary Phone</label>
                        <input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '+91 98450 12345' }}" required class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Store Email</label>
                        <input type="email" name="store_email" value="{{ $settings['store_email'] ?? 'hello@flooros.gallery' }}" required class="w-full input-custom text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Showroom Address</label>
                        <input type="text" name="store_address" value="{{ $settings['store_address'] ?? 'Building 48, Timber District, 100ft Road, Indiranagar, Bengaluru' }}" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">GSTIN Number</label>
                        <input type="text" name="store_gstin" value="{{ $settings['store_gstin'] ?? '29AAACH7409R1ZV' }}" class="w-full input-custom text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">PAN Number</label>
                        <input type="text" name="store_pan" value="{{ $settings['store_pan'] ?? 'AAACH7409R' }}" class="w-full input-custom text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '₹' }}" class="w-full input-custom text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: Invoice & Print Customization       -->
        <!-- ========================================== -->
        <div id="tabContent-invoice" class="tab-pane hidden space-y-6">
            <div class="card-surface p-6 sm:p-8 space-y-6 bg-white">
                <div class="border-b border-[#E3E8E4] pb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#202925] flex items-center gap-2">
                            <i data-lucide="receipt" class="w-5 h-5 text-brand"></i>
                            Invoice & Print Layout Customization
                        </h2>
                        <p class="text-xs text-[#6B756F] mt-0.5">Control the exact text, title, terms, bank details, and signatory printed on customer tax invoices.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-brand-light text-brand uppercase tracking-wider">
                        Print Template Engine
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Invoice Header Title</label>
                        <input type="text" name="invoice_title" value="{{ $settings['invoice_title'] ?? 'TAX INVOICE' }}" class="w-full input-custom text-sm font-bold">
                        <span class="text-[10px] text-[#6B756F]">E.g. TAX INVOICE, BILL OF SUPPLY</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Invoice Subtitle</label>
                        <input type="text" name="invoice_subtitle" value="{{ $settings['invoice_subtitle'] ?? 'Original for Recipient' }}" class="w-full input-custom text-sm">
                        <span class="text-[10px] text-[#6B756F]">E.g. Original for Recipient / Triplicate Copy</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Authorized Signatory Label</label>
                        <input type="text" name="invoice_authorized_signatory" value="{{ $settings['invoice_authorized_signatory'] ?? 'For FloorOS Flooring Studio' }}" class="w-full input-custom text-sm font-medium">
                        <span class="text-[10px] text-[#6B756F]">Company signature block title</span>
                    </div>
                </div>

                <!-- Bank & UPI Payment Details -->
                <div class="pt-4 border-t border-[#E3E8E4] space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#202925] flex items-center gap-1.5">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-brand"></i>
                        Bank & Digital Payment Information Printed on Invoice
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">Bank Name</label>
                            <input type="text" name="invoice_bank_name" value="{{ $settings['invoice_bank_name'] ?? 'HDFC Bank Ltd.' }}" class="w-full input-custom text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">Account Number</label>
                            <input type="text" name="invoice_bank_account" value="{{ $settings['invoice_bank_account'] ?? '50200088991122' }}" class="w-full input-custom text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">IFSC Code</label>
                            <input type="text" name="invoice_bank_ifsc" value="{{ $settings['invoice_bank_ifsc'] ?? 'HDFC0001234' }}" class="w-full input-custom text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">UPI ID for Direct QR</label>
                            <input type="text" name="invoice_upi_id" value="{{ $settings['invoice_upi_id'] ?? 'flooros@upi' }}" class="w-full input-custom text-sm font-mono">
                        </div>
                    </div>
                </div>

                <!-- Terms, Notes & Declaration -->
                <div class="pt-4 border-t border-[#E3E8E4] grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Invoice Terms & Conditions</label>
                        <textarea name="invoice_terms" rows="4" class="w-full input-custom text-xs font-mono">{{ $settings['invoice_terms'] ?? "1. Goods once inspected and delivered cannot be returned.\n2. Interest @ 18% p.a. will be levied on bills delayed past 15 days.\n3. Subject to local jurisdiction." }}</textarea>
                        <span class="text-[10px] text-[#6B756F]">One term per line. Printed on every customer invoice copy.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Customer Thank-You Note</label>
                        <textarea name="invoice_notes" rows="4" class="w-full input-custom text-xs">{{ $settings['invoice_notes'] ?? 'Thank you for choosing FloorOS for your premium architectural flooring project!' }}</textarea>
                        <span class="text-[10px] text-[#6B756F]">Brief footer appreciation note for customers.</span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Statutory Invoice Declaration</label>
                        <input type="text" name="invoice_declaration" value="{{ $settings['invoice_declaration'] ?? 'We declare that this invoice shows the actual price of the goods described and all particulars are true and correct.' }}" class="w-full input-custom text-xs">
                        <span class="text-[10px] text-[#6B756F]">GST statutory declaration line at the bottom of the invoice.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: Product Form Fields Configuration   -->
        <!-- ========================================== -->
        <div id="tabContent-products" class="tab-pane hidden space-y-6">
            <div class="card-surface p-6 sm:p-8 space-y-6 bg-white">
                <div class="border-b border-[#E3E8E4] pb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#202925] flex items-center gap-2">
                            <i data-lucide="layers" class="w-5 h-5 text-brand"></i>
                            Product Creation Optional Fields Visibility
                        </h2>
                        <p class="text-xs text-[#6B756F] mt-0.5">Streamline the product creation workflow. Toggle the display of optional and advanced specification fields.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-amber-50 text-amber-700 uppercase tracking-wider">
                        Workflow Optimization
                    </span>
                </div>

                <input type="hidden" name="product_field_toggles_submitted" value="1">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Toggle: Brand & Collection -->
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand hover:bg-slate-50/50 cursor-pointer transition">
                        <input type="checkbox" name="toggle_show_brand_collection" value="1" {{ ($productFieldToggles['show_brand_collection'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-brand rounded border-slate-300 focus:ring-brand">
                        <div>
                            <span class="block text-xs font-bold text-[#202925]">Brand & Collection Name</span>
                            <span class="block text-[11px] text-[#6B756F] mt-0.5">Manufacturer label and collection model series</span>
                        </div>
                    </label>

                    <!-- Toggle: Material & Surface -->
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand hover:bg-slate-50/50 cursor-pointer transition">
                        <input type="checkbox" name="toggle_show_material_surface" value="1" {{ ($productFieldToggles['show_material_surface'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-brand rounded border-slate-300 focus:ring-brand">
                        <div>
                            <span class="block text-xs font-bold text-[#202925]">Material & Surface Finish</span>
                            <span class="block text-[11px] text-[#6B756F] mt-0.5">Core composition (Oak, SPC) and texture finish</span>
                        </div>
                    </label>

                    <!-- Toggle: Thickness & Dimensions -->
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand hover:bg-slate-50/50 cursor-pointer transition">
                        <input type="checkbox" name="toggle_show_dimensions_thickness" value="1" {{ ($productFieldToggles['show_dimensions_thickness'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-brand rounded border-slate-300 focus:ring-brand">
                        <div>
                            <span class="block text-xs font-bold text-[#202925]">Plank Thickness & Dimensions</span>
                            <span class="block text-[11px] text-[#6B756F] mt-0.5">Plank thickness (mm), wear layer, length x width</span>
                        </div>
                    </label>

                    <!-- Toggle: Installation & Warranty -->
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand hover:bg-slate-50/50 cursor-pointer transition">
                        <input type="checkbox" name="toggle_show_installation_warranty" value="1" {{ ($productFieldToggles['show_installation_warranty'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-brand rounded border-slate-300 focus:ring-brand">
                        <div>
                            <span class="block text-xs font-bold text-[#202925]">Installation & Warranty</span>
                            <span class="block text-[11px] text-[#6B756F] mt-0.5">Click-lock / glue-down type and warranty years</span>
                        </div>
                    </label>

                    <!-- Toggle: Coverage Per Box -->
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand hover:bg-slate-50/50 cursor-pointer transition">
                        <input type="checkbox" name="toggle_show_coverage_per_box" value="1" {{ ($productFieldToggles['show_coverage_per_box'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-brand rounded border-slate-300 focus:ring-brand">
                        <div>
                            <span class="block text-xs font-bold text-[#202925]">Coverage Per Box & Applications</span>
                            <span class="block text-[11px] text-[#6B756F] mt-0.5">Sq ft coverage per box and suitable room tags</span>
                        </div>
                    </label>

                    <!-- Toggle: Detailed Description -->
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand hover:bg-slate-50/50 cursor-pointer transition">
                        <input type="checkbox" name="toggle_show_description" value="1" {{ ($productFieldToggles['show_description'] ?? true) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-brand rounded border-slate-300 focus:ring-brand">
                        <div>
                            <span class="block text-xs font-bold text-[#202925]">Long Public Description</span>
                            <span class="block text-[11px] text-[#6B756F] mt-0.5">Architectural marketing overview and features</span>
                        </div>
                    </label>
                </div>

                <div class="p-3 rounded-lg bg-emerald-50/80 border border-emerald-200 text-[11px] text-emerald-800 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-brand shrink-0"></i>
                    <span>Essential fields (Product Name, Category, Base Starting Price, Unit of Measurement, and Shades/Variants) always remain visible and mandatory to protect product catalog data integrity.</span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 5: Operational & Tax Rules             -->
        <!-- ========================================== -->
        <div id="tabContent-operations" class="tab-pane hidden space-y-6">
            <div class="card-surface p-6 sm:p-8 space-y-6 bg-white">
                <div class="border-b border-[#E3E8E4] pb-4">
                    <h2 class="text-base font-bold text-[#202925] flex items-center gap-2">
                        <i data-lucide="calculator" class="w-5 h-5 text-brand"></i>
                        Accounting, Taxes & Operational Rules
                    </h2>
                    <p class="text-xs text-[#6B756F] mt-0.5">Control taxation percentages, inventory thresholds, document numbering prefixes, and public pricing rules.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Default GST Rate (%)</label>
                        <input type="number" step="0.01" name="default_gst_rate" value="{{ $settings['default_gst_rate'] ?? '18.00' }}" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Default Reorder Threshold</label>
                        <input type="number" name="default_reorder_threshold" value="{{ $settings['default_reorder_threshold'] ?? '100' }}" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Invoice Number Prefix</label>
                        <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV' }}" class="w-full input-custom text-sm font-mono">
                    </div>
                </div>

                <div class="pt-4 border-t border-[#E3E8E4] grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Public Pricing Visibility</label>
                        <select name="public_pricing_enabled" class="w-full input-custom text-sm">
                            <option value="1" {{ ($settings['public_pricing_enabled'] ?? '1') === '1' ? 'selected' : '' }}>Visible (Show selling price per sq ft to public)</option>
                            <option value="0" {{ ($settings['public_pricing_enabled'] ?? '1') === '0' ? 'selected' : '' }}>Hidden (Display "Price on Enquiry" only)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Public Stock Availability Mode</label>
                        <select name="public_stock_visibility" class="w-full input-custom text-sm">
                            <option value="badge" {{ ($settings['public_stock_visibility'] ?? 'badge') === 'badge' ? 'selected' : '' }}>Clean Badge ("In Stock" / "Limited Availability")</option>
                            <option value="exact" {{ ($settings['public_stock_visibility'] ?? '') === 'exact' ? 'selected' : '' }}>Exact Quantity (Shows actual available sq ft)</option>
                            <option value="none" {{ ($settings['public_stock_visibility'] ?? '') === 'none' ? 'selected' : '' }}>Hidden Completely</option>
                        </select>
                    </div>
                </div>

                <!-- Industry-Standard Authentication & Admin-Manageable Session Lifetime -->
                <div class="pt-6 border-t border-[#E3E8E4] space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-[#202925] flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4 text-brand"></i>
                            Staff Authentication, Security & Session Timeout
                        </h3>
                        <p class="text-xs text-[#6B756F] mt-0.5">Control staff login rate-limiting and administrative inactivity session timeout (configurable up to 1 week long).</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">Session Inactivity Timeout</label>
                            <select name="session_lifetime" class="w-full input-custom text-sm">
                                <option value="120" {{ ($settings['session_lifetime'] ?? '10080') == '120' ? 'selected' : '' }}>2 Hours (Standard)</option>
                                <option value="480" {{ ($settings['session_lifetime'] ?? '') == '480' ? 'selected' : '' }}>8 Hours (Full Workday)</option>
                                <option value="1440" {{ ($settings['session_lifetime'] ?? '') == '1440' ? 'selected' : '' }}>24 Hours (1 Day)</option>
                                <option value="4320" {{ ($settings['session_lifetime'] ?? '') == '4320' ? 'selected' : '' }}>3 Days</option>
                                <option value="10080" {{ ($settings['session_lifetime'] ?? '10080') == '10080' ? 'selected' : '' }}>7 Days / 1 Week (Long Stay)</option>
                            </select>
                            <span class="text-[10px] text-slate-400 mt-1 block">Staff remains logged in up to the chosen duration of inactivity.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">Session Termination on Browser Close</label>
                            <select name="session_expire_on_close" class="w-full input-custom text-sm">
                                <option value="0" {{ ($settings['session_expire_on_close'] ?? '0') === '0' ? 'selected' : '' }}>Keep session active across browser restarts (Recommended)</option>
                                <option value="1" {{ ($settings['session_expire_on_close'] ?? '') === '1' ? 'selected' : '' }}>Expire immediately when browser is closed</option>
                            </select>
                            <span class="text-[10px] text-slate-400 mt-1 block">Whether closing the browser terminates the staff session.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">Max Login Attempts (Brute Force Protection)</label>
                            <select name="max_login_attempts" class="w-full input-custom text-sm">
                                <option value="5" {{ ($settings['max_login_attempts'] ?? '5') == '5' ? 'selected' : '' }}>5 Attempts (Industry Standard)</option>
                                <option value="10" {{ ($settings['max_login_attempts'] ?? '') == '10' ? 'selected' : '' }}>10 Attempts (Relaxed)</option>
                                <option value="0" {{ ($settings['max_login_attempts'] ?? '') == '0' ? 'selected' : '' }}>Disabled (No lockout)</option>
                            </select>
                            <span class="text-[10px] text-slate-400 mt-1 block">Temporarily locks IP/Email on consecutive failed attempts.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 6: PWA App & Mobile Install           -->
        <!-- ========================================== -->
        <div id="tabContent-pwa" class="tab-pane hidden space-y-6">
            <div class="card-surface p-6 sm:p-8 space-y-6 bg-white">
                <div class="border-b border-[#E3E8E4] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-[#202925] flex items-center gap-2">
                            <i data-lucide="smartphone" class="w-5 h-5 text-brand"></i>
                            Progressive Web App (PWA) & Mobile Installation
                        </h2>
                        <p class="text-xs text-[#6B756F] mt-0.5">Configure how FloorOS installs on mobile phones, tablets, and desktops as a native standalone app.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        PWA Active & Installable
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Left Form Column -->
                    <div class="lg:col-span-8 space-y-5">
                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">
                                Full Application Name (Splash Screen & App Store Title) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="pwa_name" 
                                   id="pwaNameInput"
                                   value="{{ $settings['pwa_name'] ?? 'FloorOS — Flooring Gallery & Atelier' }}" 
                                   oninput="updatePwaPreview()"
                                   required 
                                   class="w-full input-custom text-sm font-medium">
                            <span class="text-[10px] text-slate-400 mt-1 block">The primary application title shown when installing and on splash screens.</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#202925] mb-1">
                                    Short App Name (Home Screen & Launcher) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       name="pwa_short_name" 
                                       id="pwaShortNameInput"
                                       value="{{ $settings['pwa_short_name'] ?? 'FloorOS' }}" 
                                       oninput="updatePwaPreview()"
                                       maxlength="15"
                                       required 
                                       class="w-full input-custom text-sm font-bold font-mono">
                                <span class="text-[10px] text-slate-400 mt-1 block">Displayed directly beneath the app icon on mobile phone home screens (max 12-15 chars).</span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#202925] mb-1">Display Mode</label>
                                <select name="pwa_display" class="w-full input-custom text-sm font-medium">
                                    <option value="standalone" {{ ($settings['pwa_display'] ?? 'standalone') === 'standalone' ? 'selected' : '' }}>Standalone (Native App Feel, No Browser Bar)</option>
                                    <option value="minimal-ui" {{ ($settings['pwa_display'] ?? '') === 'minimal-ui' ? 'selected' : '' }}>Minimal UI (Subtle Back/Refresh Controls)</option>
                                    <option value="fullscreen" {{ ($settings['pwa_display'] ?? '') === 'fullscreen' ? 'selected' : '' }}>Fullscreen (Immersive Kiosk Mode)</option>
                                </select>
                                <span class="text-[10px] text-slate-400 mt-1 block">Standalone hides the browser address bar for a native app feel.</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">PWA Description</label>
                            <textarea name="pwa_description" rows="2" class="w-full input-custom text-xs">{{ $settings['pwa_description'] ?? 'Premium European Hardwood, Waterproof SPC & Luxury Surfaces Gallery' }}</textarea>
                            <span class="text-[10px] text-slate-400 mt-1 block">Used by browser installation prompts and search crawlers.</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#202925] mb-1">Theme Color (Status Bar)</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="pwa_theme_color" value="{{ $settings['pwa_theme_color'] ?? '#1E2A24' }}" class="w-9 h-9 rounded-lg border border-slate-200 cursor-pointer p-0.5">
                                    <input type="text" name="pwa_theme_color" value="{{ $settings['pwa_theme_color'] ?? '#1E2A24' }}" class="flex-1 input-custom text-xs font-mono">
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Tints the device status bar at the very top of the phone screen.</span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#202925] mb-1">Splash Background Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="pwa_background_color" value="{{ $settings['pwa_background_color'] ?? '#FFFFFF' }}" class="w-9 h-9 rounded-lg border border-slate-200 cursor-pointer p-0.5">
                                    <input type="text" name="pwa_background_color" value="{{ $settings['pwa_background_color'] ?? '#FFFFFF' }}" class="flex-1 input-custom text-xs font-mono">
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Color of the startup splash screen while app loads.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Live Preview Card -->
                    <div class="lg:col-span-4 bg-slate-50 rounded-2xl p-5 border border-slate-200/80 space-y-4">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Home Screen Icon Preview</span>
                        
                        <!-- Simulated Phone Screen Icon -->
                        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col items-center text-center space-y-2">
                            <div class="w-20 h-20 rounded-2xl shadow-md overflow-hidden border border-slate-100 flex items-center justify-center bg-slate-900">
                                <img src="/icons/icon-192x192.png" alt="App Icon" class="w-full h-full object-cover">
                            </div>
                            <span id="previewPwaShortName" class="text-xs font-bold text-slate-900 truncate max-w-[140px]">
                                {{ $settings['pwa_short_name'] ?? 'FloorOS' }}
                            </span>
                            <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                Installable Web App
                            </span>
                        </div>

                        <!-- Technical Specs -->
                        <div class="space-y-1.5 text-[11px] text-slate-500 pt-2 border-t border-slate-200">
                            <div class="flex justify-between">
                                <span>Manifest:</span>
                                <a href="/manifest.json" target="_blank" class="font-mono text-brand font-semibold hover:underline">/manifest.json</a>
                            </div>
                            <div class="flex justify-between">
                                <span>Service Worker:</span>
                                <a href="/sw.js" target="_blank" class="font-mono text-brand font-semibold hover:underline">/sw.js</a>
                            </div>
                            <div class="flex justify-between">
                                <span>Maskable Icon:</span>
                                <span class="font-mono text-slate-700">512×512 PNG</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Apple Touch Icon:</span>
                                <span class="font-mono text-slate-700">180×180 PNG</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Floating Save Bar -->
        <div class="sticky bottom-4 z-20 flex items-center justify-between p-4 bg-white/95 backdrop-blur-md border border-[#E3E8E4] rounded-xl shadow-lg">
            <div class="flex items-center gap-2 text-xs text-[#6B756F]">
                <i data-lucide="info" class="w-4 h-4 text-brand"></i>
                <span>Changes will apply immediately across public storefront and admin modules.</span>
            </div>
            <button type="submit" class="btn-brand text-xs py-2 px-6 shadow-md">
                <i data-lucide="check" class="w-4 h-4"></i> Save System Configuration
            </button>
        </div>
    </form>
</div>

<script>
function switchSettingsTab(tabKey) {
    // Hide all tab panes
    document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
    
    // Remove active styles from all buttons
    document.querySelectorAll('.tab-nav-btn').forEach(btn => {
        btn.classList.remove('border-brand', 'text-brand');
        btn.classList.add('border-transparent', 'text-[#6B756F]');
    });

    // Show target pane
    const targetPane = document.getElementById('tabContent-' + tabKey);
    const targetBtn = document.getElementById('tabBtn-' + tabKey);
    if (targetPane && targetBtn) {
        targetPane.classList.remove('hidden');
        targetBtn.classList.remove('border-transparent', 'text-[#6B756F]');
        targetBtn.classList.add('border-brand', 'text-brand');
    }

    // Persist tab via URL hash
    window.location.hash = tabKey;
}

// Initial tab restore from URL hash
document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash ? window.location.hash.substring(1) : 'theme';
    const validTabs = ['theme', 'store', 'invoice', 'products', 'operations'];
    if (validTabs.includes(hash)) {
        switchSettingsTab(hash);
    }
});

const themePresets = @json($presets);

function applyThemePreset(presetKey) {
    const p = themePresets[presetKey];
    if (!p) return;

    if (p.values.theme_primary) {
        document.querySelector('input[name="theme_primary"]').value = p.values.theme_primary;
        document.getElementById('theme_primary_hex').value = p.values.theme_primary;
    }
    if (p.values.theme_primary_dark) {
        document.querySelector('input[name="theme_primary_dark"]').value = p.values.theme_primary_dark;
        document.getElementById('theme_primary_dark_hex').value = p.values.theme_primary_dark;
    }
    if (p.values.theme_primary_light) {
        document.querySelector('input[name="theme_primary_light"]').value = p.values.theme_primary_light;
        document.getElementById('theme_primary_light_hex').value = p.values.theme_primary_light;
    }
    if (p.values.theme_accent) {
        document.querySelector('input[name="theme_accent"]').value = p.values.theme_accent;
        document.getElementById('theme_accent_hex').value = p.values.theme_accent;
    }

    // Visual selection feedback
    document.querySelectorAll('.preset-card').forEach(el => el.classList.remove('ring-2', 'ring-brand', 'border-brand'));
    const activeCard = document.getElementById('presetCard-' + presetKey);
    if (activeCard) {
        activeCard.classList.add('ring-2', 'ring-brand', 'border-brand');
    }

    if (window.showAlert) {
        window.showAlert({
            type: 'info',
            title: p.name + ' Preset Applied',
            message: 'Theme variables filled. Click "Save All Settings" to apply live changes across the application.'
        });
    }
}

function updateColorHex(colorInput, hexInputId) {
    document.getElementById(hexInputId).value = colorInput.value;
}

function updateColorPicker(hexInput, colorInputName) {
    const val = hexInput.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        document.querySelector(`input[name="${colorInputName}"]`).value = val;
    }
}

function updatePwaPreview() {
    const input = document.getElementById('pwaShortNameInput');
    const preview = document.getElementById('previewPwaShortName');
    if (input && preview) {
        preview.innerText = input.value || 'FloorOS';
    }
}
</script>
@endsection
