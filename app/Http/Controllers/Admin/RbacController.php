<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RbacController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->withCount('users')->get();
        $permissions = Permission::all()->groupBy('group');

        return view('admin.rbac.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('admin.rbac.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'slug' => 'nullable|string|max:50|unique:roles,slug',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $slug = !empty($validated['slug']) 
            ? Str::slug($validated['slug']) 
            : Str::slug($validated['name']);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('admin.rbac.index')
            ->with('success', "Role '{$role->name}' created successfully with assigned permissions.");
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all()->groupBy('group');
        $assignedPermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.rbac.edit', compact('role', 'permissions', 'assignedPermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // If it's the owner role, protect all permissions from being stripped
        if ($role->slug === 'owner') {
            $allPermIds = Permission::pluck('id')->toArray();
            $role->permissions()->sync($allPermIds);
        } else {
            $role->permissions()->sync($validated['permissions'] ?? []);
        }

        return redirect()->route('admin.rbac.index')
            ->with('success', "Role '{$role->name}' permissions updated successfully.");
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', "System role '{$role->name}' is protected and cannot be deleted.");
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Cannot delete role '{$role->name}' because {$role->users()->count()} staff members are currently assigned to it.");
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.rbac.index')
            ->with('success', "Role '{$role->name}' deleted successfully.");
    }

    public function togglePermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'enable' => 'required|boolean',
        ]);

        if ($role->slug === 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'The Owner role is protected. All system permissions are permanently enabled.',
            ], 422);
        }

        if ($validated['enable']) {
            $role->permissions()->syncWithoutDetaching([$validated['permission_id']]);
        } else {
            $role->permissions()->detach($validated['permission_id']);
        }

        return response()->json([
            'success' => true,
            'message' => "Permission updated for role '{$role->name}'.",
            'is_enabled' => (bool) $validated['enable'],
        ]);
    }
}
