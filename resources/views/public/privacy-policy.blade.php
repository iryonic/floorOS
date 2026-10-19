@extends('layouts.public')

@section('title', 'Privacy Policy — FloorOS')

@section('content')
<div class="bg-white min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-surface p-8 sm:p-12 space-y-6 text-[#202925]">
            <h1 class="text-3xl font-extrabold">Privacy Policy</h1>
            <p class="text-xs text-[#6B756F]">Last updated: October 2026</p>

            <div class="prose text-xs text-[#4B5563] space-y-4 leading-relaxed">
                <p>Welcome to FloorOS. We respect your privacy and are committed to protecting the personal information you share with us through our website and showroom interactions.</p>

                <h3 class="text-sm font-bold text-[#202925]">1. Information We Collect</h3>
                <p>When you browse our flooring catalog, submit an enquiry, or request an architectural sample kit, we collect your name, phone number, email address, and project specifications. We do not process unsolicited third-party marketing trackers.</p>

                <h3 class="text-sm font-bold text-[#202925]">2. How We Use Your Information</h3>
                <p>Your details are used solely to provide tailored quotation calculations, arrange sample deliveries, coordinate installation logistics, and address your customer service inquiries.</p>

                <h3 class="text-sm font-bold text-[#202925]">3. Contact & Inquiries</h3>
                <p>If you have any questions regarding your data privacy, please contact our showroom desk at {{ $storeEmail }} or call {{ $storePhone }}.</p>
            </div>
        </div>
    </div>
</div>
@endsection
