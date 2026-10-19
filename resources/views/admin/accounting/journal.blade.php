@extends('layouts.admin')

@section('title', 'General Journal — FloorOS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-[#E3E8E4]">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#6B756F] mb-1">
                <a href="{{ route('admin.accounting.index') }}" class="hover:text-brand font-medium transition-colors">Accounting & Ledger</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#202925] font-semibold">General Journal</span>
            </div>
            <h1 class="text-2xl font-bold text-[#202925] tracking-tight">Double-Entry General Journal</h1>
            <p class="text-xs text-[#6B756F] mt-0.5">Audit trail of balanced debit and credit journal postings generated from business transactions.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.accounting.index') }}" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Back to Ledger</span>
            </a>
            <a href="{{ route('admin.accounting.profit-loss') }}" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5">
                <i data-lucide="trending-up" class="w-3.5 h-3.5 text-brand"></i>
                <span>Profit & Loss</span>
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card-surface p-4 border border-[#E3E8E4] shadow-xs">
        <form action="{{ route('admin.accounting.journal') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by entry # or description..." class="w-full pl-9 pr-3 py-2 text-xs border border-[#E3E8E4] rounded-xl focus:ring-1 focus:ring-brand focus:border-brand font-medium text-[#202925]">
            </div>

            <button type="submit" class="btn-brand text-xs py-2 px-4 shadow-xs font-bold">
                Filter
            </button>
            @if(request('search'))
                <a href="{{ route('admin.accounting.journal') }}" class="btn-secondary text-xs py-2 px-3 self-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Journal Entries Feed -->
    <div class="space-y-4">
        @forelse($entries as $entry)
            <div class="card-surface border border-[#E3E8E4] shadow-xs overflow-hidden">
                <div class="px-5 py-3 border-b border-[#E3E8E4] bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-[#202925]">{{ $entry->entry_number }}</span>
                        <span class="text-[#6B756F]">{{ \Carbon\Carbon::parse($entry->date)->format('d M Y') }}</span>
                        @if($entry->reference_type)
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-200 text-slate-700 font-semibold">
                                {{ class_basename($entry->reference_type) }} #{{ $entry->reference_id }}
                            </span>
                        @endif
                    </div>
                    <div class="text-[#6B756F] text-[11px] truncate max-w-md">
                        {{ $entry->description }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-[#E3E8E4] bg-white text-slate-700 font-bold uppercase text-[10px] tracking-wider">
                                <th class="py-2.5 px-4 w-28">Code</th>
                                <th class="py-2.5 px-4">Account Particulars</th>
                                <th class="py-2.5 px-4 text-right w-36">Debit (Dr)</th>
                                <th class="py-2.5 px-4 text-right w-36">Credit (Cr)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E3E8E4]">
                            @foreach($entry->lines as $line)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-2.5 px-4 font-mono text-slate-500 text-[11px]">{{ $line->account->code ?? '—' }}</td>
                                    <td class="py-2.5 px-4 font-medium text-[#202925] {{ $line->credit > 0 ? 'pl-8' : '' }}">
                                        {{ $line->account->name ?? 'Account' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-medium text-[#202925]">
                                        {{ $line->debit > 0 ? '₹' . number_format($line->debit, 2) : '—' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-medium text-[#202925]">
                                        {{ $line->credit > 0 ? '₹' . number_format($line->credit, 2) : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-50/70 font-bold border-t border-[#E3E8E4]">
                                <td colspan="2" class="py-2.5 px-4 text-right text-[#6B756F] uppercase text-[10px]">Balanced Total:</td>
                                <td class="py-2.5 px-4 text-right font-mono text-emerald-700 font-extrabold">₹{{ number_format($entry->lines->sum('debit'), 2) }}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-emerald-700 font-extrabold">₹{{ number_format($entry->lines->sum('credit'), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="card-surface border border-[#E3E8E4] p-12 text-center text-xs text-[#6B756F]">
                <i data-lucide="book-open" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                <p>No journal entries found matching your query.</p>
            </div>
        @endforelse

        @if($entries->hasPages())
            <div class="card-surface border border-[#E3E8E4] p-4">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
