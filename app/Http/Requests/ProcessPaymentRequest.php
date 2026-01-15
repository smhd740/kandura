<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|string|in:stripe,wallet,cod',
        ];
    }

    public function messages(): array
    {
        $locale = app()->getLocale();

        return [
            'payment_method.required' => $locale === 'ar'
                ? 'طريقة الدفع مطلوبة'
                : 'Payment method is required',
            'payment_method.in' => $locale === 'ar'
                ? 'طريقة الدفع غير صالحة. الخيارات المتاحة: stripe, wallet, cod'
                : 'Invalid payment method. Available options: stripe, wallet, cod',
        ];
    }
}
