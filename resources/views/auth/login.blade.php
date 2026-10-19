<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login — FloorOS Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        {!! $themeCss !!}
    </style>
</head>
<body class="min-h-screen bg-white text-[#202925] flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden grid grid-cols-1 md:grid-cols-2">
        <!-- Left Side: Brand Imagery -->
        <div class="hidden md:flex flex-col justify-between p-8 bg-brand text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-20 pointer-events-none">
                <img src="/images/flooring/hero-flooring.svg" alt="Flooring" class="w-full h-full object-cover">
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white/20 backdrop-blur-xs flex items-center justify-center text-white shadow-xs">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="8" height="8" rx="1.5"></rect>
                            <rect x="13" y="3" width="8" height="8" rx="1.5"></rect>
                            <rect x="3" y="13" width="8" height="8" rx="1.5"></rect>
                            <rect x="13" y="13" width="8" height="8" rx="1.5"></rect>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">Floor<span class="text-accent">OS</span></span>
                </div>
            </div>

            <div class="relative z-10 my-auto py-10 space-y-4">
                <span class="text-xs uppercase tracking-widest text-accent font-bold">Store Management System</span>
                <h2 class="text-2xl font-bold leading-tight">Connected Inventory, Sales & Accounting</h2>
                <p class="text-xs text-white/90 leading-relaxed">
                    Single source of truth powering your physical store operations, inventory balances, and public customer discovery catalog.
                </p>
            </div>

            <div class="relative z-10 text-[11px] text-white/80 flex items-center justify-between">
                <span>© {{ date('Y') }} FloorOS Atelier</span>
                <a href="{{ route('home') }}" class="text-accent hover:underline flex items-center gap-1 font-bold">
                    Storefront <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>

        <!-- Right Side: Authentication Form -->
        <div class="p-8 sm:p-10 flex flex-col justify-between">
            <div>
                <div class="md:hidden flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center text-white">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                    </div>
                    <span class="text-lg font-bold text-[#202925]">Floor<span class="text-brand">OS</span></span>
                </div>

                <h1 class="text-2xl font-bold text-[#202925]">Staff Portal Access</h1>
                <p class="text-xs text-[#6B756F] mt-1">Sign in with authorized staff credentials to manage the flooring business.</p>

                @if($errors->any())
                    <div class="mt-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('admin.login.submit') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#202925] mb-1">Email Address</label>
                        <input type="email" id="emailInput" name="email" value="{{ old('email') }}" placeholder="name@company.com" required autocomplete="email" class="w-full input-custom text-sm">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-[#202925]">Password</label>
                        </div>
                        <div class="relative">
                            <input type="password" id="passwordInput" name="password" value="" placeholder="Enter your password" required autocomplete="current-password" class="w-full input-custom text-sm pr-10">
                            <button type="button" onclick="togglePasswordVisibility('passwordInput', 'passwordToggleIcon')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand transition p-1" title="Toggle password visibility" aria-label="Toggle password visibility">
                                <i data-lucide="eye" id="passwordToggleIcon" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-2 text-[#6B756F] cursor-pointer">
                            <input type="checkbox" name="remember" value="1" checked class="rounded border-[#E3E8E4] text-brand focus:ring-brand">
                            <span>Keep me signed in (up to 1 week)</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full btn-brand py-2.5 text-sm font-semibold justify-center">
                        <i data-lucide="log-in" class="w-4 h-4"></i> Sign In to Dashboard
                    </button>
                </form>
            </div>

            <!-- Footer Navigation Link -->
            <div class="mt-8 pt-6 border-t border-[#E3E8E4] flex items-center justify-between text-xs text-[#6B756F]">
                <span>FloorOS Secure Staff Authentication</span>
                <a href="{{ route('home') }}" class="text-brand hover:underline font-semibold flex items-center gap-1">
                    ← Back to Storefront
                </a>
            </div>
        </div>
    </div>

    <script>
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
</body>
</html>
