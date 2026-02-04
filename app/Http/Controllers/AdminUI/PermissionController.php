<?php

namespace App\Http\Controllers\AdminUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index()
    {
        // Group permissions by module (second word in permission name)
        $allPermissions = Permission::orderBy('name')->get();

        $permissions = $allPermissions->groupBy(function($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[1] ?? 'other';
        });

        $stats = [
            'total' => $allPermissions->count(),
            'modules' => $permissions->count(),
        ];

        return view('admin.permissions.index', compact('permissions', 'stats'));
    }
}
