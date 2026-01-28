<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon'); // Get coupon ID from route

        return [
            // Coupon Code (unique except current coupon)
            'code' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[A-Z0-9-_]+$/i',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],

            'one_time_per_user' => 'nullable|boolean',

            // Discount Type & Amount
            'discount_type' => 'sometimes|in:percentage,fixed',
            'amount' => [
                'sometimes',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if ($this->discount_type === 'percentage' && $value > 100) {
                        $fail(app()->getLocale() === 'ar'
                            ? 'النسبة المئوية يجب أن تكون بين 0.01 و 100'
                            : 'Percentage must be between 0.01 and 100');
                    }
                },
            ],

            // Usage Limits
            'max_usage' => 'sometimes|integer|min:1',

            // Dates
            'starts_at' => 'nullable|date',
            'expires_at' => 'sometimes|date|after:starts_at',

            // Minimum Order Amount
            'min_order_amount' => 'nullable|numeric|min:0',

            // Status
            'is_active' => 'boolean',

            // User Specific
            'is_user_specific' => 'boolean',
            'allowed_users' => [
                'nullable',
                'array',
                'required_if:is_user_specific,1',
            ],
            'allowed_users.*' => 'exists:users,id',

            // Description
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        // نفس الرسائل من StoreCouponRequest
        return (new StoreCouponRequest())->messages();
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $locale = app()->getLocale();

            // Fixed discount should not exceed min_order_amount
            if ($this->discount_type === 'fixed' && $this->min_order_amount) {
                if ($this->amount > $this->min_order_amount) {
                    $message = $locale === 'ar'
                        ? 'قيمة الخصم الثابت يجب أن تكون أقل من أو تساوي الحد الأدنى للطلب'
                        : 'Fixed discount amount must be less than or equal to minimum order amount';
                    $validator->errors()->add('amount', $message);
                }
            }
        });
    }
}
