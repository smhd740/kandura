<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'discount_type',
        'amount',
        'max_usage',
        'used_count',
        'starts_at',
        'expires_at',
        'min_order_amount',
        'is_active',
        'is_user_specific',
        'description',
        'one_time_per_user',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_usage' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'is_user_specific' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'one_time_per_user' => 'boolean',
    ];

    // ========================================
    // Relationships
    // ========================================

    /**
     * Users allowed to use this coupon (many-to-many)
     */
    public function allowedUsers()
    {
        return $this->belongsToMany(User::class, 'coupon_user')
            ->withTimestamps();
    }

    /**
     * Coupon usage records (who used it and when)
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Orders that used this coupon
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ========================================
    // Scopes
    // ========================================

    /**
     * Scope: Active coupons only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Valid coupons (not expired and active)
     */
    public function scopeValid($query)
    {
        $now = now()->format('Y-m-d H:i'); // بدون ثواني

        return $query->where('is_active', true)
            ->whereRaw("DATE_FORMAT(expires_at, '%Y-%m-%d %H:%i') >= ?", [$now])
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                    ->orWhereRaw("DATE_FORMAT(starts_at, '%Y-%m-%d %H:%i') <= ?", [$now]);
            });
    }

    /**
     * Scope: Available coupons (valid + has remaining uses)
     */
    public function scopeAvailable($query)
    {
        return $query->valid()
            ->where(function ($q) {
                $q->whereNull('max_usage')
                    ->orWhereColumn('used_count', '<', 'max_usage');
            });
    }


    /**
     * Scope: Search by code
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where('code', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Scope: Filter by discount type
     */
    public function scopeByType($query, $type)
    {
        if (empty($type)) {
            return $query;
        }

        return $query->where('discount_type', $type);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        if (empty($status)) {
            return $query;
        }

        $now = now()->format('Y-m-d H:i');

        switch ($status) {
            case 'active':
                return $query->where('is_active', true)
                    ->whereRaw("DATE_FORMAT(expires_at, '%Y-%m-%d %H:%i') >= ?", [$now])
                    ->where(function ($q) use ($now) {
                        $q->whereNull('starts_at')
                            ->orWhereRaw("DATE_FORMAT(starts_at, '%Y-%m-%d %H:%i') <= ?", [$now]);
                    })
                    ->where(function ($q) {
                        $q->whereNull('max_usage')
                            ->orWhereColumn('used_count', '<', 'max_usage');
                    });

            case 'inactive':
                return $query->where('is_active', false);

            case 'expired':
                return $query->whereRaw("DATE_FORMAT(expires_at, '%Y-%m-%d %H:%i') < ?", [$now]);

            case 'used_up':
                return $query->whereColumn('used_count', '>=', 'max_usage');

            default:
                return $query;
        }
    }





    /**
     * Scope: Sort coupons
     */
    public function scopeSort($query, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        return $query->orderBy($sortBy, $sortOrder);
    }

    // ========================================
    // Helper Methods
    // ========================================

    /**
     * Check if coupon is valid (time-wise)
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->startOfMinute();

        // Check if expired
        if ($this->expires_at && $this->expires_at < $now) {
            return false;
        }

        // Check if started
        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon has remaining uses
     */
    //     public function hasRemainingUses(): bool
    // {
    //     if (is_null($this->max_usage)) {
    //         return true; // استخدام غير محدود
    //     }

    //     return $this->used_count < $this->max_usage;
    // }


    public function hasRemainingUses(): bool
    {
        $used = $this->used_count ?? 0;

        if (is_null($this->max_usage)) {
            return true;
        }

        return $used < $this->max_usage;
    }


    /**
     * Check if coupon is available (valid + has uses)
     */
    public function isAvailable(): bool
    {
        return $this->isValid() && $this->hasRemainingUses();
    }

    /**
     * Check if user can use this coupon
     */
    public function canBeUsedBy(User $user): bool
    {
        // If coupon is user-specific, check if user is in allowed list
        if ($this->is_user_specific) {
            return $this->allowedUsers()->where('user_id', $user->id)->exists();
        }

        return true;
    }

    /**
     * Check if user already used this coupon
     */
    public function isUsedBy(User $user): bool
    {
        return $this->usages()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Check if order amount meets minimum requirement
     */
    public function meetsMinimumAmount(float $orderAmount): bool
    {
        if (!$this->min_order_amount) {
            return true;
        }

        // إذا الكوبون رقم ثابت، السعر لازم يكون أكبر أو يساوي قيمة الكوبون
        if ($this->discount_type === 'fixed') {
            return $orderAmount >= max($this->amount, $this->min_order_amount);
        }

        return $orderAmount >= $this->min_order_amount;
    }

    /**
     * Calculate discount amount for a given order total
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($orderAmount * $this->amount) / 100;
        } else {
            $discount = $this->amount;
        }

        // ❌ الخصم أكبر أو يساوي سعر الطلب
        if ($discount >= $orderAmount) {
            throw ValidationException::withMessages([
                'coupon_code' => [
                    app()->getLocale() === 'ar'
                        ? 'قيمة الخصم يجب أن تكون أقل من قيمة الطلب'
                        : 'Discount amount must be less than order total',
                ],
            ]);
        }

        return round($discount, 2);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Decrement usage count (for refunds)
     */
    public function decrementUsage(): void
    {
        if ($this->used_count > 0) {
            $this->decrement('used_count');
        }
    }

    /**
     * Get remaining uses
     */
    public function getRemainingUses(): int
    {
        return max(0, $this->max_usage - $this->used_count);
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentage(): float
    {
        if ($this->max_usage === 0) {
            return 0;
        }

        return ($this->used_count / $this->max_usage) * 100;
    }

    /**
     * Is coupon active (used in admin views)
     */
    // public function isActive(): bool
    // {
    //     return $this->isValid() && $this->hasRemainingUses();
    // }

    // /**
    //  * Is coupon expired
    //  */

    // public function isExpired(): bool
    // {
    //     return $this->expires_at !== null && now()->gt($this->expires_at);
    // }

    // /**
    //  * Is coupon fully used
    //  */
    // public function isFullyUsed(): bool
    // {
    //     if (is_null($this->max_usage)) {
    //         return false;
    //     }

    //     return $this->used_count >= $this->max_usage;
    // }


    public function isActive(): bool
    {
        return $this->is_active && ! $this->isExpired() && ! $this->isFullyUsed();
    }

    public function isInactive(): bool
    {
        return ! $this->is_active;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }

    public function isFullyUsed(): bool
    {
        if (is_null($this->max_usage)) {
            return false;
        }

        return $this->used_count >= $this->max_usage;
    }
}
