<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of cities
     */
    public function index(Request $request)
    {
        $query = City::withCount('addresses');

        // Search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Active filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->sort($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 20);
        $cities = $query->paginate($perPage)->withQueryString();

        return view('admin.cities.index', compact('cities'));
    }

    /**
     * Display the specified city
     */
    public function show(City $city)
    {
        $city->loadCount('addresses');

        // Get recent addresses for this city
        $recentAddresses = $city->addresses()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.cities.show', compact('city', 'recentAddresses'));
    }

    /**
     * Get city statistics (JSON)
     */
    public function statistics()
    {
        $stats = [
            'total' => City::count(),
            'active' => City::where('is_active', true)->count(),
            'inactive' => City::where('is_active', false)->count(),
            'with_addresses' => City::has('addresses')->count(),
            'top_cities' => City::withCount('addresses')
                ->orderBy('addresses_count', 'desc')
                ->take(5)
                ->get()
                ->map(function ($city) {
                    return [
                        'name' => $city->name,
                        'count' => $city->addresses_count
                    ];
                }),
        ];

        return response()->json($stats);
    }
}
