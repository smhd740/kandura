<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(['pending', 'processing', 'completed', 'cancelled']),
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return app()->getLocale() === 'ar' ? [
            'status.required' => 'يرجى تحديد الحالة الجديدة',
            'status.string' => 'صيغة الحالة غير صحيحة',
            'status.in' => 'الحالة المحددة غير صالحة. القيم المسموحة: pending, processing, completed, cancelled',
        ] : [
            'status.required' => 'Please specify the new status',
            'status.string' => 'Invalid status format',
            'status.in' => 'Invalid status. Allowed values: pending, processing, completed, cancelled',
        ];
    }

    /**
     * Custom validation after basic rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $order = $this->route('order'); // Get order from route
            $newStatus = $this->status;
            $locale = app()->getLocale();

            if (!$order) {
                return;
            }

            $currentStatus = $order->status;

            // Validate status transitions
            $validTransitions = [
                'pending' => ['processing', 'cancelled'],
                'processing' => ['completed', 'cancelled'],
                'completed' => [], // Cannot change from completed
                'cancelled' => [], // Cannot change from cancelled
            ];

            if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
                $message = $locale === 'ar'
                    ? "لا يمكن تغيير الحالة من {$currentStatus} إلى {$newStatus}"
                    : "Cannot change status from {$currentStatus} to {$newStatus}";

                $validator->errors()->add('status', $message);
            }
        });
    }
}
