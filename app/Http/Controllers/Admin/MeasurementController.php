<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MeasurementResource;
use App\Models\Measurement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class MeasurementController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of measurements.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Measurement::class);

        $query = Measurement::with('user');

        // Search
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Filter by size
        if ($request->filled('size')) {
            $query->bySize($request->input('size'));
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->forUser($request->input('user_id'));
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $measurements = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Measurements retrieved successfully',
            'data' => MeasurementResource::collection($measurements),
            'meta' => [
                'current_page' => $measurements->currentPage(),
                'last_page' => $measurements->lastPage(),
                'per_page' => $measurements->perPage(),
                'total' => $measurements->total(),
            ],
        ]);
    }

    /**
     * Display the specified measurement.
     */
    public function show(Measurement $measurement): JsonResponse
    {
        $this->authorize('view', $measurement);

        $measurement->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Measurement retrieved successfully',
            'data' => new MeasurementResource($measurement),
        ]);
    }

    /**
     * Get available sizes.
     */
    public function availableSizes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Available sizes retrieved successfully',
            'data' => Measurement::availableSizes(),
        ]);
    }
}
