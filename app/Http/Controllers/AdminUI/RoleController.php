<?php

namespace App\Http\Controllers\AdminUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::withCount('permissions', 'users')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        // Group permissions by module (first word after action)
        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[1] ?? 'other'; // e.g., "view users" -> "users"
        });

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        // Create role
        $role = Role::create(['name' => $validated['name']]);

        // Filter out non-existent permissions
        $validPermissionIds = [];
        if (!empty($validated['permissions'])) {
            $existingPermissions = Permission::whereIn('id', $validated['permissions'])->pluck('id')->toArray();
            $validPermissionIds = $existingPermissions;
        }

        // Assign permissions (only valid ones)
        if (!empty($validPermissionIds)) {
            $role->syncPermissions($validPermissionIds);
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role created successfully'));
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        // Prevent editing system roles
        if (in_array($role->name, ['super_admin', 'admin', 'user', 'guest'])) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('Cannot edit system role'));
        }

        // Group permissions
        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[1] ?? 'other';
        });

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing system roles
        if (in_array($role->name, ['super_admin', 'admin', 'user', 'guest'])) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('Cannot edit system role'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        // Update role
        $role->update(['name' => $validated['name']]);

        // Filter out non-existent permissions
        $validPermissionIds = [];
        if (!empty($validated['permissions'])) {
            $existingPermissions = Permission::whereIn('id', $validated['permissions'])->pluck('id')->toArray();
            $validPermissionIds = $existingPermissions;
        }

        // Sync permissions (only valid ones)
        $role->syncPermissions($validPermissionIds);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role updated successfully'));
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Prevent deleting system roles
        if (in_array($role->name, ['super_admin', 'admin', 'user', 'guest'])) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('Cannot delete system role'));
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('Cannot delete role with assigned users'));
        }

        $role->delete();

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role deleted successfully'));
    }
}
