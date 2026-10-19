@extends('layouts.public')

@section('title', "{$product->name} — Flooring Specifications & Shades")
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@section('no_header', true)
@section('no_mobile_nav', true)

@section('content')
@php
    $firstShade = $product->publicShades->first();
    $initialPrice = (float) ($firstShade->selling_price ?? $product->base_price ?? 0);
    $storePhone = \App\Models\Setting::get('store_phone', '+91 98450 12345');
    $cleanPhone = preg_replace('/[^0-9]/', '', $storePhone);
@endphp

<!-- ======================================================== -->
<!-- Clean, Minimal Top Navigation Utility Bar                 -->
<!-- ======================================================== -->
<div class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-100 transition-all">
    <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-2 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-3">
        
        <!-- Left: Back to Category / Catalog -->
        <div class="flex items-center gap-2">
            <a href="{{ route('catalog') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-brand bg-slate-50 hover:bg-slate-100/80 border border-slate-200/80 rounded-full px-3 py-1.5 transition shadow-2xs group"
               title="Back to {{ $product->category->name }}">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform text-slate-500 group-hover:text-brand"></i>
            </a>

            <!-- Desktop Breadcrumb path -->
            <div class="hidden md:flex items-center gap-1.5 text-xs text-slate-400 pl-2">
                <span>/</span>
                <a href="{{ route('catalog') }}" class="hover:text-brand text-slate-500 transition">Catalog</a>
                <span>/</span>
                <span class="text-slate-900 font-medium truncate max-w-[220px]">{{ $product->name }}</span>
            </div>
        </div>

        <!-- Center: Brand watermark for clean look -->
        <a href="{{ route('home') }}" class="sm:hidden flex items-center gap-1 text-slate-900 font-extrabold text-sm tracking-tight">
            Floor<span class="text-brand">OS</span>
        </a>

        <!-- Right: Actions (Share & Direct WhatsApp) -->
        <div class="flex items-center gap-2">
            <a href="https://wa.me/{{ $cleanPhone }}?text=Hello,%20I%20am%20inquiring%20about%20{{ urlencode($product->name) }}" 
               target="_blank"
               class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100/80 border border-emerald-200/80 rounded-full px-3 py-1.5 transition">
                <i data-lucide="message-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Showroom WhatsApp</span>
            </a>

            <button type="button" 
                    onclick="shareProduct()" 
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-600 hover:text-brand flex items-center justify-center transition shadow-2xs" 
                    title="Share Product">
                <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- Main Product Detail Layout                                -->
<!-- ======================================================== -->
<div class="bg-white min-h-screen py-4 sm:py-8 pb-32 sm:pb-20">
    <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-start">
            
            <!-- Left Column: Gallery on Desktop + Mobile Flow (7 Cols) -->
            <div class="lg:col-span-7 space-y-5">
                
                <!-- Pure Image Gallery Preview Card (Zero Overlay Info/Badges - Touch Slideable) -->
                <div class="space-y-3">
                    <div id="imageSwipeContainer" class="rounded-2xl sm:rounded-3xl bg-slate-50 border border-slate-200/80 overflow-hidden aspect-4/3 relative shadow-xs group cursor-pointer select-none touch-pan-y">
                        <img id="mainProductImage" 
                             src="{{ $firstShade->image ?? $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover transition-all duration-300 pointer-events-none select-none">
                        
                        <!-- Top Right Controls: ONLY Expand Lightbox Button (No badges, No text) -->
                        <div class="absolute top-3.5 right-3.5 z-10">
                            <button type="button" 
                                    onclick="openLightbox(); event.stopPropagation();" 
                                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/95 backdrop-blur-md flex items-center justify-center text-slate-700 shadow-xs hover:bg-brand hover:text-white transition cursor-pointer" 
                                    title="View Fullscreen Lightbox">
                                <i data-lucide="maximize-2" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            </button>
                        </div>

                        <!-- Prev / Next Controls: Accessible on medium screens & desktop -->
                        <button type="button" 
                                onclick="navigateImage(-1, event)" 
                                class="flex absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/90 backdrop-blur-md items-center justify-center text-slate-700 shadow-md hover:bg-brand hover:text-white transition opacity-70 md:opacity-0 md:group-hover:opacity-100 z-10 cursor-pointer" 
                                title="Previous Image">
                            <i data-lucide="chevron-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </button>
                        <button type="button" 
                                onclick="navigateImage(1, event)" 
                                class="flex absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/90 backdrop-blur-md items-center justify-center text-slate-700 shadow-md hover:bg-brand hover:text-white transition opacity-70 md:opacity-0 md:group-hover:opacity-100 z-10 cursor-pointer" 
                                title="Next Image">
                            <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </button>
                    </div>

                    <!-- Mobile Dots Indicators (Below Image, Clean & Minimal) -->
                    <div id="mobileDots" class="flex items-center justify-center gap-1.5 sm:hidden py-1">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Gallery Thumbnails Strip (Desktop & Mobile) -->
                    @if($product->publicShades->count() > 1 || ($product->primary_image && $product->publicShades->count() > 0))
                        <div class="flex gap-2.5 overflow-x-auto pb-1 no-scrollbar pt-1">
                            @if($product->primary_image)
                                <button type="button" 
                                        onclick="setActiveImageByIndex(0)" 
                                        class="gallery-thumb shrink-0 w-16 h-14 sm:w-20 sm:h-16 rounded-xl overflow-hidden border-2 border-brand transition shadow-2xs bg-slate-50 cursor-pointer">
                                    <img src="{{ $product->primary_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </button>
                            @endif

                            @foreach($product->publicShades as $idx => $shade)
                                @php $thumbIndex = $product->primary_image ? ($idx + 1) : $idx; @endphp
                                <button type="button" 
                                        onclick="selectShade({{ $shade->id }}, '{{ addslashes($shade->shade_name) }}', '{{ $shade->sku }}', '{{ $shade->shade_code }}', {{ $shade->selling_price }}, '{{ $shade->image ?: $product->primary_image }}', '{{ $shade->stock_status }}', {{ $shade->stock_quantity }}, {{ $thumbIndex }})" 
                                        class="gallery-thumb shrink-0 w-16 h-14 sm:w-20 sm:h-16 rounded-xl overflow-hidden border border-slate-200/80 hover:border-brand transition shadow-2xs bg-slate-50 cursor-pointer">
                                    <img src="{{ $shade->image ?: $product->primary_image }}" alt="{{ $shade->shade_name }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- ======================================================== -->
                <!-- Mobile Flow: Title, Price & Shades (Visible on Mobile)    -->
                <!-- ======================================================== -->
                <div class="lg:hidden space-y-4 pt-1">
                    
                    <!-- Product Title, Price & Stock Status -->
                    <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-bold text-brand uppercase tracking-wider">
                                {{ $product->brand ?: 'Architectural Flooring Atelier' }}
                            </span>
                            <span class="activeSkuBadge text-[10px] font-mono px-2 py-0.5 rounded-md bg-slate-100 text-slate-500">
                                {{ $firstShade->sku ?? $product->slug }}
                            </span>
                        </div>

                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight leading-snug">
                            {{ $product->name }}
                        </h1>

                        <!-- Price & Live Stock Status -->
                        <div class="pt-2 border-t border-slate-100 flex items-end justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium uppercase">Supply Price</span>
                                <div class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">
                                    <span class="activePrice">₹{{ number_format($initialPrice, 0) }}</span>
                                    <span class="text-xs font-normal text-slate-400">/ {{ $product->unit_of_measure }}</span>
                                </div>
                            </div>

                            <div class="availabilityBadge">
                                @if(($firstShade->stock_quantity ?? 10) <= 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Available on Order
                                    </span>
                                @elseif(($firstShade->stock_quantity ?? 10) <= ($firstShade->reorder_threshold ?? 5))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> In Stock
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Shade Swatches Selector -->
                    @if($product->publicShades->count() > 0)
                        <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wide">
                                    Finish: <span class="activeShadeName text-brand">{{ $firstShade->shade_name }}</span>
                                </span>
                                <span class="text-[11px] text-slate-400">
                                    {{ $product->publicShades->count() }} {{ Str::plural('Shade', $product->publicShades->count()) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                @foreach($product->publicShades as $index => $shade)
                                    @php $shadeImgIndex = $product->primary_image ? ($index + 1) : $index; @endphp
                                    <button type="button" 
                                            onclick="selectShade({{ $shade->id }}, '{{ addslashes($shade->shade_name) }}', '{{ $shade->sku }}', '{{ $shade->shade_code }}', {{ $shade->selling_price }}, '{{ $shade->image ?: $product->primary_image }}', '{{ $shade->stock_status }}', {{ $shade->stock_quantity }}, {{ $shadeImgIndex }})" 
                                            data-shade-id="{{ $shade->id }}"
                                            class="shade-pill p-2 rounded-xl bg-white border text-left transition flex items-center gap-2 cursor-pointer {{ $index === 0 ? 'border-brand ring-2 ring-brand/20 bg-brand-light/10 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-slate-50/50' }}">
                                        <div class="w-9 h-9 rounded-lg overflow-hidden shrink-0 border border-slate-200 bg-white">
                                            <img src="{{ $shade->image ?: $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                                                 alt="{{ $shade->shade_name }}" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="overflow-hidden min-w-0 flex-1">
                                            <span class="block text-xs font-bold text-slate-900 truncate">{{ $shade->shade_name }}</span>
                                            <span class="block text-[11px] text-brand font-semibold font-mono">₹{{ number_format($shade->selling_price, 0) }}</span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- ======================================================== -->
                <!-- Space-Saving Accordions Section (Mobile & Desktop)        -->
                <!-- ======================================================== -->
                <div class="space-y-3 pt-2">
                    
                    <!-- Accordion 1: Key Technical Specifications -->
                    <div class="border border-slate-200/80 rounded-2xl bg-white overflow-hidden shadow-2xs transition">
                        <button type="button" 
                                onclick="toggleAccordion('accSpecs')" 
                                class="w-full px-4 sm:px-5 py-3.5 sm:py-4 flex items-center justify-between text-left font-bold text-slate-900 hover:bg-slate-50/50 transition cursor-pointer">
                            <span class="flex items-center gap-2.5 text-xs sm:text-sm">
                                <i data-lucide="sliders" class="w-4 h-4 text-brand"></i>
                                <span>Key Specifications & Attributes</span>
                            </span>
                            <i data-lucide="chevron-down" id="icon-accSpecs" class="w-4 h-4 text-slate-400 transition-transform duration-200 rotate-180"></i>
                        </button>

                        <div id="accSpecs" class="px-4 sm:px-5 pb-4 pt-1 border-t border-slate-100">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 pt-2">
                                <div class="p-2.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Core Material</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 block truncate">{{ $product->material_type ?: 'Hardwood / SPC' }}</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Surface Finish</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 block truncate">{{ $product->surface_finish ?: 'Matte UV Lacquer' }}</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Dimensions</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 block truncate">{{ $product->dimensions ?: 'Standard Plank' }}</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Thickness</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 block truncate">{{ $product->thickness ?: '12 mm' }}</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Installation</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 block truncate">{{ $product->installation_type ?: 'Click-Lock System' }}</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50/70 border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Box Coverage</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 block truncate">{{ $product->coverage_per_box ?: 20 }} {{ $product->unit_of_measure }}/box</span>
                                </div>
                            </div>

                            @if($product->suitable_for)
                                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-600">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-brand shrink-0"></i>
                                    <span>Recommended Spaces: <strong class="text-slate-800">{{ $product->suitable_for }}</strong></span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Accordion 2: Room Area & Box Estimator -->
                    <div class="border border-slate-200/80 rounded-2xl bg-white overflow-hidden shadow-2xs transition">
                        <button type="button" 
                                onclick="toggleAccordion('accEstimator')" 
                                class="w-full px-4 sm:px-5 py-3.5 sm:py-4 flex items-center justify-between text-left font-bold text-slate-900 hover:bg-slate-50/50 transition cursor-pointer">
                            <span class="flex items-center gap-2.5 text-xs sm:text-sm">
                                <i data-lucide="calculator" class="w-4 h-4 text-brand"></i>
                                <span>Room Area & Box Estimator</span>
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="hidden sm:inline text-[11px] text-slate-400 font-normal">Calculate boxes & cost</span>
                                <i data-lucide="chevron-down" id="icon-accEstimator" class="w-4 h-4 text-slate-400 transition-transform duration-200"></i>
                            </div>
                        </button>

                        <div id="accEstimator" class="hidden px-4 sm:px-5 pb-4 pt-1 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between pt-2">
                                <span class="text-xs text-slate-500">Carton Standard: <strong>{{ $product->coverage_per_box ?: 20 }} {{ $product->unit_of_measure }}</strong> / box</span>
                                <span class="text-[10px] text-slate-400">+10% cutting margin included</span>
                            </div>

                            <!-- Area Preset Buttons -->
                            <div class="flex gap-1.5 overflow-x-auto pb-0.5 no-scrollbar">
                                <button type="button" onclick="setArea(250)" class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700 hover:bg-brand hover:text-white hover:border-brand transition">250 sq ft</button>
                                <button type="button" onclick="setArea(500)" class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700 hover:bg-brand hover:text-white hover:border-brand transition">500 sq ft</button>
                                <button type="button" onclick="setArea(1000)" class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700 hover:bg-brand hover:text-white hover:border-brand transition">1000 sq ft</button>
                                <button type="button" onclick="setArea(1500)" class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700 hover:bg-brand hover:text-white hover:border-brand transition">1500 sq ft</button>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Your Floor Space</label>
                                    <div class="relative">
                                        <input type="number" id="calcAreaInput" value="500" min="1" oninput="calculateBoxes()" class="w-full input-custom text-xs py-2 pr-10 font-bold bg-white">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 font-medium">sq ft</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Required Cartons</label>
                                    <div id="calcBoxResult" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 font-mono">
                                        Calculating...
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">Estimated Material Cost:</span>
                                <span id="calcTotalCostResult" class="font-extrabold text-slate-900 font-mono text-sm">₹0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion 3: Collection Story & Description -->
                    @if($product->description)
                        <div class="border border-slate-200/80 rounded-2xl bg-white overflow-hidden shadow-2xs transition">
                            <button type="button" 
                                    onclick="toggleAccordion('accDesc')" 
                                    class="w-full px-4 sm:px-5 py-3.5 sm:py-4 flex items-center justify-between text-left font-bold text-slate-900 hover:bg-slate-50/50 transition cursor-pointer">
                                <span class="flex items-center gap-2.5 text-xs sm:text-sm">
                                    <i data-lucide="file-text" class="w-4 h-4 text-brand"></i>
                                    <span>Collection Overview & Description</span>
                                </span>
                                <i data-lucide="chevron-down" id="icon-accDesc" class="w-4 h-4 text-slate-400 transition-transform duration-200"></i>
                            </button>

                            <div id="accDesc" class="hidden px-4 sm:px-5 pb-4 pt-2 border-t border-slate-100">
                                <div class="text-xs sm:text-sm text-slate-600 leading-relaxed space-y-2">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Accordion 4: Quality Guarantee & Mill Warranty -->
                    <div class="border border-slate-200/80 rounded-2xl bg-white overflow-hidden shadow-2xs transition">
                        <button type="button" 
                                onclick="toggleAccordion('accWarranty')" 
                                class="w-full px-4 sm:px-5 py-3.5 sm:py-4 flex items-center justify-between text-left font-bold text-slate-900 hover:bg-slate-50/50 transition cursor-pointer">
                            <span class="flex items-center gap-2.5 text-xs sm:text-sm">
                                <i data-lucide="shield-check" class="w-4 h-4 text-brand"></i>
                                <span>Quality Assurance & Mill Warranty</span>
                            </span>
                            <i data-lucide="chevron-down" id="icon-accWarranty" class="w-4 h-4 text-slate-400 transition-transform duration-200"></i>
                        </button>

                        <div id="accWarranty" class="hidden px-4 sm:px-5 pb-4 pt-2 border-t border-slate-100">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-2">
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center space-y-1">
                                    <i data-lucide="award" class="w-4 h-4 text-brand mx-auto"></i>
                                    <span class="text-[11px] font-bold text-slate-900 block">25-Yr Mill Warranty</span>
                                    <span class="text-[10px] text-slate-400 block">Residential Grade</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center space-y-1">
                                    <i data-lucide="droplets" class="w-4 h-4 text-brand mx-auto"></i>
                                    <span class="text-[11px] font-bold text-slate-900 block">Moisture Shield</span>
                                    <span class="text-[10px] text-slate-400 block">Sealed Joint System</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center space-y-1">
                                    <i data-lucide="leaf" class="w-4 h-4 text-brand mx-auto"></i>
                                    <span class="text-[11px] font-bold text-slate-900 block">Low VOC Certified</span>
                                    <span class="text-[10px] text-slate-400 block">Safe Indoor Air</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center space-y-1">
                                    <i data-lucide="truck" class="w-4 h-4 text-brand mx-auto"></i>
                                    <span class="text-[11px] font-bold text-slate-900 block">Direct Dispatch</span>
                                    <span class="text-[10px] text-slate-400 block">48-Hr Delivery</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ======================================================== -->
                <!-- Mobile Only: Quick Sample Enquiry Card                   -->
                <!-- ======================================================== -->
                <div class="lg:hidden pt-2" id="contactFormMobileSection">
                    <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-xs space-y-3.5">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <i data-lucide="send" class="w-3.5 h-3.5 text-brand"></i>
                                    <span>Request Sample & Mill Quote</span>
                                </h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">Physical swatches delivered to your site</p>
                            </div>
                        </div>

                        <form action="{{ route('enquiry.submit') }}" method="POST" class="space-y-2.5">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="product_shade_id" class="formShadeId" value="{{ $firstShade->id ?? '' }}">
                            <input type="hidden" name="preferred_contact" value="whatsapp">

                            <div class="space-y-2">
                                <input type="text" name="name" required placeholder="Your Full Name *" class="w-full input-custom text-xs py-2 bg-white">
                                <input type="tel" name="phone" required placeholder="WhatsApp Number *" class="w-full input-custom text-xs py-2 bg-white">
                                <textarea name="message" rows="2" placeholder="Room dimensions, city, or physical swatch request..." class="w-full input-custom text-xs py-2 bg-white"></textarea>
                            </div>

                            <button type="submit" class="w-full btn-brand py-2.5 text-xs font-bold shadow-xs">
                                <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                                <span>Inquire for <span class="btnShadeLabel">{{ $firstShade->shade_name ?? $product->name }}</span></span>
                            </button>
                        </form>

                        <div class="pt-2 flex items-center justify-between text-xs text-slate-500 border-t border-slate-100">
                            <a href="https://wa.me/{{ $cleanPhone }}?text=Hello,%20I%20am%20interested%20in%20{{ urlencode($product->name) }}" target="_blank" class="hover:text-brand flex items-center gap-1.5 font-semibold transition">
                                <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i> WhatsApp
                            </a>
                            <a href="tel:{{ $storePhone }}" class="hover:text-brand flex items-center gap-1.5 font-semibold transition">
                                <i data-lucide="phone" class="w-4 h-4 text-brand"></i> Call Showroom
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ======================================================== -->
            <!-- Right Column: Desktop Sticky Column (Hidden on Mobile)   -->
            <!-- ======================================================== -->
            <div class="hidden lg:block lg:col-span-5 lg:sticky lg:top-20 space-y-5">
                
                <!-- Desktop Product Header & Pricing Card -->
                <div class="rounded-2xl sm:rounded-3xl bg-white border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-bold text-brand uppercase tracking-wider">
                            {{ $product->brand ?: 'Architectural Flooring Atelier' }}
                        </span>
                        <span class="activeSkuBadge text-[10px] font-mono px-2 py-0.5 rounded-md bg-slate-100 text-slate-500">
                            {{ $firstShade->sku ?? $product->slug }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-snug">
                        {{ $product->name }}
                    </h1>

                    <!-- Price & Stock Status -->
                    <div class="pt-2 border-t border-slate-100 flex items-end justify-between gap-3">
                        <div>
                            <span class="text-[11px] text-slate-400 block font-medium uppercase">Supply Price</span>
                            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-mono">
                                <span class="activePrice">₹{{ number_format($initialPrice, 0) }}</span>
                                <span class="text-xs font-normal text-slate-400">/ {{ $product->unit_of_measure }}</span>
                            </div>
                        </div>

                        <div class="availabilityBadge">
                            @if(($firstShade->stock_quantity ?? 10) <= 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span> Available on Order
                                </span>
                            @elseif(($firstShade->stock_quantity ?? 10) <= ($firstShade->reorder_threshold ?? 5))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Low Stock
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> In Stock
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400">
                        * Mill-direct wholesale & retail supply. Taxes and standard cutting allowances calculated at checkout.
                    </p>
                </div>

                <!-- Desktop Shade Selector Card -->
                @if($product->publicShades->count() > 0)
                    <div class="rounded-2xl sm:rounded-3xl bg-white border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-3.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 uppercase tracking-wide">
                                Finish: <span class="activeShadeName text-brand">{{ $firstShade->shade_name }}</span>
                            </span>
                            <span class="text-[11px] text-slate-400">
                                {{ $product->publicShades->count() }} {{ Str::plural('Shade', $product->publicShades->count()) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:gap-2.5">
                            @foreach($product->publicShades as $index => $shade)
                                @php $shadeImgIndex = $product->primary_image ? ($index + 1) : $index; @endphp
                                <button type="button" 
                                        onclick="selectShade({{ $shade->id }}, '{{ addslashes($shade->shade_name) }}', '{{ $shade->sku }}', '{{ $shade->shade_code }}', {{ $shade->selling_price }}, '{{ $shade->image ?: $product->primary_image }}', '{{ $shade->stock_status }}', {{ $shade->stock_quantity }}, {{ $shadeImgIndex }})" 
                                        data-shade-id="{{ $shade->id }}"
                                        class="shade-pill p-2 sm:p-2.5 rounded-xl bg-white border text-left transition flex items-center gap-2.5 cursor-pointer {{ $index === 0 ? 'border-brand ring-2 ring-brand/20 bg-brand-light/10 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-slate-50/50' }}">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 border border-slate-200 bg-white">
                                        <img src="{{ $shade->image ?: $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                                             alt="{{ $shade->shade_name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="overflow-hidden min-w-0 flex-1">
                                        <span class="block text-xs font-bold text-slate-900 truncate">{{ $shade->shade_name }}</span>
                                        <span class="block text-[11px] text-brand font-semibold font-mono">₹{{ number_format($shade->selling_price, 0) }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Desktop Direct Enquiry Form Card -->
                <div id="contactFormSection" class="rounded-2xl sm:rounded-3xl bg-white border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <i data-lucide="send" class="w-4 h-4 text-brand"></i>
                                <span>Request Sample & Mill Quote</span>
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Physical swatches delivered to your site</p>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full">Fast Dispatch</span>
                    </div>

                    <form action="{{ route('enquiry.submit') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_shade_id" class="formShadeId" value="{{ $firstShade->id ?? '' }}">
                        <input type="hidden" name="preferred_contact" value="whatsapp">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div>
                                <input type="text" name="name" required placeholder="Your Full Name *" class="w-full input-custom text-xs py-2 bg-white">
                            </div>
                            <div>
                                <input type="tel" name="phone" required placeholder="WhatsApp Number *" class="w-full input-custom text-xs py-2 bg-white">
                            </div>
                        </div>

                        <div>
                            <textarea name="message" rows="2" placeholder="Room dimensions, city, or physical swatch request..." class="w-full input-custom text-xs py-2 bg-white"></textarea>
                        </div>

                        <button type="submit" class="w-full btn-brand py-2.5 sm:py-3 text-xs font-bold shadow-xs">
                            <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                            <span>Inquire for <span class="btnShadeLabel">{{ $firstShade->shade_name ?? $product->name }}</span></span>
                        </button>
                    </form>

                    <div class="pt-2 flex items-center justify-between text-xs text-slate-500 border-t border-slate-100">
                        <a href="https://wa.me/{{ $cleanPhone }}?text=Hello,%20I%20am%20interested%20in%20{{ urlencode($product->name) }}" target="_blank" class="hover:text-brand flex items-center gap-1.5 font-semibold transition">
                            <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i> WhatsApp Showroom
                        </a>
                        <a href="tel:{{ $storePhone }}" class="hover:text-brand flex items-center gap-1.5 font-semibold transition">
                            <i data-lucide="phone" class="w-4 h-4 text-brand"></i> Call Showroom
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <!-- Matching Collections Section -->
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="pt-8 border-t border-slate-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-brand uppercase tracking-wider block">More in this Category</span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 mt-0.5">Explore Matching {{ $product->category->name }}</h3>
                    </div>
                    <a href="{{ route('catalog', ['category' => $product->category->slug]) }}" class="text-xs font-bold text-brand hover:underline flex items-center gap-1">
                        <span>View All</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($relatedProducts as $rel)
                        <a href="{{ route('products.show', $rel->slug) }}" class="group rounded-2xl bg-white border border-slate-200/80 p-3 hover:border-brand transition shadow-2xs flex flex-col justify-between">
                            <div class="aspect-4/3 rounded-xl overflow-hidden bg-slate-50 mb-3">
                                <img src="{{ $rel->primary_image ?: '/images/flooring/category-wood.svg' }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $rel->brand ?: 'Atelier Selection' }}</span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-brand transition truncate">{{ $rel->name }}</h4>
                                <span class="text-xs font-extrabold text-slate-900 font-mono block">₹{{ number_format($rel->base_price, 0) }} <span class="text-[10px] font-normal text-slate-400">/ {{ $rel->unit_of_measure }}</span></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

<!-- ======================================================== -->
<!-- Mobile Clean Sticky Bottom Bar (App Feel)                 -->
<!-- ======================================================== -->
<div class="fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-3 sm:hidden shadow-[0_-4px_20px_rgba(0,0,0,0.06)] flex items-center justify-between gap-3"
     style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
    <div class="flex items-center gap-2.5 min-w-0">
        <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 bg-white shrink-0 shadow-2xs">
            <img id="mobileShadeThumb" 
                 src="{{ $firstShade->image ?? $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                 alt="Selected Shade" 
                 class="w-full h-full object-cover">
        </div>
        <div class="min-w-0">
            <div class="text-[10px] text-slate-500 font-medium truncate max-w-[120px]" id="mobileShadeName">
                {{ $firstShade->shade_name ?? $product->name }}
            </div>
            <div class="text-base font-extrabold text-slate-900 font-mono" id="mobilePrice">
                ₹{{ number_format($initialPrice, 0) }}
                <span class="text-[10px] font-normal text-slate-400">/sqft</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 shrink-0">
        <a href="https://wa.me/{{ $cleanPhone }}?text=Hello,%20I%20am%20interested%20in%20{{ urlencode($product->name) }}" 
           target="_blank" 
           class="p-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs" 
           title="WhatsApp Showroom">
            <i data-lucide="message-circle" class="w-4 h-4"></i>
        </a>
        <button type="button" 
                onclick="scrollToEnquiry()" 
                class="btn-brand text-xs py-2.5 px-4 font-bold shadow-xs">
            Enquire Now
        </button>
    </div>
</div>

<!-- ======================================================== -->
<!-- Fullscreen Lightbox Modal                                -->
<!-- ======================================================== -->
<div id="productLightboxModal" class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md hidden flex flex-col justify-between p-3 sm:p-6 transition-all select-none">
    <div class="flex items-center justify-between text-white pb-2 z-20">
        <div>
            <h3 class="text-sm sm:text-base font-bold">{{ $product->name }}</h3>
            <span id="lightboxCounter" class="text-xs text-slate-400 font-mono">1 / 1</span>
        </div>
        <button type="button" onclick="closeLightbox()" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition cursor-pointer" aria-label="Close lightbox">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Center Lightbox Swipe Area (Touch, Drag & Swipe Supported) -->
    <div id="lightboxSwipeArea" class="relative w-full max-w-5xl flex-1 flex items-center justify-center p-2 mx-auto cursor-grab active:cursor-grabbing overflow-hidden touch-pan-y">
        <button type="button" onclick="navigateImage(-1, event)" class="absolute left-2 sm:left-4 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center transition shadow-lg cursor-pointer" aria-label="Previous image">
            <i data-lucide="chevron-left" class="w-5 h-5 sm:w-6 sm:h-6"></i>
        </button>
        
        <div class="w-full h-full flex items-center justify-center overflow-hidden">
            <img id="lightboxImage" src="{{ $firstShade->image ?? $product->primary_image ?: '/images/flooring/category-wood.svg' }}" alt="{{ $product->name }}" class="max-h-[75vh] max-w-full object-contain rounded-xl shadow-2xl transition-all duration-200 select-none pointer-events-none">
        </div>

        <button type="button" onclick="navigateImage(1, event)" class="absolute right-2 sm:right-4 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center transition shadow-lg cursor-pointer" aria-label="Next image">
            <i data-lucide="chevron-right" class="w-5 h-5 sm:w-6 sm:h-6"></i>
        </button>
    </div>

    <!-- Lightbox Bottom Navigation Strip (Touch Hint & Thumbnails) -->
    <div class="z-20 space-y-2 text-center">
        <div class="text-[11px] text-white/50 flex items-center justify-center gap-1.5 font-medium">
            <span>← Swipe horizontally to browse →</span>
        </div>
        <div id="lightboxThumbStrip" class="flex items-center justify-center gap-2 overflow-x-auto max-w-full pb-2 pt-1 no-scrollbar">
            <!-- Thumbnails injected via JS -->
        </div>
    </div>
</div>

<script>
    const coveragePerBox = {{ (float) ($product->coverage_per_box ?: 20) }};
    let currentShadePrice = {{ $initialPrice }};

    // Images Array for swipe and lightbox
    const productImages = [
        @if($product->primary_image)
            "{{ $product->primary_image }}",
        @endif
        @foreach($product->publicShades as $s)
            @if($s->image && $s->image !== $product->primary_image)
                "{{ $s->image }}",
            @endif
        @endforeach
    ];

    if (productImages.length === 0) {
        productImages.push("{{ $product->primary_image ?: '/images/flooring/category-wood.svg' }}");
    }

    let currentImageIndex = 0;

    function renderMobileDots() {
        const dotsContainer = document.getElementById('mobileDots');
        if (!dotsContainer) return;
        dotsContainer.innerHTML = '';
        if (productImages.length <= 1) return;

        productImages.forEach((_, idx) => {
            const dot = document.createElement('button');
            dot.className = `w-2 h-2 rounded-full transition-all duration-300 ${idx === currentImageIndex ? 'bg-brand w-5' : 'bg-slate-300'}`;
            dot.onclick = () => setActiveImageByIndex(idx);
            dotsContainer.appendChild(dot);
        });
    }

    function updateLightboxThumbnails() {
        const strip = document.getElementById('lightboxThumbStrip');
        if (!strip) return;
        if (strip.children.length === 0) {
            productImages.forEach((img, i) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `shrink-0 w-14 h-11 rounded-lg overflow-hidden border transition cursor-pointer ${i === currentImageIndex ? 'border-brand ring-2 ring-brand/60 scale-105' : 'border-white/30 hover:border-white'}`;
                btn.onclick = (e) => { e.stopPropagation(); setActiveImageByIndex(i); };
                btn.innerHTML = `<img src="${img}" class="w-full h-full object-cover">`;
                strip.appendChild(btn);
            });
        } else {
            Array.from(strip.children).forEach((btn, i) => {
                if (i === currentImageIndex) {
                    btn.className = 'shrink-0 w-14 h-11 rounded-lg overflow-hidden border-2 border-brand ring-2 ring-brand/60 scale-105 transition cursor-pointer';
                } else {
                    btn.className = 'shrink-0 w-14 h-11 rounded-lg overflow-hidden border border-white/30 hover:border-white transition cursor-pointer';
                }
            });
        }
    }

    function updateImageDisplay() {
        const mainImg = document.getElementById('mainProductImage');
        const lbImg = document.getElementById('lightboxImage');
        const lbCounter = document.getElementById('lightboxCounter');

        if (mainImg && productImages[currentImageIndex]) {
            mainImg.src = productImages[currentImageIndex];
            mainImg.classList.add('opacity-90');
            setTimeout(() => mainImg.classList.remove('opacity-90'), 150);
        }
        if (lbImg && productImages[currentImageIndex]) {
            lbImg.src = productImages[currentImageIndex];
            lbImg.classList.add('opacity-90');
            setTimeout(() => lbImg.classList.remove('opacity-90'), 150);
        }
        if (lbCounter) lbCounter.innerText = `${currentImageIndex + 1} / ${productImages.length}`;

        // Highlight Main Gallery Thumbnails
        document.querySelectorAll('.gallery-thumb').forEach((thumb, i) => {
            if (i === currentImageIndex) {
                thumb.classList.add('border-brand', 'border-2');
                thumb.classList.remove('border-slate-200/80');
            } else {
                thumb.classList.remove('border-brand', 'border-2');
                thumb.classList.add('border-slate-200/80');
            }
        });

        updateLightboxThumbnails();
        renderMobileDots();
    }

    function setActiveImageByIndex(index) {
        if (index >= 0 && index < productImages.length) {
            currentImageIndex = index;
            updateImageDisplay();
        }
    }

    function navigateImage(direction, event) {
        if (event) event.stopPropagation();
        currentImageIndex = (currentImageIndex + direction + productImages.length) % productImages.length;
        updateImageDisplay();
    }

    // Unified Touch & Pointer Drag Slide Swiper (Medium Screens, Mobile & Touchscreens)
    function attachSwipeHandler(containerEl, onLeft, onRight, onClick) {
        if (!containerEl) return;
        let startX = 0;
        let startY = 0;
        let isDown = false;
        let hasSwiped = false;

        // Touch events
        containerEl.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                hasSwiped = false;
            }
        }, { passive: true });

        containerEl.addEventListener('touchmove', (e) => {
            if (!startX || !startY || e.touches.length !== 1) return;
            const diffX = e.touches[0].clientX - startX;
            const diffY = e.touches[0].clientY - startY;
            if (Math.abs(diffX) > 15 && Math.abs(diffX) > Math.abs(diffY)) {
                hasSwiped = true;
            }
        }, { passive: true });

        containerEl.addEventListener('touchend', (e) => {
            if (!startX || !startY) return;
            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;
            const diffX = endX - startX;
            const diffY = endY - startY;

            if (Math.abs(diffX) > 35 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) onLeft();
                else onRight();
            } else if (!hasSwiped && onClick && Math.abs(diffX) < 10 && Math.abs(diffY) < 10) {
                onClick();
            }
            startX = 0;
            startY = 0;
        }, { passive: true });

        // Pointer / Mouse Drag events (Tablets, Medium screens, Desktop touch)
        containerEl.addEventListener('pointerdown', (e) => {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            isDown = true;
            hasSwiped = false;
            startX = e.clientX;
            startY = e.clientY;
        });

        containerEl.addEventListener('pointermove', (e) => {
            if (!isDown) return;
            const diffX = e.clientX - startX;
            const diffY = e.clientY - startY;
            if (Math.abs(diffX) > 15 && Math.abs(diffX) > Math.abs(diffY)) {
                hasSwiped = true;
            }
        });

        containerEl.addEventListener('pointerup', (e) => {
            if (!isDown) return;
            isDown = false;
            const diffX = e.clientX - startX;
            const diffY = e.clientY - startY;

            if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) onLeft();
                else onRight();
            } else if (!hasSwiped && onClick && Math.abs(diffX) < 10 && Math.abs(diffY) < 10) {
                onClick();
            }
            startX = 0;
            startY = 0;
        });

        containerEl.addEventListener('pointercancel', () => {
            isDown = false;
            startX = 0;
            startY = 0;
        });
    }

    // Attach swipe handlers for both Main Gallery Preview & Lightbox Modal
    attachSwipeHandler(
        document.getElementById('imageSwipeContainer'),
        () => navigateImage(1),
        () => navigateImage(-1),
        () => openLightbox()
    );

    attachSwipeHandler(
        document.getElementById('lightboxSwipeArea'),
        () => navigateImage(1),
        () => navigateImage(-1)
    );

    attachSwipeHandler(
        document.getElementById('productLightboxModal'),
        () => navigateImage(1),
        () => navigateImage(-1)
    );

    // Keyboard navigation in Lightbox
    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('productLightboxModal');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'ArrowRight') navigateImage(1);
            if (e.key === 'ArrowLeft') navigateImage(-1);
            if (e.key === 'Escape') closeLightbox();
        }
    });

    // Lightbox Modal Controls
    function openLightbox(idx) {
        if (typeof idx === 'number') currentImageIndex = idx;
        const modal = document.getElementById('productLightboxModal');
        modal.classList.remove('hidden');
        updateImageDisplay();
    }

    function closeLightbox() {
        document.getElementById('productLightboxModal').classList.add('hidden');
    }

    // Space-Saving Accordion Toggle
    function toggleAccordion(id) {
        const panel = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        if (!panel) return;
        
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            panel.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    }

    // Area Calculator
    function setArea(val) {
        document.getElementById('calcAreaInput').value = val;
        calculateBoxes();
    }

    function calculateBoxes() {
        const inputEl = document.getElementById('calcAreaInput');
        if (!inputEl) return;
        const area = parseFloat(inputEl.value) || 0;
        const totalAreaWithCut = area * 1.10;
        const boxes = Math.ceil(totalAreaWithCut / coveragePerBox);
        const actualCoverage = (boxes * coveragePerBox).toFixed(0);
        const estimatedCost = (actualCoverage * currentShadePrice).toLocaleString('en-IN', { maximumFractionDigits: 0 });

        const resultBox = document.getElementById('calcBoxResult');
        if (resultBox) resultBox.innerText = `${boxes} Boxes (${actualCoverage} sq ft)`;

        const costEl = document.getElementById('calcTotalCostResult');
        if (costEl) costEl.innerText = '₹' + estimatedCost;
    }

    // Live Shade Selector Handler (Synchronizes Mobile & Desktop UI seamlessly)
    function selectShade(id, name, sku, code, price, image, stockStatus, stockQty, imgIndex) {
        currentShadePrice = parseFloat(price);

        // Update active shade names
        document.querySelectorAll('.activeShadeName').forEach(el => el.innerText = name);
        if (document.getElementById('mobileShadeName')) {
            document.getElementById('mobileShadeName').innerText = name;
        }

        // Update thumbnail on mobile bottom bar
        if (document.getElementById('mobileShadeThumb') && image) {
            document.getElementById('mobileShadeThumb').src = image;
        }

        // Update SKU badges
        document.querySelectorAll('.activeSkuBadge').forEach(el => el.innerText = sku);

        // Update Price displays
        document.querySelectorAll('.activePrice').forEach(el => el.innerText = '₹' + currentShadePrice.toFixed(0));
        if (document.getElementById('mobilePrice')) {
            document.getElementById('mobilePrice').innerHTML = `₹${currentShadePrice.toFixed(0)} <span class="text-[10px] font-normal text-slate-400">/sqft</span>`;
        }

        // Update form inputs & submit button labels
        document.querySelectorAll('.formShadeId').forEach(el => el.value = id);
        document.querySelectorAll('.btnShadeLabel').forEach(el => el.innerText = name);

        // Update image
        if (typeof imgIndex === 'number' && imgIndex < productImages.length) {
            setActiveImageByIndex(imgIndex);
        } else if (image) {
            const foundIdx = productImages.indexOf(image);
            if (foundIdx !== -1) {
                setActiveImageByIndex(foundIdx);
            } else {
                const mainImg = document.getElementById('mainProductImage');
                if (mainImg) mainImg.src = image;
            }
        }

        // Highlight active swatch pill
        document.querySelectorAll('.shade-pill').forEach(btn => {
            btn.classList.remove('border-brand', 'ring-2', 'ring-brand/20', 'bg-brand-light/10', 'shadow-2xs');
            btn.classList.add('border-slate-200', 'bg-slate-50/50');
        });
        document.querySelectorAll(`.shade-pill[data-shade-id="${id}"]`).forEach(btn => {
            btn.classList.remove('border-slate-200', 'bg-slate-50/50');
            btn.classList.add('border-brand', 'ring-2', 'ring-brand/20', 'bg-brand-light/10', 'shadow-2xs');
        });

        // Update Availability Badges across views
        document.querySelectorAll('.availabilityBadge').forEach(badgeContainer => {
            if (stockQty <= 0) {
                badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span> Available on Order
                </span>`;
            } else if (stockStatus === 'Low Stock' || stockQty <= 5) {
                badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Low Stock
                </span>`;
            } else {
                badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> In Stock
                </span>`;
            }
        });

        // Recalculate estimated total cost with newly selected shade price
        calculateBoxes();
    }

    function scrollToEnquiry() {
        const mobileTarget = document.getElementById('contactFormMobileSection');
        const desktopTarget = document.getElementById('contactFormSection');
        if (window.innerWidth < 1024 && mobileTarget) {
            mobileTarget.scrollIntoView({ behavior: 'smooth' });
        } else if (desktopTarget) {
            desktopTarget.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Mobile Share Handler
    function shareProduct() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                url: window.location.href,
            }).catch(() => {});
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Product link copied to clipboard!');
            }).catch(() => {});
        }
    }

    // Keyboard support for Lightbox
    document.addEventListener('keydown', (e) => {
        const lb = document.getElementById('productLightboxModal');
        if (lb && !lb.classList.contains('hidden')) {
            if (e.key === 'ArrowLeft') navigateImage(-1);
            if (e.key === 'ArrowRight') navigateImage(1);
            if (e.key === 'Escape') closeLightbox();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        calculateBoxes();
        renderMobileDots();
        updateImageDisplay();
    });
</script>
@endsection
