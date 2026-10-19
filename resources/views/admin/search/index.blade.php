@extends('layouts.admin')

@section('title', 'Search Results for "' . $q . '" — FloorOS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-text-primary tracking-tight">Global Search Results</h1>
            <p class="text-xs text-text-secondary mt-1">Showing matching records across catalog products, shade SKUs, enquiries, and cash transactions for <span class="font-bold text-text-primary">"{{ $q }}"</span>.</p>
        </div>
    </div>

    @php
        $totalResults = $products->count() + $shades->count() + $enquiries->count() + $transactions->count();
    @endphp

    @if($totalResults === 0)
        <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-12 text-center text-xs text-text-secondary">
            <i data-lucide="search-x" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
            <p class="text-sm font-bold text-text-primary">No matching records found</p>
            <p class="mt-1">Try searching by product name, shade SKU, customer enquiry, or transaction description.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Products & Collections -->
            @if($products->isNotEmpty())
                <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-text-primary uppercase tracking-wider flex items-center gap-1.5 border-b border-border-default pb-2">
                        <i data-lucide="package" class="w-4 h-4 text-brand"></i> Products & Collections ({{ $products->count() }})
                    </h3>
                    <div class="divide-y divide-border-default text-xs">
                        @foreach($products as $p)
                            <a href="{{ route('admin.products.edit', $p) }}" class="py-2 flex items-center justify-between hover:bg-slate-50 -mx-2 px-2 rounded transition-colors">
                                <div>
                                    <span class="font-bold text-text-primary block">{{ $p->name }}</span>
                                    <span class="text-[11px] text-text-secondary">{{ $p->brand }} {{ $p->collection_name ? '• ' . $p->collection_name : '' }}</span>
                                </div>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Shades & SKUs -->
            @if($shades->isNotEmpty())
                <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-text-primary uppercase tracking-wider flex items-center gap-1.5 border-b border-border-default pb-2">
                        <i data-lucide="layers" class="w-4 h-4 text-brand"></i> Shades & SKUs ({{ $shades->count() }})
                    </h3>
                    <div class="divide-y divide-border-default text-xs">
                        @foreach($shades as $s)
                            <a href="{{ route('admin.inventory.index', ['search' => $s->sku]) }}" class="py-2 flex items-center justify-between hover:bg-slate-50 -mx-2 px-2 rounded transition-colors">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full border border-slate-300" style="background-color: {{ $s->hex_color ?? '#cbd5e1' }}"></span>
                                        <span class="font-bold text-text-primary">{{ $s->product->name ?? 'Flooring' }} — {{ $s->name }}</span>
                                    </div>
                                    <span class="text-[11px] font-mono text-slate-500">SKU: {{ $s->sku }} • Stock: {{ number_format($s->stock_quantity, 1) }} {{ $s->unit }}</span>
                                </div>
                                <span class="font-bold text-text-primary">₹{{ number_format($s->selling_price, 2) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Financial Transactions -->
            @if($transactions->isNotEmpty())
                <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-text-primary uppercase tracking-wider flex items-center gap-1.5 border-b border-border-default pb-2">
                        <i data-lucide="wallet" class="w-4 h-4 text-emerald-600"></i> Cash Flow & Transactions ({{ $transactions->count() }})
                    </h3>
                    <div class="divide-y divide-border-default text-xs">
                        @foreach($transactions as $txn)
                            <a href="{{ route('admin.accounting.index') }}" class="py-2 flex items-center justify-between hover:bg-slate-50 -mx-2 px-2 rounded transition-colors">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold {{ $txn->type === 'credit' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                            {{ strtoupper($txn->type) }}
                                        </span>
                                        <span class="font-medium text-text-primary">{{ $txn->description }}</span>
                                    </div>
                                    <span class="text-[11px] text-text-secondary block">{{ $txn->transaction_date->format('d M Y') }} • {{ $txn->payment_method ? ucfirst($txn->payment_method) : 'Cash' }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-xs {{ $txn->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $txn->type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Customer Enquiries -->
            @if($enquiries->isNotEmpty())
                <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-text-primary uppercase tracking-wider flex items-center gap-1.5 border-b border-border-default pb-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-purple-600"></i> Customer Enquiries ({{ $enquiries->count() }})
                    </h3>
                    <div class="divide-y divide-border-default text-xs">
                        @foreach($enquiries as $enq)
                            <a href="{{ route('admin.enquiries.show', $enq) }}" class="py-2 flex items-center justify-between hover:bg-slate-50 -mx-2 px-2 rounded transition-colors">
                                <div>
                                    <span class="font-mono font-bold text-text-primary">{{ $enq->enquiry_number }}</span>
                                    <span class="text-[11px] text-text-secondary block">{{ $enq->name }} ({{ $enq->phone }})</span>
                                </div>
                                <span class="capitalize px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ str_replace('_', ' ', $enq->status) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
