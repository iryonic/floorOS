@extends('layouts.admin')

@section('title', 'Role-Based Access Control (RBAC)')
@section('page_title', 'Roles & Permissions (RBAC)')

@section('content')
<div class="w-full space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                <i data-lucide="shield-check" class="w-6 h-6 text-brand"></i>
                Access-Control
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage store roles, configure fine-grained operational permissions, and govern staff privileges.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary text-xs py-2 px-3">
                <i data-lucide="users" class="w-4 h-4 text-slate-600"></i> Manage Staff
            </a>
            <a href="{{ route('admin.rbac.create') }}" class="btn-brand text-xs py-2 px-4 shadow-xs">
                <i data-lucide="plus" class="w-4 h-4"></i> Create New Role
            </a>
        </div>
    </div>

    <!-- Roles Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($roles as $role)
            <div class="card-surface p-5 flex flex-col justify-between space-y-4 hover:border-brand transition group bg-white shadow-2xs">
                <div>
                    <!-- Top Role Meta -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-brand-light flex items-center justify-center text-brand font-bold shadow-2xs border border-brand-light">
                                <i data-lucide="{{ $role->slug === 'owner' ? 'crown' : ($role->slug === 'manager' ? 'briefcase' : ($role->slug === 'sales' ? 'receipt' : ($role->slug === 'inventory' ? 'boxes' : 'landmark'))) }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-brand transition">{{ $role->name }}</h3>
                                <span class="font-mono text-[10px] text-slate-500 font-semibold uppercase tracking-wider">slug: {{ $role->slug }}</span>
                            </div>
                        </div>
                        @if($role->is_system)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wide">
                                System
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-50 text-slate-600 border border-slate-200 uppercase tracking-wide">
                                Custom
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                        {{ $role->description ?: 'No specific description provided for this operational role.' }}
                    </p>

                    <!-- Metrics -->
                    <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-slate-100 text-xs">
                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                            <span class="text-[10px] text-slate-400 block font-medium">Assigned Staff</span>
                            <span class="text-sm font-extrabold text-slate-900">{{ $role->users_count }} active</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-slate-200">
                            <span class="text-[10px] text-slate-400 block font-medium">Permissions</span>
                            <span class="text-sm font-extrabold text-brand">
                                {{ $role->slug === 'owner' ? 'All (Full Access)' : $role->permissions->count() . ' granted' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">
                        {{ $role->is_system ? 'Protected System Role' : 'Custom Configurable' }}
                    </span>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.rbac.edit', $role) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-700 hover:border-brand hover:text-brand hover:bg-brand-light transition" title="Edit Role & Permissions">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        @if(!$role->is_system && $role->users_count === 0)
                            <form action="{{ route('admin.rbac.destroy', $role) }}" method="POST" data-confirm="Are you sure you want to delete this custom role?" data-confirm-title="Delete Custom Role" data-confirm-destructive="true">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:border-rose-300 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete Role">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Comprehensive Permission Matrix -->
    <div class="card-surface p-6 bg-white shadow-2xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="table" class="w-5 h-5 text-brand"></i>
                    Role & Permission Authorization Matrix
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Overview of active permissions mapped across all administrative roles.</p>
            </div>
            <span class="px-2.5 py-1 rounded text-[11px] font-bold bg-brand-light text-brand uppercase tracking-wider">
                Active Backend Enforcement
            </span>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead class="bg-white text-slate-800 border-b-2 border-slate-200 uppercase text-[10px] tracking-wider font-bold">
                    <tr>
                        <th class="p-3 pl-4 w-72">Permission Name & Scope</th>
                        <th class="p-3">Slug</th>
                        @foreach($roles as $role)
                            <th class="p-3 text-center">{{ $role->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($permissions as $group => $perms)
                        <tr class="bg-slate-50/70">
                            <td colspan="{{ 2 + $roles->count() }}" class="p-2.5 pl-4 font-bold text-slate-800 uppercase tracking-wider text-[10px]">
                                {{ $group }}
                            </td>
                        </tr>
                        @foreach($perms as $perm)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-3 pl-4 font-medium text-slate-900">
                                    <div>{{ $perm->name }}</div>
                                    <span class="text-[10px] text-slate-400 font-normal">{{ $perm->description }}</span>
                                </td>
                                <td class="p-3 font-mono text-[11px] text-slate-500">
                                    {{ $perm->slug }}
                                </td>
                                @foreach($roles as $role)
                                    <td class="p-3 text-center">
                                        @if($role->slug === 'owner')
                                            <!-- Protected Owner Role (Always Active) -->
                                            <div class="inline-flex items-center justify-center" title="Owner permissions are permanently granted and system-protected.">
                                                <div class="w-9 h-5 bg-brand rounded-full p-0.5 cursor-not-allowed opacity-90 flex items-center justify-end pr-0.5 shadow-inner">
                                                    <div class="w-4 h-4 bg-white rounded-full shadow-xs flex items-center justify-center">
                                                        <i data-lucide="lock" class="w-2.5 h-2.5 text-brand stroke-[2.5]"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Interactive Custom Toggle Switch -->
                                            <label class="relative inline-flex items-center cursor-pointer select-none" title="Toggle {{ $perm->name }} for {{ $role->name }}">
                                                <input type="checkbox" 
                                                       class="sr-only peer rbac-matrix-toggle" 
                                                       data-role-id="{{ $role->id }}" 
                                                       data-perm-id="{{ $perm->id }}"
                                                       data-url="{{ route('admin.rbac.togglePermission', $role) }}"
                                                       {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}
                                                       onchange="handleRbacToggle(this)">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand shadow-inner hover:opacity-90"></div>
                                            </label>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function handleRbacToggle(checkbox) {
    const url = checkbox.dataset.url;
    const permId = checkbox.dataset.permId;
    const enable = checkbox.checked ? 1 : 0;

    checkbox.disabled = true;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            permission_id: permId,
            enable: enable
        })
    })
    .then(res => res.json())
    .then(data => {
        checkbox.disabled = false;
        if (!data.success) {
            checkbox.checked = !checkbox.checked;
            if (window.showAlert) {
                window.showAlert({ type: 'error', title: 'Permission Error', message: data.message });
            }
        } else {
            if (window.showAlert) {
                window.showAlert({
                    type: 'success',
                    title: 'Permission Updated',
                    message: data.message || 'Role permissions saved successfully.'
                });
            }
        }
    })
    .catch(err => {
        checkbox.disabled = false;
        checkbox.checked = !checkbox.checked;
        if (window.showAlert) {
            window.showAlert({ type: 'error', title: 'Network Error', message: 'Failed to update permission state.' });
        }
    });
}
</script>
@endsection
