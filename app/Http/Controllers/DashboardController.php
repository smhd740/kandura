<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\City;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        // Get statistics
        $totalUsers = User::count();
        $totalAddresses = Address::count();
        $activeUsers = User::where('is_active', true)->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $totalCities = City::active()->count();

        // Get recent users (last 5)
        $recentUsers = User::latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalUsers',
            'totalAddresses',
            'activeUsers',
            'newUsersToday',
            'totalCities',
            'recentUsers'
        ));
    }
}
