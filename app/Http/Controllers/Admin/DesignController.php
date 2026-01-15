<?php

namespace App\Http\Controllers\Admin;

use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DesignResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DesignController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of all designs (Admin view).
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAll', Design::class);

        $query = Design::with(['user', 'measurements', 'images', 'designOptions']);

        // Search by design name or user name
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Filter by size
        if ($request->filled('size')) {
            $query->bySize($request->input('size'));
        }

        // Filter by price range
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->byPriceRange(
                $request->input('min_price'),
                $request->input('max_price')
            );
        }

        // Filter by design option
        if ($request->filled('design_option_id')) {
            $query->byDesignOption($request->input('design_option_id'));
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->forUser($request->input('user_id'));
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->sort($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 15);
        $designs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Designs retrieved successfully',
            'data' => DesignResource::collection($designs),
            'meta' => [
                'current_page' => $designs->currentPage(),
                'last_page' => $designs->lastPage(),
                'per_page' => $designs->perPage(),
                'total' => $designs->total(),
            ],
            'filters' => [
                'available_sizes' => \App\Models\Measurement::availableSizes(),
                'design_options' => \App\Models\DesignOption::active()
                    ->select('id', 'name', 'type')
                    ->get(),
            ],
        ]);
    }

    /**
     * Display the specified design.
     */
    public function show(Design $design): JsonResponse
    {
        $this->authorize('view', $design);

        $design->load(['user', 'measurements', 'images', 'designOptions']);

        return response()->json([
            'success' => true,
            'message' => 'Design retrieved successfully',
            'data' => new DesignResource($design),
        ]);
    }

    /**
     * Get design statistics (bonus).
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('viewAll', Design::class);

        $stats = [
            'total_designs' => Design::count(),
            'active_designs' => Design::active()->count(),
            'total_users' => Design::distinct('user_id')->count('user_id'),
            'designs_by_size' => Design::join('design_measurements', 'designs.id', '=', 'design_measurements.design_id')
                ->join('measurements', 'design_measurements.measurement_id', '=', 'measurements.id')
                ->select('measurements.size', DB::raw('count(DISTINCT designs.id) as count'))
                ->groupBy('measurements.size')
                ->get(),
            'average_price' => Design::avg('price'),
            'total_value' => Design::sum('price'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Design statistics retrieved successfully',
            'data' => $stats,
        ]);
    }
}
