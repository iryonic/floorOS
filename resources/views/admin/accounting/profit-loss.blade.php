@extends('layouts.admin')

@section('title', 'Profit & Loss Statement — FloorOS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-[#E3E8E4]">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#6B756F] mb-1">
                <a href="{{ route('admin.accounting.index') }}" class="hover:text-brand font-medium transition-colors">Accounting & Ledger</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#202925] font-semibold">Income Statement</span>
            </div>
            <h1 class="text-2xl font-bold text-[#202925] tracking-tight">Profit & Loss Statement</h1>
            <p class="text-xs text-[#6B756F] mt-0.5">Real-time revenue, true cost of goods sold (COGS), operating expenses, and net profit.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.accounting.index') }}" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Back to Ledger</span>
            </a>
            <button onclick="window.print()" class="btn-secondary text-xs py-2 px-3.5 flex items-center gap-2 shadow-xs">
                <i data-lucide="printer" class="w-4 h-4 text-[#6B756F]"></i>
                <span>Print Statement</span>
            </button>
        </div>
    </div>

    <!-- Date Range Picker -->
    <div class="card-surface p-4 border border-[#E3E8E4] shadow-xs">
        <form action="{{ route('admin.accounting.profit-loss') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3">
            <span class="text-xs font-bold text-[#202925]">Report Period:</span>
            <div class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}" class="py-1.5 px-3 text-xs border border-[#E3E8E4] rounded-xl focus:ring-1 focus:ring-brand focus:border-brand font-medium text-[#202925]">
                <span class="text-xs text-[#6B756F]">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="py-1.5 px-3 text-xs border border-[#E3E8E4] rounded-xl focus:ring-1 focus:ring-brand focus:border-brand font-medium text-[#202925]">
            </div>
            <button type="submit" class="btn-brand text-xs py-2 px-4 shadow-xs font-bold">
                Apply Date Range
            </button>
        </form>
    </div>

    <!-- P&L Financial Statement Card -->
    <div class="card-surface border border-[#E3E8E4] shadow-xs p-6 space-y-6">
        <div class="border-b border-[#E3E8E4] pb-4 flex justify-between items-end">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#202925] uppercase tracking-wider">Statement of Income & Operations</h2>
                <p class="text-xs text-[#6B756F] mt-0.5">For period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <span class="text-xs font-mono text-slate-500 font-semibold">Currency: INR (₹)</span>
        </div>

        <!-- 1. Revenue Section -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-brand flex items-center gap-1.5">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> 1. Operating Revenue
            </h3>
            <div class="divide-y divide-[#E3E8E4] text-xs">
                <div class="py-2.5 flex justify-between text-[#6B756F]">
                    <span>Gross Flooring Sales (Excl. Tax)</span>
                    <span class="font-bold text-[#202925] font-mono">₹{{ number_format($grossSales, 2) }}</span>
                </div>
                <div class="py-2.5 flex justify-between text-rose-600">
                    <span>Less: Customer Invoiced Discounts</span>
                    <span class="font-bold font-mono">- ₹{{ number_format($totalDiscounts, 2) }}</span>
                </div>
                <div class="py-3 flex justify-between text-xs font-bold bg-slate-50/80 px-3 rounded-xl border border-[#E3E8E4]/60">
                    <span class="text-[#202925]">Net Sales Revenue</span>
                    <span class="text-brand text-sm font-mono font-extrabold">₹{{ number_format($netRevenue, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- 2. COGS Section -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-rose-600 flex items-center gap-1.5">
                <i data-lucide="boxes" class="w-3.5 h-3.5"></i> 2. Cost of Goods Sold (COGS)
            </h3>
            <div class="divide-y divide-[#E3E8E4] text-xs">
                <div class="py-2.5 flex justify-between text-[#6B756F]">
                    <span>Cost of Flooring Inventory Sold (Weighted Cost Basis)</span>
                    <span class="font-bold text-rose-600 font-mono">₹{{ number_format($cogs, 2) }}</span>
                </div>
                <div class="py-3 flex justify-between text-xs font-bold bg-slate-50/80 px-3 rounded-xl border border-[#E3E8E4]/60">
                    <span class="text-[#202925]">Total Cost of Goods Sold</span>
                    <span class="text-rose-600 text-sm font-mono font-extrabold">₹{{ number_format($cogs, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Gross Profit -->
        <div class="p-4 rounded-xl bg-brand-light/70 border border-brand/20 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-brand-dark uppercase tracking-wider block">Gross Profit</span>
                <span class="text-[11px] text-[#6B756F]">Gross Margin: <strong class="text-[#202925]">{{ $grossMarginPercent }}%</strong></span>
            </div>
            <div class="text-xl font-extrabold text-brand-dark font-mono">
                ₹{{ number_format($grossProfit, 2) }}
            </div>
        </div>

        <!-- 4. Operating Expenses (Opex) -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-700 flex items-center gap-1.5">
                <i data-lucide="receipt" class="w-3.5 h-3.5"></i> 3. Operating Overhead Expenses
            </h3>
            <div class="divide-y divide-[#E3E8E4] text-xs">
                @forelse($expensesByCategory as $expRow)
                    <div class="py-2.5 flex justify-between text-[#6B756F]">
                        <span>{{ $expRow['category'] }}</span>
                        <span class="font-bold text-[#202925] font-mono">₹{{ number_format($expRow['total'], 2) }}</span>
                    </div>
                @empty
                    <div class="py-2.5 text-[#6B756F] italic">No operating expenses recorded in this period.</div>
                @endforelse
                <div class="py-3 flex justify-between text-xs font-bold bg-slate-50/80 px-3 rounded-xl border border-[#E3E8E4]/60">
                    <span class="text-[#202925]">Total Operating Expenses</span>
                    <span class="text-rose-600 text-sm font-mono font-extrabold">₹{{ number_format($totalOpex, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Operating Net Profit -->
        <div class="p-5 rounded-xl {{ $operatingProfit >= 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }} flex items-center justify-between">
            <div>
                <span class="text-xs font-bold {{ $operatingProfit >= 0 ? 'text-emerald-800' : 'text-rose-800' }} uppercase tracking-wider block">
                    Operating Net Profit
                </span>
                <span class="text-[11px] text-[#6B756F]">
                    Net Margin: <strong class="text-[#202925]">{{ $netMarginPercent }}%</strong> of Net Sales Revenue
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono {{ $operatingProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                ₹{{ number_format($operatingProfit, 2) }}
            </div>
        </div>
    </div>
</div>
@endsection
