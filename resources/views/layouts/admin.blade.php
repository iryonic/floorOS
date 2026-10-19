<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') — FloorOS Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- PWA Web App Manifest & Meta Tags -->
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="theme-color" content="{{ \App\Models\Setting::get('pwa_theme_color', \App\Models\Setting::get('theme_primary_dark', '#1E2A24')) }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="{{ \App\Models\Setting::get('pwa_short_name', 'FloorOS') }}">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" href="/favicon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        {!! $themeCss !!}
        
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-base, 0.5rem);
            font-size: 0.8125rem;
            font-weight: 500;
            color: #4B5563;
            transition: all 0.15s ease;
        }
        
        .sidebar-link:hover {
            color: var(--color-primary, #2EAA1F);
            background-color: var(--color-primary-light, #EDFBE8);
        }
        
        .sidebar-link.active {
            color: var(--color-primary, #2EAA1F);
            background-color: var(--color-primary-light, #EDFBE8);
            font-weight: 600;
        }

        .sidebar-link.active svg,
        .sidebar-link.active i {
            color: var(--color-primary, #2EAA1F);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-white text-[#202925] flex antialiased">

    <!-- Mobile Drawer Overlay -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-xs hidden lg:hidden"></div>

    <!-- Admin Sidebar (Scrollable without visible scrollbar) -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-[#E3E8E4] flex flex-col justify-between transition-transform duration-200 -translate-x-full lg:translate-x-0">
        <!-- Top Section: Logo & Nav -->
        <div class="flex-1 overflow-y-auto no-scrollbar px-4 py-5 space-y-6">
            <!-- Brand -->
            <div class="flex items-center justify-between pb-4 border-b border-[#E3E8E4]">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand flex items-center justify-center text-white shadow-xs">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="8" height="8" rx="1.5"></rect>
                            <rect x="13" y="3" width="8" height="8" rx="1.5"></rect>
                            <rect x="3" y="13" width="8" height="8" rx="1.5"></rect>
                            <rect x="13" y="13" width="8" height="8" rx="1.5"></rect>
                            <line x1="3" y1="11" x2="11" y2="3"></line>
                            <line x1="13" y1="21" x2="21" y2="13"></line>
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold tracking-tight text-[#202925]">Floor<span class="text-brand">OS</span></span>
                        <span class="block text-[10px] uppercase font-semibold text-[#6B756F] tracking-wider">Flooring Store ERP</span>
                    </div>
                </a>
                <button type="button" onclick="toggleSidebar()" class="lg:hidden p-1.5 text-neutral-400 hover:text-neutral-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Groups -->
            <nav class="space-y-5">
                <!-- Group 1: Operational Core -->
                <div>
                    <span class="px-2 text-[10px] font-bold uppercase tracking-wider text-[#9CA3AF]">Operations</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-neutral-500"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                            <i data-lucide="layers" class="w-4 h-4 text-neutral-500"></i>
                            <span>Products & Shades</span>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                            <i data-lucide="grid" class="w-4 h-4 text-neutral-500"></i>
                            <span>Categories</span>
                        </a>
                        <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                            <i data-lucide="boxes" class="w-4 h-4 text-neutral-500"></i>
                            <span>Inventory & Stock</span>
                        </a>
                        <a href="{{ route('admin.hero-slides.index') }}" class="sidebar-link {{ request()->routeIs('admin.hero-slides*') ? 'active' : '' }}">
                            <i data-lucide="gallery-horizontal" class="w-4 h-4 text-neutral-500"></i>
                            <span>Hero Slides</span>
                        </a>
                    </div>
                </div>

                <!-- Group 2: Financials & Cash Flow -->
                <div>
                    <span class="px-2 text-[10px] font-bold uppercase tracking-wider text-[#9CA3AF]">Financial Management</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('admin.accounting.index') }}" class="sidebar-link {{ request()->routeIs('admin.accounting*') ? 'active' : '' }}">
                            <i data-lucide="landmark" class="w-4 h-4 text-neutral-500"></i>
                            <span>Accounting & Cash Flow</span>
                        </a>
                    </div>
                </div>

                <!-- Group 4: Communications & Enquiries -->
                <div>
                    <span class="px-2 text-[10px] font-bold uppercase tracking-wider text-[#9CA3AF]">Communications</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('admin.enquiries.index') }}" class="sidebar-link justify-between {{ request()->routeIs('admin.enquiries*') ? 'active' : '' }}">
                            <div class="flex items-center gap-3">
                                <i data-lucide="message-square" class="w-4 h-4 text-neutral-500"></i>
                                <span>Customer Enquiries</span>
                            </div>
                            @if(isset($unreadEnquiriesCount) && $unreadEnquiriesCount > 0)
                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-brand text-white">{{ $unreadEnquiriesCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Group 5: Administration -->
                <div>
                    <span class="px-2 text-[10px] font-bold uppercase tracking-wider text-[#9CA3AF]">System</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <i data-lucide="user-cog" class="w-4 h-4 text-neutral-500"></i>
                            <span>Staff & Accounts</span>
                        </a>
                        <a href="{{ route('admin.rbac.index') }}" class="sidebar-link {{ request()->routeIs('admin.rbac*') ? 'active' : '' }}">
                            <i data-lucide="shield-check" class="w-4 h-4 text-neutral-500"></i>
                            <span>RBAC & Permissions</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                            <i data-lucide="palette" class="w-4 h-4 text-neutral-500"></i>
                            <span>Settings & Appearance</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Bottom User Card -->
        <div class="p-4 border-t border-[#E3E8E4] bg-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center font-bold text-xs uppercase shadow-2xs">
                        {{ substr(auth()->user()->name ?? 'Staff', 0, 2) }}
                    </div>
                    <div class="overflow-hidden">
                        <span class="block text-xs font-semibold text-[#202925] truncate">{{ auth()->user()->name ?? 'Admin Staff' }}</span>
                        <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-medium bg-brand-light text-brand capitalize">
                            {{ auth()->user()->roleRelation?->name ?? (auth()->user()->role ?? 'Manager') }}
                        </span>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-1.5 text-neutral-400 hover:text-rose-600 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Container (Wide responsive layout) -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
        <!-- Top App Bar -->
        <header class="sticky top-0 z-30 bg-white border-b border-[#E3E8E4] h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-2xs">
            <!-- Left: Toggle & Breadcrumb -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleSidebar()" class="lg:hidden p-2 text-neutral-600 hover:bg-slate-50 rounded-md">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="hidden sm:flex items-center gap-2 text-xs text-[#6B756F]">
                    <span>FloorOS ERP</span>
                    <span>/</span>
                    <span class="text-[#202925] font-semibold">@yield('page_title', 'Business Portal')</span>
                </div>
            </div>

            <!-- Middle: Global Search -->
            <div class="flex-1 max-w-md mx-4">
                <form action="{{ route('admin.search') }}" method="GET" class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-neutral-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products, SKUs, invoices, customers..." class="w-full pl-9 pr-4 py-1.5 text-xs bg-white border border-[#E3E8E4] rounded-lg focus:outline-none focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] transition">
                </form>
            </div>

            <!-- Right: Quick Actions & Live Store Link -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-brand bg-brand-light hover:bg-brand hover:text-white rounded-lg transition">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Public Catalog</span>
                </a>

                <!-- Quick New Transaction Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button type="button" onclick="document.getElementById('quickActionMenu').classList.toggle('hidden')" class="btn-brand text-xs py-1.5 px-3">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">New Action</span>
                    </button>
                    <div id="quickActionMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-[#E3E8E4] rounded-lg shadow-lg py-1.5 text-xs z-50">
                        <a href="{{ route('admin.products.create') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 text-[#202925]">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-brand"></i> Add New Product
                        </a>
                        <a href="{{ route('admin.inventory.adjust') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 text-[#202925]">
                            <i data-lucide="sliders" class="w-4 h-4 text-blue-600"></i> Adjust Inventory
                        </a>
                        <button type="button" onclick="document.getElementById('quickActionMenu').classList.add('hidden'); openCreditModal();" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-slate-50 text-[#202925] text-left">
                            <i data-lucide="plus" class="w-4 h-4 text-brand"></i> Record Money In (+)
                        </button>
                        <button type="button" onclick="document.getElementById('quickActionMenu').classList.add('hidden'); openDebitModal();" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-slate-50 text-[#202925] text-left">
                            <i data-lucide="minus" class="w-4 h-4 text-rose-600"></i> Record Money Out (-)
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main View Area (Expanded Wide Container) -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8 w-full max-w-7xl 2xl:max-w-[1700px] mx-auto">
            <!-- Flash Notification Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-600"></i>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-2xs">
                    <div class="flex items-center gap-2 mb-1 font-bold text-sm">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i> Form Validation Errors
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Admin Mobile Sticky Bottom Navigation Bar -->
    <nav class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-lg border-t border-[#E3E8E4] px-2 py-2 flex items-center justify-around lg:hidden shadow-[0_-4px_16px_rgba(0,0,0,0.05)]">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'text-brand' : 'text-neutral-400 hover:text-neutral-700' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'stroke-[2.5]' : '' }}"></i>
            <span class="text-[10px] font-semibold">Home</span>
        </a>

        <!-- Products -->
        <a href="{{ route('admin.products.index') }}" class="flex flex-col items-center gap-1 py-1 px-2 rounded-xl transition {{ request()->routeIs('admin.products*') ? 'text-brand' : 'text-neutral-400 hover:text-neutral-700' }}">
            <i data-lucide="layers" class="w-5 h-5 {{ request()->routeIs('admin.products*') ? 'stroke-[2.5]' : '' }}"></i>
            <span class="text-[10px] font-semibold">Products</span>
        </a>

        <!-- Money In (+) -->
        <button type="button" onclick="openCreditModal()" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl transition text-brand hover:opacity-90 active:scale-95" title="Record Money In (Deposit / Credit)">
            <div class="w-6 h-6 rounded-lg bg-brand-light text-brand flex items-center justify-center border border-brand/20">
                <i data-lucide="plus" class="w-4 h-4 stroke-[3]"></i>
            </div>
            <span class="text-[10px] font-bold text-brand">+ Money In</span>
        </button>

        <!-- Money Out (-) -->
        <button type="button" onclick="openDebitModal()" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl transition text-rose-600 hover:text-rose-700 active:scale-95" title="Record Money Out (Expense / Debit)">
            <div class="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 border border-rose-200/60 flex items-center justify-center">
                <i data-lucide="minus" class="w-4 h-4 stroke-[3]"></i>
            </div>
            <span class="text-[10px] font-bold text-rose-600">- Money Out</span>
        </button>

        <!-- Inventory / Stock -->
        <a href="{{ route('admin.inventory.index') }}" class="flex flex-col items-center gap-1 py-1 px-2 rounded-xl transition {{ request()->routeIs('admin.inventory*') ? 'text-brand' : 'text-neutral-400 hover:text-neutral-700' }}">
            <i data-lucide="boxes" class="w-5 h-5 {{ request()->routeIs('admin.inventory*') ? 'stroke-[2.5]' : '' }}"></i>
            <span class="text-[10px] font-semibold">Stock</span>
        </a>

        <!-- Full Drawer Menu -->
        <button type="button" onclick="toggleSidebar()" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl text-neutral-400 hover:text-neutral-700 transition">
            <i data-lucide="menu" class="w-5 h-5"></i>
            <span class="text-[10px] font-semibold">Menu</span>
        </button>
    </nav>

    <!-- Global Financial Modals (Credit & Debit) Accessible across all admin pages and mobile navigation -->
    @include('admin.accounting._modals')

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.setAttribute('data-lucide', 'eye-off');
                    if (window.createFloorIcons) window.createFloorIcons();
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.setAttribute('data-lucide', 'eye');
                    if (window.createFloorIcons) window.createFloorIcons();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.createFloorIcons) window.createFloorIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
