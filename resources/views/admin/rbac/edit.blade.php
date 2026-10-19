@extends('layouts.admin')

@section('title', 'Edit Role — RBAC')
@section('page_title', 'Configure Role: ' . $role->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $role->is_system ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-brand-light text-brand' }} uppercase tracking-wider">
                    {{ $role->is_system ? 'System Protected Role' : 'Custom Configurable Role' }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-6 h-6 text-brand"></i>
                Configure Role: {{ $role->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Assigned to {{ $role->users()->count() }} staff members.</p>
        </div>
        <a href="{{ route('admin.rbac.index') }}" class="btn-secondary text-xs py-2 px-3">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back to Roles
        </a>
    </div>

    @if($role->slug === 'owner')
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0"></i>
            <div>
                <strong class="font-bold">Store Owner Protection:</strong>
                The Store Owner role automatically retains full access to all system capabilities, accounting ledgers, and administrative settings.
            </div>
        </div>
    @endif

    <form action="{{ route('admin.rbac.update', $role) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Role Core Profile -->
        <div class="card-surface p-6 sm:p-8 space-y-4 bg-white shadow-2xs">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                Role Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-900 mb-1">Role Title *</label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" required class="w-full input-custom text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-900 mb-1">System Identifier Slug</label>
                    <input type="text" value="{{ $role->slug }}" disabled class="w-full input-custom text-sm font-mono bg-slate-50 cursor-not-allowed">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Slug is permanent to protect code integration.</span>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-900 mb-1">Role Responsibilities / Description</label>
                    <textarea name="description" rows="2" class="w-full input-custom text-sm">{{ old('description', $role->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Permissions Assignment -->
        <div class="card-surface p-6 sm:p-8 space-y-6 bg-white shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Operational Permissions Configuration
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Toggle specific feature scopes for staff assigned to this role.</p>
                </div>
                @if($role->slug !== 'owner')
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleAllCheckboxes(true)" class="px-2.5 py-1 rounded text-xs font-semibold bg-brand-light text-brand hover:bg-brand hover:text-white transition">
                            Select All
                        </button>
                        <button type="button" onclick="toggleAllCheckboxes(false)" class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                            Deselect All
                        </button>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                @foreach($permissions as $group => $perms)
                    <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-white">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i data-lucide="check-square" class="w-4 h-4 text-brand"></i>
                                {{ $group }}
                            </h3>
                            @if($role->slug !== 'owner')
                                <button type="button" onclick="toggleGroupCheckboxes('group-{{ Str::slug($group) }}')" class="text-[11px] font-semibold text-brand hover:underline">
                                    Toggle Group
                                </button>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            @foreach($perms as $p)
                                @php
                                    $isChecked = $role->slug === 'owner' || in_array($p->id, $assignedPermissionIds);
                                @endphp
                                <label class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 hover:border-brand cursor-pointer transition {{ $isChecked ? 'bg-brand-light/30 border-brand/40' : 'bg-white' }}">
                                    <div class="space-y-0.5">
                                        <span class="block text-xs font-bold text-slate-900">{{ $p->name }}</span>
                                        <span class="block text-[11px] text-slate-500 font-normal">{{ $p->description }}</span>
                                        <span class="block text-[10px] font-mono text-slate-400">{{ $p->slug }}</span>
                                    </div>
                                    <div class="relative shrink-0">
                                        <input type="checkbox" name="permissions[]" value="{{ $p->id }}" {{ $isChecked ? 'checked' : '' }} {{ $role->slug === 'owner' ? 'disabled' : '' }} class="sr-only peer perm-checkbox group-{{ Str::slug($group) }}">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand shadow-inner"></div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Sticky Submit Footer -->
        <div class="sticky bottom-4 z-20 p-4 rounded-xl bg-white border border-slate-200 shadow-xl flex items-center justify-between">
            <a href="{{ route('admin.rbac.index') }}" class="btn-secondary text-xs py-2 px-4">
                Cancel
            </a>
            <button type="submit" class="btn-brand py-2.5 px-6 font-semibold shadow-xs">
                <i data-lucide="save" class="w-4 h-4"></i> Save Permissions
            </button>
        </div>
    </form>

</div>

<script>
    function toggleAllCheckboxes(state) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = state);
    }

    function toggleGroupCheckboxes(groupClass) {
        const checkboxes = document.querySelectorAll('.' + groupClass);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endsection
