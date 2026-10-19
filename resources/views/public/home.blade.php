@extends('layouts.public')

@section('title', 'FloorOS — Beautiful Floors for Better Spaces')
@section('meta_description', 'Explore our curated range of engineered hardwood, 100% waterproof SPC, and architectural surfaces for residential and commercial spaces.')

@section('content')
<div class="space-y-6 sm:space-y-12 py-3 sm:py-6 bg-white">
    <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12">

        @php
            $slideCount = $heroSlides->count() > 0 ? $heroSlides->count() : 1;
        @endphp

        <!-- 1. Hero Sliding Banners Section (STRICT: Pure visual banner images with no text/content overlay) -->
        <section class="relative w-full rounded-2xl sm:rounded-3xl overflow-hidden shadow-xs border border-slate-200/80 bg-slate-900 group select-none" id="heroBannerCarousel" aria-label="Featured Flooring Banners">
            <style>
                .hero-banner-viewport {
                    position: relative;
                    width: 100%;
                    aspect-ratio: 1 / 1; /* Mobile <= 576px (Square 1:1) */
                    overflow: hidden;
                    background-color: #0f172a;
                }
                @media (min-width: 577px) {
                    .hero-banner-viewport {
                        aspect-ratio: 16 / 10; /* Tablet 577px - 991px (16:10) */
                    }
                }
                @media (min-width: 992px) {
                    .hero-banner-viewport {
                        aspect-ratio: 1350 / 620; /* Desktop >= 992px (~2.18:1) */
                    }
                }
                .hero-slides-track {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: {{ $slideCount * 100 }}%;
                    height: 100%;
                    display: flex;
                    transition: transform 450ms cubic-bezier(0.25, 1, 0.5, 1);
                    will-change: transform;
                }
                .hero-slide-item {
                    width: {{ 100 / $slideCount }}%;
                    min-width: {{ 100 / $slideCount }}%;
                    max-width: {{ 100 / $slideCount }}%;
                    height: 100%;
                    flex-shrink: 0;
                    position: relative;
                    overflow: hidden;
                }
                .hero-slide-item a,
                .hero-slide-item picture,
                .hero-slide-item picture img {
                    display: block;
                    width: 100%;
                    height: 100%;
                }
                .hero-slide-item picture img {
                    object-fit: cover;
                    object-position: center;
                }
            </style>

            <div class="hero-banner-viewport">
                <!-- Slides Track -->
                <div class="hero-slides-track" id="heroSlidesTrack">
                    @forelse($heroSlides as $index => $slide)
                        <div class="hero-slide-item" data-slide-index="{{ $index }}">
                            @if($slide->destination_url)
                                <a href="{{ $slide->destination_url }}" class="hero-slide-link cursor-pointer group/slide focus:outline-hidden" title="{{ $slide->alt_text ?: 'View Flooring Collection' }}">
                                    <picture>
                                        @if($slide->desktop_src)
                                            <source media="(min-width: 992px)" srcset="{{ $slide->desktop_src }}">
                                        @endif
                                        @if($slide->tablet_src)
                                            <source media="(min-width: 577px)" srcset="{{ $slide->tablet_src }}">
                                        @endif
                                        <img src="{{ $slide->mobile_src }}" 
                                             alt="{{ $slide->alt_text ?: 'Hero Flooring Banner ' . ($index + 1) }}" 
                                             class="select-none pointer-events-none transition duration-500 group-hover/slide:scale-[1.01]"
                                             loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                    </picture>
                                </a>
                            @else
                                <div class="w-full h-full">
                                    <picture>
                                        @if($slide->desktop_src)
                                            <source media="(min-width: 992px)" srcset="{{ $slide->desktop_src }}">
                                        @endif
                                        @if($slide->tablet_src)
                                            <source media="(min-width: 577px)" srcset="{{ $slide->tablet_src }}">
                                        @endif
                                        <img src="{{ $slide->mobile_src }}" 
                                             alt="{{ $slide->alt_text ?: 'Hero Flooring Banner ' . ($index + 1) }}" 
                                             class="select-none pointer-events-none"
                                             loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                    </picture>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="hero-slide-item">
                            <picture>
                                <source media="(min-width: 992px)" srcset="{{ asset('images/hero/slide-1-desktop.jpg') }}">
                                <source media="(min-width: 577px)" srcset="{{ asset('images/hero/slide-1-tablet.jpg') }}">
                                <img src="{{ asset('images/hero/slide-1-mobile.jpg') }}" 
                                     alt="FloorOS Premium Architectural Flooring" 
                                     class="select-none pointer-events-none">
                            </picture>
                        </div>
                    @endforelse
                </div>

                @if($heroSlides->count() > 1)
                    <!-- Navigation Arrows (Hidden on touch mobile screens to prevent obscuring banners; visible on tablet/desktop) -->
                    <button type="button" 
                            id="heroPrevBtn"
                            aria-label="Previous Slide" 
                            class="hidden sm:flex absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/85 hover:bg-white text-slate-800 hover:text-slate-950 shadow-md backdrop-blur-sm border border-white/60 items-center justify-center transition-all transform active:scale-90 opacity-0 group-hover:opacity-100 focus:opacity-100">
                        <i data-lucide="chevron-left" class="w-10 h-10 sm:w-6 sm:h-6 pointer-events-none"></i>
                    </button>
                    <button type="button" 
                            id="heroNextBtn"
                            aria-label="Next Slide" 
                            class="hidden sm:flex absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/85 hover:bg-white text-slate-800 hover:text-slate-950 shadow-md backdrop-blur-sm border border-white/60 items-center justify-center transition-all transform active:scale-90 opacity-0 group-hover:opacity-100 focus:opacity-100">
                        <i data-lucide="chevron-right" class="w-10 h-10 sm:w-6 sm:h-6 pointer-events-none"></i>
                    </button>

                    <!-- Indicators (Dots) -->
                    <div class="absolute bottom-2.5 sm:bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 sm:gap-2 px-3 py-1 sm:py-1.5 rounded-full bg-slate-950/50 backdrop-blur-md border border-white/20 shadow-sm" id="heroDotsContainer">
                        @foreach($heroSlides as $i => $slide)
                            <button type="button" 
                                    class="hero-dot h-1.5 sm:h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'w-5 sm:w-6 bg-white' : 'w-1.5 sm:w-2 bg-white/50 hover:bg-white/80' }}" 
                                    data-goto="{{ $i }}" 
                                    aria-label="Go to slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($heroSlides->count() > 1)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const track = document.getElementById('heroSlidesTrack');
                    const prevBtn = document.getElementById('heroPrevBtn');
                    const nextBtn = document.getElementById('heroNextBtn');
                    const dots = document.querySelectorAll('.hero-dot');
                    const carousel = document.getElementById('heroBannerCarousel');
                    const totalSlides = {{ $heroSlides->count() }};
                    let currentIndex = 0;
                    let autoplayTimer = null;
                    const autoplayInterval = 5000;

                    function updateSlider(index) {
                        if (index < 0) {
                            currentIndex = totalSlides - 1;
                        } else if (index >= totalSlides) {
                            currentIndex = 0;
                        } else {
                            currentIndex = index;
                        }

                        // Accurate percentage translation based on total slides
                        const percent = (currentIndex * 100) / totalSlides;
                        track.style.transform = `translateX(-${percent}%)`;

                        dots.forEach((dot, idx) => {
                            if (idx === currentIndex) {
                                dot.classList.remove('w-1.5', 'sm:w-2', 'bg-white/50');
                                dot.classList.add('w-5', 'sm:w-6', 'bg-white');
                            } else {
                                dot.classList.remove('w-5', 'sm:w-6', 'bg-white');
                                dot.classList.add('w-1.5', 'sm:w-2', 'bg-white/50');
                            }
                        });
                    }

                    function startAutoplay() {
                        stopAutoplay();
                        autoplayTimer = setInterval(() => {
                            updateSlider(currentIndex + 1);
                        }, autoplayInterval);
                    }

                    function stopAutoplay() {
                        if (autoplayTimer) {
                            clearInterval(autoplayTimer);
                            autoplayTimer = null;
                        }
                    }

                    if (prevBtn) {
                        prevBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            updateSlider(currentIndex - 1);
                            startAutoplay();
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            updateSlider(currentIndex + 1);
                            startAutoplay();
                        });
                    }

                    dots.forEach(dot => {
                        dot.addEventListener('click', (e) => {
                            e.stopPropagation();
                            const targetIdx = parseInt(dot.getAttribute('data-goto'));
                            updateSlider(targetIdx);
                            startAutoplay();
                        });
                    });

                    // Pause on hover for mouse devices only
                    if (window.matchMedia('(hover: hover)').matches) {
                        carousel.addEventListener('mouseenter', stopAutoplay);
                        carousel.addEventListener('mouseleave', startAutoplay);
                    }

                    // Touch Swipe Support for Mobile & Tablets
                    let startX = 0;
                    let startY = 0;
                    let moveX = 0;
                    let moveY = 0;
                    let isSwiping = false;
                    let swipeDistance = 0;

                    carousel.addEventListener('touchstart', (e) => {
                        if (!e.touches || e.touches.length === 0) return;
                        startX = e.touches[0].clientX;
                        startY = e.touches[0].clientY;
                        moveX = startX;
                        moveY = startY;
                        swipeDistance = 0;
                        isSwiping = true;
                        stopAutoplay();
                    }, { passive: true });

                    carousel.addEventListener('touchmove', (e) => {
                        if (!isSwiping || !e.touches || e.touches.length === 0) return;
                        moveX = e.touches[0].clientX;
                        moveY = e.touches[0].clientY;
                        swipeDistance = startX - moveX;
                    }, { passive: true });

                    carousel.addEventListener('touchend', (e) => {
                        if (!isSwiping) return;
                        isSwiping = false;

                        const diffY = Math.abs(startY - moveY);
                        // Trigger only if horizontal swipe dominates vertical scrolling and threshold exceeded
                        if (Math.abs(swipeDistance) > 35 && Math.abs(swipeDistance) > diffY) {
                            if (swipeDistance > 0) {
                                updateSlider(currentIndex + 1); // Swiped Left -> Next
                            } else {
                                updateSlider(currentIndex - 1); // Swiped Right -> Prev
                            }
                        }

                        // Delay autoplay resume
                        setTimeout(startAutoplay, 1000);
                    }, { passive: true });

                    // Prevent unintended click redirection when swiping on a linked banner
                    carousel.querySelectorAll('.hero-slide-link').forEach(link => {
                        link.addEventListener('click', (e) => {
                            if (Math.abs(swipeDistance) > 15) {
                                e.preventDefault();
                                e.stopPropagation();
                            }
                        });
                    });

                    // Keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                        const rect = carousel.getBoundingClientRect();
                        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                        if (isVisible) {
                            if (e.key === 'ArrowLeft') {
                                updateSlider(currentIndex - 1);
                                startAutoplay();
                            } else if (e.key === 'ArrowRight') {
                                updateSlider(currentIndex + 1);
                                startAutoplay();
                            }
                        }
                    });

                    if (window.createFloorIcons) window.createFloorIcons();
                    startAutoplay();
                });
            </script>
            @endif
        </section>

        <!-- 2. Texture Shortcuts Row -->
        <section class="py-2">
            <div class="grid grid-cols-5 gap-2.5 sm:gap-6 text-center">
                @php
                    $materialShortcuts = [
                        ['name' => 'Wood', 'slug' => 'wood-flooring', 'img' => '/images/flooring/category-wood.svg'],
                        ['name' => 'Laminate', 'slug' => 'laminate-flooring', 'img' => '/images/flooring/category-laminate.svg'],
                        ['name' => 'Vinyl', 'slug' => 'spc-flooring', 'img' => '/images/flooring/category-spc.svg'],
                        ['name' => 'Tile', 'slug' => 'carpet-tiles', 'img' => '/images/flooring/category-carpet.svg'],
                        ['name' => 'Carpet', 'slug' => 'carpet-tiles', 'img' => '/images/flooring/category-carpet.svg'],
                    ];
                @endphp

                @foreach($materialShortcuts as $item)
                    <a href="{{ route('catalog', ['category' => $item['slug']]) }}" class="group flex flex-col items-center gap-2">
                        <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-2xs group-hover:shadow-md group-hover:scale-105 group-hover:border-brand transition duration-200">
                            <img src="{{ $item['img'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs sm:text-sm font-semibold text-slate-700 group-hover:text-brand transition">
                            {{ $item['name'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- 3. Featured Collection with Interactive Swatches -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Our Collection
                    </h2>
                </div>
                <a href="{{ route('catalog') }}" class="text-xs sm:text-sm font-bold text-slate-600 hover:text-brand flex items-center gap-1 transition">
                    <span>View All</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-5 gap-3.5 sm:gap-5">
                @foreach($featuredProducts as $product)
                    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-2xs hover:shadow-md hover:border-brand transition flex flex-col justify-between group">
                        <div>
                            <!-- Lifestyle Image with Category Pill -->
                            <div class="aspect-4/3 relative overflow-hidden bg-white border-b border-slate-100">
                                <a href="{{ route('products.show', $product) }}" class="block w-full h-full">
                                    <img id="home-card-img-{{ $product->id }}" 
                                         src="{{ $product->primary_image ?: '/images/flooring/category-wood.svg' }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                </a>

                                <div class="absolute bottom-2.5 left-2.5 bg-black/60 backdrop-blur-xs text-white text-[10px] sm:text-[11px] font-medium px-2.5 py-0.5 rounded-full pointer-events-none">
                                    {{ $product->category->name }}
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-3 sm:p-4 space-y-1">
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-brand transition truncate">
                                    <a href="{{ route('products.show', $product) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <p class="text-[11px] text-slate-400 font-medium truncate">
                                    {{ $product->brand ?: ($product->category->name . ' Flooring') }}
                                </p>
                                <div class="pt-1 flex items-baseline gap-1">
                                    <span class="text-xs sm:text-sm font-extrabold text-slate-900 font-mono">
                                        ₹{{ number_format($product->min_price, 0) }}
                                    </span>
                                    <span class="text-[10px] sm:text-xs text-slate-400 font-normal">/ sq ft</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer: Interactive Swatch Dots -->
                        <div class="px-3 sm:px-4 pb-3 pt-1.5 flex items-center justify-between border-t border-slate-100">
                            <div class="flex items-center gap-1.5">
                                @forelse($product->publicShades->take(5) as $idx => $s)
                                    <button type="button" 
                                            onclick="selectCardShade(this, 'home-card-img-{{ $product->id }}', '{{ $s->image ?: $product->primary_image }}', '{{ addslashes($s->shade_name) }}')"
                                            title="{{ $s->shade_name }}" 
                                            class="shade-swatch-btn w-4 h-4 sm:w-5 sm:h-5 rounded-full border-2 border-white shadow-2xs overflow-hidden transition cursor-pointer {{ $idx === 0 ? 'ring-2 ring-brand scale-110' : 'ring-1 ring-slate-300 hover:ring-slate-400' }}">
                                        <img src="{{ $s->image ?: $product->primary_image }}" alt="{{ $s->shade_name }}" class="w-full h-full object-cover pointer-events-none">
                                    </button>
                                @empty
                                    <span class="w-3.5 h-3.5 rounded-full bg-[#8C532E]"></span>
                                    <span class="w-3.5 h-3.5 rounded-full bg-[#5C351B]"></span>
                                    <span class="w-3.5 h-3.5 rounded-full bg-accent"></span>
                                @endforelse
                            </div>
                            <a href="{{ route('products.show', $product) }}" class="text-slate-400 group-hover:text-brand group-hover:translate-x-0.5 transition" title="View details">
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 4. Promotional Banner: Vibrant Kelly Green + Sun Yellow Accents -->
        <section class="rounded-3xl bg-brand text-white p-6 sm:p-10 relative overflow-hidden shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center relative z-10">
                <div class="space-y-3">
                    <span class="inline-block px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider bg-accent text-slate-900 shadow-xs">
                        ★ NEW SEASON ARRIVALS
                    </span>
                    <h3 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                        Fresh Organic Shades,<br>Engineered for Life
                    </h3>
                    <p class="text-xs sm:text-sm text-white/90 leading-relaxed max-w-sm">
                        Discover authentic European matte oak, click-lock herringbone, and ultra-durable stone polymer cores.
                    </p>
                    <div class="pt-2 flex items-center gap-3">
                        <a href="{{ route('catalog') }}" class="btn-accent text-xs sm:text-sm py-2.5 px-5 shadow-xs">
                            <span>Explore New Arrivals</span>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                        <button type="button" onclick="openContactModal()" class="text-xs sm:text-sm text-white/90 hover:text-white font-semibold underline underline-offset-4">
                            Book Showroom Consult
                        </button>
                    </div>
                </div>

                <!-- Angled Planks Graphic -->
                <div class="relative flex justify-center sm:justify-end">
                    <div class="w-52 h-36 sm:w-72 sm:h-44 rounded-2xl overflow-hidden shadow-2xl border-2 border-white/30 transform sm:rotate-2 hover:rotate-0 transition duration-300">
                        <img src="/images/flooring/category-wood.svg" alt="New Wood Flooring Shades" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Quick Clean Assurance Features (Pure white cards) -->
        <section class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-5 pt-2">
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 flex flex-col items-center gap-3.5 shadow-2xs hover:border-brand transition">
                <div class="w-10 h-10 rounded-xl  text-brand flex items-center justify-center shrink-0">
                    <i data-lucide="shield-check" class="w-10 h-10"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">25-Yr Mill Warranty</h4>
                    <span class="text-[10px] sm:text-xs text-slate-400">Residential & Commercial</span>
                </div>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 flex flex-col items-center gap-3.5 shadow-2xs hover:border-brand transition">
                <div class="w-10 h-10 rounded-xl  text-brand flex items-center justify-center shrink-0">
                    <i data-lucide="droplets" class="w-10 h-10"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">100% Waterproof</h4>
                    <span class="text-[10px] sm:text-xs text-slate-400">SPC & Rigid Stone Cores</span>
                </div>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 flex flex-col items-center gap-3.5 shadow-2xs hover:border-brand transition">
                <div class="w-10 h-10 rounded-xl  text-brand flex items-center justify-center shrink-0">
                    <i data-lucide="sparkles" class="w-10 h-10"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">Tactile Sample Kits</h4>
                    <span class="text-[10px] sm:text-xs text-slate-400">Direct Express Delivery</span>
                </div>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 flex flex-col items-center gap-3.5 shadow-2xs hover:border-brand transition">
                <div class="w-10 h-10 rounded-xl  text-brand flex items-center justify-center shrink-0">
                    <i data-lucide="truck" class="w-10 h-10"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">Express Delivery</h4>
                    <span class="text-[10px] sm:text-xs text-slate-400">Showroom Stock Ready</span>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection
