@extends('layouts.admin')

@section('title', 'Financial Ledger & Cash Flow — FloorOS')

@section('content')
<div class="space-y-6">
    <!-- Top Executive Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-[#E3E8E4]">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-light text-brand flex items-center justify-center font-bold">
                    <i data-lucide="wallet-cards" class="w-4 h-4"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#202925] tracking-tight">Accounting & Financial Flow</h1>
            </div>
            <p class="text-xs text-[#6B756F] mt-0.5">
                Daily cash management — record deposits & expenses with live sales & inventory synchronization.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Secondary Ledger Reports Links -->
            <a href="{{ route('admin.accounting.profit-loss') }}" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5" title="Revenue vs Operating Costs Statement">
                <i data-lucide="trending-up" class="w-3.5 h-3.5 text-brand"></i>
                <span>Profit & Loss</span>
            </a>
            <a href="{{ route('admin.accounting.journal') }}" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5" title="Double-entry General Journal">
                <i data-lucide="book-open" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>General Journal</span>
            </a>
        </div>
    </div>

    <!-- Quick Action Banner (FloorOS Clean Card Design) -->
    <div class="card-surface p-5 border border-[#E3E8E4] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-brand-light text-brand flex items-center justify-center font-bold shadow-2xs shrink-0">
                <i data-lucide="arrow-left-right" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#202925]">Daily Financial Controls</h2>
                <p class="text-xs text-[#6B756F] mt-0.5">Record customer deposits or daily store expenses in seconds. Syncs automatically with billing and warehouse stock.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <button type="button" 
                    onclick="openCreditModal()" 
                    class="btn-brand text-xs py-2.5 px-4 shadow-xs flex items-center gap-2 active:scale-95 transition">
                <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                <span>+ Record Credit (Deposit)</span>
            </button>
            <button type="button" 
                    onclick="openDebitModal()" 
                    class="btn-secondary text-xs py-2.5 px-4 text-rose-600 hover:text-rose-700 hover:border-rose-300 hover:bg-rose-50/50 shadow-2xs flex items-center gap-2 active:scale-95 transition">
                <i data-lucide="arrow-up-right" class="w-4 h-4 text-rose-600"></i>
                <span>- Record Debit (Expense)</span>
            </button>
        </div>
    </div>

    <!-- Synchronized Financial KPI Cards (FloorOS Stripe Pattern) -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <!-- 1. Total Credit -->
        <div class="card-surface p-4 border-l-4 border-l-brand">
            <div class="flex items-center justify-between text-[#6B756F] mb-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-brand">Total Credit</span>
                <i data-lucide="arrow-down-left" class="w-4 h-4 text-brand"></i>
            </div>
            <div class="text-lg sm:text-xl font-extrabold text-[#202925]">
                ₹{{ number_format($totalCredit, 2) }}
            </div>
            <span class="text-[10px] text-[#6B756F] block mt-1">{{ $creditCount }} total receipts in period</span>
        </div>

        <!-- 2. Total Debit -->
        <div class="card-surface p-4 border-l-4 border-l-rose-500">
            <div class="flex items-center justify-between text-[#6B756F] mb-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Total Debit</span>
                <i data-lucide="arrow-up-right" class="w-4 h-4 text-rose-500"></i>
            </div>
            <div class="text-lg sm:text-xl font-extrabold text-[#202925]">
                ₹{{ number_format($totalDebit, 2) }}
            </div>
            <span class="text-[10px] text-[#6B756F] block mt-1">{{ $debitCount }} total payouts in period</span>
        </div>

        <!-- 3. Net Cash Flow -->
        <div class="card-surface p-4 border-l-4 border-l-brand-dark">
            <div class="flex items-center justify-between text-[#6B756F] mb-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#202925]">Net Cash Flow</span>
                <i data-lucide="wallet" class="w-4 h-4 text-brand"></i>
            </div>
            <div class="text-lg sm:text-xl font-extrabold {{ $netCashFlow >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                {{ $netCashFlow >= 0 ? '+' : '' }}₹{{ number_format($netCashFlow, 2) }}
            </div>
            <span class="text-[10px] text-[#6B756F] block mt-1">Credits minus Debits in period</span>
        </div>

        <!-- 4. Sales Revenue -->
        <div class="card-surface p-4 border-l-4 border-l-blue-600">
            <div class="flex items-center justify-between text-[#6B756F] mb-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Sales Revenue</span>
                <i data-lucide="receipt" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div class="text-lg sm:text-xl font-extrabold text-[#202925]">
                ₹{{ number_format($salesRevenue, 2) }}
            </div>
            <span class="text-[10px] text-[#6B756F] block mt-1">{{ $salesCount }} confirmed sales orders</span>
        </div>

        <!-- 5. Flooring Inventory Stock Valuation -->
        <div class="card-surface p-4 border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between text-[#6B756F] mb-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Inventory Value</span>
                <i data-lucide="boxes" class="w-4 h-4 text-amber-600"></i>
            </div>
            <div class="text-lg sm:text-xl font-extrabold text-[#202925]">
                ₹{{ number_format($inventoryValue, 2) }}
            </div>
            <span class="text-[10px] text-[#6B756F] block mt-1">Stock asset at cost basis</span>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card-surface p-4 border border-[#E3E8E4] shadow-xs">
        <form method="GET" action="{{ route('admin.accounting.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <!-- Period Selector -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#6B756F] mb-1">Time Period</label>
                <select name="period" id="periodSelect" onchange="toggleCustomDates(this.value); this.form.submit();" class="input-custom w-full text-xs font-medium">
                    <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>This Month (Current)</option>
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_quarter" {{ $period === 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                    <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>This Year</option>
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Time (Full Ledger)</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
            </div>

            <!-- Custom Date Inputs (Conditional) -->
            <div id="customDateRange" class="lg:col-span-4 {{ $period === 'custom' ? 'grid grid-cols-2 gap-2' : 'hidden' }}">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-[#6B756F] mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="input-custom w-full text-xs">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-[#6B756F] mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="input-custom w-full text-xs">
                </div>
            </div>

            <!-- Type Filter -->
            <div class="{{ $period === 'custom' ? 'lg:col-span-2' : 'lg:col-span-2' }}">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#6B756F] mb-1">Type</label>
                <select name="type" onchange="this.form.submit()" class="input-custom w-full text-xs">
                    <option value="">All Types</option>
                    <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credits Only (+)</option>
                    <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>Debits Only (-)</option>
                </select>
            </div>

            <!-- Payment Method Filter -->
            <div class="{{ $period === 'custom' ? 'lg:col-span-2' : 'lg:col-span-2' }}">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#6B756F] mb-1">Method</label>
                <select name="payment_method" onchange="this.form.submit()" class="input-custom w-full text-xs">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="mpay" {{ request('payment_method') === 'mpay' ? 'selected' : '' }}>mPay</option>
                    <option value="gpay" {{ request('payment_method') === 'gpay' ? 'selected' : '' }}>GPay</option>
                    <option value="paytm" {{ request('payment_method') === 'paytm' ? 'selected' : '' }}>Paytm</option>
                    <option value="phonepe" {{ request('payment_method') === 'phonepe' ? 'selected' : '' }}>PhonePe</option>
                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cheque" {{ request('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                </select>
            </div>

            <!-- Source Filter -->
            <div class="{{ $period === 'custom' ? 'lg:col-span-2' : 'lg:col-span-2' }}">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#6B756F] mb-1">Source</label>
                <select name="source" onchange="this.form.submit()" class="input-custom w-full text-xs">
                    <option value="">All Sources</option>
                    <option value="manual" {{ request('source') === 'manual' ? 'selected' : '' }}>Manual Entries</option>
                    <option value="auto" {{ request('source') === 'auto' ? 'selected' : '' }}>Automated Sync</option>
                </select>
            </div>

            <!-- Search Keyword & Submit -->
            <div class="{{ $period === 'custom' ? 'lg:col-span-1' : 'lg:col-span-3' }} flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-[#6B756F] mb-1">Search</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="    Search purpose..." class="input-custom w-full text-xs pl-7">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2 top-2.5"></i>
                    </div>
                </div>
                <button type="submit" class="btn-brand text-xs py-2 px-3 shrink-0" title="Apply Filters">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                </button>
                @if(request()->hasAny(['period', 'type', 'payment_method', 'source', 'search', 'start_date', 'end_date']))
                    <a href="{{ route('admin.accounting.index') }}" class="btn-secondary text-xs py-2 px-2.5 shrink-0 text-slate-500 hover:text-rose-600" title="Reset Filters">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Financial Transactions Table -->
    <div class="card-surface rounded-xl border border-[#E3E8E4] shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#E3E8E4] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white">
            <div>
                <h3 class="text-sm font-bold text-[#202925] tracking-tight flex items-center gap-2 uppercase tracking-wider">
                    <i data-lucide="history" class="w-4 h-4 text-brand"></i>
                    Financial Transactions History
                </h3>
                <p class="text-xs text-[#6B756F] mt-0.5">
                    Unified ledger of client receipts, vendor settlements, and showroom overheads.
                </p>
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-[#6B756F]">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Credit = Inflow
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[11px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Debit = Outflow
                </span>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-white text-slate-700 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                    <tr>
                        <th class="py-3 px-4 sm:px-5">Date & Time</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Purpose / Description</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4 text-right">Amount (₹)</th>
                        <th class="py-3 px-4">Source</th>
                        <th class="py-3 px-4 text-right pr-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E3E8E4] bg-white text-[#202925]">
                    @forelse($transactions as $txn)
                        <tr class="hover:bg-emerald-50/40 transition">
                            <!-- Date & Time -->
                            <td class="py-3.5 px-4 sm:px-5 whitespace-nowrap">
                                <div class="font-bold text-[#202925]">{{ $txn->transaction_date ? $txn->transaction_date->format('d M Y') : $txn->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-[#6B756F] font-mono">{{ $txn->transaction_date ? $txn->transaction_date->format('h:i A') : $txn->created_at->format('h:i A') }}</div>
                            </td>

                            <!-- Type Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($txn->type === 'credit')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i data-lucide="arrow-down-left" class="w-3 h-3"></i>
                                        Credit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                                        Debit
                                    </span>
                                @endif
                            </td>

                            <!-- Purpose -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#202925] max-w-md break-words">{{ $txn->purpose }}</div>
                                @if($txn->notes)
                                    <div class="text-[10px] text-[#6B756F] italic mt-0.5 truncate max-w-sm">{{ $txn->notes }}</div>
                                @endif
                            </td>

                            <!-- Method Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    <i data-lucide="credit-card" class="w-3 h-3 text-slate-400"></i>
                                    {{ $txn->payment_method_label }}
                                </span>
                            </td>

                            <!-- Amount -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <span class="font-bold text-sm font-mono {{ $txn->type === 'credit' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $txn->type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                                </span>
                            </td>

                            <!-- Source Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($txn->source === 'manual')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i data-lucide="user" class="w-3 h-3"></i> Manual Entry
                                    </span>
                                @elseif($txn->source === 'sale_payment')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <i data-lucide="shopping-cart" class="w-3 h-3 text-emerald-600"></i> Auto (Sale)
                                    </span>
                                @elseif($txn->source === 'purchase_payment')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <i data-lucide="truck" class="w-3 h-3 text-amber-600"></i> Auto (Purchase)
                                    </span>
                                @elseif($txn->source === 'expense')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-800 border border-purple-200">
                                        <i data-lucide="receipt" class="w-3 h-3 text-purple-600"></i> Auto (Expense)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $txn->source_label }}
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 pr-5 text-right whitespace-nowrap">
                                @if($txn->source === 'manual')
                                    <form action="{{ route('admin.accounting.transactions.destroy', $txn) }}" method="POST" class="inline-block" data-confirm="Delete this manual transaction of ₹{{ number_format($txn->amount, 2) }}? Accounting totals will be updated." data-confirm-title="Delete Transaction" data-confirm-destructive="true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-neutral-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete Manual Transaction">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-neutral-400 font-mono italic">System Sync</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-[#6B756F]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-6 h-6"></i>
                                    </div>
                                    <span class="font-bold text-[#202925]">No financial transactions recorded for this filter.</span>
                                    <span class="text-[#6B756F] text-[11px]">Click "Record Credit" or "Record Debit" above to add your first transaction.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-[#E3E8E4] bg-white flex justify-center">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleCustomDates(val) {
        const el = document.getElementById('customDateRange');
        if (el) {
            if (val === 'custom') {
                el.classList.remove('hidden');
                el.classList.add('grid', 'grid-cols-2', 'gap-2');
            } else {
                el.classList.add('hidden');
                el.classList.remove('grid', 'grid-cols-2', 'gap-2');
            }
        }
    }
</script>
@endpush
@endsection
