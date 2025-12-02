<?php

namespace App\Http\Controllers\Api;

//use CityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Services\CityService;

class CityController extends Controller
{
    protected CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'sort_by' => $request->input('sort_by', 'id'),
            'sort_order' => $request->input('sort_order', 'asc'),
            'per_page' => $request->input('per_page', 5),
        ];

        $cities = $this->cityService->getActiveCities($filters);

        return response()->json([
            'success' => true,
            'message' => 'Cities retrieved successfully',
            'data' => CityResource::collection($cities),
            'meta' => [
                'current_page' => $cities->currentPage(),
                'last_page' => $cities->lastPage(),
                'per_page' => $cities->perPage(),
                'total' => $cities->total(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $city = $this->cityService->getCityById($id);

        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found',
                'timestamp' => now()->toIso8601String(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'City retrieved successfully',
            'data' => new CityResource($city),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
