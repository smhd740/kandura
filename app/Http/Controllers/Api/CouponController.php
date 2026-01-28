<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyCouponRequest;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Validate coupon
     * POST /api/coupons/validate
     */
    public function validate(ApplyCouponRequest $request): JsonResponse
    {
        $result = $this->couponService->validateCoupon(
            $request->code,
            auth()->user(),
            $request->order_amount ?? 0
        );

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'الكوبون صالح'
                : 'Coupon is valid',
            'data' => [
                'coupon_code' => $result['coupon']->code,
                'discount_type' => $result['coupon']->discount_type,
                'discount_value' => $result['coupon']->amount,
                'discount_amount' => $result['discount_amount'],
            ],
        ]);
    }
}
