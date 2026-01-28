<?php

namespace App\Http\Controllers\AdminUI;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserPermissionController extends Controller
{
    /**
     * Show the form for editing user permissions
     */
    public function edit(User $user)
    {
        // Get all roles
        $roles = Role::orderBy('name')->get();

        // Get all permissions grouped by module
        $allPermissions = Permission::orderBy('name')->get();
        $permissions = $allPermissions->groupBy(function($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[1] ?? 'other';
        });

        // Get user's current role and permissions
        $userRole = $user->roles->first();
        $userPermissions = $user->permissions->pluck('id')->toArray();
        $userRolePermissions = $userRole ? $userRole->permissions->pluck('id')->toArray() : [];

        return view('admin.user-permissions.edit', compact(
            'user',
            'roles',
            'permissions',
            'allPermissions',
            'userRole',
            'userPermissions',
            'userRolePermissions'
        ));
    }

    /**
     * Update user permissions
     */
//     public function update(Request $request, User $user)
//     {
//         $validated = $request->validate([
//             'role' => 'required|exists:roles,name',
//             'permissions' => 'nullable|array',
//             'permissions.*' => 'exists:permissions,id'
//         ]);

//         // Update user role (enum field)
//         $user->update(['role' => $validated['role']]);

//         // Sync Spatie role
//         $user->syncRoles([$validated['role']]);

//         // Sync direct permissions (إضافة صلاحيات خاصة فوق صلاحيات الرول)
//         // if (!empty($validated['permissions'])) {
//         //     $user->syncPermissions($validated['permissions']);
//         // } else {
//         //     $user->syncPermissions([]);
//         // }


//        if (!empty($validated['permissions'])) {
//     $permissions = Permission::whereIn('id', $validated['permissions'])->get();
//     $user->syncPermissions($permissions);
// } else {
//     $user->syncPermissions([]);
// }
// app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();



//         // Clear permission cache
//         app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

//         return redirect()
//             ->route('admin.users.show', $user)
//             ->with('success', __('User permissions updated successfully'));
//     }
// }

public function update(Request $request, User $user)
{
    // Validate request
    $validated = $request->validate([
        'role' => 'required|exists:roles,name',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    // 1️⃣ تحديث حقل role بالـ enum أو string
    $user->update(['role' => $validated['role']]);

    // 2️⃣ مزامنة Role مع Spatie
    $user->syncRoles([$validated['role']]);

    // 3️⃣ مزامنة Permissions مباشرة
    if (!empty($validated['permissions'])) {
        // جلب Permissions كـ Model مع التأكد من guard
        $permissions = Permission::whereIn('id', $validated['permissions'])
            ->where('guard_name', 'web')
            ->get();

        $user->syncPermissions($permissions);
    } else {
        // تفريغ كل Permissions المباشرة
        $user->syncPermissions([]);
    }

    // 4️⃣ تفريغ Cache بعد أي تعديل
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    return redirect()
        ->route('admin.users.show', $user)
        ->with('success', __('User permissions updated successfully'));
}


}
