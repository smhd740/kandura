<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDesignOptionRequest;
use App\Http\Requests\Admin\UpdateDesignOptionRequest;
use App\Http\Resources\Admin\DesignOptionResource;
use App\Models\DesignOption;
use App\Services\Admin\DesignOptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class DesignOptionController extends Controller
{
    protected $designOptionService;
    use AuthorizesRequests;
    public function __construct(DesignOptionService $designOptionService)
    {
        $this->designOptionService = $designOptionService;
    }

    /**
     * Display a listing of the design options.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', DesignOption::class);

        $filters = [
            'search' => $request->input('search'),
            'type' => $request->input('type'),
            'is_active' => $request->input('is_active'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
            'per_page' => $request->input('per_page', 15),
        ];

        $designOptions = $this->designOptionService->getAllDesignOptions($filters);

        return response()->json([
            'success' => true,
            'message' => 'Design options retrieved successfully',
            'data' => DesignOptionResource::collection($designOptions),
            'meta' => [
                'current_page' => $designOptions->currentPage(),
                'last_page' => $designOptions->lastPage(),
                'per_page' => $designOptions->perPage(),
                'total' => $designOptions->total(),
            ],
        ]);
    }

    /**
     * Store a newly created design option.
     */
    public function store(StoreDesignOptionRequest $request): JsonResponse
    {
        $this->authorize('create', DesignOption::class);

        $designOption = $this->designOptionService->createDesignOption($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Design option created successfully',
            'data' => new DesignOptionResource($designOption),
        ], 201);
    }

    /**
     * Display the specified design option.
     */
    public function show(DesignOption $designOption): JsonResponse
    {
        $this->authorize('view', $designOption);

        return response()->json([
            'success' => true,
            'message' => 'Design option retrieved successfully',
            'data' => new DesignOptionResource($designOption),
        ]);
    }

    /**
     * Update the specified design option.
     */
    public function update(UpdateDesignOptionRequest $request, DesignOption $designOption): JsonResponse
    {
        $this->authorize('update', $designOption);

        $updatedDesignOption = $this->designOptionService->updateDesignOption(
            $designOption,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Design option updated successfully',
            'data' => new DesignOptionResource($updatedDesignOption),
        ]);
    }

    /**
     * Remove the specified design option.
     */
    public function destroy(DesignOption $designOption): JsonResponse
    {
        $this->authorize('delete', $designOption);

        $this->designOptionService->deleteDesignOption($designOption);

        return response()->json([
            'success' => true,
            'message' => 'Design option deleted successfully',
        ]);
    }

    /**
     * Toggle active status of design option.
     */
    public function toggleActive(DesignOption $designOption): JsonResponse
    {
        $this->authorize('update', $designOption);

        $updatedDesignOption = $this->designOptionService->toggleActiveStatus($designOption);

        return response()->json([
            'success' => true,
            'message' => 'Design option status updated successfully',
            'data' => new DesignOptionResource($updatedDesignOption),
        ]);
    }

    /**
     * Get available types.
     */
    public function types(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Available types retrieved successfully',
            'data' => $this->designOptionService->getAvailableTypes(),
        ]);
    }
}
