<?php

namespace App\Http\Controllers\AdminUI;

use App\Models\Design;
use App\Models\DesignImage;
use App\Models\DesignOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DesignController extends Controller
{
    /**
     * Display a listing of all designs (Admin view).
     */
    public function index(Request $request)
    {
        $query = Design::with(['user', 'images', 'measurements']);

        // Search by design name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'like', "%{$search}%")
                    ->orWhere('name->ar', 'like', "%{$search}%");
            });
        }

        // Search by user name
        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->user}%");
            });
        }

        // Filter by size
        if ($request->filled('size')) {
            $query->bySize($request->size);
        }

        // Filter by price range
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->byPriceRange(
                $request->min_price,
                $request->max_price
            );
        }

        // Filter by design option
        if ($request->filled('design_option')) {
            $query->byDesignOption($request->design_option);
        }

        // Get paginated results
        $designs = $query->orderBy('created_at', 'desc')->paginate(12);

        // Statistics
        $totalDesigns = Design::count();
        $totalCreators = Design::distinct('user_id')->count('user_id');
        $avgPrice = Design::avg('price');
        $totalImages = DesignImage::count();

        // Design options for filter
        $designOptions = DesignOption::active()
            ->orderBy('type')
            ->orderBy('name->en')
            ->get();

        return view('admin.designs.index', compact(
            'designs',
            'totalDesigns',
            'totalCreators',
            'avgPrice',
            'totalImages',
            'designOptions'
        ));
    }

    /**
     * Display the specified design.
     */
    public function show(Design $design)
    {
        // Load relationships
        $design->load([
            'user',
            'images',
            'measurements',
            'designOptions'
        ]);

        return view('admin.designs.show', compact('design'));
    }
}
