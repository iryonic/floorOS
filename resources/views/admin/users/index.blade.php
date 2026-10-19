@extends('layouts.admin')

@section('title', 'Staff & Permissions — FloorOS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text-primary tracking-tight">Staff</h1>
            <p class="text-xs text-text-secondary mt-1">Manage staff accounts, assign operational roles, and enforce security policies.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-primary text-white rounded-[var(--radius-base)] text-xs font-semibold hover:bg-primary-dark transition-colors shadow-xs">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Add Staff Member
            </a>
        </div>
    </div>

    <!-- Role Reference Guide -->
    <div class="bg-surface rounded-[var(--radius-base)] border border-border-default p-4 shadow-xs text-xs space-y-2">
        <span class="font-bold text-text-primary uppercase tracking-wider block">Role Capabilities:</span>
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            <div class="p-2.5 bg-slate-50 rounded border border-border-default">
                <span class="font-bold text-primary block">Owner / Super Admin</span>
                <span class="text-[11px] text-text-secondary">Full system access, cost prices, settings, accounting.</span>
            </div>
            <div class="p-2.5 bg-slate-50 rounded border border-border-default">
                <span class="font-bold text-primary block">Manager</span>
                <span class="text-[11px] text-text-secondary">Oversees inventory, approvals, purchases, and customer operations.</span>
            </div>
            <div class="p-2.5 bg-slate-50 rounded border border-border-default">
                <span class="font-bold text-primary block">Sales Staff</span>
                <span class="text-[11px] text-text-secondary">Create invoices, manage quotations, register customer receipts.</span>
            </div>
            <div class="p-2.5 bg-slate-50 rounded border border-border-default">
                <span class="font-bold text-primary block">Inventory Staff</span>
                <span class="text-[11px] text-text-secondary">Receive purchase shipments, stock counts, warehouse movements.</span>
            </div>
            <div class="p-2.5 bg-slate-50 rounded border border-border-default">
                <span class="font-bold text-primary block">Accountant</span>
                <span class="text-[11px] text-text-secondary">Expenses, financial statements, ledger entries, tax audits.</span>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-surface rounded-[var(--radius-base)] border border-border-default shadow-xs overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="border-b border-border-default bg-slate-50/50 text-text-secondary font-semibold">
                        <th class="py-3 px-4">Staff Member</th>
                        <th class="py-3 px-4">Assigned Role</th>
                        <th class="py-3 px-4">Contact Phone</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Joined Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-default text-text-primary">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-light text-brand font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-text-primary">{{ $user->name }}</div>
                                        <div class="text-[11px] text-text-secondary">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="capitalize px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                    {{ $user->role === 'owner' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                                    {{ $user->role === 'manager' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                    {{ $user->role === 'sales' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                    {{ $user->role === 'inventory' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    {{ $user->role === 'accountant' ? 'bg-teal-50 text-teal-700 border border-teal-200' : '' }}
                                ">
                                    {{ $user->roleRelation?->name ?? $user->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-text-secondary">{{ $user->phone ?: '—' }}</td>
                            <td class="py-3.5 px-4">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-text-secondary">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 text-text-secondary hover:text-slate-900 rounded hover:bg-slate-100 transition-colors" title="Edit User">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST" data-confirm="Toggle active/inactive status for this user?" data-confirm-title="Change Account Status" class="inline-block">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-text-secondary hover:text-amber-600 rounded hover:bg-slate-100 transition-colors" title="{{ $user->status === 'active' ? 'Deactivate Account' : 'Activate Account' }}">
                                                <i data-lucide="{{ $user->status === 'active' ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-text-secondary">
                                No staff accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-border-default">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
