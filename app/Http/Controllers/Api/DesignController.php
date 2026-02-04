<?php

namespace App\Http\Controllers\Api;

use App\Models\Design;
use Illuminate\Http\Request;
use App\Services\DesignService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Http\Requests\Design\ListDesignsRequest;
use App\Http\Requests\Design\StoreDesignRequest;
use App\Http\Requests\Design\UpdateDesignRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Events\DesignCreated;

class DesignController extends Controller
{
    use AuthorizesRequests;
    protected $designService;

    public function __construct(DesignService $designService)
    {
        $this->designService = $designService;
    }

    /**
     * Display a listing of user's designs
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Design::class);

        $filters = $request->only([
            'search',
            'measurement_id',
            'min_price',
            'max_price',
            'sort_by',
            'sort_order',
            'per_page'
        ]);

        $designs = $this->designService->getUserDesigns(auth()->id(), $filters);

        return response()->json([
            'success' => true,
            'message' => 'Designs retrieved successfully',
            'data' => DesignResource::collection($designs->items()),
            'meta' => [
                'current_page' => $designs->currentPage(),
                'last_page' => $designs->lastPage(),
                'per_page' => $designs->perPage(),
                'total' => $designs->total(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Store a newly created design
     */
    public function store(StoreDesignRequest $request): JsonResponse
    {
        $this->authorize('create', Design::class);

        $design = $this->designService->createDesign(
            $request->validated(),
            auth()->id()
        );

        event(new DesignCreated($design));

        return response()->json([
            'success' => true,
            'message' => 'Design created successfully',
            'data' => new DesignResource($design),
            'timestamp' => now()->toIso8601String(),
        ], 201);
    }

    /**
     * Display the specified design
     */
    public function show(Design $design): JsonResponse
    {
        $this->authorize('view', $design);

        $design = $this->designService->getDesignById($design->id);

        return response()->json([
            'success' => true,
            'message' => 'Design retrieved successfully',
            'data' => new DesignResource($design),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Update the specified design
     */
    public function update(UpdateDesignRequest $request, Design $design): JsonResponse
    {
        $this->authorize('update', $design);

        $design = $this->designService->updateDesign($design, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Design updated successfully',
            'data' => new DesignResource($design),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Remove the specified design
     */
    public function destroy(Design $design): JsonResponse
    {
        $this->authorize('delete', $design);

        $this->designService->deleteDesign($design);

        return response()->json([
            'success' => true,
            'message' => 'Design deleted successfully',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
    public function myDesigns(ListDesignsRequest $request)
{
    $designs = $this->designService->getMyDesigns($request->validated());

    return DesignResource::collection($designs);
}

    public function browseDesigns(ListDesignsRequest $request)
{
    $designs = $this->designService->browseDesigns($request->validated());

    return DesignResource::collection($designs);
}
}
