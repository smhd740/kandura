<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1|max:10000',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        $locale = app()->getLocale();

        return [
            'user_id.required' => $locale === 'ar'
                ? 'معرف المستخدم مطلوب'
                : 'User ID is required',
            'user_id.exists' => $locale === 'ar'
                ? 'المستخدم غير موجود'
                : 'User not found',
            'amount.required' => $locale === 'ar'
                ? 'المبلغ مطلوب'
                : 'Amount is required',
            'amount.min' => $locale === 'ar'
                ? 'الحد الأدنى للمبلغ هو 1'
                : 'Minimum amount is 1',
            'amount.max' => $locale === 'ar'
                ? 'الحد الأقصى للمبلغ هو 10000'
                : 'Maximum amount is 10000',
        ];
    }
}
