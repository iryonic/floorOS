@extends('layouts.admin')

@section('title', 'Add Staff User — FloorOS')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-text-primary tracking-tight">Add Staff Member</h1>
            <p class="text-xs text-text-secondary mt-1">Create login credentials and assign business management permissions.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-border-default rounded-[var(--radius-base)] text-xs font-semibold text-text-secondary hover:bg-slate-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Users
        </a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="bg-surface rounded-[var(--radius-base)] border border-border-default p-6 shadow-xs space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-text-primary mb-1">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Vikram Joshi" class="w-full text-xs px-3 py-2 border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-semibold text-text-primary mb-1">Email Address (Login) <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="staff@flooros.com" class="w-full text-xs px-3 py-2 border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-semibold text-text-primary mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210" class="w-full text-xs px-3 py-2 border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-semibold text-text-primary mb-1">Assigned Role <span class="text-rose-500">*</span></label>
                <select name="role" required class="w-full text-xs px-3 py-2 border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="sales" {{ old('role') === 'sales' ? 'selected' : '' }}>Sales Staff</option>
                    <option value="inventory" {{ old('role') === 'inventory' ? 'selected' : '' }}>Inventory / Warehouse Staff</option>
                    <option value="accountant" {{ old('role') === 'accountant' ? 'selected' : '' }}>Accountant</option>
                    <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Store Manager</option>
                    <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner / Super Administrator</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-text-primary mb-1">Initial Password <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="password" id="createPasswordInput" name="password" required placeholder="Minimum 6 characters" class="w-full text-xs px-3 py-2 pr-10 border border-border-default rounded-[var(--radius-base)] focus:ring-1 focus:ring-primary focus:border-primary">
                    <button type="button" onclick="togglePasswordVisibility('createPasswordInput', 'createPasswordIcon')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand transition p-1" title="Toggle password visibility">
                        <i data-lucide="eye" id="createPasswordIcon" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t border-border-default">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-border-default rounded-[var(--radius-base)] text-xs font-semibold text-text-secondary hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary-dark text-white rounded-[var(--radius-base)] text-xs font-semibold transition-colors shadow-xs">
                Create Staff Account
            </button>
        </div>
    </form>
</div>
@endsection
