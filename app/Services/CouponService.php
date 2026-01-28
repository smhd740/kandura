<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;

class CouponService
{
    /**
     * Get all coupons with filters (Admin)
     */
    public function getAllCoupons(array $filters = [])
    {
        $query = Coupon::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['discount_type'])) {
            $query->byType($filters['discount_type']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->sort($sortBy, $sortOrder);

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Create coupon
     */
    public function createCoupon(array $data): Coupon
    {
        $coupon = Coupon::create([
            'code' => strtoupper($data['code']),
            'discount_type' => $data['discount_type'],
            'amount' => $data['amount'],
            'max_usage' => $data['max_usage'],
            'starts_at' => $data['starts_at'] ?? null,
            'expires_at' => $data['expires_at'],
            'min_order_amount' => $data['min_order_amount'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_user_specific' => $data['is_user_specific'] ?? false,
            'description' => $data['description'] ?? null,
            'one_time_per_user' => $data['one_time_per_user'] ?? false,
        ]);

        // إذا في مستخدمين محددين
        if ($coupon->is_user_specific && !empty($data['user_ids'])) {
            $coupon->allowedUsers()->attach($data['user_ids']);
        }

        return $coupon->load('allowedUsers');
    }

    /**
     * Update coupon
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        $coupon->update([
            'code' => isset($data['code']) ? strtoupper($data['code']) : $coupon->code,
            'discount_type' => $data['discount_type'] ?? $coupon->discount_type,
            'amount' => $data['amount'] ?? $coupon->amount,
            'max_usage' => $data['max_usage'] ?? $coupon->max_usage,
            'starts_at' => $data['starts_at'] ?? $coupon->starts_at,
            'expires_at' => $data['expires_at'] ?? $coupon->expires_at,
            'min_order_amount' => $data['min_order_amount'] ?? $coupon->min_order_amount,
            'is_active' => $data['is_active'] ?? $coupon->is_active,
            'is_user_specific' => $data['is_user_specific'] ?? $coupon->is_user_specific,
            'description' => $data['description'] ?? $coupon->description,
        ]);

        // تحديث المستخدمين المسموحين
        if (isset($data['is_user_specific'])) {
            if ($data['is_user_specific'] && !empty($data['user_ids'])) {
                $coupon->allowedUsers()->sync($data['user_ids']);
            } else {
                $coupon->allowedUsers()->detach();
            }
        }

        return $coupon->fresh(['allowedUsers']);
    }

    /**
     * Delete coupon
     */
    public function deleteCoupon(Coupon $coupon): bool
    {
        return $coupon->delete();
    }

    /**
     * Toggle active status
     */
    public function toggleActiveStatus(Coupon $coupon): Coupon
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        return $coupon->fresh();
    }

    /**
     * Validate coupon
     */
    public function validateCoupon(string $code, User $user, float $orderAmount): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        // الكوبون مش موجود
        if (!$coupon) {
            return [
                'valid' => false,
                'message' => app()->getLocale() === 'ar' ? 'كود الكوبون غير صحيح' : 'Invalid coupon code',
            ];
        }

        // الكوبون مش متاح (منتهي أو مش فعال أو استخدم كل المرات)
        if (!$coupon->isAvailable()) {
            return [
                'valid' => false,
                'message' => app()->getLocale() === 'ar' ? 'الكوبون غير متاح' : 'Coupon is not available',
            ];
        }

        // اليوزر مش من المستخدمين المسموحين
        if (!$coupon->canBeUsedBy($user)) {
            return [
                'valid' => false,
                'message' => app()->getLocale() === 'ar' ? 'غير مسموح لك باستخدام هذا الكوبون' : 'You are not allowed to use this coupon',
            ];
        }

        // اليوزر استخدمه قبل هيك
        if ($coupon->one_time_per_user && $coupon->isUsedBy($user)) {
        return [
        'valid' => false,
        'message' => app()->getLocale() === 'ar'
            ? 'لا يمكنك استخدام هذا الكوبون أكثر من مرة'
            : 'This coupon can only be used once per user',
    ];
}


        // سعر الطلب أقل من الحد الأدنى
        if (!$coupon->meetsMinimumAmount($orderAmount)) {
            return [
                'valid' => false,
                'message' => app()->getLocale() === 'ar'
                    ? "الحد الأدنى للطلب هو {$coupon->min_order_amount}"
                    : "Minimum order amount is {$coupon->min_order_amount}",
            ];
        }

        // حساب الخصم
        $discountAmount = $coupon->calculateDiscount($orderAmount);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * Apply coupon to order
     */
    public function applyCouponToOrder(Coupon $coupon, User $user, int $orderId, float $discountAmount): void
    {
        // زيادة عداد الاستخدام
        $coupon->incrementUsage();

        // تسجيل الاستخدام
        $coupon->usages()->create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
        ]);
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::active()->count(),
            'expired_coupons' => Coupon::where('expires_at', '<', now())->count(),
            'used_up_coupons' => Coupon::whereColumn('used_count', '>=', 'max_usage')->count(),
        ];
    }
}
