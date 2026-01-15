<?php

// ==============================================================================
// Request 1: Store Coupon Request (Admin creates new coupon)
// File: app/Http/Requests/StoreCouponRequest.php
// ==============================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy
    }

    public function rules(): array
    {
        return [
            // Coupon Code
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9-_]+$/i', // حروف وأرقام وشرطة وشرطة سفلية فقط
                'unique:coupons,code',
            ],

            // Discount Type & Amount
            'discount_type' => 'required|in:percentage,fixed',
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    // إذا نسبة، لازم تكون بين 0.01 و 100
                    if ($this->discount_type === 'percentage' && $value > 100) {
                        $fail(app()->getLocale() === 'ar'
                            ? 'النسبة المئوية يجب أن تكون بين 0.01 و 100'
                            : 'Percentage must be between 0.01 and 100');
                    }
                },
            ],

            // Usage Limits
            'max_usage' => 'required|integer|min:1',

            // Dates
            'starts_at' => 'nullable|date|after_or_equal:today',
            'expires_at' => [
                'required',
                'date',
                'after:starts_at', // لازم يكون بعد starts_at
            ],

            // Minimum Order Amount
            'min_order_amount' => 'nullable|numeric|min:0',

            // Status
            'is_active' => 'boolean',

            // User Specific
            'is_user_specific' => 'boolean',
            'user_ids' => [
                'nullable',
                'array',
                'required_if:is_user_specific,true', // إذا is_user_specific = true، لازم user_ids
            ],
            'user_ids.*' => 'exists:users,id',

            // Description
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return app()->getLocale() === 'ar' ? [
            // Code
            'code.required' => 'كود الكوبون مطلوب',
            'code.unique' => 'كود الكوبون مستخدم مسبقاً',
            'code.regex' => 'كود الكوبون يجب أن يحتوي على حروف وأرقام فقط',
            'code.max' => 'كود الكوبون لا يمكن أن يتجاوز 50 حرف',

            // Discount Type
            'discount_type.required' => 'نوع الخصم مطلوب',
            'discount_type.in' => 'نوع الخصم يجب أن يكون نسبة أو رقم ثابت',

            // Amount
            'amount.required' => 'قيمة الخصم مطلوبة',
            'amount.numeric' => 'قيمة الخصم يجب أن تكون رقماً',
            'amount.min' => 'قيمة الخصم يجب أن تكون أكبر من 0',

            // Max Usage
            'max_usage.required' => 'عدد مرات الاستخدام مطلوب',
            'max_usage.integer' => 'عدد مرات الاستخدام يجب أن يكون رقماً صحيحاً',
            'max_usage.min' => 'عدد مرات الاستخدام يجب أن يكون 1 على الأقل',

            // Dates
            'starts_at.date' => 'تاريخ البداية غير صحيح',
            'starts_at.after_or_equal' => 'تاريخ البداية يجب أن يكون اليوم أو بعده',
            'expires_at.required' => 'تاريخ الانتهاء مطلوب',
            'expires_at.date' => 'تاريخ الانتهاء غير صحيح',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية',

            // Min Order Amount
            'min_order_amount.numeric' => 'الحد الأدنى للطلب يجب أن يكون رقماً',
            'min_order_amount.min' => 'الحد الأدنى للطلب يجب أن يكون 0 أو أكبر',

            // User Specific
            'user_ids.required_if' => 'يرجى اختيار المستخدمين المسموح لهم',
            'user_ids.array' => 'صيغة المستخدمين غير صحيحة',
            'user_ids.*.exists' => 'أحد المستخدمين المحددين غير موجود',

            // Description
            'description.max' => 'الوصف لا يمكن أن يتجاوز 1000 حرف',
        ] : [
            // Code
            'code.required' => 'Coupon code is required',
            'code.unique' => 'Coupon code already exists',
            'code.regex' => 'Coupon code must contain only letters, numbers, hyphens and underscores',
            'code.max' => 'Coupon code cannot exceed 50 characters',

            // Discount Type
            'discount_type.required' => 'Discount type is required',
            'discount_type.in' => 'Discount type must be percentage or fixed',

            // Amount
            'amount.required' => 'Discount amount is required',
            'amount.numeric' => 'Discount amount must be a number',
            'amount.min' => 'Discount amount must be greater than 0',

            // Max Usage
            'max_usage.required' => 'Maximum usage count is required',
            'max_usage.integer' => 'Maximum usage must be a valid number',
            'max_usage.min' => 'Maximum usage must be at least 1',

            // Dates
            'starts_at.date' => 'Start date is invalid',
            'starts_at.after_or_equal' => 'Start date must be today or later',
            'expires_at.required' => 'Expiration date is required',
            'expires_at.date' => 'Expiration date is invalid',
            'expires_at.after' => 'Expiration date must be after start date',

            // Min Order Amount
            'min_order_amount.numeric' => 'Minimum order amount must be a number',
            'min_order_amount.min' => 'Minimum order amount must be 0 or greater',

            // User Specific
            'user_ids.required_if' => 'Please select allowed users',
            'user_ids.array' => 'Invalid users format',
            'user_ids.*.exists' => 'One of the selected users does not exist',

            // Description
            'description.max' => 'Description cannot exceed 1000 characters',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $locale = app()->getLocale();

            // إذا الكوبون رقم ثابت، قيمة الخصم لازم تكون أقل من أو تساوي min_order_amount
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
