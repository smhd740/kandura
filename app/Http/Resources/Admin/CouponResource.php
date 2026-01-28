<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'discount_type' => $this->discount_type,
            'amount' => (float) $this->amount,
            'max_usage' => $this->max_usage,
            'used_count' => $this->used_count,
            'remaining_uses' => $this->getRemainingUses(),
            'starts_at' => $this->starts_at?->format('Y-m-d H:i:s'),
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            'min_order_amount' => $this->min_order_amount ? (float) $this->min_order_amount : null,
            'is_active' => $this->is_active,
            'one_time_per_user' => $this->one_time_per_user,
            'is_user_specific' => $this->is_user_specific,
            'description' => $this->description,

            // Status
            'is_valid' => $this->isValid(),
            'is_available' => $this->isAvailable(),
            'is_expired' => $this->expires_at < now(),
            'is_used_up' => $this->used_count >= $this->max_usage,

            // Allowed users (if user-specific)
            'allowed_users' => $this->when(
                $this->is_user_specific && $this->relationLoaded('allowedUsers'),
                function () {
                    return $this->allowedUsers->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ];
                    });
                }
            ),

            // Usage history
            'usage_history' => $this->when(
                $this->relationLoaded('usages'),
                function () {
                    return $this->usages->map(function ($usage) {
                        return [
                            'id' => $usage->id,
                            'user' => [
                                'id' => $usage->user->id,
                                'name' => $usage->user->name,
                            ],
                            'order_id' => $usage->order_id,
                            'discount_amount' => (float) $usage->discount_amount,
                            'used_at' => $usage->created_at->format('Y-m-d H:i:s'),
                        ];
                    });
                }
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
