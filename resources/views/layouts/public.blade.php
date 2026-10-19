<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FloorOS — Premium Flooring & Surface Gallery')</title>
    <meta name="description" content="@yield('meta_description', 'Explore authentic hardwood, waterproof SPC, and designer vinyl flooring for residential and commercial spaces.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,400&display=swap" rel="stylesheet">
    
    <!-- PWA Web App Manifest & Meta Tags -->
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="theme-color" content="{{ \App\Models\Setting::get('pwa_theme_color', \App\Models\Setting::get('theme_primary_dark', '#1E2A24')) }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ \App\Models\Setting::get('pwa_short_name', 'FloorOS') }}">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icons/icon-512x512.png">
    <link rel="icon" type="image/png" href="/favicon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        {!! $themeCss !!}
        
        .font-serif-luxury {
            font-family: 'Playfair Display', Georgia, serif;
        }

        /* PWA Install Button Display Rules: Strictly hide in header on small/tablet screens (<1024px) */
        #pwaInstallBtnNavbar {
            display: none !important;
        }
        @media (min-width: 1024px) {
            #pwaInstallBtnNavbar.can-install {
                display: inline-flex !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased bg-white text-slate-900 selection:bg-brand-light selection:text-brand">

    @unless(View::hasSection('no_header'))
    <!-- Top Announcement Bar (Fully Responsive) -->
    <div class="bg-brand-dark text-white text-xs py-1.5 sm:py-2 px-3 sm:px-6 lg:px-8 border-b border-brand/30">
        <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto flex items-center justify-between gap-3 overflow-hidden">
            <!-- Left: Address / Location with smart truncation -->
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <span class="inline-flex items-center gap-1.5 opacity-95 min-w-0" title="{{ $storeAddress }}">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-accent shrink-0"></i>
                    <span class="truncate max-w-[180px] sm:max-w-xs md:max-w-sm lg:max-w-md xl:max-w-xl font-medium">{{ $storeAddress }}</span>
                </span>
                <span class="text-white/40 hidden xl:inline">|</span>
                <span class="hidden xl:inline-flex items-center gap-1.5 opacity-95 whitespace-nowrap">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-accent shrink-0"></i>
                    <span>Mon-Sat: 10 AM - 8 PM</span>
                </span>
            </div>

            <!-- Right: Contact Phone & Staff Portal -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0 text-xs">
                <a href="tel:{{ $storePhone }}" class="inline-flex items-center gap-1.5 hover:text-accent transition whitespace-nowrap">
                    <i data-lucide="phone" class="w-3.5 h-3.5 text-accent shrink-0"></i>
                    <span class="font-semibold">{{ $storePhone }}</span>
                </a>
                <span class="text-white/30 hidden sm:inline">|</span>
                <a href="{{ route('admin.login') }}" class="opacity-85 hover:opacity-100 hover:text-white transition flex items-center gap-1 whitespace-nowrap">
                    <i data-lucide="shield-check" class="w-3 h-3 text-accent shrink-0"></i>
                    <span class="hidden sm:inline">Staff Portal</span>
                    <span class="sm:hidden">Staff</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
        <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-3 sm:px-6 lg:px-8 h-14 sm:h-18 lg:h-20 flex items-center justify-between gap-2 sm:gap-4 lg:gap-6">
            
            <!-- Left: Mobile/Tablet Hamburger & Brand Logo -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Mobile & Tablet Hamburger (Opens Slide-out Sidebar Drawer) -->
                <button type="button" onclick="openMobileSidebar()" class="p-2 -ml-1 text-slate-800 hover:bg-slate-100 rounded-xl transition lg:hidden" aria-label="Open menu">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-2.5 group">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-brand flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-none whitespace-nowrap">
                            Floor<span class="text-brand">OS</span>
                        </span>
                        <span class="hidden xl:block text-[9px] uppercase tracking-wider font-semibold text-slate-500 mt-0.5 whitespace-nowrap">Flooring Gallery & Atelier</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Nav Links (Centered on Desktop >= 1024px) -->
            <nav class="hidden lg:flex items-center gap-6 xl:gap-8 text-sm font-semibold text-slate-700 whitespace-nowrap">
                <a href="{{ route('home') }}" class="hover:text-brand transition {{ request()->routeIs('home') ? 'text-brand font-bold' : '' }}">Home</a>
                <a href="{{ route('catalog') }}" class="hover:text-brand transition {{ request()->routeIs('catalog*') ? 'text-brand font-bold' : '' }}">Flooring Catalog</a>
                <a href="{{ route('contact') }}" class="hover:text-brand transition {{ request()->routeIs('contact') ? 'text-brand font-bold' : '' }}">Showroom & Atelier</a>
            </nav>

            <!-- Right Header Actions (Install, Search & Contact) -->
            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                <!-- PWA Install App Button (Desktop >= 1024px Only) -->
                <button type="button" 
                        id="pwaInstallBtnNavbar" 
                        onclick="triggerPwaInstall()" 
                        class="hidden lg:inline-flex items-center gap-1.5 h-9 sm:h-10 px-3 sm:px-3.5 rounded-xl border border-brand/30 bg-brand-light/70 hover:bg-brand-light text-brand text-xs sm:text-sm font-bold shadow-2xs transition shrink-0 whitespace-nowrap" 
                        title="Install {{ \App\Models\Setting::get('pwa_short_name', 'FloorOS') }} Web App">
                    <i data-lucide="download" class="w-3.5 h-3.5 sm:w-4 sm:h-4 pointer-events-none"></i>
                    <span>Install App</span>
                </button>

                <!-- Search Icon Button -->
                <button type="button" onclick="openSearchModal()" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-700 hover:text-brand flex items-center justify-center transition shadow-2xs shrink-0" title="Search catalog (Ctrl + K)">
                    <i data-lucide="search" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </button>

                <!-- Contact Us Button -->
                <button type="button" onclick="openContactModal()" class="h-9 sm:h-10 px-3 sm:px-4 rounded-xl btn-brand text-xs sm:text-sm font-bold flex items-center gap-1.5 whitespace-nowrap shadow-xs shrink-0">
                    <i data-lucide="mail" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                    <span>Contact<span class="hidden sm:inline"> Us</span></span>
                </button>
            </div>
        </div>
    </header>
    @endunless

    <!-- Slide-Out Mobile Sidebar Navigation Drawer -->
    <div id="mobileSidebarOverlay" onclick="closeMobileSidebar()" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs hidden transition-opacity duration-300"></div>
    <aside id="mobileSidebarDrawer" class="fixed top-0 bottom-0 left-0 w-80 max-w-[85vw] z-50 bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col justify-between border-r border-slate-200">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand flex items-center justify-center text-white shadow-xs">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold text-slate-900">Floor<span class="text-brand">OS</span></span>
                </div>
                <button type="button" onclick="closeMobileSidebar()" class="p-2 text-slate-400 hover:text-slate-700 rounded-lg transition" aria-label="Close sidebar">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Drawer Nav Links -->
            <nav class="space-y-1.5 text-sm font-semibold">
                <a href="{{ route('home') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-xl transition {{ request()->routeIs('home') ? 'bg-brand-light text-brand' : 'text-slate-700 hover:bg-slate-50' }}">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span>Storefront Home</span>
                </a>
                <a href="{{ route('catalog') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-xl transition {{ request()->routeIs('catalog*') ? 'bg-brand-light text-brand' : 'text-slate-700 hover:bg-slate-50' }}">
                    <i data-lucide="grid" class="w-4 h-4"></i>
                    <span>Flooring Catalog</span>
                </a>
                <a href="{{ route('contact') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-xl transition {{ request()->routeIs('contact') ? 'bg-brand-light text-brand' : 'text-slate-700 hover:bg-slate-50' }}">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                    <span>Showroom & Atelier</span>
                </a>
                <button type="button" onclick="closeMobileSidebar(); openContactModal();" class="w-full flex items-center gap-3 py-2.5 px-3 rounded-xl text-slate-700 hover:bg-slate-50 transition text-left">
                    <i data-lucide="mail" class="w-4 h-4 text-brand"></i>
                    <span>Contact Us</span>
                </button>
            </nav>

            <!-- Install PWA App Box (Mobile Drawer) -->
            <div id="pwaInstallBoxDrawer" class="p-3.5 rounded-2xl bg-gradient-to-br from-brand-light/90 via-emerald-50 to-white border border-brand/25 shadow-xs space-y-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center shadow-xs overflow-hidden shrink-0 border border-slate-200">
                        <img src="/icons/icon-96x96.png" alt="App Icon" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-bold text-slate-900 truncate">{{ \App\Models\Setting::get('pwa_short_name', 'FloorOS') }} App</span>
                        <span class="block text-[10px] text-slate-500 font-medium">Install for fast offline access</span>
                    </div>
                </div>
                <button type="button" 
                        onclick="triggerPwaInstall()" 
                        class="w-full py-2 px-3 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition active:scale-95">
                    <i data-lucide="download" class="w-3.5 h-3.5 pointer-events-none"></i>
                    <span>Install Web App</span>
                </button>
            </div>
        </div>

        <div class="p-6 border-t border-slate-100 space-y-3">
            <a href="tel:{{ $storePhone }}" class="flex items-center gap-2.5 text-xs font-bold text-brand p-3 rounded-xl bg-brand-light">
                <i data-lucide="phone" class="w-4 h-4"></i>
                Call {{ $storePhone }}
            </a>
            <a href="{{ route('admin.login') }}" class="flex items-center gap-2 text-xs text-slate-500 hover:text-slate-800 py-1 px-1">
                <i data-lucide="shield-check" class="w-4 h-4 text-accent"></i>
                Staff Management Portal
            </a>
        </div>
    </aside>

    <!-- Success & Alert Notifications -->
    @if(session('success'))
        <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-2xs">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 mt-0.5"></i>
                <div class="text-sm">
                    <p class="font-bold mb-1">Please check the following:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Page Body (Pure white background) -->
    <main class="flex-grow @unless(View::hasSection('no_mobile_nav')) pb-20 md:pb-0 @endunless bg-white">
        @yield('content')
    </main>

    <!-- Modern Responsive Search Modal (Requirement 6) -->
    <div id="siteSearchModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-start justify-center p-4 sm:pt-20">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-5 sm:p-7 relative border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <button type="button" onclick="closeSearchModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-brand">Catalog Search</span>
                <h3 class="text-lg sm:text-xl font-bold text-slate-900 mt-0.5">Find Flooring Planks & Collections</h3>
            </div>

            <form action="{{ route('catalog') }}" method="GET" class="relative">
                <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="siteSearchInput" name="search" placeholder="Search by species, shade name, SKU (e.g. Oak, Walnut, SPC, Grey)..." class="w-full pl-12 pr-24 py-3.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand shadow-xs">
                <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 btn-brand text-xs py-2 px-3.5 shadow-xs font-semibold">
                    Search
                </button>
            </form>

            <div class="mt-4 pt-3 border-t border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Popular Searches</span>
                <div class="flex flex-wrap gap-1.5">
                    <a href="{{ route('catalog', ['search' => 'Oak']) }}" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs text-slate-700 hover:border-brand hover:text-brand transition">European Oak</a>
                    <a href="{{ route('catalog', ['search' => 'SPC']) }}" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs text-slate-700 hover:border-brand hover:text-brand transition">Waterproof SPC</a>
                    <a href="{{ route('catalog', ['search' => 'Chevron']) }}" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs text-slate-700 hover:border-brand hover:text-brand transition">French Chevron</a>
                    <a href="{{ route('catalog', ['search' => 'Walnut']) }}" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs text-slate-700 hover:border-brand hover:text-brand transition">Walnut Elegance</a>
                    <a href="{{ route('catalog', ['search' => 'Laminate']) }}" class="px-2.5 py-1 rounded-lg border border-slate-200 text-xs text-slate-700 hover:border-brand hover:text-brand transition">Commercial Laminate</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Us Modal (Requirement 12: Modern, responsive, Approximate Area field removed) -->
    <div id="contactUsModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto no-scrollbar p-6 sm:p-8 relative border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <button type="button" onclick="closeContactModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-50 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="mb-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-light text-brand text-xs font-bold uppercase tracking-wider mb-2">
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                    <span>Showroom Inquiry</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Connect With Our Specialists</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Tell us about your flooring project or preferred wood finishes for personalized guidance.</p>
            </div>

            <form action="{{ route('enquiry.submit') }}" method="POST" id="contactUsForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-900 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Rahul Mehta" class="w-full input-custom text-sm">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-900 mb-1">Phone Number (WhatsApp) *</label>
                        <input type="tel" name="phone" required placeholder="+91 98765 43210" class="w-full input-custom text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-900 mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="rahul@example.com" class="w-full input-custom text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-900 mb-1">Preferred Communication</label>
                    <select name="preferred_contact" class="w-full input-custom text-sm">
                        <option value="whatsapp">WhatsApp Message</option>
                        <option value="call">Direct Phone Call</option>
                        <option value="email">Email Follow-up</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-900 mb-1">Project Details or Required Collection</label>
                    <textarea name="message" rows="3" placeholder="Specify your rooms (Living, Bedroom, Clinic) or preferred shade (Oak, Chevron, SPC)..." class="w-full input-custom text-sm"></textarea>
                </div>
                <button type="submit" id="contactSubmitBtn" class="w-full btn-brand py-3 text-sm font-bold justify-center shadow-xs">
                    <span>Send Inquiry</span>
                    <i data-lucide="send" class="w-4 h-4 ml-1"></i>
                </button>
                <p class="text-[11px] text-center text-slate-400">Your contact info is strictly confidential and used solely for your showroom consultation.</p>
            </form>
        </div>
    </div>

    <!-- Modern and Interactive Footer (Requirement 11: Clean, responsive, active theme variables) -->
    <footer class="bg-white text-slate-800 pt-12 pb-16 sm:pb-12 border-t border-slate-200">
        <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-100">
                
                <!-- Col 1: Brand & Contact -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand flex items-center justify-center text-white shadow-xs">
                            <i data-lucide="layers" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-slate-900">Floor<span class="text-brand">OS</span></span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        {{ $storeTagline }} — Precision engineered hardwood, waterproof SPC, and acoustic surface systems.
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storePhone) }}" target="_blank" class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-brand hover:text-white hover:border-brand transition" title="WhatsApp">
                            <i data-lucide="phone-call" class="w-4 h-4"></i>
                        </a>
                        <a href="mailto:{{ $storeEmail }}" class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-brand hover:text-white hover:border-brand transition" title="Email">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Collections Accordion on Mobile / Grid on Desktop -->
                <div class="border-t border-slate-100 md:border-t-0 pt-3 md:pt-0">
                    <button type="button" onclick="toggleFooterAccordion('collections')" class="w-full flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-900 md:cursor-default py-1">
                        <span>Flooring Collections</span>
                        <i data-lucide="chevron-down" id="footerIcon-collections" class="w-4 h-4 text-slate-400 md:hidden transition-transform duration-200"></i>
                    </button>
                    <div id="footerContent-collections" class="hidden md:block pt-3 space-y-2.5 text-xs text-slate-600">
                        <div><a href="{{ route('catalog', ['category' => 'wood-flooring']) }}" class="hover:text-brand transition">Engineered Hardwood</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'spc-flooring']) }}" class="hover:text-brand transition">100% Waterproof SPC</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'luxury-vinyl']) }}" class="hover:text-brand transition">Luxury Vinyl Tiles (LVT)</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'laminate-flooring']) }}" class="hover:text-brand transition">Commercial Laminate</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'carpet-tiles']) }}" class="hover:text-brand transition">Modular Carpet Tiles</a></div>
                    </div>
                </div>

                <!-- Col 3: Customer Care & Services Accordion -->
                <div class="border-t border-slate-100 md:border-t-0 pt-3 md:pt-0">
                    <button type="button" onclick="toggleFooterAccordion('services')" class="w-full flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-900 md:cursor-default py-1">
                        <span>Store & Services</span>
                        <i data-lucide="chevron-down" id="footerIcon-services" class="w-4 h-4 text-slate-400 md:hidden transition-transform duration-200"></i>
                    </button>
                    <div id="footerContent-services" class="hidden md:block pt-3 space-y-2.5 text-xs text-slate-600">
                        <div><a href="{{ route('contact') }}" class="hover:text-brand transition">Showroom Directions & Hours</a></div>
                        <div><button type="button" onclick="openContactModal()" class="hover:text-brand transition text-left">Contact Us & Inquiries</button></div>
                        <div><a href="{{ route('privacyPolicy') }}" class="hover:text-brand transition">Privacy Policy</a></div>
                        <div><a href="{{ route('admin.login') }}" class="hover:text-brand transition inline-flex items-center gap-1 text-brand font-semibold">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Staff Login
                        </a></div>
                    </div>
                </div>

                <!-- Col 4: Atelier & Showroom Accordion -->
                <div class="border-t border-slate-100 md:border-t-0 pt-3 md:pt-0">
                    <button type="button" onclick="toggleFooterAccordion('atelier')" class="w-full flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-900 md:cursor-default py-1">
                        <span>Flagship Atelier</span>
                        <i data-lucide="chevron-down" id="footerIcon-atelier" class="w-4 h-4 text-slate-400 md:hidden transition-transform duration-200"></i>
                    </button>
                    <div id="footerContent-atelier" class="hidden md:block pt-3 space-y-2.5 text-xs text-slate-600">
                        <p class="leading-relaxed">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-brand inline mr-1"></i>
                            {{ $storeAddress }}
                        </p>
                        <p>
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-brand inline mr-1"></i>
                            {{ $storePhone }}
                        </p>
                        <p>
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-brand inline mr-1"></i>
                            {{ $storeEmail }}
                        </p>
                    </div>
                </div>

            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>© {{ date('Y') }} FloorOS — All rights reserved. Real-time synchronized store catalog.</p>
               <a href="https://saastify.fmmedianetwork.com" class="text-brand text-md font-bold font-medium pb-4">Developed By Irfan  Manzoor </a>
            </div>
        </div>
    </footer>

    @unless(View::hasSection('no_mobile_nav'))
    <!-- Mobile Sticky Bottom Navigation Bar (Clean & pure white) -->
    <nav class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-lg border-t border-slate-200 px-4 py-2 flex items-center justify-around md:hidden shadow-[0_-4px_16px_rgba(0,0,0,0.04)]">
        <!-- Home -->
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl transition {{ request()->routeIs('home') ? 'text-brand' : 'text-slate-400 hover:text-slate-700' }}">
            <i data-lucide="home" class="w-5 h-5 {{ request()->routeIs('home') ? 'stroke-[2.5]' : '' }}"></i>
            <span class="text-[10px] font-semibold tracking-tight">Home</span>
        </a>

        <!-- Catalog -->
        <a href="{{ route('catalog') }}" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl transition {{ request()->routeIs('catalog*') ? 'text-brand' : 'text-slate-400 hover:text-slate-700' }}">
            <i data-lucide="layout-grid" class="w-5 h-5 {{ request()->routeIs('catalog*') ? 'stroke-[2.5]' : '' }}"></i>
            <span class="text-[10px] font-semibold tracking-tight">Catalog</span>
        </a>

        <!-- Contact Us -->
        <button type="button" onclick="openContactModal()" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl text-slate-400 hover:text-slate-700 transition">
            <i data-lucide="mail" class="w-5 h-5"></i>
            <span class="text-[10px] font-semibold tracking-tight">Contact</span>
        </button>

        <!-- Menu Drawer -->
        <button type="button" onclick="openMobileSidebar()" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl transition text-slate-400 hover:text-slate-700">
            <i data-lucide="menu" class="w-5 h-5"></i>
            <span class="text-[10px] font-semibold tracking-tight">Menu</span>
        </button>
    </nav>
    @endunless

    <script>
        function openMobileSidebar() {
            const overlay = document.getElementById('mobileSidebarOverlay');
            const drawer = document.getElementById('mobileSidebarDrawer');
            overlay.classList.remove('hidden');
            drawer.classList.remove('-translate-x-full');
            drawer.classList.add('translate-x-0');
        }

        function closeMobileSidebar() {
            const overlay = document.getElementById('mobileSidebarOverlay');
            const drawer = document.getElementById('mobileSidebarDrawer');
            overlay.classList.add('hidden');
            drawer.classList.remove('translate-x-0');
            drawer.classList.add('-translate-x-full');
        }

        function openSearchModal() {
            const modal = document.getElementById('siteSearchModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                const input = document.getElementById('siteSearchInput');
                if (input) input.focus();
            }, 50);
        }

        function closeSearchModal() {
            document.getElementById('siteSearchModal').classList.add('hidden');
        }

        function openContactModal() {
            document.getElementById('contactUsModal').classList.remove('hidden');
        }

        function closeContactModal() {
            document.getElementById('contactUsModal').classList.add('hidden');
        }

        function toggleFooterAccordion(id) {
            if (window.innerWidth >= 768) return; // Keep open on desktop
            const content = document.getElementById('footerContent-' + id);
            const icon = document.getElementById('footerIcon-' + id);
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-180');
            }
        }

        // Global keyboard listeners
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeSearchModal();
                closeContactModal();
                closeMobileSidebar();
            }
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                openSearchModal();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (window.createFloorIcons) window.createFloorIcons();
        });

        // ----------------------------------------------------
        // PWA Service Worker Registration & Installation Prompt
        // ----------------------------------------------------
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('FloorOS PWA registered with scope:', reg.scope))
                    .catch(err => console.warn('PWA registration warning:', err));
            });
        }

        let deferredPwaPrompt = null;

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent default mini-infobar on mobile
            e.preventDefault();
            deferredPwaPrompt = e;

            // Reveal Install button on navbar (desktop only) and drawer (small screens / mobile)
            const navBtn = document.getElementById('pwaInstallBtnNavbar');
            const drawerBox = document.getElementById('pwaInstallBoxDrawer');
            if (navBtn) {
                navBtn.classList.add('can-install');
                navBtn.classList.remove('hidden');
            }
            if (drawerBox) {
                drawerBox.classList.remove('hidden');
            }
        });

        function triggerPwaInstall() {
            if (deferredPwaPrompt) {
                deferredPwaPrompt.prompt();
                deferredPwaPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User installed FloorOS PWA');
                    }
                    deferredPwaPrompt = null;
                });
            } else {
                // Helpful instructions for iOS Safari and other browsers
                const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                if (isIos) {
                    alert("To install FloorOS on iPhone / iPad:\n1. Tap the Share button at the bottom of Safari\n2. Scroll down and tap 'Add to Home Screen'");
                } else {
                    alert("To install FloorOS App:\n1. Tap your browser menu (three dots ⋮ or ⋯)\n2. Select 'Install App' or 'Add to Home Screen'");
                }
            }
        }

        window.addEventListener('appinstalled', () => {
            console.log('FloorOS PWA installed successfully');
            const navBtn = document.getElementById('pwaInstallBtnNavbar');
            if (navBtn) {
                navBtn.classList.remove('can-install');
                navBtn.classList.add('hidden');
            }
            const drawerBox = document.getElementById('pwaInstallBoxDrawer');
            if (drawerBox) {
                drawerBox.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
