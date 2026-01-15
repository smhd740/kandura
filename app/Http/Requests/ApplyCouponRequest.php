<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in Service/Controller
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|exists:coupons,code',
        ];
    }

    public function messages(): array
    {
        return app()->getLocale() === 'ar' ? [
            'code.required' => 'كود الكوبون مطلوب',
            'code.exists' => 'كود الكوبون غير صحيح',
        ] : [
            'code.required' => 'Coupon code is required',
            'code.exists' => 'Invalid coupon code',
        ];
    }
}
