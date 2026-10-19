@extends('layouts.admin')

@section('title', 'Enquiry ' . $enquiry->enquiry_number . ' — FloorOS')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-text-secondary mb-1">
                <a href="{{ route('admin.enquiries.index') }}" class="hover:text-primary transition-colors">Customer Enquiries</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-text-primary font-medium">{{ $enquiry->enquiry_number }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-text-primary tracking-tight">{{ $enquiry->enquiry_number }}</h1>
                <span class="capitalize px-2.5 py-0.5 rounded-full text-xs font-semibold
                    {{ $enquiry->status === 'new' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                    {{ $enquiry->status === 'converted' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                    {{ $enquiry->status === 'quotation_sent' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                    {{ $enquiry->status === 'contacted' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                ">
                    {{ str_replace('_', ' ', $enquiry->status) }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">

            @if($enquiry->phone)
                @php
                    $waText = urlencode("Hello {$enquiry->name}, thank you for enquiring about our flooring collections at " . config('app.name', 'FloorOS') . "! We received your request for {$enquiry->product?->name} ({$enquiry->shade?->name}). How can we assist you today?");
                    $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $enquiry->phone) . "?text={$waText}";
                @endphp
                <a href="{{ $waUrl }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 border border-emerald-200 rounded-[var(--radius-base)] bg-emerald-50 text-emerald-800 text-xs font-semibold hover:bg-emerald-100 transition-colors shadow-xs">
                    <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i>
                    Chat on WhatsApp
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Customer & Requested Product Info (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Details Card -->
            <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-text-primary uppercase tracking-wider border-b border-border-default pb-3 flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-primary"></i> Customer Contact Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-text-secondary block">Name:</span>
                        <span class="font-bold text-text-primary text-sm">{{ $enquiry->name }}</span>
                    </div>
                    <div>
                        <span class="text-text-secondary block">Phone Number:</span>
                        <span class="font-bold text-text-primary">{{ $enquiry->phone }}</span>
                    </div>
                    <div>
                        <span class="text-text-secondary block">Email Address:</span>
                        <span class="text-text-primary">{{ $enquiry->email ?: 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="text-text-secondary block">Preferred Contact Channel:</span>
                        <span class="capitalize font-semibold text-text-primary">{{ $enquiry->preferred_contact ?: 'Phone' }}</span>
                    </div>
                </div>
            </div>

            <!-- Requested Flooring Material -->
            <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-text-primary uppercase tracking-wider border-b border-border-default pb-3 flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4 text-primary"></i> Requested Flooring Specifications
                </h3>

                @if($enquiry->product)
                    <div class="flex items-start gap-4">
                        @if($enquiry->product->primary_image)
                            <img src="{{ $enquiry->product->primary_image }}" alt="{{ $enquiry->product->name }}" class="w-20 h-20 rounded object-cover border border-border-default">
                        @else
                            <div class="w-20 h-20 rounded bg-slate-100 flex items-center justify-center text-slate-400">
                                <i data-lucide="image" class="w-8 h-8"></i>
                            </div>
                        @endif

                        <div class="space-y-1 text-xs">
                            <h4 class="text-sm font-bold text-text-primary">{{ $enquiry->product->name }}</h4>
                            <p class="text-text-secondary">Category: {{ $enquiry->product->category->name ?? 'Flooring' }}</p>

                            @if($enquiry->shade)
                                <div class="flex items-center gap-2 pt-1">
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-300" style="background-color: {{ $enquiry->shade->hex_color ?? '#cbd5e1' }}"></span>
                                    <span class="font-semibold text-text-primary">Selected Shade: {{ $enquiry->shade->name }}</span>
                                    <span class="font-mono text-slate-500">({{ $enquiry->shade->sku }})</span>
                                </div>
                                <p class="text-primary font-bold">List Price: ₹{{ number_format($enquiry->shade->selling_price, 2) }} / {{ $enquiry->shade->unit }}</p>
                            @endif

                            @if($enquiry->area_sqft)
                                <p class="pt-1 font-semibold text-slate-700">Estimated Project Coverage: {{ number_format($enquiry->area_sqft) }} sq ft</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-xs text-text-secondary">The visitor submitted a general store inquiry without selecting a specific material.</p>
                @endif

                @if($enquiry->message)
                    <div class="mt-4 pt-3 border-t border-border-default">
                        <span class="text-xs font-semibold text-text-secondary block mb-1">Customer's Message:</span>
                        <div class="p-3 bg-slate-50 rounded text-xs text-text-primary leading-relaxed border border-border-default">
                            "{{ $enquiry->message }}"
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Status, Assignment & Internal Notes (1 Col) -->
        <div class="space-y-6">
            <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-text-primary uppercase tracking-wider border-b border-border-default pb-3">
                    Enquiry Workflow & Assignment
                </h3>

                <form action="{{ route('admin.enquiries.status', $enquiry) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-text-primary mb-1">Enquiry Status <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full text-xs px-3 py-2 border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">
                            <option value="new" {{ $enquiry->status === 'new' ? 'selected' : '' }}>New (Unread)</option>
                            <option value="contacted" {{ $enquiry->status === 'contacted' ? 'selected' : '' }}>Contacted (Client called)</option>
                            <option value="quotation_sent" {{ $enquiry->status === 'quotation_sent' ? 'selected' : '' }}>Quotation Sent</option>
                            <option value="follow_up" {{ $enquiry->status === 'follow_up' ? 'selected' : '' }}>Follow-up Required</option>
                            <option value="converted" {{ $enquiry->status === 'converted' ? 'selected' : '' }}>Converted (Sale confirmed)</option>
                            <option value="closed" {{ $enquiry->status === 'closed' ? 'selected' : '' }}>Closed / Lost</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-primary mb-1">Assign Staff Member</label>
                        <select name="assigned_to" class="w-full text-xs px-3 py-2 border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">
                            <option value="">Unassigned</option>
                            @foreach($staffUsers as $user)
                                <option value="{{ $user->id }}" {{ $enquiry->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-primary mb-1">Internal Sales Notes</label>
                        <textarea name="internal_notes" rows="4" placeholder="Client site visit scheduled, sample swatch sent..." class="w-full text-xs px-3 py-2 border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">{{ old('internal_notes', $enquiry->internal_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2 bg-primary hover:bg-primary-dark text-white rounded-[var(--radius-base)] text-xs font-semibold transition-colors shadow-xs">
                        Update Status & Notes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
