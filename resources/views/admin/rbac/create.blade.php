@extends('layouts.admin')

@section('title', 'Create Operational Role — RBAC')
@section('page_title', 'Create Custom Role')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="shield-plus" class="w-6 h-6 text-brand"></i>
                Create New Role
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Define role details and assign operational permissions.</p>
        </div>
        <a href="{{ route('admin.rbac.index') }}" class="btn-secondary text-xs py-2 px-3">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back to Roles
        </a>
    </div>

    <form action="{{ route('admin.rbac.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Role Core Profile -->
        <div class="card-surface p-6 sm:p-8 space-y-4 bg-white shadow-2xs">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                Role Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-900 mb-1">Role Title *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Regional Showroom Supervisor" class="w-full input-custom text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-900 mb-1">Identifier Slug (Auto-generated if empty)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. showroom-supervisor" class="w-full input-custom text-sm font-mono">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-900 mb-1">Role Responsibilities / Description</label>
                    <textarea name="description" rows="2" placeholder="Briefly describe what this operational role is authorized to perform..." class="w-full input-custom text-sm">{{ old('description') }}</textarea>
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
                    <p class="text-xs text-slate-500 mt-0.5">Select the permissions granted to staff assigned to this role.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleAllCheckboxes(true)" class="px-2.5 py-1 rounded text-xs font-semibold bg-brand-light text-brand hover:bg-brand hover:text-white transition">
                        Select All
                    </button>
                    <button type="button" onclick="toggleAllCheckboxes(false)" class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                        Deselect All
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                @foreach($permissions as $group => $perms)
                    <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-white">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i data-lucide="check-square" class="w-4 h-4 text-brand"></i>
                                {{ $group }}
                            </h3>
                            <button type="button" onclick="toggleGroupCheckboxes('group-{{ Str::slug($group) }}')" class="text-[11px] font-semibold text-brand hover:underline">
                                Toggle Group
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            @foreach($perms as $p)
                                <label class="flex items-start gap-2.5 p-2 rounded-lg border border-slate-100 hover:border-slate-300 hover:bg-slate-50/50 cursor-pointer transition">
                                    <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="perm-checkbox group-{{ Str::slug($group) }} mt-0.5 rounded text-brand focus:ring-brand">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">{{ $p->name }}</span>
                                        <span class="block text-[11px] text-slate-500 font-normal">{{ $p->description }}</span>
                                        <span class="block text-[10px] font-mono text-slate-400 mt-0.5">{{ $p->slug }}</span>
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
                <i data-lucide="check" class="w-4 h-4"></i> Save & Grant Role
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
