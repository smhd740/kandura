<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of addresses
     */
    public function index(Request $request)
    {
        $query = Address::with(['user', 'city']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('street', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // City filter
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Default filter
        if ($request->filled('is_default')) {
            $query->where('is_default', $request->is_default);
        }

        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 12);
        $addresses = $query->paginate($perPage)->withQueryString();

        // Get cities and users for filters
        $cities = City::active()->get();
        $users = User::active()->get();

        return view('admin.addresses.index', compact('addresses', 'cities', 'users'));
    }

    /**
     * Display the specified address
     */
    public function show(Address $address)
    {
        $address->load(['user', 'city']);

        return view('admin.addresses.show', compact('address'));
    }

    /**
     * Remove the specified address
     */
    public function destroy(Address $address)
    {
        $address->delete();

        return redirect()
            ->route('admin.addresses.index')
            ->with('success', __('Address deleted successfully!'));
    }

    /**
     * Get addresses statistics (JSON)
     */
    public function statistics()
    {
        $stats = [
            'total' => Address::count(),
            'default_addresses' => Address::where('is_default', true)->count(),
            'by_city' => Address::with('city')
                ->selectRaw('city_id, COUNT(*) as count')
                ->groupBy('city_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'city' => $item->city->name ?? 'Unknown',
                        'count' => $item->count
                    ];
                }),
            'recent' => Address::with(['user', 'city'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get addresses for specific user (AJAX)
     */
    public function byUser(User $user)
    {
        $addresses = $user->addresses()
            ->with('city')
            ->latest()
            ->get();

        return response()->json($addresses);
    }
}
