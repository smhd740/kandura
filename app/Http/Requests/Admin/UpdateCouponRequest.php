<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code,' . $this->route('coupon')->id,
            'discount_type' => 'required|in:percentage,fixed',
            'amount' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'required|date|after:starts_at',
            'max_usage' => 'nullable|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_user_specific' => 'boolean',
            'allowed_users' => 'nullable|array',
            'allowed_users.*' => 'exists:users,id',
            'description' => 'nullable|string',
            'one_time_per_user' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper($this->code),
            'is_active' => $this->boolean('is_active'),
            'is_user_specific' => $this->boolean('is_user_specific'),
            'one_time_per_user' => $this->boolean('one_time_per_user'),
        ]);
    }
}
