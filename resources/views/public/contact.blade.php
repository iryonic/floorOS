@extends('layouts.public')

@section('title', 'Showroom Location & Contact — FloorOS')
@section('meta_description', 'Visit the FloorOS flooring design atelier. Speak with our architectural flooring consultants.')

@section('content')
<div class="bg-white min-h-screen py-10 sm:py-14">
    <div class="max-w-7xl 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs uppercase tracking-widest font-bold text-brand">Showroom & Atelier</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Visit Our Design Atelier</h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Experience tactile full-scale plank displays, chevron parquet mockups, and waterproof stone-core SPC demonstration tanks.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Info Card (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 sm:p-8 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Showroom Address & Hours</h3>
                    
                    <div class="space-y-4 text-xs text-[#4B5563]">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-light text-brand flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="block font-bold text-[#202925] text-sm">Flagship Gallery</span>
                                <p class="mt-0.5 leading-relaxed">{{ $storeAddress }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-light text-brand flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="block font-bold text-[#202925] text-sm">Operating Hours</span>
                                <p class="mt-0.5 leading-relaxed">{{ \App\Models\Setting::get('store_hours', 'Mon - Sat: 10:00 AM - 8:00 PM | Sun: Appointment') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-light text-brand flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="block font-bold text-[#202925] text-sm">Direct Phone & WhatsApp</span>
                                <p class="mt-0.5 leading-relaxed font-mono">{{ $storePhone }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-light text-brand flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="block font-bold text-[#202925] text-sm">Official Email</span>
                                <p class="mt-0.5 leading-relaxed">{{ $storeEmail }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#E3E8E4] flex gap-3">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storePhone) }}" target="_blank" class="w-full btn-brand text-xs justify-center py-2.5 shadow-xs">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp Chat
                        </a>
                        <a href="tel:{{ $storePhone }}" class="w-full btn-secondary text-xs justify-center py-2.5">
                            <i data-lucide="phone" class="w-4 h-4 text-brand"></i> Direct Call
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Send Direct Message Form (7 Cols) -->
            <div class="lg:col-span-7">
                <div class="card-surface p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-2xs space-y-5">
                    <div>
                        <h3 class="text-xl font-bold text-[#202925]">Connect With An In-Store Specialist</h3>
                        <p class="text-xs text-[#6B756F] mt-1">Whether you are an architect detailing specifications or a homeowner planning a living room renovation, our team is ready to assist.</p>
                    </div>

                    <form action="{{ route('enquiry.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#202925] mb-1">Your Name *</label>
                                <input type="text" name="name" required placeholder="e.g. Vikramaditya" class="w-full input-custom text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#202925] mb-1">Phone Number *</label>
                                <input type="text" name="phone" required placeholder="+91 98765 43210" class="w-full input-custom text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#202925] mb-1">Email Address</label>
                                <input type="email" name="email" placeholder="vikram@example.com" class="w-full input-custom text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#202925] mb-1">Preferred Response Method</label>
                                <select name="preferred_contact" class="w-full input-custom text-sm">
                                    <option value="whatsapp">WhatsApp Message</option>
                                    <option value="call">Phone Call</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#202925] mb-1">Message or Project Query</label>
                            <textarea name="message" rows="4" placeholder="Mention your requirements, preferred flooring material (Hardwood, SPC, Vinyl, Laminate), project timeline, or appointment request..." class="w-full input-custom text-sm"></textarea>
                        </div>

                        <button type="submit" class="btn-brand py-3 px-8 text-xs font-bold shadow-md">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Send Message to Showroom
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
