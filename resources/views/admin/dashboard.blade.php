@extends('layouts.admin')

@section('title', 'Business Dashboard')
@section('page_title', 'Operational Overview')

@section('content')
<div class="space-y-8">
    
    <!-- Top Greeting & Quick Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#202925]">Business Dashboard</h1>
            <p class="text-xs text-[#6B756F] mt-0.5">Real-time overview of catalog, inventory reserves, cash flow, and customer enquiries.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openCreditModal()" class="btn-brand text-xs py-2 px-3 shadow-xs flex items-center gap-1.5 font-bold">
                <i data-lucide="plus" class="w-3.5 h-3.5 stroke-[3]"></i> + Money In
            </button>
            <button type="button" onclick="openDebitModal()" class="btn-secondary text-xs py-2 px-3 text-rose-600 hover:text-rose-700 hover:border-rose-300 hover:bg-rose-50/50 flex items-center gap-1.5 font-bold">
                <i data-lucide="minus" class="w-3.5 h-3.5 stroke-[3] text-rose-600"></i> - Money Out
            </button>
            <a href="{{ route('admin.products.create') }}" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5 text-brand"></i> Add Product
            </a>
            <a href="{{ route('admin.inventory.adjust') }}" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5">
                <i data-lucide="sliders" class="w-3.5 h-3.5 text-blue-600"></i> Adjust Stock
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5">
                <i data-lucide="boxes" class="w-3.5 h-3.5 text-neutral-600"></i> Inventory
            </a>
        </div>
    </div>

    <!-- 6 KPI Metric Cards (Responsive Grid) -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
        <!-- 1. Catalog Products -->
        <div class="card-surface p-3.5 sm:p-4 border-l-4 border-l-brand hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] truncate" title="Catalog Products">Products</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-brand-light text-brand border border-brand/20 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="package" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-brand"></i>
                </div>
            </div>
            <div>
                <div class="text-[15px] sm:text-lg lg:text-xl font-extrabold tracking-tight text-[#202925] truncate">
                    {{ $totalProducts }} <span class="text-xs font-semibold text-neutral-500">Items</span>
                </div>
                <span class="text-[10px] text-[#6B756F] block mt-0.5 sm:mt-1 truncate">Published collections</span>
            </div>
        </div>

        <!-- 2. Active Shades -->
        <div class="card-surface p-3.5 sm:p-4 border-l-4 border-l-teal-500 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] truncate" title="Active Shades">Active Shades</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="layers" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-teal-600"></i>
                </div>
            </div>
            <div>
                <div class="text-[15px] sm:text-lg lg:text-xl font-extrabold tracking-tight text-[#202925] truncate">
                    {{ $totalShades }} <span class="text-xs font-semibold text-neutral-500">SKUs</span>
                </div>
                <span class="text-[10px] text-[#6B756F] block mt-0.5 sm:mt-1 truncate">Material swatches</span>
            </div>
        </div>

        <!-- 3. Current Inventory Value -->
        <div class="card-surface p-3.5 sm:p-4 border-l-4 border-l-blue-500 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] truncate" title="Inventory Value">Stock Value</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="boxes" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600"></i>
                </div>
            </div>
            <div>
                <div class="text-[15px] sm:text-lg lg:text-xl font-extrabold tracking-tight text-[#202925] truncate" title="₹{{ number_format($inventoryValue, 2) }}">
                    ₹{{ number_format($inventoryValue, 2) }}
                </div>
                <span class="text-[10px] text-[#6B756F] block mt-0.5 sm:mt-1 truncate">Cost valuation basis</span>
            </div>
        </div>

        <!-- 4. Low Stock Items -->
        <div class="card-surface p-3.5 sm:p-4 border-l-4 {{ $lowStockCount > 0 ? 'border-l-amber-500 bg-amber-50/20' : 'border-l-neutral-300' }} hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] truncate" title="Low Stock Items">Low Stock</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-600"></i>
                </div>
            </div>
            <div>
                <div class="text-[15px] sm:text-lg lg:text-xl font-extrabold tracking-tight {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-[#202925]' }} truncate">
                    {{ $lowStockCount }} <span class="text-xs font-semibold text-neutral-500">Alerts</span>
                </div>
                <span class="text-[10px] text-[#6B756F] block mt-0.5 sm:mt-1 truncate">At or below threshold</span>
            </div>
        </div>

        <!-- 5. Month Money In -->
        <div class="card-surface p-3.5 sm:p-4 border-l-4 border-l-brand hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] truncate" title="Month Money In">Money In</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-brand-light text-brand border border-brand/20 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="plus" class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[3] text-brand"></i>
                </div>
            </div>
            <div>
                <div class="text-[15px] sm:text-lg lg:text-xl font-extrabold tracking-tight text-brand truncate" title="₹{{ number_format($monthCredits, 2) }}">
                    ₹{{ number_format($monthCredits, 2) }}
                </div>
                <span class="text-[10px] text-[#6B756F] block mt-0.5 sm:mt-1 truncate">Received this month</span>
            </div>
        </div>

        <!-- 6. Month Money Out -->
        <div class="card-surface p-3.5 sm:p-4 border-l-4 border-l-rose-500 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] truncate" title="Month Money Out">Money Out</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="minus" class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[3] text-rose-600"></i>
                </div>
            </div>
            <div>
                <div class="text-[15px] sm:text-lg lg:text-xl font-extrabold tracking-tight text-rose-600 truncate" title="₹{{ number_format($monthDebits, 2) }}">
                    ₹{{ number_format($monthDebits, 2) }}
                </div>
                <span class="text-[10px] text-[#6B756F] block mt-0.5 sm:mt-1 truncate">Spent this month</span>
            </div>
        </div>
    </div>

    <!-- Cash Flow Trend Chart & Quick Enquiries -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cash Flow Chart (2 Cols) -->
        <div class="lg:col-span-2 card-surface p-5 sm:p-6 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h3 class="text-sm font-bold text-[#202925] uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4 text-brand"></i>
                        Cash Flow Activity Trend
                    </h3>
                    <p class="text-xs text-[#6B756F]">Money received (inflow) vs money paid out (outflow)</p>
                </div>
                <div class="flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-200 text-xs shadow-2xs">
                    <a href="{{ route('admin.dashboard', ['period' => '7']) }}" class="px-2.5 py-1 rounded {{ $chartPeriod == '7' ? 'bg-brand font-bold text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">7 Days</a>
                    <a href="{{ route('admin.dashboard', ['period' => '30']) }}" class="px-2.5 py-1 rounded {{ $chartPeriod == '30' ? 'bg-brand font-bold text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">30 Days</a>
                    <a href="{{ route('admin.dashboard', ['period' => '90']) }}" class="px-2.5 py-1 rounded {{ $chartPeriod == '90' ? 'bg-brand font-bold text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">90 Days</a>
                </div>
            </div>

            <div class="h-64 relative">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <!-- Recent Customer Inquiries (1 Col) -->
        <div class="card-surface p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-[#202925] uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-brand"></i>
                        Customer Enquiries
                    </h3>
                    <a href="{{ route('admin.enquiries.index') }}" class="text-xs font-semibold text-brand hover:underline">View All</a>
                </div>

                @if($recentEnquiries->isEmpty())
                    <p class="text-xs text-[#6B756F] py-6 text-center">No customer enquiries received yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentEnquiries as $enq)
                            <div class="p-3 rounded-lg bg-white border border-slate-200 hover:border-brand shadow-2xs transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="block text-xs font-bold text-[#202925]">{{ $enq->name }}</span>
                                        <span class="block text-[11px] text-[#6B756F]">{{ $enq->phone }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded {{ $enq->status_badge['bg'] }}">
                                        {{ $enq->status_badge['label'] }}
                                    </span>
                                </div>
                                @if($enq->product)
                                    <span class="block text-[11px] text-brand font-semibold mt-1 truncate">
                                        {{ $enq->product->name }} ({{ $enq->area_sqft ? $enq->area_sqft . ' sq ft' : 'Enquiry' }})
                                    </span>
                                @endif
                                <div class="mt-2 flex items-center justify-between pt-1 border-t border-[#E3E8E4]/60 text-[11px]">
                                    <span class="text-neutral-400">{{ $enq->created_at->diffForHumans() }}</span>
                                    <a href="{{ route('admin.enquiries.show', $enq) }}" class="font-semibold text-brand hover:underline">Respond →</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-[#E3E8E4] text-center">
                <a href="{{ route('home') }}" target="_blank" class="text-xs font-medium text-[#6B756F] hover:text-brand flex items-center justify-center gap-1">
                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    View Public Discovery Website
                </a>
            </div>
        </div>
    </div>

    <!-- Integrated Accounting & Cash Flow Section -->
    <div class="card-surface overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-[#E3E8E4] flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-light text-brand flex items-center justify-center shadow-2xs shrink-0">
                    <i data-lucide="wallet-cards" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm sm:text-base font-bold text-[#202925]">Financial Cash Flow & Ledger</h2>
                        <span class="badge-brand text-[10px]">{{ now()->format('F Y') }}</span>
                    </div>
                    <p class="text-xs text-[#6B756F] mt-0.5">Live monetary flow with instant record credit and debit entries.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="openCreditModal()" class="btn-brand text-xs py-2 px-3 shadow-xs flex items-center gap-1.5 font-bold">
                    <i data-lucide="plus" class="w-3.5 h-3.5 stroke-[3]"></i> + Record Money In
                </button>
                <button type="button" onclick="openDebitModal()" class="btn-secondary text-xs py-2 px-3 text-rose-600 hover:text-rose-700 hover:border-rose-300 hover:bg-rose-50/50 shadow-2xs flex items-center gap-1.5 font-bold">
                    <i data-lucide="minus" class="w-3.5 h-3.5 stroke-[3] text-rose-600"></i> - Record Money Out
                </button>
                <a href="{{ route('admin.accounting.index') }}" class="text-xs font-semibold text-brand hover:underline flex items-center gap-1 ml-1">
                    <span>Full Ledger</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        <!-- 3-Column Monthly Cash Flow Summary Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-[#E3E8E4] bg-slate-50/60 border-b border-[#E3E8E4]">
            <!-- Inflow -->
            <div class="p-3.5 sm:p-4 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] block truncate">Total Money In (Received)</span>
                    <div class="text-base sm:text-lg font-extrabold text-emerald-700 mt-0.5 truncate" title="₹{{ number_format($monthCredits, 2) }}">
                        ₹{{ number_format($monthCredits, 2) }}
                    </div>
                    <span class="text-[10px] text-[#6B756F] truncate block">All money received this month</span>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="plus" class="w-4 h-4 sm:w-5 sm:h-5 stroke-[3]"></i>
                </div>
            </div>

            <!-- Outflow -->
            <div class="p-3.5 sm:p-4 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] block truncate">Total Money Out (Spent)</span>
                    <div class="text-base sm:text-lg font-extrabold text-rose-600 mt-0.5 truncate" title="₹{{ number_format($monthDebits, 2) }}">
                        ₹{{ number_format($monthDebits, 2) }}
                    </div>
                    <span class="text-[10px] text-[#6B756F] truncate block">All money paid out this month</span>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <i data-lucide="minus" class="w-4 h-4 sm:w-5 sm:h-5 stroke-[3]"></i>
                </div>
            </div>

            <!-- Net Flow -->
            <div class="p-3.5 sm:p-4 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-[#6B756F] block truncate">Net Monthly Flow</span>
                    <div class="text-base sm:text-lg font-extrabold {{ $netCashFlow >= 0 ? 'text-brand' : 'text-rose-600' }} mt-0.5 truncate" title="{{ $netCashFlow >= 0 ? '+' : '' }}₹{{ number_format($netCashFlow, 2) }}">
                        {{ $netCashFlow >= 0 ? '+' : '' }}₹{{ number_format($netCashFlow, 2) }}
                    </div>
                    <span class="text-[10px] text-[#6B756F] truncate block">{{ $netCashFlow >= 0 ? 'Positive net cash flow' : 'Net cash deficit this month' }}</span>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-brand-light text-brand flex items-center justify-center shrink-0">
                    <i data-lucide="wallet" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </div>
            </div>
        </div>

        <!-- Transactions History Table -->
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-[#202925] uppercase tracking-wider">Recent Transactions</h3>
                <span class="text-[11px] text-[#6B756F]">Showing latest {{ count($recentFinancialTransactions) }} records</span>
            </div>

            @if($recentFinancialTransactions->isEmpty())
                <div class="p-8 text-center text-xs text-[#6B756F] border border-dashed border-[#E3E8E4] rounded-xl">
                    <i data-lucide="receipt" class="w-6 h-6 text-slate-300 mx-auto mb-2"></i>
                    No financial transactions recorded yet this month. Click "+ Record Credit" or "- Record Debit" to add one.
                </div>
            @else
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-xs text-left whitespace-nowrap">
                        <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                            <tr>
                                <th class="py-2.5 px-3">Date</th>
                                <th class="py-2.5 px-3">Type</th>
                                <th class="py-2.5 px-3">Purpose / Particulars</th>
                                <th class="py-2.5 px-3">Method</th>
                                <th class="py-2.5 px-3">Reference</th>
                                <th class="py-2.5 px-3">Recorded By</th>
                                <th class="py-2.5 px-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($recentFinancialTransactions as $ft)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-2.5 px-3 text-neutral-600 font-mono text-[11px]">
                                        {{ \Carbon\Carbon::parse($ft->transaction_date)->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if($ft->type === 'credit')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-light text-brand border border-brand/20">
                                                <i data-lucide="plus" class="w-2.5 h-2.5 stroke-[3]"></i> Money In
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200/50">
                                                <i data-lucide="minus" class="w-2.5 h-2.5 stroke-[3]"></i> Money Out
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 font-medium text-[#202925] max-w-xs truncate" title="{{ $ft->purpose }}">
                                        {{ $ft->purpose }}
                                        @if($ft->notes)
                                            <span class="text-[10px] text-[#6B756F] block truncate">{{ $ft->notes }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <i data-lucide="credit-card" class="w-2.5 h-2.5 text-slate-400"></i>
                                            {{ $ft->payment_method_label }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-neutral-500 font-mono text-[11px]">
                                        @if($ft->reference_type)
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px]">
                                                {{ class_basename($ft->reference_type) }} #{{ $ft->reference_id }}
                                            </span>
                                        @else
                                            <span class="text-neutral-400">Direct Entry</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-neutral-500 text-[11px]">
                                        {{ $ft->creator?->name ?? 'System' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-xs {{ $ft->type === 'credit' ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $ft->type === 'credit' ? '+' : '-' }}₹{{ number_format($ft->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Low Stock Inventory Alerts -->
    <div class="card-surface overflow-hidden">
        <div class="p-5 border-b border-[#E3E8E4] flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#202925] uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500"></i>
                    Low Stock Alerts
                </h3>
                <p class="text-xs text-[#6B756F]">Shades and material variants at or below configured reorder threshold</p>
            </div>
            <a href="{{ route('admin.inventory.index') }}" class="text-xs font-semibold text-brand hover:underline">All Inventory</a>
        </div>

        @if($lowStockShades->isEmpty())
            <div class="p-8 text-center text-xs text-[#6B756F]">
                <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-500 mx-auto mb-2"></i>
                All inventory levels are currently above reorder thresholds.
            </div>
        @else
            <div class="overflow-x-auto w-full">
                <table class="w-full text-xs text-left whitespace-nowrap">
                    <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="p-3 pl-5">Product & Shade</th>
                            <th class="p-3">SKU</th>
                            <th class="p-3 text-right">Current Stock</th>
                            <th class="p-3 text-right">Threshold</th>
                            <th class="p-3 pr-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($lowStockShades as $ls)
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="p-3 pl-5 font-medium text-[#202925]">
                                    <div class="font-bold">{{ $ls->product->name }}</div>
                                    <div class="text-[11px] text-[#6B756F] flex items-center gap-1.5 mt-0.5">
                                        @if($ls->hex_color)
                                            <span class="w-2.5 h-2.5 rounded-full border border-slate-300" style="background-color: {{ $ls->hex_color }}"></span>
                                        @endif
                                        <span>{{ $ls->shade_name }}</span>
                                    </div>
                                </td>
                                <td class="p-3 font-mono text-[#6B756F]">{{ $ls->sku }}</td>
                                <td class="p-3 text-right">
                                    <span class="font-bold text-amber-700">{{ number_format($ls->stock_quantity, 1) }}</span>
                                    <span class="text-neutral-500 text-[11px]">{{ $ls->unit }}</span>
                                </td>
                                <td class="p-3 text-right text-neutral-500">
                                    {{ number_format($ls->reorder_threshold, 1) }} {{ $ls->unit }}
                                </td>
                                <td class="p-3 pr-5 text-right">
                                    <a href="{{ route('admin.inventory.adjust', ['shade_id' => $ls->id]) }}" class="px-2.5 py-1 text-[11px] rounded bg-brand-light text-brand hover:bg-brand hover:text-white font-semibold transition border border-brand/20">
                                        Adjust Stock
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('cashFlowChart');
        if (ctx && window.Chart) {
            const brandColor = getComputedStyle(document.documentElement).getPropertyValue('--brand-primary').trim() || '#2EAA1F';
            new window.Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Money In (+₹)',
                            data: {!! json_encode($chartCredits) !!},
                            borderColor: brandColor,
                            backgroundColor: brandColor + '15',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointBackgroundColor: brandColor,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Money Out (-₹)',
                            data: {!! json_encode($chartDebits) !!},
                            borderColor: '#E11D48',
                            backgroundColor: '#E11D4810',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            borderDash: [4, 4],
                            pointBackgroundColor: '#E11D48',
                            pointRadius: 2.5,
                            pointHoverRadius: 5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            display: true,
                            position: 'top',
                            labels: {
                                font: { size: 11 },
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString('en-IN', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + (value >= 1000 ? (value / 1000) + 'k' : value);
                                },
                                font: { size: 10 }
                            },
                            grid: { color: '#E3E8E4' }
                        },
                        x: {
                            ticks: { font: { size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
