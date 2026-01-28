<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\Admin\CouponResource;
use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CouponController extends Controller
{
    use AuthorizesRequests;

    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Display a listing of coupons
     * GET /api/admin/coupons
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Coupon::class);

        $filters = $request->only([
            'search',
            'discount_type',
            'status',
            'sort_by',
            'sort_order',
            'per_page'
        ]);

        $coupons = $this->couponService->getAllCoupons($filters);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم جلب الكوبونات بنجاح'
                : 'Coupons retrieved successfully',
            'data' => CouponResource::collection($coupons),
            'meta' => [
                'current_page' => $coupons->currentPage(),
                'last_page' => $coupons->lastPage(),
                'per_page' => $coupons->perPage(),
                'total' => $coupons->total(),
            ],
        ]);
    }

    /**
     * Store a newly created coupon
     * POST /api/admin/coupons
     */
    public function store(StoreCouponRequest $request): JsonResponse
    {
        $this->authorize('create', Coupon::class);

        $coupon = $this->couponService->createCoupon($request->validated());

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم إنشاء الكوبون بنجاح'
                : 'Coupon created successfully',
            'data' => new CouponResource($coupon),
        ], 201);
    }

    /**
     * Display the specified coupon
     * GET /api/admin/coupons/{coupon}
     */
    public function show(Coupon $coupon): JsonResponse
    {
        $this->authorize('view', $coupon);

        $coupon->load(['allowedUsers', 'usages.user']);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم جلب تفاصيل الكوبون بنجاح'
                : 'Coupon details retrieved successfully',
            'data' => new CouponResource($coupon),
        ]);
    }

    /**
     * Update the specified coupon
     * PUT/PATCH /api/admin/coupons/{coupon}
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): JsonResponse
    {
        $this->authorize('update', $coupon);

        $updatedCoupon = $this->couponService->updateCoupon($coupon, $request->validated());

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم تحديث الكوبون بنجاح'
                : 'Coupon updated successfully',
            'data' => new CouponResource($updatedCoupon),
        ]);
    }

    /**
     * Remove the specified coupon
     * DELETE /api/admin/coupons/{coupon}
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $this->authorize('delete', $coupon);

        $this->couponService->deleteCoupon($coupon);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم حذف الكوبون بنجاح'
                : 'Coupon deleted successfully',
        ]);
    }

    /**
     * Toggle coupon active status
     * POST /api/admin/coupons/{coupon}/toggle-status
     */
    public function toggleStatus(Coupon $coupon): JsonResponse
    {
        $this->authorize('update', $coupon);

        $updatedCoupon = $this->couponService->toggleActiveStatus($coupon);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم تحديث حالة الكوبون بنجاح'
                : 'Coupon status updated successfully',
            'data' => new CouponResource($updatedCoupon),
        ]);
    }

    /**
     * Get coupon statistics
     * GET /api/admin/coupons/statistics
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('viewAny', Coupon::class);

        $stats = $this->couponService->getStatistics();

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم جلب إحصائيات الكوبونات بنجاح'
                : 'Coupon statistics retrieved successfully',
            'data' => $stats,
        ]);
    }
}
