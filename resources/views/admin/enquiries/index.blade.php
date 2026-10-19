@extends('layouts.admin')

@section('title', 'Customer Enquiries — FloorOS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text-primary tracking-tight">Public Messages</h1>
            <p class="text-xs text-text-secondary mt-1">Quotations requested by website visitors for specific flooring collections and shades.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <a href="{{ route('admin.enquiries.index') }}" class="px-3 py-1.5 rounded-full font-semibold transition-colors {{ !request('status') ? 'bg-primary text-white' : 'bg-surface border border-border-default text-text-secondary hover:bg-slate-50' }}">
            All ({{ $statusCounts['all'] }})
        </a>
        <a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="px-3 py-1.5 rounded-full font-semibold transition-colors flex items-center gap-1.5 {{ request('status') === 'new' ? 'bg-rose-600 text-white' : 'bg-surface border border-border-default text-text-secondary hover:bg-slate-50' }}">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> New ({{ $statusCounts['new'] }})
        </a>
        <a href="{{ route('admin.enquiries.index', ['status' => 'contacted']) }}" class="px-3 py-1.5 rounded-full font-semibold transition-colors {{ request('status') === 'contacted' ? 'bg-primary text-white' : 'bg-surface border border-border-default text-text-secondary hover:bg-slate-50' }}">
            Contacted ({{ $statusCounts['contacted'] }})
        </a>
        <a href="{{ route('admin.enquiries.index', ['status' => 'quotation_sent']) }}" class="px-3 py-1.5 rounded-full font-semibold transition-colors {{ request('status') === 'quotation_sent' ? 'bg-primary text-white' : 'bg-surface border border-border-default text-text-secondary hover:bg-slate-50' }}">
            Quote Sent ({{ $statusCounts['quotation_sent'] }})
        </a>
        <a href="{{ route('admin.enquiries.index', ['status' => 'converted']) }}" class="px-3 py-1.5 rounded-full font-semibold transition-colors {{ request('status') === 'converted' ? 'bg-emerald-600 text-white' : 'bg-surface border border-border-default text-text-secondary hover:bg-slate-50' }}">
            Converted ({{ $statusCounts['converted'] }})
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-4 shadow-xs">
        <form action="{{ route('admin.enquiries.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name, phone, enquiry ref..." class="w-full pl-9 pr-3 py-2 text-xs border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">
            </div>

            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-text-primary text-xs font-semibold rounded-[var(--radius-base)] transition-colors">
                Search
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.enquiries.index') }}" class="px-3 py-2 text-xs text-text-secondary hover:text-text-primary self-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Enquiries Table -->
    <div class="bg-white rounded-[var(--radius-base)] border border-border-default shadow-xs overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="border-b border-border-default bg-white text-text-secondary font-semibold">
                        <th class="py-3 px-4">Ref #</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Requested Flooring</th>
                        <th class="py-3 px-4 text-center">Approx Area</th>
                        <th class="py-3 px-4 text-center">Pref. Contact</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Assigned</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-default text-text-primary">
                    @forelse($enquiries as $enquiry)
                        <tr class="hover:bg-slate-50/50 transition-colors {{ $enquiry->status === 'new' ? 'bg-amber-50/20 font-medium' : '' }}">
                            <td class="py-3.5 px-4 font-mono font-bold text-text-primary">
                                <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="hover:text-primary transition-colors">
                                    {{ $enquiry->enquiry_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-text-secondary">{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-text-primary">{{ $enquiry->name }}</div>
                                <div class="text-[11px] text-text-secondary">{{ $enquiry->phone }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($enquiry->product)
                                    <div class="font-semibold text-text-primary">{{ $enquiry->product->name }}</div>
                                    @if($enquiry->shade)
                                        <div class="text-[11px] text-text-secondary flex items-center gap-1 mt-0.5">
                                            <span class="w-2 h-2 rounded-full border border-slate-300" style="background-color: {{ $enquiry->shade->hex_color ?? '#cbd5e1' }}"></span>
                                            <span>{{ $enquiry->shade->name }}</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-text-secondary">General Store Enquiry</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono">
                                {{ $enquiry->area_sqft ? number_format($enquiry->area_sqft) . ' sq ft' : '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-center capitalize">
                                <span class="px-2 py-0.5 rounded text-[11px] bg-slate-100 text-slate-700">
                                    {{ $enquiry->preferred_contact ?: 'Phone' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($enquiry->status === 'new')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        New
                                    </span>
                                @elseif($enquiry->status === 'converted')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Converted
                                    </span>
                                @elseif($enquiry->status === 'quotation_sent')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        Quote Sent
                                    </span>
                                @elseif($enquiry->status === 'contacted')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Contacted
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ ucfirst($enquiry->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-text-secondary">
                                {{ $enquiry->assignee->name ?? 'Unassigned' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="p-1.5 text-text-secondary hover:text-primary rounded hover:bg-slate-100 transition-colors inline-block" title="View Enquiry">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-xs text-text-secondary">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="message-square" class="w-8 h-8 text-slate-300"></i>
                                    <p>No customer enquiries found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enquiries->hasPages())
            <div class="p-4 border-t border-border-default">
                {{ $enquiries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
