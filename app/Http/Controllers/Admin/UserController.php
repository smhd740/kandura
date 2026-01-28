<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
           // $query->where('role', $request->role);
            $query->role($request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        // Get all roles from Spatie
        $roles = Role::orderBy('name')->pluck('name')->toArray();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Get valid role names from Spatie
        $validRoles = Role::pluck('name')->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:' . implode(',', $validRoles)],
            'is_active' => ['nullable', 'boolean'],
            'email_verified' => ['nullable', 'boolean'],
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
            'email_verified_at' => $request->has('email_verified') ? now() : null,
        ]);

        // Sync Spatie role
        $user->syncRoles([$validated['role']]);

        if ($validated['role'] === 'user') {
    $user->wallet()->create(['amount' => 0]);
}

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', __('User created successfully!'));
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['addresses.city']);

        // Get user statistics
        $stats = [
            'total_addresses' => $user->addresses()->count(),
            'default_address' => $user->addresses()->where('is_default', true)->first(),
            'joined' => $user->created_at->diffForHumans(),
            'last_updated' => $user->updated_at->diffForHumans(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        // Get all roles from Spatie
        $roles = Role::orderBy('name')->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Get valid role names from Spatie
        $validRoles = Role::pluck('name')->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:20'],
           // 'role' => ['required', 'in:' . implode(',', $validRoles)],
        'role' => ['required', Rule::exists('roles', 'name')]
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        // Sync Spatie role
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', __('User updated successfully!'));
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', __("User {$status} successfully!"));
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', __('You cannot delete your own account!'));
        }

        // Delete user
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('User deleted successfully!'));
    }

    /**
     * Get user statistics (JSON)
     */
    public function statistics()
    {
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'by_role' => [
                'users' => User::where('role', 'user')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'super_admins' => User::where('role', 'super_admin')->count(),
            ],
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json($stats);
    }
}
