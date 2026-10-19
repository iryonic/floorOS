@extends('layouts.admin')

@section('title', 'Stock Movement Audit History')
@section('page_title', 'Stock Movement History')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">Stock Movement History</h1>
            <p class="text-xs text-[#6B756F]">Immutable audit trail of purchases, sales deductions, physical counts, and wastage adjustments.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Inventory Balances
            </a>
            <a href="{{ route('admin.inventory.adjust') }}" class="btn-brand text-xs py-2 px-4">
                <i data-lucide="plus" class="w-4 h-4"></i> New Adjustment
            </a>
        </div>
    </div>

    <!-- Movement Filter Bar -->
    <div class="card-surface p-4">
        <form action="{{ route('admin.inventory.movements') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-neutral-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ref or SKU..." class="w-full pl-9 pr-3 py-2 text-xs input-custom">
            </div>

            <div>
                <select name="type" onchange="this.form.submit()" class="w-full py-2 text-xs input-custom">
                    <option value="">All Movement Types</option>
                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale Deduction</option>
                    <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Purchase Receipt</option>
                    <option value="opening" {{ request('type') === 'opening' ? 'selected' : '' }}>Opening Balance</option>
                    <option value="damage" {{ request('type') === 'damage' ? 'selected' : '' }}>Damaged / Wastage</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Manual Adjustment</option>
                    <option value="audit" {{ request('type') === 'audit' ? 'selected' : '' }}>Physical Audit</option>
                </select>
            </div>

            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()" class="w-full py-2 text-xs input-custom" placeholder="From Date">
            </div>

            <div>
                <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()" class="w-full py-2 text-xs input-custom" placeholder="To Date">
            </div>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="card-surface overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                    <tr>
                        <th class="p-3 pl-5">Date & Time</th>
                        <th class="p-3">Reference #</th>
                        <th class="p-3">Product Shade & SKU</th>
                        <th class="p-3">Type</th>
                        <th class="p-3 text-right">Movement</th>
                        <th class="p-3 text-right">Previous</th>
                        <th class="p-3 text-right font-bold">New Balance</th>
                        <th class="p-3">Reason / Details</th>
                        <th class="p-3 pr-5 text-right">Staff</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($movements as $m)
                        <tr class="hover:bg-emerald-50/40 transition">
                            <td class="p-3 pl-5 text-neutral-500 whitespace-nowrap">
                                {{ $m->occurred_at ? $m->occurred_at->format('d M Y, h:i A') : $m->created_at->format('d M Y') }}
                            </td>
                            <td class="p-3 font-mono font-bold text-[#202925] whitespace-nowrap">
                                {{ $m->reference_number }}
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-[#202925]">{{ $m->shade->product->name ?? 'Flooring' }}</div>
                                <div class="text-[#6B756F] text-[11px]">{{ $m->shade->shade_name ?? '' }} ({{ $m->sku }})</div>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-neutral-100 text-neutral-700">
                                    {{ $m->type_label }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-bold whitespace-nowrap {{ $m->direction === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $m->direction === 'in' ? '+' : '-' }}{{ number_format($m->quantity, 1) }} {{ $m->unit }}
                            </td>
                            <td class="p-3 text-right text-neutral-400">
                                {{ number_format($m->previous_stock, 1) }}
                            </td>
                            <td class="p-3 text-right font-bold text-[#202925]">
                                {{ number_format($m->new_stock, 1) }} {{ $m->unit }}
                            </td>
                            <td class="p-3 text-[#4B5563] max-w-xs truncate" title="{{ $m->reason }}">
                                {{ $m->reason ?: '—' }}
                            </td>
                            <td class="p-3 pr-5 text-right text-neutral-500">
                                {{ $m->user->name ?? 'System' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#E3E8E4]">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection
