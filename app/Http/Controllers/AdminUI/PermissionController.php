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
            return $parts[1] ?? 'other'; // e.g., "view users" -> "users"
        });

        $stats = [
            'total' => $allPermissions->count(),
            'modules' => $permissions->count(),
        ];

        return view('admin.permissions.index', compact('permissions', 'stats'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        // Get existing modules for suggestions
        $modules = Permission::all()
            ->map(function($permission) {
                $parts = explode(' ', $permission->name);
                return $parts[1] ?? null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Common actions
        $actions = ['view', 'create', 'edit', 'delete', 'activate', 'deactivate', 'approve', 'reject'];

        return view('admin.permissions.create', compact('modules', 'actions'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        // Create permission
        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web'
        ]);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('Permission created successfully'));
    }
}
